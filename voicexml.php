<?php
if($_COOKIE['login'] != "yes")
{
	header('Location: index.php');
	exit;
}
		
		
	include "filepath.php";

	$VXML_BASE_PATH = "vxml/";
	$VXML_FILE = "/etc/voiceglue.conf";

	$SubmitAcction = $_SERVER["QUERY_STRING"];
	
	if( $SubmitAcction != "" )
	{
		$parse_temp = explode( "=" , $SubmitAcction );
		$SubmitAcction = chop($parse_temp[1]);
	}

	if(isset($_POST[Submit]))
	{
		unlink($VXML_FILE);
		unlink($RecPath);
		$fp = fopen( $VXML_FILE , "w+");


		
		$StrTotal = "";	
		if( $recNum != "" && $recfile != "" )
		{
			$StrTotal .= "$recNum record.vxml\n";
			$fp1 = fopen( $RecPath , "w+");
			fwrite( $fp1 , "rec=$recfile" );		
			fclose( $fp1 );
		}
		
		for( $i = 0; $i < $MAX_VXML_FIELD; $i++ )
		{
			$Exten = sprintf("Exten%d",$i);
			$Exten = $$Exten;
			$xmlpath = sprintf("xmlpath%d",$i);
			$xmlpath = $$xmlpath;
		
			if( $Exten == "" || $xmlpath == "" )
				continue;
				
			if( strstr( $xmlpath , "://" ) != "" ) // http
				$StrTotal .= "$Exten $xmlpath\n";
			else // file
				$StrTotal .= "$Exten $VXML_BASE_PATH$xmlpath\n";
		}
		fwrite( $fp , $StrTotal );		
		fclose( $fp );
//    		shell_exec( "asterisk -rx reload" );
	}	


	$Exten = array();
	$Vxml = array();	
	
	$RecPathtmp = file( $RecPath );
	$parse_temp = explode( "=" , $RecPathtmp[0] );
	
	$recfile = chop($parse_temp[1]);
	
	$content_array = file( $VXML_FILE );
	reset($content_array);
	$i = 0;
	while( list($key , $val) = each($content_array) )
	{	
		$tmp = explode( " " , $val );
	
		if( strstr( $tmp[1] , "http" ) == "" ) // local file
			$recNum = 	chop(ltrim($tmp[0]));
		else
			array_push( $Exten , chop(ltrim($tmp[0])) );
		
//		$SubStr = substr( $tmp[1] , 0 , strlen($VXML_BASE_PATH) );
//		if( $SubStr == $VXML_BASE_PATH )
		if( strstr( $tmp[1] , "http" ) == "" ) // local file
		{ // local file
//			$recfile = chop(ltrim($tmp[1]));
//			array_push( $Vxml , substr($tmp[1],strlen($VXML_BASE_PATH) ) );
		}
		else
		{ //  http
			array_push( $Vxml , chop($tmp[1]) );
		}
	}
	if( $SubmitAcction != "" )
	{
		if( $SubmitAcction == "-1" )
		{
			array_push( $Exten,"" );
			array_push( $Vxml,"" );
		}
		else
		{
//			echo sizeof($Exten);
			array_splice($Exten , $SubmitAcction , 1 );
			array_splice($Vxml , $SubmitAcction , 1 );
		}
	}
	
	
	
?>




