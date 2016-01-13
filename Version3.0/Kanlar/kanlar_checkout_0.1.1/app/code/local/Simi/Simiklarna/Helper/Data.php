<?php

class Simi_Simiklarna_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function converDataAddress($data, &$address){
                $address['firstname'] = $data['given_name'];
                $address['lastname'] = $data['family_name'];
                $address['street'] = array($data['street_address'], '');
                $address['postcode'] = $data['postal_code'];
                $address['city'] = $data['city'];
                $address['country_id'] = strtoupper($data['country']);
                $address['email'] = $data['email'];
                $address['telephone'] = $data['phone'];
	}
}

