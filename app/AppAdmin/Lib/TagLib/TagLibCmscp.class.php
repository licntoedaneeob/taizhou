<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2013 All rights reserved.
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
class TagLibCmscp extends TagLib{
    // 标签定义
    protected $tags   =  array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'pictureflg'   => array('attr'=>'filevar,desc,caption', 'close'=>0),
        'fileflg'      => array('attr'=>'filevar,desc,caption', 'close'=>0),
        'statusflg'    => array('attr'=>'valvar,id,href,caption', 'close'=>0),
        'checkflg'     => array('attr'=>'valvar,caption', 'close'=>0),
        'genderflg'     => array('attr'=>'valvar,caption', 'close'=>0)
//        'grid'=>array('attr'=>'id,pk,style,action,actionlist,show,datasource','close'=>0),
//        'list'=>array('attr'=>'id,pk,style,action,actionlist,show,datasource,checkbox','close'=>0),
//        'imagebtn'=>array('attr'=>'id,name,value,type,style,click','close'=>0),
//        'checkbox'=>array('attr'=>'name,checkboxes,checked,separator','close'=>0),
//        'radio'=>array('attr'=>'name,radios,checked,separator','close'=>0),
//     	'calendar'=>array('attr'=>'id,name,ifformat,showstime,timeformat,more')
        );
    
	//<if condition="$rec['thumb'] neq ''"><a href="{$rec['thumb']}" class="pic-lightbox" ><i class="icon-picture exists-yes"  /></a><else /><i class="icon-picture exists-no"  /></if>
	public function _pictureflg($attr){
		$tag      = $this->parseXmlAttr($attr,'pictureflg');
		$filevar  = isset($tag['filevar']) ? $tag['filevar'] : '';
		$caption  = isset($tag['caption']) ? $tag['caption'].' <?php echo ":\\\\n";?>' : '';
		$desc     = isset($tag['desc']) ? $tag['desc'].'<?php echo "\\\\n";?>' : '';
//		trace('_pictureflg, file=['.$file.']', 'TagLibCmscp');
//		if( empty($file) || $file == '' ){
//			$parseStr = '<a href="#" title="Empty" class="flg-picture-empty><i class="icon-picture flg-exists-no"  /></a>';
//		}else{
//			$parseStr = '<a href="'.$file.'" class="flg-picture flg-picture-show" target="_blank" title="'. $desc .'"><i class="icon-picture flg-exists-yes"  /></a>';
//		}
		$parseStr = '<?php if(empty('. $filevar  .')){ ?>';
		$parseStr.= '<a href="#" title="'.$caption.' Empty" class="flg-picture-empty"><i class="icon-picture flg-exists-no" ></i></a>';
		$parseStr.= '<?php } else { ?>';
		$parseStr.= '<a href="<?php echo '.$filevar.';?>" class="flg-picture flg-picture-show" target="_blank" title="'.$caption.' '.$desc.' <?php echo '.$filevar.';?>"><i class="icon-picture flg-exists-yes"  ></i></a>';
		$parseStr.= '<?php } ?>';
		return $parseStr;
	}

	public function _fileflg($attr){
		$tag      = $this->parseXmlAttr($attr,'fileflg');
		$filevar  = isset($tag['filevar']) ? $tag['filevar'] : '';
		$caption  = isset($tag['caption']) ? $tag['caption'].' <?php echo ":\\\\n";?>' : '';
		$desc     = isset($tag['desc']) ? $tag['desc'].' <?php echo "\\\\n";?>' : '';
		trace('_fileflg, file=['.$filevar.']', 'TagLibCmscp');
		$parseStr = '<?php if(empty('. $filevar  .')){ ?>';
		$parseStr.= '<a href="#" title="'.$caption.' Empty" class="flg-file-empty"><i class="icon-file-alt flg-exists-no"  ></i></a>';
		$parseStr.= '<?php } else { ?>';
		$parseStr.= '<a href="<?php echo '.$filevar.';?>" class="flg-file flg-file-show" target="_blank" title="'.$caption.' '.$desc.' <?php echo '.$filevar.';?>"><i class="icon-file-alt flg-exists-yes"  ></i></a>';
		$parseStr.= '<?php } ?>';
//		
//		if( empty($file) || $file == '' ){
//			$parseStr = '<a href="#" title="Empty" class="flg-file-empty"><i class="icon-file-alt flg-exists-no"  /></a>';
//		}else{
//			$parseStr = '<a href="'.$file.'" class="flg-file flg-file-show" target="_blank" title="'. $desc .'"><i class="icon-file-alt flg-exists-yes"  /></a>';
//		}
		return $parseStr;
	}
	//<a href="javascript:table_change_status({$rec['id']},'form_record_status')" id="form_record_status_{$rec['id']}"><img src="__ROOT__/statics/images/status_{$rec['status']}.gif" /></a>
	public function _statusflg($attr){
		$tag      = $this->parseXmlAttr($attr,'statusflg');
		$valvar   = isset($tag['valvar']) ? $tag['valvar'] : '';
		$caption  = isset($tag['caption']) ? $tag['caption'].' <?php echo ":\\\\n";?>' : '';
		$id       = isset($tag['id'])     ? $tag['id'] : '';
		$href     = isset($tag['href'])   ? $tag['href'] : '#';
		trace('_statusflg, status=['.$valvar.']', 'TagLibCmscp');
		
		$parseStr = '<a href="'. str_replace('"', '\"', $href) .'" id="'. $id .'" class="flg-status" title="'. $caption .'"><img src="'. __ROOT__ .'/statics/images/status_<?php echo '. $valvar .';?>.gif" /></a>';
		return $parseStr;
	}
	//<if condition="$rec['r_yusai'] eq '1'"><i class="icon-check color-yes" /><else /><i class="icon-check-empty color-no" />
	public function _checkflg($attr){
		$tag      = $this->parseXmlAttr($attr,'checkflg');
		$valvar   = isset($tag['valvar']) ? $tag['valvar'] : '';
		$caption  = isset($tag['caption']) ? $tag['caption'].' <?php echo "\\\\n";?>' : '';
		$parseStr = '<?php if(empty('. $valvar  .')){ ?>';
			$parseStr.= '<i class="icon-check-empty color-check-no" title="'. $caption .'" ></i>';
		$parseStr.= '<?php } else { ?>';
			$parseStr.= '<i class="icon-check color-check-yes" title="'. $caption .'" ></i>';
		$parseStr.= '<?php } ?>';
//		if( empty($value) ){
//			$parseStr = '<i class="icon-check-empty flg-check-no" />';
//		}else{
//			$parseStr = '<i class="icon-check flg-check-yes" />';
//		}
		return $parseStr;
	}

	public function _genderflg($attr){
		$tag      = $this->parseXmlAttr($attr,'genderflg');
		$valvar   = isset($tag['valvar']) ? $tag['valvar'] : '';
		$caption  = isset($tag['caption']) ? $tag['caption'].' <?php echo ":\\\\n";?>' : '<?php echo L("性别").":\\\\n";?>';
		$parseStr = '<?php if('. $valvar  .' == "0"){ ?>';
			$parseStr.= '<i class="icon-user color-female" title="'. $caption .' <?php echo L("女");?>" ></i>';
		$parseStr.= '<?php } else  if('. $valvar  .' == "1"){ ?>';
			$parseStr.= '<i class="icon-user color-male" title="'. $caption .' <?php echo L("男");?>" ></i>';
		$parseStr.= '<?php } else { ?>';
			$parseStr.= '<i class="icon-user color-gender" title="'. $caption .' <?php echo L("未知");?>" ></i>';
		$parseStr.= '<?php } ?>';
		return $parseStr;
	}

}
?>