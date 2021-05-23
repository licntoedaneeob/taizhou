<?php

class LkHTML
{
	
    /**
     * ListSort
     * @access public
     * @param $text       : 当前排序列标题
     * @param $order_code : 当前排序列代码
     * @param $now_order  : 当前列表页排序代码
     * @param $direction  : 当前列表页排序方向( asc / desc )
     * @param $jsfunc     : 点击排序调用的 js 名称。（此 JS 接受 3 个参数 order, dir, task ）
     * @param $title      : 鼠标移到排序列的提示文字
     * @param $task       : 默认值 NULL
     * @return html
     */
	public static function ListSort( $text, $order_code, $now_order = 0, $direction = 'asc', $jsfunc = '', $title = '', $task=NULL){
		$jsfunc = ( $jsfunc == '' ) ? 'lkListOrdering' : $jsfunc;
		$title = ( $title == '' ) ? 'Click to sort this column' : $title;
		$direction	= strtolower( $direction );
		$images		= array( '<i class="icon-sort-up" />', '<i class="icon-sort-down" />', '<i class="icon-sort" />' );
		$index = ( $order_code == $now_order ) ? ( intval( $direction == 'desc' ) ) : 2;
		$direction	= ($direction == 'desc') ? 'asc' : 'desc';
		$html = '<a href="javascript:'.$jsfunc.'(\''.$order_code.'\',\''.$direction.'\',\''.$task.'\');" title="'.$title.'">';
		$html.= $text;
		$html.= ' '.$images[$index];
		$html.= '</a>';
		return $html;
	}
	public static function ListSortIcon( $order_code, $now_order = 0, $direction = 'asc' ){
		$direction	= strtolower( $direction );
		$images		= array( '<i class="icon-sort-up" />', '<i class="icon-sort-down" />', '<i class="icon-sort" />' );
		$index = ( $order_code == $now_order ) ? ( intval( $direction == 'desc' ) ) : 2;
		return $images[$index];
	}
	public static function ListSortHref( $jsfunc = 'lkListOrdering', $order_code, $now_order = 0, $direction = 'asc' ){
		$direction	= strtolower( $direction );
		$direction	= ($direction == 'desc') ? 'asc' : 'desc';
		$html = 'javascript:'. $jsfunc . '(\''.$order_code.'\',\''.$direction.'\',\''.$task.'\');';
		return $html;
	}
	
	

}
?>