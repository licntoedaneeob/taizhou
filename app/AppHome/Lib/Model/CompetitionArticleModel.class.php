<?php
class CompetitionArticleModel extends Model
{
    // 数据表名（不包含表前缀）
    protected $tableName        =   'competition_article';

	// 自动填充设置
	protected $_auto = array(
		array('status', '1', self::MODEL_INSERT, 'string'),
		array('sort', '100', self::MODEL_INSERT, 'string'),
		array('create_time', 'time', self::MODEL_INSERT, 'function'),
		array('modify_time', 'time', self::MODEL_INSERT, 'function'),
		array('modify_time', 'time', self::MODEL_UPDATE, 'function')
	);	

}