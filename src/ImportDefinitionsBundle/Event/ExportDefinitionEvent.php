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
 * @copyright  Copyright (c) 2016-2018 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitionsBundle\Event;

use ImportDefinitionsBundle\Model\ExportDefinitionInterface;
use Symfony\Component\EventDispatcher\Event;
use ImportDefinitionsBundle\Model\ImportDefinitionInterface;

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

    /**
     * @param ExportDefinitionInterface $definition
     * @param mixed $subject
     */
    public function __construct(ExportDefinitionInterface $definition, $subject = null)
    {
        $this->definition = $definition;
        $this->subject = $subject;
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
}