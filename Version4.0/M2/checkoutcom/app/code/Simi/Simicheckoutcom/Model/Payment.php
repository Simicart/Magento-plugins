<?php

namespace Simi\Simicheckoutcom\Model;

class Payment extends \Magento\Payment\Model\Method\AbstractMethod
{

    protected $_code = 'simicheckoutcom';
    
    protected $_infoBlockType = 'Simi\Simicheckoutcom\Block\Payment';
    
    protected $_isOffline = true;
}
