<?php
	include "filepath.php";

	$InputIndex = $_SERVER["QUERY_STRING"];
	$parse_temp = explode( "=" , $InputIndex );
	$InputIndex = chop($parse_temp[1]);
	
	
	if(isset($_POST[Submit]))
	{
		$fp = fopen( $GROUP_ALIAS , "w+");
		reset($ch_name);
		fwrite( $fp , "0=\n" );
		$i = 1;
		while( list($key , $val) = each($ch_name) )
		{		
			$StrTotal = $i++ . "=" . $val . "\n";
			fwrite( $fp , $StrTotal );
		}	
	
		fclose( $fp );	

		
		echo "<Script language='JavaScript'>";
		echo "opener.window.location.reload();";
		echo "window.close();";
		echo"</Script>";
 	}
	
	$content_array = file( $GROUP_ALIAS );
	while( list($key , $val) = each($content_array) )
	{	
		$tmp = explode( "=" , $val );
		$tmp[0] = trim($tmp[0]);
		$tmp[1] = trim($tmp[1]);
		
		$ch_name[$tmp[0]] = $tmp[1];
	}	
	



?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">

<title>Conference Edition</title>


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
</style>
<script language = "javascript">
<!--
function Addnew()
{
//	var page = 'voicexml.php?v=-1';   
//	window.location.href = page;
	var tableObj = document.getElementById("mytable");
	var length = tableObj.rows.length;


	var maxlen =  99;
	if( length > maxlen )
		return;
	
    var newRowObj = tableObj.insertRow(length);
	var len = length;
	
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = "<div align='center'>"+len+"</div>";
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = "<input type='text' name='ch_name[]' value='' style='background-color:#A8E61D; width :100%'>";

	
	
}

//-->
</script> 



</head>


<body>
<form name="form" method="POST" enctype="multipart/form-data" action="<? $PHP_SELF ?>">
<?
	echo "<p align='left' style=$MainTitleStyle color='$MainTitleTextColor'>&nbsp;&nbsp;";
	echo $WORDLIST['group-name'][$LANG];
	echo "</p>";
?>
<table id="mytable" width="80%" border="1" align="center">
  <tr>
    <td width="15%"><div align="center">No.</div></td>
    <td width="85%"><div align="center">	
	<? echo $WORDLIST['group-name'][$LANG]; ?>
	</div></td>
 </tr>
<?php
	reset( $ch_name );
	while( list($key , $val) = each($ch_name) )
	{	
		if( $key == "0" ) continue;
	
			echo "<tr>";
			echo "<th scope='row'>$key</th>";
			echo "<td><input name='ch_name[]' type='text' value='$val' STYLE='background-color:$MainEditBackGround; width: 100%'></td></tr>";


	}
 ?>  

</table>


<p align="center"> 
<input name="btnNew" type="button" value="<? echo $WORDLIST['adddnew'][$LANG] ?>" onClick="javascript:Addnew()" style="<? echo $MainBtnStyle ?>;height:30px;width:60px"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type='submit' name='Submit' value="<? echo $WORDLIST['submit'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="Cancel" onclick="window.close()" value="<? echo $WORDLIST['cancel'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> </td>
</p>

</form>
</body>


</html>
