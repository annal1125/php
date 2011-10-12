
<?php
	if($_COOKIE['login'] != "yes")
	{
		header('Location: index.php');
//		echo "<meta http-equiv=refresh content=0;URL='index.html'>";
		exit;
	
	}
	
	include "filepath.php";
	$MOD_PATH = "$BASE_CONF/ava.vlm";

	if(isset($_POST[Submit]))
	{
		unlink($MOD_PATH);
			
		$fp = fopen( $MOD_PATH , "w+");
		// write ch
		for( $i = 0; $i < $MAX_MEDIA_FIELD; $i++ )
		{
			$chnameT = sprintf("ch_name%d",$i);
			$chsrcT = sprintf("ch_src%d",$i);
			$chradioT =  sprintf("ch_radio%d",$i);
			$chselT = sprintf("ch_sel%d",$i);
			$ch_BitrateT = sprintf("ch_Bitrate%d",$i);
			$ch_FramerateT = sprintf("ch_Framerate%d",$i);
			$ACodecT = sprintf("ACodec%d",$i);
			$VCodecT = sprintf("VCodec%d",$i);
			
			if( $$chnameT != "" && 	($$chsrcT != "" || $$chradioT == "1" ) )
			{
				$VSizeT = sprintf("VSize%d",$i);
				
				if( $$VSizeT == "CIF" )
				{
					$viewwidth = "352";
					$viewheight = "288";
				}
				else
				{
					$viewwidth = "176";
					$viewheight = "144";
				}
				
				$StrTemp1 = sprintf("new %s vod\n" , $$chnameT );
				
				if( $$chradioT == "1" || strstr( $$chsrcT , "udp" ) == "") // file
				{
					$chsrcT = rtrim($$chselT);
					$StrTemp2 = sprintf("setup %s input /home/ava_media/movie/%s\n" , $$chnameT,$chsrcT);
				}
				else // internet
					$StrTemp2 = sprintf("setup %s input %s\n" , $$chnameT,$$chsrcT);

				$StrTemp3 = sprintf("setup %s output #transcode{vcodec=%s,vb=%s,fps=%s,scale=1,width=%s,height=%s,acodec=%s,samplerate=8000,channels=1}\n", 
					$$chnameT ,$$VCodecT,$$ch_BitrateT,$$ch_FramerateT,
					$viewwidth,$viewheight,$$ACodecT);
			
				$StrTemp4 = sprintf("setup %s enabled\n" , $$chnameT );
			
				$OutputStr = sprintf("%s%s%s%s\n" , $StrTemp1,$StrTemp2,$StrTemp3,$StrTemp4);

//		echo "OutputStr :$OutputStr<br>";
				fwrite( $fp , $OutputStr );
			}
		}
		fclose( $fp );	
	
		// copy movie file
		if( $userfile_size > 0  )
		{
			copy($userfile, "/home/ava_media/movie/$userfile_name");
		}
		shell_exec( "nohup /home/ava_media/goava.sh >  result.txt &" );

	}// end if(isset($_POST[Submit]))

	// init data
	$content_array1 = file( $MOD_PATH );
	$i = 0;
	$j = 0;
	$k = 0;
	while( $content_array1[$i] != "" )
	{
//		echo "$content_array1[$i]<br>";
		
		$parse_temp1 = explode( " " , $content_array1[$i] );
		
		if( $parse_temp1[2] == "input" )
		{
			$ch_name[$j] = $parse_temp1[1];
			
			if( strstr( $parse_temp1[3] , "/home/" ) == "" )
			{ // internet
				$ch_src[$j] = $parse_temp1[3];
				$ch_radiovalue[$j] = 0;
			}
			else
			{ // movie
				$moviefile = explode ("/movie/", $parse_temp1[3]);
				$ch_src[$j] = rtrim($moviefile[1]);	
				$ch_radiovalue[$j] = 1;
			}
			
			$j++;
		}
		else if( $parse_temp1[2] == "output" )
		{
			// Video codec 
			$Strtemp =  explode("=",$parse_temp1[3]);
			$Strtemp1 = explode( "," , $Strtemp[1] );
			$ch_Vcodec[$k] = $Strtemp1[0];
			
			// bit rate
			$Strtemp1 = explode( "," , $Strtemp[2] );
			$ch_Bitrate[$k] = $Strtemp1[0];
			
			// frame rate
			$Strtemp1 = explode( "," , $Strtemp[3] );
			$ch_Framerate[$k] = $Strtemp1[0];
	
			// size
			$Strtemp1 = explode( "," , $Strtemp[5] );
			$Strtemp1 = $Strtemp1[0];
			if( $Strtemp1 == "352" )
				$ch_Vsize[$k] = "CIF";
			else
				$ch_Vsize[$k] = "QCIF";
				
			// Audio codec
			$Strtemp1 = explode( "," , $Strtemp[7] );
			$ch_Acodec[$k] = $Strtemp1[0];
			$k++;
		}
		$i++;
	}
	
	// init framerate and bitrate value
	for( ;$k < $MAX_MEDIA_FIELD; $k++ )
	{
		$ch_Framerate[$k] = "20";
		$ch_Bitrate[$k] = "384";
	}
	
  
