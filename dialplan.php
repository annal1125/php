
<?php
	if($_COOKIE['login'] != "yes")
	{
		header('Location: index.php');
		exit;
	
	}
	
	include "filepath.php";
	
	$TRUNK_EX = "$BASE_AST/ex_trunk.conf";

	
	$CEI_KEY = "CEI";
	$IP_PHONE_KEY = "IP_Phone";
	$MCU = "MCU";
	$DISA_KEY = "DISA";
	$E1 = "E1";
	
	if( $PLATFORM == 1 )
	{
		if( $SUBFORM == 1 )
			$TrunkAlias = array ("Disable",$CEI_KEY,$IP_PHONE_KEY,$DISA_KEY);
		else
			$TrunkAlias = array ("Disable",$CEI_KEY );
	}
	else
	{
		$TrunkAlias = array ("Disable",$IP_PHONE_KEY);
		if( $E1_SUPPORT )
			array_push( $TrunkAlias , $E1 );
		if( $MCU_SUPPORT )
			array_push( $TrunkAlias , $MCU );
	}
	
	// get trunk 
	$content_array = file( $SIPTRUNK_PATH );
	$i = 0;

	while( $content_array[$i] != "" )
	{
		if( strstr($content_array[$i] , "[") != "" )
		{
			$StrTep = chop( substr($content_array[$i], 1 , -2 ) );
			
			if( $StrTep != "" )
				array_push( $TrunkAlias , $StrTep );		
		}
		$i++;	
	}
	$content_array = file( $SIPTRUNK_PATH_IAX );
	$i = 0;

	$iaxtmp = NULL;
	
	while( $content_array[$i] != "" )
	{
		if( strstr($content_array[$i] , "[") != "" )
		{
			$StrTep = chop( substr($content_array[$i], 1 , -2 ) );
			
			if( $StrTep != "" ){
				array_push( $TrunkAlias , $StrTep );	
				$iaxtmp[$StrTep] = true;
			}
				
		}
		$i++;	
	}
	
	if($_POST['sub'])
	{
		unlink($TRUNK_EX);
		$fp = fopen( $TRUNK_EX , "w+");	
		
		$bTitle = 0;
		for( $i = 0; $i < $MAX_DIAL_PLAN; $i++ )
		{		
			$prefix = sprintf("prefix%d",$i);
			$prefix = $$prefix;
			$add = sprintf("add%d",$i);
			$add = $$add;
			$drop = sprintf("drop%d",$i);
			$drop = $$drop;
			$Primary_routeT = sprintf("Primary_route%d",$i);
			$Primary_routeT = $$Primary_routeT;
			$dialdelay = sprintf("dialdelay%d",$i);
			$dialdelay = $$dialdelay;			
			
			if( $prefix == "" || $Primary_routeT == "Disable" )
				continue;
			
			$StrTotal = "";
			if( $bTitle == 0 )
			{
				$StrTotal .= "[ava_media]\n";
				$bTitle = 1;
			}
				
		// _02XX,n,Dial(SIP/9${EXTEN:2}@Annal_Test,,tT)
		// _02XX,n,Dial(SIP/9${EXTEN:2},,tT)
		// _02XX,n,Dial(SIP/9@Annal_Test,,tTD(www${EXTEN:2}))
		// _7.,1,Dial(IAX2/test/${EXTEN})
	    // _5565,1,Dial(ZAP/G1/123${EXTEN:5},,rtTD(www${EXTEN:2}))
/*			
			if( $Primary_routeT == $IP_PHONE_KEY )
			{
				$StrTotal .= "exten => _$prefix,1,Dial(SIP/$add\${EXTEN";
				if( $drop != "" )
					$StrTotal .= ":$drop";
				$StrTotal .= "},,rtT)\n";
			}		
	*/		
			switch( $Primary_routeT )
			{
			case $MCU:
				$StrTotal .= "exten => _$prefix,1,Dial(Local/$add\${EXTEN";
				if( $drop != "" )
					$StrTotal .= ":$drop";
				$StrTotal .= "}@$Primary_routeT,,rtT)\n";
				break;
			case $E1:
				if( $dialdelay > 0 )
				{  // _5565,1,Dial(ZAP/G1/123,,rtTD(www${EXTEN:2}))
					$StrTotal .= "exten => _$prefix,1,Dial(ZAP/G1/$add,,rtTD(";
					$StrTotal .= str_repeat( "w", $dialdelay * 2 );
					$StrTotal .= "\${EXTEN";
					if( $drop != "" ) $StrTotal .= ":$drop";
					$StrTotal .= "}))\n";
				}
				else
				{
					$StrTotal .= "exten => _$prefix,1,Dial(ZAP/G1/$add\${EXTEN";
					if( $drop != "" ) $StrTotal .= ":$drop";
					$StrTotal .= "},,rtT)\n";
				}
				break;
			default:
//				$StrTotal .= sprintf("exten => _%s,1,Answer\n",$prefix);
//				$StrTotal .= sprintf("exten => _%s,n,Dial(SIP/%s",$prefix , $add );
				if( $iaxtmp[$Primary_routeT] )
				{
					$StrTotal .= "exten => _$prefix,1,Dial(IAX2/$Primary_routeT/$add\${EXTEN";
					if( $drop != "" )
						$StrTotal .= ":$drop";
					$StrTotal .= "})\n";
				}
				else
				{ 	// _02XX,n,Dial(SIP/9@Annal_Test,,tTD(www${EXTEN:2}))
					// _02XX,n,Dial(SIP/9${EXTEN:2}@Annal_Test,,tT)
					
					if( $dialdelay > 0 )
					{  // _5565,1,Dial(ZAP/G1/123,,rtTD(www${EXTEN:2}))
						$StrTotal .= "exten => _$prefix,1,Dial(SIP/$add";
						if( $Primary_routeT != $IP_PHONE_KEY )
							$StrTotal .= "@$Primary_routeT";
						$StrTotal .= ",,rtTD(";
						$StrTotal .= str_repeat( "w", $dialdelay * 2 );
						$StrTotal .= "\${EXTEN";
						if( $drop != "" ) $StrTotal .= ":$drop";
						$StrTotal .= "}))\n";
					
					}	
					else
					{
						$StrTotal .= "exten => _$prefix,1,Dial(SIP/$add\${EXTEN";
						if( $drop != "" )
							$StrTotal .= ":$drop";
						$StrTotal .= "}";
						if( $Primary_routeT != $IP_PHONE_KEY )
							$StrTotal .= "@$Primary_routeT";
						$StrTotal .= ",,rtT)\n";
					}
				}
				break;
			}
			$StrTotal .= "exten => _$prefix,n,Goto(DialState,s-\${DIALSTATUS},1)\n";
			$StrTotal .= "exten => _$prefix,n,HangUp\n";
			
			fwrite( $fp , $StrTotal );						
		}		
		
		fclose( $fp );
		
		shell_exec( "asterisk -rx reload" );
		
		
	}// end if(isset($_POST[Submit]))

	// init data
	$prefix	= "";
	$add	= "";
	$drop	= "";
	$Primary_route	= "";
	
	
	$content_array = file( $TRUNK_EX );
	$i = 0;
	$j = 0;

	while( $content_array[$i] != "" )
	{
//		echo "$content_array1[$i]<br>";
		if( strstr($content_array[$i] , "[" ) != "" )
		{
			$i++;
			continue;
		}
		// _02XX,n,Dial(SIP/9${EXTEN:2}@Annal_Test,,tT)
		// _02XX,n,Dial(SIP/9${EXTEN:2},,tT)
		// _02XX,n,Dial(SIP/9@Annal_Test,,tTD(www${EXTEN:2}))
		// _7.,1,Dial(IAX2/test/${EXTEN})
	    // _5565,1,Dial(ZAP/G1/123${EXTEN:5},,rtTD(www${EXTEN:2}))
		
		if( strstr($content_array[$i] , "Dial(SIP" ) != "" || 
			strstr($content_array[$i] , "Dial(Local" ) != "" )
		{	
			$postmp = strpos( $content_array[$i] , "=> " ) + 4; // drop '_' and  ',' 
			$ParseStr1 = substr( $content_array[$i] , $postmp  ,  strpos( $content_array[$i] , "," ) - $postmp );
			$prefix[$j] = $ParseStr1;
			
			if( ( $iDelay = strpos( $content_array[$i] , "D(" ) ) == false )
			{ // no delay
			  // _02XX,n,Dial(SIP/9${EXTEN:2}@Annal_Test,,tT)
				$dialdelay[$j] = 0;
				$Strtmp1 = substr( $content_array[$i] ,  strpos( $content_array[$i] , "EN") );
				if( strpos( $Strtmp1 , ":" ) )
					$drop[$j] = substr( $Strtmp1 , 3 , strpos( $Strtmp1 , "}" ) -3 );				
					
				$Strtmp = substr( $content_array[$i] , strpos($content_array[$i] , "Dial(") + 5  );	
				
				if( ($postmp = strpos( $Strtmp , "@" )) == false )
				{ // _02XX,n,Dial(SIP/987${EXTEN:2},,rtT))
					$Strtmp = substr( $Strtmp , strpos( $Strtmp , "/" ) );
					$add[$j] = substr( $Strtmp , 1 , strpos( $Strtmp , "$" ) - 1 );
					$Primary_route[$j] = $IP_PHONE_KEY;
				}
				else
				{ // _02XX,n,Dial(SIP/9${EXTEN:2}@Annal_Test,,tT)
				  // SIP/886${EXTEN:1}@SS)
					$Strtmp = substr( $Strtmp , strpos( $Strtmp , "/" ) );
					$add[$j] = substr( $Strtmp , 1 , strpos( $Strtmp , "$" ) - 1 );

					$Strtmp =  substr( $Strtmp , strpos( $Strtmp , "@" ) + 1 );
					if( ($postmp = strpos( $Strtmp , "," )) == false )
					{ // SIP/886${EXTEN:1}@SS)
						$Primary_route[$j] =  substr( $Strtmp , 0 , strpos( $Strtmp , ")" )  );
					}
					else
					{ // _02XX,n,Dial(SIP/9${EXTEN:2}@Annal_Test,,tT)
						$Primary_route[$j] =  substr( $Strtmp , 0 , $postmp );
					}
				}
			}
			else
			{ // 2th dial , need delay
			  // 02XX,n,Dial(SIP/9@Annal_Test,,tTD(www${EXTEN:2}))
				$Strtmp = substr( $content_array[$i] , $iDelay + 2 );
				$Strtmp1 = substr( $Strtmp , 0 , strpos( $Strtmp , "$") );
				$dialdelay[$j] = strlen($Strtmp1) * 0.5;
				$Strtmp1 = substr( $Strtmp ,  strpos( $Strtmp , "N") );
				if( strpos( $Strtmp1 , ":" ) )
					$drop[$j] = substr( $Strtmp1 , 2 , strpos( $Strtmp1 , "}" ) -2 );
				
				$Strtmp = substr( $content_array[$i] , strpos($content_array[$i] , "Dial(") + 5  );
				// echo "$Strtmp" . "<br>";

				if( ($postmp = strpos( $Strtmp , "@" )) == false )
				{ // _02XX,n,Dial(SIP/987,,rtTD(www${EXTEN:2}))
					$Strtmp = substr( $Strtmp , strpos( $Strtmp , "/" ) );
					$add[$j] = substr( $Strtmp , 1 , strpos( $Strtmp , "," ) - 1 );
					$Primary_route[$j] = $IP_PHONE_KEY;
				}
				else
				{ // _02XX,n,Dial(SIP/9@Annal_Test,,tTD(www${EXTEN:2}))
					$Strtmp = substr( $Strtmp , strpos( $Strtmp , "/" ) );
					$Primary_route[$j] = substr( $Strtmp , strpos( $Strtmp , "@" ) + 1, strpos( $Strtmp , "," ) - strpos( $Strtmp , "@" ) - 1);
					$add[$j] = substr( $Strtmp , 1 , strpos( $Strtmp , "@" ) - 1 );
//					 echo "$add[$j] :$Primary_route[$j]" . "<br>";
				}
			}
			$j++;
		}
		else if( strstr($content_array[$i] , "Dial(IAX2/" ) != "" )
		{
			// _7.,1,Dial(IAX2/test/876${EXTEN:2})
			$dialdelay[$j] = 0;
			$postmp = strpos( $content_array[$i] , "=> " ) + 4; // drop '_' and  ',' 
			$ParseStr1 = substr( $content_array[$i] , $postmp  ,  strpos( $content_array[$i] , "," ) - $postmp );
			$prefix[$j] = $ParseStr1;
			$Strtmp = substr( $content_array[$i] , strpos( $content_array[$i] , "l(" ) + 2 );
			$Strtmp1 = substr( $Strtmp , strpos( $Strtmp , "/" ) + 1);

			$Primary_route[$j] = substr( $Strtmp1 ,0, strpos( $Strtmp1 , "/" )  );
			$Strtmp = substr( $Strtmp1 , strlen($Primary_route[$j]) + 1 );
			
			$postmp = strpos( $Strtmp , "$" );
			$add[$j] = substr( $Strtmp , 0 , $postmp );
			if( ($postmp = strpos( $Strtmp , ":" ) ) )
				$drop[$j] = substr( $Strtmp , $postmp + 1 , strpos( $Strtmp , "}" ) - $postmp -1);
		
	//		echo "Annal :$drop[$j]";
			$j++;
		}
		else if( strstr($content_array[$i] , "Dial(ZAP/" ) != "" )
		{ 		 
			$Primary_route[$j] = $E1;
	
			$postmp = strpos( $content_array[$i] , "=> " ) + 4; // drop '_' and  ',' 
			$ParseStr1 = substr( $content_array[$i] , $postmp  ,  strpos( $content_array[$i] , "," ) - $postmp );
			$prefix[$j] = $ParseStr1;
			
			$Strtmp1 = substr( $content_array[$i] ,  strpos( $content_array[$i] , "EN") );
			if( strpos( $Strtmp1 , ":" ) )
				$drop[$j] = substr( $Strtmp1 , 3 , strpos( $Strtmp1 , "}" ) -3 );	

			
			if( ( $iDelay = strpos( $content_array[$i] , "D(" ) ) == false )
			{ // no delay
			  //_5565,1,Dial(ZAP/G1/123${EXTEN:5},,rtT)
				$dialdelay[$j] = 0;
				$Strtmp = substr( $content_array[$i] , strpos( $content_array[$i] , "/" ) + 1 );
				$Strtmp1 = substr( $Strtmp , strpos( $Strtmp , "/" ) + 1 );
				$add[$j] = substr( $Strtmp1 , 0 , strpos( $Strtmp1 , "$" ) );
			}
			else
			{ // _5565,1,Dial(ZAP/G1/123,,rtTD(www${EXTEN:2}))
			
				$Strtmp = substr( $content_array[$i] , $iDelay + 2 );
				$Strtmp1 = substr( $Strtmp , 0 , strpos( $Strtmp , "$") );
				$dialdelay[$j] = strlen($Strtmp1) * 0.5;	
				$Strtmp = substr( $content_array[$i] , strpos( $content_array[$i] , "/" ) + 1 );
				$Strtmp1 = substr( $Strtmp , strpos( $Strtmp , "/" ) + 1 );
				$add[$j] = substr( $Strtmp1 , 0 , strpos( $Strtmp1 , "," ) );

				
			}
			$j++;
			
		}
		
		
	/* 	
		
		
			$Field_Str = explode( " => " , $content_array[$i] );
			// _02XX,n,Dial(SIP/9${EXTEN:2}@Annal_Test,,tTD(www123))
			$ParseStr1 = explode( "," , $Field_Str[1]);
			$prefix[$j] = substr( chop($ParseStr1[0]) , 1 );
			// Dial(SIP/9${EXTEN:2}@Annal_Test,,tTD(www123))
			$ParseStr2 = explode( "/" , $ParseStr1[2]);
			// 9${EXTEN:2}@Annal_Test,,tTD(www123))
			if( strstr( $ParseStr2[1] , "@" ) == "" )
			{ // IP phone route
				$Primary_route[$j]  = $IP_PHONE_KEY;
				$ParseStr4 = explode( "$" , $ParseStr2[1]);	
				// 9        {EXTEN:2}
				$add[$j] = chop( $ParseStr4[0] );
				
				if( strstr( $ParseStr4[1] , "D(" ) != "" )
				{ // get delay time 
					Get2thDialDelayTime( $ParseStr4[1] );
				
				}
				$ParseStr5 = explode( ":" , $ParseStr4[1]);		
				if( $ParseStr5[1] != "" )
				{
					
					$drop[$j] = substr($ParseStr5[1], 0 , -1 );	
				}
			}
			else
			{ // trunk route
//				if( strstr($ParseStr2[1] ,  $DISA_KEY ) != "" )
				{ // disa 
//					$Primary_route[$j]  = $DISA_KEY;
				}
//				else
				{
					$ParseStr3 = explode( "@" , $ParseStr2[1]);	
					// 9${EXTEN:2}         Annal_Test,,tTD(www123))
					$ParseStr3_1 = explode( "," , $ParseStr3[1]);	
					
					$Primary_route[$j] = chop( $ParseStr3_1[0] );
					$ParseStr4 = explode( "$" , $ParseStr3[0]);	
					// 9        {EXTEN:2}
					$add[$j] = chop( $ParseStr4[0] );
					
					$ParseStr5 = explode( ":" , $ParseStr4[1]);

					if( $ParseStr5[1] != "" )
						$drop[$j] = substr($ParseStr5[1], 0 , -1 );	
				}
			}
			$j++;
		}
		else if( strstr($content_array[$i] , "Dial(IAX2/" ) != "" )
		{
			//exten => _7.,1,Dial(IAX2/test/${EXTEN})
			$Field_Str = explode( " => " , $content_array[$i] );
			$ParseStr1 = explode( "," , $Field_Str[1]);
			$prefix[$j] = substr( chop($ParseStr1[0]) , 1 );
			// Dial(IAX2/test/9${EXTEN:2})
			$ParseStr2 = explode( "/" , $ParseStr1[2]);
			$Primary_route[$j] = chop($ParseStr2[1]);
		
			$ParseStr4 = explode( "$" , $ParseStr2[2]);	
				// 9        {EXTEN:2}
			$add[$j] = chop( $ParseStr4[0] );
			$ParseStr5 = explode( ":" , $ParseStr4[1]);		
			if( $ParseStr5[1] != "" )
			{
				$ParseStr6 =  explode( "}" , $ParseStr5[1]);		
				 
				$drop[$j] = $ParseStr6[0];
			}
			$j++;

		}
		else if( strstr($content_array[$i] , "Dial(ZAP/" ) != "" )
		{
			$Field_Str = explode( " => " , $content_array[$i] );
			// _02XX,n,Dial(SIP/9${EXTEN:2}@Annal_Test,,tT)
			$ParseStr1 = explode( "," , $Field_Str[1]);
			$prefix[$j] = substr( chop($ParseStr1[0]) , 1 );
			// Dial(SIP/9${EXTEN:2}@Annal_Test,,tT)
			$ParseStr2 = explode( "/" , $ParseStr1[2]);
			// 9${EXTEN:2}@Annal_Test,,tT)
			
			$Primary_route[$j]  = $E1;
			$ParseStr4 = explode( "$" , $ParseStr2[2]);	
				// 9        {EXTEN:2}
			$add[$j] = chop( $ParseStr4[0] );
			$ParseStr5 = explode( ":" , $ParseStr4[1]);		
			if( $ParseStr5[1] != "" )
				$drop[$j] = substr($ParseStr5[1], 0 , -1 );	
			$j++;
			
		}
 */
		$i++;
	}
