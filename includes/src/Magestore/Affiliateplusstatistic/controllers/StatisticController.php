<?php

class Magestore_Affiliateplusstatistic_StatisticController extends Mage_Core_Controller_Front_Action {

    protected function _initAction() {
        $this->_title($this->__('Affiliateplus'))
                ->_title($this->__('Statistic'));
        $this->loadLayout()
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Affiliate Plus'), Mage::helper('adminhtml')->__('Statistic'));

        return $this;
    }

    /**
     * get Account helper
     *
     * @return Magestore_Affiliateplus_Helper_Account
     */
    protected function _getAccountHelper() {
        return Mage::helper('affiliateplus/account');
    }

    public function indexAction() {
        if (!Mage::getStoreConfig('affiliateplus/statistic/enable')) {
            return $this->_redirect('affiliateplus/index/index');
        }
        if ($this->_getAccountHelper()->accountNotLogin()) {
            return $this->_redirect('affiliateplus');
        }
        $this->_title($this->__('Reports'));
        $this->loadLayout();
        $this->renderLayout();
    }

    public function ajaxBlockAction() {
        $output = '';
        $blockTab = $this->getRequest()->getParam('block');
        if (in_array($blockTab, array(
                    'frontend_diagrams_sales',
                    'frontend_diagrams_transactions',
                    'frontend_diagrams_commissions',
                    'frontend_diagrams_totals',
                    'frontend_diagrams_clicks',
                    'frontend_diagrams_impressions'
                ))) {
            $block = $this->getLayout()->createBlock("affiliateplusstatistic/$blockTab");
            $output = $block->toHtml();
        }
        $this->getResponse()->setBody($output);
    }

    public function tunnelAction() {
        //if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
        $httpClient = new Varien_Http_Client();
        $gaData = $this->getRequest()->getParam('ga');
        $gaHash = $this->getRequest()->getParam('h');
        if ($gaData && $gaHash) {
            $newHash = Mage::helper('adminhtml/dashboard_data')->getChartDataHash($gaData);
            if ($newHash == $gaHash) {
                if ($params = unserialize(base64_decode(urldecode($gaData)))) {
                    $response = $httpClient->setUri(Mage_Adminhtml_Block_Dashboard_Graph::API_URL)
                            ->setParameterGet($params)
                            ->setConfig(array('timeout' => 5))
                            ->request('GET');
                    $headers = $response->getHeaders();

                    $this->getResponse()
                            ->setHeader('Content-type', $headers['Content-type'])
                            ->setBody($response->getBody());
                }
            }
        }
    }

    public function reportAction() {
        $report_type = $this->getRequest()->getParam('report_type');
        $group_by = $this->getRequest()->getParam('group_by');
        $period = $this->getRequest()->getParam('period');
        $date_from = $this->getRequest()->getParam('date_from');
        $date_to = $this->getRequest()->getParam('date_to');
        $status = $this->getRequest()->getParam('status');
        if ($report_type == 1) {
            $block = $this->getLayout()->createBlock('affiliateplusstatistic/frontend_report_sales_grid');
            $filterData = new Varien_Object(
                            array(
                                "filter_group_by" => $group_by,
                                "period_type" => $period,
                                "from" => $date_from,
                                "to" => $date_to
                            )
            );
            $block->setFilterData($filterData);
            $block->setPeriodType($period);

            $this->getResponse()->setBody($block->toHtml());
        } else if ($report_type == 2) {
            $block = $this->getLayout()->createBlock('affiliateplusstatistic/frontend_report_actions_clicks');
            //$block->setTemplate('affiliateplusstatistic/report/grid/container.phtml');
            $filterData = new Varien_Object(
                            array(
                                "filter_group_by" => $group_by,
                                "period_type" => $period,
                                "from" => $date_from,
                                "to" => $date_to
                            )
            );
            $block->setFilterData($filterData);
            $block->setPeriodType($period);
            $block->setActionType(2);
            //Zend_Debug::dump($block->getCollection()->getAllIds());die('1');
            $this->getResponse()->setBody($block->toHtml());
        } else if ($report_type == 3) {
            $block = $this->getLayout()->createBlock('affiliateplusstatistic/frontend_report_actions_impressions');
            $filterData = new Varien_Object(
                            array(
                                "filter_group_by" => $group_by,
                                "period_type" => $period,
                                "from" => $date_from,
                                "to" => $date_to
                            )
            );
            $block->setFilterData($filterData);
            $block->setPeriodType($period);
            $block->setActionType(1);
            $this->getResponse()->setBody($block->toHtml());
        }
    }

