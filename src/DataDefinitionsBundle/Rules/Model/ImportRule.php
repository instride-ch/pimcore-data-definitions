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
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace Wvision\Bundle\DataDefinitionsBundle\Rules\Model;

use CoreShop\Component\Rule\Model\RuleTrait;
use Exception;
use Serializable;

class ImportRule implements ImportRuleInterface, Serializable
{
    use RuleTrait;

    protected int $id;

    public function getId()
    {
        return $this->id;
    }

    public function serialize()
    {
        return [
            'name' => $this->getName(),
            'active' => $this->getActive(),
            'actions' => $this->getActions()->getValues(),
            'conditions' => $this->getConditions()->getValues()
        ];
    }

    public function unserialize($serialized)
    {
        throw new Exception('not supported');
    }
}
