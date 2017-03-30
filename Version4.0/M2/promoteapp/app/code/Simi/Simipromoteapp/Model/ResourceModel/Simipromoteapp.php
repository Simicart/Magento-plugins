<?php

namespace Simi\Simipromoteapp\Model\ResourceModel;

class Simipromoteapp extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('simipromoteapp', 'simipromoteapp_id');
    }
}
