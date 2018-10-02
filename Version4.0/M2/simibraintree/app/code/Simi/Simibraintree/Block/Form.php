<?php 
namespace Simi\Simibraintree\Block;

/**
* Form Block
*/
class Form extends \Magento\Framework\View\Element\Template
{
    public $scopeConfig;
    public $storeManager;
    public $braintreeHelper;

    public $supportedMethods =[];
    public function __construct(
        \Simi\Simiconnector\Block\Context $context,
        \Simi\Simibraintree\Helper\Data $helper
    ){
        $this->braintreeHelper = $helper;
        parent::__construct($context);
        $this->setTemplate('form.phtml');
    }

    public function getStoreConfig($path){
    	return $this->_scopeConfig->getValue($path,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getMethodsAvailable(){
    	return explode(',', $this->getStoreConfig('payment/simibraintree/support_methods'));
    }


    public function getPaymentConfigJson($order){
    	$this->supportedMethods = $this->getMethodsAvailable();

    	$config =[];
    	$config['authorization'] = $this->braintreeHelper->getTokenKey();
    	$config['container'] ='#dropin-container';
    	$this->_getPaypalConfig($config,$order);
    	$this->_getPaypalCreditConfig($config,$order);
    	$this->_get3DSecureConfig($config,$order);
    	$this->_getVenmoConfig($config,$order);
    	$this->_getGoogleConfig($config,$order);
    	$this->_getAppleConfig($config,$order);

    	return $config;
    }

    public function isUse3DSecure(){
    	return $this->getStoreConfig('payment/simibraintree/use_3d_secure');
    }

    private function _getPaypalConfig(&$config,$order){
    	if(in_array(\Simi\Simibraintree\Model\Method::SUPPORT_PAYPAL, $this->supportedMethods)){
    		$config['paypal'] = [
    			'flow'=> 'vault'
    		];
    	}
    }

    private function _getPaypalCreditConfig(&$config,$order){
    	if(in_array(\Simi\Simibraintree\Model\Method::SUPPORT_PAYPAL_CREDIT, $this->supportedMethods)){
    		$config['paypalCredit'] = [
    			'flow'=> 'vault'
    		];
    	}
    }

    private function _get3DSecureConfig(&$config,$order){
    	$amount = $order->getData('grand_total_incl_tax') ? $order->getData('grand_total_incl_tax') : $order->getData('grand_total'); 
    	$currencyCode   = $this->_storeManager->getStore()->getCurrentCurrencyCode(); 
    	if($this->isUse3DSecure()){
    		$config['threeDSecure'] = [
    			'amount'=> $amount
    		];
    	}
    }

    private function _getVenmoConfig(&$config,$order){
    	if(in_array(\Simi\Simibraintree\Model\Method::SUPPORT_VENMO, $this->supportedMethods)){
    		$config['venmo'] = [
    			'allowNewBrowserTab'=> false
    		];
    	}
    }

    private function _getGoogleConfig(&$config,$order){
    	$amount = $order->getData('grand_total_incl_tax') ? $order->getData('grand_total_incl_tax') : $order->getData('grand_total');
    	$currencyCode   = $this->_storeManager->getStore()->getCurrentCurrencyCode(); 
    	if(in_array(\Simi\Simibraintree\Model\Method::SUPPORT_GOOGLE_PAY, $this->supportedMethods)){
    		$config['googlePay'] = [
    			'merchantId'=> $this->getStoreConfig('payment/simibraintree/google_account'),
    			'transactionInfo'=>[
    				'totalPriceStatus'=>'FINAL',
    				'totalPrice'=> $amount,
    				'currencyCode'=> $currencyCode
    			],
    			'cardRequirements'=>[
    				'billingAddressRequired' => true
    			]
    		];
    	}
    }

    private function _getAppleConfig(&$config,$order){
    	$amount = $order->getData('grand_total_incl_tax') ? $order->getData('grand_total_incl_tax') : $order->getData('grand_total');
    	if(in_array(\Simi\Simibraintree\Model\Method::SUPPORT_APPLE_PAY, $this->supportedMethods)){
    		$config['applePay'] = [
    			'displayName'=>  $this->_storeManager->getStore()->getName(),
    			'paymentRequest' => [
    				'total'=>[
    					'label' =>$this->_storeManager->getStore()->getName(),
    					'amount' => $amount
    				],
    				'requiredBillingContactFields' => ["postalAddress"]
    			]
    		];
    	}
    }
}