<?php
include_once "../incl/lib/connection.php";
include_once "../incl/lib/mainLib.php";
include "../config/dashboard.php";
require "../config/proxy.php";
$gs = new mainLib();
$file = trim(basename($_GET['request']));
switch($file) {
	case 'musiclibrary.dat': 
		if(!file_exists('gdps.dat')) {
			$time = $db->prepare('SELECT reuploadTime FROM songs WHERE reuploadTime > 0 ORDER BY reuploadTime DESC LIMIT 1');
			$time->execute();
			$time = $time->fetchColumn();
			$gs->updateLibraries($_GET['token'], $_GET['expires'], $time, 1);
		}
		echo file_get_contents('gdps.dat');
		break;
	case 'musiclibrary_version.txt': 
		$time = $db->prepare('SELECT reuploadTime FROM songs WHERE reuploadTime > 0 ORDER BY reuploadTime DESC LIMIT 1');
		$time->execute();
		$time = $time->fetchColumn();
		if(!$time) $time = 1;
		$gs->updateLibraries($_GET['token'], $_GET['expires'], $time, 1);
		$times = [];
		foreach($customLibrary AS $library) {
			if($library[2] !== null) $times[] = explode(', ', file_get_contents('s'.$library[0].'.txt'))[1];
		}
		$times[] = $time;
		rsort($times);
		echo $times[0];
		break;
	default:
		$servers = [];
		foreach($customLibrary AS $library) {
			$servers[$library[0]] = $library[2];
		}
		if(!file_exists('ids.json')) {
			$time = $db->prepare('SELECT reuploadTime FROM songs WHERE reuploadTime > 0 ORDER BY reuploadTime DESC LIMIT 1');
			$time->execute();
			$time = $time->fetchColumn();
			$gs->updateLibraries($_GET['token'], $_GET['expires'], $time, 1);
		}
		$musicID = explode('.', $file)[0];
		$song = $gs->getLibrarySongInfo($musicID, true);
		if($song) $url = $song['download'];
		else $url = $gs->getSongInfo($musicID, 'download');
		$curl = curl_init($url);
		if($proxytype == 1) curl_setopt($ch, CURLOPT_PROXY, $host);
		elseif($proxytype == 2) {
			curl_setopt($ch, CURLOPT_PROXY, $host);
			curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
		}
		if(!empty($auth)) curl_setopt($ch, CURLOPT_PROXYUSERPWD, $auth); 
		curl_setopt_array($curl, [
			CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
			CURLOPT_RETURNTRANSFER => 1
		]);
		echo curl_exec($curl);
		curl_close($curl);
		break;
}
?>