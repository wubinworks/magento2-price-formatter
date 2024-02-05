<?php
/**
 * Copyright © Wubinworks. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Wubinworks\PriceFormatter\Plugin\Directory;

use Magento\Framework\Locale\Currency as LocaleCurrency;
use Wubinworks\PriceFormatter\Helper\Data as Helper;
use Wubinworks\PriceFormatter\Model\Config\Backend\PriceFormat;

/**
 * Plugin for Magento\Directory\Model\Currency
 *
 * This plugin should only be used in frontend
 * Affects \Magento\Framework\Pricing\Helper\Data::currency
 */
class Currency
{
    /**
     * @var \Wubinworks\PriceFormatter\Helper\Data
     */
    protected $helper;

    /**
     * Constructor
     *
     * @param \Wubinworks\PriceFormatter\Helper\Data $helper
     */
    public function __construct(
        Helper $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Change output format for frontend price-box-mixin.js
     *
     * @param \Magento\Directory\Model\Currency $subject
     * @param string $result
     *
     * @return array|string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetOutputFormat(
        \Magento\Directory\Model\Currency $subject,
        $result
    ) {
        if($this->helper->isEnabled() && $result != '') {
            $pricePlaceholder = $this->helper->getPricePlaceholder();
            return [
                'enabled' => $this->helper->isEnabled(),
                'requiredPrecision' => $this->helper->getPrecisionByPricePlaceholder($pricePlaceholder),
                'pattern' => $result
            ];
        }

        return $result;
    }

    /**
     * Change precision
     *
     * @param \Magento\Directory\Model\Currency $subject
     * @param   float $price
     * @param   int $precision
     * @param   array $options
     * @param   bool $includeContainer
     * @param   bool $addBrackets
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeFormatPrecision(
        \Magento\Directory\Model\Currency $subject,
        $price,
        $precision,
        $options = [],
        $includeContainer = true,
        $addBrackets = false
    ): array {
        if($this->helper->isEnabled()) {
            $pricePlaceholder = $this->helper->getPricePlaceholder();
            $precision = $this->helper->getPrecisionByPricePlaceholder($pricePlaceholder);
        }

        return [$price, $precision, $options, $includeContainer, $addBrackets];
    }

    /**
     * Extract the price number and format it
     *
     * @param \Magento\Directory\Model\Currency $subject
     * @param string $result
     * @param float $price
     * @param array $options
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterFormatTxt(
        \Magento\Directory\Model\Currency $subject,
        $result,
        $price,
        $options = []
    ): string {
        if($this->helper->isEnabled()) {
            $symbol = array_key_exists(LocaleCurrency::CURRENCY_OPTION_SYMBOL, $options)
                ? $options[LocaleCurrency::CURRENCY_OPTION_SYMBOL] : $subject->getCurrencySymbol();
            $number = $this->removeCurrencySymbol($symbol, $result);

            /**
             * For $subject->getOutputFormat()
             */
            if (!array_key_exists(LocaleCurrency::CURRENCY_OPTION_DISPLAY, $options)
                || $options[LocaleCurrency::CURRENCY_OPTION_DISPLAY] !== \Magento\Framework\Currency::NO_SYMBOL) {

                return str_replace(
                    [$this->helper->getPricePlaceholder(), PriceFormat::SYMBOL],
                    [$number, $symbol],
                    $this->helper->getPriceFormat()
                );
            }
        }

        return $result;
    }

    /**
     * Remove currency symbol.
     *
     * @param string $symbol
     * @param string $input
     *
     * @return string
     */
    protected function removeCurrencySymbol(string $symbol, string $input): string
    {
        $input = preg_replace('/' . preg_quote($symbol, '/') . '/u', '', $input);
        return preg_replace('/[[:^ascii:]]/', '', $input);
    }
}
