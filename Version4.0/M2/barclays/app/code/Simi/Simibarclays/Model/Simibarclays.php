<?php
namespace Simi\Simibarclays\Model;

/**
* 
*/
class Simibarclays extends \Magento\Framework\Model\AbstractModel
{
	public $simiObjectManager;

	public function _construct()
    {
        $this->_init('Simi\Simibarclays\Model\ResourceModel\Simibarclays');
    }
}