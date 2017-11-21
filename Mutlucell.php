<?php
class Mutlucell {
    public $userName;
    public $password;
    public $apiKey;
    public $result;
    private $method;
    private $json = array();
    public $error = "";

	/**
	 * Check if user information is it API or mobile and password
     * and set curl as default send  method
     *
     * @param string $userName The Account username or mobile in https://www.mutlucell.com.tr/
     * @param string $password The password of mutlucell account
     * @param string $apiKey The api key from  mutlucell account
	 **/
    function __construct($userName=NULL, $password=NULL, $apiKey=NULL) {
        if (!empty($apiKey)){
            $this->apiKey = $apiKey;
        }elseif(!empty($userName)&&!empty($userName)){
            $this->userName = $userName;
            $this->password = $password;
        }
		$this->method = 'curl';
    }

    /**
     * Check if user information is it API or mobile and password And if
     * this information is not empty set in variables for all api function other return error
     *
     * @param string $userName The Account username or mobile in https://www.mutlucell.com.tr/
     * @param string $password The password of multucell account
     * @param string $apiKey The api key from  multucell account
     * @return string $this->error If there is no error, it doesn't return anything
     **/
    public function setInfo($userName=NULL,   $password=NULL, $apiKey=NULL) {
		if(empty($userName) && empty($password) && empty($apiKey)) {
			$this->error = 'Please Insert Data';	
		} elseif (!empty($apiKey)) {
			$this->apiKey = $apiKey;
		} elseif(!empty($userName) & !empty($password)) {
			$this->userName = $userName;
            $this->password = $password;			
		}
		return $this->error;
    }

    /**
     * Check if user information is not empty and
     * prepare information in array to Merge with another message data
     * you can call this function just in api function because it's private
     *
     **/
    private function checkUserInfo() {
		$this->json = array();
		$this->error = "";
        if (!empty($this->apiKey)) {
            $this->json=array("apiKey"=>$this->apiKey);
        } elseif (!empty($this->userName) && !empty($this->password)) {
            $this->json=array("mobile"=>$this->userName,"password"=>$this->password);
        } else {
            $this->error = 'insert APIKEY or Username and Password';
        }
    }

    /**
     * Using  send method you'r selected in api function and
     * if doesn't match with any cases return error
     *
     * @param string $host The Account username or mobile in https://www.mutlucell.com.tr/ (required)
     * @param string $path The password of mutlucell account(required)
     * @param string $data Message data
     * @return string $this->error If any error found
     * @return string $this->result If there is no error , it's json report from https://www.mutlucell.com.tr/
     **/
    private function run($host,$path,$data='') {
        switch ($this->method) {
            case 'curl':
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $host.$path);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
                curl_setopt($ch, CURLOPT_POSTFIELDS, "$data");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $this->result = curl_exec($ch);
                break;
            case 'fsockopen':
                $host=str_replace('https://','',$host);
                $host=str_replace('http://','',$host);
                $length = strlen($data);
                $fsockParameter = "POST ".$path." HTTP/1.1\r\n";
                $fsockParameter .= "Host: ".$host."\r\n";
                $fsockParameter .= "Content-Type: text/xml\r\n";
                $fsockParameter .= "Connection: close\r\n";
                $fsockParameter .= "Content-Length: $length\r\n\r\n";
                $fsockParameter .= "$data";
                $fsockConn = fsockopen($host, 80, $errno, $errstr, 30);
                fwrite($fsockConn, $fsockParameter);
                $clearResult = false;
                while (!feof($fsockConn)) {
                    $line = fgets($fsockConn, 10240);
                    if ($line == "\r\n" && !$clearResult)
                        $clearResult = true;
                    if ($clearResult){
                        $this->result .= $line;
                    }
                }
                break;
            case 'fopen':
                $contextOptions['http'] = array('method' => 'POST', 'header'=>'Content-type: application/x-www-form-urlencoded', 'content'=> $data, 'max_redirects'=>0, 'protocol_version'=> 1.0, 'timeout'=>10, 'ignore_errors'=>TRUE);
                $contextResouce  = stream_context_create($contextOptions);
                $handle = fopen($host.$path, 'r', false, $contextResouce);
                $this->result = stream_get_contents($handle);
                break;
            case 'file':
                $contextOptions['http'] = array('method' => 'POST', 'header'=>'Content-type: application/x-www-form-urlencoded', 'content'=> $data, 'max_redirects'=>0, 'protocol_version'=> 1.0, 'timeout'=>10, 'ignore_errors'=>TRUE);
                $contextResouce  = stream_context_create($contextOptions);
                $arrayResult = file($host.$path, FILE_IGNORE_NEW_LINES, $contextResouce);
                $this->result = $arrayResult;
                break;
            case 'file_get_contents':
                $contextOptions['http'] = array('method' => 'POST', 'header'=>'Content-type: application/x-www-form-urlencoded', 'content'=> $data, 'max_redirects'=>0, 'protocol_version'=> 1.0, 'timeout'=>10, 'ignore_errors'=>TRUE);
                $contextResouce  = stream_context_create($contextOptions);
                $this->result = file_get_contents($host.$path, false, $contextResouce);
                break;
            default:
                $this->error = 'active one of the following portals (curl,fopen,fsockopen,file,file_get_contents) on server';
                return $this->error;
        }

