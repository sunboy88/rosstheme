<?php

class Belvg_Referralreward_Block_Adminhtml_Title extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Render element html
     *
     * @param Varien_Data_Form_Element_Abstract
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $id    = $element->getHtmlId();
        $html  = '<tr id="row_' . $id . '" class="system-fieldset-sub-head">
                    <td colspan="5"><h4>' . $element->getLabel() . '</h4></td>
                  </tr>';

        return $html;
    }

}
