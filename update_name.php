<?php
class Update_name{
	private $_screen_name;
	private $_twitter;
	private $_temp;
	private $_template;
	function __construct(){
		chdir(dirname(__FILE__));
		date_default_timezone_set("Asia/Tokyo");
		require_once("setting.php");
		$this->_screen_name = $screen_name;
		$this->_template = $template;
		$this->_temp = "";
		require_once("lib/OAuth.php");
		require_once("lib/twitteroauth.php");
		$this->_twitter = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
	}
	function run(){
		return $this->_twitter->StreamRequest(array($this,"UserStreamCallback"));
	}
	function UserStreamCallback($ch,$data){
		$strlen = strlen($data);
		if(strpos($data,"\r\n"===false)){
			return $strlen;
		}
		$split = explode("\r\n",$data);
		$content = $this->_temp . $split[0];
		
		if (!is_callable(array($this,"UserStreamOutput"))) return 0;
		$stop = call_user_func(
				array($this,"UserStreamOutput"),
				json_decode($content,true)
		);
		$this->_temp = @$split[1];
		if($stop){
			return 0;
		}
		return $strlen;
	}
	function UserStreamOutput($json){
		if(!empty($json["text"])){
			if(empty($json["retweeted_status"]["text"])){
				if(preg_match("/^.*?[a-zA-Z]{2} \@?[a-zA-Z0-9_]+/ui",$json["text"])){
					if(!preg_match("/^\@".$this->_screen_name." update_name (.+)$/iu",$json["text"])){
						return 0;
					}
				}
				$name = null;
				if(preg_match("/^(.+?)(（|\()\@".$this->_screen_name."(\)|）)$/iu",$json["text"],$preg)){
					$name = $preg[1];
				}else if(preg_match("/^\@".$this->_screen_name." update_name (.+)$/iu",$json["text"],$preg)){
					$name = $preg[1];
				}
				if(!empty($name)){
					$this->_twitter->post("account/update_profile",array("name"=>$name));
					if(!empty($this->_template)){
						$status = str_replace("{name}",$name,str_replace("{screen_name}",$json["user"]["screen_name"],$this->_template));
						$update_id = $json["id_str"];
						$this->_twitter->post("statuses/update",array("status"=>$status,"in_reply_to_status_id"=>$update_id));
					}
				}
			}
		}
		return 0;
	}
}
?>