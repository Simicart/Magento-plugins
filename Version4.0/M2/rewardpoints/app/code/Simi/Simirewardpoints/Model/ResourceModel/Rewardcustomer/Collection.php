<?php

namespace Simi\Simirewardpoints\Model\ResourceModel\Rewardcustomer;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

/**
 * Flat customer online grid collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends SearchResult
{

    /**
     * Init collection select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinLeft(['customer_reward' => $this->getTable('simirewardpoints_customer')], 'main_table.entity_id = customer_reward.customer_id', ['point_balance']);
        $this->getSelect()->columns(['point_balance' => "IF(customer_reward.point_balance,customer_reward.point_balance,0)"]);
        return $this;
    }
}
