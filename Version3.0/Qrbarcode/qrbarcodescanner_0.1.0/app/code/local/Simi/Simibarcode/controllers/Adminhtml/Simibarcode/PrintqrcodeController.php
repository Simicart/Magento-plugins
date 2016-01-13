<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Simibarcode
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Simibarcode Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_Simibarcode
 * @author      Magestore Developer
 */
class Simi_Simibarcode_Adminhtml_Simibarcode_PrintqrcodeController extends Mage_Adminhtml_Controller_Action {

    /**
     * select template to print qrcode
     *
     * @return Simi_Simibarcode_Adminhtml_SimiqrcodeController
     */
    public function selecttemplateAction() 
    {
        echo $this->getLayout()->createBlock('simibarcode/adminhtml_printqrcode')->setTemplate('simibarcode/printqrcode/selecttemplate.phtml')->toHtml();
    }

    public function printAction() 
    {
        $data = $this->getRequest()->getPost();

        $displayBorder = 0;
        $showCode = 0;
        $numberCopies = 0;
        $qrcode_template = '';
        $qrcode_ids = '';
        if (isset($data['display_border'])) {
            $displayBorder = 1;
        }

        if (isset($data['number_copies'])) {
            $numberCopies = $data['number_copies'];
        }
        if (isset($data['qrcode_template'])) {
            $qrcode_template = $data['qrcode_template'];
        }
        if (isset($data['qrcode_ids'])) {
            $qrcode_ids = $data['qrcode_ids'];
        }

        echo $this->getLayout()->createBlock('simibarcode/adminhtml_printqrcode')
                ->setData('number_copies', $numberCopies)
                ->setData('qrcode_template', $qrcode_template)
                ->setData('qrcode_ids', explode(',', $qrcode_ids))
                ->setData('display_border', $displayBorder)
                ->setTemplate('simibarcode/printqrcode/form.phtml')->toHtml();
    }

    public function getImageAction() 
    {
        $params = $this->getRequest()->getParams();
        $size = $params['size'];
        $code = $params['text'];
        $type = $params['type'];
        include("lib/Simi/QrCode/src/Endroid/QrCode/QrCode.php");
        $qr = new Endroid\QrCode\QrCode();
        $qr->setText($code);
        $qr->setSize($size);
        $qr->setPadding(10);
        $qr->setErrorCorrection($type);
        imagepng($qr->getImage(), Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'simibarcode' . DS . 'images' . DS . 'qrcode.png');
        $qr->render(); 
    }

    public function printQrcodeAction() 
    {
        $params = $this->getRequest()->getParams();
        $imageUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'simibarcode/images/qrcode.png';
// Zend_debug::dump($params);die();
        $contents = $this->getLayout()->createBlock('adminhtml/template')
                ->setTemplate('simibarcode/printqrcode/printqrcode.phtml')
                ->assign('barcodeId', $params['barcodeId'])
                ->assign('qty', $params['number_of_barcode'])
                ->assign('qrcodeTemplate', $params['qrcode_template'])
                ->assign('fontSize', $params['font_size'])
                ->assign('imageWidth', $params['image_width'])
                ->assign('imageUrl', $imageUrl);
        // if (isset($params['border'])) {
        //     $contents->assign('border', $params['border']);
        // } else {
        //     $contents->assign('border', 0);
        // }
        include("lib/Simi/Mpdf/mpdf.php");
        $top = '10';
        $bottom = '10';
        $left = '10';
        $right = '10';
       
        $mpdf = new mPDF('', $params['printing_format'], 8, '', $left, $right, $top, $bottom);

        $mpdf->WriteHTML($contents->toHtml());

        $mpdf->Output();
    }

}
