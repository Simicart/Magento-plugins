<?php

namespace Simi\Simiapicache\Controller\Index;

class Flush extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        $simiobjectManager = $this->_objectManager;
        $simiobjectManager->get('Simi\Simiapicache\Helper\Data')->flushCache();
        echo 'done';
    }
}
