<?php
	include "platform.php";
		
	switch( $PLATFORM )
	{
	case 3:
		$MenuBackGroundColor1 = "#0D3E7E";
		$MenuBackGroundColor2 = "#0D3E7E";
		$MenuTextColor = "#F6FDFF";
		$MenuTextOverColor = "#1AC1FF";
		$MenuItemBackGroundColor = "#0D3E7E";
		
		$IndexColor = "#0D3E7E";
		$MainBackGroundColor = "#EAEAEA";
		$MainTitleTextColor = "#1042A6";
		$MainTitleStyle = "";
		$MainFieldBackGround = "#A1C4E2";
		$MainFieldStyle = "background-color:$MainFieldBackGround; color:$MainTitleTextColor;";
		$MainFieldOverColor = "#1AC1FF";
		
		$MainEditBackGround = "#CCDFEC";
		$MainEditBackGround1 = "#CCDFEC";
		$MainBtnStyle = "background-color: #F3F3EE";

		
		
		break;
	default:
		$MenuBackGroundColor1 = "#003100";
		$MenuBackGroundColor2 = "#003100";
		$MenuTextColor = "#FFFFFF";
		$MenuTextOverColor = "#411";
		$MenuItemBackGroundColor = "#FF7979";	
		
		$IndexColor = "#308148";
		$MainBackGroundColor = "#990000";
		$MainTitleTextColor = "#FFFFFF";
		$MainTitleStyle = "FILTER:Shadow(Color=8888ff,direction=150);height=20";
		$MainFieldStyle = "filter: glow(color=#9966FF,strength=3); height:10px; color:$MainTitleTextColor;";
		$MainFieldBackGround = "#990000";
		$MainEditBackGround = "#FF7979";
		$MainEditBackGround1 = "#FFCC99";
		
		$MainBtnStyle = "border: 5px dotted #C0C0C0; background-color: #FFD5AA";
		$MainFieldOverColor = "#FFCC99";
	

	
	
		break;
	}

		

	
	
	
?>

