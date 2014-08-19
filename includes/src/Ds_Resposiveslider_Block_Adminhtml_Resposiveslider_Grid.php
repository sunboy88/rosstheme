<?php

class Ds_Resposiveslider_Block_Adminhtml_Resposiveslider_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("resposivesliderGrid");
				$this->setDefaultSort("slide_id");
				$this->setDefaultDir("ASC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("resposiveslider/resposiveslider")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("slide_id", array(
				"header" => Mage::helper("resposiveslider")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "slide_id",
				));
                
				$this->addColumn("title", array(
				"header" => Mage::helper("resposiveslider")->__("Title"),
				"index" => "title",
				));
						$this->addColumn('is_active', array(
						'header' => Mage::helper('resposiveslider')->__('Active'),
						'index' => 'is_active',
						'type' => 'options',
						'options'=>Ds_Resposiveslider_Block_Adminhtml_Resposiveslider_Grid::getOptionArray3(),				
						));
						

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('slide_id');
			$this->getMassactionBlock()->setFormFieldName('slide_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_resposiveslider', array(
					 'label'=> Mage::helper('resposiveslider')->__('Remove Resposiveslider'),
					 'url'  => $this->getUrl('*/adminhtml_resposiveslider/massRemove'),
					 'confirm' => Mage::helper('resposiveslider')->__('Are you sure?')
				));
			return $this;
		}
			
		static public function getOptionArray3()
		{
            $data_array=array(); 
			$data_array[0]='Active';
			$data_array[1]='In Active';
            return($data_array);
		}
		static public function getValueArray3()
		{
            $data_array=array();
			foreach(Ds_Resposiveslider_Block_Adminhtml_Resposiveslider_Grid::getOptionArray3() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		

}