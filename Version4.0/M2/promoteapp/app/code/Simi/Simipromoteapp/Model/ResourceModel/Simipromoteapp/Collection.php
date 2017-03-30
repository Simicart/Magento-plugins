<?php

namespace Simi\Simipromoteapp\Model\ResourceModel\Simipromoteapp;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Simi\Simipromoteapp\Model\Simipromoteapp', 'Simi\Simipromoteapp\Model\ResourceModel\Simipromoteapp');
    }
}
