<?php

	
	$WORDLIST = array( 
	// menu 
	"networksetting"	=> array( "en" => "Network" , "cn" => "網 路 設 定" , "kr" => "네트워크설정" ),
	"accesscode" 	 	=> array( "en" => "Accesscode" , "cn" => "代 碼 設 定"  , "kr" => "허용번호설정" ),
	"groupsetting" 	 	=> array( "en" => "Group" , "cn" => "群 組 設 定"  , "kr" => "그룹번호설정" ),
	"extensionset" 	 	=> array( "en" => "Subscribers" , "cn" => "分 機 設 定"  , "kr" => "내선번호설정" ),
	"SIPTrunk" 	 	 	=> array( "en" => "SIP Trunk" , "cn" => "SIP 局線設定"  , "kr" => "SIP프락시등록"),
	"dialplan" 	 	 	=> array( "en" => "Dial Plan" , "cn" => "撥 碼 計 畫"  , "kr" => "Dial Plan" ),
	"disa" 	 	 		=> array( "en" => "DISA" , "cn" => "DISA"  , "kr" => "DISA" ),
	"ipcam" 	 	 	=> array( "en" => "IP Cam" , "cn" => "IP攝影機設定"  , "kr" => "IP카메라등록"),
	"mcuset" 	 	 	=> array( "en" => "MCU Settings" , "cn" => "會議室設定"  , "kr" => "컨퍼런스설정"),
	"mcustatus" 	 	=> array( "en" => "VCS Status" , "cn" => "會議室狀態"  , "kr" => "컨퍼런스모니터링" ),
	"status" 	 	 	=> array( "en" => "Channel Status" , "cn" => "狀 態 顯 示"  , "kr" => "등록상태모니터링"),
	"time" 	 	 	 	=> array( "en" => "Time Zone" , "cn" => "時 區 設 定"  , "kr" => "시간대역설정"),
	"upgrade" 	 	 	=> array( "en" => "Upgrade/Backup" , "cn" => "版本更新與備份"  , "kr" => "버전업그레이드"),
	"password" 	 	 	=> array( "en" => "Password" , "cn" => "更 改 密 碼"  , "kr" => "패스워드변경"),
	"reboot" 	 	 	=> array( "en" => "Reboot" , "cn" => "重 新 啟 動"  , "kr" => "다 시 시 작" ),
	"streaming" 	 	=> array( "en" => "Streaming" , "cn" => "影片串流設定"  , "kr" => "Streaming" ),
	"VXMLSet" 	 		=> array( "en" => "VXML Settings" , "cn" => "語音流程設定"  , "kr" => "VXML Settings" ),
	"autodial" 	 		=> array( "en" => "MCU Dial out" , "cn" => "會議室外撥設定"  , "kr" => "MCU Dial out" ),
	// main
	"submit" 	 	 	=> array( "en" => "Apply " , "cn" => " 確 定 "  , "kr" => " 확  인 "),
	"cancel" 	 	 	=> array( "en" => "Cancel" , "cn" => " 取 消 "  , "kr" => " 취  소 "),
	// Tool tip ( cannot have space "test 123 -> test_123" )
	"tip-voicemail" 	=> array( "en" => "VoiceMail" , "cn" => "語音留箱"  , "kr" => "Voice_Mail"),
	"tip-Intovoicemail" => array( "en" => "Transfer_the_call_into_voicemail" , 
								  "cn" => "將無法應答之來電，轉入語音留箱"  , "kr" => "Transfer_to_Voice_Mail"),
	"tip-allfwtitle" 	=> array( "en" => "Unconditional_Forward" , "cn" => "無條件轉移"  , "kr" => "UCF"),
	"tip-allfw" 		=> array( "en" => "Unconditional_forward_all_calls" , 
								  "cn" => "所有的來電將被轉移"  , "kr" => "Unconditional_Forward"),
	"tip-nofwtitle" 	=> array( "en" => "No_Answer_Forward" , "cn" => "無人回應轉移"  , "kr" => "NAF"),
	"tip-nofw" 			=> array( "en" => "Forward_all_calls_after_no_answer" , 
								  "cn" => "無人應答時，來電將被轉移"  , "kr" => "No_Answer_Forward"),
	"tip-busyfwtitle" 	=> array( "en" => "Busy_and_unreachable_Forward" , "cn" => "忙線與未上線轉移"  , "kr" => "BUF"),
	"tip-busyfw" 		=> array( "en" => "Forward_all_calls_if_busy_or_unreachable" , 
								  "cn" => "忙線與未上線時，來電將被轉移"  , "kr" => "Busy_&_Unreachable_Forward"),
	"tip-fwNumtitle" 	=> array( "en" => "Forward Numbers" , "cn" => "轉移號碼"  , "kr" => "Forward_No."),
	"tip-fwNum" 		=> array( "en" => "Please_input_the_forward_numbers" , 
								  "cn" => "請在此填入轉移之號碼"  , "kr" => "Forward_No."),
	"tip-vxmlfiletitle" => array( "en" => "VXML_FILES" , "cn" => "VXML檔案"  , "kr" => "VXML_FILES"),
	"tip-vxmlfile" 		=> array( "en" => "Double_click_into_selection_page" , 
								  "cn" => "雙擊滑鼠進入檔案選擇頁面"  , "kr" => "Double_click_into_selection_page"),
	
	// net config
	"userinfo" 	 	 	=> array( "en" => "User Info" , "cn" => "使用者資訊"  , "kr" => "User Info"),
	
	// extensions
	"ex-name" 	 	 	=> array( "en" => "Alias" , "cn" => "名稱"  , "kr" => "사용명칭"),
	"Account" 	 	 	=> array( "en" => "Account" , "cn" => "帳號"  , "kr" => "계정"),
	"Account-num" 	 	=> array( "en" => "Account/Number" , "cn" => "帳號/號碼"  , "kr" => "계정/패스워드"),
	"Password" 	 	 	=> array( "en" => "Password" , "cn" => "密碼"  , "kr" => "패스워드"),
	"vcodec" 	 	 	=> array( "en" => "VideoCodec" , "cn" => "視訊編碼"  , "kr" => "영상코덱"),
	"acodec" 	 	 	=> array( "en" => "AudioCodec" , "cn" => "語音編碼"  , "kr" => "음성코덱"),
	"mailforward" 	 	=> array( "en" => "VoiceMail  /  Incoming call Forward" ,
								  "cn" => "語音留箱 ／ 來電轉移設定"  , "kr" => "보이스메세지 ／ 포워딩설정"),
	"forward" 	 		=> array( "en" => "Incoming call Forward" ,
								  "cn" => "來電轉移設定"  , "kr" => "포워딩설정"),
	"group" 	 	 	=> array( "en" => "Group" , "cn" => "群組"  , "kr" => "Group"),
	"group-name" 	 	 => array( "en" => "Group name" , "cn" => "群組名稱"  , "kr" => "Group name"),
	"grouppickup" 	 	=> array( "en" => "Group Pickup" , "cn" => "代接群組"  , "kr" => "Group Pickup"),
	"nat" 	 			=> array( "en" => "NAT" , "cn" => "NAT"  , "kr" => "NAT"),
	
	
	// SIPTrunk
	"trunk-alias" 	 	=> array( "en" => "Alias" , "cn" => "局線名稱"  , "kr" => "국선명칭"),
	"proxy-port" 	 	=> array( "en" => "Proxy:Port" , "cn" => "伺服器:埠"  , "kr" => "프락시:포트"),
	"telnum" 	 	 	=> array( "en" => "Numbers" , "cn" => "電話號碼"  , "kr" => "전화번호"),
	"trunk-breg" 		=> array( "en" => "Reg" , "cn" => "註冊"  , "kr" => "Reg"),
	"trunk-protocal" 	=> array( "en" => "Protocal" , "cn" => "協定"  , "kr" => "Protocal"),
	
	// dial plan
	"prefix" 	 	 	=> array( "en" => "Prefix" , "cn" => "前置碼"  , "kr" => "Prefix"),
	"Add" 	 	 		=> array( "en" => "Add" , "cn" => "加碼"  , "kr" => "Add"),
	"Drop" 	 	 		=> array( "en" => "Drop" , "cn" => "減碼"  , "kr" => "Drop" ),
	"dial_delay" 	 	=> array( "en" => "Dial-delay" , "cn" => "撥號延遲"  , "kr" => "Dial-delay" ),
	"Trunk-sel" 	 	=> array( "en" => "Trunk-selection" , "cn" => "局線選擇"  , "kr" => "국선선택"),
	// ip cam
	"num" 	 	 		=> array( "en" => "Numbers" , "cn" => "號碼"  , "kr" => "번호"),
	"ipaddress" 	 	=> array( "en" => "IP Address" , "cn" => "IP位址"  , "kr" => "IP입력"),
	"Size" 	 	 		=> array( "en" => "Size" , "cn" => "尺寸"  , "kr" => "사이즈"),
	"FPS" 	 	 		=> array( "en" => "FPS" , "cn" => "FPS"  , "kr" => "FPS"),
	"Bitrate" 	 	 	=> array( "en" => "Bitrate" , "cn" => "Bitrate"  , "kr" => "Bitrate"),
	// status
	"regsucc" 	 	 	=> array( "en" => "Reg. Successfully" , "cn" => "註冊成功"  , "kr" => "등록성공"),
	"regfail" 	 	 	=> array( "en" => "Reg. fail" , "cn" => "註冊失敗"  , "kr" => "등록실패"),
	"Caller" 	 	 	=> array( "en" => "Caller" , "cn" => "主叫端" ,"kr" => "Caller"),
	"Callee" 	 	 	=> array( "en" => "Callee" , "cn" => "被叫端" ,"kr" => "Called"),
	// status
	"reg-ip" 	 	 	=> array( "en" => "Reg. IP Public / privacy" , "cn" => "註冊IP位址 公網/私網" , "kr" => "Registered IP Public/Private"),
	"reg-status" 	 	=> array( "en" => "Reg. Status" , "cn" => "註冊狀態"  , "kr" => "등록상태"),
	"talking num" 	 	=> array( "en" => "Connecting numbers" , "cn" => "通話對象"  , "kr" => "통화대상"),
	// upgrade
	"version" 	 	 	=> array( "en" => "Version" , "cn" => "版號"  , "kr" => "버전"),
	"fwupgrade-import" 	 	=> array( "en" => "Firmware Upgrade / Import settings" , "cn" => "韌體更新 / 匯入設定檔"  , "kr" => "펌웨어 업그레이드"),
	"export" 	 		=> array( "en" => "Export" , "cn" => "檔案備份 匯出"  , "kr" => "Export"),
	// password
	"newpwd" 	 	 	=> array( "en" => "new password" , "cn" => "新密碼"  , "kr" => "새패스워드"),
	"confirmpwd" 	 	=> array( "en" => "Confirm password" , "cn" => "確認新密碼"  , "kr" => "패스워드확인"),
	"modifysucc" 	 	=> array( "en" => "Successfully" , "cn" => "更改成功"  , "kr" => "변경성공"),
	"worngpwd" 	 	 	=> array( "en" => "Please verify the password" , "cn" => "請確認密碼是否輸入正確"  , "kr" => "패스워드가 맞는지 확인해주세요"),
	"accountnull" 	 	=> array( "en" => "Account cannot be empty" , "cn" => "帳號不可為空白"  , "kr" => "계정란은 빈공간을 허용하지 않습니다"),
	// MOD 
	"chname" 	 		=> array( "en" => "channel name" , "cn" => "頻道名稱"  , "kr" => "channel name"),
	"source" 	 		=> array( "en" => "Source" , "cn" => "來源"  , "kr" => "Source"),
	// VXML
	"recfilenum" 	 	=> array( "en" => "Dialed Num for Recorder" , "cn" => "錄音檔播碼"  , "kr" => "Dialed Num for Recorder"),
	"filename" 	 		=> array( "en" => "File name" , "cn" => "檔案名稱"  , "kr" => "File name"),
	"adddnew" 	 		=> array( "en" => "New" , "cn" => "新增"  , "kr" => "New"),
	"CalldeeNum" 	 	=> array( "en" => "Calldee Numbers" , "cn" => "被叫號"  , "kr" => "Calldee Numbers"),
	"del" 	 			=> array( "en" => "Delete" , "cn" => "刪除"  , "kr" => "Delete"),
	// Disa
	"recwelcome" 	 	=> array( "en" => "Record welcome audio" , "cn" => "錄製歡迎詞號碼"  , "kr" => "Record welcome audio"),
	"recnoanswer" 		=> array( "en" => "Record no answer audio" , "cn" => "錄製無人應答號碼"  , "kr" => "Record no answer audio"),
	"recbusy" 			=> array( "en" => "Record busy audio" , "cn" => "錄製忙線號碼"  , "kr" => "Record busy audio"),
	"recinvalid" 		=> array( "en" => "Record invalid audio" , "cn" => "錄製無此分機號碼"  , "kr" => "Record invalid audio"),
	// time zone
	"timezoneset" 	 	=> array( "en" => "Time Zone Setting" , "cn" => "時區設定"  , "kr" => "Time Zone Setting"),
	"nowtime" 	 		=> array( "en" => "Present Time" , "cn" => "現在時間"  , "kr" => "Present Time"),
	// auto dial
	"modify" 	 	 	=> array( "en" => "Modify" , "cn" => "編輯"  , "kr" => "Modify"),
	// MCU
	"mcu-details" 	 	=> array( "en" => "MCU Details" , "cn" => "會議室參數設定"  , "kr" => "MCU Details"),
	"mcu-room-setting" 	=> array( "en" => "Room Settings" , "cn" => "會議室房間設定"  , "kr" => "Room Settings"),
	"mcu-room-num" 	 	=> array( "en" => "Room numbers" , "cn" => "會議室號碼"  , "kr" => "Room numbers"),
	"mcu-room-pass" 	=> array( "en" => "Room numbers" , "cn" => "會議室號碼"  , "kr" => "Room numbers"),
	"mcu-max-member" 	=> array( "en" => "Max members" , "cn" => "會議室人數"  , "kr" => "Max members"),
	"mcu-record" 		=> array( "en" => "Record" , "cn" => "錄影"  , "kr" => "Record"),
	"mcu-view-file" 	=> array( "en" => "View Files" , "cn" => "檢示錄影檔"  , "kr" => "View Files"),
	// MCU file
	"mcu-files" 		=> array( "en" => "MCU Files" , "cn" => "錄影檔"  , "kr" => "MCU Files"),
	"file-size" 		=> array( "en" => "File size" , "cn" => "檔案大小"  , "kr" => "File size"),
	"files" 			=> array( "en" => "Files" , "cn" => "檔名"  , "kr" => "Files"),
	"download" 			=> array( "en" => "Download" , "cn" => "下載"  , "kr" => "Download"),
	// Dial Attribute
	"Dial-Attribute" 	=> array( "en" => "Dial Attribute" , "cn" => "外撥設定"  , "kr" => "Dial Attribute"),
	"Retry-Times" 		=> array( "en" => "Retry Times" , "cn" => "重撥次數"  , "kr" => "Retry Times"),
	"Seconds-Between-Retries" 		=> array( "en" => "Seconds Between Retries" , "cn" => "重撥間隔秒數"  , "kr" => "Seconds Between Retries"),
	"wait-ans" 			=> array( "en" => "SSeconds to wait for an answer" , "cn" => "響鈴時間(秒)"  , "kr" => "Seconds to wait for an answer"),
	//accesscode
	"Un_enable" 	    => array( "en" => "Unconditional Forward Enable" , "cn" => "無條件轉移啟動"  , "kr" => "UCF"),
	"Un_disable" 		=> array( "en" => "Unconditional Forward Disable" , "cn" => "無條件轉移解除"  , "kr" => "Unconditional_Forward"),
	"enable-busy" 	    => array( "en" => "Busy and unreachable Forward enable" , "cn" => "忙線與未上線轉移啟動"  , "kr" => "BUF"),
	"disable-busy" 	    => array( "en" => "Busy and unreachable Forward disable" , "cn" => "忙線與未上線轉移解除"  , "kr" => "BUF"),
    "enable-nofwtitle" 	=> array( "en" => "No Answer Forward Enable" , "cn" => "無人回應轉移啟動"  , "kr" => "NAF"),
	"disable-nofwtitle" => array( "en" => "No Answer Forward Disable" , "cn" => "無人回應轉移解除"  , "kr" => "NAF"),
	"ring-timer"        => array( "en" => "Ring Timer(Sec)" , "cn" => "分機響鈴時間(秒)"  , "kr" => "NAF"),
	"pickup"        	=> array( "en" => "Call Pickup" , "cn" => "代接"  , "kr" => "Call Pickup"),
	
	
	// dial group
	"ringtype" 	 		=> array( "en" => "Ring Type" , "cn" => "響鈴種類"  , "kr" => "Ring Type"),
	"simultaneous_ring" => array( "en" => "Simultaneous" , "cn" => "群響"  , "kr" => "Simultaneous"),
	"serial_ring" 		=> array( "en" => "Serial" , "cn" => "循響"  , "kr" => "Serial"),
	"serial_ringtime" 	=> array( "en" => "Serial Ring Time" , "cn" => "循響時間"  , "kr" => "Serial Ring Time"),
	
	
	
	
	);
	
?>
