<?php

if($_COOKIE['login'] != "yes")
{
        header('Location: index.php');
        exit;
}


	include "filepath.php";

	if( $PLATFORM == 1 )
	{
		$NET_PATH0 = "$BASE_CONF/if.sh";
		$NET_PATH1 = "$BASE_CONF/iflan.sh";
		$NTP_PATH = "$BASE_NTP/TZ";
		$DNS_PATH = "$BASE_CONF/config/resolv.conf";
		$USER_INFO_PATH = "$BASE_CONF/userinfo.conf";
	}
	else
	{
		$NET_PATH = "/etc/sysconfig/network-scripts/";
		$DNS_PATH = "/etc/sysconfig/networking/profiles/default/resolv.conf";
		$DHCPD_PATH = "/etc/dhcpd.conf";
		$DHCPD_ETH_PATH = "/etc/sysconfig/dhcpd";
		
	}




	
function IsIPFormat( $IPStr )
{
        $bIsIP = false;
        $StrLen = strlen( $IPStr );
        
        if( $StrLen > 15 || $StrLen < 7 )
                return $bIsIP;  

        $parse_temp = explode( "." , $IPStr );
        
        $bIsIP = true;
        
        for( $i = 0; $i < 4; $i++ )
        {
                $StrFormat = chop( $parse_temp[$i] );

                if( strlen( $StrFormat ) < 0 || strlen( $StrFormat ) > 3 )
                {
                        $bIsIP = false;
                        break;
                }

                if( $StrFormat < 0 || $StrFormat > 255 )
                {
                        $bIsIP = false;
                        break;                          
                }       
        }
        return $bIsIP;
}
    
	if(isset($_POST[Submit]))
	{	
		$StrTotal = "\n";
		
		for( $i = 0; $i < 2; $i++ )
		{
			if( $DNS_Set[$i] != "" )
				$StrTotal .= "nameserver ".$DNS_Set[$i]."\n";
		}
	    unlink( $DNS_PATH );
        $fpDns = fopen( $DNS_PATH , "w+");
        fwrite( $fpDns , $StrTotal );
        fclose( $fpDns );  				
	
		if( $PLATFORM == 1 )
		{
			// WAN
			$fp = fopen( $NET_PATH0 , "w+");
			$StrTotal  = "ifconfig ixp0 hw ether $MAC_Set[0]\n";
			$StrTotal .= "ifconfig ixp0 $IP_Set[0] netmask $Mask_Set[0]\n";
			if( $GW_Set[0] != NULL )
				$StrTotal .= "route add default gw $GW_Set[0]\n";
	        fwrite( $fp , $StrTotal );
			fclose( $fp );
			// LAN
			$fp = fopen( $NET_PATH1 , "w+");
			$StrTotal  = "ifconfig ixp1 hw ether $MAC_Set[1]\n";
			$StrTotal .= "ifconfig ixp1 $IP_Set[1] netmask $Mask_Set[1]\n";
	        fwrite( $fp , $StrTotal );
			fclose( $fp );
			
			// User info
			$fp = fopen( $USER_INFO_PATH , "w+");
	        fwrite( $fp , $userinfo );
			fclose( $fp );
			
			shell_exec( "sh $NET_PATH0" );
			shell_exec( "sh $NET_PATH1" );

		}
		else
		{
			while( list($key , $val) = each($EthernetArray) )
			{ 
				$NetTmp = $NET_PATH.$key;
				unlink( $NetTmp );
				$fp = fopen( $NetTmp , "w+");
				$StrTotal = "";
				while( list($key1 , $val1) = each( $val ) )
				{ 			
					if( strstr( $val1 , "NETMASK" ) != "" ||
						strstr( $val1 , "IPADDR" ) != "" ||
						strstr( $val1 , "GATEWAY" ) != "" )
					{
						continue;
					}

					$StrTotal .= chop($val1)."\n";
				}
				if( $IP_Set[$key] != "" )
					$StrTotal .= ("IPADDR=".$IP_Set[$key]."\n");
				if( $Mask_Set[$key] != "" )
					$StrTotal .= ("NETMASK=".$Mask_Set[$key]."\n");
				if( $GW_Set[$key] != "" )
					$StrTotal .= ("GATEWAY=".$GW_Set[$key]."\n");

	//			echo "$StrTotal<br>";
				fwrite( $fp , $StrTotal );
				fclose( $fp );  
				
				$eth = substr( $key , strlen("ifcfg-") );
				$Exe = "ifconfig $eth ".$IP_Set[$key]." netmask ".$Mask_Set[$key];
				shell_exec( $Exe );	
				$Exe = "route add default gw ".$GW_Set[$key];
				shell_exec( $Exe );		
				$Exe = "/etc/init.d/network restart";
				shell_exec( $Exe );					
			}	// end while( list($key , $val) = each($EthernetArray) )

			$fpDHCPD= fopen( $DHCPD_PATH , "w+");
			
			
			$StrTotal = "ddns-update-style none;\n";
			$StrTotal .= "default-lease-time $default_lease_time;\n";
			$StrTotal .= "max-lease-time $max_lease_time;\n";
			$StrTotal .= "option domain-name-servers $domain_name_servers[0]";
			if( $domain_name_servers[1] != null )
				$StrTotal .= ",139.175.10.20";
			$StrTotal .= ";\n\n";
			
	
			$route_tmp = substr( $routers , 0 , strrpos( $routers , "." ) );
			$StrTotal .= "subnet $route_tmp.0 netmask $subnet_mask {\n";
			$StrTotal .= "option routers $routers;\n";
			$StrTotal .= "option subnet-mask $subnet_mask;\n";
			$StrTotal .= "range $range[1] $range[0];\n";
			$StrTotal .= "}\n";
		
			fwrite( $fpDHCPD , $StrTotal );
			fclose( $fpDHCPD );  
		
	//		$Exe = "/etc/init.d/dhcpd restart";
			$Exe = "service dhcpd restart";
			shell_exec( $Exe );	

			
		}	// end if( $PLATFORM == 1 )
	}       

	$MAC_Set = "";
	$Mask_Set = "";
	$IP_Set = "";
	$GW_Set = "";
	$DNS_Set = array();
	$EthernetArray = array();

	$j = 0;
	if( $PLATFORM == 1 )
	{ // CEI
		for( $j = 0; $j < 2; $j++ )
		{
			$NET =  "NET_PATH".$j;
			$NET =  $$NET;

			$content_array = file( $NET );
			$i = 0;
			while( $content_array[$i] != "" )
			{
				if( strstr( $content_array[$i] , "hw ether" ) != "" )
				{ // get mac                    
					$parse_temp = explode( " " , $content_array[$i] );                      
					$MAC_Set[$j] = chop($parse_temp[4]);                
				}
				else if( strstr( $content_array[$i] , "netmask" ) != "" )
				{
					$parse_temp = explode( " " , $content_array[$i] );      
					$IP_Set[$j] = chop($parse_temp[2]);
					$Mask_Set[$j] = chop($parse_temp[4]);       
				}
				else if(  strstr( $content_array[$i] , "default gw" ) != "" )
				{
					$parse_temp = explode( " " , $content_array[$i] );      
					$GW_Set[$j] = chop($parse_temp[4]);         
				}
				$i++;
			}
        }  

		$fd = fopen ($USER_INFO_PATH, "r");
		$userinfo = fread ($fd, filesize ($USER_INFO_PATH));
		fclose ($fd);

			
		
	    $content_array = file( $DNS_PATH );
        $i = 0;
        while( $content_array[$i] != "" )
        {
			if( strstr( $content_array[$i] , "nameserver " ) != "" )
			{
	            $parse_temp = explode( " " , $content_array[$i] );      
				array_push( $DNS_Set , chop( $parse_temp[1] ) );
			}
		    $i++;
		}		
	}
	else
	{
		$NetTmp = $NET_PATH.$j++;
		$handle=opendir($NET_PATH);
		while ($file = readdir($handle)) 	          		  
		{
			if( $file == "." || $file == ".."  || strstr( $file , "ifcfg-" ) == "" || strstr( $file , "ifcfg-lo" ) )
				continue;
			$NetTmp = $NET_PATH.$file;
			$content_array = file( $NetTmp );
	//		array_push( $EthernetArray[$file] , $content_array );
			$EthernetArray[$file] = $content_array;
			
			while( list($key , $val) = each($content_array) )
			{
				if( strstr( $val , "NETMASK" ) != "" )
				{
					$parse_temp = explode( "=" , $val );   
					$Mask_Set[$file] = chop(ltrim( $parse_temp[1] ) );                         
				}
				else if( strstr( $val , "IPADDR" ) != "" )
				{
					$parse_temp = explode( "=" , $val );   
					$IP_Set[$file] = chop( ltrim ( $parse_temp[1]) );  
				}
				else if(  strstr( $val , "GATEWAY" ) != "" )
				{
					$parse_temp = explode( "=" , $val );   
					$GW_Set[$file] = chop(ltrim( $parse_temp[1]) );  
				}
			}
		}	   
		$content_array = file( $DNS_PATH );
		$j = 0;
		while( list($key , $val) = each($content_array) )
		{
			if( strstr( $val , "nameserver " ) != "" )
			{
				$DnsTmp = chop( ltrim( substr( $val , strlen( "nameserver" ) ) ) );
				array_push( $DNS_Set , $DnsTmp );
			}
		}
		
		
		// $content_array = file( $DHCPD_ETH_PATH );
		// while( list($key , $val) = each($content_array) )
		// {
			// if( strstr( $val , "DHCPDARGS=" ) != "" )
			// {
				// $dhcpd_eth = trim( substr( $val , strlen( "DHCPDARGS=" ) ) );
				// break;
			// }
		// }
		
		// $content_array = file( $DHCPD_PATH );
		
		// while( list($key , $val) = each($content_array) )
		// {
			// if( strstr( $val , "default-lease-time" ) != "" )
			// {
				// $start = strrpos( $val , " " ) + 1;
				// $end = strrpos( $val , ";" );
				// $default_lease_time = trim(substr( $val , $start , $end - $start ));
			// }
			// else if( strstr( $val , "max-lease-time" ) != "" )
			// {
				// $start = strrpos( $val , " " ) + 1;
				// $end = strrpos( $val , ";" );
				// $max_lease_time = trim(substr( $val , $start , $end - $start ));
			// }
			// else if( strstr( $val , "option domain-name-servers" ) != "" )
			// {
				// $start = strrpos( $val , " " ) + 1;
				// $end = strrpos( $val , ";" );
				// $tmp = trim(substr( $val , $start , $end - $start ));
				
				// $domain_name_servers = explode( "," , $tmp );
			// }
			// else if( strstr( $val , "option routers" ) != "" )
			// {
				// $start = strrpos( $val , " " ) + 1;
				// $end = strrpos( $val , ";" );
				// $routers = trim(substr( $val , $start , $end - $start ));
			// }
			// else if( strstr( $val , "subnet-mask" ) != "" )
			// {
				// $start = strrpos( $val , " " ) + 1;
				// $end = strrpos( $val , ";" );
				// $subnet_mask = trim(substr( $val , $start , $end - $start ));
			// }
			// else if( strstr( $val , "range" ) != "" )
			// {
				// $start = strpos( $val , " " ) + 1;
				// $end = strrpos( $val , ";" );
				// $tmp = trim(substr( $val , $start , $end - $start ));
				// $range = explode( " " , $tmp );
			// }
		// }
	}
