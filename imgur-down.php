<?php
if($argc < 2) {
    print "Need one argument : album to download\n";
    print "Example : Veelu\n";
    exit (1);
}
$albumName = $argv[1];

$contents = file_get_contents("http://api.imgur.com/2/album/$albumName.json");
// $contents = file_get_contents("$albumName.json");
$resp = json_decode($contents);
if(isset($resp->error)) {
    $errorMsg = $resp->error->message;
    print "The following error occurred : ".$errorMsg."\n";
    exit (2);
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
$clean = "album/".$clean;
if(!is_dir($clean)) {
    mkdir($clean);
}

$lastGood = '';
$cnt = 0;
foreach($images as $img) {
    $cnt++;
    $original = $img->links->original;
    $filePath = $clean.'/'.$cnt.".".basename($original);
    echo "($cnt/$total) : $original : ";
    if(file_exists($filePath)) {
	$file_size = filesize($filePath);
	$img_size = $img->image->size;
        if($file_size == $img_size) {
            echo "skipping\n";
            continue;
        }
    }
    echo "fetching\n";
    getURL($original, $filePath);
}
echo "Finished downloading album to : $clean\n";
echo "All Done\n";


function getURL($url, $filePath)
{
    $ch = curl_init(); 
    $fh = fopen($filePath, 'w'); 
    curl_setopt($ch, CURLOPT_FILE, $fh); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_exec($ch); 
    if(curl_error($ch)) {
        print_r(curl_error($ch));
        exit(3);
    }
    fflush($fh); 
    fclose($fh);
    curl_close($ch);
}
