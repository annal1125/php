<?php
if($_COOKIE['login'] != "yes")
{
	header('Location: index.php');
	exit;
}
	include "filepath.php";

	
$TimeZoneArray = array( "GMT-12:00" => "<option value='GMT-12:00'>(GMT-12:00) Eniwetok, Kwajalein</option>" ,
	"GMT-11:00" => "<option value='GMT-11:00'>(GMT-11:00) Midway Island, Samoa</option>",
	"GMT-10:00" => "<option value='GMT-10:00'>(GMT-10:00) Hawaii</option>",
    "GMT-09:00" => "<option value='GMT-09:00'>(GMT-09:00) Alaska</option>",
    "GMT-08:00" => "<option value='GMT-08:00'>(GMT-08:00) Pacific Time (US & Canada), Tijuana</option>",
    "GMT-07:00" => "<option value='GMT-07:00'>(GMT-07:00) Mountain Time (US & Canada), Arizona</option>",
    "GMT-06:00" => "<option value='GMT-06:00'>(GMT-06:00) Central Time (US & Canada), Mexico City</option>",
    "GMT-05:00" => "<option value='GMT-05:00'>(GMT-05:00) Eastern Time (US & Canada), Bogota, Lima, Quito</option>",
    "GMT-04:00" => "<option value='GMT-04:00'>(GMT-04:00) Atlantic Time (Canada), Caracas, La Paz</option>",
    "GMT-03:00" => "<option value='GMT-03:00'>(GMT-03:00) Brassila, Buenos Aires, Georgetown, Falkland Is</option>",
    "GMT-02:00" => "<option value='GMT-02:00'>(GMT-02:00) Mid-Atlantic, Ascension Is., St. Helena</option>",
    "GMT-01:00" => "<option value='GMT-01:00'>(GMT-01:00) Azores, Cape Verde Islands</option>",
    "GMT" => "<option value='GMT'>(GMT) Casablanca, Dublin, Edinburgh, London, Lisbon, Monrovia</option>",
    "GMT+01:00" => "<option value='GMT+01:00'>(GMT+01:00) Amsterdam, Berlin, Brussels, Madrid, Paris, Rome</option>",
    "GMT+02:00" => "<option value='GMT+02:00'>(GMT+02:00) Cairo, Helsinki, Kaliningrad, South Africa</option>",
    "GMT+03:00" => "<option value='GMT+03:00'>(GMT+03:00) Baghdad, Riyadh, Moscow, Nairobi</option>",
    "GMT+03:30" => "<option value='GMT+03:30'>(GMT+03:30) Tehran</option>",
    "GMT+04:00" => "<option value='GMT+04:00'>(GMT+04:00) Abu Dhabi, Baku, Muscat, Tbilisi</option>",
    "GMT+04:30" => "<option value='GMT+04:30'>(GMT+04:30) Kabul</option>",
    "GMT+05:00" => "<option value='GMT+05:00'>(GMT+05:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>",
    "GMT+05:30" => "<option value='GMT+05:30'>(GMT+05:30) Bombay, Calcutta, Madras, New Delhi</option>",
    "GMT+05:45" => "<option value='GMT+05:45'>(GMT+05:45) Katmandu</option>",
    "GMT+06:00" => "<option value='GMT+06:00'>(GMT+06:00) Almaty, Colombo, Dhaka, Novosibirsk</option>",
    "GMT+06:30" => "<option value='GMT+06:30'>(GMT+06:30) Rangoon</option>",
    "GMT+07:00" => "<option value='GMT+07:00'>(GMT+07:00) Bangkok, Hanoi, Jakarta</option>",
    "GMT+08:00" => "<option value='GMT+08:00'>(GMT+08:00) Taipei,Beijing, Hong Kong, Perth, Singapore</option>",
    "GMT+09:00" => "<option value='GMT+09:00'>(GMT+09:00) Osaka, Sapporo, Seoul, Tokyo, Yakutsk</option>",
    "GMT+09:30" => "<option value='GMT+09:30'>(GMT+09:30) Adelaide, Darwin</option>",
    "GMT+10:00" => "<option value='GMT+10:00'>(GMT+10:00) Canberra, Guam, Melbourne, Sydney, Vladivostok</option>",
    "GMT+11:00" => "<option value='GMT+11:00'>(GMT+11:00) Magadan, New Caledonia, Solomon Islands</option>",
    "GMT+12:00" => "<option value='GMT+12:00'>(GMT+12:00) Auckland, Wellington, Fiji, Marshall Island</option>" );
	
	$NTP_PATH = "/mnt/app1/ntp/TZ"; 
	
	
	if(isset($_POST[Submit]))
	{
		$TimeTZ = $zone;
		$Str1 = substr($TimeTZ , 3,1 );
	
		if( $Str1 == "+" )
			$TimeTZ = str_replace($Str1, "-", $TimeTZ);
		else if( $Str1 == "-" )
			$TimeTZ = str_replace($Str1, "+", $TimeTZ); 

		$TimeTZ .= "\n";
		
	
		unlink($NTP_PATH);
		$fp = fopen( $NTP_PATH , "w+");	
		fwrite( $fp , $TimeTZ );
		fclose( $fp );
	}



	$TimeTZ = chop(exec("cat $NTP_PATH"));
	$Str1 = substr($TimeTZ , 3,1 );
	
	if( $Str1 == "+" )
		$TimeTZ = str_replace($Str1, "-", $TimeTZ);
	else if( $Str1 == "-" )
		$TimeTZ = str_replace($Str1, "+", $TimeTZ); 

	
	
function ShowTime()
{
	$Nowtime = exec("date");
	echo "$Nowtime";
}
function InitTimeZone()
{
	global $TimeZoneArray;
	global $TimeTZ;
		
	echo $TimeZoneArray[$TimeTZ];
	
	while(list($key , $val) = each($TimeZoneArray)) 
	{
		if( $key == $TimeTZ )
			continue;
		echo $val;
	}
}
?>




<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=big5">

<title>Conference</title>

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


<body >
<form name="form" method="POST" enctype="multipart/form-data" action="<? $PHP_SELF ?>">
<?
	echo "<div align='left' style=$MainTitleStyle color='$MainTitleTextColor'>&nbsp;&nbsp;";
	echo $WORDLIST['time'][$LANG];
	echo "</div>";
?>

<br>
<table width="70%" border="0" align="center">

<tr>
<th width="20%" scope="row"><font style="<? echo "color:$MainTitleTextColor"; ?>; padding:5px"><? echo $WORDLIST['timezoneset'][$LANG] ?></font></th>
<td  width="80%" ><select name="zone" STYLE="background-color:<? echo $MainEditBackGround ?>;width :100%" >
	<? InitTimeZone() ?>
   </select></td>
   </tr>
	<th scope="row"><font style=""<? echo "color:$MainTitleTextColor"; ?>; padding:5px"><? echo $WORDLIST['nowtime'][$LANG] ?></font></th>
	<td scope="row"><? ShowTime();?></td>

	</tr>
	
</table>

	
<p align="center"> 
<?
//if($_COOKIE['login_s'] != "yes" )
//	$disablekey = "disabled='disabled'";
echo "<input type='submit' name='Submit' $disablekey value=' 確 定 '  style='border: 5px dotted #C0C0C0; background-color: #FFD5AA'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
?>
</p>

</form>
</body>


</html>
