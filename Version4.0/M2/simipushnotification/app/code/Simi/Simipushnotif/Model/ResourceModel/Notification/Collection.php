<?php
/**
 * Created by PhpStorm.
 * User: scott
 * Date: 1/30/18
 * Time: 10:55 AM
 */

namespace Simi\Simipushnotif\Model\ResourceModel\Notification;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Simi\Simipushnotif\Model\Notification', 'Simi\Simipushnotif\Model\ResourceModel\Notification');
    }
}
