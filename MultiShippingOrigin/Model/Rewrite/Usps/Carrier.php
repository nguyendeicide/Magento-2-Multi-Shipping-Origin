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

namespace Acidpos\MultiShippingOrigin\Model\Rewrite\Usps;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Acidpos\MultiShippingOrigin\Helper\Data;

class Carrier extends \Magento\Usps\Model\Carrier
{    
    /**
     * Prepare and set request to this instance
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function setRequest(RateRequest $request)
    {
        $parentSetRequest = parent::setRequest($request);        
        $parentRawRequest = $parentSetRequest->_rawRequest;

        $destCountry = $request->getDestCountryId();
        $destRegionCode = $request->getDestRegionCode();
        $destRegionId = $this->_regionFactory->create()->loadByCode($destRegionCode, $destCountry)->getId();

        if ( $this->isMultiShippingOriginEnabled($request) 
            && $this->isMultiShippingOriginEnabledForUSPS($request)
            && $this->isInSelectedUsStates($destRegionId,$request) ){

            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/multiShippingOrigin-settings-enabled.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);                 
            $logger->info('Destination Region:'.$destRegionId);

            $secondaryCountryId = $this->_scopeConfig->getValue(
                Data::XML_PATH_SHIPPING_SECONDARY.'country_id',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $request->getStore()
            );
            
            $secondaryPostCode = $this->_scopeConfig->getValue(
                Data::XML_PATH_SHIPPING_SECONDARY.'postcode',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $request->getStore()
            );
            /* replacing origin shipping - USPS only uses country and postcode for calculate */

            if ( isset($secondaryCountryId) ){
                $parentRawRequest->setOrigCountry($this->_countryFactory->create()->load($secondaryCountryId)->getData('iso2_code'));
            }
        
            if ( isset($secondaryPostCode) ){
                $parentRawRequest->setOrigPostal($secondaryPostCode);
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

    public function isMultiShippingOriginEnabledForUSPS($request){
        
        $applyForMethods = $this->_scopeConfig->getValue(
            Data::XML_PATH_SHIPPING_SECONDARY.'apply_methods',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $request->getStore()
        );
        $arrayMethods = explode(',',$applyForMethods);

        return in_array("usps", $arrayMethods);
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
