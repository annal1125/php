﻿<?php
	if($_COOKIE['login'] != "yes")
	{
		header('Location: index.php');
		exit;
	
	}
	
//	 $leavetime = date("YmdHis", "169150188");
	
//	echo "$leavetime<br>";
	
	include "filepath.php";
	include "AstManager.php";
	
	function CleanFW($i)
	{
		delDB( "UFW" , $i );
		delDB( "NFW" , $i );
		delDB( "BFW" , $i );
		delDB( "FWNum" , $i );
	}
	
	$SortField = $_SERVER["QUERY_STRING"];
	
	if( $SortField != "" )
	{
		$parse_temp = explode( "=" , $SortField );
		$SortField = chop($parse_temp[1]);
	}
	
	$VideoArray = array( "disable" => "Disable" ,
		"h263" 	=> "H263" , 
		"h263p" => "H263p" , 
		"mpeg4" => "Mpeg4" , 
		"all"	=> "All" );
	

	if( $SUPPORTG729 == 1 && $SUPPORTG723 == 0 )
	{
		$AudioArray = array(
			"ulaw" 		=> "G711u" ,
			"g729" 		=> "G729" , 
			"ulawg729" 	=> "G711u/G729" );
	}
	else if( $SUPPORTG729 == 0 && $SUPPORTG723 == 1 )
	{
		$AudioArray = array(
			"ulaw" 		=> "G711u" ,
			"g723" 		=> "G723" ,
			"ulawg723" 	=> "G711u/G723" );
	}
	else if( $SUPPORTG729 == 1 && $SUPPORTG723 == 1 )
	{
		$AudioArray = array(
			"ulaw" 			=> "G711u" ,
			"g729" 			=> "G729" , 
			"g723" 			=> "G723" ,
			"ulawg729" 		=> "G711u/G729",
			"ulawg723" 		=> "G711u/G723",
			"g729g723" 		=> "G729/G723" , 
			"ulawg729g723" 	=> "G711u/G729/G723" );
	}
	else
	{
		$AudioArray = array("ulaw" => "G711u");
	}

	$DTMFArray = array("rfc2833" , "info" );
	
	
	Net_AsteriskManager( '127.0.0.1' , '5038' );
	connect();
	login('admin', 'avadesign22221266');	

	
	if(isset($_POST[Submit]))
	{
		unlink($SIP_EX_PATH);
		
		$fp = fopen( $SIP_EX_PATH , "w+");	
		
		for( $i = 0; $i < $MAX_EXTENSION; $i++ )
		{	
			$Account = sprintf("Account%d",$i);
			$Account = $$Account;
			
			if( $Account == "" ) 
			{
				$DelArray = sprintf("DelArray%d",$i);
				$DelArray = $$DelArray;
				
				if( $DelArray != "" ) 
					CleanFW($DelArray);
				continue;
			}
				
			$name = sprintf("name%d",$i);
			$name = $$name;
			
			if( $name == "" )
				$name = $Account;
				
			$AudioCodec = sprintf("AudioCodec%d",$i);
			$AudioCodec = $$AudioCodec;
			
			switch( $AudioCodec )
			{
			case "ulaw":
				$ACodecTemp = "allow=ulaw";
				break;
			case "g729":
				$ACodecTemp = "allow=g729";
				break;
			case "g723":
				$ACodecTemp = "allow=g723";
				break;
			case "ulawg729":
				$ACodecTemp = "allow=ulaw\nallow=g729";
				break;
			case "ulawg723":
				$ACodecTemp = "allow=ulaw\nallow=g723";
				break;
			case "g729g723":
				$ACodecTemp = "allow=ulaw\nallow=g723";
				break;
			case "ulawg729g723":
				$ACodecTemp = "allow=ulaw\nallow=g729\nallow=g723";
				break;
			}
			if( $SUPPORTVIDEO )
			{
				$VCodec = sprintf("VCodec%d",$i);
				$VCodec = $$VCodec;
				
				switch( $VCodec )
				{
				case "disable":
					$VCodecTemp = "videosupport=no";
					break;
				case "h263":
					$VCodecTemp = "videosupport=yes\n";
					$VCodecTemp .= "allow=h263";
					break;
				case "h263p":
					$VCodecTemp = "videosupport=yes\n";
					$VCodecTemp .= "allow=h263p";
					break;
				case "mpeg4":
					$VCodecTemp = "videosupport=yes\n";
					$VCodecTemp .= "allow=mpeg4";
					break;
				case "all":
					$VCodecTemp = "videosupport=yes\n";
					$VCodecTemp .= "allow=h263\n";
					$VCodecTemp .= "allow=h263p\n";
					$VCodecTemp .= "allow=mpeg4\n";
					$VCodecTemp .= "allow=h264";
					break;
				}
			}
			
			$Password = sprintf("Password%d",$i);
			$Password = $$Password;
			
			$StrTotal = "[$Account]\n";
			$StrTotal .= "username=$name\n";
			$StrTotal .= "secret=$Password\n";
			$StrTotal .= "disallow=all\n";
			$StrTotal .= "dtmfmode=rfc2833\n";
			$StrTotal .= "host=dynamic\n";
			$StrTotal .= "type=friend\n";
			$StrTotal .= "context=ava_media\n";
			$StrTotal .= "nat=yes\n";
			$StrTotal .= "qualify=yes\n";
			$StrTotal .= "canreinvite=no\n";
			$StrTotal .= "$ACodecTemp\n";
			if( $SUPPORTVIDEO )
				$StrTotal .= "$VCodecTemp\n";
			$StrTotal .= "\n";
					
			fwrite( $fp , $StrTotal );
			
			
			// set forward	
			$checkAllFW = sprintf("checkAllFW%d",$i);
			$checkAllFW = $$checkAllFW;
			$checkNoAnsFW = sprintf("checkNoAnsFW%d",$i);
			$checkNoAnsFW = $$checkNoAnsFW;
			$checkBusyFW = sprintf("checkBusyFW%d",$i);
			$checkBusyFW = $$checkBusyFW;
			$FW = sprintf("FW%d",$i);
			$FW = $$FW;
			
			if( $checkAllFW == "on" && $FW != "" )
				putDB( "UFW" , $Account , "1" );
			else
				delDB( "UFW" , $Account );
			
			if( $checkNoAnsFW == "on" && $FW != "" )
				putDB( "NFW" , $Account , "1" );
			else
				delDB( "NFW" , $Account );

			if( $checkBusyFW == "on" && $FW != "" )
				putDB( "BFW" , $Account , "1" );
			else
				delDB( "BFW" , $Account );
			
			if( $FW == "" )
				delDB( "FWNum" , $Account );
			else
				putDB( "FWNum" , $Account , $FW );
			
			
		}
		fclose( $fp );
		
		shell_exec( "asterisk -rx reload" );
	}
	// set para empty
	$VCodec = "";
	$ch_name = "";
	$ch_Pwd = "";
	$ch_account = "";
	$ch_DTMF = "";
	$ch_Acodec = "";
	

	$content_array = file( $SIP_EX_PATH );
	$i = 0;
	$j = -1;

	
	while( $content_array[$i] != "" )
	{
//		echo "$content_array[$i]<br>";

		if( strstr($content_array[$i] , "[" ) != "" )
		{
			$j++;
			$parse_temp = explode( "[" , $content_array[$i] );	
			$parse_temp1 = explode( "]" , $parse_temp[1] );	
			$ch_account[$j] = chop($parse_temp1[0]);
			$i++;
			continue;
		}
		if( $j < 0  ) 
		{
			$i++;
			continue;
		}
		$parse_temp = explode( "=" , $content_array[$i] );	
		
		switch( $parse_temp[0] )
		{
		case "username":
			$ch_name[$j] = chop($parse_temp[1]);
			break;
		case "secret":
			$ch_Pwd[$j] = chop($parse_temp[1]);
			break;
		case "dtmfmode":
			$ch_DTMF[$j] = chop($parse_temp[1]);
			break;
		case "allow":
			if( strstr( $parse_temp[1] , "ulaw" ) != "" ||
				strstr( $parse_temp[1] , "g729" ) != "" ||
				strstr( $parse_temp[1] , "g723" ) != "" )
			{ // Audio codec 
				$ch_Acodec[$j] .= chop($parse_temp[1]);
			}
			else if( strstr( $parse_temp[1] , "h263" ) != "" ||
				strstr( $parse_temp[1] , "h263p" ) != "" ||
				strstr( $parse_temp[1] , "mpeg4" ) != "" )
			{
				if( $VCodec[$j] == "" )
					$VCodec[$j] = chop($parse_temp[1]);
				else if( $VCodec[$j] != "disable" )
					$VCodec[$j] = "all";
			}
			break;
		case "videosupport":
			$tmp = chop($parse_temp[1]);
			if( $tmp == "no" )
				$VCodec[$j] = "disable";
			break;
		}				
		$i++;
	}	
	
	
	$response = getDB();
	
	$tok = strtok($response,"\r\n");
	$i = 0;		
	$BusyFWKey = "/BFW/";
	$NoAnsFWKey = "/NFW/";
	$AllFWKey = "/UFW/";
	$NumFWKey = "/FWNum/";
	
	$FW = "";
	$BFWArr = "";
	$NFWArr = "";
	$AFWArr = "";
	$FWNumWArr = "";
	
	while($tok) 
	{	// get forward data		
		$TokenKey = "";
		if( strstr( $tok , $BusyFWKey ) != "" )
			$TokenKey = $BusyFWKey;
		else if( strstr( $tok , $NoAnsFWKey ) != "" )
			$TokenKey = $NoAnsFWKey;
		else if( strstr( $tok , $AllFWKey ) != "" )
			$TokenKey = $AllFWKey;
		else if( strstr( $tok , $NumFWKey ) != "" )
			$TokenKey = $NumFWKey;
			
	
		if( $TokenKey  != "" )
		{	
//			echo "$tok<br>";
			$Str1 = explode(  ":" , $tok );
			
			$Str1[1] = ltrim($Str1[1]);
			$Str1[1] = chop($Str1[1]);
			
			if( $Str1[1] != "" )
			{

				$CallerNum = chop(substr( $Str1[0] , strlen( $TokenKey ) ) );
				
				switch( $TokenKey )
				{
				case $BusyFWKey: $BFWArr[$CallerNum] = $Str1[1]; break;
				case $NoAnsFWKey: $NFWArr[$CallerNum] = $Str1[1]; break;
				case $AllFWKey: $AFWArr[$CallerNum] = $Str1[1]; break;
				case $NumFWKey: $FWNumWArr[$CallerNum] = $Str1[1]; break;
				}
			}
		}
		$tok = strtok("\r\n");
	}
	
	
	$i = 0;
	$tmpPwd = "";
	$tmpName = "";
	$tmpVCodec = "";
	$tmpACodec = "";
	$tmpAccount = "";
	
	$BtnSel = array( 1,2,3,4,5 );
	switch( $SortField )
	{
	case 1: // name 
	case -1:
		if( $SortField == 1 )
			asort($ch_name );
		else
			arsort($ch_name );
		
		while(list($key , $val) = each($ch_name)) 
		{
			$tmpAccount[$i] = $ch_account[$key];
			$tmpPwd[$i] = $ch_Pwd[$key];
			$tmpVCodec[$i] = $VCodec[$key];
			$tmpACodec[$i] = $ch_Acodec[$key];
			$i++;
		}
		for( $i = 0; $i < sizeof($ch_name); $i++ )
		{
			$ch_account[$i] = $tmpAccount[$i];
			$ch_Pwd[$i] = $tmpPwd[$i];
			$VCodec[$i] = $tmpVCodec[$i];
			$ch_Acodec[$i] = $tmpACodec[$i];
		}
		if( $SortField == 1 )
		{
			$BtnSel[0] = -1;
			sort($ch_name );
		}
		else
		{
			$BtnSel[0] = 1;
			rsort($ch_name );
		}
	break;
	case 2: // account
	case -2:
		if( $SortField == 2 )
			asort($ch_account );
		else
			arsort($ch_account );
		while(list($key , $val) = each($ch_account)) 
		{
			$tmpName[$i] = $ch_name[$key];
			$tmpPwd[$i] = $ch_Pwd[$key];
			$tmpVCodec[$i] = $VCodec[$key];
			$tmpACodec[$i] = $ch_Acodec[$key];
			$i++;
		}
		for( $i = 0; $i < sizeof($ch_account); $i++ )
		{
			$ch_name[$i] = $tmpName[$i];
			$ch_Pwd[$i] = $tmpPwd[$i];
			$VCodec[$i] = $tmpVCodec[$i];
			$ch_Acodec[$i] = $tmpACodec[$i];
		}
		if( $SortField == 2 )
		{
			$BtnSel[1] = -2;
			sort($ch_account );
		}
		else
		{
			$BtnSel[1] = 2;
			rsort($ch_account );
		}
	break;
	case 3: // pwd
	case -3:
		if( $SortField == 3 )
			asort($ch_Pwd );
		else
			arsort($ch_Pwd );

		while(list($key , $val) = each($ch_Pwd)) 
		{
			$tmpName[$i] = $ch_name[$key];
			$tmpAccount[$i] = $ch_account[$key];
			$tmpVCodec[$i] = $VCodec[$key];
			$tmpACodec[$i] = $ch_Acodec[$key];
			$i++;
		}
		for( $i = 0; $i < sizeof($ch_Pwd); $i++ )
		{
			$ch_name[$i] = $tmpName[$i];
			$ch_account[$i] = $tmpAccount[$i];
			$VCodec[$i] = $tmpVCodec[$i];
			$ch_Acodec[$i] = $tmpACodec[$i];
		}
		if( $SortField == 3 )
		{
			$BtnSel[2] = -3;
			sort($ch_Pwd );
		}
		else
		{
			$BtnSel[2] = 3;
			rsort($ch_Pwd );
		}
	break;
	case 4: // Vcodec
	case -4:
		if( $SortField == 4 )
			asort($VCodec );
		else
			arsort($VCodec );

		while(list($key , $val) = each($VCodec)) 
		{
			$tmpName[$i] = $ch_name[$key];
			$tmpAccount[$i] = $ch_account[$key];
			$tmpPwd[$i] = $ch_Pwd[$key];
			$tmpACodec[$i] = $ch_Acodec[$key];
			$i++;
		}
		for( $i = 0; $i < sizeof($VCodec); $i++ )
		{
			$ch_name[$i] = $tmpName[$i];
			$ch_account[$i] = $tmpAccount[$i];
			$ch_Pwd[$i] = $tmpPwd[$i];
			$ch_Acodec[$i] = $tmpACodec[$i];
		}
		if( $SortField == 4 )
		{
			$BtnSel[3] = -4;
			sort($VCodec );
		}
		else
		{
			$BtnSel[3] = 4;
			rsort($VCodec );
		}
	break;
	case 5: // Acodec
	case -5:
		if( $SortField == 5 )
			asort($ch_Acodec );
		else
			arsort($ch_Acodec );

		while(list($key , $val) = each($ch_Acodec)) 
		{
			$tmpName[$i] = $ch_name[$key];
			$tmpAccount[$i] = $ch_account[$key];
			$tmpPwd[$i] = $ch_Pwd[$key];
			$tmpVCodec[$i] = $VCodec[$key];
			$i++;
		}
		for( $i = 0; $i < sizeof($ch_Acodec); $i++ )
		{
			$ch_name[$i] = $tmpName[$i];
			$ch_account[$i] = $tmpAccount[$i];
			$ch_Pwd[$i] = $tmpPwd[$i];
			$VCodec[$i] = $tmpVCodec[$i];
		}
		if( $SortField == 5 )
		{
			$BtnSel[4] = -5;
			sort($ch_Acodec );
		}
		else
		{
			$BtnSel[4] = 5;
			rsort($ch_Acodec );
		}
	break;
	}
	
	
	$checkBusyFW = "";
	$checkNoAnsFW = "";
	$checkAllFW = "";
	$FW = "";
	
	for( $i = 0; $i < sizeof($ch_account); $i++ )
	{
		reset($BFWArr);
		while(list($key , $val) = each($BFWArr)) 
		{
			if( $ch_account[$i] == $key )
			{
				$checkBusyFW[$i] = 1;
				break;
			}
		}		
		reset($NFWArr);
		while(list($key , $val) = each($NFWArr)) 
		{
			if( $ch_account[$i] == $key )
			{
				$checkNoAnsFW[$i] = 1;
				break;
			}
		}		
		reset($AFWArr);
		while(list($key , $val) = each($AFWArr)) 
		{
			if( $ch_account[$i] == $key )
			{
				$checkAllFW[$i] = 1;
				break;
			}
		}		
		reset($FWNumWArr);
		while(list($key , $val) = each($FWNumWArr)) 
		{
			if( $ch_account[$i] == $key )
			{
				$FW[$i] = $val;
				break;
			}
		}		
	}
	
	
	logout();
	
	
	


