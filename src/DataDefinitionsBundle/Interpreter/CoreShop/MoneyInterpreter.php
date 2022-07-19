<?php


namespace Wvision\Bundle\DataDefinitionsBundle\Interpreter\CoreShop;


use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Currency\Model\Money;
use Wvision\Bundle\DataDefinitionsBundle\Context\InterpreterContextInterface;
use Wvision\Bundle\DataDefinitionsBundle\Interpreter\InterpreterInterface;
use function is_string;

final class MoneyInterpreter implements InterpreterInterface
{
    private $currencyRepository;

    public function __construct(CurrencyRepositoryInterface $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    public function interpret(InterpreterContextInterface $context)
    {
        $value = $this->getValue($context->getValue(), $context->getConfiguration());
        $currency = $this->resolveCurrency($value, $context->getConfiguration());

        if (null === $currency) {
            return null;
        }

        return new Money($value, $currency);
    }

    /**
     * @param $value
     * @param $context->getConfiguration()
     *
     * @return int
     */
    private function getValue($value, $context->getConfiguration())
    {
        $inputIsFloat = $context->getConfiguration()['isFloat'];

        $value = preg_replace("/[^0-9,.]+/", "", $value);

        if (is_string($value)) {
            $value = str_replace(',', '.', $value);
            $value = (float)$value;
        }

        if ($inputIsFloat) {
            $value = (int)round(round($value, 2) * 100, 0);
        }

        return (int)$value;
    }

    /**
     * @param string $value
     * @param array $context->getConfiguration()
     *
     * @return CurrencyInterface|null
     */
    private function resolveCurrency($value, $context->getConfiguration())
    {
        $currency = null;

        if (preg_match('/^\pL+$/u', $value)) {
            $currencyCode = preg_replace("/[^a-zA-Z]+/", "", $value);

            $currency = $this->currencyRepository->getByCode($currencyCode);
        }

        if ($currency === null && isset($context->getConfiguration()['currency']) && null !== $context->getConfiguration()['currency']) {
            $currency = $this->currencyRepository->find($context->getConfiguration()['currency']);
        }

        return $currency;
    }
}
