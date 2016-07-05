<?php

namespace ImportDefinitions\Model\Interpreter;
use ImportDefinitions\Model\Mapping;
use ImportDefinitions\Model\Placeholder;
use Pimcore\File;
use Pimcore\Model\Asset;
use Pimcore\Model\Object\Concrete;
use Pimcore\Tool;

/**
 * Class AssetUrl
 * @package ImportDefinitions\Model\Interpreter
 */
class AssetUrl extends AbstractInterpreter {

    /**
     * @param Concrete $object
     * @param $value
     * @param Mapping $map
     * @param array $data
     * @return mixed
     */
    public function interpret(Concrete $object, $value, Mapping $map, $data) {
        $config = $map->getInterpreterConfig();
        $path = $config['path'];

        if(filter_var($value, FILTER_VALIDATE_URL)) {
            $filename = File::getValidFilename(basename($value));

            //Convert placeholder path

            $assetPath = Placeholder::replace($path, $data);
            $assetFullPath = $assetPath . "/" . $filename;

            $asset = Asset::getByPath($assetFullPath);

            if(!$asset instanceof Asset) {
                //Download
                $data = @file_get_contents($value);

                if($data) {
                    $asset = new Asset();
                    $asset->setFilename($filename);
                    $asset->setParent(Asset\Service::createFolderByPath($assetPath));
                    $asset->setData($data);
                    $asset->save();
                }
            }
            
            return $asset;
        }

        return null;
    }
}