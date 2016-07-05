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
        );");

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
