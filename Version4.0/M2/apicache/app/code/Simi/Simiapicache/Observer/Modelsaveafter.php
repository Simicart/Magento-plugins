<?php

namespace Simi\Simiapicache\Observer;

use Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\ObjectManagerInterface as ObjectManager;

class Modelsaveafter implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface as ObjectManager
     */
    private $simiObjectManager;

    public function __construct(ObjectManager $simiObjectManager)
    {
        $this->simiObjectManager = $simiObjectManager;
    }

    /**
     * Add site map data to api get storeview
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $passedModels = [
            'Magento\Reports\Model\Event',
            'Magento\Customer\Model\Visitor',
            'Magento\Quote\Model\Quote',
            'Magento\Quote\Model\Quote\Address',
            'Magento\Quote\Model\Quote\Item',
            'Magento\Quote\Model\Quote\Item\Option',
            'Magento\Theme\Model\Theme',
            'Magento\Security\Model\AdminSessionInfo',
            'Magento\Cms\Model\Block',
            'Magento\Ui\Model\Bookmark',
            'Magento\Customer\Model\Backend\Customer\Interceptor',
            //'Magento\Framework\App\Config\Value\Interceptor',
            'Magento\Cms\Model\Page',
            'Magento\Reports\Model\Flag',
            'Magento\Widget\Model\Widget\Instance',
        ];
        if (!$observer->getObject())
            return $this;
        $modelClass = get_class($observer->getObject());
        foreach ($passedModels as $passedModel) {
            if ($passedModel == $modelClass){
                return $this;
            }
        }
        $this->simiObjectManager->get('Simi\Simiapicache\Helper\Data')->flushCache();
    }
}
