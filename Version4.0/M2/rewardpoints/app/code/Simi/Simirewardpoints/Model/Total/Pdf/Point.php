<?php

namespace Simi\Simirewardpoints\Model\Total\Pdf;

class Point extends \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal
{

    /**
     * Get Total amount from source
     *
     * @return float
     */
    public function getAmount()
    {
        return -$this->getSource()->getSimirewardpointsDiscount();
    }
}
