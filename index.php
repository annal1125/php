<?php
include "filepath.php";

	$DISPLAY = "AVA Media PBX";
	switch( $PLATFORM )
	{
	case 0: // AVA
		switch( $SUBFORM )
		{
		case 1: $DISPLAY = "AVA Media PBX"; break;
		case 2: $DISPLAY = "AVA MCU Server"; break;
		case 3: $DISPLAY = "AVA Media Server"; break;
		}
		break;
	case 1: // CEI
		switch( $SUBFORM )
		{
		case 1: $DISPLAY = "CEI IPX-100"; break;
		case 2: $DISPLAY = "CEI IP-DID"; break;
		}
		break;
	case 2: // CHT
		switch( $SUBFORM )
		{
		case 1: $DISPLAY = "AVA Media Server"; break;
		case 2: $DISPLAY = "AVA VXML Server"; break;
		}
		break;
	case 3: // Planet
		$DISPLAY = "PLANET MCU Management";
		$BGColor = "#EAEAEA";
		break;
	case 4:	// Hit Point
		switch( $SUBFORM )
		{
		case 1: $DISPLAY = "HIT POINT PBX"; break;
		case 2: $DISPLAY = "HIT POINT MCU"; break;
		}
		break;
	}


?>

<html>
<head>
<meta HTTP-EQUIV=Content-Type CONTENT="text/html;">
<title><? echo $DISPLAY ?></title>

<style type="text/css">
<!--
body {
        background-color: <? echo $MainBackGroundColor ?>;
		
}
body,td,th {
	color: #FFFFFF;
}
.style6 {font-size: xx-large}
<?
	if( $PLATFORM == 3 )
	{
		echo "body";
		echo "{";
		echo "background-image: url('MCU-Login.jpg');";
		echo "background-color:#213969;";
		echo "background-repeat: no-repeat;";
		echo "background-position:center";
		echo "}";	
		
		echo "TD {";
		echo "COLOR: #ffffff";
		echo "}";
		echo "TH {";
		echo "COLOR: #ffffff";
		echo "}";
		echo "body,td,th {";
		echo "font-size: 12px;";
		echo "font-weight: bold;";
		echo "}";
	}
?>
-->
</style>
</head>



<body>
<form action="login.php" method="post">


<?

	
	if( $PLATFORM == 3 )
	{	
		echo "<br><br><br>";
		echo "<table width='500' height='271' border='0' align='center' cellpadding='0' cellspacing='0'>";
		echo "<tr>";
		echo "<td width='500' background='Login-bg.jpg'><br>";
		echo "<br><br><br><br><br><br>";
		echo "<TABLE width=300 align=center border=0>";
		echo "<TBODY><TR>";
        echo "<TH width='32%' align='right' scope=row><font color='#333333' face='Arial, Helvetica, sans-serif'>User Name</font></TH>";
        echo "<TD width='68%'>";
        echo '<DIV align=center><INPUT style="WIDTH: 100%; BACKGROUND-COLOR: #e9e9e9" name=usrN></DIV>';
	    echo "</TD></TR><TR>";
        echo '<TH align="right" scope=row><font color="#333333" face="Arial, Helvetica, sans-serif">Password</font></TH>';
        echo '<TD><DIV align=center><INPUT style="WIDTH: 100%; BACKGROUND-COLOR: #e9e9e9" type=password name=pwdD></DIV></TD>';
		echo "</TR></TBODY></TABLE></td></tr></table>";
	
		echo '<CENTER><INPUT type=submit value="Login">';
		echo '</CENTER>';
	


	}
	else
	{
		echo "<br><br><br><br><br><br>";
		echo "<TABLE width=100%;>";
		echo "<TR><TD style='FILTER: glow(color:$IndexColor,strength=13);color: #ffffff'>";
		echo "<div align='center' class='style6'>$DISPLAY</div></TD>";
		echo "</TR></TABLE><br><br>";

		echo "<table width='200' border='0' align='center'>";
		echo "<tr>";
		echo "<th width='40%' scope='row' style='color:$MainTitleTextColor;'>";
		echo $WORDLIST['Account'][$LANG];
		echo "</th>";
		echo "<td><div align='center' ><input type='text' name='usrN' STYLE='background-color:$MainEditBackGround;width :100%'></div></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<th scope='row' style='color:$MainTitleTextColor;'>";
		echo $WORDLIST['Password'][$LANG];
		echo "</th>";
		echo "<td><div align='center' ><input type='password' name='pwdD' STYLE='background-color:$MainEditBackGround;width :100%'></div></td>";
		echo "</tr>";
		echo "</table>";
		echo "<br><br><center><input type='submit' value=' Login '></center>";
		
		
	}
?> 

  

</form> 

</body>


</html>
