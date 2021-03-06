<?php
namespace Simi\Simibraintree\Helper;

class Data extends \Simi\Simiconnector\Helper\Data
{
    protected $paymentGateway;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {
        parent::__construct($context, $simiObjectManager, $directoryList);
        $this->paymentGateway = new \Braintree\Gateway([
            'environment' => $this->getEnvironment(),
            'merchantId' => $this->getMerchantId(),
            'publicKey' => $this->getPublicKey(),
            'privateKey' => $this->getPrivateKey()
        ]);
    }

    public function getEnvironment()
    {
        $environment = 'production';
        if ($this->getStoreConfig('payment/simibraintree/is_sandbox')) {
            $environment = 'sandbox';
        }
        return $environment;
    }

    public function getMerchantId()
    {
        return $this->getStoreConfig('payment/simibraintree/merchant_id');
    }

    public function getPublicKey()
    {
        return $this->getStoreConfig('payment/simibraintree/public_key');
    }

    public function getPrivateKey()
    {
        return $this->getStoreConfig('payment/simibraintree/private_key');
    }

    public function generateCustomerId($customerId, $email)
    {
        return md5($customerId . '-' . $email);
    }

    public function getTokenKey()
    {
        // $isBraintreeCustomerExist = false;
        // $customerId = '';
        // $customEmail = '';
        // $braintreCustomerId ='';
        
        // if ($customer = $this->getCustomer()) {
        //     $customEmail= $customer->getEmail();
        //     $customerId = $customer->getId();

        //     if ($customEmail && $customerId) {
        //         $braintreCustomerId = $this->generateCustomerId($customerId, $customEmail);
        //         try {
        //             $isBraintreeCustomerExist = $this->paymentGateway->customer()->find($braintreCustomerId);
        //         } catch (\Exception $e) {
        //             $isBraintreeCustomerExist = false;
        //         }
        //     }
        // }

        // $params = ["merchantAccountId" => $this->getMerchantId()];
        // if ($isBraintreeCustomerExist) {
        //     $params['customerId'] = $braintreCustomerId;
        // }
        // try {
        //     $token = $this->paymentGateway->clientToken()->generate($params);
        // } catch (Exception $e) {
        //     Mage::logException($e);
        //     return false;
        // }
        return $this->simiObjectManager->create('\Magento\Braintree\Model\Adapter\BraintreeAdapterFactory')->create()->generate();
    }

    public function getCustomer()
    {
        $customerSession = $this->simiObjectManager->get('Magento\Customer\Model\Session');
        if ($customerSession->isLoggedIn()) {
            return $customerSession->getCustomer();
        }
        return null;
    }

    public function createTransaction($data)
    {
        $orderId = $data->order_id;
        $amount = $data->amount;
        $nonce = $data->nonce;
        $type = $data->type;
        $description = $data->description;
        $status = false;

        $options = [
            'submitForSettlement' => true
        ];
        if ($type == 'PayPal') {
            $options['paypal'] = [
                'description' => $description
            ];
        }

		$order = $this->simiObjectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($orderId);
		$billingAddress = $order->getBillingAddress();
		$shipping = [];
		if ($order->getShippingAddress()) {
			$shippingAddress = $order->getShippingAddress();
			$shipping = [
                'firstName' => $shippingAddress->getData('firstname'),
                'lastName' => $shippingAddress->getData('lastname'),
                'company' => $shippingAddress->getData('company'),
                'streetAddress' => $shippingAddress->getData('street'),
                'locality' => $shippingAddress->getData('city'),
                'region' => $shippingAddress->getRegionCode(),
                'postalCode' => $shippingAddress->getData('postcode'),
                'countryCodeAlpha2' => $shippingAddress->getData('country_id')
            ];
        }
        
        $params = [
            'amount' => $amount,
            'orderId' => $orderId,
            'paymentMethodNonce' => $nonce,
            'options' => $options,
            'customer' => [
                'firstName' => $order->getData('customer_firstname'),
				'lastName' => $order->getData('customer_firstname'),
				'phone' => $billingAddress->getData('telephone'),
                'email' => $order->getData('customer_email')
            ],
            'billing' => [
                'firstName' => $billingAddress->getData('firstname'),
                'lastName' => $billingAddress->getData('lastname'),
                'company' => $billingAddress->getData('company'),
                'streetAddress' => $billingAddress->getData('street'),
                'locality' => $billingAddress->getData('city'),
                'region' => $billingAddress->getRegionCode(),
                'postalCode' => $billingAddress->getData('postcode'),
                'countryCodeAlpha2' => $billingAddress->getData('country_id')
			],
			'shipping' => $shipping
        ];

        $result = $this->paymentGateway->transaction()->sale($params);
        return $result;
    }
}
