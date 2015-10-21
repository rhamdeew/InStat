<?php

if(isset($_SERVER['REQUEST_METHOD'])) {
	die('No way!');
}

require 'vendor/autoload.php';
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

ORM::configure('mysql:host=localhost;dbname='.getenv('DB_NAME'));
ORM::configure('username', getenv('DB_USER'));
ORM::configure('password', getenv('DB_PASSWORD'));

$shortopts = "";
$longopts = array(
	"set::",
	"unset::",
	"fileset::",
	"fileunset::",
	);

$options = getopt($shortopts,$longopts);

$mode = 'help';
if(isset($options['set'])) {
	$mode = 'set';
	$susers = $options['set'];
}
if(isset($options['unset'])) {
	$mode = 'unset';
	$susers = $options['unset'];
}
if(isset($options['fileset'])) {
	$mode = 'fileset';
	$file = $options['fileset'];
}
if(isset($options['fileunset'])) {
	$mode = 'fileunset';
	$file = $options['fileunset'];
}

if($mode!=='help') {
	$users = array();
	if($mode=='fileset' || $mode=='fileunset') {
		$handle = fopen($file,'r');
		if ($handle) {
			while (($buffer = fgets($handle, 4096)) !== false) {
				$users[] = trim($buffer);
			}
			fclose($handle);
		}
		if($mode=='fileset') {
			$mode = 'set';
		}
		else {
			$mode = 'unset';
		}
	}
	elseif($mode=='set' || $mode=='unset') {
		$users = explode(',',$susers);
	}
	// var_dump($users);
	// var_dump($mode);

	foreach($users as $user) {
		$dbUser = ORM::for_table('user')->where('user_name',$user)->find_one();
		$banned = 0;
		if($mode=='set') {
			$banned = 1;
		}
		if($dbUser) {
			$dbUser->banned=$banned;
			$dbUser->save();
			$dbPhotos = ORM::for_table('photos')->where('user_id',$dbUser->user_id)->find_many();
			$i = 0;
			foreach($dbPhotos as $photo) {
				$photo->banned = $banned;
				if($photo->save())
					$i++;
			}
			if($mode=='set') {
				echo "Ban user: ".$user."\n";
				echo "Banned ".$i." photos\n";
			}
			else {
				echo "Unban user: ".$user."\n";
				echo "Unbanned ".$i." photos\n";
			}
		}
	}
}
else {
	echo "\n";
	echo "Usage:\n";
	echo "--set=username(,username2) - ban username(s)\n";
	echo "--unset=username(,username2) - unban username(s)\n";
	echo "--fileset=ban.txt - ban usernames, one username per line\n";
	echo "--fileunset=ban.txt - unban usernames, one username per line\n";
	echo "\n\n";
}

echo "Done!\n";
