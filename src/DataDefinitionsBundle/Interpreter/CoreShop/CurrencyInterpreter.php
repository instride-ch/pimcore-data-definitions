<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Interpreter\CoreShop;

use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;
use Instride\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;
use Instride\Bundle\DataDefinitionsBundle\Interpreter\InterpreterInterface;

final class CurrencyInterpreter implements InterpreterInterface
{
    private $currencyRepository;

    public function __construct(
        CurrencyRepositoryInterface $currencyRepository,
    ) {
        $this->currencyRepository = $currencyRepository;
    }

    public function interpret(InterpreterContextInterface $context): mixed
    {
        return $this->currencyRepository->getByCode($context->getValue());
    }
}