function getmoviefile( $Src )
{
	   $handle=opendir('/home/ava_media/movie/');
	
	   if( $Src != "" )
			 echo "<option value='$Src'>$Src"; 
	
	
	   while ($file = readdir($handle)) 	          		  
	   {
	   	
	   	if( $file == "." || $file == ".."  )
		{
	   		continue;
		}
		 echo "<option value='$file'>$file"; 
	
	   }	   
   	   closedir($handle);		
}	
function getCodec($codec)
{
	if( $codec == "H263" )
	{
		echo "<option value=H263>H263p</option>";
		echo "<option value=h263>H263</option>";
	}
	else
	{
		echo "<option value=h263>H263</option>";
		echo "<option value=H263>H263p</option>";
	}
}
function getVsize($Vsize)
{
	if( $Vsize == "QCIF" )
	{
		echo "<option value=QCIF>QCIF</option>";
		echo "<option value=CIF>CIF</option>";
	}
	else
	{
		echo "<option value=CIF>CIF</option>";
		echo "<option value=QCIF>QCIF</option>";
	}
}
function getACodec($ACodec)
{
	if( $ACodec == "g729" )
	{
		echo "<option value=g729>g729</option>";
		echo "<option value=ulaw>ulaw</option>";
	}
	else
	{
		echo "<option value=ulaw>ulaw</option>";
		echo "<option value=g729>g729</option>";
	}
}

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Streaming ServeR</title>
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
<!--
  bgcolor="#f9e6a2"  text="#0906a2" link="#ff6600" vlink="red" align="center">
-->
  <form name="form" method="POST" enctype="multipart/form-data" action="<? $PHP_SELF ?>">
<br>
  <p align="left" style=FILTER:Shadow(Color=8888ff,direction=150);height=20 color="#0000FF">&nbsp;&nbsp;<? echo $WORDLIST['streaming'][$LANG] ?></p>
  
  <table width="100%" border="1">
    <tbody>
	  <tr>
        <td class="IN_textM" align="center" width="16%" style="filter: glow(color=#9966FF,strength=3); height:10px; color:white; padding:5px"><? echo $WORDLIST['chname'][$LANG] ?></td>
       
        <td class="IN_textM" width="2%"></td>
        <td class="IN_textM" width="28%">
        	<p align="center" style="filter: glow(color=#9966FF,strength=3); height:10px; color:white; padding:5px"><? echo $WORDLIST['source'][$LANG] ?></p></td>
        <td class="IN_textM" width="12%">
        	<p align="center" style="filter: glow(color=#9966FF,strength=3); height:10px; color:white; padding:5px"><? echo $WORDLIST['vcodec'][$LANG] ?></p></td>
        <td class="IN_textM" width="6%">
        <p align="center" style="filter: glow(color=#9966FF,strength=3); height:10px; color:white; padding:5px"><? echo $WORDLIST['FPS'][$LANG] ?></p></td>
        <td class="IN_textM" width="16%">
        <p align="center" style="filter: glow(color=#9966FF,strength=3); height:10px; color:white; padding:5px"><? echo $WORDLIST['Bitrate'][$LANG] ?></p></td>
         <td class="IN_textM" width="8%">
		<p align="center" style="filter: glow(color=#9966FF,strength=3); height:10px; color:white; padding:5px"><? echo $WORDLIST['Size'][$LANG] ?></p></td>
        <td class="IN_textM" width="12%">
		<p align="center" style="filter: glow(color=#9966FF,strength=3); height:10px; color:white; padding:5px"><? echo $WORDLIST['acodec'][$LANG] ?></p></td>

