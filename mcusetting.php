<?php
if($_COOKIE['login'] != "yes")
{
	header('Location: index.php');
	exit;
}

	include "filepath.php";
	include "AstManager.php";
	
	if( $PLATFORM == 3 )
		$TRASH_PIC = "planet_trash.jpg";
	else
		$TRASH_PIC = "trash.jpg";
	
	
	$MEETME_PATH = "$BASE_AST/meetme.conf";
	$MCU_MAX_PATH = "$BASE_CONF/config/max.conf";
	
	$SizeArray = array(
		"CIF" 		=> "CIF" ,
		"QCIF" 		=> "QCIF" ,
		"PAL"		=> "4CIF" );		
		
	$BitrateArray = array(
					"128" 	=> "128Kbps",
					"256"	=> "256Kbps",
					"384"	=> "384Kbps",
					"512"	=> "512Kbps");
					
					
	$FPSArray = array();
	
	for($i =5; $i<31; $i++ )
	{
		array_push( $FPSArray , $i );
	}
	
	$fp = fopen ($MCU_MAX_PATH, "r");
	$tmp = fread ($fp, filesize ($MCU_MAX_PATH));
	fclose ($fp);
	$MAX_MCU = chop( substr( $tmp , strlen("max=") ) );
	
	
	if(isset($_POST[Submit]))
	{		
		$UseMember = 0;
		for($i = 0; $i < sizeof($ch_RoomNo); $i++ )
		{
			$ch_members = sprintf("ch_members%d",$i);
			$ch_members = $$ch_members;
		
			$UseMember += $ch_members;
		}
		if( $UseMember > $MAX_MCU )
		{
			echo "<Script language='JavaScript'>";
			echo "alert('Room has reached the maximum !');";
			echo"</Script>";	
		}
		else
		{
		
			Net_AsteriskManager( '127.0.0.1' , '5038' );
			connect();
			login('admin', 'avadesign22221266');
		
			$StrTotal  = "[general]\n";
			$StrTotal .= "composition=34\n";
			$StrTotal .= "size=$size\n";
			$StrTotal .= "bitrate=$bitrate\n";
			$StrTotal .= "fps=".($fps+5)."\n";
			$fp = fopen( $MEETME_PATH , "w+");
			fwrite( $fp , $StrTotal );		
			fclose( $fp );
			
			if( !file_exists( "$BASE_AST/ex_mcu_func.conf" ) )
			{
				$StrTotal = "[macro-MCURec]\n";
				$StrTotal .= "exten => s,1,Set(filetmp=/etc/asterisk/\${EPOCH}.call)\n";
				$StrTotal .= "exten => s,n,system(echo \"Channel: Local/RecSave@MCU_REC\" >> \${filetmp})\n";
				$StrTotal .= "exten => s,n,system(echo \"MaxRetries: 1\" >> \${filetmp})\n";
				$StrTotal .= "exten => s,n,system(echo \"WaitTime:5\" >> \${filetmp})\n";
				$StrTotal .= "exten => s,n,system(echo \"Context: MCU_REC\" >> \${filetmp})\n";
				$StrTotal .= "exten => s,n,system(echo \"Extension: meetme\" >> \${filetmp})\n";
				$StrTotal .= "exten => s,n,system(echo \"SetVar: Room=\${ARG1}\" >> \${filetmp})\n";
				$StrTotal .= "exten => s,n,system(echo \"Priority: 1\" >> \${filetmp})\n";
				$StrTotal .= "exten => s,n,system(chown asterisk:asterisk \${filetmp})\n";
				$StrTotal .= "exten => s,n,system(mv \${filetmp} /var/spool/asterisk/outgoing)\n\n";
				
				$StrTotal .= "[MCU_REC]\n";
				$StrTotal .= "exten => RecSave,1,Answer\n";
				$StrTotal .= "exten => RecSave,n,mp4save(/var/www/html/mcurecord/\${STRFTIME(\${EPOCH},,%Y%m%d-%H%M%S)}.mp4)\n";
				$StrTotal .= "exten => RecSave,n,Hangup\n\n";
				$StrTotal .= "exten => meetme,1,Answer\n";
				$StrTotal .= "exten => meetme,n,MeetMe(\${Room}|dxq)\n";
				$StrTotal .= "exten => meetme,n,Hangup\n";
				
				$StrTotal .= "; ---------  MCU ---------------------------------------\n";
				$StrTotal .= "[MCU]\n";
				$StrTotal .= "exten => _X.,1,NoOp( Coming MCU \${EXTEN})\n";
				$StrTotal .= "exten => _X.,n,Dial(Local/\${EXTEN}@ava_media)\n";
				$StrTotal .= "exten => _X.,n,HangUp()\n";
				$StrTotal .= "; ---------  End MCU ------------------------------------\n";
				
				$StrTotal .= "[MCUINVITE]\n";
				$StrTotal .= "exten => s,1,Background(vm-enter-num-to-call)\n";
				$StrTotal .= "exten => s,n,WaitExten(60)\n\n";
				
				$StrTotal .= "exten => _X#,1,Set(Dst=\${EXTEN:0:1})\n";
				$StrTotal .= "exten => _X#,n,Goto(ava_media,MCUHOOK,1)\n";
				$StrTotal .= "exten => _XX#,1,Set(Dst=\${EXTEN:0:2})\n";
				$StrTotal .= "exten => _XX#,n,Goto(ava_media,MCUHOOK,1)\n";
				$StrTotal .= "exten => _XXX#,1,Set(Dst=\${EXTEN:0:3})\n";
				$StrTotal .= "exten => _XXX#,n,Goto(ava_media,MCUHOOK,1)\n";
				$StrTotal .= "exten => _XXXX#,1,Set(Dst=\${EXTEN:0:4})\n";
				$StrTotal .= "exten => _XXXX#,n,Goto(ava_media,MCUHOOK,1)\n";
				$StrTotal .= "exten => _XXXXX#,1,Set(Dst=\${EXTEN:0:5})\n";
				$StrTotal .= "exten => _XXXXX#,n,Goto(ava_media,MCUHOOK,1)\n";
				$StrTotal .= "exten => _XXXXXX#,1,Set(Dst=\${EXTEN:0:6})\n";
				$StrTotal .= "exten => _XXXXXX#,n,Goto(ava_media,MCUHOOK,1)\n";
				$StrTotal .= "exten => _XXXXXXX#,1,Set(Dst=\${EXTEN:0:7})\n";
				$StrTotal .= "exten => _XXXXXXX#,n,Goto(ava_media,MCUHOOK,1)\n";
				$StrTotal .= "exten => _XXXXXXXX#,1,Set(Dst=\${EXTEN:0:8})\n";
				$StrTotal .= "exten => _XXXXXXXX#,n,Goto(ava_media,MCUHOOK,1)\n";
				$StrTotal .= "exten => _XXXXXXXXX#,1,Set(Dst=\${EXTEN:0:9})\n";
				$StrTotal .= "exten => _XXXXXXXXX#,n,Goto(ava_media,MCUHOOK,1)\n";
				$StrTotal .= "exten => _XXXXXXXXXX#,1,Set(Dst=\${EXTEN:0:10})\n";
				$StrTotal .= "exten => _XXXXXXXXXX#,n,Goto(ava_media,MCUHOOK,1)\n";			
				
				$fp = fopen( "$BASE_AST/ex_mcu_func.conf" , "w+");
				fwrite( $fp , $StrTotal );		
				fclose( $fp );
			}
		
			$StrTotal = "#include  \"ex_mcu_func.conf\"\n\n";
			$StrTotal .= "[ava_media]\n";

			for($i = 0; $i < sizeof($ch_RoomNo); $i++ )
			{
				if( $ch_RoomNo[$i] == "" ) continue;
				$ch_RoomPass = sprintf("ch_RoomPass%d",$i);
				$ch_RoomPass = $$ch_RoomPass;
				$ch_check = sprintf("ch_check%d",$i);
				$ch_check = $$ch_check;
				$ch_members = sprintf("ch_members%d",$i);
				$ch_members = $$ch_members;
		
				if( $ch_check == "on" )
				{
					$IsRec = "rec";
					putDB( "MCU_REC" , $ch_RoomNo[$i] , "ON" );
				}
				else
				{
					$IsRec = "";
					delDB( "MCU_REC" , $ch_RoomNo[$i] );
				}
		
				$StrTotal .= "exten => $ch_RoomNo[$i],1,Set(members=$ch_members)\n";
				$StrTotal .= "exten => $ch_RoomNo[$i],n,MeetMeCount(\${EXTEN}|count)\n";
				$StrTotal .= "exten => $ch_RoomNo[$i],n,GotoIf(\$[\${count}>=\${members}]?hangup)\n";
				if( $ch_RoomPass != "" )
					$StrTotal .= "exten => $ch_RoomNo[$i],n,Authenticate($ch_RoomPass)\n";
				
				$StrTotal .= "exten => $ch_RoomNo[$i],n,Set(IsRec=\${DB(MCU_REC/\${EXTEN})})\n";
				$StrTotal .= "exten => $ch_RoomNo[$i],n,GotoIf($[\"\${IsRec}\" != \"ON\"]?meetme)\n";
				$StrTotal .= "exten => $ch_RoomNo[$i],n,Macro(MCURec,\${EXTEN})\n";
				$StrTotal .= "exten => $ch_RoomNo[$i],n(meetme),Set(MCUROOM=\${EXTEN})\n";
				$StrTotal .= "exten => $ch_RoomNo[$i],n,Set(MEETME_EXIT_CONTEXT=ava_media)\n";
				$StrTotal .= "exten => $ch_RoomNo[$i],n,MeetMe(\${MCUROOM}|Aadv1);$IsRec\n";
				$StrTotal .= "exten => $ch_RoomNo[$i],n(hangup),Hangup()\n";
				$StrTotal .= "\n";
							
			}
			
			$StrTotal .= "exten => MCUHOOK,1,Dial(SIP/\${Dst},,Tg)\n";
			$StrTotal .= "exten => MCUHOOK,n,Goto(\${MCUROOM},meetme)\n";
			//$StrTotal .= "exten => *,1,Goto(MCUINVITE,s,1)\n";
			
			
			logout();
			
			$fp = fopen( $MCU_PATH , "w+");
			fwrite( $fp , $StrTotal );		
			fclose( $fp );
			
			shell_exec( "asterisk -rx reload" );
		
		}


	} // end if(isset($_POST[Submit]))
	
	// init param
	$members = null;
	$RoomNo = null;
	$RoomPass = null;
	$check = null;

	$content_array = file( $MEETME_PATH );	
	while( list($key , $val) = each($content_array) )
	{		
		if( strstr( $val , "size=" ) != "" )
			$size = chop(substr( $val , strlen("size=")));
		else if( strstr( $val , "bitrate=" ) != "" )
			$bitrate = chop(substr( $val , strlen("bitrate=")));
		else if( strstr( $val , "fps=" ) != "" )
			$fps = chop(substr( $val , strlen("fps=")))-5;

	}// end while

	$content_array = file( $MCU_PATH );	
	$i = 0;
	$j = -1;
	while( $content_array[$i] != "" )
	{	
		if( strstr( $content_array[$i] , "members=" ) != "" )
		{
			$iPos = strrpos( $content_array[$i] , "=" ) + 1;
			$tmp = substr( $content_array[$i] , $iPos , strrpos($content_array[$i],")")-$iPos  );
			$members[++$j] = $tmp;
			$iPos = strpos( $content_array[$i] , " => " ) + 4;
			$tmp = substr( $content_array[$i] , $iPos , strpos($content_array[$i],",")-$iPos  );
			$RoomNo[$j] = $tmp;	
			
		}
		else if( strstr( $content_array[$i] , "Authenticate" ) != "" )
		{  
			$iPos = strrpos( $content_array[$i] , "Authenticate(" ) + strlen("Authenticate(");
			$tmp = substr( $content_array[$i] , $iPos , strrpos($content_array[$i],")") - $iPos  );
			$RoomPass[$j] = $tmp;			
		}
		else if( strstr( $content_array[$i] , "MeetMe" ) != "" )
		{  //rec
			if( strstr( $content_array[$i] , "rec" ) != "" )
				$check[$j] = "checked";	
			else
				$check[$j] = "1";	
		}
		
		
		
		$i++;
	}// end while


