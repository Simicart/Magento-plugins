<?php
/**
 * Created by PhpStorm.
 * User: haven
 * Date: 1/7/2019
 * Time: 9:22 AM
 */
class Simicart_Simihr_Model_Observer {

    public function sendMail() {
        // Mage::log("Run cronnnnnnnnnnnnnnnnnnnnnnnnn!");
        $jobs = Mage::getResourceModel('simihr/jobOffers_collection')->addFieldToFilter('status', 1)->getData();
        $timeNow = date("Y/m/d");
        $timeNow = explode("/",$timeNow);
        $yearNow = (int)$timeNow[0];
        $monthNow = (int)$timeNow[1];
        $dayNow = (int)$timeNow[2];
        foreach ($jobs as $job) {
            $startTime = explode("/",$job['start_time']);
            $deadline = explode("/",$job['deadline']);

            if(isset($startTime[0]) && isset($startTime[1]) && isset($startTime[2])) {
                $dayStartCompare = (int)$startTime[0];
                $monthStartCompare = (int)$startTime[1];
                $yearStartCompare = (int)$startTime[2];

                $totaldays = self::getDaysOfMonth($monthStartCompare);

                if($yearStartCompare == $yearNow) {
                    if($monthNow == $monthStartCompare) {
                        $temp = $dayStartCompare - $dayNow;
                        if( $temp == 3){
                            $msg = "Time to apply " . $job['name'] . " will start in 3 days at " . $job['start_time']. ".";
                            $data = [];
                            $data['msg'] = $msg;
                            self::customMail($data);
                        }
                    } elseif ($monthStartCompare == ($monthNow + 1) && ($dayStartCompare <=3 && ($dayStartCompare + $totaldays - $dayNow) == 3)) {
                        $msg = "Time to apply " . $job['name'] . " will start in 3 days at " . $job['start_time']. ".";
                        $data = [];
                        $data['msg'] = $msg;
                        self::customMail($data);
                    }
                }
            }

            if(isset($deadline[0]) && isset($deadline[1]) && isset($deadline[2])) {

                $dayDeadline = (int)$deadline[0];
                $monthDeadline = (int)$deadline[1];
                $yearDeadline = (int)$deadline[2];

                $totaldays = self::getDaysOfMonth($monthDeadline);

                if($yearDeadline == $yearNow ) {
                    if ($monthNow == $monthDeadline) {
                        $temp = $dayDeadline - $dayNow;
                        if( $temp == 3){                            
                            $msg = "Time to apply " . $job['name'] . " will end in 3 days at " . $job['deadline'] . ".";
                            $data = [];
                            $data['msg'] = $msg;
                            self::customMail($data);
                        }
                    } elseif ($monthDeadline == ($monthNow + 1) && ($dayDeadline <=3 && ($dayDeadline + $totaldays - $dayNow) == 3)) {
                        $msg = "Time to apply " . $job['name'] . " will end in 3 days at " . $job['deadline'] . ".";
                        $data = [];
                        $data['msg'] = $msg;
                        self::customMail($data);
                    }
                }
            }
        }
    }
    public function customMail($data) {
        // Mage::log("Run cron to send mail!");
        $templateId = 183;
         // get store and config
        $store = Mage::app()->getStore();
        $config = array(
            'area' => 'frontend',
            'store' => $store->getId()
        );

        $sender = array(
            'name' => 'Simihr',
            'email' => 'havenoescape@gmail.com',
        );

        $recipient_email = 'max@simicart.com';
        $recipient_name = 'hr';

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

        $add_cc=array("hr@simicart.com");
        $mail = Mage::getModel('core/email_template');
        $mail->getMail()->addCc($add_cc);
         $mail->setDesignConfig($config)
            ->sendTransactional($templateId, $sender, $recipient_email, $recipient_name, $vars, $storeId);
        $translate->setTranslateInline(true);
        Mage::log("Simihr sent mail to hr@simicart.com and max@simicart.com");
    }

    public function getDaysOfMonth($month) {
        $fullMonths = [1,3,5,7,8,10,12];
        $lessmonths = [4,6,9,11];
        if (in_array($month, $fullMonths)) {
            return 31;
        } elseif (in_array($month, $lessmonths)) {
            return 30;
        } else {
            return 28;
        }
    }
}
?>