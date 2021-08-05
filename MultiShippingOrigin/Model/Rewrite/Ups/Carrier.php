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

namespace Acidpos\MultiShippingOrigin\Model\Rewrite\Ups;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Acidpos\MultiShippingOrigin\Helper\Data;

class Carrier extends \Magento\Ups\Model\Carrier
{
    /**
     * Prepare and set request to this instance
     *
     * @param RateRequest $request
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function setRequest(RateRequest $request)
    {
        $parentSetRequest = parent::setRequest($request);        
        $parentRawRequest = $parentSetRequest->_rawRequest;

        $destCountry = $parentRawRequest->getDestCountry();
        $destRegionCode = $parentRawRequest->getDestRegionCode();              

        $destRegionId = $this->_regionFactory->create()->loadByCode($destRegionCode, $destCountry)->getId();

        if ( $this->isMultiShippingOriginEnabled($request) 
            && $this->isMultiShippingOriginEnabledForUPS($request) 
            && $this->isInSelectedUsStates($destRegionId,$request) ){
            /*
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/multiShippingOrigin-settings-enabled.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);                 
            $logger->info('methods:'.$destRegionCode);
            */    
            $secondaryCountryId = $this->_scopeConfig->getValue(
                Data::XML_PATH_SHIPPING_SECONDARY.'country_id',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $request->getStore()
            );

            $secondaryRegionId = $this->_scopeConfig->getValue(
                Data::XML_PATH_SHIPPING_SECONDARY.'region_id',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $request->getStore()
            );
            
            $secondaryPostCode = $this->_scopeConfig->getValue(
                Data::XML_PATH_SHIPPING_SECONDARY.'postcode',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $request->getStore()
            );

            $secondaryCity = $this->_scopeConfig->getValue(
                Data::XML_PATH_SHIPPING_SECONDARY.'city',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $request->getStore()
            );
            
            /* replace origin shipping*/
            if ( isset($secondaryCountryId) ){
                $parentRawRequest->setOrigCountry($this->_countryFactory->create()->load($secondaryCountryId)->getData('iso2_code'));
            }
            
            if ( is_numeric($secondaryRegionId) && isset($secondaryRegionId) ) {
                $secondaryRegionCode = $this->_regionFactory->create()->load($secondaryRegionId)->getCode();
                $parentRawRequest->setOrigRegionCode($secondaryRegionCode);
            }    

            if ( isset($secondaryPostCode) ){
                $parentRawRequest->setOrigPostal($secondaryPostCode);
            } 
            
            if ( isset($secondaryCity) ){
                $parentRawRequest->setOrigCity($secondaryCity);
            }
            
        }

        return $parentSetRequest;
    }

    public function isMultiShippingOriginEnabled($request){

        return boolval(
            $this->_scopeConfig->getValue(
                Data::XML_PATH_SHIPPING_SECONDARY.'active',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $request->getStore()
            )
        );

    }

    /**
     * Check if UPS is enabled.
     * @return boolean
     */

    public function isMultiShippingOriginEnabledForUPS($request){
        
        $applyForMethods = $this->_scopeConfig->getValue(
            Data::XML_PATH_SHIPPING_SECONDARY.'apply_methods',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $request->getStore()
        );
        $arrayMethods = explode(',',$applyForMethods);

        return in_array("ups", $arrayMethods);
    }   

    /**
     * Check if Destination ID is in selected US states configuration admin.
     * @return boolean
     */

    public function isInSelectedUsStates($destRegionId,$request){
        
        $enabledUsStates = $this->_scopeConfig->getValue(
            Data::XML_PATH_SHIPPING_SECONDARY.'use_for_us_state',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $request->getStore()
        );
        $arraySelectedUsStates = explode(',',$enabledUsStates);
        
        return in_array($destRegionId, $arraySelectedUsStates);
    }   
}
