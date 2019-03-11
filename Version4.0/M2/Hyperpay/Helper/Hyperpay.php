<?php

/**
 * Connector data helper
 */

namespace Simi\Hyperpay\Helper;

use Magento\Store\Model\ScopeInterface;

class Hyperpay extends \Magento\Framework\App\Helper\AbstractHelper
{
	const XML_PATH_ACTIVE = "simi_hyperpay/simi_hyperpay_main/active";
	const XML_PATH_USER_ID = "simi_hyperpay/simi_hyperpay_main/user_id";
    const XML_PATH_PASSWORD = "simi_hyperpay/simi_hyperpay_main/password";
    const XML_PATH_ENTITY_ID = "simi_hyperpay/simi_hyperpay_main/entity_id";
    const XML_PATH_CURRENCY = "simi_hyperpay/simi_hyperpay_main/currency";
    const XML_PATH_PAYMENT_TYPE = "simi_hyperpay/simi_hyperpay_main/payment_type";
    const XML_PATH_NOTIFICATION_URL = "simi_hyperpay/simi_hyperpay_main/notification_url";

	public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function isActive($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ACTIVE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    public function getUserId($storeId = null) 
    {
    	return $this->scopeConfig->getValue(
            self::XML_PATH_ACTIVE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    
    public function getPassword($storeId = null) 
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PASSWORD,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    public function getEntityId($storeId = null) 
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENTITY_ID,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    public function getCurrency($storeId = null) 
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CURRENCY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    public function getPaymentType($storeId = null) 
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_TYPE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    public function getNotificationUrl($storeId = null) 
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NOTIFICATION_URL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
