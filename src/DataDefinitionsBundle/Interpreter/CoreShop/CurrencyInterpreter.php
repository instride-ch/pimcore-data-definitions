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

namespace Wvision\Bundle\DataDefinitionsBundle\Interpreter\CoreShop;

use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;
use Wvision\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;
use Wvision\Bundle\DataDefinitionsBundle\Interpreter\InterpreterInterface;

final class CurrencyInterpreter implements InterpreterInterface
{
    private $currencyRepository;

    public function __construct(CurrencyRepositoryInterface $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    public function interpret(InterpreterContextInterface $context): mixed
    {
        return $this->currencyRepository->getByCode($context->getValue());
    }
}


