<?php

/**
 * Promoteapp Content helper
 */

namespace Simi\Simipromoteapp\Helper;

use IntlDateFormatter;

class Datetime extends Data
{
    public function formatDateTime($time_format){
        return date('Y-m-d',strtotime($time_format));
    }

    public function getDateField($idField, $default_value = null, $block = null){
        $form = $this->formFactory->create(array(
            'id'        => 'edit_form',
            'action'    => $block->getUrl('*/*/save'),
            'method'    => 'post'
        ));

        $element = $this->dateFactory->create(
            array(
                'name' => $idField,
                'label' => __('Date'),
                'tabindex' => 1,
                'class' => 'required-entry'
            )
        );
        $element->setForm($form);
        $value = $default_value ==  null ? date('Y-m-d') : date('Y-m-d',strtotime($default_value));
        $element->setValue($value);
        $element->setName($idField);
        $element->setId($idField);
        $element->setFormat($this->simiObjectManager->get('Magento\Framework\Stdlib\DateTime\TimezoneInterface')
                            ->getDateFormat(\IntlDateFormatter::LONG));
        return $element->getElementHtml();
    }

    public function getFirstDateOfCurrentMonth(){
       return $this->formatDateTime('first day of this month');
    }

    public function getLastDateOfCurrentMonth(){
       return $this->formatDateTime('last day of this month');
    }

    function date_range($first, $last, $step = '+1 day', $output_format = 'd/m/Y' ) {

        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);

        while( $current <= $last ) {

            $dates[] = date($output_format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }
}
