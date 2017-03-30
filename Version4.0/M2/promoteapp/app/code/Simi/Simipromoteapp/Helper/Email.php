<?php

namespace Simi\Simipromoteapp\Helper;

use Simi\Simipromoteapp\Model\Status;
use Magento\Framework\App\Area;

class Email extends Data
{
    const XML_SENDER_NAME = 'email/sender_name_identity';
    const XML_SENDER_EMAIL = 'email/sender_email_identity';
    const XML_EMAIL_REGISTER_TEMPLATE = 'email/email_for_register_template';
    const XML_EMAIL_SUBSCRIBER_TEMPLATE = 'email/email_for_subscriber_template';
    const XML_EMAIL_PURCHASING_TEMPLATE = 'email/email_for_purchasing_template';
    const XML_EMAIL_ENABLE = 'email/enable';
    const XML_IOS_LINK = 'app/ios_link';
    const XML_ANDROID_LINK = 'app/android_link';
    const XML_IMAGE_FIRST_BLOCK = 'promotepage/section_image_first_block';
    const XML_IMAGE_SECOND_BLOCK = 'promotepage/section_image_second_block';
    const XML_IMAGE_SMALL_THIRD_BLOCK = 'promotepage/section_image_small_third_block';
    const XML_IMAGE_LARGE_THIRD_BLOCK = 'promotepage/section_image_large_third_block';
    const XML_IMAGE_FOURTH_BLOCK = 'promotepage/section_image_fourth_block';
    const XML_CMS_PROMOTE_ID = 'promotepage/cms_promote_id';

    public function isEnable()
    {
        return $this->getHelperData()->getStoreConfig('simipromoteapp/'.self::XML_EMAIL_ENABLE);
    }

    public function getHelperData()
    {
        return $this->simiObjectManager->get('Simi\Simipromoteapp\Helper\Data');
    }

    public function getTemplateEmailId($type)
    {
        if ($type == Status::TYPE_REGISTER) {
            return $this->getHelperData()->getStoreConfig('simipromoteapp/'.self::XML_EMAIL_REGISTER_TEMPLATE);
        } elseif ($type == Status::TYPE_PURCHASING) {
            return $this->getHelperData()->getStoreConfig('simipromoteapp/'.self::XML_EMAIL_PURCHASING_TEMPLATE);
        } elseif ($type == Status::TYPE_SUBSCRIBER) {
            return $this->getHelperData()->getStoreConfig('simipromoteapp/'.self::XML_EMAIL_SUBSCRIBER_TEMPLATE);
        } else {
            return null;
        }
    }

    public function getSenderName()
    {
        return $this->getHelperData()->getStoreConfig('simipromoteapp/'.self::XML_SENDER_NAME);
    }

    public function getSenderEmail()
    {
        return $this->getHelperData()->getStoreConfig('simipromoteapp/'.self::XML_SENDER_EMAIL);
    }

    public function getiOsLink()
    {
        return $this->getHelperData()->getStoreConfig('simipromoteapp/'.self::XML_IOS_LINK);
    }

    public function getAndroidLink()
    {
        return $this->getHelperData()->getStoreConfig('simipromoteapp/'.self::XML_ANDROID_LINK);
    }

    public function getLogLink($email, $template_id)
    {
        return $this->simiObjectManager->get('\Magento\Framework\Url')
                ->getUrl('simipromoteapp/report/report', ['email'=>$email,'template_id'=>$template_id]);
    }

    /**
     * send email
     * senderInfo = array('name'=>'','email'=>'');
     * variables = array(''=>'');
     **/
    public function sendEmail($data, $type)
    {
        // get template id
        $templateId = $this->getTemplateEmailId($type);
        $iOs_link = $this->getiOsLink();
        $android_link = $this->getAndroidLink();
        $email_sender = $this->getSenderEmail();

        if ($templateId == null || ($iOs_link == null && $android_link == null) || $email_sender == null || filter_var($email_sender, FILTER_VALIDATE_EMAIL) === false) {
            // can not send email
        } else {
            // prepare variables for email
            $variables = [
                'customer_name' => $data['name'],
                'customer_email' => $data['email'],
                'ios_link' => $iOs_link,
                'android_link' => $android_link,
                'log_link' => $this->getLogLink($data['email'], $templateId)
            ];
            // recipient
            $recipient_name = $data['name'];
            $recipient_email = $data['email'];

            // sender information
            $senderInfo = [
                'name' => $this->getSenderName(),
                'email' => $email_sender,
            ];

            $storeId = $this->simiObjectManager->create('\Magento\Store\Model\StoreManagerInterface')
                        ->getStore()->getId();
            try{
                $transport = $this->transportBuilder
                        ->setTemplateIdentifier($templateId)
                        ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId])
                        ->setTemplateVars($variables)
                        ->setFrom($senderInfo)
                        ->addTo($recipient_email, $recipient_name)
                        ->getTransport();

                $transport->sendMessage();
            } catch (\Exception $ex){
                return;
            }
            // insert customer email
            $data['template_id'] = $templateId;
            $this->simiObjectManager->create('Simi\Simipromoteapp\Helper\Customer')->saveCustomerEmail($data);
        }
    }
}
