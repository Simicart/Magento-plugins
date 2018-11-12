<?php

namespace Simi\Simiapicache\Controller\Adminhtml\Index;

class Flush extends \Magento\Backend\App\Action
{
    public function execute()
    {
        try{
            $simiobjectManager = $this->_objectManager;
            $helper = $simiobjectManager->get('Simi\Simiapicache\Helper\Data');
            $autoFlush = $helper->getStoreConfig('simiapicache/general/auto_flush');
            if($autoFlush){
                $helper->flushCache();
                $this->messageManager->addSuccess(__('Api Cache has been Flushed.'));
            }else{
                $api_cache = $helper->getStoreConfig('simiapicache/general/model_api');
                if(!$api_cache || $api_cache == ''){
                    throw new \Exception(__('Please select api cache to flush !'));
                }else{
                    $api_cache = explode(',',$api_cache);
                    foreach ($api_cache as $api){
                        $helper->flushCache($api);
                    }
                    $this->messageManager->addSuccess(__('Api Cache has been Flushed.'));
                }
            }
        }catch (\Exception $e){
            $this->messageManager->addError($e->getMessage());
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath(
            'adminhtml/system_config/edit',
            [
                'section' => 'simiapicache'
            ]
        );
    }
}
