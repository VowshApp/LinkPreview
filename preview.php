<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Request-Methods: GET');

$link = base64_decode($_GET['link']);
$parts = parse_url($link);
$extension = pathinfo($parts['path'], PATHINFO_EXTENSION);

// Fix images that aren't a direct link
if(strpos($link, "twimg.com") > -1) {
    parse_str($parts['query'], $query);
    $extension = $query['format'];
}

// Image preview
if(in_array($extension, ['png', 'jpeg', 'jpg', 'gif', 'bmp', 'svg'])) {
    $image = getimagesize($link);
    $data = 'data:'.$image['mime'].';base64,'.base64_encode(file_get_contents($link));
    header('Content-Type: text/html; charset=utf-8');
    echo('<img src="'.$data.'" alt="Link preview" class="img-thumbnail">');
}
else {
    $tags = get_meta_tags($link);
    if(!isset($tags['title'])) $tags['title'] = '';
    if(!isset($tags['description'])) $tags['description'] = 'No description is available.';
    header('Content-Type: application/json; charset=utf-8');
    echo(json_encode(["title" => $tags['title'], "content" => $tags['description']], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}