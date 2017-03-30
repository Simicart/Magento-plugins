<?php

/**
 * Action change points by admin
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Model\Action;

class Admin extends \Simi\Simirewardpoints\Model\Action\AbstractAction implements
    \Simi\Simirewardpoints\Model\Action\InterfaceAction
{

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $_auth;

    public function __construct(\Magento\Backend\Model\Auth $auth)
    {
        $this->_auth = $auth;
    }

    /**
     * Calculate and return point amount that admin changed
     *
     * @return int
     */
    public function getPointAmount()
    {
        $actionObject = $this->getData('action_object');
        if (!is_object($actionObject)) {
            return 0;
        }
        return (int) $actionObject->getPointAmount();
    }

    /**
     * get Label for this action, this is the reason to change
     * customer reward points balance
     *
     * @return string
     */
    public function getActionLabel()
    {
        return __('Changed by Admin');
    }

    public function getActionType()
    {
        return \Simi\Simirewardpoints\Model\Transaction::ACTION_TYPE_BOTH;
    }

    /**
     * get Text Title for this action, used when create an transaction
     *
     * @return string
     */
    public function getTitle()
    {
        $actionObject = $this->getData('action_object');
        if (!is_object($actionObject)) {
            return '';
        }
        return (string) $actionObject->getData('title');
    }

    /**
     * get HTML Title for action depend on current transaction
     *
     * @param \Simi\Simirewardpoints\Model\Transaction $transaction
     * @return string
     */
    public function getTitleHtml($transaction = null)
    {
        if (is_null($transaction)) {
            return $this->getTitle();
        }
        if (Mage::app()->getStore()->isAdmin()) {
            return '<strong>' . $transaction->getExtraContent() . ': </strong>' . $transaction->getTitle();
        }
        return $transaction->getTitle();
    }

    /**
     * prepare data of action to storage on transactions
     * the array that returned from function $action->getData('transaction_data')
     * will be setted to transaction model
     *
     * @return \Simi\Simirewardpoints\Model\Action\Interface
     */
    public function prepareTransaction()
    {
        $transactionData = [
            'status' => \Simi\Simirewardpoints\Model\Transaction::STATUS_COMPLETED,
        ];
        if ($user = $this->_auth->getUser()) {
            $transactionData['extra_content'] = $user->getUsername();
        }
        $actionObject = $this->getData('action_object');
        if (is_object($actionObject) && $actionObject->getExpirationDay() && $this->getPointAmount() > 0) {
            $transactionData['expiration_date'] = $this->getExpirationDate($actionObject->getExpirationDay());
        }
        $this->setData('transaction_data', $transactionData);
        return parent::prepareTransaction();
    }
}
