<?php
/**
 * Created by PhpStorm.
 * User: scott
 * Date: 1/30/18
 * Time: 10:54 AM
 */

namespace Simi\Simipushnotif\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Simi\Simipushnotif\Model\ResourceModel\Notification as NotificationRM;
use Simi\Simipushnotif\Model\ResourceModel\Notification\Collection;

class Notification extends AbstractModel
{
    /**
     * @var \Simi\Simipushnotif\Helper\Website
     * */
    public $websiteHelper;
    public $simiObjectManager;

    /**
     * Notification constructor.
     * @param Context $context
     * @param ObjectManagerInterface $simiObjectManager
     * @param Registry $registry
     * @param NotificationRM $resource
     * @param Collection $resourceCollection
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $simiObjectManager,
        Registry $registry,
        /**/
        NotificationRM $resource,
        Collection $resourceCollection
    ) {
    
        $this->simiObjectManager = $simiObjectManager;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }

    public function toOptionTypeHash()
    {
        $platform = [
            '1' => __('Product In-app'),
            '2' => __('Category In-app'),
            '3' => __('Website Page'),
        ];
        return $platform;
    }

    public function getMessage()
    {
        $message = $this->getCollection()
                    ->addFieldToFilter('status',1)
                    ->getLastItem();
        return $message;
    }
}
