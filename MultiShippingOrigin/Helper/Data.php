<?php

namespace Acidpos\MultiShippingOrigin\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{

	const XML_PATH_SHIPPING_SECONDARY = 'shipping/multi_shipping_origin/';

	public function getConfigValue($field, $storeId = null)
	{
		return $this->scopeConfig->getValue(
			$field, ScopeInterface::SCOPE_STORE, $storeId
		);
	}

	public function getSecondaryConfig($code, $storeId = null)
	{

		return $this->getConfigValue(self::XML_PATH_SHIPPING_SECONDARY . $code, $storeId);
	}

}