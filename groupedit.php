<?php

if($_COOKIE['login'] != "yes")
{
	header('Location: index.php');
	exit;
}


include "filepath.php";
include "AstManager.php";

	$GroupName = $_SERVER["QUERY_STRING"];
	
	if( $GroupName != "" )
	{
		$parse_temp = explode( "=" , $GroupName );
		$GroupName = chop($parse_temp[1]);
	}
	$RingTypeArray = array("all" => $WORDLIST['simultaneous_ring'][$LANG] , "serial" => $WORDLIST['serial_ring'][$LANG] );
	
	Net_AsteriskManager( '127.0.0.1' , '5038' );
	connect();
	login('admin', 'avadesign22221266');	
	$GROUP = "group";

	if($_POST['sub'])
	{	
		reset( $right );
		if( $ringtype == "all" )
		{ //  simultaneous ring 
			$StrTotal = "";
			$bFirst = 1;
			if( $GroupName != $groupName )
			{// delete old 
				delDBtree( $GroupName );
				delDB( $GROUP , $GroupName );
			}
			// add new
			while( list($key , $val) = each($right) )
			{
				if( !$bFirst )
					$StrTotal .= "&";	
				if( strstr( $val , "/" ) == "" ) // SIP
					$StrTotal .= "SIP/";
				else
				{
					if( strstr( $val , "PBX" ) != "" ) // PBX
					{
						$tmp = substr( $val , strlen("PBX/") );
						$val = "AVA/".$tmp;
					}
					else
					{ // Trunk ... 
						$Str1 = explode(  "/" , $val );
						$val = "SIP/" . chop($Str1[1]) . "@" . $Str1[0];
					}
				}

				$StrTotal .= $val;
				$bFirst = 0;
			}
			putDB( $GROUP , $groupName , $StrTotal );
		}
		else
		{ // serial ring
			if( $GroupName != $groupName )
			{// delete old 
				delDBtree( $GroupName );
				delDB( $GROUP , $GroupName );
			}
			// add new
			putDB( $GROUP , $groupName , $ringtime );
			$i = 1;
			
			while( list($key , $val) = each($right) )
			{
				if( strstr( $val , "/" ) == "" ) // SIP
					$StrTotal = "SIP/$val";
				else
				{
					if( strstr( $val , "PBX" ) != "" ) // PBX
					{
						$tmp = substr( $val , strlen("PBX/") );
						$val = "AVA/".$tmp;
					}
					else
					{ // Trunk ... 
						$Str1 = explode(  "/" , $val );
						$val = "SIP/" . chop($Str1[1]) . "@" . $Str1[0];
						
					}
					$StrTotal = $val;
					
				}
				putDB( $groupName , $i++ , $StrTotal );			
			}
		}
	echo "<Script language='JavaScript'>";
	echo "opener.window.history.go(0);";
	echo "window.close();";
	echo"</Script>";
 	}	
	

	$GroupKey = "/$GROUP/";
	$response = getDB();
	$RightContect = "";
	$Leftcontent = "";
	$GroupTmp = "";
	$SerialRingTime = "";
	
	$tok = strtok($response,"\r\n");

	while($tok) 
	{	// get group data	
		if( strstr( $tok , $GroupKey ) == "" )
		{
			$tok = strtok("\r\n");
			continue;
		}
		
		$Str1 = substr(  $tok , strpos( $tok , ":")+1 );
		$Str1 = chop(ltrim($Str1));	
		
		if( $Str1 == "" )
		{
			$tok = strtok("\r\n");
			continue;
		}

		if( $GroupName != chop( substr( $tok,strlen( $GroupKey ),strpos($tok," ") ) ) )
		{
			$tok = strtok("\r\n");
			continue;
		}
	
		if( strstr( $Str1, "&" ) != "" )
		{ //  simultaneous ring 
			// SIP/2102&SIP/2103&777@ava-199
			$RingType = "all";
			$Str2 = explode(  "&" , $Str1 );
			$i = 0;
			while( $Str2[$i] )
			{
				$Str3 = explode(  "/" , $Str2[$i] );
				if( $Str3[0] == "SIP" )
					$Strtmp = chop(ltrim($Str3[1]));
				else if( $Str3[0] == "AVA" )
				{
			
					$Str3 = chop(ltrim($Str3[1]));
					$Strtmp = "PBX/". $Str3;
					
				}
				else
				{
					$Strtmp = chop(ltrim($Str2[$i]));	
					$Pos = strpos( $Strtmp , "@" );
					if( $Pos != "" )
					{
						$Str11 = substr( $Strtmp , 0 , $Pos );
						$Str21 = substr( $Strtmp , $Pos+1 );
						$Strtmp = $Str21 . "/" . $Str11;
					}
				}
				$RightContect[$Strtmp] = $Strtmp;	
				$i++;
			}
		}
		else
		{ //serial ring
			$RingType = "serial";
			$GroupTmp = "/" . chop( substr( $tok, strlen( $GroupKey ),strpos($tok," ") ) ) . "/";
			$SerialRingTime = chop(ltrim($Str1));	
			break;
		}
		$tok = strtok("\r\n");
	}			
	if( $GroupTmp != "" )
	{//serial ring
		$tok = strtok($response,"\r\n");
		while($tok) 
		{
			if( strstr( $tok , $GroupTmp ) == "" )
			{
				$tok = strtok("\r\n");
				continue;
			}
			$Str1 = explode(  ":" , $tok );
			$Str1[1] = chop(ltrim($Str1[1]));	
			
			if( $Str1[1] == "" )
			{
				$tok = strtok("\r\n");
				continue;
			}
			$Str1[0] = chop(ltrim($Str1[0]));	
			$iPos = substr( $Str1[0], strlen( $GroupTmp ) );
			
			$Str3 = explode(  "/" , $Str1[1] );			
			if( $Str3[0] == "SIP" )
				$StrTmp  = chop(ltrim($Str3[1]));
			else if( $Str3[0] == "AVA" )
				$StrTmp  = "PBX/".chop(ltrim($Str3[1]));
			else
			{
				$StrTmp  = chop(ltrim($Str1[1]));
				$Pos = strpos( $StrTmp , "@" );
				if( $Pos != "" )
				{
					$Str11 = substr( $StrTmp , 0 , $Pos );
					$Str21 = substr( $StrTmp , $Pos+1 );
					$StrTmp = $Str21 . "/" . $Str11;
				}
			}
				
				$RightContect[$StrTmp]	= $StrTmp;	
				
			$tok = strtok("\r\n");
		}
	}

	$content_array = file( $SIP_EX_PATH );

	$i = 0;
	$Lefttmp = "";
	while( $content_array[$i] != "" )
	{
		if( strstr($content_array[$i] , "[" ) != "" )
		{
			$parse_temp = explode( "[" , $content_array[$i] );	
			$parse_temp1 = explode( "]" , $parse_temp[1] );
			$Strtmp = chop($parse_temp1[0]);
			$Lefttmp[$Strtmp] = $Strtmp;
		}
		$i++;
	}
	
	logout();
	$k = 0;
	reset($Lefttmp);
	while( list($keyL , $valL) = each($Lefttmp) )
	{
		$bExist = 0;
		reset($RightContect);
		while( list($keyR , $valR) = each($RightContect) )
		{
			if( $valL == $valR )
			{
				$bExist = 1;
				break;
			}
		}
		if( !$bExist )
			$Leftcontent[$keyL] = $valL;
	}
	
	$content_array = file( $SIPTRUNK_PATH );
	$i = 0;
	
	$TrunkAlias = "";
	if( $PLATFORM == 1 )
		$TrunkAlias["PBX"] = "PBX";
	while( $content_array[$i] != "" )
	{
		if( strstr($content_array[$i] , "[") != "" )
		{
			$StrTep = chop( substr($content_array[$i], 1 , -2 ) );
			
			if( $StrTep != "" )
				$TrunkAlias[$StrTep] = $StrTep;		
		}
		$i++;	
	}	
	
	