function ComboSize($str)
{
	global $SizeArray;

	if( $str != "" )
	{
		echo "<option value=$str>$SizeArray[$str]</option>";
	}
	reset( $SizeArray ); 

	while( list($key , $val) = each($SizeArray) )
	{
		if( $key == $str )
			continue;
		echo "<option value=$key>$val</option>";
	}

}
function ComboBitrate($str)
{
	global $BitrateArray;

	if( $str != "" )
	{
		echo "<option value=$str>$BitrateArray[$str]</option>";
	}
	reset( $BitrateArray ); 

	while( list($key , $val) = each($BitrateArray) )
	{
		if( $key == $str )
			continue;
		echo "<option value=$key>$val</option>";
	}

}
function ComboFPS($str)
{
	global $FPSArray;

	if( $str != "" )
	{
		echo "<option value=$str>$FPSArray[$str]</option>";
	}
	reset( $FPSArray ); 

	while( list($key , $val) = each($FPSArray) )
	{
		if( $key == $str )
			continue;
		echo "<option value=$key>$val</option>";
	}

}

function ShowRoute($route)
{
	global $RouteGroupArray;
	reset( $RouteGroupArray ); 
	echo "Annal".$route;
	if( $route != "" )
		echo "<option value=$route>$RouteGroupArray[$route]</option>";

	while( list($key , $val) = each($RouteGroupArray) )
	{
		if( $key == $route )
			continue;
		echo "<option value=$key>$val</option>";
	}

}

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">

