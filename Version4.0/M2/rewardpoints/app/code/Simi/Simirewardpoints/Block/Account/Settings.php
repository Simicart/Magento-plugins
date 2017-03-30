<?php

/**
 * Simirewardpoints Settings
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Block\Account;

class Settings extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    public $_objectManager;

    /**
     * @var \Simi\Simirewardpoints\Helper\Customer
     */
    protected $_helperCustomer;

    /**
     * Settings constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Simi\Simirewardpoints\Helper\Customer $helperCustomer
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Simi\Simirewardpoints\Helper\Customer $helperCustomer
    ) {
        parent::__construct($context, []);
        $this->_helperCustomer = $helperCustomer;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * get current reward points account
     *
     * @return \Simi\Simirewardpoints\Model\Customer
     */
    public function getRewardAccount()
    {
        $rewardAccount = $this->_helperCustomer->getAccount();
        if (!$rewardAccount->getId()) {
            $rewardAccount->setIsNotification(1)
                    ->setExpireNotification(1);
        }
        return $rewardAccount;
    }
}
