
<?php
	if($_COOKIE['login'] != "yes")
	{
//		header('Location: index.php');
//		exit;
	
	}
//	ob_end_clean(); 
//	ob_implicit_flush(true);
//	extract($_POST,EXTR_OVERWRITE);
	
	$SortField = $_SERVER["QUERY_STRING"];
	
	if( $SortField != "" )
	{
		$parse_temp = explode( "=" , $SortField );
		$SortField = chop($parse_temp[1]);
	}	
	
//	echo "$SortField<br>"; 
	
	include "AstManager.php";
	include "filepath.php";
	
	$MAX_PEERS = 10;
	
	
	Net_AsteriskManager( '127.0.0.1' , '5038' );
	connect();
	login('admin', 'avadesign22221266');
	
	$response = SipShowPeers();
	
//	echo "$response<br>";
	
	$Field_Str = explode( "\n" , $response );
		
		
	$name = "";	
	$num = "";	
	$IP = "";	
	$RegState = "";	
	$callee = "";	
	$CallState = "";	
	$Channels = "";
	
	
	$i = 0;
	$j = 0;
	while( $Field_Str[$i] != "" )
	{
		$StrStatus = chop( $Field_Str[$i] );
		
		if( (strstr( $StrStatus , "/" ) != "" ) && ( strstr( $StrStatus , "Host" ) == "" ) )
		{
			$Str1 = explode( "/" , $StrStatus );
			$num[$j] = $Str1[0];
			$Str1 = explode( " " , $Str1[1] );
			$name[$j] = $Str1[0];
			$j++;
		}
		$i++;	
	}
	$MAX_PEERS = $j;
	

	
	for( $i = 0;$i < $MAX_PEERS; $i++ )
	{
		$response = SipShowPeer( $num[$i] );
		
		$tok = strtok($response,"\r\n");
		while($tok) 
		{
			if( strstr( $tok , "Addr->IP" ) != "" )
			{
				if(  strstr( $tok , "Unspecified" ) == "" )
				{
					$Str1 = explode( ": " , $tok );
					$Str1 = explode( " " , $Str1[1] );
					$IP[$i] = $Str1[0];
					$IP[$i] .= ":";
					$IP[$i] .= $Str1[2];
				}
			}
			if( strstr( $tok , "Reg. Contact : sip:" ) != "" )
			{
				$Str1 = explode( "@" , $tok );
				$IP[$i] .= "  /  ";
				$Str2 = explode( ";" , $Str1[1] );
				$IP[$i] .= chop($Str2[0]);
			}
			if( strstr( $tok , "Status       :" ) != "" )
			{
				if( strstr( $tok , "OK" ) != "" )
					$RegState[$i] = "OK";
				else
					$RegState[$i] = "Unreachable";
			}
			$tok = strtok("\r\n");
		}
	}
	
	$SIP_PEERS = $MAX_PEERS;
	if( $PLATFORM == 1 )
	{
		$MAX_PEERS += 8;
		for( $i = 1;$i <= 8; $i++ )
			array_push( $name , "DSP CH$i" );
	}
	
	
	$response = ShowChannels();
	$tok = strtok($response,"\r\n");
	$i = 0;
	
	
	
	while($tok) 
	{			
		if( strstr( $tok , "Up" ) != "" )
		{ // channl used
			$CallerType  = substr($tok, 0,3 );
			
			// get channel names
			$pos = strpos( $tok , " " );
			$Channels[$i] = trim( substr($tok, 4 , $pos - 4 ) ); // 4: SIP/
			if( strstr( $Channels[$i] , "-" ) != "" ) 
				$RupKey = "-";
			else if( strstr( $Channels[$i] , ":" ) != "" ) // sometime interrup key was ':' not '-'
				$RupKey = ":";
			else
				$RupKey = "";
			
			if( $RupKey != "" )
			{
				$pos = strrpos( $Channels[$i] , $RupKey );			
				$Channels[$i] = substr($Channels[$i], 0 , $pos ); 
			}

			if( $CallerType == 'Ava' )
			{ // CEI
				$num[$SIP_PEERS++] = $Channels[$i];
			}

			// get application(Data)
			if( strstr( $tok , "Bridged" ) != "" )
			{ // Calldee
				$Str1 = explode( "Bridged Call(" , $tok );
				$CallerType  = substr($Str1[1], 0,3 );
				//Bridged Call(SIP/2020-095716d0
				$DataTmp = trim( substr($Str1[1], 4 ) ); // 4: SIP/
				
				if( strstr( $DataTmp , "-" ) != "" ) 
					$RupKey = "-";
				else if( strstr( $DataTmp , ":" ) != "" ) // sometime interrup key was ':' not '-'
					$RupKey = ":";
				else
					$RupKey = ")";
					
				
				$calleeTmp[$i] = substr($DataTmp, 0 , strrpos( $DataTmp , $RupKey ) );

				$CallStateTmp[$i] = "Calldee";
			}
			else
			{  // Caller 
				$pos1 = strpos( $tok , " " );
				$pos2 = strpos( $tok , "Up" ) - 1;
			
				$Str1 =  substr($tok,$pos1,$pos2-$pos1 );
				$pos1 = strpos( $Str1 , "@" );
				
				$calleeTmp[$i] = trim(substr($Str1,0,$pos1 ) );
								
				$CallStateTmp[$i] = "Caller";
				
//				echo "$calleeTmp[$i]<br>";
			}
		} // end if( strstr( $tok , "Up" ) != "" )
		$i++;
		$tok = strtok("\r\n");
	} // end while
		
	// check trunk reg
	$response = SipShowRegistry();
	
	$Field_Str = explode( "\n" , $response );

	$i = -1;
	$j = 0;
	$TrunkRegTmp = "";
	while( $Field_Str[++$i] != "" )
	{	
		$StrStatus = chop( $Field_Str[$i] );
	
		if( strstr( $StrStatus , "Reg.Time" ) != "" || 
			strstr( $StrStatus , "Privilege:" ) != "" ||
			strstr( $StrStatus , "ponse:" ) != "" ||
			strstr( $StrStatus , "END COMMAND" ) != "" )
			continue;
			
		if( strstr( $StrStatus , "Registered" ) != "" )
			$TrunkRegTmp = "OK";
		else
			$TrunkRegTmp = "";
	
		$tok = strtok($StrStatus,"  ");
		$k = 0;
		while($tok) 
		{
			if( $k++ == 1 )
			{
				$TrunkTmp = chop($tok);
				for( $j = 0; $j < sizeof( $name ); $j++ )
				{
					if( $TrunkTmp == $name[$j] )
					{

						$name[$j] = $num[$j];
						$num[$j] = $TrunkTmp;
						if( $TrunkRegTmp == "OK" )
							$RegState[$j] = "OK";
						else
							$RegState[$j] = "Unreachable";
						

						
						break;
					}
				}
				break;
			}
			$tok = strtok("  ");
		}
	}
	
	
	logout();	
		
	if( $PLATFORM == 1 )
	{
		for( $i = 1;$i <= 8; $i++ )
			array_push( $RegState , "OK" );
	}
	

	$NameArray = array();
	$NumArray = array();
	$IPArray = array();
	$RegArray = array();
	$CalleeArray = array();
	$CallStateArray = array();
	

	for( $i = 0;$i < $MAX_PEERS; $i++ )
	{
		$bMatch = 0;
		reset( $Channels ); 
		while( list($key , $val) = each($Channels) )
		{	
			if( $val == $num[$i] || $val == $name[$i] )
			{  // talking 
				array_push( $NameArray , $name[$i] );
				array_push( $NumArray , $num[$i] );
				array_push( $IPArray , $IP[$i] );
				array_push( $RegArray , $RegState[$i] );
				array_push( $CalleeArray , $calleeTmp[$key] );
				array_push( $CallStateArray , $CallStateTmp[$key] );
				$bMatch = 1;
			}

		}
		
		if( !$bMatch )
		{ // only registered
			array_push( $NameArray , $name[$i] );
			array_push( $NumArray , $num[$i] );
			array_push( $IPArray , $IP[$i] );
			array_push( $RegArray , $RegState[$i] );
			array_push( $CalleeArray , "" );
			array_push( $CallStateArray , "" );
		}
	}	
	
	