<title>Conference Edition</title>


<script language = "javascript">
<!--
var MAX_MEMBERS = <? echo $MAX_MCU ?>;
var KEY_DOWN = "";


function viewfile() 
{
	FORM=document.forms[0];
	var page = 'mcurecord.php';   
//	window.open(page,"","");	
	window.open(page,"","width=600,height=500,scrollbars=yes");	
//	alert(FORM.FieldSel);
}
function isNumber(val){
var reg = /^[0-9]*$/;
return reg.test(val);
}
function val(num){
 var num=parseFloat(num);
 if(isNaN(num)) return 0;
 return num;
}

function abs(num){
 if(num<0) return -num;
 return num;
}

function DecPlaces(num){
 var str=""+num;
 str=str.substr(str.indexOf(".")+1);
 return str.length;
}

function Fix(num,numOfDec){
 if(!numOfDec) numOfDec=0;
 
 num=num*Math.pow(10,numOfDec);
 num=Math.round(num);
 num=num/Math.pow(10,numOfDec);
 return num;
}

String.prototype.chkIsContain=function(){
 var arg=arguments;
 for(var i=0;i<arg.length;i++){
  if(this.indexOf(arg[i])!=-1) return true;
 }
 return false;
}

String.prototype.chkIsMatch=function(){
 var arg=arguments;
 for(var i=0;i<arg.length;i++){
  if(this==arg[i]) return true;
 }
 return false;
}

