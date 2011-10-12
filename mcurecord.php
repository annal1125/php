<?php
	if($_COOKIE['login'] != "yes")
	{
		header('Location: index.php');
		exit;	
	}
	include "filepath.php";
	
	$REC_PATH = "/var/www/html/mcurecord";
	
	if($_POST['del'] != "" )
	{
		system("rm -f /var/www/html/mcurecord/$del");
	}	

	// init param
	$handle=opendir($REC_PATH);
	$RecFile = array();
	$FilesSize = array();
	
	while ($file = readdir($handle)) 	          		  
	{
		if( $file == "." || $file == ".."  )
			continue;
		$type = filetype( $REC_PATH."/".$file );

		if( $type == "file" )
		{
			array_push( $RecFile , $file );
			$size = filesize( $REC_PATH."/".$file );
			$size /= 1024;
			
			$size = sprintf( "%0.2f" , $size );
			
			array_push( $FilesSize ,  $size );
		}
	}	   
	closedir($handle);	
	

?>


<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">

<title>mcurecord</title>
<script language = "javascript">
<!--
function BtnDel(msg)
{
	document.forms[0].del.value = msg;
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
	color: #FFFFFF;
}
.style1 {font-family: Arial, Helvetica, sans-serif}
.style3 {font-family: "Courier New", Courier, monospace}
-->
</style>

</head>

<body>
<form name="form" method="POST" enctype="multipart/form-data" action="<? $PHP_SELF ?>">
<?
	echo "<p align='left' style=$MainTitleStyle color='$MainTitleTextColor'>&nbsp;&nbsp;";
	echo $WORDLIST['mcu-files'][$LANG];
	echo "</p>";
?>
	

<p>  
<table width="100%" border="1" align="center">

<?
	echo "<tr>";
	echo "<th width='30%' scope='col' font style='$MainFieldStyle; padding:5px'>"; 
	echo $WORDLIST['files'][$LANG];
	echo "</font></th>";
	
	echo "<th width='10%' scope='col' font style='$MainFieldStyle; padding:5px'>"; 
	echo $WORDLIST['file-size'][$LANG];
	echo "  (Kb)</font></th>";
	
	echo "<th width='5%' scope='col' font style='$MainFieldStyle; padding:5px'>"; 
	echo $WORDLIST['download'][$LANG];
	echo "</font></th>";
	
	echo "<th width='5%' scope='col' font style='$MainFieldStyle; padding:5px'>"; 
	echo $WORDLIST['del'][$LANG];
	echo "</font></th>";

	echo "</tr>";
	
	reset( $RecFile );
	reset( $FilesSize );
	
	if( $PLATFORM == 3 )
		$trash = "planet_trash.jpg";
	else
		$trash = "trash.jpg";
	

//	
	while( list($key , $val) = each($RecFile) )
	{	
		echo "<tr>";
		echo "<th scope='row' font style='background-color:$MainEditBackGround; color:#000000;' >$val</font></th>";
		echo "<th scope='row' font style='background-color:$MainEditBackGround; color:#000000;'>$FilesSize[$key]</font></th>";
		echo "<td align='center' font style='background-color:$MainEditBackGround; color:#000000;'><a href='mcurecord/$val'><img src='down.jpg' style='cursor: hand;'></a></font></td>";
		echo "<td align='center' font style='background-color:$MainEditBackGround; color:#000000;'><img src='$trash' onClick=BtnDel('$val') style='cursor: hand;'></font></td>";
		echo "</tr>";
		
	}

	

?>
</table>
<input name="del" type="hidden" value="" >
<input name="rec" type="hidden" value="" >

<br><br>

<br><br>
</form>
</body>


</html>