<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>vxml</title>
<style type="text/css">
<!--
.tooltiptitle{COLOR: #FFFFFF; TEXT-DECORATION: none; CURSOR: Default; font-family: 新明細體; font-weight: bold; font-size: 8pt}
.tooltipcontent{COLOR: #000000; TEXT-DECORATION: none; CURSOR: Default; font-family: 新明細體; font-size: 8pt}
#ToolTip{position:absolute; width: 100px; top: 0px; left: 0px; z-index:4; visibility:hidden;}
-->
</style>
<script language = "javascript">
<!--
var ie = document.all ? 1 : 0
var ns = document.layers ? 1 : 0
if(ns){doc = "document."; sty = ""}
if(ie){doc = "document.all."; sty = ".style"}
var initialize = 0
var Ex, Ey, topColor, subColor, ContentInfo
if(ie){
Ex = "event.x"
Ey = "event.y"
topColor = "#660000"
subColor = "#FFCC99"
}

if(ns){
Ex = "e.pageX"
Ey = "e.pageY"
window.captureEvents(Event.MOUSEMOVE)
window.onmousemove=overhere
topColor = "#660000"
subColor = "#FFCC99"
}
function MoveToolTip(layerName, FromTop, FromLeft, e){
if(ie){eval(doc + layerName + sty + ".top = "  + (eval(FromTop) + document.body.scrollTop))}
if(ns){eval(doc + layerName + sty + ".top = "  +  eval(FromTop))}
eval(doc + layerName + sty + ".left = " + (eval(FromLeft) + 15))
}
function ReplaceContent(layerName){
if(ie){document.all[layerName].innerHTML = ContentInfo}
if(ns){
with(document.layers[layerName].document) 
{ 
   open(); 
   write(ContentInfo); 
   close(); 
}
}
}
function Activate(){initialize=1}
function deActivate(){initialize=0}
function overhere(e){
if(initialize){
MoveToolTip("ToolTip", Ey, Ex, e)
eval(doc + "ToolTip" + sty + ".visibility = 'visible'")
}
else{
MoveToolTip("ToolTip", 0, 0)
eval(doc + "ToolTip" + sty + ".visibility = 'hidden'")
}
}
function EnterContent(layerName, TTitle, TContent)
{
ContentInfo = '<table border="0" width="200" cellspacing="0" cellpadding="0">'+
'<tr><td width="100%" bgcolor="#000000">'+
'<table border="0" width="100%" cellspacing="1" cellpadding="0">'+
'<tr><td width="100%" bgcolor='+topColor+'>'+
'<table border="0" width="90%" cellspacing="0" cellpadding="0" align="center">'+
'<tr><td width="100%" align="center">'+
'<font class="tooltiptitle">&nbsp;'+TTitle+'</font>'+
'</td></tr>'+
'</table>'+
'</td></tr>'+
'<tr><td width="100%" bgcolor='+subColor+'>'+
'<table border="0" width="90%" cellpadding="0" cellspacing="1" align="center">'+
'<tr><td width="100%">'+
'<font class="tooltipcontent">'+TContent+'</font>'+
'</td></tr>'+
'</table>'+
'</td></tr>'+
'</table>'+
'</td></tr>'+
'</table>';
ReplaceContent(layerName)
Activate();
}

function Addnew()
{
//	var page = 'voicexml.php?v=-1';   
//	window.location.href = page;

	var tableObj = document.getElementById("mytable");
	
	var len = tableObj.rows.length;
	
    var newRowObj = tableObj.insertRow(len);
	 
    var newExten = newRowObj.insertCell(newRowObj.cells.length);
    var newxmlpath = newRowObj.insertCell(newRowObj.cells.length);
    var newDel = newRowObj.insertCell(newRowObj.cells.length);
	
	newExten.innerHTML = "<input type='text' name='Exten"+len+"' value='' style='background-color:#FFCC99; width :100%'>";
	newxmlpath.innerHTML = "<input type='text' name='xmlpath"+len+"' value='' ondblclick='xml_path("+len+")' style='background-color:#FFCC99; width:100%' />";
	newDel.innerHTML = "<p align='center'><img src='trash.jpg' onClick='BtnDel("+len+")' style='cursor: hand; background-color:#FFCC99;'></p>";

	
}
function BtnDel(msg)
{
//	var page = 'voicexml.php?v=' + msg;   
//	window.location.href = page;
//	document.getElementById("mytable").deleteRow(msg+1); 
	document.getElementById("Exten"+msg).value = "";
	document.getElementById("xmlpath"+msg).value = "";
}
function xml_path( msg )
{
	var page = 'xmlfile.php?v=' + msg;   
//	window.open(page,"","width=400,height=500,scrollbars=yes,resizable=1");	
	window.open(page,'','channelmode=0,height=500, location=0, menubar=0, resizable=1, scrollbars=0, status=0, titlebar=0, toolbar=0, width=400');
}

//-->
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


<body onmousemove="overhere()">
<form name="form" method="POST" enctype="multipart/form-data" action="<? $PHP_SELF ?>">
<p align="left" style=FILTER:Shadow(Color=8888ff,direction=150);height=20 color="#0000FF">&nbsp;&nbsp;<? echo $WORDLIST['VXMLSet'][$LANG] ?></p>

<table width="90%" border="1" align="center">
<tr>
    <th width="20%" scope="col" ><? echo $WORDLIST['recfilenum'][$LANG] ?></th>
	<th width="80%" scope="col"><? echo $WORDLIST['filename'][$LANG] ?></th>
</tr>
<tr>
	 <td><input type='text' name='recNum' value='<? echo "$recNum" ?>' style='background-color:#FFCC99; width :100%'></td>
	 <td><input type='text' name='recfile' value='<? echo "$recfile" ?>' style='background-color:#FFCC99; width :100%'></td>
</tr>
</table>


<p align="center"> 
<input name="btnNew" type="button" value="<? echo $WORDLIST['adddnew'][$LANG] ?>" onClick="javascript:Addnew()" STYLE='background-color:#FF7979'>
</p>
<div id="ToolTip"></div>
<table id="mytable" width="90%" border="1" align="center">
<tr>
    <th width="20%" scope="col" ><? echo $WORDLIST['CalldeeNum'][$LANG] ?></th>
	<th width="73%" scope="col" onMouseover=EnterContent('ToolTip','<? echo $WORDLIST['tip-vxmlfiletitle'][$LANG] ?>','<? echo $WORDLIST['tip-vxmlfile'][$LANG] ?>'); onMouseout=deActivate();>PHP Location </th>
	<th width="7%" scope="col"><? echo $WORDLIST['del'][$LANG] ?></th>
</tr>
<?
	for( $i = 0; $i < sizeof($Exten); $i++ )
	{
		echo "<tr>";
	  	echo "<td><input type='text' name='Exten$i' value='$Exten[$i]' style='background-color:#FFCC99; width :100%'></td>";
		echo "<td><input type='text' name='xmlpath$i' value='$Vxml[$i]'  ondblclick=xml_path($i); style='background-color:#FFCC99; width:100%'></td>";
		echo "<td align='center' ><img src='trash.jpg' onClick='BtnDel($i)' style='cursor: hand; background-color:#FFCC99;'></td>";
		echo "</tr>";
	}
?>	
	
</table>

	

	
<p align="center"> 
<input type='submit' name='Submit' value='<? echo $WORDLIST['submit'][$LANG] ?>'  style='border: 5px dotted #C0C0C0; background-color: #FFD5AA'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="Cancel" value="<? echo $WORDLIST['cancel'][$LANG] ?>" style="border: 5px dotted #C0C0C0; background-color: #FFD5AA" > </td>
</p>
<br><br>

</form>
</body>


</html>
