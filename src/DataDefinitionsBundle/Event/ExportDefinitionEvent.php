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

namespace WVision\Bundle\DataDefinitionsBundle\Event;

use WVision\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;
use Symfony\Component\EventDispatcher\Event;

final class ExportDefinitionEvent extends Event
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

class_alias(ExportDefinitionEvent::class, 'ImportDefinitionsBundle\Event\ExportDefinitionEvent');
