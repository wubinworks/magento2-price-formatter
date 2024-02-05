<?php
/**
 * Copyright © Wubinworks All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Wubinworks\PriceFormatter\Model\Config\Backend;

use Magento\Framework\Exception\LocalizedException;
use Wubinworks\PriceFormatter\Helper\Data as Helper;

/**
 * Backend model for Price Format
 */
class PriceFormat extends \Magento\Framework\App\Config\Value
{
    public const PRICE = '{{price}}';
    public const PRICE_NO_DECIMAL = '{{price_no_decimal}}';
    public const PRICE_BITCOIN = '{{price_bitcoin}}';
    public const SYMBOL = '{{symbol}}';

    /**
     * @var \Wubinworks\PriceFormatter\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Constructor
     *
     * @param \Wubinworks\PriceFormatter\Helper\Data $helper
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param ?\Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param ?\Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Helper $helper,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        ?\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        ?\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->escaper = $escaper;
        $this->messageManager = $messageManager;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Check if value is empty string or contains exactly one price placeholder
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $value = (string)$this->getValue();
        if (strlen($value)) {
            try {
                $this->helper->validatePriceFormat($value);
                $unknownPlaceholders = $this->helper->getUnknownPlaceholders($value);
                if(!empty($unknownPlaceholders)) {
                    $this->messageManager->addNotice(
                        __(
                            'Just a notice, you got unkonwn placeholder(s): %1',
                            implode(', ', $unknownPlaceholders)
                        )
                    );
                }

            } catch(\Exception $e) {
                $placeholders = array_keys($this->helper->getPrecisionByPricePlaceholder());
                array_unshift(
                    $placeholders,
                    str_replace(' ', '&nbsp;', $this->escaper->escapeHtml($value))
                );
                throw new LocalizedException(
                    __(
                        'Saving "%1" failed.<br />You need exactly <strong>ONE</strong> of %2, %3 and %4',
                        $placeholders
                    )
                );
            }
        }

        return parent::beforeSave();
    }
}