function chkIsExist(name){
 for(var i=0;i<document.forms.length;i++){
  for(var j=0;j<document.forms[i].elements.length;j++){
   if(document.forms[i].elements[j].name==name) return true;
   if(document.forms[i].elements[j].id==name) return true;
  }
 }
 return false;
}

function checkUpDown(obj){
 obj.value=Fix(val(obj.value),DecPlaces(val(obj.inc) ) );
 if(val(obj.value)>val(obj.max)) obj.value=obj.max;
 if(val(obj.value)<val(obj.min)) obj.value=obj.min;
}

function plusUpDown(obj){
 if(!obj.disabled){
  obj.value-=-val(obj.inc);
  checkUpDown(obj);
 }
}

function substUpDown(obj){
 if(!obj.disabled){
  obj.value-=val(obj.inc);
  checkUpDown(obj);
 }
}

function checkKeyIsNum(obj,e){
 if(document.all){
  var e=window.event;
  var k=e.keyCode;
 }else{
  var k=e.which;
 }
 
 function isthiskey(keyCode){
  var arg=arguments;
  for(var i=1;i<arg.length;i++){
   if(keyCode==arg[i]) return true;
  }
 }

 if(k==13){
  checkUpDown(obj);
  return true;
 }
 if(k==38) plusUpDown(obj);
 if(k==40) substUpDown(obj);
 
 if(k>="0".charCodeAt(0) && k<="9".charCodeAt(0)){
  return true;
 }
 
 if(k>=96 && k<=105){
  return true;
 }
 
 if(k==110 || k==190){
  if(val(obj.inc)!=val(Math.floor(obj.inc)) && !obj.value.chkIsContain(".") ) return true;
 }
 
 if(k==109 || k==189 || k==45){
  if(val(obj.min)<0 && !obj.value.chkIsContain("-") ) return true;
 }
 
 if( isthiskey(k,35,36,37,39,46,8,9,20,144,145) ) return true;

 return false;
}

