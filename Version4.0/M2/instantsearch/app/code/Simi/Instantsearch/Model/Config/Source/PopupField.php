<?php

namespace Simi\Instantsearch\Model\Config\Source;

class PopupField implements \Magento\Framework\Option\ArrayInterface
{

    const SUGGEST = 'suggest';
    const PRODUCT = 'product';

    /**
     *
     * @return array
     */
    public function toOptionArray()
    {
        $this->options = [
            ['value' => self::SUGGEST, 'label' => __('Suggest')],
            ['value' => self::PRODUCT, 'label' => __('Product')],
        ];
        return $this->options;
    }
}
