<?php
if($_COOKIE['login'] != "yes")
{
	header('Location: index.php');
	exit;
}


include "filepath.php";

extract($_POST,EXTR_OVERWRITE);


	
if(isset($_POST[Submit]))
{
	system("reboot");
	

}	

?>




<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">

<title>Conference</title>

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
	echo $WORDLIST['reboot'][$LANG];
	echo "</p>";

?>
	
<p align="center"> 

<input type='submit' name='Submit' value='<? echo $WORDLIST['reboot'][$LANG] ?>'  style='<? echo $MainBtnStyle ?>'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

</p>

</form>
</body>


</html>
