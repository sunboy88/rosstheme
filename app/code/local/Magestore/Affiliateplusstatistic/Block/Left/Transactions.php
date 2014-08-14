<?php
class Magestore_Affiliateplusstatistic_Block_Left_Transactions extends Magestore_Affiliateplusstatistic_Block_Left_Pie
{
	/* 23-04-2014 hainh update function fix error displaying google chart */
    public function __construct() {
        $collection = Mage::getResourceModel('affiliateplusstatistic/sales_collection')
                ->prepareLifeTimeTotal();
        $statusArr = array(1, 2, 3);
        if ($storeId = $this->getRequest()->getParam('store'))
            $collection->addFieldToFilter('store_id', $storeId);

        $chartData = array();
        foreach ($collection->load() as $item) {
            if (in_array($item->getStatus(), $statusArr)) {
                $chartData[$item->getStatus()] = $item->getTotal();
            }
        }

        foreach ($statusArr as $status) {
            if (!isset($chartData[$status]))
                $chartData[$status] = 0;
        }

        ksort($chartData);

        if (count($chartData))
            $this->_is_has_data = true;

        $buffer = implode(',', $chartData);

        $this->_google_chart_params = array(
            'cht' => 'p3',
            'chdl' => $this->__('Completed (%d)', $chartData[1])
            . '|' . $this->__('Pending (%d)', $chartData[2])
            . '|' . $this->__('Canceled (%d)', $chartData[3]),
            'chd' => "t:$buffer",
            'chdlp' => 'b',
            'chco' => '0000dd|00dd00|dd0000'
        );

        $this->setHtmlId('left_transactions');
        parent::__construct();
    }
    
    protected function _prepareData(){
    	$this->setDataHelperName('affiliateplusstatistic/traffics');
    }
}