<?php
declare(strict_types=1);

namespace DR\Internationalization\Money;

use DR\Internationalization\Number\NumberParser;
use InvalidArgumentException;
use Money\Currencies;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Money\Parser\DecimalMoneyParser;

class MoneyProvider
{
    private Currency            $currency;
    private ?DecimalMoneyParser $parser = null;

    /** @phpstan-var Currencies<Currency[]> */
    private Currencies $currencies;

    /**
     * @phpstan-param Currencies<Currency[]>|null $currencies
     */
    public function __construct(string $currencyCode, ?Currencies $currencies = null)
    {
        $this->currency   = new Currency($currencyCode);
        $this->currencies = $currencies ?? new ISOCurrencies();
    }

    /**
     * Method to parse money in decimal or int values to the cents value based on currency.
     * example: 2.34 -> 234, 23 -> 2300
     * @throws MoneyParseException
     * @throws ParserException
     */
    public function parse(string $amount, ?string $currencyCode = null): Money
    {
        if ($this->parser === null) {
            $this->parser = new DecimalMoneyParser($this->currencies);
        }

        $parsedAmount = NumberParser::parseFloat($amount);
        if ($parsedAmount === false) {
            throw new MoneyParseException('Amount: ' . $amount . ' and currencyCode: ' . ($currencyCode ?? $this->currency->getCode()));
        }

        return $this->parser->parse((string)$parsedAmount, $currencyCode !== null ? new Currency($currencyCode) : $this->currency);
    }

    /**
     * Method to get a Money object. Must be provided an integer-ish value.
     * Works: 2300 or '3'
     * Does not work: 2.34 or '2.34'
     *
     * @param string|int $amount
     *
     * @throws InvalidArgumentException If amount is not integer
     */
    public function getMoney($amount, ?string $currencyCode = null): Money
    {
        return new Money($amount, $currencyCode !== null ? new Currency($currencyCode) : $this->currency);
    }
}
