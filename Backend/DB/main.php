<?php
require_once 'sqllite.php';
require_once 'telegramAPI.php';

$ifsAccount="@ifs\\.tuwien\\.ac\\.at";

function statusCommand($user_id, $first_name, $chat_id){
	if(isUserActive($user_id)){
		apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Hi ".$first_name."! You are currently logged (".getUserEmail($user_id).")!"));
	}else{
		apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Hi ".$first_name."! Who are you?"));
	}
}

function registerCommand($user_id, $username, $first_name, $chat_id, $text){
	global $ifsAccount;
	$message = trim(str_replace("/register", "", $text));
	if(preg_replace('/[a-z]+'.$ifsAccount.'/i', 'ok', $message) == 'ok'){
		if(createUser($user_id, $username, $message)){
			apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Welcome ".$first_name."! From now on you're gonna be logged!"));
		}else if(!isUserActive($user_id)){
			if(updateUserActive($user_id, $message, true)){
				apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Welcome ".$first_name."! From now on you're gonna be logged!"));
			}else{
				apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Hei ".$first_name."! The string is not your ifs account!"));
			}
		}else{
			apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Great ".$first_name."! You've been registered again, nope!"));
		}
	}else{
		apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Uhm ".$first_name."! The string \"".$message."\" does't seem to be an ifs account ([a-z]+".$ifsAccount.")"));
	}
}

function unregisterCommand($user_id, $first_name, $chat_id, $text){
	global $ifsAccount;
	$message = trim(str_replace("/unregister", "", $text));
	if(isUserActive($user_id)){
		if(preg_replace('/[a-z]+'.$ifsAccount.'/i', 'ok', $message) == 'ok'){
			if(updateUserActive($user_id, $message, false)){
				apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Bye bye ".$first_name."! Hope to see you soon!"));
			}else{
				apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Hei ".$first_name."! The string is not your ifs account"));
			}
		}else{
			apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Hei ".$first_name."! You must use your ifs account to be unregistered"));
		}
	}else{
		apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Hi ".$first_name."! Who are you?"));
	}
}

function logMessage($message_id, $user_id, $first_name, $chat_id, $text, $date){
	$message = trim(str_replace("/log", "", $text));
	if(isUserActive($user_id)){
		if(strlen($message)==0){
			apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Hi ".$first_name."! Please write me something!"));
		}else if(strlen($message)>300){
			apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Oh my gosh ".$first_name."! You should't write a poem but just a log! (you wrote ".strlen($message)." chars)"));
		}else{
			if(date("Y-m-d") == date("Y-m-d", getLastLogDate($user_id))){
				apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Blip Blup ".$first_name."! Policy violated: One log per day! (Message not logged)"));
			}else{ 
				createLog($message_id, $user_id, $message, $date);
				if($message == getLastLog($user_id)){
					apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "👍 (".strlen($message)."/300)"));
				}else{
					apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Last message of ".$first_name." has not been properly logged!\n\"".getLastLog($user_id)))."\"";
				}
			}
		}
	}else{
		apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Hi ".$first_name."! Who are you?"));
	}
}

$admin=68650763;//aldo
$admin2=175137590; //mihai
$irGroupChat_id=-54794322;
$devGroupChat_id=-44883434;
$ADAY = 86400;
$ANHOUR = 3600;

