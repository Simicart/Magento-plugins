<?php
/**
 * Created by PhpStorm.
 * User: scott
 * Date: 1/30/18
 * Time: 8:50 AM
 */

namespace Simi\Simipwa\Controller\Adminhtml\Cache;

use Magento\Backend\App\Action;

class Delete extends Action
{

    public function execute()
    {
        $result = $this->_objectManager->get('Simi\Simipwa\Helper\Data')->clearAppCaches();
        return $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode($result));
    }
}