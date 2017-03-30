<?php

/**
 * Action Spend Point for Order
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Magestore Developer
 */

namespace Simi\Simirewardpoints\Model\Action\Spending;

class Order extends \Simi\Simirewardpoints\Model\Action\AbstractAction implements
    \Simi\Simirewardpoints\Model\Action\InterfaceAction
{

    /**
     * Calculate and return point amount that spent for order
     *
     * @return int
     */
    public function getPointAmount()
    {
        $order = $this->getData('action_object');
        return -(int) $order->getSimiRewardpointsSpent();
    }

    /**
     * get Label for this action, this is the reason to change
     * customer reward points balance
     *
     * @return string
     */
    public function getActionLabel()
    {
        return __('Spend points to purchase order');
    }

    public function getActionType()
    {
        return \Simi\Simirewardpoints\Model\Transaction::ACTION_TYPE_SPEND;
    }

    /**
     * get Text Title for this action, used when create an transaction
     *
     * @return string
     */
    public function getTitle()
    {
        $order = $this->getData('action_object');
        return __('Spend points to purchase order #%1', $order->getIncrementId());
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
        if ($this->_storeManager->getStore()->getCode() == \Magento\Store\Model\Store::ADMIN_CODE) {
            $editUrl = $this->_urlBuilder->getUrl('adminhtml/sales_order/view', ['order_id' => $transaction->getOrderId()]);
        } else {
            $editUrl = $this->_urlBuilder->getUrl('sales/order/view', ['order_id' => $transaction->getOrderId()]);
        }
        return __(
            'Spend points to purchase order %1',
            '<a href="' . $editUrl . '" title="'
                . __('View Order')
                . '">#' . $transaction->getOrderIncrementId() . '</a>'
        );
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
        $order = $this->getData('action_object');

        $transactionData = [
            'status' => \Simi\Simirewardpoints\Model\Transaction::STATUS_COMPLETED,
            'order_id' => $order->getId(),
            'order_increment_id' => $order->getIncrementId(),
            'order_base_amount' => $order->getBaseGrandTotal(),
            'order_amount' => $order->getGrandTotal(),
            'base_discount' => $order->getSimiRewardpointsBaseDiscount(),
            'discount' => $order->getSimiRewardpointsDiscount(),
            'store_id' => $order->getStoreId(),
        ];

        $this->setData('transaction_data', $transactionData);
        return parent::prepareTransaction();
    }
}
