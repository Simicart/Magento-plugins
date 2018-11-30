<?php

namespace Simi\Simifbconnect\Block;

/**
* 
*/
class Config extends \Magento\Framework\View\Element\Template
{
	public $helper;
	public $simiObjectManager;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Simi\Simifbconnect\Helper\Data $helper,
		\Magento\Framework\ObjectManagerInterface $simiObjectManager
	)
    {
    	$this->helper = $helper;
    	$this->simiObjectManager = $simiObjectManager;
    	parent::__construct($context);
    }

    public function getHelper(){
    	return $this->helper;
    }
}
