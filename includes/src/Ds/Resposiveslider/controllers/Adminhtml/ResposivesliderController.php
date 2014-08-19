<?php
class Ds_Resposiveslider_Adminhtml_ResposivesliderController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction()
	{
		$this->loadLayout()->_setActiveMenu("resposiveslider/resposiveslider")->_addBreadcrumb(Mage::helper("adminhtml")->__("Resposiveslider  Manager"),Mage::helper("adminhtml")->__("DS Responsive Slider Manager"));
		return $this;
	}
	
	public function indexAction() 
	{
		$this->_title($this->__("DS Responsive Slider"));
		$this->_title($this->__("Manager DS Responsive Slider"));

		$this->_initAction();
		$this->renderLayout();
	}
	
	public function editAction()
	{			    
		$this->_title($this->__("Resposiveslider"));
		$this->_title($this->__("Resposiveslider"));
		$this->_title($this->__("Edit Item"));
		
		$id = $this->getRequest()->getParam("id");
		$model = Mage::getModel("resposiveslider/resposiveslider")->load($id);
		if ($model->getId())
		{
			Mage::register("resposiveslider_data", $model);
			$this->loadLayout();
			$this->_setActiveMenu("resposiveslider/resposiveslider");
			$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Resposiveslider Manager"), Mage::helper("adminhtml")->__("Resposiveslider Manager"));
			$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Resposiveslider Description"), Mage::helper("adminhtml")->__("Resposiveslider Description"));
			$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock("resposiveslider/adminhtml_resposiveslider_edit"))->_addLeft($this->getLayout()->createBlock("resposiveslider/adminhtml_resposiveslider_edit_tabs"));
			$this->renderLayout();
		} 
		else
		{
			Mage::getSingleton("adminhtml/session")->addError(Mage::helper("resposiveslider")->__("Item does not exist."));
			$this->_redirect("*/*/");
		}
	}

	public function newAction()
	{
		$this->_title($this->__("Resposiveslider"));
		$this->_title($this->__("Resposiveslider"));
		$this->_title($this->__("New Item"));

		$id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("resposiveslider/resposiveslider")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data))
		{
			$model->setData($data);
		}

		Mage::register("resposiveslider_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("resposiveslider/resposiveslider");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("DS Responsive Slider Manager"), Mage::helper("adminhtml")->__("DS Responsive Slider Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("DS Responsive Slider Description"), Mage::helper("adminhtml")->__("DS Responsive Slider Description"));


		$this->_addContent($this->getLayout()->createBlock("resposiveslider/adminhtml_resposiveslider_edit"))->_addLeft($this->getLayout()->createBlock("resposiveslider/adminhtml_resposiveslider_edit_tabs"));

		$this->renderLayout();

	}
	
	public function saveAction()
	{
		$post_data=$this->getRequest()->getPost();
			if ($post_data)
			{
				try
				{
				//save image
					try
					{
						if(isset($post_data['image_name']['delete']))
						{
							$del	=	1;
						}
						else
						{
							$del	=	0;
						}
						if($del)
						{
							$post_data['image_name']='';
						}
						else
						{
							unset($post_data['image_name']);
							if (isset($_FILES))
							{
								if ($_FILES['image_name']['name'])
								{
									if($this->getRequest()->getParam("id")){
										$model = Mage::getModel("resposiveslider/resposiveslider")->load($this->getRequest()->getParam("id"));
										if($model->getData('image_name')){
												$io = new Varien_Io_File();
												$io->rm(Mage::getBaseDir('media').DS.implode(DS,explode('/',$model->getData('image_name'))));	
										}
									}
									$path = Mage::getBaseDir('media') . DS . 'resposiveslider' . DS .'resposiveslider'.DS;
									$uploader = new Varien_File_Uploader('image_name');
									$uploader->setAllowedExtensions(array('jpg','png','gif'));
									$uploader->setAllowRenameFiles(false);
									$uploader->setFilesDispersion(false);
									$destFile = $path.$_FILES['image_name']['name'];
									$filename = $uploader->getNewFileName($destFile);
									$uploader->save($path, $filename);

									$post_data['image_name']='resposiveslider/resposiveslider/'.$filename;
								}
							}
						}
					}
					catch (Exception $e)
					{
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
						return;
					}
					//save image
					$model = Mage::getModel("resposiveslider/resposiveslider")
					->addData($post_data)
					->setId($this->getRequest()->getParam("id"))
					->save();

					Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Resposiveslider was successfully saved"));
					Mage::getSingleton("adminhtml/session")->setResposivesliderData(false);

					if ($this->getRequest()->getParam("back"))
					{
						$this->_redirect("*/*/edit", array("id" => $model->getId()));
						return;
					}
					$this->_redirect("*/*/");
					return;
				}
				catch (Exception $e)
				{
					Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
					Mage::getSingleton("adminhtml/session")->setResposivesliderData($this->getRequest()->getPost());
					$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					return;
				}

			}
		$this->_redirect("*/*/");
	}
	
	public function deleteAction()
	{
		if( $this->getRequest()->getParam("id") > 0 )
		{
			try {
				$model = Mage::getModel("resposiveslider/resposiveslider");
				$model->setId($this->getRequest()->getParam("id"))->delete();
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
				$this->_redirect("*/*/");
			} 
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
				$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
			}
		}
		$this->_redirect("*/*/");
	}

	public function massRemoveAction()
	{
		try
		{
			$ids = $this->getRequest()->getPost('slide_ids', array());
			foreach ($ids as $id) {
				  $model = Mage::getModel("resposiveslider/resposiveslider");
				  $model->setId($id)->delete();
			}
			Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
		}
		catch (Exception $e)
		{
			Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
		}
		$this->_redirect('*/*/');
	}		
}
