<?php

namespace Simi\Simirewardpoints\Model\ResourceModel\Rate;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'rate_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Simi\Simirewardpoints\Model\Rate', 'Simi\Simirewardpoints\Model\ResourceModel\Rate');
    }
}
