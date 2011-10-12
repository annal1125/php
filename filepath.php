<?php
		include "color.php";
		include "lang.php";
		include_once "init.php";
		
		$BASE_AST = "/etc/asterisk";
		
		switch( $PLATFORM )
		{
		case 4: // hitpoint
		case 0: // ava planet
		case 3: // planet
	        $MAX_EXTENSION = 100;
	        $MAX_DIAL_PLAN = 20;
	        $MAX_SIP_TRUNK = 20;
			$MAX_MEDIA_FIELD = 7;
			$MAX_VXML_FIELD = 100;
			$MAX_WEB_CAM = 20;
			$SUPPORTVIDEO = 1;
			$SUPPORTG729 = 1;
			$SUPPORTG723 = 1;
			$MAX_AUTO_DIAL_FIELD = 9;
						
	        $BASE_CONF = "/home/ava_media";
			$WEBCAM_PATH = "$BASE_AST/ex_webcam.conf";
			$WEB_USER_PATH = "$BASE_CONF/web_usr.conf";
			$WEB_SUSER_PATH = "$BASE_CONF/web_super.conf";
			$DNS_PATH = "/etc/resolv.conf";
			$MOD_PATH = "$BASE_CONF/ava.vlm";
			$VXMLDir = "/var/www/html/vxml";
			$RecPath = "/var/www/html/recpath.conf";
			$AUTODIAL_PATH = "$BASE_CONF/callout_tmp";
			break;
		case 1:  // cei 
	        $MAX_EXTENSION = 50;
	        $MAX_DIAL_PLAN = 50;
	        $MAX_SIP_TRUNK = 50;
			
	        $BASE_CONF = "/mnt/app1";
			$BASE_NTP = "/mnt/app1/ntp/TZ";
			$SUPPORTVIDEO = 1;
			$SUPPORTG729 = 1;
			$SUPPORTG723 = 1;
			
			$WEB_USER_PATH = "$BASE_CONF/config/web_usr.conf";
			$WEB_SUSER_PATH = "$BASE_CONF/config/web_super.conf";
			break;
		case 2: // cht
			$VXMLDir = "/var/www/html/vxml";
			$RecPath = "/var/www/html/recpath.conf";

	        $MAX_EXTENSION = 10;
	        $MAX_DIAL_PLAN = 20;
	        $MAX_SIP_TRUNK = 20;
			$MAX_MEDIA_FIELD = 7;
			$MAX_VXML_FIELD = 100;
			
	        $BASE_CONF = "/home/ava_media";
			$WEBCAM_PATH = "$BASE_AST/ex_webcam.conf";
			$MAX_WEB_CAM = 20;
			$SUPPORTVIDEO = 1;
			$SUPPORTG729 = 1;
			$SUPPORTG723 = 1;
			
			$WEB_SUSER_PATH = "$BASE_CONF/web_super.conf";
			$DNS_PATH = "/etc/resolv.conf";
			$MOD_PATH = "$BASE_CONF/ava.vlm";
			break;
		}

		$TRASH_PIC = "trash.jpg";	
		$FWVER_PATH = "$BASE_CONF/config/ver.conf";
		$TRUNK_EX = "$BASE_AST/ex_trunk.conf";
		$SIPTRUNKREG_PATH = "$BASE_AST/sip_trunk_reg.conf";
		$SIPTRUNKREG_PATH_IAX = "$BASE_AST/sip_trunk_reg_iax.conf";
		$SIPTRUNK_PATH = "$BASE_AST/sip_trunk.conf";
		$SIPTRUNK_PATH_IAX = "$BASE_AST/sip_trunk_iax.conf";

		
		$SIP_EX_PATH = "$BASE_AST/sip_account.conf";
		$SIP_EX = "$BASE_AST/sip_account.conf";
		$GROUP_ALIAS = "$BASE_CONF/group_alias.conf";
		$EX_FORWARD = "$BASE_AST/ex_forward.conf";
		$MCU_PATH = "$BASE_AST/ex_mcu.conf";
		$LANG = "cn"; // en: english
					  // cn:  chinese
					  //kr : korean
		


		switch( $PLATFORM )
		{
		case 4: // hitpoint
			$IAX2_SUPPORT = true;
			$E1_SUPPORT = true;
			$MCU_SUPPORT = true;
			switch( $SUBFORM )
			{
			case 1: // pbx
				$E1_SUPPORT = true; 
				break;
			case 2: // mcu 
				$MCU_SUPPORT = true;
				break;
			}	
			break;
		
		case 0: // ava planet
			break;
		case 3: // planet
			$TRASH_PIC = "planet_trash.jpg";
			break;
		
		
		}


	
		extract($_POST,EXTR_OVERWRITE);		
?>

