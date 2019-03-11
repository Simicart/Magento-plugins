<?php

namespace Simi\Simibarclays\Model\ResourceModel;

class Simibarclays extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function _construct()
    {
        $this->_init('simibarclays_transaction', 'entity_id');
    }
}
