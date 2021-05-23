<?php
session_start();
header("Content-type:text/html;charset:utf-8");
//全局变量


//检查并重设重复文件名
function checkFileName($filefolder,$filename)
{
	$i = 1;
	while (file_exists($filefolder."/".$filename)){
		$fn = $filename;
		$dotpos = strrpos($fn,".");
		$mainfn = substr($fn,0,$dotpos);
		$exfn = substr($fn,$dotpos+1,strlen($fn));
		$leftpos = strrpos($mainfn,"[");
		$rightpos = strrpos($mainfn,"]");
		if ($leftpos === false && $rightpos === false){
			$filename = $mainfn."[".$i."]".".".$exfn;
		}else{
			$signnum = substr($mainfn,$leftpos,$rightpos-$leftpos+1);
			$simplenum = substr($signnum,1,strlen($signnum)-2);
			if (is_numeric($simplenum)){
				$mainfn = str_replace($signnum,"",$mainfn);
			}
			$filename = $mainfn."[".$i."]".".".$exfn;
		}
		$i++;
	}
	return $filename;
}
//上传文件
function uploadImg($photeDir,$temp_name,$file_name)
{
	$imgPath = $photeDir . '/' . $file_name;
	$dPath = $imgPath;
	@move_uploaded_file($temp_name, $dPath);
}




$uploadroad="D:/www/test/phpexcel/files/";

$succ_result=0;
$error_result=0;
$file=$_FILES['pic'];
$max_size="20000000"; //最大文件限制（单位：byte） 20M
$fname=$file['name'];
$ftype=strtolower(substr(strrchr($fname,'.'),1));


if(!($ftype=='xls' || $ftype=='xlsx')){
         echo "Import file type is error";
          exit;   
         }
         
         
         



      if ($_FILES['pic']['name'] != "") {
      	  
					$filename = checkFileName($uploadroad, $_FILES['pic']['name']);
					$file11   =   basename($filename);
					$aa=explode(".",$file11);
                    $aa_num=count($aa)-1;
                    
$fname="excel_".time()."_".rand(10,99);
                    
                    $filename=$fname.".".$aa[$aa_num];
					uploadImg($uploadroad, $_FILES['pic']['tmp_name'], $filename);
					
//原始图
$insertArray['pic']=$filename;



				} else {
					$insertArray['pic'] = "";

				}
				

				
//文件格式
 $uploadfile=$uploadroad.$filename;

//require("./conn.php");  //连接mysql数据库

//调用phpexcel类库
require_once 'Classes/phpexcel.php'; 
require_once 'Classes/PHPExcel/IOFactory.php';


$objReader = PHPExcel_IOFactory::createReader('Excel2007');//use excel2007 for 2007 format 

$objPHPExcel = $objReader->load($uploadfile); 


$sheet = $objPHPExcel->getSheet(0); 
$highestRow = $sheet->getHighestRow(); // 取得总行数 
$highestColumn = $sheet->getHighestColumn(); // 取得总列数
  $arr_result=array();
  $strs=array();

//$j=3 代表从第3行开始获取数据
for($j=3;$j<=$highestRow;$j++)
 { 
    unset($arr_result);
    unset($strs);
 for($k='A';$k<= $highestColumn;$k++)
    { 
     //读取单元格
  $arr_result  .= $objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue().',';
    }
 $strs=explode(",",$arr_result);
 $sql="insert into student(typeId,name,sex,age) values ($strs[0],'$strs[1]','$strs[2]',$strs[3])";
 echo $sql."<br/>"; 
 //mysql_query("set names utf8");
 //$result=mysql_query($sql) or die("执行错误");

 //$insert_num=mysql_affected_rows();
  //if($insert_num>0){
  //      $succ_result+=1;
   // }else{
   //     $error_result+=1;
   //}
}

echo "插入成功".$succ_result."条数据！！！<br>";
echo "插入失败".$error_result."条数据！！！";
?>