function processAdminMessage($message) {
	global $admin, $irGroupChat_id, $devGroupChat_id;
	// process incoming message
	$user_id = $message['from']['id'];
	if($user_id == $admin || $user_id == $admin2){
		$chat_id = $message['chat']['id'];

		if (isset($message['text'])) {
			// incoming text message
			$text = $message['text'];
			if(strpos($text, "/users") === 0){
				$results = getUserRows();
				$o="";
				while ($user = $results->fetchArray()) {
					$o .= $user["id"]." ".$user["username"]." ".$user["email"]." ".$user["active"]."\n";
				}
				apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => $o));
			}else if(strpos($text, "/lastlogof") === 0){
				$user_id = trim(str_replace("/lastlogof", "", $text));
				$results = getLastLogRow($user_id);
				$o="";
				while ($log = $results->fetchArray()) {
					$date = date("Y-m-d H:i:s", $log["date"]);
					$o .= $log["id"]." ".$log["id_user"]." ".$date." ".$log["content"]."\n";
				}
				apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => $o));
			}else if(strpos($text, "/sendemailto")===0){
				$args = explode(" ", trim(str_replace("/sendemailto", "", $text)));
				$to = $args[0];
				$subject = 'From your lovely Logger in Telegram';
				unset($args[0]);
				$message = implode(" ", $args);
				$headers = 'From: noreply@ifs.tuwien.ac.at' . "\r\n" .
				    'X-Mailer: PHP/' . phpversion();
				mail($to, $subject, $message, $headers);
				print($message);
			}else if(strpos($text, "/sendremaindertoregisterto")===0){
				$to = trim(str_replace("/sendremaindertoregisterto", "", $text));
				$subject = 'Please join our IR-TUWien group in Telegram';
				$results = getUserRows($user_id);
				$message = "Hi there!".
					"\nPlease join our IR-TUWien group at this link: https://telegram.me/joinchat/BBeHCwNEGFKaYX64uzIapw,\n".
					"register using your ifs account (/register surname@ifs.tuwien.ac.at),\n".
					"then log your day (no more then 300 characters), and check out what your colleagues are saying about their day:\n\n";
				while ($user = $results->fetchArray()) {
					$username = ucfirst(explode("@", $user["email"])[0]);
					$message .= $username." said \"".getLastLog($user["id"])."\"\n\n";
				}
				$message .= "\n".
					"We are looking forward to read your daily updates,\n".
					"hope to see you soon!\n\n".
					"Sincerely,\n".
					"your Logger";

				$headers = 'From: noreply@ifs.tuwien.ac.at' . "\r\n" .
				    'X-Mailer: PHP/' . phpversion();
				mail($to, $subject, $message, $headers);
				print($message);
			}else if(strpos($text, "/help")===0){
				$o=
					"/users\n".
					"/lastlog user_id\n".
					"/sendremaindertoregisterto surname@ifs.tuwien.ac.at";
				apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => $o));
			}
		} else {
		}
	}
}

function processMessage($message) {
	global $admin, $irGroupChat_id, $devGroupChat_id;
	// process incoming message
	$message_id = $message['message_id'];
	$date = $message['date'];
	$chat_id = $message['chat']['id'];
	$user_id = $message['from']['id'];
	$first_name = $message['from']['first_name'];
	$username = $message['from']['username'];

	if (isset($message['text'])){
		if($chat_id==$irGroupChat_id || $chat_id==$devGroupChat_id) {
			// incoming text message
			$text = $message['text'];
			if(strpos($text, "/log") === 0){
				logMessage($message_id, $user_id, $first_name, $chat_id, $text, $date);
			}else if(strpos($text, "/status") === 0){
				statusCommand($user_id, $first_name, $chat_id);
			}else if(strpos($text, "/register") === 0){
				registerCommand($user_id, $username, $first_name, $chat_id, $text);
			}else if(strpos($text, "/unregister") === 0){
				unregisterCommand($user_id, $username, $first_name, $chat_id, $text);
			}else if (strpos($text, "/start") === 0) {
				apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Ready to log!'));
			} else if (strpos($text, "/stop") === 0) {
				apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Bye bye!'));
			}
		} else {
			processAdminMessage($message);
		}
	} else {
	}
}

function isWeekend($date) {
    return (date('N', strtotime($date)) >= 6);
}

if (php_sapi_name() == 'cli') {
	$offset = 0;
	//$results = apiRequest('getUpdates', array());
	//$offset = $results[0]["update_id"];
	//print "******************".$offset."\n";
	//for(;;){
		// if run from console, set or delete webhook
		$results = apiRequest('getUpdates', array());
		foreach($results as &$result){
			processMessage($result["message"]);
			//print $message["chat"]["id"]."\n";	
			$offset = $results[0]["update_id"];				 
		}

		$results = apiRequest('getUpdates', array("offset" => $offset + 1));
//		$offset = $results[-1]["update_id"];
		print "offset ".$offset."\n";
		//sleep(1);
	//}

}
?>

