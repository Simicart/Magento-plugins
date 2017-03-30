<?php

namespace Simi\Simirewardpoints\Model\System\Config\Source;

class Rounding implements \Magento\Framework\Option\ArrayInterface
{

    const REFER_URL_PARAM_IDENTIFY = '1';
    const REFER_URL_PARAM_AFFILIATE_ID = '2';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'round', 'label' => __('Normal')],
            ['value' => 'floor', 'label' => __('Rounding Down')],
            ['value' => 'ceil', 'label' => __('Rounding Up')],
        ];
    }
}
