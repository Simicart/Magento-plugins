<?php

namespace Simi\Simirewardpoints\Model\Total\Quote;

class Label extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{

    public function __construct()
    {
        $this->setCode('simirewardpoints_label');
    }

    /**
     * add point label
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return \Simi\Simirewardpoints\Model\Total\Quote\Label
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        return [
            'code' => $this->getCode(),
            'title' => '1',
            'value' => 1,
        ];
    }
}
