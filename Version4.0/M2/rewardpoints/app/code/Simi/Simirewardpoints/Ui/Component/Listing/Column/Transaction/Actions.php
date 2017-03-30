<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simi\Simirewardpoints\Ui\Component\Listing\Column\Transaction;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 */
class Actions implements OptionSourceInterface
{

    public function toOptionHash()
    {
        return [
            'earning_invoice' => __('Earn points for purchasing order'),
            'earning_creditmemo' => __('Taken back points for refunding order'),
            'earning_cancel' => __('Taken back points for cancelling order'),
            'spending_order' => __('Spend points to purchase order'),
            'spending_creditmemo' => __('Retrieve points spent on refunded order'),
            'spending_cancel' => __('Retrieve points spent on cancelled order'),
            'admin' => __('Changed by Admin')
        ];
    }

    public function toOptionArray()
    {
        $options = [];
        foreach (self::toOptionHash() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $options;
    }
}
