<?php

/**
 *
 * Copyright © 2016 Simicommerce. All rights reserved.
 */

namespace Simi\Hyperpay\Controller\Index;
use Simi\Hyperpay\Helper\Hyperpay;
class Checkout extends \Magento\Framework\App\Action\Action
{

    // public $object;
    // public function __construct(\Simi\Hyperpay\Helper\Hyperpay $object){
    //     $this->object = $object;
    // }
    public function execute()
    {
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $object = $objectManager->create("\Simi\Hyperpay\Helper\Hyperpay");
        $userId = $object->getUserId();
        $password = $object->getPassword();
        $entityId = $object->getEntityId();
        $currency = $object->getCurrency();
        $paymentType = $object->getPaymentType();
        $notificationUrl = $object->getNotificationUrl();
        $amount = '1000';
        $url = 'https://oppwa.com/v1/checkouts';

        $data = array(
            
            "authentication.userId"=>$userId,
            "authentication.password"=>$password,
            "authentication.entityId"=>$entityId,
            "amount"=>"10000",
            "currency"=>$currency,
            "paymentType"=>$paymentType,
            "notificationUrl"=>$notificationUrl,
            "amount"=>$amount //12->19 number

        );
// print_r($data);die();   
        $fields_string = http_build_query($data);
        header("Content-type:application/json");
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($data));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

        //execute post
        
        $result = curl_exec($ch);
        curl_close($ch);
       
        $result = (json_decode($result, true));
        
        print_r($result);   
    }
}
