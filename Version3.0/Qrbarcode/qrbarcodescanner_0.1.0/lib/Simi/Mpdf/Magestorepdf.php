<?php

/* Change by Zeus 04/12 */
if (!defined('DS')) {
    define( 'DS', DIRECTORY_SEPARATOR );
}
//define('DS', DIRECTORY_SEPARATOR);
/* end change */
include Mage::getBaseDir() . DS . 'lib' . DS . 'Mpdf' . DS . 'mpdf.php';

class Mpdf_Magestorepdf extends mPDF
{
    //put your code here
}

?>
