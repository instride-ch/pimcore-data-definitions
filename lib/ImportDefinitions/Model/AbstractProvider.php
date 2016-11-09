<?php
/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016 W-Vision (http://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitions\Model;

use ImportDefinitions\Model\Cleaner\AbstractCleaner;
use ImportDefinitions\Model\Filter\AbstractFilter;
use ImportDefinitions\Model\Interpreter\AbstractInterpreter;
use ImportDefinitions\Model\Mapping\FromColumn;
use ImportDefinitions\Model\Runner\AbstractRunner;
use ImportDefinitions\Model\Setter\AbstractSetter;
use Pimcore\File;
use Pimcore\Mail;
use Pimcore\Model\Document;
use Pimcore\Model\Object\AbstractObject;
use Pimcore\Model\Object\ClassDefinition;
use Pimcore\Model\Object\Concrete;
use Pimcore\Model\Object\Listing;
use Pimcore\Model\Object\Service;
use Pimcore\Model\Version;
use Pimcore\Tool;

/**
 * Base Class every Provider needs to implement
 *
 * Class AbstractProvider
 * @package ImportDefinitions
 */
abstract class AbstractProvider
{
    /**
     * available Providers.
     *
     * @var array
     */
    public static $availableProviders = array('csv', 'sql', 'json', 'xml');

    /**
     * @var Mapping[]
     */
    public $mappings;

    /**
     * @var \Monolog\Logger|null
     */
    public $logger;

    /**
     * @var \Exception[]
     */
    protected $exceptions = [];

    /**
     * @var int[]
     */
    protected $objectIds = [];

    /**
     * Add Provider.
     *
     * @param $provider
     */
    public static function addProvider($provider)
    {
        if (!in_array($provider, self::$availableProviders)) {
            self::$availableProviders[] = $provider;
        }
    }

    /**
     * @param array $values
     */
    public function setValues(array $values)
    {
        foreach ($values as $key => $value) {
            if ($key == 'type') {
                continue;
            }

            $setter = 'set'.ucfirst($key);

            if ($key === "mappings") {
                $mappings = [];

                if (is_array($value)) {
                    foreach ($value as $vMap) {
                        $mapping = new Mapping();
                        $mapping->setValues($vMap);

                        $mappings[] = $mapping;
                    }

                    $value = $mappings;
                }
            }

            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }

    /**
     * @return array
     */
    public static function getAvailableProviders()
    {
        return self::$availableProviders;
    }

    /**
     * @param array $availableProviders
     */
    public static function setAvailableProviders($availableProviders)
    {
        self::$availableProviders = $availableProviders;
    }

    /**
     * @return Mapping[]
     */
    public function getMappings()
    {
        return $this->mappings;
    }

    /**
     * @param Mapping[] $mappings
     */
    public function setMappings($mappings)
    {
        $this->mappings = $mappings;
    }

    /**
     * Test Data provided for this Provider
     *
     * @return boolean
     * @throws \Exception
     */
    abstract public function testData();

    /**
     * Get Columns from data
     *
     * @return FromColumn[]
     */
    abstract public function getColumns();

    /**
     * @param Definition $definition
     * @param $params
     * @param AbstractFilter|null $filter
     * @param array $data
     *
     * @throws \Exception
     */
    protected function runImport($definition, $params, $filter = null, $data = array())
    {
        $count = 0;
        $countToClean = 1000;

        if(is_array($data)) {
            foreach ($data as $row) {
                try {
                    $object = $this->importRow($definition, $row, $params, $filter);

                    if ($object instanceof Concrete) {
                        $this->objectIds[] = $object->getId();
                    }

                    if(($count + 1) % $countToClean === 0) {
                        \Pimcore::collectGarbage();
                        $this->logger->info("Clean Garbage");
                        \Pimcore::getEventManager()->trigger("importdefinitions.status", "Collect Garbage");
                    }

                    $count++;
                } catch (\Exception $ex) {
                    $this->logger->error($ex);

                    $this->exceptions[] = $ex;

                    if ($definition->getStopOnException()) {
                        throw $ex;
                    }
                }
            }
        }
    }

    /**
     * @param $definition
     * @param $params
     * @param null $filter
     *
     * @return array
     */
    abstract protected function getData($definition, $params, $filter = null);

    /**
     * @return \Monolog\Logger
     */
    protected function getLogger() {
        if(is_null($this->logger)) {
            $this->logger = new \Monolog\Logger('import-definitions');
            $this->logger->pushHandler(new \Monolog\Handler\StreamHandler(PIMCORE_LOG_DIRECTORY . "/import-definitions-" . time() . ".log"));
        }

        return $this->logger;
    }

    /**
     * @param Definition $definition
     * @param array $data
     * @param array $params
     * @param AbstractFilter|null $filter
     *
     * @return Concrete
     *
     * @throws \Exception
     */
    protected function importRow($definition, $data, $params, $filter = null, $runner = null) {
        $runner = null;

        $object = $this->getObjectForPrimaryKey($definition, $data);

        if ($filter instanceof AbstractFilter) {
            if (!$filter->filter($definition, $data, $object)) {
                return null;
            }
        }

        \Pimcore::getEventManager()->trigger("importdefinitions.status", "Import Object " . ($object->getId() ? $object->getFullPath() : "new"));
        \Pimcore::getEventManager()->trigger("importdefinitions.object.start", $object);

        if ($definition->getRunner()) {
            $runnerClass = '\ImportDefinitions\Model\Runner\\' . $definition->getRunner();

            if (!Tool::classExists($runnerClass)) {
                throw new \Exception("Runner class not found ($runnerClass)");
            }

            $runner = new $runnerClass;
        }


        if ($runner instanceof AbstractRunner) {
            $runner->preRun($object, $data, $definition, $params);
        }

        $this->getLogger()->info("Imported Object: " . $object->getRealFullPath());

        foreach ($definition->getMapping() as $mapItem) {
            $value = null;

            if (array_key_exists($mapItem->getFromColumn(), $data)) {
                $value = $data[$mapItem->getFromColumn()];
            }

            $this->setObjectValue($object, $mapItem, $value, $data, $definition, $params);
        }

        $object->setUserModification(0); //Set User to "system"
        $object->save();

        \Pimcore::getEventManager()->trigger("importdefinitions.status", "Imported Object " . $object->getFullPath());
        \Pimcore::getEventManager()->trigger("importdefinitions.object.finished", $object);

        if ($runner instanceof AbstractRunner) {
            $runner->postRun($object, $data, $definition, $params);
        }

        return $object;
    }


    /**
     * @param Definition $definition
     * @param $params
     *
     * @throws \Exception
     */
    public function doImport($definition, $params)
    {
        $filterObject = null;

        if($definition->getCreateVersion()) {
            Version::enable();
        }
        else {
            Version::disable();
        }

        if($definition->getFilter()) {
            $filterClass = '\ImportDefinitions\Model\Filter\\' . $definition->getFilter();

            if(!Tool::classExists($filterClass)) {
                throw new \Exception("Filter class not found ($filterClass)");
            }

            $filterObject = new $filterClass();
        }

        $data = $this->getData($definition, $params, $filterObject);

        \Pimcore::getEventManager()->trigger("importdefinitions.total", count($data));

        $this->runImport($definition, $params, $filterObject, $data);

        //Get Cleanup type
        $type = $definition->getCleaner();
        $class = 'ImportDefinitions\\Model\\Cleaner\\' . ucfirst($type);

        if (Tool::classExists($class)) {
            $class = new $class();

            if ($class instanceof AbstractCleaner) {
                $this->logger->info(sprintf("Running Cleaner '%s", $type));
                \Pimcore::getEventManager()->trigger("importdefinitions.status", sprintf("Running Cleaner '%s", $type));

                $class->cleanup($definition, $this->objectIds);

                $this->logger->info(sprintf("Finished Cleaner '%s", $type));
                \Pimcore::getEventManager()->trigger("importdefinitions.status", sprintf("Finished Cleaner '%s", $type));
            }
        }

        if(count($this->exceptions) > 0) {
            $this->sendDocument(Document::getById($definition->getFailureNotificationDocument()), $definition);
        }
        else {
            $this->sendDocument(Document::getById($definition->getSuccessNotificationDocument()), $definition);
        }

        \Pimcore::getEventManager()->trigger("importdefinitions.finished");
    }

    /**
     * @param $document
     * @param Definition $definition
     */
    public function sendDocument($document, $definition) {
        if($document instanceof Document) {
            $params = [
                "exceptions" => $this->exceptions,
                "objectIds" => $this->objectIds,
                "className" => $definition->getClass(),
                "countObjects" => count($this->objectIds),
                "countExceptions" => count($this->exceptions),
                "name" => $definition->getName(),
                "provider" => $definition->getProvider()
            ];

            if ($document instanceof Document\Email) {
                $mail = new Mail();
                $mail->setDocument($document);
                $mail->setParams($params);

                $mail->send();
            } else if (is_a($document, "\\Pimcore\\Model\\Document\\Pushover")) {
                $document->send($params);
            }
        }
    }

    /**
     * @param Definition $definition
     * @param array $data
     *
     * @throws \Exception
     *
     * @return Concrete
     */
    public function getObjectForPrimaryKey($definition, $data)
    {
        $class = $definition->getClass();
        $classDefinition = ClassDefinition::getByName($class);
        $obj = null;

        if (!$classDefinition instanceof ClassDefinition) {
            throw new \Exception("Class not found $class");
        }

        $classObject = '\Pimcore\Model\Object\\' . ucfirst($class);
        $classList = '\Pimcore\Model\Object\\' . ucfirst($class) . '\Listing';

        $list = new $classList();

        if ($list instanceof Listing) {
            $mapping = $definition->getMapping();
            $condition = [];
            $conditionValues = [];
            foreach ($mapping as $map) {
                if ($map->getPrimaryIdentifier()) {
                    $condition[] = $map->getToColumn() . " = ?";
                    $conditionValues[] = $data[$map->getFromColumn()];
                }
            }

            if (count($condition) === 0) {
                throw new \Exception("No primary identifier defined!");
            }

            $list->setUnpublished(true);
            $list->setCondition(implode(" AND ", $condition), $conditionValues);
            $list->setObjectTypes([AbstractObject::OBJECT_TYPE_VARIANT, AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_FOLDER]);
            $objectData = $list->load();

            if (count($objectData) === 1) {
                $obj = $objectData[0];
            }

            if (!isset($obj)) {
                $obj = new $classObject();
            }

            if ($obj instanceof AbstractObject) {
                $key = File::getValidFilename($definition->createKey($data));

                if($definition->getRelocateExistingObjects() || !$obj->getId()) {
                    $obj->setParent(Service::createFolderByPath($definition->createPath($data)));
                }

                if($definition->getRenameExistingObjects() || !$obj->getId()) {
                    if ($definition->getKey() && $key) {
                        $obj->setKey($key);
                    } else {
                        $obj->setKey(File::getValidFilename(implode("-", $conditionValues)));
                    }
                }

                $obj->setKey(Service::getUniqueKey($obj));

                return $obj;
            }

            if (count($objectData) > 1) {
                throw new \Exception("Object with the same primary key was found multiple times");
            }
        }

        return null;
    }

    /**
     * @param Concrete $object
     * @param Mapping $map
     * @param $value
     * @param array $data
     * @param Definition $definition
     * @param array $params
     *
     * @throws \Exception
     */
    public function setObjectValue(Concrete $object, Mapping $map, $value, $data, $definition, $params)
    {
        $mapConfig = $map->getConfig();

        if ($mapConfig['interpreter']) {
            $class = 'ImportDefinitions\Model\Interpreter\\' . ucfirst($mapConfig['interpreter']);

            if (Tool::classExists($class)) {
                $class = new $class();

                if ($class instanceof AbstractInterpreter) {
                    $value = $class->interpret($object, $value, $map, $data, $definition, $params);
                }
            }
        }

        if($map->getToColumn() === "type") {
            if($mapConfig['setter'] !== "objectType") {
                throw new \Exception("Type has to be used with ObjectType Setter!");
            }
        }

        if ($mapConfig['setter']) {
            $class = 'ImportDefinitions\Model\Setter\\' . ucfirst($mapConfig['setter']);

            if (Tool::classExists($class)) {
                $class = new $class();

                if ($class instanceof AbstractSetter) {
                    $class->set($object, $value, $map, $data);
                }
            }
        } else {
            $object->setValue($map->getToColumn(), $value);
        }
    }
}
