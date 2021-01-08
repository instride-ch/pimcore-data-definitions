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
use Wvision\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;

final class ExportDefinitionEvent extends Event implements DefinitionEventInterface
{
    protected $definition;
    protected $params = [];
    protected $subject;

    public function __construct(ExportDefinitionInterface $definition, $subject = null, array $params = [])
    {
        $this->definition = $definition;
        $this->subject = $subject;
        $this->params = $params;
    }

    public function getDefinition(): ExportDefinitionInterface
    {
        return $this->definition;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}

