<?php


namespace Simi\Simipromoteapp\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    
    public $simiObjectManager;
    public $contentHelper;
    
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager
    ) {
        $this->simiObjectManager  = $simiObjectManager;
        $this->contentHelper      = $this->simiObjectManager->get('Simi\Simipromoteapp\Helper\Content');
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $context;
        $installer = $setup;
        try {
            //Add email template
            $purchased_subject = $this->contentHelper->getPurchasingSubject();
            $purchased_content = $this->contentHelper->getPurchasingContent();

            $register_subject = $this->contentHelper->getRegisterSubject();
            $register_content = $this->contentHelper->getRegisterContent();

            $subscriber_subject = $this->contentHelper->getSubscriberSubject();
            $subscriber_content = $this->contentHelper->getSubscriberContent();

            $now = $this->simiObjectManager
                    ->get('\Magento\Framework\Stdlib\DateTime\DateTimeFactory')->create()->gmtDate();
            $columns = [
                'template_code',
                'template_text',
                'template_type',
                'template_subject',
                'template_sender_name',
                'template_sender_email',
                'added_at',
                'modified_at'
            ];
            $data1 = [
                'Email For Register',
                $register_content,
                2,
                $register_subject,
                null,
                null,
                $now,
                $now
            ];

            $data2 = [
                'Email For Subscriber',
                $subscriber_content,
                2,
                $subscriber_subject,
                null,
                null,
                $now,
                $now
            ];

            $data3 = [
                'Email For Purchasing Order',
                $purchased_content,
                2,
                $purchased_subject,
                null,
                null,
                $now,
                $now
            ];
            $installer->getConnection()
                    ->insertArray($setup->getTable('email_template'), $columns, [$data1, $data2, $data3]);

            $installer->endSetup();
        } catch (\Exception $e) {
            return;
        }
    }
}