/*
	for( $i = 0;$i < $MAX_PEERS; $i++ )
	{
		if( $Channels[$i] == "" )
			continue;
		for( $j = 0; $j < $MAX_PEERS; $j++ )
		{
			if( $Channels[$i] == $num[$j] )
			{
				echo "$Channels[$i]<br>";
				$callee[$j] = chop($calleeTmp[$i]);
				$CallState[$j] = $CallStateTmp[$i];
				
				break;
			}
		}			
	}	
*/
	
?>

<html>
<head>


<meta http-equiv="expires" content="text/html; charset=UTF-8">

<title>Streaming ServeR</title>

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
</style>

<script language='javascript'>
var g_SelArray = new Array(0, 0, 0, 0, 0);
var FieldArray = new Array("name","num","IP","RegState","callee","CallState");
var ColREG = "#66FF66";
var ColUNKNOW = "#FFCC99";
var	ColCaller = "#33FFCC";
var	ColCalldee = "#CCCC00";

function OMOver(OMO){OMO.style.backgroundColor='<? echo $MainFieldOverColor ?>';}
function OMOut(OMO){OMO.style.backgroundColor='<? echo $MainFieldBackGround ?>';}


function DrawColor()
{
//	window.history.go(0)
//	var timerID;
//	timerID = setTimeout("window.history.go(0)",3000 );
	var i = 0;
	var j = 0;

	while( document.getElementById(FieldArray[3]+j) )
	{
		var SetColor = ColREG;
		
		if( document.getElementById(FieldArray[5] + j).value != "" )
		{
			if( document.getElementById(FieldArray[5] + j).value == "Caller" )
				SetColor = ColCaller;
			else if( document.getElementById(FieldArray[5] + j).value == "Calldee" )
				SetColor = ColCalldee;
		}	
		else if( document.getElementById(FieldArray[3] + j).value == "Unreachable" )
			SetColor = ColUNKNOW;
		else
			SetColor = ColREG;
			
		for( i = 0; i < FieldArray.length; i++ )
			document.getElementById(FieldArray[i] + j).style.backgroundColor = SetColor; 
		j++;
		
	}
	

}
function SortBtn(msg) 
{
	var StrField;
	var i = 0;
	var j = 0;

	var Field2D = new Array();
	
	while( document.getElementById(FieldArray[msg]+j) )
	{
		var tmp = new Array(FieldArray.length);
		for( i = 0; i < FieldArray.length; i++ )
		{
			tmp[i] = document.getElementById(FieldArray[i]+j).value;
		}
		Field2D.push(tmp);
		j++;
	}	
	
	if( !g_SelArray[msg] )
		Field2D.sort(function(left,right){return left[msg]>right[msg]?1:-1});
	else
		Field2D.sort(function(left,right){return left[msg]>right[msg]?-1:1});

	
	for( j = 0; j <  Field2D.length; j++ )
		for( i = 0; i < FieldArray.length; i++ )
			document.getElementById(FieldArray[i] + j).value = Field2D[j][i];

	g_SelArray[msg] = !g_SelArray[msg]; 
	
	DrawColor();
}
</script>

