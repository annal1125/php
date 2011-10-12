<?php
if($_COOKIE['login'] != "yes")
{
	header('Location: index.php');
	exit;
}

include "filepath.php";

	if(isset($_POST[Submit]))
	{
//		echo "Annal :$userfile_size <br>";
//		echo "Annal :$userfile_name <br>";		
		if( $userfile_size <= 0  )
			exit;
		
		if(  substr( $userfile_name , -3 ) == "cfg" )
		{
//			echo "$userfile<br>";
			exec("mv $userfile /$userfile_name");
			if( $PLATFORM == 1 )
				exec( "tar xvf  /$userfile_name -C /" );
			else
				exec( "tar zxf /$userfile_name" );
				
			echo "<Script language='JavaScript'> alert('Import Succssfully');</Script>";			
				
			shell_exec( "asterisk -rx reload" );
		}
		else
		{
			$UPDATEPATH = "$BASE_CONF/update_tmp";

			exec( "rm -rf $UPDATEPATH" );		
			exec( "mkdir -p $UPDATEPATH" );
			copy($userfile, "$UPDATEPATH/$userfile_name");
			exec( "tar zxf $UPDATEPATH/$userfile_name -C $UPDATEPATH" );
			exec( "$UPDATEPATH/update.sh" );
			echo "<Script language='JavaScript'> alert('Upload Succssfully');</Script>";			
		}
	

	}	
	if(isset($_POST[Export]))
	{
		if( $PLATFORM == 1 ){
			exec( "tar cf /var/tmp/config.tar.gz /mnt/app1/ava_pbx/asterisk" );
			header('Location: export_1.php');
		}
		else{
			header('Location: export_0.php');
			exec( "tar zcf /home/config.tar.gz /etc/asterisk" );
		}
		
	}	
	



	$VerInfo = exec("cat $FWVER_PATH");
	
	if( strstr( $VerInfo , "FWVer" ) == "" )
		$VerInfo = "FWVer=0.1";
	
	$Str = explode("=",$VerInfo);

    $FWVer = chop($Str[1]);


	
?>



<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">

<title>upgrade</title>

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
	echo "<div align='left' style=$MainTitleStyle color='$MainTitleTextColor'>&nbsp;&nbsp;";
	echo $WORDLIST['upgrade'][$LANG];
	echo "</div>";
?>

<table width="80%" border="0" p align="center">
 <tr>
 <th width="45%" scope='row'><font style="<? echo $MainFieldStyle ?>; padding:5px"><? echo $WORDLIST['Version'][$LANG] ?></font></th>
 <th width="55%" scope='row' style="color:<? echo $MainTitleTextColor ?>" >Ver <? echo $FWVer; ?></th>
</tr> 

 <tr>
 <th scope='row'><font style="<? echo "color:$MainTitleTextColor"; ?>; padding:5px"><? echo $WORDLIST['fwupgrade-import'][$LANG] ?></font></th>
 <td><input name="MAX_FILE_SIZE" type="hidden" value=""><input name="userfile" type="file" STYLE="background-color:<? echo $MainEditBackGround ?>;width :95%">&nbsp;</td>
</tr> 
 

 <tr>
 <th scope='row'><font style="<? echo "color:$MainTitleTextColor"; ?>; padding:5px"><? echo $WORDLIST['export'][$LANG] ?></font></th>
 <td><input type='submit' name='Export' value="<? echo $WORDLIST['export'][$LANG] ?>" style="width :50%">&nbsp;</td>
 </tr> 

</table>

	
	
<p align="center"> 
<input type='submit' name='Submit' value="<? echo $WORDLIST['submit'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="Cancel" value="<? echo $WORDLIST['cancel'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> </td>
</p>

</form>
</body>


</html>
