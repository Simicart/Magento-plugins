<?php
namespace Simi\Simibraintree\Model\Api;

class Braintreeapis extends \Simi\Simiconnector\Model\Api\Apiabstract {
	 /**
     * override
     */
    public function setBuilderQuery()
    {
        $this->builderQuery = $this->simiObjectManager->create('Simi\Simibraintree\Model\Simibraintree');   
    }

    /**
     * @return override
     */
    public function store()
    {
        $data = $this->getData();
        $content = $data['contents'];  
        $detail['message'] = $this->builderQuery->updateBraintreePayment($content);
        return $this->getDetail($detail);
    }
}