<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\Paypalmobile\Model;



/**
 * Pay In Store payment method model
 */
class Paypal extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'paypal_mobile';

    
    protected $_infoBlockType = 'Simi\Paypalmobile\Block\Paypal';
    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;


  

}
