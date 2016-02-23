<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simibarcode
 * @copyright   Copyright (c) 2012 
 * @license     
 */

 /**
 * Simibarcode Adminhtml Controller
 * 
 * @category    
 * @package     Simibarcode
 * @author      Developer
 */
class Simi_Simibarcode_Adminhtml_Simibarcode_PrintbarcodeController extends Mage_Adminhtml_Controller_Action {

    /**
     * select template to print barcode
     *
     * @return Simi_Simibarcode_Adminhtml_SimibarcodeController
     */
    public function selecttemplateAction() 
    {

        // $function = Mage::getModel('simibarcode/printbarcode_function');
        echo $this->getLayout()->createBlock('simibarcode/adminhtml_printbarcode')->setTemplate('simibarcode/printbarcode/selecttemplate.phtml')->toHtml();
    }

    public function getImageAction() 
    {
        $params = $this->getRequest()->getParams();
        $type = $params['type'];
        $code = $params['text'];
        if (isset($params['customize'])) {
            $heigth = $params['heigth_barcode'];
            $barcodeOptions = array('text' => $code,
                'barHeight' => $heigth,
                'fontSize' => $params['font_size'],
                'withQuietZones' => true
            );
        } else {
            $barcodeOptions = array('text' => $code,
                'fontSize' => $params['font_size'],
                'withQuietZones' => true
            );
        }
        // No required options
        $rendererOptions = array();

        // Draw the barcode in a new image,
        // send the headers and the image
        $imageResource = Zend_Barcode::factory(
                        $type, 'image', $barcodeOptions, $rendererOptions
        );
        imagepng($imageResource->draw(), Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'simibarcode' . DS . 'images' . DS . 'barcode.png');
        $imageResource->render();
    }

    public function printBarcodeAction() 
    {
        $params = $this->getRequest()->getParams();
// Zend_debug::dump($params);die();
        $imageUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'simibarcode/images/barcode.png';
        $contents = $this->getLayout()->createBlock('adminhtml/template')
                ->setTemplate('simibarcode/printbarcode/printbarcode.phtml')
                ->assign('barcodeId', $params['barcodeId'])
                ->assign('qty', $params['number_of_barcode'])
                ->assign('barcodeTemplate', $params['barcode_template'])
                ->assign('fontSize', $params['font_size'])
                ->assign('imageWidth', $params['image_width'])
                ->assign('imageUrl', $this->convertImage($imageUrl));
        if (isset($params['border'])) {
            $contents->assign('border', $params['border']);
        } else {
            $contents->assign('border', 0);
        }
        include("lib/Simi/Mpdf/mpdf.php");
        $top = '10';
        $bottom = '10';
        $left = '10';
        $right = '10';
       
        $mpdf = new mPDF('', $params['printing_format'], 8, '', $left, $right, $top, $bottom);

        $mpdf->WriteHTML($contents->toHtml());

        $mpdf->Output();
    }

    public function convertImage($url)
    {
        $type = pathinfo($url, PATHINFO_EXTENSION);
        $data = file_get_contents($url);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }

}
