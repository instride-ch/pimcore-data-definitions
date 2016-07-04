<?php

namespace ImportDefinitions\Model;

use Pimcore\File;

/**
 * Class Placeholder
 * @package ImportDefinitions\Model
 */
class Placeholder {

    /**
     * @param $placeholder
     * @param $data
     * @return string
     */
    public static function replace($placeholder, $data) {
        $myData = $data;

        foreach($myData as &$d) {
            $d = File::getValidFilename($d);
        }

        $placeholderHelper = new \Pimcore\Placeholder();
        return $placeholderHelper->replacePlaceholders($placeholder, $myData);
    }

}