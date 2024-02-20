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
use Instride\Bundle\DataDefinitionsBundle\Model\ImportDefinitionInterface;

final class ImportDefinitionEvent extends Event implements DefinitionEventInterface
{
    protected ImportDefinitionInterface $definition;
    protected $subject;
    protected array $options;

    public function __construct(ImportDefinitionInterface $definition, $subject = null, array $options = [])
    {
        $this->definition = $definition;
        $this->subject = $subject;
        $this->options = $options;
    }

    public function getDefinition(): ImportDefinitionInterface
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