?>




<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>Group</title>

<script language = "javascript">
function ClickOK()
{
var a = document.getElementById("ringtime").value;
if(a == ""){alert("循響時間未輸入時間\n\n Serial Ring Time is empty");}
else{
	var obj = document.forms[0].elements['right[]'];

	for(var i=0;i<obj.options.length;i++)
	{
		obj.options[i].selected = true;
	}
	document.forms[0].sub.value = 1;
	document.forms[0].submit();
   }
}
function SelRingType()
{
	var FORM=document.forms[0];
	
	var sel = FORM.ringtype.selectedIndex;
	
	
	if( FORM.ringtype.options[sel].text == "群響" )
		FORM.ringtime.disabled = true;
	else
		FORM.ringtime.disabled = false;
}

function moveOption(e1, e2)
{

	var bText = false;

	for(var i=0;i<e1.options.length;i++)
	{
		if(e1.options[i].selected)
		{
			var e = e1.options[i];
			e2.options.add(new Option(e.text, e.value));
			e1.remove(i);
			i=i-1
			bText = true;
		}
	}
	var FORM=document.forms[0];
	
	if( !bText && FORM.TrunkText.value != "" )
	{
		var iSel = FORM.SelTrunk.selectedIndex;
		
		var Str = FORM.SelTrunk.options[iSel].text + "/" + FORM.TrunkText.value;
		e2.options.add(new Option(Str, Str));
		FORM.TrunkText.value = "";
	}


}
function swapNode(node1,node2)
{

 var _parent=node1.parentNode;

 var _t1=node1.nextSibling;
 var _t2=node2.nextSibling;

 if(_t1)_parent.insertBefore(node2,_t1);
 else _parent.appendChild(node2);

 if(_t2)_parent.insertBefore(node1,_t2);
 else _parent.appendChild(node1);
}


