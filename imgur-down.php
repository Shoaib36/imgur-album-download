<?php
if($argc < 2) {
	print "Need one argument : album to download\n";
	print "Example : Veelu\n";
	exit 1;
}
$albumName = $argv[1];

$contents = file_get_contents("http://api.imgur.com/2/album/$album.json");
// $contents = file_get_contents("$albumName.json");
$resp = json_decode($contents);
if(isset($resp->error) {
	$errorMsg = $resp->error->message;
	print "The following error occurred : ".$errorMsg."\n";
	exit 2;
}

$album = $resp->album;
$title = $album->title;
$description = $album->description;
$images = $album->images;
$total = count($images);
echo "Title : $title\nDesc : $description\nCount : ".$total."\n";

$clean = $albumName;
if($title != '') {
	$clean = preg_replace("/[^a-z0-9\-.]/i", '', $title);
}
mkdir($clean);
foreach($images as $img) {
	$original = $img->links->original;
	$filePath = basename($original);
	echo "fetching ($cnt/$total) : $original\n";
	getURL($original, $clean."/".$filePath);
}

function getURL($url, $filePath)
{
	$ch = curl_init(); 
	$fh = fopen($filePath, 'w'); 
	curl_setopt($ch, CURLOPT_FILE, $fh); 
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_exec($ch); 
	fflush($fh); 
	fclose($fh);
	curl_close($ch);
}
