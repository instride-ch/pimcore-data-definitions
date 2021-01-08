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

use Symfony\Contracts\EventDispatcher\Event;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;

final class ImportDefinitionEvent extends Event implements DefinitionEventInterface
{
    protected $definition;
    protected $subject;
    protected $options;

    public function __construct(ImportDefinitionInterface $definition, $subject = null, array $options = [])
    {
        $this->definition = $definition;
        $this->subject = $subject;
        $this->options = $options;
    }

    public function getDefinition() : ImportDefinitionInterface
    {
        return $this->definition;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}

