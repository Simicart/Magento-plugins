<?php

class Simi_Simibraintree_Model_Api_Braintreeapis extends Simi_Simiconnector_Model_Api_Abstract {
	 /**
     * override
     */
    public function setBuilderQuery()
    {
        $this->builderQuery = Mage::getModel('simibraintree/simibraintree');        
    }

    /**
     * @return override
     */
    public function store()
    {
        $data = $this->getData();
        $content = $data['contents'];  
        $detail['message'] = Mage::getModel('simibraintree/simibraintree')->updateBraintreePayment40($content);
        return $this->getDetail($detail);
    }
}