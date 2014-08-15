<?php
class Devinc_Occ_Model_Source_Payment
{
	//returns active payment methods
	public function toOptionArray()
    {
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();
        
        $methods = array();
        $methods[] = array(
            'label'   => '',
            'value' => '',
        );
        
        foreach ($payments as $paymentCode=>$paymentModel) {
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
            $methods[$paymentCode] = array(
                'label'   => $paymentTitle,
                'value' => $paymentCode,
            );
        }

        return $methods;
    }
}
