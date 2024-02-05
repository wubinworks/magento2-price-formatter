<?php
/**
 * Copyright © Wubinworks. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Wubinworks\PriceFormatter\Helper;

use Magento\Framework\Exception\LocalizedException;
use Wubinworks\PriceFormatter\Model\Config\Backend\PriceFormat;

/**
 * System config helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public const XML_PATH_CURRENCY_OPTIONS_PRICE_FORMAT = 'currency/options/wubinworks_price_format';

    /**
     * @var ?string
     */
    protected $priceFormat;

    /**
     * @var ?string
     */
    protected $pricePlaceholder;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->priceFormat = null;
        $this->pricePlaceholder = null;
        parent::__construct($context);
    }

    /**
     * Get current store system configuration value
     *
     * @param string $path
     * @param string $scopeType
     * @param null|int|string $scopeCode
     * @return mixed
     */
    public function getConfig($path, $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * Is Price Formatter enabled?
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return strlen($this->getPriceFormat()) ? true : false;
    }

    /**
     * Get price format
     *
     * @return string
     */
    public function getPriceFormat()
    {
        if(is_null($this->priceFormat)) {
            $this->priceFormat = (string)$this->getConfig(self::XML_PATH_CURRENCY_OPTIONS_PRICE_FORMAT);
        }

        return $this->priceFormat;
    }

    /**
     * Get precision by price placeholder
     *
     * @param ?string $placeholder
     *
     * @return array|int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPrecisionByPricePlaceholder(?string $placeholder = null)
    {
        $arr = [
            PriceFormat::PRICE => 2,
            PriceFormat::PRICE_NO_DECIMAL => 0,
            PriceFormat::PRICE_BITCOIN => 8
        ];

        if ($placeholder !== null) {
            if(!array_key_exists($placeholder, $arr)) {
                throw new LocalizedException(__('Unknown Price Placeholder: %1', $placeholder));
            }

            return $arr[$placeholder];
        }

        return $arr;
    }

    /**
     * Validate price format
     *
     * @param string $value
     *
     * @return string matched price placeholder
     * @throws \Exception need catch internally
     */
    public function validatePriceFormat(string $value): string
    {
        $delimiter = '/';
        $pattern = '(' . implode('|', array_keys($this->getPrecisionByPricePlaceholder())) . ')';

        if(1 !== preg_match_all($delimiter . $pattern . $delimiter, $value, $matches, PREG_SET_ORDER)) {
            throw new \Exception($value . ' does not contain exactly ONE price placeholder.');
        }

        return $matches[0][1];
    }

    /**
     * @param string $value
     * @return string[]
     */
    public function getUnknownPlaceholders(string $value)
    {
        $knownPlaceholders = array_keys($this->getPrecisionByPricePlaceholder());
        $knownPlaceholders[] = PriceFormat::SYMBOL;
        $unknownPlaceholders = [];
        preg_match_all('/{{[^{}]*}}/', $value, $matches, PREG_SET_ORDER);
        foreach($matches as $match) {
            if(!in_array($match[0], $knownPlaceholders)
                    && !in_array($match[0], $unknownPlaceholders)) {
                $unknownPlaceholders[] = $match[0];
            }
        }

        return $unknownPlaceholders;
    }

    /**
     * Get price placeholder
     *
     * @return string
     * @throws \Exception need catch internally
     */
    public function getPricePlaceholder(): string
    {
        if(is_null($this->pricePlaceholder)) {
            $this->pricePlaceholder = $this->validatePriceFormat($this->getPriceFormat());
        }

        return $this->pricePlaceholder;
    }
}
