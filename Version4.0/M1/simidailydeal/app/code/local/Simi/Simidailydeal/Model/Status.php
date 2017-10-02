<?php

class Simi_Simidailydeal_Model_Status extends Varien_Object
{
	const STATUS_COMING = 1;
	const STATUS_DISABLE = 2;
	const STATUS_ACTIVE = 3;

	static public function getOptionArray()
	{
		return array(
			self::STATUS_COMING  => Mage::helper('simidailydeal')->__('Schedule'),
			self::STATUS_DISABLE => Mage::helper('simidailydeal')->__('Disable'),
            self::STATUS_ACTIVE => Mage::helper('simidailydeal')->__('Active')
		);
	}

	static public function getOptionHash()
	{
		$options = array();
		foreach (self::getOptionArray() as $value => $label)
			$options[] = array(
				'value' => $value,
				'label' => $label
			);

		return $options;
	}
}