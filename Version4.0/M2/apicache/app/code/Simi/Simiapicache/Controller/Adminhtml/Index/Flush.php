<?php

namespace Simi\Simiapicache\Controller\Adminhtml\Index;

class Flush extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $simiobjectManager = $this->_objectManager;
        $simiobjectManager->get('Simi\Simiapicache\Helper\Data')->flushCache();
        $this->messageManager->addSuccess(__('Api Cache has been Flushed.'));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath(
            'adminhtml/system_config/edit',
            [
                'section' => 'simiapicache'
            ]
        );
    }
}
