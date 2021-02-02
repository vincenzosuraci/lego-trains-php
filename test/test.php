<?php

$haystack = file_get_contents('suoni.html');
$offset = 0;
$needle = '.mp3';
while ( false !== ( $pos = strpos($haystack, $needle, $offset) ) ) {
    $first_double_quote_pos = strrpos(substr($haystack,0,$pos), '"');
    $url = substr($haystack, $first_double_quote_pos+1, $pos - $first_double_quote_pos - 1) . $needle;
    echo '<a href="' . $url . '" target="_blank">' . $url . '</a><br/>';
    $filename = basename($url);
    file_put_contents(
        $filename,
        file_get_contents($url)
    );
    $offset = $pos + strlen($needle);
}