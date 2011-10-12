<?php



	
//require 'AsteriskManagerException.php';

//class Net_AsteriskManager

//    $NOSOCKET      = 'No socket defined';
//    $AUTHFAIL      = 'Authorisation failed';
//    $CONNECTFAILED = 'Connection failed';
//    $NOCOMMAND     = 'Unknown command specified';
//    $NOPONG        = 'No response to ping';
//    $NOSERVER      = 'No server specified';
//    $MONITORFAIL   = 'Monitoring of channel failed';
//    $RESPERR       = 'Server didn\'t respond as expected';
//    $CMDSENDERR    = 'Sending of command failed';


  
    $server = "";
    $port = "";
    $_socket = "";
	$ActionID = 0;
 
    function Net_AsteriskManager( $server1 , $port1 )
    {
		
		global $server;
		global $port;
		
        $server = $server1;
        $port = $port1;
    }

    function close()
    {

		global $_socket;
		
		socket_close($_socket);

    //    return fclose($_socket);
    }	

    function connect()
    {
		global $_socket;
		global $server;
		global $port;
		

        if ($_socket)
            $close();
	
        if ($_socket = fsockopen($server, $port,&$errno,&$errstr,30))
		{
		//	set_socket_blocking( $_socket , false );
      //     stream_set_timeout($_socket, 3);
            return "1";
        }
		return "0";

    }

	function _sendCommand($command)
    {

		global $_socket;
	
	        if (!fputs($_socket, $command) ) 
			return "0";

/* 	
        if (!fwrite($_socket, $command) ) 
			return "0";
 */        				
//		to slowly ....		
		$response = "";
		$i = 0;
//		$response = stream_get_contents($_socket);	
	
	

		while(1)
		{
			$res .= fgets($_socket);
			$response = $res;
			if( strstr( $res , "\r\n\r\n" ) != "" || $i++ > 10000 )
				break;
		}

	
	
       return $response;
	   
  
    }
	
	
	function login($username, $password )
    {   

		global $ActionID;
				
		
		$Strlogin = "action: Login\r\n";
//		$Strlogin .= sprintf("actionid: 27772036_%d#\r\n",$ActionID++);
		
		$Strlogin .= "username: ";
		$Strlogin .= "$username\r\n";
		$Strlogin .= "secret: ";
		$Strlogin .= "$password\r\n\r\n";
	

        $response = _sendCommand($Strlogin);		
				
		return $response;
    }
	
    function logout()
    {
 		global $_socket;
       _sendCommand("Action: Logoff\r\n\r\n");
		fclose($_socket);
    }
	function delDBtree( $family )
    {
		if( $family == "" )
			return;
			
		$StrCmd = "action: Command\r\n";
		$StrCmd .= "command: database deltree $family\r\n\r\n";
        $response = _sendCommand($StrCmd);
        return $response;
    }		

    function delDB( $family , $key )
    {
		if( $family == "" || $key == ""  )
			return;
			
		$StrCmd = "action: Command\r\n";
		$StrCmd .= "command: database del $family $key\r\n\r\n";
        $response = _sendCommand($StrCmd);
        return $response;
    }		
    function putDB( $family , $key , $value )
    {
		if( $family == "" || $key == "" || $value == "" )
			return;
			
		$StrCmd = "action: Command\r\n";
		$StrCmd .= "command: database put $family $key $value\r\n\r\n";
        $response = _sendCommand($StrCmd);
        return $response;
    }		
    function getDB()
    {
		$StrCmd = "action: Command\r\n";
		$StrCmd .= "command: database show\r\n\r\n";
        $response = _sendCommand($StrCmd);
        return $response;
    }	
    function getDBValue( $family , $key )
    {
		$StrCmd = "action: Command\r\n";
		$StrCmd .= "command: database get $family $key\r\n\r\n";
        $response = _sendCommand($StrCmd);
 		echo "$key ---> $response<br>";
       return $response;
    }		
    function getSipPeers()
    {
		$StrCmd = "Action: SIPpeers\r\n\r\n";
        $response = _sendCommand($StrCmd);
        return $response;
    }	
    function SipShowRegistry()
    {
		$StrCmd = "action: Command\r\n";
		$StrCmd .= "command: sip show registry\r\n\r\n";
        $response = _sendCommand($StrCmd);
        return $response;
    }	
    function ShowChannels()
    {
		$StrCmd = "action: Command\r\n";
		$StrCmd .= "command: core show channels\r\n\r\n";
        $response = _sendCommand($StrCmd);
        return $response;
    }	
    function SipShowPeers()
    {
		$StrCmd = "action: Command\r\n";
		$StrCmd .= "command: sip show peers\r\n\r\n";
		$response = _sendCommand($StrCmd);
        return $response;
    }	
	
    function SipShowPeer( $Peer )
    {
		$StrCmd = "action: Command\r\n";
		$StrCmd .= "command: sip show peer $Peer\r\n\r\n";
        $response = _sendCommand($StrCmd);
        return $response;
    }		
	
	
