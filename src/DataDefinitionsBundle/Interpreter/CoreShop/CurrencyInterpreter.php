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

namespace Wvision\Bundle\DataDefinitionsBundle\Interpreter\CoreShop;

use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;
use Wvision\Bundle\DataDefinitionsBundle\Interpreter\InterpreterInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\DefinitionInterface;
use Wvision\Bundle\DataDefinitionsBundle\Model\Mapping;
use Pimcore\Model\DataObject\Concrete;

final class CurrencyInterpreter implements InterpreterInterface
{
    /**
     * @var CurrencyRepositoryInterface
     */
    private $currencyRepository;

    /**
     * @param CurrencyRepositoryInterface $currencyRepository
     */
    public function __construct(CurrencyRepositoryInterface $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function interpret(Concrete $object, $value, Mapping $map, $data, DefinitionInterface $definition, $params, $configuration)
    {
        return $this->currencyRepository->getByCode($value);
    }
}

class_alias(CurrencyInterpreter::class, 'ImportDefinitionsBundle\Interpreter\CoreShop\CurrencyInterpreter');
