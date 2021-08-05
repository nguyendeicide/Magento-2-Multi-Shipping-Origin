<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Acidpos\MultiShippingOrigin\Model\Config\Source;

class AllShippingMethods implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $_shippingConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Shipping\Model\Config $shippingConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Model\Config $shippingConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_shippingConfig = $shippingConfig;
    }

    /**
     * Return array of shipping methods.
     * @return array
     */
    public function toOptionArray()
    {        
        $methods = [
            ['value' => 'ups', 'label' => 'UPS'],
            ['value' => 'usps', 'label' => 'United States Postal Service'],
            ['value' => 'fedex', 'label' => 'Federal Express'],
            ['value' => 'dhl', 'label' => 'DHL']
        ];
        
        return $methods;
    }
}
