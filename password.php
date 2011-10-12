<?php
	if($_COOKIE['login'] != "yes")
	{
		header('Location: index.php');
		exit;	
	}
	include "filepath.php";
	

	if(isset($_POST[Submit]))
	{
		if( $newusr != ""  )
		{ // normal user
			if(	$newpwd == $cpwd )
			{
				unlink( $WEB_USER_PATH );
				$fp = fopen( $WEB_USER_PATH , "w+");
				$StrTotal = sprintf("%s=%s\n",$newusr,$newpwd);
				fwrite( $fp , $StrTotal );
				fclose( $fp );
				echo "<Script language='JavaScript'> alert('";
				echo $WORDLIST['modifysucc'][$LANG];
				echo "');</Script>";
			}
			else{
				echo "<Script language='JavaScript'> alert('";
				echo $WORDLIST['worngpwd'][$LANG];
				echo "');</Script>";
			}
		}
		else
		{
			echo "<Script language='JavaScript'> alert('";
			echo $WORDLIST['accountnull'][$LANG];
			echo "');</Script>";	
		}			
	}	
	
?>


<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">

<title>Passwords</title>



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
	echo $WORDLIST['password'][$LANG];
	echo "</p>";
?>

<br><br><br>

<br><br> 
<table width="600" border="0" align="center">
<tr><th scope="row" width="50%" font style="<? echo "color:$MainTitleTextColor"; ?>; padding:5px"><? echo $WORDLIST['Account'][$LANG] ?></font></th>
<td><div align="left" width="50%" ><input type="text" name="newusr" STYLE="background-color:<? echo $MainEditBackGround ?>;width :100%"></div></td>
</tr>
<tr><th scope="row"><div align="center" ><font style="<? echo "color:$MainTitleTextColor"; ?>; padding:5px"><? echo $WORDLIST['newpwd'][$LANG] ?></font></div></th>
<td><div align="center" ><input type="password" name="newpwd" STYLE="background-color:<? echo $MainEditBackGround1 ?>;width :100%"></div></td>
</tr>
<tr><th scope="row"><div align="center" ><font style="<? echo "color:$MainTitleTextColor"; ?>; padding:5px"><? echo $WORDLIST['confirmpwd'][$LANG] ?></font></div></th>
<td><div align="center" ><input type="password" name="cpwd" STYLE="background-color:<? echo $MainEditBackGround1 ?>;width :100%"></div></td>
</tr>
</table>

<br><br>

<p align="center"> 
<input type='submit' name='Submit' value="<? echo $WORDLIST['submit'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
<input type="submit" name="Cancel" value="<? echo $WORDLIST['cancel'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> </td>
</p>

</form>
</body>


</html>
