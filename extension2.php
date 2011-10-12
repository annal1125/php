<?php
	if($_COOKIE['login'] != "yes")
	{
		header('Location: index.php');
		exit;
	
	}
	
//	 $leavetime = date("YmdHis", "169150188");
	
//	echo "$leavetime<br>";
	
	include "filepath.php";
	include "AstManager.php";
	
	
	$SortField = $_SERVER["QUERY_STRING"];
	
	if( $SortField != "" )
	{
		$parse_temp = explode( "=" , $SortField );
		$SortField = chop($parse_temp[1]);
	}
	
	$VideoArray = array( "disable" => "Disable" ,
		"h263p" => "H263p");
	
	$LevelArray = array( 
		"1" => "1" ,
		"2" => "2" ,
		"3" => "3" ,
		"4" => "4" ,
		"5" => "5" ,
		);
		
		
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

	$GROUP = "Level";
	
	
	if(isset($_POST[Submit]))
	{
		unlink($SIP_EX_PATH);
		
		$fp = fopen( $SIP_EX_PATH , "w+");	
		
		for( $i = 0; $i < $MAX_EXTENSION; $i++ )
		{	
			$Account = sprintf("Account%d",$i);
			$Account = $$Account;
			$Level = sprintf("Level%d",$i);
			$Level = $$Level;
			
			if( $Account == "" ) 
			{
				$DelArray = sprintf("DelArray%d",$i);
				$DelArray = $$DelArray;
				
				if( $DelArray != "" ) 
					delDB( $GROUP , $DelArray );
				
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
			$StrTotal .= "call-limit=1\n";
			$StrTotal .= "canreinvite=no\n";
			$StrTotal .= "$ACodecTemp\n";
			if( $SUPPORTVIDEO )
				$StrTotal .= "$VCodecTemp\n";
			$StrTotal .= "\n";
					
			fwrite( $fp , $StrTotal );
			
			
			
			putDB( $GROUP , $Account , $Level );

			
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
			$res = getDBValue( $GROUP , $ch_name[$j] );
			$res =chop(substr( $res , strpos( $res , "Value: ")+ strlen("Value: "), 1 ));
			if( $res < 1 || $res > 5 ) $res = 1;
			 $ch_Level[$j] = $res;
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

function SetOPLevel($level)
{
	global $LevelArray;
	reset( $LevelArray ); 
	
	if( $level != "" )
		echo "<option value=$level>$LevelArray[$level]</option>";

	while( list($key , $val) = each($LevelArray) )
	{
		if( $key == $level )
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
	"<? echo '<select name=\'level'?>" +len+ "<? echo '\' STYLE=\'background-color:#A8E61D;width :100%\' >'; SetOPLevel(''); echo '</select>'; ?>";

	
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = 
	"<p align='center'><img src='trash.jpg'  onClick='BtnDel(" + len + ")' style='cursor: hand; background-color:#A8E61D;' /></p>";
}
function BtnDel(msg)
{
	document.getElementById("DelArray"+msg).value = document.getElementById("Account"+msg).value;

	document.getElementById("name"+msg).value = "";
	document.getElementById("Account"+msg).value = "";
	document.getElementById("Password"+msg).value = "";
	


	
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
	echo "<tr>";
    echo "<th width='20%' scope='row' >";
	echo $WORDLIST['ex-name'][$LANG];
	echo "</th>";
	
    echo "<th width='14%'>";
	echo $WORDLIST['Account-num'][$LANG];
	echo "</th>";
	
    echo "<th width='14%'>";
	echo $WORDLIST['Password'][$LANG];
	echo "</th>";
	
//    <td width="20%"><div align="center"><font style="filter: glow(color=#9966FF,strength=3); height:10px; color:white; padding:5px">DTMF格式</font></div></td>

	echo "<th width='10%'>";
	echo $WORDLIST['vcodec'][$LANG];
	echo "</th>";
	

    echo "<th width='15%'>";
	echo $WORDLIST['acodec'][$LANG];
	echo "</th>";
	
    echo "<th width='15%'>";
	echo "等級";
	echo "</th>";
	
    echo "<th width='5%' align='center' scope='row'>";
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
		echo "<td><select name='Level$i' STYLE='background-color:$FieldColor;width :100%' >";
		SetOPLevel($ch_Level[$i]);
		echo "</select></td>";
		
		echo "<td align='center'>";
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