    public function _initReportAction($blocks) {
        if (!is_array($blocks))
            $blocks = array($blocks);

        $requestData = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
        $requestData = $this->_filterDates($requestData, array('from', 'to'));
        $requestData['store_ids'] = Mage::app()->getStore()->getId();
        $params = new Varien_Object();

        foreach ($requestData as $key => $value)
            if (!empty($value))
                $params->setData($key, $value);

        foreach ($blocks as $block)
            if ($block) {
                $block->setPeriodType($params->getData('period_type'));
                $block->setFilterData($params);
            }

        return $this;
    }

    public function exportSalesCsvAction() {
        //if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
        $report_type = $this->getRequest()->getParam('report_type');
        if ($report_type == 1) {
            $fileName = 'affiliateSales.csv';
            $grid = Mage::getBlockSingleton('affiliateplusstatistic/frontend_report_sales_grid');
        } else if ($report_type == 2) {
            $fileName = 'affiliateClicks.csv';
            $grid = Mage::getBlockSingleton('affiliateplusstatistic/frontend_report_actions_clicks');
        } else if ($report_type == 3) {
            $fileName = 'affiliateImpressions.csv';
            $grid = Mage::getBlockSingleton('affiliateplusstatistic/frontend_report_actions_impressions');
        }
        $csv = $grid->getCsv();
        $this->_prepareDownloadResponse($fileName, $csv);
    }

    public function exportXmlAction() {
        $report_type = $this->getRequest()->getParam('report_type');
        if ($report_type == 1) {
            $fileName = 'affiliateSales.xml';
            $grid = Mage::getBlockSingleton('affiliateplusstatistic/frontend_report_sales_grid');
        } else if ($report_type == 2) {
            $fileName = 'affiliateClicks.xml';
            $grid = Mage::getBlockSingleton('affiliateplusstatistic/frontend_report_actions_clicks');
        } else if ($report_type == 3) {
            $fileName = 'affiliateImpressions.xml';
            $grid = Mage::getBlockSingleton('affiliateplusstatistic/frontend_report_actions_impressions');
        }
        $xml = $grid->getXml($fileName);
        $this->_prepareDownloadResponse($fileName, $xml);
    }

    protected function _prepareDownloadResponse($fileName, $content, $contentType = 'application/octet-stream', $contentLength = null) {
        $session = Mage::getSingleton('admin/session');
        if ($session->isFirstPageAfterLogin()) {
            $this->_redirect($session->getUser()->getStartupPageUrl());
            return $this;
        }

        $isFile = false;
        $file = null;
        if (is_array($content)) {
            if (!isset($content['type']) || !isset($content['value'])) {
                return $this;
            }
            if ($content['type'] == 'filename') {
                $isFile = true;
                $file = $content['value'];
                $contentLength = filesize($file);
            }
        }

        $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHeader('Content-type', $contentType, true)
                ->setHeader('Content-Length', is_null($contentLength) ? strlen($content) : $contentLength)
                ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                ->setHeader('Last-Modified', date('r'));

        if (!is_null($content)) {
            if ($isFile) {
                $this->getResponse()->clearBody();
                $this->getResponse()->sendHeaders();

                $ioAdapter = new Varien_Io_File();
                $ioAdapter->open(array('path' => $ioAdapter->dirname($file)));
                $ioAdapter->streamOpen($file, 'r');
                while ($buffer = $ioAdapter->streamRead()) {
                    print $buffer;
                }
                $ioAdapter->streamClose();
                if (!empty($content['rm'])) {
                    $ioAdapter->rm($file);
                }
            } else {
                $this->getResponse()->setBody($content);
            }
        }
        return $this;
    }

}
