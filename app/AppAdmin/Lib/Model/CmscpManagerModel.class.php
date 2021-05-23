<?php
class CmscpManagerModel extends RelationModel
{
    // 数据表名（不包含表前缀）
    protected $tableName        =   'cmscp_manager';

	protected $_link=array(
		'role'=>array(
			'mapping_type'  => BELONGS_TO,
			'class_name'    => 'CmscpRole',
			'foreign_key'   => 'role_id',
			'parent_key'    => 'role_id',
			'mapping_name'  => 'rolerec',
		),
	);
	
	// 自动填充设置
	protected $_auto = array(
		array('status', '1', self::MODEL_INSERT),
		array('create_time', 'time', self::MODEL_INSERT, 'function'),
		array('modify_time', 'time', self::MODEL_UPDATE, 'function')
	);	
}
?>