function SetDTMF($dtmf)
{
	global $DTMFArray;
	$i = 0;
	if( $dtmf != "" )
		echo "<option value=$dtmf>$dtmf</option>";
		
	while( $DTMFArray[$i] != "" )
	{
		if( $dtmf != $DTMFArray[$i] )
			echo "<option value=$DTMFArray[$i]>$DTMFArray[$i]</option>";
		$i++;
	}
}	

function ShowVCodec($VCodecStr)
{
	global $VideoArray;
	reset( $VideoArray ); 

	if( $VCodecStr != "" )
		echo "<option value=$VCodecStr>$VideoArray[$VCodecStr]</option>";

	while( list($key , $val) = each($VideoArray) )
	{
		if( $key == $VCodecStr )
			continue;
		echo "<option value=$key>$val</option>";
	}
}

function ShowACodec($ACodecStr)
{
	global $AudioArray;
	reset( $AudioArray ); 
	
	if( $ACodecStr != "" )
		echo "<option value=$ACodecStr>$AudioArray[$ACodecStr]</option>";

	while( list($key , $val) = each($AudioArray) )
	{
		if( $key == $ACodecStr )
			continue;
		echo "<option value=$key>$val</option>";
	}

}



?>



<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>SIP</title>

<script type="text/JavaScript">
<!--
function OMOver(OMO){OMO.style.backgroundColor='<? echo $MainFieldOverColor ?>';}
function OMOut(OMO){OMO.style.backgroundColor='<? echo $MainFieldBackGround ?>';}


