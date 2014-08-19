<?php

class Magestore_Onestepcheckout_Model_Customer_Form extends Mage_Customer_Model_Form
{
	
	public function validateData(array $data)
    {
		if(Mage::helper('onestepcheckout')->enabledOnestepcheckout()){
			return true;
		}
        $errors = array();
        foreach ($this->getAttributes() as $attribute) {
            if ($this->_isAttributeOmitted($attribute)) {
                continue;
            }
            $dataModel = $this->_getAttributeDataModel($attribute);
            $dataModel->setExtractedData($data);
            if (!isset($data[$attribute->getAttributeCode()])) {
                $data[$attribute->getAttributeCode()] = null;
            }
            $result = $dataModel->validateValue($data[$attribute->getAttributeCode()]);
            if ($result !== true) {
                $errors = array_merge($errors, $result);
            }
        }

        if (count($errors) == 0) {
            return true;
        }

        return $errors;
    }

}