function Get2thDialDelayTime($str)
{
	// {EXTEN:2}@Annal_Test,,tTD(www123))
	$tm1 = substr( $str , strpos( $str , "D(" ) + 2 , strpos( $str , ")" ) );
	echo "Annal test :$tmp1";

}	
	
function GetTrunkIndex($Trunk)
{
	global $TrunkAlias;
	
	if( $Trunk != "" )
		echo "<option value=$Trunk>$Trunk</option>";
	
	for( $j = 0; $j < sizeof($TrunkAlias); $j++ )
	{
		if( $TrunkAlias[$j] == $Trunk )
			continue;
			
		echo "<option value=$TrunkAlias[$j]>$TrunkAlias[$j]</option>";	
	}
}	
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>Streaming ServeR</title>

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
				

	newRowObj.insertCell(newRowObj.cells.length).innerHTML = "<div align='center'>"+len+"</div>";
	
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = "<input type='text' name='prefix"+len+"' value='' style='background-color:#A8E61D; width :100%'>";
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = "<input type='text' name='drop"+len+"' value='' style='background-color:#A8E61D; width:100%'>";
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = 
	"<input type='text' name='add"+len+"' value='' style='background-color:#A8E61D; width:100%'>";
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = "<input type='text' name='dialdelay"+len+"' value='' style='background-color:#A8E61D; width:100%'>";
	
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = 		
	"<? echo '<select name=\'Primary_route'?>" +len+ "<? echo '\' STYLE=\'background-color:#A8E61D;width :100%\' >'; GetTrunkIndex(''); echo '</select>'; ?>";
	

	newRowObj.insertCell(newRowObj.cells.length).innerHTML = 
	"<p align='center'><img src='<? echo $TRASH_PIC ?>' onClick='BtnDel("+len+")' style='cursor: hand; background-color:#A8E61D;'></p>";

	
	
}	
	
