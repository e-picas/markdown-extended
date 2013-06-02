<?php

// show errors at least initially
@ini_set('display_errors','1'); @error_reporting(E_ALL ^ E_NOTICE);

// set a default timezone to avoid PHP5 warnings
$dtmz = date_default_timezone_get();
date_default_timezone_set( !empty($dtmz) ? $dtmz:'Europe/Paris' );

// arguments settings
$arg_dir = isset($_GET['dir']) ? rtrim($_GET['dir'], '/').'/' : 'parts/';
$img_dir = isset($_GET['img_dir']) ? rtrim($_GET['img_dir'], '/').'/' : 'photos/';
$vidz_dir = isset($_GET['videos_dir']) ? rtrim($_GET['videos_dir'], '/').'/' : 'videos/';
$arg_root = isset($_GET['root']) ? $_GET['root'] : __DIR__;
$arg_i = isset($_GET['i']) ? $_GET['i'] : 0;
$arg_ln = isset($_GET['ln']) ? $_GET['ln'] : 'en';

require_once __DIR__.'/../src/SplClassLoader.php';
$classLoader = new SplClassLoader('MarkdownExtended', __DIR__.'/../src');
$classLoader->register();

$page = 'MD_syntax.md';
$content = file_get_contents($page);
$markdown = \MarkdownExtended\MarkdownExtended::getInstance();
if (isset($_GET['page'])) {
    $page = $_GET['page'];
    $content = file_get_contents($page);
	$parser = $markdown::get('\MarkdownExtended\Parser', $options);
    $md_content = $parser->transform($content);
}

?><!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Test & documentation of PHP "MarkdownExtended" package</title>
    <meta name="description" content="A complete PHP 5.3 version of the Markdown syntax parser" />
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="assets/html5boilerplate/css/normalize.css" />
    <link rel="stylesheet" href="assets/html5boilerplate/css/main.css" />
    <script src="assets/html5boilerplate/js/vendor/modernizr-2.6.2.min.js"></script>
	<link rel="stylesheet" href="assets/styles.css" />
<script type="text/javascript">
var emdreminders_window; // use this variable to interact with the cheat sheet window
function emdreminders_popup(url){
    if (!url) url='markdown_reminders.html?popup';
    if (url.lastIndexOf("popup")==-1) url += (url.lastIndexOf("?")!=-1) ? '&popup' : '?popup';
    emdreminders_window = window.open(url, 'markdown_reminders', 
       'directories=0,menubar=0,status=0,location=1,scrollbars=1,resizable=1,fullscreen=0,width=840,height=380,left=120,top=120');
    emdreminders_window.focus();
    return false; 
}
</script>
</head>
<body>
    <!--[if lt IE 7]>
        <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
    <![endif]-->

    <header id="top" role="banner">
        <hgroup>
            <h1>The PHP "<em>MarkdownExtended</em>" package</h1>
            <h2 class="slogan">A complete PHP 5.3 version of the Markdown syntax parser.</h2>
        </hgroup>
        <div class="hat">
            <p>These pages show and demonstrate the use and functionality of the <a href="https://github.com/atelierspierrot/markdown-extended">atelierspierrot/markdown-extended</a> PHP package you just downloaded.</p>
        </div>
    </header>

	<nav>
		<h2>Map of the package</h2>
        <ul id="navigation_menu" class="menu" role="navigation">
            <li><a href="index.html">Homepage</a><ul>
                <li><a href="index.php?page=MD_syntax.md">MD_syntax.md</a></li>
                <li><a href="index.php?page=testMD_syntax_deux.md">testMD_syntax_deux.md</a></li>
                <li><a href="index.php?page=testMD_syntax_trois.md">testMD_syntax_trois.md</a></li>
                <li><a href="index.php?page=testMD_syntax_un.md">testMD_syntax_un.md</a></li>
                <li><a href="index.php?page=test.rtf">test.rtf</a></li>
            </ul></li>
            <li><a href="../src/markdown_reminders.html" onclick="return emdreminders_popup('../src/markdown_reminders.html');" title="Markdown syntax reminders (new floated window)" target="_blank">Markdown Reminders</a></li>
        </ul>

        <div class="info">
            <p><a href="https://github.com/atelierspierrot/markdown-extended">See online on GitHub</a></p>
            <p class="comment">The sources of this plugin are hosted on <a href="http://github.com">GitHub</a>. To follow sources updates, report a bug or read opened bug tickets and any other information, please see the GitHub website above.</p>
        </div>

    	<p class="credits" id="user_agent"></p>
	</nav>

    <div id="content" role="main">

        <div class="info">
            <p><strong>Current class infos:</strong></p>
            <p><?php echo $markdown::info(true); ?></p>
        </div>

        <article>
<?php
if (!empty($md_content)) {
    echo $md_content;
} else {
    echo '<pre>'.$content.'</pre>'; 
}
?>
        </article>
    </div>

    <footer id="footer">
		<div class="credits float-left">
		    This page is <a href="" title="Check now online" id="html_validation">HTML5</a> & <a href="" title="Check now online" id="css_validation">CSS3</a> valid.
		</div>
		<div class="credits float-right">
		    <a href="https://github.com/atelierspierrot/markdown-extended">atelierspierrot/markdown-extended</a> package by <a href="https://github.com/PieroWbmstr">Piero Wbmstr</a> under <a href="http://opensource.org/licenses/GPL-3.0">GNU GPL v.3</a> license.
		</div>
    </footer>

    <div class="back_menu" id="short_navigation">
        <a href="#" title="See navigation menu" id="short_menu_handler"><span class="text">Navigation Menu</span></a>
        &nbsp;|&nbsp;
        <a href="#top" title="Back to the top of the page"><span class="text">Back to top&nbsp;</span>&uarr;</a>
        <ul id="short_menu" class="menu" role="navigation"></ul>
    </div>

    <div id="message_box" class="msg_box"></div>

<!-- jQuery lib -->
<script src="assets/js/jquery-1.9.1.min.js"></script>

<!-- HTML5 boilerplate -->
<script src="assets/html5boilerplate/js/plugins.js"></script>

<!-- jQuery.tablesorter plugin -->
<script src="assets/js/jquery.tablesorter.min.js"></script>

<!-- jQuery.highlight plugin -->
<script src="assets/js/highlight.js"></script>

<!-- scripts for demo -->
<script src="assets/scripts.js"></script>

<script>
$(function() {
    initBacklinks();
    activateMenuItem();
    getToHash();
    buildFootNotes();
    addCSSValidatorLink('assets/styles.css');
    addHTMLValidatorLink();
    $("#user_agent").html( navigator.userAgent );
    $('pre.code').highlight({source:0, indent:'tabs', code_lang: 'data-language'});
});
</script>
</body>
</html>
