<?php

// show errors at least initially
@ini_set('display_errors','1'); @error_reporting(E_ALL ^ E_NOTICE);

// set a default timezone to avoid PHP5 warnings
$dtmz = date_default_timezone_get();
date_default_timezone_set( !empty($dtmz) ? $dtmz:'Europe/Paris' );

// arguments settings
$doc = isset($_GET['doc']) ? $_GET['doc'] : null;
$md = isset($_GET['md']) ? $_GET['md'] : 'none';
$arg_ln = isset($_GET['ln']) ? $_GET['ln'] : 'en';

// contents settings
$js_code = false;
$parse_options = array();
$templater_options = array();

// process
if (!is_null($doc)) {
    $info = $error = $content = '';
    // get the Composer autoloader
    if (file_exists($b = __DIR__.'/../vendor/autoload.php')) {
        require_once $b;
        $class_info = \MarkdownExtended\Helper::info(true);
        $info = <<<EOT
    <p><a id="classinfo_handler" class="handler" title="Infos from the MarkdownExtended class">Current class infos</a></p>
    <div id="classinfo"><p>$class_info</p></div>
EOT;
        $options = array();
        if (file_exists($doc)) {
            $info .= <<<EOT
    <p><a id="plaintext_handler" class="handler" title="See plain text link">Original <em>$doc</em> document</a></p>
    <div id="plaintext"><ul>
        <li><a href="$doc" title="See original standalone parsed version of the file">See the standalone version of <em>$doc</em></a></li>
        <li><a href="$doc?plain" title="See plain text version of the file">See the original content of <em>$doc</em></a></li>
    </ul></div>
EOT;
            switch ($md) {
                case 'none'; default:
                    $content = '<pre>'.file_get_contents($doc).'</pre>'; 
                    break;
                case 'process';
/*
                    $content = \MarkdownExtended\MarkdownExtended::getInstance()
                        ->get('\MarkdownExtended\Parser', $options)
                        ->transform($source_content);
*/
//                    $source_content = file_get_contents($doc);
                    $mde_content = \MarkdownExtended\MarkdownExtended::create()
                        ->transformSource($doc)
//                        ->transformString($source_content)
//                        ->get('Parser', $parse_options)
//                        ->getContent()
//                        ->getContent()->getBody()
//                        ->getTemplater($templater_options)
//                        ->parse()
//                        ->getContent()
                        ;


/*

                    $tpl = \MarkdownExtended\MarkdownExtended::getInstance()
                        ->getTemplater($templater_options);
//var_export($tpl);
echo $tpl;
exit();

echo "<html><head>"
    .$mde_content->getMetadataHtml() // the content metadata
    ."</head><body>"
    .$mde_content->getBody() // the content HTML body
    ."<hr />"
    .$mde_content->getNotesHtml() // the content footnotes
    ."</body></html>";
exit();



echo '<pre>';
var_export($mde_content);
echo '</pre>';
exit('yo');
*/
                    break;
            }
        } else {
            $error = 'Markdown document source "'.$doc.'" not found!';
        }
    } else {
        $error = 'You need to run Composer on your project to use this interface!';
    }
} else {
    $content = file_get_contents('usage.html');
    $js_code = true;
}

?><!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<?php $title_done = false; ?>
<?php if (!empty($mde_content) && $mde_content->getMetadata()) : ?>
    <?php foreach ($mde_content->getMetadata() as $meta_name=>$meta_content) : ?>
		<?php if ($meta_name=='title'): ?>
    <title><?php echo $meta_content; ?></title>
            <?php $title_done = true; ?>
		<?php else: ?>
    <meta name="<?php echo $meta_name; ?>" content="<?php echo $meta_content; ?>" />
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
<?php if (!$title_done) : ?>
    <title>Test & documentation of PHP "MarkdownExtended" package</title>
