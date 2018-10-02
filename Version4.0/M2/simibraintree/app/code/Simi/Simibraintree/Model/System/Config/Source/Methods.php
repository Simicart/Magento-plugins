<?php
/**
 * Created by PhpStorm.
 * User: scott
 * Date: 9/26/18
 * Time: 3:16 PM
 */

namespace Simi\Simibraintree\Model\System\Config\Source;


class Methods implements \Magento\Framework\Option\ArrayInterface
{
	protected $_methods = [
		\Simi\Simibraintree\Model\Method::SUPPORT_PAYPAL => 'Paypal',
		\Simi\Simibraintree\Model\Method::SUPPORT_PAYPAL_CREDIT =>'Paypal Credit',
		\Simi\Simibraintree\Model\Method::SUPPORT_VENMO => 'Venmo',
        \Simi\Simibraintree\Model\Method::SUPPORT_APPLE_PAY =>'Apple Pay',
        \Simi\Simibraintree\Model\Method::SUPPORT_GOOGLE_PAY => 'Google Pay',
    ];


    public function toOptionArray()
    {
        $methods = $this->_methods;

        $options = [];
        foreach ($methods as $code => $label) {
            $options[] = ['value' => $code, 'label' => __($label)];
        }
        return $options;
    }


}