function changepos(obj,index)
{
	var sel = obj.selectedIndex;

	if( index == -1 )
	{
		if( sel > 0 )
		{
			obj.options[sel].swapNode( obj.options[sel-1] );
		}
	}
	else if( index == 1)
	{
		if( sel < obj.options.length-1 )
		{
			obj.options(sel).swapNode( obj.options(sel+1) );
		}
	}

	
	
}
function gototop(obj)
{
	if(obj.multiple)
	{
		if(obj.selectedIndex !=-1)
		{
		
			for(var selIndex=0; selIndex<obj.options.length; selIndex++)
			{
				if(obj.options[selIndex].selected)
				{
					var transferIndex = selIndex;
					while(transferIndex > 0 && !obj.options[transferIndex - 1].selected)
					{
						obj.options[transferIndex].swapNode(obj.options[transferIndex - 1]);
						transferIndex--;
					}
				}
			}
		}
	}
	else
	{
		if(obj.selectedIndex !=-1)
		{
			var selIndex = obj.selectedIndex;
			while(selIndex > 0)
			{
				obj.options[selIndex].swapNode(obj.options[selIndex - 1]);
				selIndex --;
			}
		}
	}
	
}

function checkIsNum(obj)
{
  var Field = obj.value;
  var numtype="*#0123456789"; 
  
  var TextTmp = "";
  
  for(i=0;i< Field.length;i++)
  {
    if(numtype.indexOf(Field.substring(i,i+1)) >= 0) 
    {
		TextTmp += Field.substring(i,i+1);
	}
  } 
  obj.value = TextTmp;

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

<body onload=SelRingType();>
<form name="form" method="POST" enctype="multipart/form-data" action="<? $PHP_SELF ?>">
<?include "languag.php";?>
<p align="left" style=FILTER:Shadow(Color=8888ff,direction=100);height=20 color="#0000FF">&nbsp;&nbsp;<? echo $WORDLIST['groupsetting'][$LANG]; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<input type="text" name="groupName" value="<? echo $GroupName ?>" onkeyup="checkIsNum(this)" style='background-color:#FFCC99;'>
<br>&nbsp;&nbsp;<? echo $WORDLIST['ringtype'][$LANG]; ?>&nbsp;&nbsp;
<select name="ringtype" onChange=SelRingType(); STYLE='background-color:#FFCC99; width :150'>
<?
	reset( $RingTypeArray ); 
	if( $RingType != "" ){
		echo "<option value=$RingType>";
		echo $RingTypeArray[$RingType];
		echo "</option>";
	}	
		//echo "<option value=$RingType>$RingTypeArray[$RingType]</option>";
	while( list($key , $val) = each($RingTypeArray) )
	{
		if( $key == $RingType )
			continue;
		echo "<option value=$key>";
		echo $val;
		echo "</option>";
	}
?>	  
</select>	 
&nbsp;&nbsp;&nbsp;&nbsp;<? echo $WORDLIST['serial_ringtime'][$LANG]; ?>&nbsp;&nbsp;<input type="text" name="ringtime" value="<? echo "$SerialRingTime" ?> " style='background-color:#FFCC99; width :50'>
</p> 

<table width="90%" border="0" align="center" height="70%" >
<tr>
<td width="40%"  rowspan="2">
<select multiple name="left" size="8"  style='width: 200; height :275' ondblclick="moveOption(document.getElementById('left'), document.getElementById('right[]'))" STYLE='background-color:#FFCC99;'>
<?   
	reset($Leftcontent);
	while( list($key , $val) = each($Leftcontent) )
		echo "<option value=$key>$val</option>";
?>
</select>
<input type="text" name="TrunkText" value="" style="width: 100;">
<select name="SelTrunk" STYLE='background-color:#FFCC99;width :95;' >
<?
	reset($TrunkAlias);
	while( list($key , $val) = each($TrunkAlias) )
		echo "<option value=$key>$val</option>";
?>

</select>

</td>
<td width="15%" height="70%" align="center">
<input type="button" name="btn1" value=" >>>> "  style='width: 80%' onclick="moveOption(document.getElementById('left'), document.getElementById('right[]'))" STYLE='background-color:#FFCC99;'>

<br><br><br>
<input type="button" name="btn2" value=" <<<< "  style='width: 80%;' onclick="moveOption(document.getElementById('right[]'), document.getElementById('left'))" STYLE='background-color:#FFCC99;'>



</td>

<td width="40%" rowspan="2"> 
<select multiple name="right[]" size="5" style="width: 200; height :300"  ondblclick="moveOption(document.getElementById('right[]'), document.getElementById('left'))" STYLE='background-color:#FFCC99;'>
<?   
	reset($RightContect);
	while( list($key , $val) = each($RightContect) )
		echo "<option value=$key>$val</option>";
?>
</select>

<input name="sub" type="hidden" value="" >

</td>
</tr>
<tr>
<td width="15%" align="right">
<input TYPE="button" value="↑" style='width: 40%' onClick="changepos(document.getElementById('right[]'),-1)" >
<br>
<input TYPE="button" value="↓" style='width: 40%' onClick="changepos(document.getElementById('right[]'),1)">
<br>
<input TYPE="button" value="Top" style='width: 40%' onClick="gototop(document.getElementById('right[]'))">
<br>
</td>
</tr>
</table>
	
<p align="center"> 
<input type='button' name='Submit1' onclick=ClickOK();  value="<? echo $WORDLIST['submit'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" onclick="window.close();" value="<? echo $WORDLIST['cancel'][$LANG] ?>" style="<? echo $MainBtnStyle ?>" > </td>
</p>

</form>
</body>


</html>
