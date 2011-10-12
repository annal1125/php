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
	$RingTypeArray = array("all" =>"群響" , "serial" => "循響" );

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
</style></head>


<body>
<form name="form" method="POST" enctype="multipart/form-data" action="<? $PHP_SELF ?>">
<p align="left" style=FILTER:Shadow(Color=8888ff,direction=150);height=20 color="#0000FF">&nbsp;&nbsp;群組設定</p>
<p align="center"> 
<input name="btnNew" type="button" value="新增" onClick="javascript:Addnew()" STYLE='background-color:#FF7979'>
</p>

<table width="50%" border="1" align="center">
<tr>
    <th width="70%" scope="row">群組</th>
    <th width="22%" scope="row">響鈴種類</th>
    <th width="8%" scope="row">刪除</th>
	
<?	
	for( $i = 0; $i < $FieldNum; $i++ )
	{
		echo "<tr>";
		echo "<td align='center' ><input type='button' name='btnmodify' onclick=BtnModify('$GroupNum[$i]'); align='center' value='$GroupNum[$i]' STYLE='background-color:#FFCC99; width :50%'></td>";
		echo "<td align='center' ><scope='row' onclick=BtnModify('$GroupNum[$i]');  >$RingType[$i]</td>";
		echo "<td align='center' ><img src='trash.jpg' onclick=BtnDel('$GroupNum[$i]'); ></td>";
		echo "</tr>";
	}
	
?>
</table>

	
<p align="center"> 
<?
//if($_COOKIE['login_s'] != "yes" )
//	$disablekey = "disabled='disabled'";
echo "<input type='submit' name='Submit' $disablekey value=' 確 定 '  style='border: 5px dotted #C0C0C0; background-color: #FFD5AA'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
?>
<input type="submit" name="Cancel" value=" 取 消 " style="border: 5px dotted #C0C0C0; background-color: #FFD5AA" > </td>
</p>
<br><br>

</form>
</body>


</html>
