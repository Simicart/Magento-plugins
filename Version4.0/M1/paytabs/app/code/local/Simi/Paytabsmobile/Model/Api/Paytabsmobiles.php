<?php

class Simi_Paytabsmobile_Model_Api_Paytabsmobiles extends Simi_Simiconnector_Model_Api_Abstract {
	 /**
     * override
     */
    public function setBuilderQuery()
    {
        $data = $this->getData();               
        $this->builderQuery = Mage::getModel('paytabsmobile/paytabsmobile');        
    }

    /**
     * @return override
     */
    public function store()
    {
        $data = $this->getData();
        $content = $data['contents_array'];
        $paytabs = $this->builderQuery->updatePaytabsPaymentv2($content);
        $detail = array();
        if(isset($paytabs['order'])){
            $entity = $paytabs['order'];        
            $info = $entity->toArray();
            $paytabsmobile = $this->getDetail($info);
	    $detail['order'] = $paytabsmobile['paytabsmobile'];
        }        
        $detail['message'] = $paytabs['message'];
        return $this->getDetail($detail);
    }
}