?>



<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">

<title>Conference</title>

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
	
	echo "<div align='left' style=$MainTitleStyle color='$MainTitleTextColor'>&nbsp;&nbsp;";
	echo $WORDLIST['networksetting'][$LANG];
	echo "</div>";



	reset( $IP_Set );
	while( list($key , $val) = each($IP_Set) )
	{	
		if( $PLATFORM == 1 )
		{
			if( $key == 0 )
				$eth = "WAN";
			else
				$eth = "LAN";
		}
		else{
			$eth = substr( $key , strlen("ifcfg-") );
		}
		echo "<div align='center' style='color:$MainTitleTextColor'><b>$eth</b></div>";
	
		echo "<table width='50%' border='1' p align='center'>";
 		echo "<tr><th width='50%' scope='row'></th>";
	    echo "<th width='50%' scope='row'></th></tr>";

		echo "<tr><th scope='row' style='background-color:$MainFieldBackGround; color:$MainTitleTextColor;' >IP Address</th>";
	 	echo "<td><input name='IP_Set[$key]' type='text' value='$IP_Set[$key]' style='background-color:$MainEditBackGround; width: 100%'></td></tr>";

		if( $eth == "eth1" ) $routers = $IP_Set[$key];
		
 		echo "<tr><th scope='row' style='background-color:$MainFieldBackGround; color:$MainTitleTextColor; ' >Subnet Mask</th>";
 		echo "<td><input name='Mask_Set[$key]' type='text' value='$Mask_Set[$key]' style='background-color:$MainEditBackGround; width: 100%'></td></tr>"; 
		
		if( $eth != "LAN" && $eth != "eth1")
		{
			echo "<tr><th scope='row' style='background-color:$MainFieldBackGround; color:$MainTitleTextColor;' >Default Gateway</th>";
			echo "<td><input name='GW_Set[$key]' type='text' value='$GW_Set[$key]' style='background-color:$MainEditBackGround; width: 100%'></td></tr>"; 
		}
		
		if( $PLATFORM == 1 )
		{
			echo "<tr><th scope='row' >MAC Address</th>";
			echo "<th scope='row'>$MAC_Set[$key]</th></tr>";
			echo "<input name='MAC_Set[]' type='hidden' value='$MAC_Set[$key]'>";
			
		}		
		echo "</table>";
	}
	echo "<div align='center' style='color:$MainTitleTextColor'><b>DNS</b></div>";
	echo "<table width='50%' border='1' p align='center'>";
	
	echo "<tr><th scope='row' width='50%' style='background-color:$MainFieldBackGround; color:$MainTitleTextColor;' >Primary DNS</th>";
	echo "<td width='50%'><input name='DNS_Set[]' type='text' value='$DNS_Set[0]' style='background-color:$MainEditBackGround; width: 100%'></td></tr>";

	echo "<tr><th scope='row' width='50%' style='background-color:$MainFieldBackGround; color:$MainTitleTextColor;	' >Secondary DNS</th>";
	echo "<td width='50%'><input name='DNS_Set[]' type='text' value='$DNS_Set[1]' style='background-color:$MainEditBackGround; width: 100%'></td></tr>";

		
		

	echo "</table></p>";
	
	if( $PLATFORM == 1 )
	{
		echo "<table width='50%' border='0' p align='center'>";
		echo "<tr width='50%'><th scope='row' >";
		echo $WORDLIST['userinfo'][$LANG];
		echo "</th></tr>";
		echo "<td><textarea name='userinfo' rows='4' value='$userinfo' style='background-color:#FF7979; width: 100%'>$userinfo</textarea></td>";
		echo "</table>";
	}

	reset($EthernetArray);
	while( list($key , $val) = each($EthernetArray) )
	{
		while( list($key1 , $val1) = each($val) )
		{
			echo "<input name='EthernetArray[$key][]' type='hidden' value='$val1'>";	
		}
	}
	
	
	
	// echo "<div align='center' style='color:$MainTitleTextColor'><b>DHCP Server</b></div>";
	// echo "<table width='50%' border='1' p align='center'>";
	// echo "<tr><th scope='row' width='50%' style='background-color:$MainFieldBackGround; color:$MainTitleTextColor;' >Ethenet</th>";
	// echo "<th scope='row' width='50%'>$dhcpd_eth</th></tr>";
	// echo "<input name='dhcpd_eth' type='hidden' value='$dhcpd_eth'></th>";
	
	