</head>

<body language=javascript onload="DrawColor()">

  <form name="form" method="POST" enctype="multipart/form-data" action="<? $PHP_SELF ?>">
  <?
	echo "<p align='left' style=$MainTitleStyle color='$MainTitleTextColor'>&nbsp;&nbsp;";
	echo $WORDLIST['status'][$LANG];
	echo "</p>";

?>
  <table width="70%" border="0" >
  <tr>
  <th scope="row" width="18" ><input type='text' readonly="true" style="background-color:#66FF66;width :100%;" ></th>
  <th><div align="left" style="color:<? echo $MainTitleTextColor ?>" ><? echo $WORDLIST['regsucc'][$LANG] ?></div></th>
  <th scope="row" width="18" ><input type='text' readonly="true" style="background-color:#FFCC99;width :100%;" ></th>
  <th><div align="left" style="color:<? echo $MainTitleTextColor ?>" ><? echo $WORDLIST['regfail'][$LANG] ?></div></th>
  <th scope="row" width="18" ><input type='text' readonly="true" style="background-color:#33FFCC;width :100%;" ></th>
  <th><div align="left" style="color:<? echo $MainTitleTextColor ?>" ><? echo $WORDLIST['Caller'][$LANG] ?></div></th>
  <th scope="row" width="18" ><input type='text' readonly="true" style="background-color:#CCCC00;width :100%;" ></th>
  <th><div align="left" style="color:<? echo $MainTitleTextColor ?>" ><? echo $WORDLIST['Callee'][$LANG] ?></div></th>
  </tr>
  </table>
  

  
  <table width="100%" border="1" text="#000099">

	<tr>
	<td width='20%' font style='cursor: hand' align='center' style='<? echo $MainFieldStyle ?>; padding:5px' onClick=javascript:SortBtn(0); onmouseover=OMOver(this); onmouseout=OMOut(this);><? echo $WORDLIST['ex-name'][$LANG] ?></font></td>
	<td width='15%' font style='cursor: hand' align='center' style='<? echo $MainFieldStyle ?>; padding:5px' onClick=javascript:SortBtn(1); onmouseover=OMOver(this); onmouseout=OMOut(this);><? echo $WORDLIST['Account-num'][$LANG] ?></font></td>
    <td width='35%' font style='cursor: hand' align='center' style='<? echo $MainFieldStyle ?>; padding:5px' onClick=javascript:SortBtn(2); onmouseover=OMOver(this); onmouseout=OMOut(this);><? echo $WORDLIST['reg-ip'][$LANG] ?></font></td>
    <td width='10%' font style='cursor: hand' align='center' style='<? echo $MainFieldStyle ?>; padding:5px' onClick=javascript:SortBtn(3); onmouseover=OMOver(this); onmouseout=OMOut(this);><? echo $WORDLIST['reg-status'][$LANG] ?></font></td>
    <td width='20%' font style='cursor: hand' align='center' style='<? echo $MainFieldStyle ?>; padding:5px' onClick=javascript:SortBtn(4); onmouseover=OMOver(this); onmouseout=OMOut(this);><? echo $WORDLIST['talking num'][$LANG] ?></font></td>


