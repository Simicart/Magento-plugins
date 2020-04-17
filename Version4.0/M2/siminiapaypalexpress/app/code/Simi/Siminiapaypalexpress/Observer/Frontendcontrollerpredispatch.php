<?php

namespace Simi\Siminiapaypalexpress\Observer;

use Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\ObjectManagerInterface as ObjectManager;

class Frontendcontrollerpredispatch implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface as ObjectManager
     */
    private $simiObjectManager;

    public function __construct(ObjectManager $simiObjectManager)
    {
        $this->simiObjectManager = $simiObjectManager;
    }
    public function execute(Observer $observer)
    {
        $uri = $_SERVER['REQUEST_URI'];
        $pos = strpos($uri,'paypal_express.html');
        if ($pos === false)
            $pos = strpos($uri,'paypal_express_failure.html');
        $link = $this->simiObjectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')
            ->getValue('simiconnector/general/pwa_studio_url');
        if($pos && $link){
            $newUri = $link . substr($uri, $pos);
            header("Location: {$newUri}");
            exit;
        }
    }

}
