<?php
$db = new SQLite3("db/test.db") or die('Unable to open database');

$stm = "CREATE TABLE IF NOT EXISTS user(id integer PRIMARY KEY," . 
	"username text,".
	"email text UNIQUE NOT NULL,".
	"active integer NOT NULL)";
#$stm = "DROP TABLE IF EXISTS user";
$db->exec($stm);

$stm = "CREATE TABLE IF NOT EXISTS log(id integer PRIMARY KEY," . 
	"id_user text NOT NULL,".
	"content text NOT NULL,".
	"date INTEGER NOT NULL)";
#$stm = "DROP TABLE IF EXISTS log";
$db->exec($stm);

function createUser($id, $username, $email) {
	global $db;
	$insert = "INSERT INTO user VALUES ('".SQLite3::escapeString($id)."', '".SQLite3::escapeString($username)."', '".SQLite3::escapeString($email)."', 1)";
	print($insert."\n");
	return $db->exec($insert);
}

function createLog($id, $user_id, $message, $date) {
	global $db;
	$insert = "INSERT INTO log VALUES ('".SQLite3::escapeString($id)."', '".SQLite3::escapeString($user_id)."', '".SQLite3::escapeString($message)."', ".SQLite3::escapeString($date).")";
	print($insert."\n");
	return $db->exec($insert);
}

function getLastLog($user_id) {
	global $db;
	$select = "SELECT content FROM log WHERE id_user=".SQLite3::escapeString($user_id)." ORDER BY date DESC LIMIT 1";
	print($select."\n");
	$results = $db->query($select);
	while ($row = $results->fetchArray()) {
		return $row['content'];
	}
}

function getLastLogDate($user_id) {
        global $db;
        $select = "SELECT content FROM log WHERE id_user=".SQLite3::escapeString($user_id)." ORDER BY date DESC LIMIT 1";
        print($select."\n");
        $results = $db->query($select);
        while ($row = $results->fetchArray()) {
                return $row['date'];
        }
}

function getLastLogRow($user_id) {
	global $db;
	$select = "SELECT * FROM log WHERE id_user=".SQLite3::escapeString($user_id)." ORDER BY date DESC LIMIT 1";
	print($select."\n");
	$results = $db->query($select);
	return $results;
}

function getUserRows($user_id) {
	global $db;
	$select = "SELECT * FROM user";
	print($select."\n");
	$results = $db->query($select);
	return $results;
}


function updateUserActive($id, $email, $active) {
	global $db;
	$update = "UPDATE user SET active=";
	if($active)
		$update = $update."1 ";
	else
		$update = $update."0 ";
	$update = $update."WHERE id=".SQLite3::escapeString($id)." AND email='".SQLite3::escapeString($email)."'";
	print($update."\n");
	$db->exec($update);
	return isUserActive($id) == $active;
}

function isUserActive($id) {
	global $db;
	$select = "SELECT active FROM user WHERE id=".SQLite3::escapeString($id);
	print($select."\n");
	$results = $db->query($select);
	while ($row = $results->fetchArray()) {
		if($row['active']===0)
			return false;
		else
			return true;
	}
	return false;
}

function getUserEmail($id) {
	global $db;
	$select = "SELECT email FROM user WHERE id=".SQLite3::escapeString($id);
	print($insert."\n");
	$results = $db->query($select);
	while ($row = $results->fetchArray()) {
		return $row['email'];
	}
}
?>