/*	
	<select name='dhcpd_eth' STYLE='background-color:$MainEditBackGround;width :100%' >";

	echo "<option value=$dhcpd_eth>$dhcpd_eth</option>";
	reset( $IP_Set );
	while( list($key , $val) = each($IP_Set) )
	{
		$eth = substr( $key , strlen("ifcfg-") );
		if( $eth == $dhcpd_eth ) continue;
		echo "<option value=$eth>$eth</option>";
	}
*/	
	// echo "<tr><th scope='row' width='50%' style='background-color:$MainFieldBackGround; color:$MainTitleTextColor;	' >Default Lease Time (sec)</th>";
	// echo "<td width='50%'><input name='default_lease_time' type='text' value='$default_lease_time' style='background-color:$MainEditBackGround; width: 100%'></td></tr>";
	
	// echo "<tr><th scope='row' width='50%' style='background-color:$MainFieldBackGround; color:$MainTitleTextColor;	' >Max Lease Time (sec)</th>";
	// echo "<td width='50%'><input name='max_lease_time' type='text' value='$max_lease_time' style='background-color:$MainEditBackGround; width: 100%'></td></tr>";
	
	// echo "<tr><th scope='row' width='50%' style='background-color:$MainFieldBackGround; color:$MainTitleTextColor;	' >Domain Name Server 1</th>";
	// echo "<td width='50%'><input name='domain_name_servers[]' type='text' value='$domain_name_servers[0]' style='background-color:$MainEditBackGround; width: 100%'></td></tr>";
	// echo "<tr><th scope='row' width='50%' style='background-color:$MainFieldBackGround; color:$MainTitleTextColor;	' >Domain Name Server 2</th>";
	// echo "<td width='50%'><input name='domain_name_servers[]' type='text' value='$domain_name_servers[1]' style='background-color:$MainEditBackGround; width: 100%'></td></tr>";
	
	// echo "<tr><th scope='row' width='50%' style='background-color:$MainFieldBackGround; color:$MainTitleTextColor;	' >Default Route</th>";

	
	// echo "<th scope='row' width='50%'>$routers</th></tr>";
	// echo "<input name='routers' type='hidden' value='$routers'></th>";

	
	
	// echo "<tr><th scope='row' width='50%' style='background-color:$MainFieldBackGround; color:$MainTitleTextColor;	' >Subnet Mask</th>";
	// echo "<td width='50%'><input name='subnet_mask' type='text' value='$subnet_mask' style='background-color:$MainEditBackGround; width: 100%'></td></tr>";
	
	// echo "<tr><th scope='row' width='50%' style='background-color:$MainFieldBackGround; color:$MainTitleTextColor;	' >IP Range (start)</th>";
	// echo "<td width='50%'><input name='range[]' type='text' value='$range[1]' style='background-color:$MainEditBackGround; width: 100%'></td></tr>";
	// echo "<tr><th scope='row' width='50%' style='background-color:$MainFieldBackGround; color:$MainTitleTextColor;	' >IP Range (end)</th>";
	// echo "<td width='50%'><input name='range[]' type='text' value='$range[0]' style='background-color:$MainEditBackGround; width: 100%'></td></tr>";

	

	
	// echo "</table></p>";
	
	
	


?>
        
<p align="center"> 
<input type='submit' name='Submit' value="<? echo $WORDLIST['submit'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="Cancel" value="<? echo $WORDLIST['cancel'][$LANG] ?>" style="<? echo $MainBtnStyle ?>"> </td>
</p>
<br>
</form>
</body>


</html>