<?php
		for( $i = 0; $i < $MAX_MEDIA_FIELD; $i++ )
		{
			echo "<tr>";
			echo "<td rowspan='2' class='IN_textM'><input name='ch_name$i' type='text' value='$ch_name[$i]' STYLE='background-color:#FFCC99;width :100%'></td>";
			echo "<td class='IN_textM'> <input name='ch_radio$i' type='radio' value='0'"; 
			if(!$ch_radiovalue[$i]) echo "checked";
			echo "></td>";
			echo "<td class='IN_textM'><input name='ch_src$i' type='text' value='$ch_src[$i]'  style='background-color:#FF7979;width :100%'></td>";
			echo "<td rowspan='2' class='IN_textM'><select name='VCodec$i' STYLE='background-color:#FFCC99;width :100%' >";
			getCodec( $ch_Vcodec[$i] );
			echo "</select></td>";
			echo "<td rowspan='2' class='IN_textM'><input name='ch_Framerate$i' type='text' value='$ch_Framerate[$i]' STYLE='background-color:#FF7979;width :100%'></td>";
			echo "<td rowspan='2' class='IN_textM'><input name='ch_Bitrate$i' type='text' value='$ch_Bitrate[$i]' STYLE='background-color:#FFCC99;width :100%'></td>";
			echo "<td rowspan='2' class='IN_textM'><select name='VSize$i' STYLE='background-color:#FF7979;width :100%' >";
			getVsize( $ch_Vsize[$i] );
			echo "</select></td>";
	        echo "<td rowspan='2' class='IN_textM'><select name='ACodec$i' STYLE='background-color:#FFCC99;width :100%' >";
			getACodec( $ch_Acodec[$i] );
			echo "</select></td></tr>";
			echo "<tr><td class='IN_textM'><input name='ch_radio$i' type='radio' value='1'";
			if($ch_radiovalue[$i])  echo "checked ";
			echo "></td>";
			echo "<td class='IN_textM'><select name='ch_sel$i' style='background-color:#FFCC99;width :100%'>";
			if( $ch_radiovalue[$i] ) 
				getmoviefile($ch_src[$i]);
			else
				getmoviefile("");
		
			echo "</select></td>";
			echo "</tr>";
		}
	
?>	  
      <tr> 
        <td class="IN_textM" style="filter: glow(color=#9966FF,strength=3); height:10px; color:white; padding:5px">Movie Upload&nbsp;</td>

        <td class="IN_textM">&nbsp;</td>
        <td class="IN_textM"><input name="MAX_FILE_SIZE" type="hidden" value=""><input name="userfile" type="file" STYLE="background-color:#FFCC99;width=100%">&nbsp;</td>
        <td class="IN_textM"></td>
        <td class="IN_textM"></td>
        <td class="IN_textM"></td>
        <td class="IN_textM"></td>
        <td class="IN_textM"></td>
    </tbody>		  
  </table>
<p><p><p><p><p><p><p><p>

<p align="center"> 
<input type='submit' name='Submit' value='<? echo $WORDLIST['submit'][$LANG] ?>'  style='border: 5px dotted #C0C0C0; background-color: #FFD5AA'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="Cancel" value="<? echo $WORDLIST['cancel'][$LANG] ?>" style="border: 5px dotted #C0C0C0; background-color: #FFD5AA" > </td>
</p>
<br><br>
</form>

</body>
</html>

