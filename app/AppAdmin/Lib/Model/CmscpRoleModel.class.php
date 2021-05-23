<?php
class CmscpRoleModel extends RelationModel
{
    // 数据表名（不包含表前缀）
    protected $tableName        =   'cmscp_role';

	// 自动填充设置
	protected $_auto = array(
		array('status', '1', self::MODEL_INSERT),
		array('sort', '100', self::MODEL_INSERT),
	);	

	protected $_link=array(
		'admin'=>array(
			'mapping_type'  => HAS_MANY,
			'class_name'    => 'CmscpManager',
			'foreign_key'   => 'role_id',
			'parent_key'    => 'role_id',
			'mapping_name'  => 'managers',
		),
	);
}