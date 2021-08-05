<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Acidpos\MultiShippingOrigin\Model\Config\Source;

class AllUsStates implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $countryInformationAcquirer;
    

    public function __construct(
        \Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformationAcquirer
    ) {
            $this->countryInformationAcquirer = $countryInformationAcquirer;
    }

    public function toOptionArray()
    {
        $countries = $this->countryInformationAcquirer->getCountriesInfo();
        foreach ($countries as $country) {
            if($country->getId() == 'US'){
                $regions = [];
                if ($availableRegions = $country->getAvailableRegions()) {
                    foreach ($availableRegions as $region) {
                        $regions[] = [
                            'value' => $region->getId(),
                            'label' => $region->getName()
                        ];
                    }
                }
            }  
        }
        return $regions;

    }
}
