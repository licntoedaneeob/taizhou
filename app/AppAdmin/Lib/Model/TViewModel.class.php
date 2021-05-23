<?php
class TViewModel extends ViewModel
{

    private function _checkFieldsXXX($name,$fields) {
        if(false !== $pos = array_search('*',$fields)) {// 定义所有字段
            $fields  =  array_merge($fields,M($name)->getDbFields());
            unset($fields[$pos]);
        }
        return $fields;
    }
    /**
     * 检查Order表达式中的视图字段
     * @access protected
     * @param string $order 字段
     * @return string
     */
    protected function checkOrder($order='') {
         if(is_string($order) && !empty($order)) {
         	$order = trim($order);
         	$order = preg_replace('/[\s]{2,}/', ' ', $order);
            $orders = explode(',',$order);
            $_order = array();
            foreach ($orders as $order){
            	$order = trim($order);
                $array = explode(' ',$order);
                $field   =   $array[0];
                $sort   =   isset($array[1])?$array[1]:'ASC';
                // 解析成视图字段
                foreach ($this->viewFields as $name=>$val){
                    $k = isset($val['_as'])?$val['_as']:$name;
                    $val  =  $this->_checkFieldsXXX($name,$val);
                    if(false !== $_field = array_search($field,$val,true)) {
                        // 存在视图字段
                        $field     =  is_numeric($_field)?$k.'.'.$field:$k.'.'.$_field;
                        break;
                    }
                }
                $_order[] = $field.' '.$sort;
            }
            $order = implode(',',$_order);
         }
        return $order;
    }


}



?>