function clickCheck(fieldname , msg)
{	
	var fgVMS = document.getElementById("checkVMS"+msg);
	var fgAllFW = document.getElementById("checkAllFW"+msg);
	var fgNoAnsFW = document.getElementById("checkNoAnsFW"+msg);
	var fgBusyFW = document.getElementById("checkBusyFW"+msg);
	var fgFW = document.getElementById("FW"+msg);
	
	switch( fieldname )
	{
	case "VMS": // click VMS
		if( fgVMS.checked )
		{ // disable all item
			fgAllFW.checked = false;
			fgNoAnsFW.checked = false;
			fgBusyFW.checked = false;
			fgFW.checked = false;		
			
			fgAllFW.disabled = true;
			fgNoAnsFW.disabled = true;
			fgBusyFW.disabled = true;
			fgFW.disabled = true;		
		}
		else
		{ // enable all item
			fgAllFW.disabled = false;
			fgNoAnsFW.disabled = false;
			fgBusyFW.disabled = false;
			fgFW.disabled = false;		
		}
		break;
	case "AllFW":
		if( fgAllFW.checked )
		{
			fgVMS.checked = false;
			fgVMS.disabled = true;
			fgNoAnsFW.checked = false;
			fgNoAnsFW.disabled = true;
			fgBusyFW.checked = false;
			fgBusyFW.disabled = true;
		}
		else
		{
			fgNoAnsFW.disabled = false;
			fgBusyFW.disabled = false;
			fgVMS.disabled = false;
		}
		break;
	case "NoAnsFW":
	case "BusyFW":
		if( fgAllFW.checked || fgNoAnsFW.checked || fgBusyFW.checked )
		{
			fgVMS.checked = false;
			fgVMS.disabled = true;
		}
		else
		{
			fgVMS.disabled = false;
		}
		break;
	}
}
function SortBtn(msg) 
{
	FORM=document.forms[0];
	
    //document.all("sc").innerHTML="<iframe src=\"extension.php?v="+ msg +"\" frameborder=0 width=0 height=0></iframe>";
        //alert("<iframe src=\"list.php?id="+a+"\" frameborder=0 width=0 height=0></iframe>");
	
	var page = 'extension.php?v=' + msg;   
	window.location.href = page;
/*	
	var StrField;
	
	switch( msg )
	{
	case 0: // Alias
		StrField = "name"; break;
	case 1: // Account
		StrField = "Account"; break;
	case 2: // pwd
		StrField = "Password"; break;
	case 3: // VCodec
		StrField = "VCodec"; break;
	case 4: // AudioCodec
		StrField = "AudioCodec"; break;
	default: return;
	}
	
	var i = 0;
	var arr = [];
	var fg = document.getElementById(StrField+i);

	while( fg.value && i < 100 )
	{
		arr[i] = fg.value;
		i++;
		fg = document.getElementById(StrField+i);
	}
	arr.sort(); 
	arr.reverse();	
	for( i = 0; i <  arr.length; i++ )
	{
		fg = document.getElementById(StrField + i);
		fg.value = 	arr[i];
	}
*/

}

