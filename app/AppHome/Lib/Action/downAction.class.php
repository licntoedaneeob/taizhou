<?php
class downAction extends TAction
{

	
	public function index(){
        
		$downfilepath=$_GET['path'];


		//获取扩展名
		$picArray=explode(".", $downfilepath);

		if (count($picArray)>1){
		$kuozhanming_key=count($picArray)-1;
		$kuozhanming_value=$picArray[$kuozhanming_key];

		$slashArray=explode("/", $downfilepath);
		$slash_key=count($slashArray)-1;
		$slash_value=$slashArray[$slash_key];

		//header("Content-Disposition: attachment; filename=downfiles.".$kuozhanming_value);
		header("Content-Disposition: attachment; filename=".$slash_value);
		header("Pragma:public");
		readfile('http://'.$_SERVER["HTTP_HOST"].$downfilepath);
		}

        
	}
	

}
?>