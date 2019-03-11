<?php

/**
 *
 * Copyright © 2016 Simicommerce. All rights reserved.
 */

namespace Simi\Hyperpay\Controller\Index;

class Payment extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        $id ='636D4673770B55F2635130161672ECB3.prod02-vm-tx04';
        $url = 'https://oppwa.com/v1/checkouts/payment/'.$id;

      
        $data = array(
            
            "authentication.userId"=>"8a8294174d0595bb014d05d829e701d1",
            "authentication.password"=>"sy6KJsT8",
            "authentication.entityId"=>"8a8294174b7ecb28014b9699220015ca",
            "amount"=>"10000",
            "currency"=>"EUR",
            "paymentType"=>"DB",
            "card.number"=>"1234567891111", //12->19 number
            "card.expiryMonth"=>"07",
            "card.expiryYear" =>"2019",
            "virtualAccount.accountId"=>"", //AN100  [\s\S]{1,100}  The identifier of the shopper's virtual account.
            "bankAccount.holder" =>"", //Holder of the bank account  AN128  {4,128}


        );

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
        echo $result['ndc']; 
    }
}
