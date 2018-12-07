<?php

namespace Simi\Instantsearch\Model\Config\Source;

class SearchingType implements \Magento\Framework\Option\ArrayInterface
{

    const MAGENTO = 'magento';
    const SIMI = 'simi';

    /**
     *
     * @return array
     */
    public function toOptionArray()
    {
        $this->options = [
            ['value' => self::MAGENTO, 'label' => __("Magento's search Logic")],
            ['value' => self::SIMI, 'label' => __('Search by name, description, sku')],
        ];
        return $this->options;
    }
}