function mkUpDown(name,size,line,value,min,max,inc,mustNum,maxlength,propertiesForText,propertiesForBtn){

 
 if(!!size) size=abs(val(size));
 if(!size) size=5;

 
 if(!value || isNaN(value)) value=0;
 
 if(!min || isNaN(min)) min=0;
 
 if((!max && max!=0) || isNaN(max)) max=100;
 
 if(!!maxlength || maxlength==0){
  if(isNaN(maxlength) || val(maxlength)<0){
   maxlength='""';
  }else{
   maxlength=val(maxlength);
  }
 }else if(!maxlength && maxlength!=0){
  maxlength='""';
 }
 
 if(!inc || isNaN(inc)) inc=1;
 if(!propertiesForText) propertiesForText='';
 if(!propertiesForBtn) propertiesForBtn='';
 
 if((typeof mustNum).toLowerCase=="boolean") mustNum=true;
 mustNum=!!mustNum;
 if(mustNum) propertiesForText=' onkeydown="return checkKeyIsNum(this)" onkeypress="return checkKeyIsNum(this)" '+propertiesForText;

 if( (line % 2) == 0 )
	var Color = "<? echo $MainEditBackGround; ?>";
else
	var Color = "<? echo $MainEditBackGround1; ?>";

document.write('<input type=text onpaste="return false" maxlength="'+maxlength+'" onblur="checkUpDown(this)" name='+name+' value='+value+ ' style=\'background-color:'+Color+'; width: 75%\'' +  ' max='+max+' min='+min+'  inc='+inc+' '+propertiesForText+' >');
document.write('<input name=btnUp'+name+' type=button value=▲ onclick="plusUpDown(this.form.'+name+')" ondblclick=this.onclick() '+propertiesForBtn+' >');
document.write('<input name=btnDown'+name+' type=button value=▼ onclick="substUpDown(this.form.'+name+')" ondblclick=this.onclick() '+propertiesForBtn+' >');

 
 }

