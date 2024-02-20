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

namespace Instride\Bundle\DataDefinitionsBundle\Interpreter;

use Pimcore\Model\DataObject\Listing;
use Instride\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;

class ObjectResolverInterpreter implements InterpreterInterface
{
    public function interpret(InterpreterContextInterface $context): mixed
    {
        if (!$context->getValue()) {
            return null;
        }

        $class = 'Pimcore\Model\DataObject\\'.ucfirst($context->getConfiguration()['class']);
        $lookup = 'getBy'.ucfirst($context->getConfiguration()['field']);

        /**
         * @var Listing $listing
         */
        $listing = $class::$lookup($context->getValue());
        $listing->setUnpublished($context->getConfiguration()['match_unpublished']);

        if ($listing->count() === 1) {
            return $listing->current();
        }

        return null;
    }
}
