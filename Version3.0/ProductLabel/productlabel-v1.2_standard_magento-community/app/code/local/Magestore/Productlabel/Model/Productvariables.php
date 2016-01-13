<?php
class Magestore_Productlabel_Model_Productvariables
{
	protected $_productLabelVariables = array();
    public function __construct()
    {
        $this->_productLabelVariables = array(
            array(
                'value' => 'special_price',
                'label' => Mage::helper('productlabel')->__('Special Price')
            ),
			array(
                'value' => 'regular_price',
                'label' => Mage::helper('productlabel')->__('Regular Price')
            ),
			array(
                'value' => 'discount_amount',
                'label' => Mage::helper('productlabel')->__('Discount Amount')
            ),
			array(
                'value' => 'save_amount',
                'label' => Mage::helper('productlabel')->__('Amount Saved')
            ),	
			array(
                'value' => 'line_break',
                'label' => Mage::helper('productlabel')->__('Line Break')
            ),	
			array(
                'value' => 'sku',
                'label' => Mage::helper('productlabel')->__('Product SKU')
            ),	
			array(
                'value' => 'in_stock_amount',
                'label' => Mage::helper('productlabel')->__('In Stock Amount')
            )				
			);
	}
    public function getProductVariablesOptionArray($withGroup = false)
    {
        $optionArray = array();
        foreach ($this->_productLabelVariables as $variable) {
            $optionArray[] = array(
                'value' => '{{' . $variable['value'] . '}}',
                'label' => Mage::helper('productlabel')->__('%s', $variable['label'])
            );
        }
        if ($withGroup && $optionArray) {
            $optionArray = array(
                'label' => Mage::helper('productlabel')->__('Product Label Variables'),
                'value' => $optionArray
            );
        }
        return $optionArray;
    }
}