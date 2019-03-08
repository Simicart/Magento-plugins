<?php

namespace Simi\Hyperpay\Helper;

// use Magento\Store\Model\ScopeInterface;

class Data extends \Simi\Simiconnector\Helper\Data
{
	public function getConfigHyperpay($path) {
		return $this->scopeConfig->getValue('simi_hyperpay/simi_hyperpay_main/'.$path);
	}
}