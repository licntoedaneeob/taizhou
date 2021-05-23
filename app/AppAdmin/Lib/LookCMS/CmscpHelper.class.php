<?php
///CmscpMode 辅助类

class CmscpHelper{
    private static $_instance = null;
	
	function __construct(){
		//parent::__construct();
//        if(method_exists($this,'_initialize'))
//            $this->_initialize();
	}
    
    /**
     * 取得类实例
     * @static
     * @access public
     * @return mixed
     */
    static function getInstance() {
		if(! self::$_instance){
			self::$_instance = new self;
		}
		return self::$_instance;
    }



}


?>