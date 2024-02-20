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
 * @copyright 2024 instride AG (https://instride.ch)
 * @license   https://github.com/instride-ch/DataDefinitions/blob/5.0/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace Instride\Bundle\DataDefinitionsBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Instride\Bundle\DataDefinitionsBundle\Model\ExportDefinitionInterface;

final class ExportDefinitionEvent extends Event implements DefinitionEventInterface
{
    protected ExportDefinitionInterface $definition;
    protected $subject;
    protected array $params = [];

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
