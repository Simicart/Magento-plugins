<?php

namespace Simi\Simiapicache\Block\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Flushbutton extends Field
{

    /**
     * SyncButton constructor.
     * @param Context $context
     * @param array $data
     */
    
    protected function _getElementHtml(AbstractElement $element)
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'flush_api_cache',
                'label' => __('Flush'),
                'onclick' => 'setLocation(\'' . $this->getUrl('simiapicache/index/flush') . '\')',
            ]
        );
        return $button->toHtml();
    }
}
