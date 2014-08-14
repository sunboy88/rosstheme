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


$installer = $this;

$installer->startSetup();

try {

    $installer->run("

CREATE TABLE IF NOT EXISTS {$this->getTable('collpur/coupon')} (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `deal_id` int(10) unsigned NOT NULL,
  `purchase_id` int(10) unsigned NOT NULL,
  `coupon_code` varchar(100) NOT NULL,
  `status` varchar(20) NOT NULL,
  `coupon_delivery_datetime` datetime DEFAULT NULL,
  `coupon_date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_COUPON_DEAL_ID` FOREIGN KEY (`deal_id`) REFERENCES {$this->getTable('collpur/deal')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS {$this->getTable('collpur/deal')} (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `store_ids` text NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  `is_success` tinyint(4) NOT NULL,
  `close_state` tinyint(4) NOT NULL,
  `qty_to_reach_deal` int(10) unsigned NOT NULL,
  `purchases_left` int(10) unsigned NOT NULL,
  `maximum_allowed_purchases` int(10) unsigned NOT NULL,
  `available_from` datetime DEFAULT NULL,
  `available_to` datetime DEFAULT NULL,
  `price` decimal(12,4) NOT NULL,
  `auto_close` tinyint(4) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `full_description` text default '',
  `deal_image` text NOT NULL,
  `is_featured` tinyint(4) unsigned default '0',
  `enable_coupons` tinyint(4) NOT NULL,
  `coupon_prefix` varchar(255) NOT NULL default '',
  `coupon_expire_after_days` int(10) unsigned default '0',
  `expired_flag` tinyint(4) unsigned default '0',
  `sent_before_flag` tinyint(4) unsigned default '0',
  `is_successed_flag` tinyint(4) unsigned default '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS {$this->getTable('collpur/rewrite')} (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `deal_id` int(10) unsigned NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL,
  `identifier` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  CONSTRAINT `FK_DEAL_ID` FOREIGN KEY (`deal_id`) REFERENCES {$this->getTable('collpur/deal')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_DEAL_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS {$this->getTable('collpur/dealpurchases')} (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `deal_id` int(10) unsigned NOT NULL,
  `order_id` int(10) unsigned NOT NULL,
  `order_item_id` int(10) unsigned NOT NULL,
  `qty_purchased` int(10) unsigned NOT NULL,
  `qty_with_coupons` int(10) unsigned NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_id` int(10) unsigned NOT NULL,
  `purchase_date_time` datetime DEFAULT NULL,
  `shipping_amount` decimal(12,4) NOT NULL default '0.0000',
  `qty_ordered` decimal(12,4) NOT NULL default '0.0000',
  `refund_state` int(10) unsigned default '0',
  `is_successed_flag` tinyint(4) unsigned default '0',
  CONSTRAINT `AW_COLLPUR_DEAL_ID` FOREIGN KEY (`deal_id`) REFERENCES {$this->getTable('collpur/deal')} (`id`) ON DELETE CASCADE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");
} catch (Exception $e) {
    Mage::log($e->getTrace());
}

$installer->endSetup();