        return $this->result;
    }


    /**
     * Send  message directly without separate message data
     * you can use to call function (sendMsg Or sendMsgWK).
     *
     * @param string $functionName Name of the function (required)
     * @param string $data Message data (required)
     * @return string $this->error If any error found
     * @return string $this->result If there is no error , it's json report from https://www.mutlucell.com.tr/
     **/
    public function callAPI ($functionName, $data,$port=NULL) {
        $this->checkUserInfo();
        $this->getSendMethod($port);
        if(empty($this->error)) {
            switch ($functionName) {
                case 'sendBulk':
                        return $this->run('https://smsgw.mutlucell.com', '/smsgw-ws/sndblkex', $data);
                    break;
                case 'searchBulkReport':
                        return $this->run('https://smsgw.mutlucell.com', '/smsgw-ws/srchblkrprtapi', $data);
                    break;
                default:
                    $this->error[] = 'method name not found You can select either (sendBulk,searchBulkReport).';
                    return $this->error;
            }
        }else{
            return $this->error;
        }
    }

    /**
     * Check if send method selected in function and
     * test send method if work or if method doesn't selected
     * test method  and choose which works
     *
     * @param string $method Send method
     * @return string $this->error If not empty method
     **/
	private function getSendMethod($method=NULL) {
		//Change Deafult Method
		if(!empty($method)){
			$this->method = strtolower($method);
		}
		//Check CURL
		if($this->method == 'curl') {
			if(function_exists("curl_init") && function_exists("curl_setopt") && function_exists("curl_exec") && function_exists("curl_close") && function_exists("curl_errno")) {
				return 1;
			} else {
				if(!empty($method)) {
					return $this->error = 'CURL is not supported';
				} else {
					$this->method = 'fsockopen';
				}
			}			
		}
		//Check fSockOpen
		if($this->method == 'fsockopen') {
			if(function_exists("fsockopen") && function_exists("fputs") && function_exists("feof") && function_exists("fread") && function_exists("fclose")) {
				return 1;
			} else {
				if(!empty($method)) {
					return $this->error = 'fSockOpen is not supported';
				} else {
					$this->method = 'fopen';
				}
			}			
		}
		//Check fOpen
		if($this->method == 'fopen') {
			if(function_exists("fopen") && function_exists("fclose") && function_exists("fread")) {
				return 1;
			} else {
				if(!empty($method)) {
					return $this->error = 'fOpen is not supported';
				} else {
					$this->method = 'file_get_contents';
				}
			}			
		}
		//Check File
		if($this->method == 'file') {
			if(function_exists("file") && function_exists("http_build_query") && function_exists("stream_context_create")) {
				return 1;
			} else {
				if(!empty($method)) {
					return $this->error = 'File is not supported';
				} else {
					$this->method = 'file_get_contents';
				}
			}			
		}
		//Check file_get_contents
		if($this->method == 'file_get_contents') {
			if(function_exists("file_get_contents") && function_exists("http_build_query") && function_exists("stream_context_create")) {
				return 1;
			} else {
				if(!empty($method)) {
					return $this->error = 'file_get_contents is not supported';
				} else {
					$this->method=NULL;
				}
			}			
		}				
    }


    /**
     * Send Messages
     *
     * @param $message The messages will be send
     * @param $numbers Numbers that will be sending message to her
     * @param $sender The name of messages
     * @param string $tarih If the message is sent at a later date, it can be added in a parameter of the format date = "yyyy-aa-gg ss: dd". This parameter should not be used if the message is sent immediately.
     * @param string $bitis Sent messages are normally tested for 24 hours. Increasing this time can be added to a parameter in the format bitis = "yyyy-aa-gg ss: dd" to reduce yada.
     * @param string $addLinkToEnd Add this parameter at the end of your final message text, adding the description "If you do not want to receive our messages http://izn.im/ABCDEFG". The numbers receiving the message can indicate that they do not want to receive another message by clicking on the link. In this case the message number is automatically added to the blacklist and sending of the sms to this number is blocked.
     * @param string $ip Web systems provide services to users in multiple and different locations via local programs, the IP of the system sending the SMS
     * @param string $aboneid If the sender of the message is a subscriber, the subscriber id must be specified (eg: aboneid = "14587").
     * @param string $bayiid It is used for special submissions to the applications of the shoppers (for example: bayerid = "xyzsoft")
     * @param string $zamanayay It allows the message package to be sent in pieces for a specified period of time. The time parameter is written in minutes and must be at least 30
     * @param string $charset It is used to send the message using local characters (?, ? ...) in languages ??such as Turkish (for example: charset = "turkish").
     * @param null $method Send method
     * @return string
     */
    public function sendBulk($message, $numbers, $sender, $tarih='', $bitis ='', $addLinkToEnd ='', $ip='', $aboneid='', $bayiid='', $zamanayay='', $charset='default', $method=NULL) {
        $this->checkUserInfo();
        $this->getSendMethod($method);
        $variable='';
        if(empty($this->error)) {
            if(!empty($this->userName))
                $variable.='  ka="'.$this->userName.'" ';
            if(!empty($this->password))
                $variable.=' pwd="'. $this->password.'" ';
            if(!empty($sender))
                $variable.=' org="'.$sender.'" ';
            if(!empty($tarih))
                $variable.='tarih="' .$tarih. '" ';
            if(!empty($bitis))
                $variable.=' bitis="' .$bitis. '" ';
            if(!empty($addLinkToEnd))
                $variable.='addLinkToEnd="'.$addLinkToEnd.'" ';
            if(!empty($ip))
                $variable.='IP="'.$ip.'" ';
            if(!empty($aboneid))
                $variable.='aboneid="'.$aboneid.'" ';
            if(!empty($bayiid))
                $variable.=' bayiid="'.$bayiid.'" ';
            if(!empty($zamanayay))
                $variable.=' zamanayay="'.$zamanayay.'" ';
            if(!empty($charset))
                $variable.=' charset="'.$charset.'" ';
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' .
                '<smspack '.$variable.'>';
            $xml .= '<mesaj>' . '<metin>' . $message. '</metin>' . '<nums>' . $numbers. '</nums>' . '</mesaj>';
            $xml .= '</smspack>';

            return $this->run('https://smsgw.mutlucell.com','/smsgw-ws/sndblkex',$xml);
        }

        return $this->error;
    }

    /**
     * This function get all incoming messages
     *
     * @param $aboneno Your username is your connected subscriber number. The messages in your inbox have been sent to this number. (e.g. 0850 550 XX XX)
     * @param $startdate The start date of the messages you want to list. In your inbox, messages sent after this date will be listed.
     * @param $enddate The end date of the messages you want to list. Your inbox lists the messages sent by this thread.
     * @param null $method Send method
     * @return string
     */
    public function incomeing($aboneno, $startdate, $enddate, $method=NULL) {
        $this->checkUserInfo();
        $this->getSendMethod($method);
        $variable='';
        if(empty($this->error)) {
            if(!empty($this->password))
                $variable.=' pwd="'. $this->password.'" ';
            if(!empty($aboneno))
                $variable.=' aboneno="'.$aboneno.'" ';
            if(!empty($startdate))
                $variable.=' startdate="' .$startdate. '" ';
            if(!empty($enddate))
                $variable.=' enddate="' .$enddate. '" ';
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' .
                '<increport '.$variable.'/>';
            return $this->run('https://smsgw.mutlucell.com','/smsgw-ws/gtincmngapi',$xml);
        }
        return $this->error;
    }

    /**
     * With the ID of a message packet sent, the status of the SMS in that packet can be queried.
     *
     * @param $id The id parameter here is the ID that the SMS server sends for the message package for which the report is to be received.
     * @param null $method Send method
     * @return string
     */
    public function bulkReport  ($id, $method=NULL) {
            $this->checkUserInfo();
        $this->getSendMethod($method);
        $variable='';
        if(empty($this->error)) {
            if(!empty($this->userName))
                $variable.='  ka="'.$this->userName.'" ';
            if(!empty($this->password))
                $variable.=' pwd="'. $this->password.'" ';
            if(!empty($id))
                $variable.=' id="'.$id.'" ';
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' .
                '<smsrapor '.$variable.' />';
            return $this->run('https://smsgw.mutlucell.com','/smsgw-ws/gtblkrprtex',$xml);
        }
        return $this->error;
    }

    /**
     * Status reports of packages sent in a certain time interval. The time interval can not exceed 30 days.
     *
     * @param string $tarih If the message is sent at a later date, it can be added in a parameter of the format date = "yyyy-aa-gg ss: dd". This parameter should not be used if the message is sent immediately.
     * @param string $bitis Sent messages are normally tested for 24 hours. Increasing this time can be added to a parameter in the format bitis = "yyyy-aa-gg ss: dd" to reduce yada.
     * @param null $method Send method
     * @return string
     */
    public function bulkSummaryReport($tarih, $bitis, $method=NULL) {
            $this->checkUserInfo();
        $this->getSendMethod($method);
        $variable='';
        if(empty($this->error)) {
            if(!empty($this->userName))
                $variable.='  ka="'.$this->userName.'" ';
            if(!empty($this->password))
                $variable.=' pwd="'. $this->password.'" ';
            if(!empty($tarih))
                $variable.=' tarih="'.$tarih.'" ';
            if(!empty($bitis))
                $variable.=' bitis="'.$bitis.'" ';
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' .
                '<gonrapor '.$variable.' />';
            return $this->run('https://smsgw.mutlucell.com','/smsgw-ws/gtsummaryex',$xml);
        }
        return $this->error;
    }

    /**
     * To get bulk messages using number
     * @param $gsmno The numbers sent in a message
     * @param string $tarih If the message is sent at a later date, it can be added in a parameter of the format date = "yyyy-aa-gg ss: dd". This parameter should not be used if the message is sent immediately.
     * @param string $bitis Sent messages are normally tested for 24 hours. Increasing this time can be added to a parameter in the format bitis = "yyyy-aa-gg ss: dd" to reduce yada.
     * @param null $method Send method
     * @return string
     */
    public function searchBulkReport($gsmno, $tarih, $bitis, $method=NULL) {
        $this->checkUserInfo();
        $this->getSendMethod($method);
        $variable='';
        if(empty($this->error)) {
            if(!empty($this->userName))
                $variable.='  ka="'.$this->userName.'" ';
            if(!empty($this->password))
                $variable.=' pwd="'. $this->password.'" ';
            if(!empty($gsmno))
                $variable.=' gsmno="'.$gsmno.'" ';
            if(!empty($tarih))
            $variable.=' tarih="'.$tarih.'" ';
            if(!empty($bitis))
                $variable.=' bitis="'.$bitis.'" ';
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' .
                '<gonrapor '.$variable.' />';
            return $this->run('https://smsgw.mutlucell.com','/smsgw-ws/srchblkrprtapi',$xml);
        }
        return $this->error;
    }

    /**
     * To get Balance
     *
     * @param null $method Send method
     * @return string
     */
    public function credit($method=NULL) {
        $this->checkUserInfo();
        $this->getSendMethod($method);
        $variable='';
        if(empty($this->error)) {
            if(!empty($this->userName))
                $variable.='  ka="'.$this->userName.'" ';
            if(!empty($this->password))
                $variable.=' pwd="'. $this->password.'" ';
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' .
                '<smskredi  '.$variable.' />';
            return $this->run('https://smsgw.mutlucell.com','/smsgw-ws/gtcrdtex',$xml);
        }
        return $this->error;
    }

    /**
     * To get originators name
     *
     * @param null $method
     * @return string
     */
    public function originators($method=NULL) {
        $this->checkUserInfo();
        $this->getSendMethod($method);
        $variable='';
        if(empty($this->error)) {
            if(!empty($this->userName))
                $variable.='  ka="'.$this->userName.'" ';
            if(!empty($this->password))
                $variable.=' pwd="'. $this->password.'" ';
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' .
                '<smsorig  '.$variable.' />';
            return $this->run('https://smsgw.mutlucell.com','/smsgw-ws/gtorgex',$xml);
        }
        return $this->error;
    }

    /**
     * To cancel Pending Messages
     *
     * @param $id The message id that returned from send process
     * @param null $method Send method
     * @return string
     */
    public function cancelPendingBulk($id, $method=NULL) {
        $this->checkUserInfo();
        $this->getSendMethod($method);
        $variable='';
        if(empty($this->error)) {
            if(!empty($this->userName))
                $variable.='  ka="'.$this->userName.'" ';
            if(!empty($this->password))
                $variable.=' pwd="'. $this->password.'" ';
            if(!empty($id))
                $variable.=' id="'.$id.'" ';
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' .
                '<smsiptal  '.$variable.' />';
            return $this->run('https://smsgw.mutlucell.com','/smsgw-ws/cnclblkex',$xml);
        }
        return $this->error;
    }

    /**
     * To add any numbers to your black list
     *
     * @param string $numbers Numbers that you want to add to black list
     * @param null $method Send method
     * @return string
     */
    public function addBlacklist($numbers='', $method=NULL) {
        $this->checkUserInfo();
        $this->getSendMethod($method);
        $variable='';
        if(empty($this->error)) {
            if(!empty($this->userName))
                $variable.='  ka="'.$this->userName.'" ';
            if(!empty($this->password))
                $variable.=' pwd="'. $this->password.'" ';

            $xml = '<?xml version="1.0" encoding="UTF-8"?>' .
                '<addblacklist '.$variable.' ><nums>'.$numbers.'</nums></addblacklist>';

            return $this->run('https://smsgw.mutlucell.com','/smsgw-ws/addblklst',$xml);
        }
        return $this->error;
    }

    /**
     * To delete Numbers from your black list
     * @param string $numbers Numbers that you want to delete from black list
     * @param null $method Send method
     * @return string
     */
    public function deleteBlacklist($numbers='', $method=NULL) {
        $this->checkUserInfo();
        $this->getSendMethod($method);
        $variable='';
        if(empty($this->error)) {
            if(!empty($this->userName))
                $variable.='  ka="'.$this->userName.'" ';
            if(!empty($this->password))
                $variable.=' pwd="'. $this->password.'" ';

            $xml = '<?xml version="1.0" encoding="UTF-8"?>' .
                '<dltblacklist '.$variable.' ><nums>'.$numbers.'</nums></dltblacklist>';

            return $this->run('https://smsgw.mutlucell.com','/smsgw-ws/dltblklst',$xml);
        }
        return $this->error;
    }

    /**
     * To get all numbers in black list
     * @param null $method Send method
     * @return string
     */
    public function showBlacklist($method=NULL) {
        $this->checkUserInfo();
        $this->getSendMethod($method);
        $variable='';
        if(empty($this->error)) {
            if(!empty($this->userName))
                $variable.='  ka="'.$this->userName.'" ';
            if(!empty($this->password))
                $variable.=' pwd="'. $this->password.'" ';

            $xml = '<?xml version="1.0" encoding="UTF-8"?>' .
                '<blacklist '.$variable.' />';

            return $this->run('https://smsgw.mutlucell.com','/smsgw-ws/gtblklst',$xml);
        }
        return $this->error;
    }

}