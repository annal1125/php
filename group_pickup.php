<?php
	include "filepath.php";

	$InputIndex = $_SERVER["QUERY_STRING"];
	$parse_temp = explode( "=" , $InputIndex );
	$InputIndex = chop($parse_temp[1]);
	
	$parse_temp = explode( "," , $InputIndex );	
	$Parent_Index 	= $parse_temp[0];
	$InputIndex = substr( $InputIndex , strpos( $InputIndex , "," ) + 1 );
	
	if($_POST['sub'])
	{	
		$CodecStr = "";
		reset( $right );

		while( list($key , $val) = each($right) )
		{
			if( $CodecStr == null ) 
				$CodecStr .= $val;
			else 
				$CodecStr .= ("," . $val);
		}
		echo $CodecStr;
		echo "<Script language='JavaScript'>";
		echo "window.opener.form.ch_pickup$Parent_Index.value='$CodecStr';";
		echo "window.close();";
		echo"</Script>";
	}
	
//	$tok = strtok($InputIndex,"/");
	
	$RightContect = "";
	$Leftcontent = "";
	$content_array = file( $GROUP_ALIAS );
	while( list($key , $val) = each($content_array) )
	{	
		$tmp = explode( "=" , $val );
		$tmp[0] = trim($tmp[0]);
		$tmp[1] = trim($tmp[1]);
		
		if( $tmp[1] == null ) continue;
		
		if( strstr( $InputIndex , $tmp[0] ) == null )
			$Leftcontent[$tmp[0]] = $tmp[1];
		else
			$RightContect[$tmp[0]] = $tmp[1];
		
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
</head>
<script language = "javascript">
<!--

function moveOption(e1, e2)
{

	var bText = false;

	for(var i=0;i<e1.options.length;i++)
	{
		if(e1.options[i].selected)
		{
			var e = e1.options[i];
			e2.options.add(new Option(e.text, e.value));
			e1.remove(i);
			i=i-1
			bText = true;
		}
	}
}
function swapNode(node1,node2)
{

 var _parent=node1.parentNode;

 var _t1=node1.nextSibling;
 var _t2=node2.nextSibling;

 if(_t1)_parent.insertBefore(node2,_t1);
 else _parent.appendChild(node2);

 if(_t2)_parent.insertBefore(node1,_t2);
 else _parent.appendChild(node1);
}


function changepos(obj,index)
{
	var sel = obj.selectedIndex;

	if( index == -1 )
	{
		if( sel > 0 )
		{
			obj.options[sel].swapNode( obj.options[sel-1] );
		}
	}
	else if( index == 1)
	{
		if( sel < obj.options.length-1 )
		{
			obj.options(sel).swapNode( obj.options(sel+1) );
		}
	}

	
	
}
function gototop(obj)
{
	if(obj.multiple)
	{
		if(obj.selectedIndex !=-1)
		{
		
			for(var selIndex=0; selIndex<obj.options.length; selIndex++)
			{
				if(obj.options[selIndex].selected)
				{
					var transferIndex = selIndex;
					while(transferIndex > 0 && !obj.options[transferIndex - 1].selected)
					{
						obj.options[transferIndex].swapNode(obj.options[transferIndex - 1]);
						transferIndex--;
					}
				}
			}
		}
	}
	else
	{
		if(obj.selectedIndex !=-1)
		{
			var selIndex = obj.selectedIndex;
			while(selIndex > 0)
			{
				obj.options[selIndex].swapNode(obj.options[selIndex - 1]);
				selIndex --;
			}
		}
	}
	
}
function ClickOK()
{

	var obj = document.forms[0].elements['right[]'];

	for(var i=0;i<obj.options.length;i++)
	{
		obj.options[i].selected = true;
	}
	document.forms[0].sub.value = 1;
	document.forms[0].submit();

}

//-->
</script> 

<body>
<form name="form" method="POST" enctype="multipart/form-data" action="<? $PHP_SELF ?>">
<?
	echo "<p align='left' style=$MainTitleStyle color='$MainTitleTextColor'>&nbsp;&nbsp;";
	echo $WORDLIST['grouppickup'][$LANG];
	echo "</p>";
?>



<table width="90%" border="0" align="center" height="70%" >
<tr>
<td width="40%"  rowspan="2">
<select multiple name="left" size="8"  style='width: 200; height :275' ondblclick="moveOption(document.getElementById('left'), document.getElementById('right[]'))" STYLE='background-color:#FFCC99;'>
<?   
	reset($Leftcontent);
	while( list($key , $val) = each($Leftcontent) )
		echo "<option value=$key>$val</option>";
?>
</select>

</td>
<td width="15%" height="70%" align="center">
<input type="button" name="btn1" value=" >>>> "  style='width: 80%' onclick="moveOption(document.getElementById('left'), document.getElementById('right[]'))" STYLE='background-color:#FFCC99;'>

<br><br><br>
<input type="button" name="btn2" value=" <<<< "  style='width: 80%;' onclick="moveOption(document.getElementById('right[]'), document.getElementById('left'))" STYLE='background-color:#FFCC99;'>



</td>

<td width="40%" rowspan="2"> 
<select multiple name="right[]" size="5" style="width: 200; height :300"  ondblclick="moveOption(document.getElementById('right[]'), document.getElementById('left'))" STYLE='background-color:#FFCC99;'>
<?   
	reset($RightContect);
	while( list($key , $val) = each($RightContect) )
		echo "<option value=$key>$val</option>";
?>
</select>

<input name="sub" type="hidden" value="" >

</td>
</tr>
</table>


<p align="center"> 
<input type='button' name='Submit1' onclick=ClickOK(); value="<? echo $WORDLIST['submit'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="Cancel" onclick="window.close()" value="<? echo $WORDLIST['cancel'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> </td>
</p>

</form>
</body>


</html>
