<?php
/**
 * NOTICE OF LICENSE
 * The contents of this file are subject to the Lifetime Media Group, Inc. Public License Version 1.1 (the "License");
 * you may not use this file except in compliance with the License. You may obtain a copy and view the license at
 * https://www.lifetimemediagroup.com/license-agreement
 *
 * DISCLAIMER
 * Redistribution of this code is strictly prohibited and trackable. Each user must obtain legal license to use this software.
 * Please also note: Do not edit or add to this file if you wish to upgrade in the future.
 *
 * @category    Lifetime Media Group POS
 * @package     lifetime_pos
 * @copyright   Copyright (c) 2021 Lifetime Media Group, Inc. (https://www.lifetimemediagroup.com)
 * @license     https://www.lifetimemediagroup.com/license-agreement
 */
namespace Acidpos\MultiShippingOrigin\Model\Rewrite;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Acidpos\MultiShippingOrigin\Helper\Data;

class Shipping extends \Magento\Shipping\Model\Shipping
{
    /**
     * Retrieve all methods for supplied shipping data
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return $this
     * @todo make it ordered
     */
    public function collectRates(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        $parentCollectRates = parent::collectRates($request);        

        $regionId = $this->_scopeConfig->getValue(
            Data::XML_PATH_SHIPPING_SECONDARY.'region_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $request->getStore()
        );
        
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/multiShippingOrigin-settings.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);     
        
        $logger->info('RegionId for '. $request->getStore().':'.$regionId);
        /*
        $logger->info('Module status:'.$this->_helperData->getSecondaryConfig('active'));
        $logger->info('Country Id:'.$this->_helperData->getSecondaryConfig('country_id'));
        $logger->info('Region Id:'.$this->_helperData->getSecondaryConfig('region_id'));
        $logger->info('Postcode:'.$this->_helperData->getSecondaryConfig('postcode'));
        $logger->info('City:'.$this->_helperData->getSecondaryConfig('city'));
        $logger->info('Street line 1:'.$this->_helperData->getSecondaryConfig('street_line1'));
        $logger->info('Street line 2:'.$this->_helperData->getSecondaryConfig('street_line2'));
        $logger->info('Shipping methods:'.$this->_helperData->getSecondaryConfig('apply_methods'));

        /*
        $logger->info('OrigCountryId:'.$request->getOrigCountryId());
        $logger->info('OrigRegionId:'.$request->getOrigRegionId());
        $logger->info('OrigPostCode:'.$request->getOrigPostcode());
        $logger->info('OrigCity:'.$request->getOrigCity());

        $logger->info('DestCountryId:'.$request->getDestCountryId());
        $logger->info('DestRegionId:'.$request->getDestRegionId());
        $logger->info('DestRegionCode:'.$request->getDestRegionCode());
        $logger->info('DestPostCode:'.$request->getDestPostcode());
        $logger->info('DestCity:'.$request->getDestCity());*/
        
        return $parentCollectRates;
/*
        $result = parent::collectRates($request);
        $shippingPrice = 50;

        foreach ($result->getAllRates() as $method) {
            $method->setPrice($shippingPrice);
        }

        return $result;
*/
        /*$storeId = $request->getStoreId();
        if (!$request->getOrig()) {
            $request->setCountryId(
                $this->_scopeConfig->getValue(
                    Shipment::XML_PATH_STORE_COUNTRY_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $request->getStore()
                )
            )->setRegionId(
                $this->_scopeConfig->getValue(
                    Shipment::XML_PATH_STORE_REGION_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $request->getStore()
                )
            )->setCity(
                $this->_scopeConfig->getValue(
                    Shipment::XML_PATH_STORE_CITY,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $request->getStore()
                )
            )->setPostcode(
                $this->_scopeConfig->getValue(
                    Shipment::XML_PATH_STORE_ZIP,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $request->getStore()
                )
            );
        }

        $limitCarrier = $request->getLimitCarrier();
        if (!$limitCarrier) {
            $carriers = $this->_scopeConfig->getValue(
                'carriers',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );

            foreach ($carriers as $carrierCode => $carrierConfig) {
                $this->collectCarrierRates($carrierCode, $request);
            }
        } else {
            if (!is_array($limitCarrier)) {
                $limitCarrier = [$limitCarrier];
            }
            foreach ($limitCarrier as $carrierCode) {
                $carrierConfig = $this->_scopeConfig->getValue(
                    'carriers/' . $carrierCode,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $storeId
                );
                if (!$carrierConfig) {
                    continue;
                }
                $this->collectCarrierRates($carrierCode, $request);
            }
        }

        return $this;*/
    }    
}
