<?php
//	include "filepath.php";
	
	$RecPath = "/var/www/html/recpath.conf";
	$VXMLDir = "vxml/";
	$content_array = file( $RecPath );

	$parse_temp = explode( "=" , $content_array[0] );
	
	$name = chop($parse_temp[0]);
	$path = chop($parse_temp[1]);
	
	$myRec = $_REQUEST["recording"];   

	if( $path == "" || $myRec == "" )
		$PlayVoice = "Can not save record file !!";
	else
	{
		$Writh_RecFile_Path = $VXMLDir.$path;

		$PlayVoice = "Record successfully !!";
		unlink($Writh_RecFile_Path);
		$fp = fopen( $Writh_RecFile_Path , "w+");	
		fwrite($fp,$myRec);
		fclose($fp);
	
	}
	
	echo "<?xml version=\"1.0\"?>";
	echo "<vxml version=\"2.0\" xml:base=\"http://192.168.1.210/vxml\">";
	
?>


<form>
<block>
<prompt>
<? echo $PlayVoice ?> 
</prompt>
</block>
</form>
</vxml>



