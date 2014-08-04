<?php
/**
* @copyright Amasty.
*/  
class Amasty_Shopby_Model_Mysql4_Filter extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('amshopby/filter', 'filter_id');
    }

    public function getIdByCode($code)
    {
        $db = $this->_getReadAdapter();
        
        $sql = $db->select()
            ->from(array('f' => $this->getTable('amshopby/filter')), array('f.filter_id'))
            ->joinInner(array('a'=>$this->getTable('eav/attribute')), 'f.attribute_id = a.attribute_id', array())
            ->where('a.attribute_code = ?', $code)
            ->limit(1);
            
        return $db->fetchOne($sql);  
    }   
    
    /**
     * @param number $id
     * @return array
     */
    public function getFilterByAttributeId($id)
    {
        $db = $this->_getReadAdapter();
        
        $sql = $db->select()
            ->from(array('f' => $this->getTable('amshopby/filter')), array('f.*'))
            ->where('f.attribute_id = ?', $id);
            
        return $db->fetchRow($sql);  
    }
    
    public function createFilters()
    {    
        $db = $this->_getReadAdapter();
        
        //clean values from already removed filters 
        $sqlIds = (string)$db->select()
                ->from(array('f' => $this->getTable('amshopby/filter')), array('filter_id'));
        $o = $this->getTable('amshopby/value');
        $db->raw_query("DELETE FROM $o WHERE $o.filter_id NOT IN(($sqlIds))");

        // select from attributes
        $sql = $db->select()
            ->from(array('a'=>$this->getTable('eav/attribute')), array('a.attribute_id', 'a.backend_type', 'a.attribute_code'))
            ->joinLeft(array('f' => $this->getTable('amshopby/filter')), 'a.attribute_id = f.attribute_id', array())
            ->where('f.filter_id IS NULL');
           
          
        $sql
            ->joinInner(array('ca' => $this->getTable('catalog/eav_attribute')), 'a.attribute_id = ca.attribute_id', array())
            ->where('ca.is_filterable > 0')
            ->where('a.frontend_input IN (?)', array('select', 'multiselect', 'price'))
            ;

        
        $res = $db->fetchAll($sql);  
        
        $data = array();
        $arrIds = array();
        
        foreach ($res as $row) {
            $data['attribute_id'] = $row['attribute_id'];   
            $data['backend_type'] = $row['backend_type'];
            
            /*
             * Set Left block as default placement for filter 
             */
            $data['block_pos']    = 'left';
            $arrIds[] = $data['attribute_id'];
            if ($row['attribute_code'] != 'price') {  
                $db->insert($this->getTable('amshopby/filter'), $data);
            }     
        }

        // create options     
        $sql = $db->select()
            ->from(array('o' => $this->getTable('eav/attribute_option')), array())
            ->joinInner(array('f' => $this->getTable('amshopby/filter')), 'o.attribute_id = f.attribute_id', array('f.filter_id'))
            ->joinInner(array('ov' => $this->getTable('eav/attribute_option_value')), 'o.option_id = ov.option_id', array('ov.option_id','ov.value', 'ov.value'))
            ->joinLeft(array('v' => $this->getTable('amshopby/value')), 'v.option_id = o.option_id', array())
            ->where('ov.store_id = 0')
            ->where('v.value_id IS NULL');
            
        $insertSql = 'INSERT INTO ' . $this->getTable('amshopby/value') . '(filter_id, option_id, title, meta_title) ' .  (string)$sql;
        $db->raw_query($insertSql);
        
        //remove options, removed from attrributes
        $sqlIds = (string)$db->select()
                ->from(array('o' => $this->getTable('eav/attribute_option')), array('option_id'));
        $o = $this->getTable('amshopby/value');
        $db->raw_query("DELETE FROM $o WHERE $o.option_id NOT IN(($sqlIds))");
             
        return true;
    }
}