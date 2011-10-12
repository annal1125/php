<?php
if($_COOKIE['login'] != "yes")
{
	header('Location: index.php');
	exit;
}


include "filepath.php";

	
function ReadFilename( $FileDir )
{
	global $VXMLDir;
	$FileDirTmp = array();
	
	$FilePath = $VXMLDir."/".$FileDir; 
	
	$handle=opendir($FilePath);
	
	while ($file = readdir($handle)) 	          		  
	{
		if( $file == "." || $file == ".."  )
			continue;
		$type = filetype( $FilePath."/".$file );
		
		if( $type == "file" )
		{
			if( strstr( $file , "xml" ) != "" )
				array_push( $FileDirTmp , $file );
		}
		else if( $type == "dir" )
				array_push( $FileDirTmp , $file );
	}	   
	closedir($handle);		

	return $FileDirTmp;
}

function CheckDir( $FileArray )
{
	global $VXMLDir;
	
	$IsDir = -1;
	while( list($key , $val) = each($FileArray) )
	{	
		if( filetype( $VXMLDir."/".$val ) == "dir" )
		{
			$IsDir = $key;
			break;
		}
	}
	return $IsDir;
}
	$SelName = $_SERVER["QUERY_STRING"];
	
	if( $SelName != "" )
	{
		$parse_temp = explode( "=" , $SelName );
		$SelName = chop($parse_temp[1]);
	}
	$VXmlFile = array();
	$VXmlFile = ReadFilename("");
	
	reset($VXmlFile);
	$i = 0;
	$Dir = CheckDir($VXmlFile);
	
	while( ($Dir = CheckDir($VXmlFile)) != -1 && $i++ < 10000 )
	{
		$VXmltmp1 = ReadFilename($VXmlFile[$Dir]);
	
		while( list($key , $val) = each($VXmltmp1) )
			$VXmltmp1[$key] = $VXmlFile[$Dir]."/".$val;
		array_splice($VXmlFile , $Dir, 1 , $VXmltmp1  );
	
	}
		


?>




<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>Voice XML files</title>

<script language = "javascript">
function ClickOK()
{
	var localhost = window.location.host;


	var parentField = "xmlpath" + <? echo $SelName; ?>;
	var FORM=document.forms[0];
	var iSel = FORM.ComboSel.selectedIndex;
	var returnStr = "http://" + localhost + "/vxml/" + FORM.ComboSel.options[iSel].text;
	window.opener.document.getElementById(parentField).value = returnStr;
	window.close();   
}

</script>
<style type="text/css">
<!--
body {
	background-color: #990000;
}
body,td,th {
	color: #FFFFFF;
}
.style1 {font-family: Arial, Helvetica, sans-serif}
.style3 {font-family: "Courier New", Courier, monospace}
-->
</style></head>
<body>
<form name="form" method="POST" enctype="multipart/form-data" action="<? $PHP_SELF ?>">
<div align="center">Voice XML Files</div>
<select name="ComboSel" size="10" ondblclick=ClickOK(); align="center" style="width: 90%; height :70%" STYLE='background-color:#FFCC99;'>
<?   
	reset($VXmlFile);
	while( list($key , $val) = each($VXmlFile) )
		echo "<option value=$key>$val</option>";
?>
</select>

<br>
<p align="center"> 
<input type='submit' name='Submit' onclick=ClickOK(); value='<? echo $WORDLIST['submit'][$LANG] ?>'  style='border: 5px dotted #C0C0C0; background-color: #FFD5AA'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="Cancel" onclick="window.close();" value="<? echo $WORDLIST['cancel'][$LANG] ?>" style="border: 5px dotted #C0C0C0; background-color: #FFD5AA" > </td>
</p>

</form>
</body>


</html>
