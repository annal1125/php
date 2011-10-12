<?php

	include "filepath.php";
	
	$UsrDefault = "root";
	$PwdDefault = "1234";

	// get self ip
	$referrer =  parse_url($_SERVER['HTTP_REFERER']);
	$LocalIp = $referrer["host"];
	$LocalIp1 = "127.0.0.1";
	// get remote client ip
	$UsrRemoteIp = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];    
	// supervisor
	$bSupervisor = ( $UsrRemoteIp == $LocalIp || $UsrRemoteIp == $LocalIp1 ) ? 1 : 0;
	 	
	
	if( $usrN == "" )
	{ // reject
		setcookie("login","no");
		setcookie("login_s","no");
		header('Location: index.php');
		exit;
	}	
	
	$content_array = file( $WEB_USER_PATH );
	
	$i = 0;

	$parse_temp = explode( "=" , $content_array[0] );
	
	$name = chop($parse_temp[0]);
	$pwd = chop($parse_temp[1]);
	
	$parse_temp = explode( "=" , $content_array[1] );
	
	$name1 = chop($parse_temp[0]);
	$pwd1 = chop($parse_temp[1]);
	

	if( $name == "" &&  $pwd == "" )
	{	
		$name = $UsrDefault;
		$pwd = $PwdDefault;
	}	
	
/*
	$content_array = file( $WEB_SUSER_PATH );
	$parse_temp = explode( "=" , $content_array[0] );
	
	$nameS = chop($parse_temp[0]);
	$pwdS = chop($parse_temp[1]);
	
	if( $nameS == "" &&  $pwdS == "" )
	{	
		$nameS = $UsrDefault;
		$pwdS = $PwdDefault;
	}	
	
	if( $nameS == $usrN && $pwdS == $pwdD && $bSupervisor )
	{ // supervisor
		setcookie("login_s","yes");
		setcookie("login","yes");
		header('Location: main.php');
	}
*/	



	if( $name1 == $usrN && $pwd1 == $pwdD  )
	{
		setcookie("login_s","yes");
		setcookie("login","yes");
		header('Location: main.php');
	}
	else if( $name == $usrN && $pwd == $pwdD  )
	{ // normal user
		setcookie("login","yes");
		setcookie("login_s","no");
		header('Location: main.php');
	}
	else
	{ // reject
		setcookie("login","no");
		setcookie("login_s","no");
		header('Location: index.php');
	}

?> 