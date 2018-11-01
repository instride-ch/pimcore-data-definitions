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

use Symfony\Component\EventDispatcher\Event;
use ImportDefinitionsBundle\Model\ImportDefinitionInterface;

final class ImportDefinitionEvent extends Event
{
    /**
     * @var ImportDefinitionInterface
     */
    protected $definition;

    /**
     * @var mixed
     */
    protected $subject;

    /**
     * @param ImportDefinitionInterface $definition
     * @var array
     */
    protected $options;

    /**
     * @param ImportDefinitionInterface $definition
     * @param mixed $subject
     * @param array $options
     */
    public function __construct(ImportDefinitionInterface $definition, $subject = null, $options = [])
    {
        $this->definition = $definition;
        $this->subject = $subject;
        $this->options = $options;
    }

    /**
     * @return ImportDefinitionInterface
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
    public function getOptions()
    {
        return $this->options;
    }
}