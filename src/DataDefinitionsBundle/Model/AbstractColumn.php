<?php
/**
 * Data Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2019 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Wvision\Bundle\DataDefinitionsBundle\Model;

abstract class AbstractColumn
{
    /**
     * @var string
     */
    public $identifier;

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @param array $values
     */
    public function setValues(array $values)
    {
        foreach ($values as $key => $value) {
            if ($key === 'o_type') {
                continue;
            }

            $setter = sprintf('set%s', ucfirst($key));

            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }
}

class_alias(AbstractColumn::class, 'ImportDefinitionsBundle\Model\AbstractColumn');