function BtnDel(msg)
{
	document.getElementById("prefix"+msg).value = "";
	document.getElementById("drop"+msg).value = "";
	document.getElementById("add"+msg).value = "";
	document.getElementById("dialdelay"+msg).value = 0;


}
function ClickOK()
{
	var tableObj = document.getElementById("mytable");
	var len = tableObj.rows.length;
	
	for(var i=0; i < len-2;i++)
	{
		var delaytime = document.getElementById("dialdelay"+i).value;
		
		if( delaytime == "" ) 
			continue;
		if( delaytime > 0  )
		{
			var a = document.getElementById("add"+i).value;
			if( a == "" )
			{
				document.getElementById("dialdelay"+i).value = 0;
			
			}
		}
	
	}
	






	document.forms[0].sub.value = 1;
	document.forms[0].submit();
}

//-->
</script> 

<style type="text/css">
<!--
body {
        background-color: <? echo $MainBackGroundColor ?>;
}
body,td,th {
	color: <? echo $MainTitleTextColor ?>;
}
.style1 {font-family: Arial, Helvetica, sans-serif}
.style3 {font-family: "Courier New", Courier, monospace}
-->
</style></head>


<body>
<!--
  bgcolor="#f9e6a2"  text="#0906a2" link="#ff6600" vlink="red" align="center">