//-->


</script>
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
topColor = "<? echo $MainEditBackGround ?>"
subColor = "<? echo $MainEditBackGround1 ?>"
}

if(ns){
Ex = "e.pageX"
Ey = "e.pageY"
window.captureEvents(Event.MOUSEMOVE)
window.onmousemove=overhere
//topColor = "#660000"
//subColor = "#FFCC99"
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
ContentInfo = '<table border="0" width="150" cellspacing="0" cellpadding="0">'+
'<tr><td width="100%" bgcolor="#000000">'+
'<table border="0" width="100%" cellspacing="1" cellpadding="0">'+
'<tr><td width="100%" bgcolor='+topColor+'>'+
'<table border="0" width="90%" cellspacing="0" cellpadding="0" align="center">'+
'<tr><td width="100%">'+
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
	
	var maxlen =  <? echo $MAX_EXTENSION; ?>;
	if( len > maxlen )
		return;
	
    var newRowObj = tableObj.insertRow(len);

	newRowObj.insertCell(newRowObj.cells.length).innerHTML = "<input type='text' name='name"+len+"' value='' style='background-color:#A8E61D; width :100%'>";
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = "<input type='text' name='Account"+len+"' value='' style='background-color:#A8E61D; width:100%'>";
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = "<input type='text' name='Password"+len+"' value='' style='background-color:#A8E61D; width:100%'>";
	
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = "<? if( $SUPPORTVIDEO ) { echo '<select name=\'VCodec$i\' STYLE=\'background-color:#A8E61D;width :100%\' >'; ShowVCodec(''); echo '</select>'; } ?>";
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = "<? if( $SUPPORTVIDEO ) { echo '<select name=\'AudioCodec$i\' STYLE=\'background-color:#A8E61D;width :100%\' >'; ShowACodec(''); echo '</select>'; } ?>";

	newRowObj.insertCell(newRowObj.cells.length).innerHTML = 
	"<input type='checkbox' name='checkVMS" + len + "' onMouseover=EnterContent('ToolTip','" +
	"<? echo $WORDLIST['tip-voicemail'][$LANG]; ?>" + "','" + "<? echo $WORDLIST['tip-Intovoicemail'][$LANG]; ?>" + "'); " +
	"onMouseout=deActivate() onClick=clickCheck('VMS','" + len + "')>" +

	"<input type='checkbox' name='checkAllFW" + len + "' onMouseover=EnterContent('ToolTip','" +
	"<? echo $WORDLIST['tip-allfwtitle'][$LANG]; ?>" + "','" + "<? echo $WORDLIST['tip-allfw'][$LANG]; ?>" + "'); " +
	"onMouseout=deActivate() onClick=clickCheck('AllFW','" + len + "')>" +

	"<input type='checkbox' name='checkNoAnsFW" + len + "' onMouseover=EnterContent('ToolTip','" +
	"<? echo $WORDLIST['tip-nofwtitle'][$LANG]; ?>" + "','" + "<? echo $WORDLIST['tip-nofw'][$LANG]; ?>" + "'); " +
	"onMouseout=deActivate() onClick=clickCheck('NoAnsFW','" + len + "')>" +
	
	"<input type='checkbox' name='checkNoAnsFW" + len + "' onMouseover=EnterContent('ToolTip','" +
	"<? echo $WORDLIST['tip-busyfwtitle'][$LANG]; ?>" + "','" + "<? echo $WORDLIST['tip-busyfw'][$LANG]; ?>" + "'); " +
	"onMouseout=deActivate() onClick=clickCheck('BusyFW','" + len + "')>" +
		
	"&nbsp;&nbsp;" +
	"<input type='text' name='FW" + len + "' value='' style='background-color:#A8E61D; width:65%' onMouseover=EnterContent('ToolTip','" +
	"<? echo $WORDLIST['tip-fwNumtitle'][$LANG]; ?>" + "','" + "<? echo $WORDLIST['tip-fwNum'][$LANG]; ?>" + "'); " +
	"onMouseout=deActivate()>" +

	"&nbsp;&nbsp;" +
	"<img src='trash.jpg' onClick='BtnDel(" + len + ")' style='cursor: hand; background-color:#A8E61D;' />";
}
function BtnDel(msg)
{
	document.getElementById("DelArray"+msg).value = document.getElementById("Account"+msg).value;

	document.getElementById("name"+msg).value = "";
	document.getElementById("Account"+msg).value = "";
	document.getElementById("Password"+msg).value = "";
	
	document.getElementById("checkVMS"+msg).checked = false;
	document.getElementById("checkAllFW"+msg).checked = false;
	document.getElementById("checkNoAnsFW"+msg).checked = false;
	document.getElementById("checkBusyFW"+msg).checked = false;		
	document.getElementById("FW"+msg).value = "";


	
}