function Addnew()
{
	var tableObj = document.getElementById("mytable");
	var len = tableObj.rows.length;
    var newRowObj = tableObj.insertRow(len);

	newRowObj.insertCell(newRowObj.cells.length).innerHTML = 
	"<input name='ch_RoomNo[]' value='' style='background-color:#A8E61D; width :100%'>";
	
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = 
	"<input name='ch_RoomPass" +(len-1)+ "' value='' style='background-color:#A8E61D; width :100%'>";

	var name = "ch_members"+(len-1);

	newRowObj.insertCell(newRowObj.cells.length).innerHTML = 
	"<input type=text onpaste='return false' maxlength='3' onblur='checkUpDown(this)' name='"+name+
	"' value='4' style='background-color:#A8E61D; width: 75%' max='"+MAX_MEMBERS+"' min='1'  inc='1' >"+
	"<input name=btnUp'"+name+"' type=button value=▲ onclick='plusUpDown(this.form."+name+")' ondblclick=this.onclick() >" +
	"<input name=btnDown'"+name+"' type=button value=▼ onclick='substUpDown(this.form."+name+")' ondblclick=this.onclick() >";
//	for record				
//	newRowObj.insertCell(newRowObj.cells.length).innerHTML = 
//	"<p align='center'><input name='ch_check" +(len-1)+ "' type='checkbox' style='background-color:#A8E61D; width :100%'></p>";
	
	newRowObj.insertCell(newRowObj.cells.length).innerHTML = 
	"<p align='center'><img src='<? echo $TRASH_PIC ?>' onClick='BtnDel("+(len-1)+")' style='cursor: hand; background-color:#A8E61D;'></p>";
}	
function BtnDel(msg)
{
	document.getElementsByName('ch_RoomNo[]')[msg].value = "";
	document.getElementById("ch_RoomPass"+msg).value = "";
	document.getElementById("ch_check"+msg).checked = false;
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
	echo $WORDLIST['mcuset'][$LANG];
	echo "</p>";
	//<table id="mytable" width="100%" border="1" align="center">

?>
<?php
	echo "<div align='left' style=$MainTitleStyle color='$MainTitleTextColor'>&nbsp;&nbsp;";
	echo $WORDLIST['mcu-details'][$LANG];
	echo "</div>";
	echo "<table width='100%' border='1' align='center'>";
	echo "<tr>";
    echo "<th width='10%' font style='$MainFieldStyle; padding:5px'>SIZE</font></th>";
	echo "<td width='10%'><select name='size' STYLE='background-color:$MainEditBackGround;width :100%' >";
	ComboSize($size);
	echo "</select></td>";	

    echo "<th width='10%' font style='$MainFieldStyle; padding:5px'>Bitrate</font></th>";
	echo "<td width='10%'><select name='bitrate' STYLE='background-color:$MainEditBackGround;width :100%' >";
	ComboBitrate($bitrate);
	echo "</select></td>";	
	
    echo "<th width='10%' font style='$MainFieldStyle; padding:5px'>FPS</font></th>";
	echo "<td width='10%'><select name='fps' STYLE='background-color:$MainEditBackGround;width :100%' >";
	ComboFPS($fps);
	echo "</select></td>";	
	echo "</tr></table>";
	echo "<br>";
	
	echo "<div align='left' style=$MainTitleStyle color='$MainTitleTextColor'>&nbsp;&nbsp;";
	echo $WORDLIST['mcu-room-setting'][$LANG];
	echo "</div>";
	
	echo "<table id='mytable' width='100%' border='1' align='center'>";
	echo "<tr>";
    echo "<th width='20%' scope='col' font style='$MainFieldStyle; padding:5px'>";
	echo $WORDLIST['mcu-room-num'][$LANG];
	echo "</font></th>";
	
    echo "<th width='20%' scope='col' font style='$MainFieldStyle; padding:5px'>";
	echo $WORDLIST['password'][$LANG];
	echo "</font></th>";
	
    echo "<th width='20%' scope='col' font style='$MainFieldStyle; padding:5px'>"; 
	echo $WORDLIST['mcu-max-member'][$LANG];
	echo "</font></th>";
//	for record	
//    echo "<th width='20%' scope='col' font style='$MainFieldStyle; padding:5px'>"; 
//	echo $WORDLIST['mcu-record'][$LANG];
//	echo "</font></th>";
	
	echo "<th width='8%' scope='col' font style='$MainFieldStyle; padding:5px'>";
	echo $WORDLIST['del'][$LANG]; 
	echo "</font></th>";
	
	echo "</tr>";
	
	for( $i = 0; $i < sizeof($RoomNo); $i++ )
	{		
		if( ($i % 2) == 0 )
			$StrColor = $MainEditBackGround;
		else
			$StrColor = $MainEditBackGround1;
			
		echo "<tr>";
		echo "<td><input name='ch_RoomNo[]' type='text' value='$RoomNo[$i]' style='background-color:$StrColor; width: 100%'></td>";
		echo "<td><input name='ch_RoomPass$i' type='text' value='$RoomPass[$i]' style='background-color:$StrColor; width: 100%'></td>";
		echo "<td>";
		
		echo "<Script language='JavaScript'>";
		echo "mkUpDown('ch_members$i',3,$i,$members[$i],1,20,1,true,2,'','');";
		echo"</Script>";		
		echo "</td>";
//	for record		
//		echo "<th scope='row'><input name='ch_check$i' $check[$i] type='checkbox' style='background-color:$StrColor; width: 100%'></th>";
		echo "<td align='center' ><img src='$TRASH_PIC' onClick='BtnDel($i)' style='cursor: hand; style='background-color:$StrColor; width: 100%'></td>";
		echo "</tr>";
			
	}
	
	echo "</table>";
	

?>  


<br>

<p align="center"> 
<input name="btnNew" type="button" value="<? echo $WORDLIST['adddnew'][$LANG] ?>" onClick="javascript:Addnew()" style="<? echo $MainBtnStyle ?>;height:30px;width:60px"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type='submit' name='Submit' value="<? echo $WORDLIST['submit'][$LANG] ?>" style="<? echo $MainBtnStyle ?>;height:30px;width:60px"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="Cancel" value="<? echo $WORDLIST['cancel'][$LANG] ?>" style="<? echo $MainBtnStyle ?>;height:30px;width:60px"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<!--
<input name="btnView" type="button" value="<? echo $WORDLIST['mcu-view-file'][$LANG] ?>" onClick="javascript:viewfile()" style="<? echo $MainBtnStyle ?>;height:30px;width:70px"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
-->

</p>
<br><br>
</form>
</div>
</body>


</html>
