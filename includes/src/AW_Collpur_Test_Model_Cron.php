<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Collpur
 * @version    1.0.6
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Collpur_Test_Model_Cron extends EcomDev_PHPUnit_Test_Case {

    public function setup() {
        AW_Collpur_Test_Model_Mocks_Foreignresetter::dropForeignKeys();
        parent::setup();
    }

    public function checkAndSendEmails($data) {
        /* Call pre set-up method for every template if there is a need */
        foreach (Mage::getModel('customsmtp/mail')->getCollection() as $mail) {
            $mail->delete();
        }
        $this->{"prepare{$data['template']}"}($data);
        AW_Collpur_Model_Cron::$data['template']();
        $this->{"compare{$data['template']}"}($data);
    }

    public function provider__testCheckAndSendEmails() {

        return array(
            array(
                array(
                    'template' => '_processExpireAfterDaysDeals',
                    'dealId' => array(1, 2, 3),
                    'emailsSent' => 2,
                    'uid' => '001',
                    'presetData' => array(
                        'field' => array(
                            'notifications-0' => array(
                                'enable' => 1,
                                'email_sender' => 'general',
                                'administrator_email' => 'administator@admin.com',
                                'notify_admin_before_deal_expired' => '4',
                                'notify_admin_before_deal_expired_template' => 'collpur_notifications_notify_admin_before_deal_expired_template'
                            )
                        )
                    )
                )
            ),
            array(
                array(
                    'template' => '_processExpiredDeals',
                    'dealId' => array(3),
                    'emailsSent' => 1,
                    'uid' => '002',
                    'presetData' => array(
                        'field' => array(
                            'notifications-0' => array(
                                'enable' => 1,
                                'email_sender' => 'sales',
                                'administrator_email' => 'administator@admin.com',
                                'notify_admin_before_deal_expired' => '4',
                                'deal_failed_template_admin' => 'collpur_notifications_deal_failed_template_admin'
                            )
                        )
                    )
                )
            ),
            array(
                array(
                    'template' => '_processSuccessedDeals',
                    'dealId' => array(1),
                    'emailsSent' => 5,
                    'uid' => '003',
                    'presetData' => array(
                        'field' => array(
                            'notifications-0' => array(
                                'enable' => 1,
                                'email_sender' => 'sales',
                                'administrator_email' => 'administator@admin.com',
                                'notify_admin_before_deal_expired' => '4',
                                'deal_succeed_template_admin' => 'collpur_notifications_deal_succeed_template_admin',
                                'deal_succeed_template_customer' => 'collpur_notifications_deal_succeed_template_customer'
                            ),
                            'notifications-1' => array(
                                'enable' => 1,
                                'email_sender' => 'sales',
                                'administrator_email' => 'administator@admin.com',
                                'notify_admin_before_deal_expired' => '4',
                                'deal_succeed_template_customer' => 'collpur_notifications_deal_succeed_template_customer',
                                'deal_succeed_template_admin' => 'collpur_notifications_deal_succeed_template_admin',
                            )
                        )
                    )
                )
            )
        );
    }

    /**
     *
     * @test
     * @loadFixture order
     * @loadFixture orderItem
     * @loadFixture successedDeals
     * @loadFixture coupons
     * @loadFixture successedPurchases
     * @dataProvider provider__testCheckAndSendEmails
     *
     *
     *  DEAL 1
     *    - Deal 1 has 2 related purchases and disabled coupons
     *    Customer templates
     *      So notifications shoud be sent only to customer who actually bought
     *      the deal (i.e $purchase->getQtyPurchased() > 0) and that is true for both purchases.
     *    Admin templates
     *      Notifications should be sent is is_successed_flag of the deal is set to 0
     *
     *  DEAL 2 has coupons enabled and one related purchase
     *   Customer templates
     *      So notifications should be sent only to customer who actually bought
     *      the deal $purchase->getQtyPurchased() > 0 and if there are enoughf coupons has been generated for this customer
     *     ($purchase->getQtyPurchased() == $purchase->getQtyWithCoupons()) - FALSE for this purchase
     *   Admin email should be sent - is_successed_flag is set to 0
     *
     *  DEAL 3 has coupons enabled and one related purchase
     *      Customer templates
     *      So notifications should be sent only to customer who actually bought
     *      the deal $purchase->getQtyPurchased() > 0 and if there are enoughf coupons has been generated for this customer
     *     ($purchase->getQtyPurchased() == $purchase->getQtyWithCoupons()) - TRUE for this purchase
      Admin email SHOULD NOT be sent - is_successed_flag is set to 1
     *
     *   So there are shoud be 5 emails sent
     *   3 to customer customer_email: helpdeskult9@yandex.ru
     *   2 to admin helpdeskult9@yandex.ru
     *
     *   TO DO make sure all vars are parsed and sent correcly
     *   Currenctly only qty is checked
     *
     */
    public function testSuccessedTemplates($data) {
        if ($data['uid'] == '003') {
            $this->checkAndSendEmails($data);
        }
    }

    private function prepare_processSuccessedDeals($data) {
        $this->prepareStoreConfig($data);
    }

    private function compare_processSuccessedDeals($data) {
        $emailsCount = Mage::getModel('customsmtp/mail')->getCollection()->count();
        $this->assertEquals($emailsCount, $data['emailsSent']);
    }

    /**
     *
     * @test
     * @loadFixture order
     * @loadFixture orderItem
     * @loadFixture inventoryStockItem
     * @loadFixture catalog_product_website
     * @loadFixture expireBeforeDeals
     * @loadFixture coupons
     * @loadFixture expireBeforePurchases
     * @dataProvider provider__testCheckAndSendEmails
     *
     *  Admin emails
     *      Should be sent only if
     *
     * $currentZendDate = AW_Collpur_Helper_Data::getGmtTimestamp(true,true,$daysToAdd,false);
      $dateAfterDays = $currentZendDate->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

      $this
      ->getSelect()
      ->where('available_to <= ?', $dateAfterDays)
      ->where('available_to IS NOT NULL')
      ->where('sent_before_flag = 0')
      ->order('id ASC');
     *
     *   ->where('available_to <= ?', $dateAfterDays) - this is TRUE for all deals see prepare_processExpireAfterDaysDeals function
     *   ->where('sent_before_flag = 0') - this is TRUE only for 2 deals #1 and #2
     *
     */
    public function ExpireBeforeTemplates($data) {
        if ($data['uid'] == '001') {
            $this->checkAndSendEmails($data);
        }
    }

    private function prepare_processExpireAfterDaysDeals($data) {
        $this->prepareStoreConfig($data);
        foreach ($data['dealId'] as $deal) {
            $currentDate = new Zend_Date(gmdate('Y-m-d h:i:s'), Zend_Date::ISO_8601);
            $currentDate->setTimezone('UTC');
            $expireAfterDays = floor(Mage::getStoreConfig('collpur/notifications/notify_admin_before_deal_expired') / 2);
            $dateExpire = $currentDate->addDay($expireAfterDays);
            Mage::getModel('collpur/deal')->load($deal)->setAvailableTo($dateExpire->toString('YYYY-MM-dd HH:mm:ss'))->save();
        }
        Mage::app()->getWebsite()->setId(1);
    }

    private function compare_processExpireAfterDaysDeals($data) {

        $info = $data['presetData']['field']['notifications-0'];
        $emailsSent = Mage::getModel('customsmtp/mail')->getCollection();
        $this->assertEquals($data['emailsSent'], $emailsSent->count());
        $sentMail = $emailsSent->getFirstItem();
        $this->assertEquals($sentMail->getToEmail(), $info['administrator_email']);
        $this->assertEquals($sentMail->getToName(), 'Administrator');
        $this->assertEquals($sentMail->getStatus(), 'processed');
        $this->assertEquals($sentMail->getTemplateId(), $info['notify_admin_before_deal_expired_template']);
        $this->assertEquals($sentMail->getFromName(), 'Owner');
        $this->assertEquals($sentMail->getFromEmail(), 'owner@example.com');
    }

    /**
     *
     * @test
     * @loadFixture order
     * @loadFixture orderItem
     * @loadFixture expiredDeals
     * @loadFixture coupons
     * @loadFixture expiredPurchases
     * @dataProvider provider__testCheckAndSendEmails
     *
     *  Only deal #3 is expired -> close state open, available_to < UTC_TIMESTAMP and expire_flag is set to 0
     *  Only one email should be sent, becuse for store 1 (customer template) email notifications are not set
     * 
     */
    public function testExpiredDealsTemplates($data) {
        if ($data['uid'] == '002') {
            $this->checkAndSendEmails($data);
        }
    }

    private function prepare_processExpiredDeals($data) {
        $this->prepareStoreConfig($data);
    }

    private function compare_processExpiredDeals($data) {

        $info = $data['presetData']['field']['notifications-0'];
        $emailsSent = Mage::getModel('customsmtp/mail')->getCollection();
        $this->assertEquals($data['emailsSent'], $emailsSent->count());
        $sentMail = $emailsSent->getFirstItem();
        $this->assertEquals($sentMail->getToEmail(), $info['administrator_email']);
        $this->assertEquals($sentMail->getToName(), 'Administrator');
        $this->assertEquals($sentMail->getStatus(), 'processed');
        $this->assertEquals($sentMail->getTemplateId(), $info['deal_failed_template_admin']);
        $this->assertEquals($sentMail->getFromName(), 'Sales');
        $this->assertEquals($sentMail->getFromEmail(), 'sales@example.com');
    }

    private function prepareStoreConfig($data) {
        foreach ($data['presetData']['field'] as $key => $values) {
            foreach ($values as $v => $value) {
                preg_match("#(.+)-(.+)#is", $key, $matches);
                $storeId = $matches[2];
                $field = $matches[1];
                Mage::app()->getStore($storeId)->setConfig('collpur/' . $field . '/' . $v, $value);
            }
        }
    }

}