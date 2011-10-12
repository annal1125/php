<?php
if($_COOKIE['login'] != "yes")
{
	header('Location: index.php');
	exit;
}
include "filepath.php";
include "AstManager.php";
	$SubmitAcction = $_SERVER["QUERY_STRING"];
	
	if( $SubmitAcction != "" )
	{
		$parse_temp = explode( "=" , $SubmitAcction );
		$SubmitAcction = chop($parse_temp[1]);
	}

	Net_AsteriskManager( '127.0.0.1' , '5038' );
	connect();
	login('admin', 'avadesign22221266');	


	
	if(isset($_POST[Submit]))
	{

		

	}	
	

	if( $SubmitAcction != "" )
	{
		delDBtree( $SubmitAcction );
		delDB( "group" , $SubmitAcction );
	
	}
	
	
	$response = getDB();
	$GroupKey = "/group/";
	$RingTypeArray = array("all" => $WORDLIST['simultaneous_ring'][$LANG] , "serial" => $WORDLIST['serial_ring'][$LANG] );

	$tok = strtok($response,"\r\n");
	
	$i = 0;
	while($tok) 
	{	// get group data	
		if( strstr( $tok , $GroupKey ) == "" )
		{
			$tok = strtok("\r\n");
			continue;
		}
		
		$Str1 = explode(  ":" , $tok );
		$Str1[1] = chop(ltrim($Str1[1]));	
		
		if( $Str1[1] == "" )
		{
			$tok = strtok("\r\n");
			continue;
		}
		
		if( strstr( $Str1[1] , "&" ) != "" )
		{ //  simultaneous ring 
			$GroupNum[$i] = chop( substr( $Str1[0], strlen( $GroupKey ) ) );
			$RingType[$i] = $RingTypeArray["all"];
			$i++;
		}
		else
		{ //serial ring
			$GroupNum[$i] = chop( substr( $Str1[0] , strlen( $GroupKey ) ) );
			$RingType[$i] = $RingTypeArray["serial"];
			$i++;
		}
		$tok = strtok("\r\n");
	}			


	logout();
	
	$FieldNum = sizeof( $GroupNum );
	
	

?>




<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>Group</title>
<script language = "javascript">
function Addnew()
{
	var page = 'groupedit.php?v=111';   
	window.open(page,"","width=600,height=600,scrollbars=yes");	


}
function BtnModify(msg)
{
	var page = 'groupedit.php?v='+ msg;   
	window.open(page,"","width=600,height=600,scrollbars=yes");	


}
function BtnDel(msg)
{
	var page = 'group.php?v=' + msg;   
	window.location.href = page;
}


</script>

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
#Layer1 {
        WIDTH: 850px;
		}
</style></head>


<body>
<div id='Layer1' >

<form name="form" method="POST" enctype="multipart/form-data" action="<? $PHP_SELF ?>">
<?
include "languag.php";
	echo "<div align='left' style=$MainTitleStyle color='$MainTitleTextColor'>&nbsp;&nbsp;";
	echo $WORDLIST['groupsetting'][$LANG];
	echo "</div>";
?>
<p align="center"> 

</p>

<table width="60%" border="1" align="center">
<tr>
    <th width="70%" scope="row"><? echo $WORDLIST['groupsetting'][$LANG]; ?></th>
    <th width="22%" scope="row"><? echo $WORDLIST['ringtype'][$LANG]; ?></th>
    <th width="8%" scope="row"><? echo $WORDLIST['del'][$LANG]; ?></th>
	
<?	
	for( $i = 0; $i < $FieldNum; $i++ )
	{
		echo "<tr>";
		echo "<td align='center' ><input type='button' name='btnmodify' onclick=BtnModify('$GroupNum[$i]'); align='center' value='$GroupNum[$i]' STYLE='background-color:#FFCC99; width :50%'></td>";
		echo "<td align='center' ><scope='row' onclick=BtnModify('$GroupNum[$i]');  >";
		echo $RingType[$i];
		echo "</td>";
		
		echo "<td align='center' ><img src='trash.jpg' onclick=BtnDel('$GroupNum[$i]'); ></td>";
		echo "</tr>";
	}
	
?>
</table>

	
<p align="center">
<input name="btnNew" type="button" value="<? echo $WORDLIST['adddnew'][$LANG]; ?>" onClick="javascript:Addnew()" style="<? echo $MainBtnStyle ?>;height:30px;width:60px"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type='submit' name='Submit' value="<? echo $WORDLIST['submit'][$LANG] ?>" style="<? echo $MainBtnStyle ?>;height:30px;width:60px"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="Cancel" value="<? echo $WORDLIST['cancel'][$LANG] ?>" style="<? echo $MainBtnStyle ?>;height:30px;width:60px"> </td>
</p>
<br><br>

</form>
</div>
</body>


</html>
