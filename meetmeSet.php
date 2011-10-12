<?php
if($_COOKIE['login'] != "yes")
{
	header('Location: index.php');
	exit;
}

	include "filepath.php";
	include "AstManager.php";
	

	
	
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">

<title>Conference Edition</title>


<script type="text/javascript">
<!--
function Tabs() { }



Tabs.init = function(tabListId) {
var $ = document.getElementById;
Tabs.tabLinks = $(tabListId).getElementsByTagName("A");



var link, tabId, tab;
for (var i = 0; i < Tabs.tabLinks.length; i++) {
link = Tabs.tabLinks[i];
tabId = link.getAttribute("tabId");
if (!tabId) alert("Expand link does not have a tabId element: " + link.innerHTML);
tab = $(tabId);
if (!tab) alert("tabId does not exist: " + tabId);



if (i == 0) {
tab.style.display = "block";
link.className = "linkSelected " ;//+ link.className.replace(Tabs.removeUnselectedRegex, '');
} else {
tab.style.display = "none";
link.className = "linkUnselected " ;//+ link.className.replace(Tabs.removeSelectedRegex, '');
}



link.onclick = function() {
var tabId = this.getAttribute("tabId");
for (var i = 0; i < Tabs.tabLinks.length; i++) {
var link = Tabs.tabLinks[i];
var loopId = link.getAttribute("tabId");
if (loopId == tabId) {
$(loopId).style.display = "block";
link.className = "linkSelected " ;//+ link.className.replace(Tabs.removeUnselectedRegex, '');
} else {
$(loopId).style.display = "none";
link.className = "linkUnselected " ;//+ link.className.replace(Tabs.removeSelectedRegex, '');
}
}
if (this.blur) this.blur();
return false;
} // end of link function
}
}
-->
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

<style type="text/css">
<!--
#tabList { padding:3px 20px; margin:0.1em 0 0 0; }
#tabList li { list-style:none; display:inline; margin:0; }



#tabList li a { border:1px solid #bbb; padding:3px 0.5em; margin:0px;
-moz-border-radius-topleft:7px; -moz-border-radius-topright:7px;
}



#tabList li a.linkSelected { background:#fff; border-bottom:2px solid white;
padding-top:5px; font-size:110%;
}
#tabList li a.linkUnselected { background:#eee; border-bottom:1px solid #eee; }



#tabList li a:link, #tablist li a:visited { color:navy; }



#tablist li a:hover { color:#fc6; }



#tabContents { padding:20px; border-top:1px solid #BBCCDD; }



.tabcontent { display:none; }



#tabList li.tabInvisible { display:none; }
-->
</style>


</head>


<body onload="Tabs.init('tabList', 'tabContents');">
<form name="form" method="POST" enctype="multipart/form-data" action="<? $PHP_SELF ?>">
<?

	echo "<p align='left' style=$MainTitleStyle color='$MainTitleTextColor'>&nbsp;&nbsp;";
	echo $WORDLIST['mcuset'][$LANG];
	echo "</p>";
	//<table id="mytable" width="100%" border="1" align="center">

?>
<ul id="tabList">
<li><a href="#" tabId="tab1">Tab 1</a></li>
<li><a href="#" tabId="tab2">Tab 2</a></li>
<li><a href="#" tabId="tab3">Tab 3</a></li>
</ul>



	
<div id="tabContents">



<div id="tab1">
Tab 1 <input type="text" name="tabInput1" value="tab1" />
</div>



<div id="tab2">
Tab 2 <input type="text" name="tabInput2" value="tab2" />
</div>

<div id="tab3">
Tab 3 <input type="text" name="tabInput3" value="tab3" />
</div>


</div>




</body>


</html>
