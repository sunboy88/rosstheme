<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Phoenix
 * @package     Phoenix_Moneybookers
 * @copyright   Copyright (c) 2014 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$installer->run("
    ALTER TABLE  `".$this->getTable('sales/quote_address')."` ADD  `referralreward_amount` DECIMAL(10, 2) NOT NULL;
    ALTER TABLE  `".$this->getTable('sales/quote_address')."` ADD  `base_referralreward_amount` DECIMAL(10, 2) NOT NULL;

    ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `referralreward_amount` DECIMAL(10, 2) NOT NULL;
    ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_referralreward_amount` DECIMAL(10, 2) NOT NULL;

    ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `referralreward_amount_invoiced` DECIMAL( 10, 2 ) NOT NULL;
    ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_referralreward_amount_invoiced` DECIMAL( 10, 2 ) NOT NULL;

    ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `referralreward_amount_refunded` DECIMAL( 10, 2 ) NOT NULL;
    ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_referralreward_amount_refunded` DECIMAL( 10, 2 ) NOT NULL;
    
    ALTER TABLE  `".$this->getTable('sales/invoice')."` ADD  `referralreward_amount` DECIMAL( 10, 2 ) NOT NULL;
    ALTER TABLE  `".$this->getTable('sales/invoice')."` ADD  `base_referralreward_amount` DECIMAL( 10, 2 ) NOT NULL;

    ALTER TABLE  `".$this->getTable('sales/creditmemo')."` ADD  `referralreward_amount` DECIMAL( 10, 2 ) NOT NULL;
    ALTER TABLE  `".$this->getTable('sales/creditmemo')."` ADD  `base_referralreward_amount` DECIMAL( 10, 2 ) NOT NULL;

");

$installer->endSetup();