<?php

/*
		echo "<td width='20%' align='center' style='<? echo $MainFieldStyle ?>; padding:5px'>名稱&nbsp&nbsp<input name='btnname' type='button' value='↓' onClick='javascript:SortBtn($BtnSel[0])'></td>";
		echo "<td width='15%' align='center' style='<? echo $MainFieldStyle ?>; padding:5px'>號碼&nbsp&nbsp<input name='btnname' type='button' value='↓' onClick='javascript:SortBtn($BtnSel[1])'></td>";
	        echo "<td width='35%' align='center' style='<? echo $MainFieldStyle ?>; padding:5px'>註冊IP位址 公網/私網&nbsp&nbsp<input name='btnname' type='button' value='↓' onClick='javascript:SortBtn($BtnSel[2])'></td>";
	        echo "<td width='10%' align='center' style='<? echo $MainFieldStyle ?>; padding:5px'>註冊狀態&nbsp&nbsp<input name='btnname' type='button' value='↓' onClick='javascript:SortBtn($BtnSel[3])'></td>";
	        echo "<td width='20%' align='center' style='<? echo $MainFieldStyle ?>; padding:5px'>通話對像</td>";
*/    
	    echo "</tr>";
	
		

		for( $i = 0; $i < sizeof($NumArray); $i++ )
		{
			echo "<tr>";
			echo "<td><input type='text' readonly='true' name='name$i' value='$NameArray[$i]'  style='color: #000000; width :100%;'></td>";
			echo "<td><input type='text' readonly='true' name='num$i' value='$NumArray[$i]'  style='color: #000000; width :100%;'></td>";
			echo "<td><input type='text' readonly='true' name='IP$i' value='$IPArray[$i]'  style='color: #000000; width :100%;'></td>";
			echo "<td><input type='text' readonly='true' name='RegState$i' value='$RegArray[$i]'  style='color: #000000; width :100%;'></td>";
			echo "<td><input type='text' readonly='true' name='callee$i' value='$CalleeArray[$i]'  style='color: #000000; width :100%;'></td>";
			echo "<td><input name='CallState$i' type='hidden' value='$CallStateArray[$i]' ></td>";
			echo "</tr>";
			
		}


//	ob_end_flush();	
?>	  
 

</table>

<br>

</form>

</body>
</html>

