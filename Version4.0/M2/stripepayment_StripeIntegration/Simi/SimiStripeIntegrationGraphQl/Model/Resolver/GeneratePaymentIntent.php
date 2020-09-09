<?php

declare(strict_types=1);

namespace Simi\SimiStripeIntegrationGraphQl\Model\Resolver;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\App\Config\ScopeConfigInterface;

class GeneratePaymentIntent implements ResolverInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Config
     */
    protected $stripeConfigModel;

    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var QuoteFactory
     */
    protected $quoteModelFactory;

    /**
     * @var PaymentIntentFactory
     */
    protected $paymentIntentFactory;

    /**
     * @inheritdoc
     */
    public function __construct(
        \StripeIntegration\Payments\Model\Config $stripeConfigModel,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Quote\Model\QuoteFactory $quoteModelFactory,
        \StripeIntegration\Payments\Model\PaymentIntentFactory $paymentIntentFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->stripeConfigModel = $stripeConfigModel;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->quoteModelFactory = $quoteModelFactory;
        $this->paymentIntentFactory = $paymentIntentFactory;
    }

    public function resolve(
        $field,
        $context,
        $info,
        array $value = null,
        array $args = null
    ) {
        if (!$args || !isset($args['cartId'])) {
            throw new \Exception(__("No Cart id sent"), 1);
        }

        if (!$args || !isset($args['token'])) {
            throw new \Exception(__("No Token sent"), 1);
        }
        $quoteId = $this->quoteIdMaskFactory->create()->load($args['cartId'], 'masked_id')->getData('quote_id');
        if (!$quoteId) {
            throw new \Exception(__("Quote Mask not valid"), 1);
        }
        $quoteModel = $this->quoteModelFactory->create()->load($quoteId);
        if (!$quoteModel->getId()) {
            throw new \Exception(__("Quote Mask not valid"), 1);
        }
        //$this->stripeConfigModel->initStripe();
        $quoteModel->setPaymentMethod('stripe_payments');
        $paymentIntentsModel = $this->paymentIntentFactory->create();
        $paymentIntentsModel->create($quoteModel, null, null, $args['token']);
        

        return $paymentIntentsModel->getClientSecret();
    }
}
