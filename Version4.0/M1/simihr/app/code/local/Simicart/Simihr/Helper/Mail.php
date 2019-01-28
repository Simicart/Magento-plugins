<?php
class Simicart_Simihr_Helper_Mail extends Mage_Core_Helper_Abstract
{
    public function sendMail(){
        // Mage::log("Run cronnnnnnnnnnnnnnnnnnnnnnnnn!");
        $jobs = Mage::getResourceModel('simihr/jobOffers_collection')->addFieldToFilter('status', 1)->getData();
        $timeNow = date("Y/m/d");
        $timeNow = explode("/",$timeNow);
        $yearNow = $timeNow[0];
        $monthNow = $timeNow[1];
        $dayNow = $timeNow[2];
        foreach ($jobs as $job) {
            $startTime = explode("/",$job['start_time']);
            $deadline = explode("/",$job['deadline']);
            if(isset($startTime[0]) && isset($startTime[1]) && isset($startTime[2])) {
                $dayStartCompare = $startTime[0];
                $monthStartCompare = $startTime[1];
                $yearStartCompare = $startTime[2];

                if($yearStartCompare == $yearNow && $monthStartCompare == $monthNow) {
                    $temp = (int)$dayStartCompare - (int)$dayNow;
                    if( $temp == 3){


                        $msg = "Time to apply " . $job['name'] . " will start in ".$temp." days at " . $job['start_time']. ".";
                        $data = [];
                        $data['msg'] = $msg;
                        self::customMail($data);
                    }
                }
            }

            if(isset($deadline[0]) && isset($deadline[1]) && isset($deadline[2])) {

                $dayDeadline = $deadline[0];
                $monthDeadline = $deadline[1];
                $yearDeadline = $deadline[2];

                if($yearDeadline == $yearNow && $monthDeadline == $monthNow) {
                    $temp = (int)$dayDeadline - (int)$dayNow;
                    if( $temp == 3){

                        $msg = "Time to apply " . $job['name'] . " will end in ".$temp." days at " . $job['deadline'] . ".";
                        $data = [];
                        $data['msg'] = $msg;
                        self::customMail($data);
                    }
                }
            }
        }
    }
    public function customMail($data, $title, $file1 = null, $file2 = null) {
        // Mage::log("Run cron to send mail!");
        $templateId = 177;
        // get store and config
        $store = Mage::app()->getStore();
        $config = array(
            'area' => 'frontend',
            'store' => $store->getId()
        );

        $sender = array(
            'name' => $title,
            'email' => 'havenoescape@gmail.com',
        );

        $recipient_email = 'havenoescape@gmail.com';
        $recipient_name = 'hieu';

        // add variable
        $vars = array('store' => $store);
        if (sizeof($data) > 0) {
            foreach ($data as $key => $value) {
                $vars[$key] = $value;
            }
        }

        // send transaction email
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $storeId = Mage::app()->getStore()->getId();

        $add_cc=array("hieu@simicart.com");
        $mail = Mage::getModel('core/email_template');
        $mail->getMail()->addCc($add_cc);
        // if (file_exists($path1)) {
        //     $mail->getMail()
        //         ->createAttachment(
        //             file_get_contents($path1),
        //             Zend_Mime::TYPE_OCTETSTREAM,
        //             Zend_Mime::DISPOSITION_ATTACHMENT,
        //             Zend_Mime::ENCODING_BASE64,
        //             basename($filename1)
        //         );
        // }
        // if (file_exists($path2)) {
        //     $mail->getMail()
        //         ->createAttachment(
        //             file_get_contents($path1),
        //             Zend_Mime::TYPE_OCTETSTREAM,
        //             Zend_Mime::DISPOSITION_ATTACHMENT,
        //             Zend_Mime::ENCODING_BASE64,
        //             basename($filename2)
        //         );
        // }
        $mail->setDesignConfig($config)
            ->sendTransactional($templateId, $sender, $recipient_email, $recipient_name, $vars, $storeId);
        $translate->setTranslateInline(true);
        Mage::log("Simihr sent mail to hr@simicart.com and max@simicart.com");
    }
}