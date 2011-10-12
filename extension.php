<?php
	if($_COOKIE['login'] != "yes")
	{
		header('Location: index.php');
		exit;
	
	}
	
	include "filepath.php";
	include "AstManager.php";
	
	if( !file_exists( $GROUP_ALIAS ) )
	{
		$fp = fopen( $GROUP_ALIAS , "w+");
		fwrite( $fp , "0=\n" );
		
		for( $i = 1; $i <=9; $i++ )
		{
			fwrite( $fp , "$i=$i\n" );
		}
		fclose( $fp );	
	}
	
	
	$SortField = $_SERVER["QUERY_STRING"];
	
	if( $SortField != "" )
	{
		$parse_temp = explode( "=" , $SortField );
		$SortField = chop($parse_temp[1]);
	}

	$content_array = file( $GROUP_ALIAS );
	while( list($key , $val) = each($content_array) )
	{	
		$tmp = explode( "=" , $val );
		$tmp[0] = trim($tmp[0]);
		$tmp[1] = trim($tmp[1]);
		
		$GROUP_NAME[$tmp[0]] = $tmp[1];
	}	




	$DTMFArray = array("rfc2833" , "info" );
	
	
	Net_AsteriskManager( '127.0.0.1' , '5038' );
	connect();
	login('admin', 'avadesign22221266');	

	
	if(isset($_POST[Submit]))
	{
//		unlink($SIP_EX_PATH);
		$fp = fopen( $SIP_EX_PATH , "w+");	
		for( $i = 0; $i < $MAX_EXTENSION; $i++ )
		{	
			$Account = sprintf("Account%d",$i);
			$Account = $$Account;
			
			if( $Account == "" )
				continue;
				
			$name = sprintf("name%d",$i);
			$name = $$name;
			
			if( $name == "" )
				$name = $Account;
				
			$Password = sprintf("Password%d",$i);
			$Password = $$Password;				
			
			$AudioCodec = sprintf("ch_Acodec%d",$i);
			$AudioCodec = $$AudioCodec;


			$ACodecTemp = null;
			if( $AudioCodec == null )
			{
				reset( $AudioArray );
				while(list($key , $val) = each($AudioArray)) 
					$ACodecTemp .= "allow=$key\n";
			}
			else
			{
				$parse_temp = explode( "/" , $AudioCodec );	
				reset( $parse_temp );
				while(list($key , $val) = each($parse_temp)) 
					$ACodecTemp .= "allow=$val\n";
			}
			
			
			$VideoCodec = sprintf("ch_Vcodec%d",$i);
			$VideoCodec = $$VideoCodec;
			$parse_temp = explode( "/" , $VideoCodec );	
				
			$VCodecTemp = null;
			
			if( $VideoCodec == null )
			{
				reset( $VideoArray );
				while(list($key , $val) = each($VideoArray)) 
					$VCodecTemp .= "allow=$key\n";			
			}
			else
			{
				$parse_temp = explode( "/" , $VideoCodec );	
				reset( $parse_temp );
				while(list($key , $val) = each($parse_temp)) 
					$VCodecTemp .= "allow=$val\n";			
			}
			
			$group = sprintf("group%d",$i);
			$group = $$group;
			if( $group  == 0 ) $group = null;
			$ch_pickup = sprintf("ch_pickup%d",$i);
			$ch_pickup = $$ch_pickup;
			$nat = sprintf("nat%d",$i);
			$nat = $$nat;
			
			
			$StrTotal = "[$Account]\n";
			$StrTotal .= "username=$name\n";
			$StrTotal .= "secret=$Password\n";
			$StrTotal .= "disallow=all\n";
			$StrTotal .= "dtmfmode=rfc2833\n";
			$StrTotal .= "host=dynamic\n";
			$StrTotal .= "type=friend\n";
			$StrTotal .= "context=ava_media\n";
			$StrTotal .= "call-limit=1\n";
			$StrTotal .= "nat=$nat\n";
			$StrTotal .= "qualify=yes\n";
			$StrTotal .= "canreinvite=no\n";
			$StrTotal .= "videosupport=yes\n";
			$StrTotal .= "$ACodecTemp";
			$StrTotal .= "$VCodecTemp";
			$StrTotal .= "callgroup=$group\n";
			$StrTotal .= "pickupgroup=$ch_pickup\n";
			$StrTotal .= ";------End of $Account ---------\n";
					
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
	$ch_Vcodec = "";
	$ch_group_pickup = "";
	$ch_group = "";
	

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
			$ch_name[$j] = trim($parse_temp[1]);
			break;
		case "secret":
			$ch_Pwd[$j] = trim($parse_temp[1]);
			break;
		case "dtmfmode":
			$ch_DTMF[$j] = trim($parse_temp[1]);
			break;
		case "callgroup":
			$ch_group[$j] = trim($parse_temp[1]);
			break;
		case "pickupgroup":
			$ch_pickup[$j] = trim($parse_temp[1]);
			break;
		case "nat":
			$ch_nat[$j] = trim($parse_temp[1]);
			break;
		case "allow":
			$parse_temp[1] = trim($parse_temp[1]);
			$bAudio = false;
//			echo "$ch_name[$j]:$parse_temp[1]<br>";
			reset( $AudioArray );
			while(list($key , $val) = each($AudioArray)) 
			{
				if( !strcasecmp( $parse_temp[1] , $key)  )
				{
					if( $ch_Acodec[$j] == null ) 
						$ch_Acodec[$j] .= $key;
					else 
						$ch_Acodec[$j] .= ("/" . $key);
					$bAudio = true;
					break;					
				}
			}
			if( !$bAudio )
			{
				reset( $VideoArray );
				while(list($key , $val) = each($VideoArray)) 
				{
					if( !strcasecmp( $parse_temp[1] , $key)  )
					{
						if( $ch_Vcodec[$j] == null ) 
							$ch_Vcodec[$j] .= $key;
						else 
							$ch_Vcodec[$j] .= ("/" . $key);
						break;					
					}
				}
			}
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
	
	

function ShowGroup($Str)
{
	global $GROUP_NAME;
	reset($GROUP_NAME);
	
	echo "<option value=$Str>$GROUP_NAME[$Str]</option>";

	
	while( list($key , $val) = each($GROUP_NAME) )
	{
		if( $val == null )
			$val = $key;
		if( $val != "0" )
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
//	var fgVMS = document.getElementById("checkVMS"+msg);
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
//			fgVMS.checked = false;
//			fgVMS.disabled = true;
			fgNoAnsFW.checked = false;
			fgNoAnsFW.disabled = true;
			fgBusyFW.checked = false;
			fgBusyFW.disabled = true;
		}
		else
		{
			fgNoAnsFW.disabled = false;
			fgBusyFW.disabled = false;
//			fgVMS.disabled = false;
		}
		break;
	case "NoAnsFW":
	case "BusyFW":
		if( fgAllFW.checked || fgNoAnsFW.checked || fgBusyFW.checked )
		{
//			fgVMS.checked = false;
//			fgVMS.disabled = true;
		}
		else
		{
//			fgVMS.disabled = false;
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
	var length = tableObj.rows.length;


	var maxlen =  <? echo $MAX_EXTENSION; ?>;
	if( length > maxlen )
		return;
	
    var newRowObj = tableObj.insertRow(length);
	var len = length -1;
	
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = "<div align='center'>"+length+"</div>";
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = "<input type='text' name='name"+len+"' value='' style='background-color:#A8E61D; width :100%'>";
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = "<input type='text' name='Account"+len+"' value='' style='background-color:#A8E61D; width:100%'>";
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = "<input type='text' name='Password"+len+"' value='' style='background-color:#A8E61D; width:100%'>";
	
	
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = "<select name='nat"+len+"' STYLE='background-color:#A8E61D;width :100%';>" +
	"<option value=yes>YES</option><option value=no>NO</option>";
	
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = "<select name='group"+len+"' STYLE='background-color:#A8E61D;width :100%';>" +
	"<? ShowGroup(''); ?>" + "</select></td>";
	
	
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = 
	"<input name='edit02' align='center' type='button' onClick='javascript:btn_group_pickup(\""+len+",\");' value='<? echo $WORDLIST['modify'][$LANG]; ?>'" +
	 " style='<? echo $MainBtnStyle ?>; width: 100%; cursor: hand;'>";
	
	
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = 
	"<input name='edit0[]' align='center' type='button' onClick='javascript:selectbtn(\"video,"+len+",\");' value='<? echo $WORDLIST['modify'][$LANG]; ?>'" +
	 " style='<? echo $MainBtnStyle ?>; width: 100%; cursor: hand;'>";
	 	 
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = 
	"<input name='edit1[]' align='center' type='button' onClick='javascript:selectbtn(\"audio,"+len+",\");' value='<? echo $WORDLIST['modify'][$LANG]; ?>'" +
	 " style='<? echo $MainBtnStyle ?>; width: 100%; cursor: hand;'>";
	 
	 
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = 
//	"<input type='checkbox' name='checkVMS" + len + "' onMouseover=EnterContent('ToolTip','" +
//	"<? echo $WORDLIST['tip-voicemail'][$LANG]; ?>" + "','" + "<? echo $WORDLIST['tip-Intovoicemail'][$LANG]; ?>" + "'); " +
//	"onMouseout=deActivate() onClick=clickCheck('VMS','" + len + "')>" +

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
	"<input type='text' name='FW" + len + "' value='' style='background-color:#A8E61D; width:58%' onMouseover=EnterContent('ToolTip','" +
	"<? echo $WORDLIST['tip-fwNumtitle'][$LANG]; ?>" + "','" + "<? echo $WORDLIST['tip-fwNum'][$LANG]; ?>" + "'); " +
	"onMouseout=deActivate()>";

	 
    newRowObj.insertCell(newRowObj.cells.length).innerHTML = 
	"<div align='center'><img src='<? echo $TRASH_PIC ?>'  onClick='BtnDel(" + len + ")' style='cursor: hand; background-color:#A8E61D;' /></div>";
	
//	var new_element = document.createElement("input");
//	  new_element.type = "text";
//	  new_element.name = "ch_Acodec" + len;
//	  new_element.value = "ch_Acodec" + len;
//	  document.body.appendChild(new_element);


	
	}
function BtnDel(msg)
{
	document.getElementById("DelArray"+msg).value = document.getElementById("Account"+msg).value;

	document.getElementById("name"+msg).value = "";
	document.getElementById("Account"+msg).value = "";
	document.getElementById("Password"+msg).value = "";
	
}
function selectbtn(msg) 
{
	//alert(msg);
	FORM=document.forms[0];
	var page = 'codec_child.php?v=' + msg;   

	window.open(page,"","width=600,height=500,scrollbars=yes");	

}
function btn_group_alias() 
{
//	alert("123");
	FORM=document.forms[0];
	var page = 'group_alias.php';

	window.open(page,"","width=400,height=450,scrollbars=yes");	
}
function btn_group_pickup(msg) 
{
	//alert(msg);
	var page = 'group_pickup.php?v=' + msg;   
	window.open(page,"","width=600,height=500,scrollbars=yes");	
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
<!--
<body onmousemove="overhere()" ONDRAGSTART="window.event.returnValue=false" ONCONTEXTMENU="window.event.returnValue=false"  >
-->
<body onmousemove="overhere()">

<form name="form" method="POST" enctype="multipart/form-data" action="<? $PHP_SELF ?>">


<?
	echo "<p align='left' style=$MainTitleStyle color='$MainTitleTextColor'>&nbsp;&nbsp;";
	echo $WORDLIST['extensionset'][$LANG];
	echo "</p>";

?>
<div id="ToolTip"></div>


<table id="mytable" width="100%" border="1" align="center">

<?php
	echo "<tr>";
	echo "<th width='2%'>No.</th>";
    echo "<th width='10%' font style='cursor: hand' scope='row' style='$MainFieldStyle padding:5px' >";
	echo $WORDLIST['ex-name'][$LANG];
	echo "</font></th>";
	
    echo "<th width='10%' font style='cursor: hand' scope='row' style='$MainFieldStyle padding:5px' >";
	echo $WORDLIST['Account'][$LANG];
	echo "</font></th>";
	
    echo "<th width='10%' font style='cursor: hand' scope='row' style='$MainFieldStyle padding:5px' >";
	echo $WORDLIST['Password'][$LANG];
	echo "</font></th>";
	
    echo "<th width='7%' font style='cursor: hand' scope='row' style='$MainFieldStyle padding:5px' >";
	echo $WORDLIST['nat'][$LANG];
	echo "</font></th>";
	
    echo "<th width='14%' font style='cursor: hand' scope='row' style='$MainFieldStyle padding:5px' >";
	echo $WORDLIST['group'][$LANG];
	echo "</font>&nbsp;&nbsp;<input name='edit2' align='right' value='";
	
	echo $WORDLIST['group-name'][$LANG];
	echo "' type='button' style='$MainBtnStyle; cursor: hand;' onclick='javascript:btn_group_alias()'></th>";
    
	echo "<th width='11%' font style='cursor: hand' scope='row' style='$MainFieldStyle padding:5px' >";
	echo $WORDLIST['grouppickup'][$LANG];
	echo "</font></th>";

	echo "<th width='11%' font style='cursor: hand' scope='row' style='$MainFieldStyle padding:5px' >";
	echo $WORDLIST['vcodec'][$LANG];
	echo "</font></th>";

    echo "<th width='11%' font style='cursor: hand' scope='row' style='$MainFieldStyle padding:5px' >";
	echo $WORDLIST['acodec'][$LANG];
	echo "</font></th>";
	
    echo "<th width='16%' align='left' scope='row' style='$MainFieldStyle padding:5px'>";
	echo $WORDLIST['forward'][$LANG];
	echo "</font></th>";
	
	echo "<th width='8%' font style='$MainFieldStyle; padding:5px'>";
	echo $WORDLIST['del'][$LANG];
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
		echo "<td align='center'>".($i+1)."</td>";
 		echo "<td><input name='name$i' type='text' value='$ch_name[$i]' style='background-color:$FieldColor; width: 100%'></td>";
 		echo "<td><input name='Account$i' type='text' value='$ch_account[$i]' style='background-color:$FieldColor; width: 100%'></td>";
 		echo "<td><input name='Password$i' type='text' value='$ch_Pwd[$i]' style='background-color:$FieldColor; width: 100%'></td>";
	
		echo "<td><select name='nat$i' STYLE='background-color:$FieldColor;width :100%' >";
		if( strstr( $ch_nat[$i] , "no" ) != "" )
		{
			echo "<option value=no>NO</option>";
			echo "<option value=yes>YES</option>";
		}
		else
		{
			echo "<option value=yes>YES</option>";
			echo "<option value=no>NO</option>";
		}
		echo "</select></td>";
	
		echo "<td><select name='group$i' STYLE='background-color:$FieldColor;width :100%' >";
		ShowGroup($ch_group[$i]);
		echo "</select></td>";
		
		echo "<td><div align='center'><input name='edit2[]' align='center' type='button' onClick='javascript:btn_group_pickup(\"$i,";
		echo $ch_pickup[$i];
		echo "\");' value='";
		echo $WORDLIST['modify'][$LANG];
		echo "' style='$MainBtnStyle; width: 100%; cursor: hand;'></div></td>";

		echo "<td><div align='center'><input name='edit0[]' align='center' type='button' onClick='javascript:selectbtn(\"video,$i,";
		echo $ch_Vcodec[$i];
		echo "\");' value='";
		// echo $WORDLIST['modify'][$LANG];
		echo $ch_Vcodec[$i];
		echo "' style='$MainBtnStyle; width: 100%; cursor: hand;'></div></td>";
	
		echo "<td><div align='center'><input name='edit1[]' align='center' type='button' onClick='javascript:selectbtn(\"audio,$i,";
		echo $ch_Acodec[$i];
		echo "\");' value='";
//		echo $WORDLIST['modify'][$LANG];
		echo $ch_Acodec[$i];
		echo "' style='$MainBtnStyle; width: 100%; cursor: hand;'></div></td>";
		
		echo "<td>";
		
//		echo "<input type=checkbox name='checkVMS$i' onMouseover=EnterContent('ToolTip','";
//		echo $WORDLIST['tip-voicemail'][$LANG];
//		echo "','";
//		echo $WORDLIST['tip-Intovoicemail'][$LANG];
//		echo "'); onMouseout=deActivate() onClick=clickCheck('VMS',$i)>";

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
		echo "<input name='FW$i' type='text' value='$FW[$i]' style='background-color:$FieldColor; width :45%'onMouseover=EnterContent('ToolTip','";
		echo $WORDLIST['tip-fwNumtitle'][$LANG];
		echo "','";
		echo $WORDLIST['tip-fwNum'][$LANG];
		echo "'); onMouseout=deActivate()>";
				
		
		
		echo "<td align='center'><img src='$TRASH_PIC' onClick='BtnDel($i)' style='cursor: hand; background-color:#A8E61D;' /></td></tr>";
		echo "<input name='DelArray$i' type='hidden' value=''>";	

		}
	
	for( $i = 0; $i < $MAX_EXTENSION; $i++ )
	{
		echo "<input name='ch_Acodec$i' type='hidden' value='$ch_Acodec[$i]'>";
		echo "<input name='ch_Vcodec$i' type='hidden' value='$ch_Vcodec[$i]'>";
		echo "<input name='ch_pickup$i' type='hidden' value='$ch_pickup[$i]'>";
		
	}
	

?>
</table>






<p align="center"> 
<input name="btnNew" type="button" value="<? echo $WORDLIST['adddnew'][$LANG] ?>" onClick="javascript:Addnew()" style="<? echo $MainBtnStyle ?>;height:30px;width:60px"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type='submit' name='Submit' value="<? echo $WORDLIST['submit'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="Cancel" value="<? echo $WORDLIST['cancel'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> </td>
</p>
<br><br>
</form>
</body>



</html>
