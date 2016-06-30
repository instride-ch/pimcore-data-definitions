<?php

namespace ImportDefinitions;

use Pimcore\API\Plugin as PluginLib;
use Pimcore\Db;

/**
 * Pimcore Plugin
 *
 * Class Plugin
 * @package ImportDefinitions
 */
class Plugin extends PluginLib\AbstractPlugin implements PluginLib\PluginInterface
{
    /**
     * @var \Zend_Translate
     */
    protected static $_translate;

    /**
     *
     */
    public function init()
    {
        parent::init();

        \Pimcore::getEventManager()->attach('system.console.init', function (\Zend_EventManager_Event $e) {
            /** @var \Pimcore\Console\Application $application */
            $application = $e->getTarget();

            // add a namespace to autoload commands from
            $application->addAutoloadNamespace('ImportDefinitions\\Console', PIMCORE_PLUGINS_PATH.'/ImportDefinitions/lib/ImportDefinitions/Console');
        });
    }

    /**
     * @return bool
     */
    public static function install()
    {
        $db = Db::get();

        $db->query("CREATE TABLE `importdefinitions_log` (
          `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `definition` int NOT NULL,
          `o_id` int NOT NULL
        ");

        return true;
    }

    /**
     * @return bool
     */
    public static function uninstall()
    {
        return true;
    }

    /**
     * @return bool
     */
    public static function isInstalled()
    {
        return true;
    }

    /**
     * get translation directory.
     *
     * @return string
     */
    public static function getTranslationFileDirectory()
    {
        return PIMCORE_PLUGINS_PATH.'/ImportDefinitions/static/texts';
    }

    /**
     * get translation file.
     *
     * @param string $language
     *
     * @return string path to the translation file relative to plugin directory
     */
    public static function getTranslationFile($language)
    {
        if (is_file(self::getTranslationFileDirectory()."/$language.csv")) {
            return "/ImportDefinitions/static/texts/$language.csv";
        } else {
            return '/ImportDefinitions/static/texts/en.csv';
        }
    }

    /**
     * get translate.
     *
     * @param $lang
     *
     * @return \Zend_Translate
     */
    public static function getTranslate($lang = null)
    {
        if (self::$_translate instanceof \Zend_Translate) {
            return self::$_translate;
        }
        if (is_null($lang)) {
            try {
                $lang = \Zend_Registry::get('Zend_Locale')->getLanguage();
            } catch (\Exception $e) {
                $lang = 'en';
            }
        }

        self::$_translate = new \Zend_Translate(
            'csv',
            PIMCORE_PLUGINS_PATH.self::getTranslationFile($lang),
            $lang,
            array('delimiter' => ',')
        );

        return self::$_translate;
    }
}
