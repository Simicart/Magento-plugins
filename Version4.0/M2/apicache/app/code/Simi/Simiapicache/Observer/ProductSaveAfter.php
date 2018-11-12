<?php
/**
 * Created by PhpStorm.
 * User: macos
 * Date: 11/12/18
 * Time: 2:10 PM
 */

namespace Simi\Simiapicache\Observer;

use Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\ObjectManagerInterface as ObjectManager;

class ProductSaveAfter implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface as ObjectManager
     */
    private $simiObjectManager;

    public function __construct(ObjectManager $simiObjectManager)
    {
        $this->simiObjectManager = $simiObjectManager;
    }

    public function execute(Observer $observer){
        $helper = $this->simiObjectManager->get('Simi\Simiapicache\Helper\Data');
        $product = $observer->getProduct();
        $id = $product->getId();
        $helper->removeOnList($id,'urldicts',false);
        $helper->removeOnList($id,'products_detail',false);
        $helper->removeOnList($id,'products_list');
    }
}