<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Productquestions
 * @version    1.5.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('productquestions')}  DEFAULT CHARACTER SET utf8;

ALTER TABLE {$this->getTable('productquestions')} CHANGE `question_author_name` `question_author_name` VARCHAR( 255 ) CHARACTER SET utf8 NOT NULL;
ALTER TABLE {$this->getTable('productquestions')} CHANGE `question_text` `question_text` TEXT CHARACTER SET utf8 NOT NULL;
ALTER TABLE {$this->getTable('productquestions')} CHANGE `question_reply_text` `question_reply_text` TEXT CHARACTER SET utf8 NOT NULL;
ALTER TABLE {$this->getTable('productquestions')} CHANGE `question_product_name` `question_product_name` VARCHAR( 255 ) CHARACTER SET utf8 NOT NULL;

");

$installer->endSetup();
?>
