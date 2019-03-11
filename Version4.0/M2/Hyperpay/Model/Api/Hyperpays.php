<?php
namespace Simi\Hyperpay\Model\Api;

class Hyperpays extends \Simi\Simiconnector\Model\Api\Apiabstract
{

    public function setBuilderQuery()
    {die('111');
        $data                 = $this->getData();
        $parameters           = $data['params'];
        $this->hyperPay = $this->simiObjectManager->get('\Simi\Hyperpay\Helper\Hyperpay');
        $this->hyperPay->setData($data);
        
        throw new \Simi\Simiconnector\Helper\SimiException(__('Invalid method fffff.'), 4);

    }

}
?>