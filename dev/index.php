<?php
// show errors at least initially
@ini_set('display_errors','1'); @error_reporting(E_ALL ^ E_NOTICE);

// set a default timezone to avoid PHP5 warnings
$dtmz = date_default_timezone_get();
date_default_timezone_set( !empty($dtmz) ? $dtmz:'Europe/Paris' );

if (file_exists($a = __DIR__.'/../src/markdown.php')) {
    require_once $a;
} else {
    die('Markdown aliases file not found !');
}

$test_file = __DIR__.'/../demo/MD_syntax.md';
echo '<h3>Test working file "MD_syntax.md"</h3>';

if (file_exists($test_file)) {
    echo '<p>OK - File exists</p>';
} else {
    echo '<p>ERROR - File not found!</p>';
}

echo '<h3>Test of "MarkdownFromSource"</h3>';
echo '<pre>';
echo MarkdownFromSource($test_file);
echo '</pre>';

exit('yo');

echo '<h3>Test of "Markdown"</h3>';
echo '<pre>';
echo Markdown(file_get_contents($test_file));
echo '</pre>';


exit('yo');
