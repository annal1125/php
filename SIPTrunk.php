<?php
if($_COOKIE['login'] != "yes")
{
	header('Location: index.php');
	exit;
}


	include "filepath.php";
	$SIPTRUNKREG_PATH = "$BASE_AST/sip_trunk_reg.conf";
	$AVA_PATH = "$BASE_AST/ava.conf";
	
	if(isset($_POST[Submit]))
	{	
		unlink($SIPTRUNKREG_PATH);
		unlink($SIPTRUNK_PATH);	
		
		$fpReg = fopen( $SIPTRUNKREG_PATH , "w+");
		$fpReg_iax = fopen( $SIPTRUNKREG_PATH_IAX , "w+");
		$fpTrunk = fopen( $SIPTRUNK_PATH , "w+");
		$fpTrunk_iax = fopen( $SIPTRUNK_PATH_IAX , "w+");
				
		$bFirst = 1;
		

		for( $i = 0; $i < $MAX_SIP_TRUNK; $i++ )
		{	
			// get field value
			$ch_alias = sprintf("ch_alias%d",$i);
			$ch_alias = $$ch_alias;
			$ch_Proxy = sprintf("ch_Proxy%d",$i);
			$ch_Proxy = $$ch_Proxy;	
			$ch_account = sprintf("ch_account%d",$i);
			$ch_account = $$ch_account;
			$ch_pwd = sprintf("ch_pwd%d",$i);
			$ch_pwd = $$ch_pwd;
			$ch_num = sprintf("ch_num%d",$i);
			$ch_num = $$ch_num;
			$checkreg = sprintf("checkreg%d",$i);
			$checkreg = $$checkreg;
			$regtype = sprintf("regtype%d",$i);
			$regtype = $$regtype;
			
			
			
			// register file
			if( $ch_Proxy == "" || $ch_alias == ""  )
				continue;
			// register => user:secret:authuser@host:port/extension
			$StrTotal = NULL;
			
			if( $checkreg == NULL || $ch_account == NULL)
				$StrTotal  = ";";
				
			$StrTotal .= "register => ";
			$StrTotal .= "$ch_account";
			
			if( $ch_pwd != "" )
				$StrTotal .= ":$ch_pwd:$ch_account";
			$StrTotal .= "@$ch_Proxy";
			if( $ch_num != "" )
				$StrTotal .= "/$ch_num";
			$StrTotal .= ";$ch_alias\n";
			
			
			
			if( $regtype == "IAX2" )
				fwrite( $fpReg_iax , $StrTotal );	
			else
				fwrite( $fpReg , $StrTotal );	

			$proxytmp = explode( ":" , $ch_Proxy );
			$proxy = chop($proxytmp[0]);
			$porttmp = chop($proxytmp[1]);
			
			
			$StrTotal = sprintf("[%s]\n",$ch_alias);
			$StrTotal .= sprintf("secret=%s\n",$ch_pwd);
			$StrTotal .= sprintf("username=%s\n",$ch_account);
			$StrTotal .= sprintf("host=%s\n",$proxy);
			$StrTotal .= sprintf("fromuser=%s\n",$ch_num);
			$StrTotal .= "fromdomain=$proxy\n";
			
			if( $porttmp != "" )
				$StrTotal .= "port=$porttmp\n";
		
			$StrTotal .= "context=ava_media\n";
			$StrTotal .= "dtmfmode=rfc2833\n";
			$StrTotal .= "insecure=invite,port\n";
			$StrTotal .= "type=peer\n";
			$StrTotal .= "canreinvite=no\n";
 			$StrTotal .= "disallow=all\n";
			
			$AudioCodec = sprintf("ch_Acodec%d",$i);
			$AudioCodec = $$AudioCodec;
			
			if( $AudioCodec == null )
				$parse_temp = $AudioArray;
			else
				$parse_temp = explode( "/" , $AudioCodec );	
			$ACodecTemp = null;
			
			reset( $parse_temp );
			while(list($key , $val) = each($parse_temp)) 
				$ACodecTemp .= "allow=$val\n";
		
			$VideoCodec = sprintf("ch_Vcodec%d",$i);
			$VideoCodec = $$VideoCodec;
			$parse_temp = explode( "/" , $VideoCodec );	
			
			if( $VideoCodec == null )
				$parse_temp = $VideoArray;
			else
				$parse_temp = explode( "/" , $VideoCodec );	
				
			$VCodecTemp = null;
			
			reset( $parse_temp );
			while(list($key , $val) = each($parse_temp)) 
				$VCodecTemp .= "allow=$val\n";			

			$StrTotal .= "$ACodecTemp";
			$StrTotal .= "$VCodecTemp";
				
 			$StrTotal .= "qualify=yes\n";
 			$StrTotal .= "nat=yes\n";	
			
			if( $regtype == "IAX2" )
			{
				$StrTotal .= "trunk=yes\n";
				$StrTotal .= "auth=md5\n";
				$StrTotal .= "notransfer=yes\n";
			}

			
 			$StrTotal .= "\n";
 						
						
			if( $regtype == "IAX2" )
				fwrite( $fpTrunk_iax , $StrTotal );	
			else
				fwrite( $fpTrunk , $StrTotal );	
			
		} // end for( $i = 0; $i < $MAX_FIELD; $i++ )
	
		fclose( $fpTrunk );
		fclose( $fpReg );	
		fclose( $fpReg_iax );
		fclose( $fpTrunk_iax );	
		
		shell_exec( "asterisk -rx reload" );
	} // end if(isset($_POST[Submit]))
	
	// init param
	$ch_account = NULL;
	$ch_pwd = NULL;
	$ch_Proxy = NULL;
	$ch_num = NULL;
	$ch_alias = NULL;
	$ch_Acodec = NULL;
	$ch_Vcodec = NULL;
	$checkreg = NULL;
	$i = 0;
	
	for( $j = 0; $j < 2; $j++ )
	{
		if( $j == 0 )
			$content_array = file( $SIPTRUNKREG_PATH );	
		else
			$content_array = file( $SIPTRUNKREG_PATH_IAX );	
		
		while( list($key , $val) = each($content_array) )
		{		 
			// register => user:secret:authuser@host:port/extension
			// reg info sample :  
			// register => +886342460301@ims-ngn.chttl.com.tw:ims123456@10.144.166.40/+88634246030;+88634246030
			$Field_Str = explode( " => " , $val );

			if( strstr($Field_Str[0] , "register" ) == NULL )
			{ // register
				continue;
			}
			
			$checkreg[$i] = ($Field_Str[0] == ";register" ) ? false:true;

			
			$middle = substr( strrchr( $Field_Str[1] , "@" ) , 1 );
			// $middle = host:port/extension;alias
			$Str = explode( ";" , $middle );
			// $Str[0] = host:port/extension              $Str[1] = alias
			$ch_alias[$i] = trim($Str[1]);
			$ch_num[$i] =  substr( strrchr( $Str[0] , "/" ) , 1 );
			$Str1 = explode( "/" , $Str[0] );
			// $Str1[0] = host:port   $Str1[1] = extension;
			$ch_Proxy[$i] = $Str1[0];
			// ---------------------------------------------------------------------------------------- //
			$Str = ltrim( $Field_Str[1] );
			$First =  substr( $Str , 0 , strrpos( $Str , "@" ) );
			// $First = user:secret:authuser
			$Str1 = explode( ":" , $First );
			// $Str1[0] = user  $Str1[1] = secret  $Str1[2] =authuser
			$ch_account[$i] = $Str1[0];
			$ch_pwd[$i] = $Str1[1];
			$i++;
		}// end while
	} // end for
	
	for( $j = 0; $j < 2; $j++ )
	{
		if( $j == 0 )
			$content_array = file( $SIPTRUNK_PATH );	
		else
			$content_array = file( $SIPTRUNK_PATH_IAX );	
		
		while( list($key , $val) = each($content_array) )
		{
			if( strstr($val , "[") != "" )
			{
				$start = strpos($val,"[" )+1;
				$end = strpos( $val , "]" );
				$StrTep = substr($val, $start , $end - $start );
				
				if( $StrTep != "" )
				{
					reset( $ch_alias );
					while( list($key , $val) = each($ch_alias) )
					{
						if( !strcasecmp(  $StrTep , $val )  )
						{
							$Prefix_Index = $key;
							break;
						}
					}
				}
			}
			$parse_temp = explode( "=" , $val );
			$parse_temp[1] = trim($parse_temp[1]);
			
			if( $j == 0 )
				$ch_regtype[$j] = "SIP";
			else
				$ch_regtype[$Prefix_Index] = "IAX2";
			
			
			if( $parse_temp[0] == "allow")
			{
				$bAudio = false;
	//			echo "$ch_name[$j]:$parse_temp[1]<br>";
				reset( $AudioArray );
				while(list($key , $val) = each($AudioArray)) 
				{
					if( !strcasecmp( $parse_temp[1] , $key)  )
					{
						if( $ch_Acodec[$Prefix_Index] == null ) 
							$ch_Acodec[$Prefix_Index] .= $key;
						else 
							$ch_Acodec[$Prefix_Index] .= ("/" . $key);
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
							if( $ch_Vcodec[$Prefix_Index] == null ) 
								$ch_Vcodec[$Prefix_Index] .= $key;
							else 
								$ch_Vcodec[$Prefix_Index] .= ("/" . $key);
							break;					
						}
					}
				}
			}
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
<meta http-equiv="Content-Type" content="text/html; charset=big5">

<title>Conference Edition</title>
<script language = "javascript">
<!--
function Addnew()
{
//	var page = 'voicexml.php?v=-1';   
//	window.location.href = page;
	var tableObj = document.getElementById("mytable");
	var len = tableObj.rows.length;
	
	var maxlen =  <? echo $MAX_DIAL_PLAN; ?>;
	if( len > maxlen )
		return;
    var newRowObj = tableObj.insertRow(len);
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = 
	"<div align='center'><input type='checkbox' name='checkreg"+len+"' value='' style='background-color:#A8E61D; width :100%'></div>";
	
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = 
	"<select name='regtype"+len+"' STYLE='background-color:#A8E61D;width :100%';>" +
	"<option value=SIP>SIP</option>"
	<? if( $IAX2_SUPPORT ) echo '+ "<option value=IAX2>IAX2</option>"'; ?>;
	
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = "<input type='text' name='ch_alias"+len+"' value='' style='background-color:#A8E61D; width :100%'>";
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = "<input type='text' name='ch_Proxy"+len+"' value='' style='background-color:#A8E61D; width:100%'>";
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = "<input type='text' name='ch_account"+len+"' value='' style='background-color:#A8E61D; width:100%'>";
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = "<input type='text' name='ch_pwd"+len+"' value='' style='background-color:#A8E61D; width:100%'>";
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = "<input type='text' name='ch_num"+len+"' value='' style='background-color:#A8E61D; width:100%'>";
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = 
	"<input name='edit0[]' align='center' type='button' onClick='javascript:selectbtn(\"video,"+len+",\");' value='<? echo $WORDLIST['modify'][$LANG]; ?>'" +
	 " style='<? echo $MainBtnStyle ?>; width: 100%; cursor: hand;'>";
	 	 
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = 
	"<input name='edit1[]' align='center' type='button' onClick='javascript:selectbtn(\"audio,"+len+",\");' value='<? echo $WORDLIST['modify'][$LANG]; ?>'" +
	 " style='<? echo $MainBtnStyle ?>; width: 100%; cursor: hand;'>";
	
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = 
	"<p align='center'><img src='<? echo $TRASH_PIC ?>' onClick='BtnDel("+len+")' style='cursor: hand; background-color:#A8E61D;'></p>";
}	
function BtnDel(msg)
{
	document.getElementById("ch_alias"+msg).value = "";
	document.getElementById("ch_Proxy"+msg).value = "";
	document.getElementById("ch_account"+msg).value = "";

	document.getElementById("ch_pwd"+msg).value = "";
}
function selectbtn(msg) 
{
	FORM=document.forms[0];
	var page = 'codec_child.php?v=' + msg;   
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

<body>
<form name="form" method="POST" enctype="multipart/form-data" action="<? $PHP_SELF ?>">
<?
	echo "<p align='left' style=$MainTitleStyle color='$MainTitleTextColor'>&nbsp;&nbsp;";
	echo $WORDLIST['SIPTrunk'][$LANG];
	echo "</p>";

?>
<table id="mytable" width="100%" border="1" align="center">
<?php

	$widthSize = 13;

	echo "<tr>";
    echo "<th width='6%' font style='$MainFieldStyle; padding:5px'>";
	echo $WORDLIST['trunk-breg'][$LANG];
	echo "</font></th>";
    echo "<th width='6%' font style='$MainFieldStyle; padding:5px'>";
	echo $WORDLIST['trunk-protocal'][$LANG];
	echo "</font></th>";
	
    echo "<th width='$widthSize%' font style='$MainFieldStyle; padding:5px'>";
	echo $WORDLIST['trunk-alias'][$LANG];
	echo "</font></th>";
    echo "<th width='$widthSize%' font style='$MainFieldStyle; padding:5px'>";
	echo $WORDLIST['proxy-port'][$LANG];
	echo "</font></th>";
    echo "<th width='$widthSize%' font style='$MainFieldStyle; padding:5px'>";
	echo $WORDLIST['Account'][$LANG];
	echo "</font></th>";
    echo "<th width='$widthSize%' font style='$MainFieldStyle; padding:5px'>";
	echo $WORDLIST['Password'][$LANG];
	echo "</font></th>";
    echo "<th width='$widthSize%' font style='$MainFieldStyle; padding:5px'>";
	echo $WORDLIST['telnum'][$LANG];
	echo "</font></th>";
	
    echo "<th width='9%' font style='$MainFieldStyle; padding:5px'>";
	echo $WORDLIST['vcodec'][$LANG];
	echo "</font></th>";

    echo "<th width='9%' font style='$MainFieldStyle; padding:5px'>";
	echo $WORDLIST['acodec'][$LANG];
	echo "</font></th>";
	
	echo "<th width='6%' font style='$MainFieldStyle; padding:5px'>";
	echo $WORDLIST['del'][$LANG];
	echo "</font></th>";
	
	echo "</tr>";


	for( $i = 0; $i < sizeof($ch_account); $i++ )
	{		
		if( ($i % 2) == 0 )
			$StrColor = $MainEditBackGround;
		else
			$StrColor = $MainEditBackGround1;
		
		echo "<tr>";
		
		if( $checkreg[$i] ) $StrCheck = "checked='CHECKED'";
		else $StrCheck = "";
		echo "<td align='center'><input type=checkbox name='checkreg$i' $StrCheck ></td>";

		echo "<td><select name='regtype$i' STYLE='background-color:$StrColor;width :100%' >";
		if( $ch_regtype[$i] == "IAX2" )
		{
			echo "<option value=IAX2>IAX2</option>";
			echo "<option value=SIP>SIP</option>";
		}
		else
		{
			echo "<option value=SIP>SIP</option>";
			if( $IAX2_SUPPORT )
				echo "<option value=IAX2>IAX2</option>";
		}
		
		echo "</select></td>";
		echo "<td><input name='ch_alias$i' type='text' value='$ch_alias[$i]' style='background-color:$StrColor; width: 100%'></td>";
		echo "<td><input name='ch_Proxy$i' type='text' value='$ch_Proxy[$i]' style='background-color:$StrColor; width: 100%'></td>";
		echo "<td><input name='ch_account$i' type='text' value='$ch_account[$i]' style='background-color:$StrColor; width: 100%'></td>";
		echo "<td><input name='ch_pwd$i' type='text' value='$ch_pwd[$i]' style='background-color:$StrColor; width: 100%'></td>";
		echo "<td><input name='ch_num$i' type='text' value='$ch_num[$i]' style='background-color:$StrColor; width: 100%'></td>";

		echo "<td><div align='center'><input name='edit0[]' align='center' type='button' onClick='javascript:selectbtn(\"video,$i,";
		echo $ch_Vcodec[$i];
		echo "\");' value='";
		echo $WORDLIST['modify'][$LANG];
		echo "' style='$MainBtnStyle; width: 100%; cursor: hand;'></div></td>";
		
		echo "<td><div align='center'><input name='edit1[]' align='center' type='button' onClick='javascript:selectbtn(\"audio,$i,";
		echo $ch_Acodec[$i];
		echo "\");' value='";
		echo $WORDLIST['modify'][$LANG];
		echo "' style='$MainBtnStyle; width: 100%; cursor: hand;'></div></td>";

		echo "<td align='center' ><img src='$TRASH_PIC' onClick='BtnDel($i)' style='cursor: hand; background-color:#FFCC99;'></td>";
		echo "</tr>";
	}
	
	for( $i = 0; $i < $MAX_SIP_TRUNK; $i++ )
	{
		echo "<input name='ch_Acodec$i' type='hidden' value='$ch_Acodec[$i]'>";
		echo "<input name='ch_Vcodec$i' type='hidden' value='$ch_Vcodec[$i]'>";
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
