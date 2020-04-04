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

namespace Wvision\Bundle\DataDefinitionsBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Wvision\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;

final class ExportDefinitionEvent extends Event implements DefinitionEventInterface
{
    /**
     * @var ExportDefinitionInterface
     */
    protected $definition;

    /**
     * @var mixed
     */
    protected $subject;

    protected $params = [];

    /**
     * @param ExportDefinitionInterface $definition
     * @param null                      $subject
     * @param array                     $params
     */
    public function __construct(ExportDefinitionInterface $definition, $subject = null, $params = [])
    {
        $this->definition = $definition;
        $this->subject = $subject;
        $this->params = $params;
    }

    /**
     * @return ExportDefinitionInterface
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}

