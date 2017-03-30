<?php

namespace Simi\Simirewardpoints\Controller;

use Magento\Framework\App\RequestInterface;

/**
 * Class AbstractAction
 * @package Simi\Simirewardpoints\Controller
 */

abstract class AbstractAction extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;
    /**
     * @var \Simi\Simirewardpoints\Helper\Data
     */
    protected $_helperData;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Simi\Simirewardpoints\Helper\Config
     */
    protected $_helperConfig;
    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $_modelPage;

    /**
     * @var \Simi\Simirewardpoints\Helper\Block\Spend
     */
    protected $_helperSpend;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;
    /**
     * @var \Simi\Simirewardpoints\Block\Checkout\Form
     */
    protected $_checkoutForm;
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_checkoutCart;
    /**
     * @var \Simi\Simirewardpoints\Helper\Point
     */
    protected $_helperPoint;
    /**
     * @var \Simi\Simirewardpoints\Helper\Calculation\Spending
     */
    protected $_calculationSpending;

    /**
     * @var \Simi\Simirewardpoints\Model\CustomerFactory
     */
    protected $_rewardpointsCustomerFactory;

    /**
     * @var \Magento\Framework\Session\SessionManager
     */
    protected $_sessionManager;

    /**
     * AbstractAction constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Simi\Simirewardpoints\Helper\Data $helperData
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Simi\Simirewardpoints\Helper\Config $helperConfig
     * @param \Magento\Cms\Model\Page $modelPage
     * @param \Simi\Simirewardpoints\Helper\Block\Spend $helperSpend
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Simi\Simirewardpoints\Block\Checkout\Form $checkoutForm
     * @param \Magento\Checkout\Model\Cart $checkoutCart
     * @param \Simi\Simirewardpoints\Helper\Point $helperPoint
     * @param \Simi\Simirewardpoints\Model\CustomerFactory $rewardpointsCustomerFactory
     * @param \Simi\Simirewardpoints\Helper\Calculation\Spending $calculationSpending
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Simi\Simirewardpoints\Helper\Data $helperData,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Simi\Simirewardpoints\Helper\Config $helperConfig,
        \Magento\Cms\Model\Page $modelPage,
        \Simi\Simirewardpoints\Helper\Block\Spend $helperSpend,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Simi\Simirewardpoints\Block\Checkout\Form $checkoutForm,
        \Magento\Checkout\Model\Cart $checkoutCart,
        \Simi\Simirewardpoints\Helper\Point $helperPoint,
        \Simi\Simirewardpoints\Model\CustomerFactory $rewardpointsCustomerFactory,
        \Simi\Simirewardpoints\Helper\Calculation\Spending $calculationSpending,
        \Magento\Framework\Session\SessionManager $sessionManager
    ) {
        $this->_request = $request;
        $this->_rewardpointsCustomerFactory = $rewardpointsCustomerFactory;
        $this->_helperData = $helperData;
        $this->_customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->_storeManager = $storeManager;
        $this->_helperConfig = $helperConfig;
        $this->_modelPage = $modelPage;
        $this->_helperSpend = $helperSpend;
        $this->_checkoutSession = $checkoutSession;
        $this->_checkoutForm = $checkoutForm;
        $this->_checkoutCart = $checkoutCart;
        $this->_helperPoint = $helperPoint;
        $this->_calculationSpending = $calculationSpending;
        $this->_sessionManager = $sessionManager;
        parent::__construct($context);
    }

    /**
     * @return $this
     */

    public function dispatch(RequestInterface $request)
    {
        if (!$this->_helperData->isEnable()) {
            $this->_redirect('customer/account');
            $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
        }
        $action = $this->getRequest()->getActionName();
        if ($action != 'policy' && $action != 'redirectLogin') {
            // Check customer authentication
            if (!$this->_customerSession->isLoggedIn()) {
                $this->_customerSession->setAfterAuthUrl(
                    $this->_url->getUrl($this->_request->getFullActionName('/'))
                );
                $this->_redirect('customer/account/login');
                $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
            }
        }
        return parent::dispatch($request);
    }
}