/*
  


 
    
  
    function connect()
    {
        if ($this->_socket) {
            $this->close();
        }
        
        if ($this->_socket = fsockopen($this->server, $this->port)) {
            stream_set_timeout($this->_socket, 3);
            return true;
        }
        
        throw new Net_AsteriskManagerException (
            Net_AsteriskManagerException::CONNECTFAILED
        );
    }

  
   
 


 


    function command($command)
    {
        $this->_checkSocket();
    
        $response = $this->_sendCommand("Action: Command\r\n"
            ."Command: $command\r\n\r\n");

        if (strpos($response, 'No such command') !== false) {
            throw new Net_AsteriskManagerException(
                Net_AsteriskManagerException::NOCOMMAND
            );
        }
        return $response;
    }

 
    function ping()
    {
        $this->_checkSocket();

        $response = $this->_sendCommand("Action: Ping\r\n\r\n");
        if (strpos($response, "Pong") === false) {
            return false;
        }
        return true;
    }

 
    function originateCall($extension, 
                           $channel, 
                           $context, 
                           $cid, 
                           $priority = 1, 
                           $timeout = 30000, 
                           $variables = null, 
                           $action_id = null)
    {
        $this->_checkSocket();
        
        $command = "Action: Originate\r\nChannel: $channel\r\n"
            ."Context: $context\r\nExten: $extension\r\nPriority: $priority\r\n"
            ."Callerid: $cid\r\nTimeout: $timeout\r\n";

        if (count($variables) > 0) {
            $chunked_vars = array();
            foreach ($variables as $key => $val) {
                $chunked_vars[] = "$key=$val";
            }
            $chunked_vars = implode('|', $chunked_vars);
            $command     .= "Variable: $variables\r\n";
        }

        if ($action_id) {
            $command .= "ActionID: $action_id\r\n";
        }
        $this->_sendCommand($command."\r\n");
        return true;
    }

  
    function getQueues()
    {
        $this->_checkSocket();

        $response = $this->_sendCommand("Action: Queues\r\n\r\n");
        return $response;
    }

 
    function queueAdd($queue, $handset, $penalty = null)
    {
        $this->_checkSocket();
        
        $command = "Action: QueueAdd\r\nQueue: $queue\r\n"
                    ."Interface: $handset\r\n";

        if ($penalty) {
            $this->_sendCommand($command."Penalty: $penalty\r\n\r\n");
            return true;
        }

        $this->_sendCommand($command."\r\n");
        return true;
    }

 
    function queueRemove($queue, $handset) 
    {
        $this->_checkSocket();
        
        $this->_sendCommand("Action: QueueRemove\r\nQueue: $queue\r\n"
            ."Interface: $handset\r\n\r\n");

        return true;
    }

  
    function startMonitor($channel, $filename, $format, $mix = null)
    {
        
        $this->_checkSocket();
        
        $response = $this->_sendCommand("Action: Monitor\r\nChannel: $channel\r\n"
                               ."File: $filename\r\nFormat: $format\r\n"
                               ."Mix: $mix\r\n\r\n");
        
        if (strpos($response, "Success") === false) {
            throw new Net_AsteriskManagerException(
                Net_AsteriskManagerException::MONITORFAIL
            );
        } else {
            return true;
        }
    }

 
    function stopMonitor($channel)
    {
        $this->_checkSocket();
        
        $this->_sendCommand("Action: StopMonitor\r\n"
                            ."Channel: $channel\r\n\r\n");
        return true;
    }

   
    function getChannelStatus($channel = null)
    {
        $this->_checkSocket();
        
        $response = $this->_sendCommand("Action: Status\r\nChannel: "
            ."$channel\r\n\r\n");
        
        return $response;
    }



 
    function getIaxPeers() 
    {
        $this->_checkSocket();

        $response = $this->_sendCommand("Action: IAXPeers\r\n\r\n");
        return $response;
    }

 
    function parkedCalls()
    {
        $this->_checkSocket();

        $response = $this->_sendCommand("Action: ParkedCalls\r\n"
            ."Parameters: ActionID\r\n\r\n");
        return $response;
    }
	
*/	
	


?>
