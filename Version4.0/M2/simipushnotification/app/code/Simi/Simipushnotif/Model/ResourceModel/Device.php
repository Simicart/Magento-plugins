<?php

namespace Simi\Simipushnotif\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Connector Resource Model
 */
class Device extends AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('simipush_device', 'device_id');
    }
}