<?php endif; ?>
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
            <h2 class="slogan">A complete PHP 5.3 package of Markdown syntax parser (extended version).</h2>
        </hgroup>
        <div class="hat">
            <p>These pages show and demonstrate the use and functionality of the <a href="https://github.com/atelierspierrot/markdown-extended">atelierspierrot/markdown-extended</a> PHP package you just downloaded.</p>
        </div>
    </header>

	<nav>
		<h2>Map of the package</h2>
        <ul id="navigation_menu" class="menu" role="navigation">
            <li><a href="index.php">Usage</a></li>
            <li><a href="index.php?doc=MD_syntax.md">MD_syntax.md</a><ul>
                <li><a href="index.php?doc=MD_syntax.md">plain text version</a></li>
                <li><a href="index.php?doc=MD_syntax.md&md=process">markdown parsed version</a></li>
            </ul></li>
            <li><a href="index.php?doc=../README.md">Package README.md</a><ul>
                <li><a href="index.php?doc=../README.md">plain text version</a></li>
                <li><a href="index.php?doc=../README.md&md=process">markdown parsed version</a></li>
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
<?php
if (!is_null($doc)) {
    $info = $error = $content = '';
    // get the Composer autoloader
    if (file_exists($b = __DIR__.'/../vendor/autoload.php')) {
        require_once $b;
        $info = '<p><strong>Current class infos:</strong></p><p>'.\MarkdownExtended\MarkdownExtended::info(true).'</p>';
        $options = array();
        if (file_exists($doc)) {
            $source_content = file_get_contents($doc);
            switch ($md) {
                case 'none'; default:
                    $content = '<pre>'.$source_content.'</pre>'; 
                    break;
                case 'process';
                    $content = \MarkdownExtended\MarkdownExtended::getInstance()
                        ->get('\MarkdownExtended\Parser', $options)
                        ->transform($source_content);
                    break;
            }
        } else {
            $error = 'Markdown document source "'.$doc.'" not found!';
        }
    } else {
        $error = 'You need to run Composer on your project to use this interface!';
    }
} else {
    $content = file_get_contents('usage.html');
}
?>

<?php if (!empty($error)) : ?>
    <div class="message error">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<?php if (!empty($info)) : ?>
    <div class="info">
        <?php echo $info; ?>
    </div>
<?php endif; ?>

<?php if (!empty($mde_content)) : ?>
    <article>

<?php
$menu = $mde_content->getMenu();
$depth = 0;
$current_level = null;
?>
<?php if (!empty($menu)) : ?>
<aside id="page_menu">
    <h2>Table of contents</h2>
    <ul class="menu">
    <?php foreach ($menu as $item_id=>$menu_item) : ?>
        <?php $diff = $menu_item['level']-(is_null($current_level) ? $menu_item['level'] : $current_level); ?>
        <?php if ($diff > 0) : ?>
            <?php for ($i=0; $i<$diff; $i++) : ?>
            <ul>
                <li>
            <?php endfor; ?>
        <?php elseif ($diff < 0) : ?>
            <?php for ($i=0; $i>$diff; $i--) : ?>
                </li>
            </ul></li>
            <li>
            <?php endfor; ?>
        <?php else : ?>
            <?php if (!is_null($current_level)) : ?>
            </li>
            <?php endif; ?>
            <li>
        <?php endif; ?>
                <a href="#<?php echo $item_id; ?>"><?php echo $menu_item['text']; ?></a>
        <?php $current_level = $menu_item['level']; ?>
    <?php endforeach; ?>
    </ul>
</aside>  
<?php endif; ?>

        <?php echo $mde_content->getBody(); ?>
        <?php if ($mde_content->getNotes()) : ?>
		<div class="footnotes">
			<ol>
            <?php foreach ($mde_content->getNotes() as $id=>$note_content) : ?>
			    <li id="<?php echo $note_content['note-id']; ?>"><?php echo $note_content['text']; ?></li>
            <?php endforeach; ?>
            </ol>
        </div>
        <?php endif; ?>
    </article>
<?php elseif (!empty($content)) : ?>
    <article>
        <?php echo $content; ?>
    </article>
<?php endif; ?>

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
    $('pre').each(function(i,o) {
        var dl = $(this).attr('data-language');
        if (dl) {
            $(this).addClass('code')
                .highlight({indent:'tabs', code_lang: 'data-language'});
        }
    });
    initHandler('classinfo', true);
    initHandler('plaintext', true);
});
</script>
</body>
</html>
