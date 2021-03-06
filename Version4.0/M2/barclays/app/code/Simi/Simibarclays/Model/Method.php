<?php

namespace Simi\Simibarclays\Model;

use \Magento\Payment\Model\Method\AbstractMethod;

class Method extends AbstractMethod
{
    protected $_code = 'simibarclays';

    protected $_infoBlockType = \Simi\Simibarclays\Block\Info::class;

    public function isInitializeNeeded()
    {
        return false;
    }

    public function getConfigPaymentAction() {
        return false;
    }
}