-->
  <form name="form" method="POST" enctype="multipart/form-data" action="<? $PHP_SELF ?>">
<?
	echo "<p align='left' style=$MainTitleStyle color='$MainTitleTextColor'>&nbsp;&nbsp;";
	echo $WORDLIST['dialplan'][$LANG];
	echo "</p>";



?>
  <table id="mytable" width="100%" border="1">
    <tbody>
	  <tr>
<?
		echo "<tr>";
		echo "<th width='2%'>";
		echo "No.";
		echo "</th>";
	
	    echo "<th width='15%' font style='$MainFieldStyle; padding:5px'>";
		echo $WORDLIST['prefix'][$LANG];
		echo "</font></th>";
		
	    echo "<th width='10%' font style='$MainFieldStyle; padding:5px'>";
		echo $WORDLIST['Drop'][$LANG];
		echo "</font></th>";
		
	    echo "<th width='20%' font style='$MainFieldStyle; padding:5px'>";
		echo $WORDLIST['Add'][$LANG];
		echo "</font></th>";
		
	    echo "<th width='10%' font style='$MainFieldStyle; padding:5px'>";
		echo $WORDLIST['dial_delay'][$LANG];
		echo "</font></th>";		
		
	
	    echo "<th width='30%' font style='$MainFieldStyle; padding:5px'>";
		echo $WORDLIST['Trunk-sel'][$LANG];
		echo "</font></th>";
		
	    echo "<th width='8%' font style='$MainFieldStyle; padding:5px'>";
		echo $WORDLIST['del'][$LANG];
		echo "</font></th>";
       

		for( $i = 0; $i < sizeof($Primary_route); $i++ )
		{
			echo "<tr>";
			echo "<td align='center'>".($i+1)."</td>";			
			echo "<td><input name='prefix$i' type='text' value='$prefix[$i]' STYLE='background-color:$MainEditBackGround;width :100%'></td>";
			echo "<td><input name='drop$i' type='text' maxlength='1' value='$drop[$i]'  style='background-color:$MainEditBackGround1;width :100%'></td>";
			echo "<td><input name='add$i' type='text' value='$add[$i]'  style='background-color:$MainEditBackGround1;width :100%'></td>";
			echo "<td><input name='dialdelay$i' type='text' value='$dialdelay[$i]'  style='background-color:$MainEditBackGround1;width :100%'></td>";
			echo "<td><select name='Primary_route$i' STYLE='background-color:$MainEditBackGround;width :100%' >";
			GetTrunkIndex($Primary_route[$i]);
			echo "</select></td>";
				
			echo "<td align='center' ><img src='$TRASH_PIC' onClick='BtnDel($i)' style='cursor: hand; background-color:#FFCC99;'></td>";
			
			echo "</select></td>";

		}
	
?>	  
 
    </tbody>		  
  </table>
<p><p><p><p><p><p><p><p>

<p align="center"> 
<input name="btnNew" type="button" value="<? echo $WORDLIST['adddnew'][$LANG] ?>" onClick="javascript:Addnew()" style="<? echo $MainBtnStyle ?> ;height:30px;width:60px"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" name="Submit1" onclick=ClickOK(); value="<? echo $WORDLIST['submit'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="Cancel" value="<? echo $WORDLIST['cancel'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> </td>
</p>
<input name="sub" type="hidden" value="" >

<br><br>
</form>

</body>
</html>