//-->
</script> 



<style type="text/css">
<!--
body {
        background-color: <? echo $MainBackGroundColor ?>;
}
body,td,th {
	color: #FFFFFF;
}
.style1 {font-family: Arial, Helvetica, sans-serif}
.style3 {font-family: "Courier New", Courier, monospace}
-->



</style></head>

<body onmousemove="overhere()" ONDRAGSTART="window.event.returnValue=false" ONCONTEXTMENU="window.event.returnValue=false"  >

<form name="form" method="POST" enctype="multipart/form-data" action="<? $PHP_SELF ?>">


<?
	echo "<div align='left' style=$MainTitleStyle color='$MainTitleTextColor'>&nbsp;&nbsp;";
	echo $WORDLIST['extensionset'][$LANG];
	echo "</div>";

?>
<div id="ToolTip"></div>

<div align="center"><input name="btnNew" type="button" value="<? echo $WORDLIST['adddnew'][$LANG] ?>" onClick="javascript:Addnew()" STYLE='background-color:#FF7979'></div>



<table id="mytable" width="100%" border="1" align="center">

<?php

	if( $SUPPORTVIDEO )
		$widthSize = "20%";
	else
		$widthSize = "25%";	
		
	echo "<tr>";
    echo "<th width='14%' font style='cursor: hand' scope='row' style='$MainFieldStyle padding:5px' onmouseover=OMOver(this); onmouseout=OMOut(this); onClick='javascript:SortBtn($BtnSel[0])'>";
	echo $WORDLIST['ex-name'][$LANG];
	echo "</font></th>";
	
    echo "<th width='14%' font style='cursor: hand' scope='row' style='$MainFieldStyle padding:5px' onmouseover=OMOver(this); onmouseout=OMOut(this); onClick='javascript:SortBtn($BtnSel[1])'>";
	echo $WORDLIST['Account-num'][$LANG];
	echo "</font></th>";
	
    echo "<th width='14%' font style='cursor: hand' scope='row' style='$MainFieldStyle padding:5px' onmouseover=OMOver(this); onmouseout=OMOut(this); onClick='javascript:SortBtn($BtnSel[2])'>";
	echo $WORDLIST['Password'][$LANG];
	echo "</font></th>";
	
