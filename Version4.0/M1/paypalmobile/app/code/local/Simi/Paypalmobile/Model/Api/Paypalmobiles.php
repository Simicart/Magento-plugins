<?php

class Simi_Paypalmobile_Model_Api_Paypalmobiles extends Simi_Simiconnector_Model_Api_Abstract {
	 /**
     * override
     */
    public function setBuilderQuery()
    {
        $data = $this->getData();               
        $this->builderQuery = Mage::getModel('paypalmobile/paypalmobile');        
    }

    /**
     * @return override
     */
    public function store()
    {
        $data = $this->getData();
        $content = $data['contents'];
        $paypal = $this->builderQuery->updatePaypalPaymentv2($content);
        $detail = array();
        if(isset($paypal['order'])){
        	$entity = $paypal['order'];        
	        $info = $entity->toArray();
            $paypalmobile = $this->getDetail($info);
	        $detail['order'] = $paypalmobile['paypalmobile'];
        }        
        $detail['message'] = $paypal['message'];
        return $this->getDetail($detail);
    }
}