<?php
/**
 * Created by PhpStorm.
 * User: scott
 * Date: 9/26/18
 * Time: 2:27 PM
 */

namespace Simi\Simibraintree\Model;

use \Magento\Payment\Model\Method\AbstractMethod;

class Method extends AbstractMethod
{
	const SUPPORT_PAYPAL ='paypal';

	const SUPPORT_PAYPAL_CREDIT = 'paypal_credit';

	const SUPPORT_VENMO = 'venmo';

	const SUPPORT_APPLE_PAY ='apple';

	const SUPPORT_GOOGLE_PAY = 'google';

    protected $_code = 'simibraintree';

    protected $_infoBlockType = \Simi\Simibraintree\Block\Info::class;
}