//    <td width="20%"><div align="center"><font style="filter: glow(color=#9966FF,strength=3); height:10px; color:white; padding:5px">DTMF格式</font></div></td>
 	if( $SUPPORTVIDEO )
	{
		echo "<th width='10%' font style='cursor: hand' scope='row' style='$MainFieldStyle padding:5px' onmouseover=OMOver(this); onmouseout=OMOut(this); onClick='javascript:SortBtn($BtnSel[3])'>";
		echo $WORDLIST['vcodec'][$LANG];
		echo "</font></th>";
	}

    echo "<th width='15%' font style='cursor: hand' scope='row' style='$MainFieldStyle padding:5px' onmouseover=OMOver(this); onmouseout=OMOut(this); onClick='javascript:SortBtn($BtnSel[4])'>";
	echo $WORDLIST['acodec'][$LANG];
	echo "</font></th>";
	
	
    echo "<th width='33%' align='left' scope='row' style='$MainFieldStyle padding:5px'>M&nbsp;&nbsp;A&nbsp;&nbsp;N&nbsp;&nbsp;B&nbsp;&nbsp;";
	echo $WORDLIST['mailforward'][$LANG];
	echo "</font></th>";
	
	
	echo "</tr>";
	

	$Color1 = $MainEditBackGround;
	$Color2 = $MainEditBackGround1;
	

	for( $i = 0; $i < sizeof($ch_name); $i++ )
	{
		if( ($i % 2 ) == 0 )
			$FieldColor = $Color1;
		else
			$FieldColor = $Color2;
		
		echo "<tr>";
 		echo "<td><input name='name$i' type='text' value='$ch_name[$i]' style='background-color:$FieldColor; width: 100%'></td>";
 		echo "<td><input name='Account$i' type='text' value='$ch_account[$i]' style='background-color:$FieldColor; width: 100%'></td>";
 		echo "<td><input name='Password$i' type='text' value='$ch_Pwd[$i]' style='background-color:$FieldColor; width: 100%'></td>";
//		echo "<td><select name='DTMF$i' STYLE='background-color:$FieldColor;width :100%' >";
//		SetDTMF($ch_DTMF[$i]);
//		echo "</select></td>";

		if( $SUPPORTVIDEO )
		{
			echo "<td><select name='VCodec$i' STYLE='background-color:$FieldColor;width :100%' >";
			ShowVCodec($VCodec[$i]);
			echo "</select></td>";
		} 
		echo "<td><select name='AudioCodec$i' STYLE='background-color:$FieldColor;width :100%' >";
		ShowACodec($ch_Acodec[$i]);
		echo "</select></td>";
		
		
		echo "<td>";
		

		echo "<input type=checkbox name='checkVMS$i' onMouseover=EnterContent('ToolTip','";
		echo $WORDLIST['tip-voicemail'][$LANG];
		echo "','";
		echo $WORDLIST['tip-Intovoicemail'][$LANG];
		echo "'); onMouseout=deActivate() onClick=clickCheck('VMS',$i)>";


		if( $checkAllFW[$i] ) $StrCheck = "checked='CHECKED'";
		else $StrCheck = "";
		echo "<input type=checkbox name='checkAllFW$i' $StrCheck onMouseover=EnterContent('ToolTip','";
		echo $WORDLIST['tip-allfwtitle'][$LANG];
		echo "','";
		echo $WORDLIST['tip-allfw'][$LANG];
		echo "'); onMouseout=deActivate() onClick=clickCheck('AllFW',$i)>"; 


		
		if( $checkNoAnsFW[$i] ) $StrCheck = "checked='CHECKED'";
		else $StrCheck = "";
		echo "<input type=checkbox name='checkNoAnsFW$i' $StrCheck onMouseover=EnterContent('ToolTip','";
		echo $WORDLIST['tip-nofwtitle'][$LANG];
		echo "','";
		echo $WORDLIST['tip-nofw'][$LANG];
		echo "'); onMouseout=deActivate() onClick=clickCheck('NoAnsFW',$i)>"; 
		
		if( $checkBusyFW[$i] ) $StrCheck = "checked='CHECKED'";
		else $StrCheck = "";
		echo "<input type=checkbox name='checkBusyFW$i' $StrCheck onMouseover=EnterContent('ToolTip','";
		echo $WORDLIST['tip-busyfwtitle'][$LANG];
		echo "','";
		echo $WORDLIST['tip-busyfw'][$LANG];
		echo "'); onMouseout=deActivate() onClick=clickCheck('BusyFW',$i)>"; 
 		
		echo "&nbsp;&nbsp;";
		echo "<input name='FW$i' type='text' value='$FW[$i]' style='background-color:$FieldColor; width :65%'onMouseover=EnterContent('ToolTip','";
		echo $WORDLIST['tip-fwNumtitle'][$LANG];
		echo "','";
		echo $WORDLIST['tip-fwNum'][$LANG];
		echo "'); onMouseout=deActivate()>";
		
		
		echo "&nbsp;&nbsp;";
		echo "<img src='trash.jpg' onClick='BtnDel($i)' style='cursor: hand; background-color:#A8E61D;' />";
		
		
		echo "</td></tr>";
		
		echo "<input name='DelArray$i' type='hidden' value=''>";	

		
	}

?>
</table>






<p align="center"> 
<input type='submit' name='Submit' value="<? echo $WORDLIST['submit'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="Cancel" value="<? echo $WORDLIST['cancel'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> </td>
</p>
<br><br>
</form>
</body>



</html>
