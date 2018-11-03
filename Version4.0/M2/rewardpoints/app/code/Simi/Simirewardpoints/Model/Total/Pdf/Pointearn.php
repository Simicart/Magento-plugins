<?php

namespace Simi\Simirewardpoints\Model\Total\Pdf;

class Pointearn extends \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal
{

    /**
     * Get array of arrays with totals information for display in PDF
     * array(
     *  $index => array(
     *      'amount'   => $amount,
     *      'label'    => $label,
     *      'font_size'=> $font_size
     *  )
     * )
     *
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $amount = $this->getAmount();
        $label = __($this->getTitle()) . ':';
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $total = [
            'amount' => $amount,
            'label' => $label,
            'font_size' => $fontSize
        ];
        return [$total];
    }

    /**
     * Get Total amount from source
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->getSource()->getSimirewardpointsEarn();
    }
}
