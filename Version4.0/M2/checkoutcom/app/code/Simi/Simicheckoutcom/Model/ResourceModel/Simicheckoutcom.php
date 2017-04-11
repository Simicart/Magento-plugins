<?php

namespace Simi\Simicheckoutcom\Model\ResourceModel;

/**
 * Connector Resource Model
 */
class Simicheckoutcom extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('simicheckoutcom', 'simicheckoutcom_id');
    }
}
