<?php
class Magestore_Onestepcheckout_Model_Sales_Order_Pdf_Invoice extends Mage_Sales_Model_Order_Pdf_Invoice
{
    public function getPdf ($invoices = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');
        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->emulate($invoice->getStoreId());
                Mage::app()->setCurrentStore($invoice->getStoreId());
            }
            $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
            $pdf->pages[] = $page;
            $order = $invoice->getOrder();
            /* Add image */
            $this->insertLogo($page, $invoice->getStore());
            /* Add address */
            $this->insertAddress($page, $invoice->getStore());
            /* Add head */
            $this->insertOrder($page, $order, 
            Mage::getStoreConfigFlag(
            self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, $order->getStoreId()));
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $this->_setFontRegular($page);
            $page->drawText(
            Mage::helper('sales')->__('Invoice # ') . $invoice->getIncrementId(), 
            35, 780, 'UTF-8');
            /* Add table */
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 570, $this->y - 15);
            $this->y -= 10;
            /* Add table head */
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
            $page->drawText(Mage::helper('sales')->__('Products'), 35, $this->y, 
            'UTF-8');
            //            $page->drawText(Mage::helper('sales')->__('SKU'), 255, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Price'), 380, 
            $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Qty'), 430, $this->y, 
            'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Tax'), 480, $this->y, 
            'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Subtotal'), 530, 
            $this->y, 'UTF-8');
            $this->y -= 15;
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            /* Add body */
            foreach ($invoice->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                if ($this->y < 15) {
                    $page = $this->newPage(array('table_header' => true));
                }
                /* Draw item */
                $page = $this->_drawItem($item, $page, $order);
                $page->setLineWidth(0.5);
                $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
                $page->drawLine(25, $this->y, 570, $this->y);
                $this->y -= 10;
            }
            // Output Giftwrap Information
            $gifBlock = Mage::getBlockSingleton(
            'giftwrap/adminhtml_sales_order_view_tab_giftwrap');
            $giftwrapItems = $gifBlock->getOrderItemGiftwrap($order->getId());
            if (count($giftwrapItems)) {
                /* Add table */
                $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
                $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
                $page->setLineWidth(0.5);
                $page->drawRectangle(25, $this->y, 570, $this->y - 15);
                $this->y -= 10;
                /* Add table head */
                $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
                $page->drawText(Mage::helper('sales')->__('Item #'), 35, 
                $this->y, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Product'), 70, 
                $this->y, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Giftwrap Style'), 
                200, $this->y, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Giftcard'), 300, 
                $this->y, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Message'), 450, 
                $this->y, 'UTF-8');
                $this->y -= 15;
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                /* Add body */
                $i = 0;
                foreach ($giftwrapItems as $giftwrapItem) {
				
                    $i ++;
                    // var_dump($this->y);
                    // echo "<br />";
                    if ($this->y < 60) {
                        // var_dump($i);die();
                        $page = $this->newPage(
                        array('table_header' => true));
                    }
                    $page->drawText($i, 35, $this->y, 'UTF-8');
                    //$page->drawText($gifBlock->getProduct($giftwrapItem['itemId'])->getName(), 70, $this->y, 'UTF-8');
                    $item = Mage::getModel(
                    'sales/quote_item')->load($giftwrapItem['itemId']);
                    $this->drawGiftProduct($item->getProductId(), $pdf, $page);
                    $page->drawText(
                    $gifBlock->getGiftwrapStyleName($giftwrapItem['styleId']), 
                    200, $this->y + 10, 'UTF-8');
                    $image = $gifBlock->getGiftwrapStyleImage(
                    $giftwrapItem['styleId']);
					
                    if ($image) {
                        $fileExtension = end(explode(".", $image));
                        $fileExtension=strtolower($fileExtension);
                        switch ($fileExtension) {
                            case 'tif':
                                $check = 1;
                                break;
                            case 'tiff':
                                $check = 1;
                                break;
                            case 'png':
                                $check = 1;
                                break;
                            case 'jpg':
                                $check = 1;
                                break;
                            case 'jpe':
                                $check = 1;
                                break;
                            case 'jpeg':
                                $check = 1;
                                break;
                            default:
                                $check = 0;
                                break;
                        }
                        if ($check == 1) {
                            /*$image = Mage::getStoreConfig(
                            'system/filesystem/media', $store) . '/giftwrap/' .
                             $image;*/
							$image = Mage::getBaseDir('media').DS.'giftwrap'.DS.$image;
							if ($image) {
                                $this->insertImageGif($page, $image, 
                                $invoice->getStore(), $this->y);
                            } else {
                                $page->drawText(
                                Mage::helper('sales')->__('No Image'), 300, 
                                $this->y, 'UTF-8');
                            }
                        } else {
                            $page->drawText(
                            Mage::helper('sales')->__('Unsupported type.'), 300, 
                            $this->y, 'UTF-8');
                        }
                    } else {
                        $page->drawText(Mage::helper('sales')->__('No Image'), 
                        300, $this->y, 'UTF-8');
                    }
                    $page->drawText(
                    $gifBlock->getGiftcardName($giftwrapItem['giftcardId']), 300, 
                    $this->y + 10, 'UTF-8');
                    $this->drawGiftcard($page,$gifBlock,$giftwrapItem['giftcardId'],$invoice);
                    $this->drawGift($giftwrapItem, $pdf, $page);
                    if ($check == 1) {
                        $this->y -= 60;
                    } else {
                        $this->y -= 15;
                    }
                }
            }
            /* Add totals */
            $page = $this->insertTotals($page, $invoice);
            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->revert();
            }
        }
        $this->_afterGetPdf();
        return $pdf;
    }
    public function drawGiftcard (&$page,$gifBlock,$giftcardId,$invoice)
    {
	
        $image = $gifBlock->getGiftcardImage($giftcardId);
        if ($image) {
            $fileExtension = end(explode(".", $image));
            switch ($fileExtension) {
                case 'tif':
                    $check = 1;
                    break;
                case 'tiff':
                    $check = 1;
                    break;
                case 'png':
                    $check = 1;
                    break;
                case 'jpg':
                    $check = 1;
                    break;
                case 'jpe':
                    $check = 1;
                    break;
                case 'jpeg':
                    $check = 1;
                    break;
                default:
                    $check = 0;
                    break;
            }
			
            if ($check == 1) {
                /*$image = Mage::getStoreConfig('system/filesystem/media', $store) .
                 '/giftwrap/giftcard/' . $image;*/
				 $image = Mage::getBaseDir('media').DS.'giftwrap'.DS.'giftcard'.DS.$image;
                if (is_file($image)) {					
                    $this->insertImageGif($page, $image, $invoice->getStore(), 
                    $this->y,290);
                } else {
                    $page->drawText(Mage::helper('sales')->__('No Image'), 300, 
                    $this->y, 'UTF-8');
                }
            } else {
                $page->drawText(Mage::helper('sales')->__('Unsupported type.'), 
                300, $this->y, 'UTF-8');
            }
        } else {
            $page->drawText(Mage::helper('sales')->__('No Image'), 300, 
            $this->y, 'UTF-8');
        }
    }
    protected function insertImageGif (&$page, $image, $store = null, $y,$x=null)
    {
        $image = Zend_Pdf_Image::imageWithPath($image);
        if(!$x){
        	$page->drawImage($image, 190, $y - 55, 250, $y + 5);
        }else{
        	$page->drawImage($image, $x, $y - 55, $x+60, $y + 5);
        }
        return $page;
    }
    public function drawGift ($giftwrapItem, $pdf, $page)
    {
        $gifBlock = Mage::getBlockSingleton(
        'giftwrap/adminhtml_sales_order_view_tab_giftwrap');
        $lines = array();
        $lines[0][] = array(
        'text' => Mage::helper('core/string')->str_split(
        $giftwrapItem['giftwrap_message'], 25), 'feed' => 450);
        $lineBlock = array('lines' => $lines, 'height' => 10);
        $page = $this->drawLineBlocks($page, array($lineBlock), 
        array('table_header' => true));
        $this->setPage($page);
    }
    public function drawGiftProduct ($giftwrapItem, $pdf, $page)
    {
        $gifBlock = Mage::getBlockSingleton(
        'giftwrap/adminhtml_sales_order_view_tab_giftwrap');
        $lines = array();
        $lines[0][] = array(
        'text' => Mage::helper('core/string')->str_split(
        $gifBlock->getProduct($giftwrapItem)
            ->getName(), 25), 'feed' => 70);
        $lineBlock = array('lines' => $lines, 'height' => 10);
        $page = $this->drawLineBlocks($page, array($lineBlock), 
        array('table_header' => true));
        $this->setPage($page);
    }
}