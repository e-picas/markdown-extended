<?php
/**
 * Show errors at least initially
 *
 * `E_ALL` => for hard dev
 * `E_ALL & ~E_STRICT` => for hard dev in PHP5.4 avoiding strict warnings
 * `E_ALL & ~E_NOTICE & ~E_STRICT` => classic setting
 */
//@ini_set('display_errors','1'); @error_reporting(E_ALL);
//@ini_set('display_errors','1'); @error_reporting(E_ALL & ~E_STRICT);
@ini_set('display_errors','1'); @error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

/**
 * Set a default timezone to avoid PHP5 warnings
 */
$dtmz = @date_default_timezone_get();
date_default_timezone_set($dtmz?:'Europe/Paris');

// arguments settings
$doc = isset($_GET['doc']) ? $_GET['doc'] : null;
$md = isset($_GET['md']) ? $_GET['md'] : 'none';
$arg_ln = isset($_GET['ln']) ? $_GET['ln'] : 'en';
$page = isset($_GET['page']) ? $_GET['page'] : null;
if (!empty($page)) {
    if (file_exists($page.'.php')) $page = $page . '.php';
    elseif (file_exists($page.'.html')) $page = $page . '.html';
    else unset($page);
}

// contents settings
$js_code = false;
$parse_options = array();
$templater_options = array();
$info = $error = $content = '';

// -----------------------------------
// NAMESPACE
// -----------------------------------

// get the Composer autoloader
if (file_exists($a = __DIR__.'/../../../autoload.php')) {
    require_once $a;
} elseif (file_exists($b = __DIR__.'/../vendor/autoload.php')) {
    require_once $b;

// else try to register `MarkdownExtended` namespace
} elseif (file_exists($c = __DIR__.'/../src/SplClassLoader.php')) {
    require_once $c;
    $classLoader = new SplClassLoader('MarkdownExtended', __DIR__.'/../src');
    $classLoader->register();

// else error, classes can't be found
} else {
    $error = 'You need to run Composer on your project to use this interface!';
}

// Custom classes
if (file_exists($d = __DIR__.'/../src/SplClassLoader.php')) {
    require_once $d;
    $classLoader = new SplClassLoader('MDE_Overrides', __DIR__.'/user');
    $classLoader->register();
}

// -----------------------------------
// Page Content
// -----------------------------------

// process
if (!is_null($doc)) {
    if (empty($error)) {
        $class_info = \MarkdownExtended\Helper::info(true);
        $info = <<<EOT
    <p><a id="classinfo_handler" class="handler" title="Infos from the MarkdownExtended class">Current class infos</a></p>
    <div id="classinfo"><p>$class_info</p></div>
EOT;
        $options = array();
//        $options['output_format'] = '\MDE_Overrides\MyHTMLOutput';
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
                        ->transformSource($doc, $options)
//                        ->transformString($source_content, $options)
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
    }
} elseif (empty($page)) {
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

    <a id="top"></a>
    <header role="banner">
        <h1>The PHP "<em>MarkdownExtended</em>" package</h1>
        <h2 class="slogan">A complete PHP 5.3 package of Markdown syntax parser (extended version).</h2>
        <div class="hat">
            <p>These pages show and demonstrate the use and functionality of the <a href="http://github.com/atelierspierrot/markdown-extended">atelierspierrot/markdown-extended</a> PHP package you just downloaded.</p>
        </div>
    </header>

    <nav>
        <h2>Map of the package</h2>
        <ul id="navigation_menu" class="menu" role="navigation">
            <li><a href="index.php">Usage</a></li>
            <li><a href="../markdown_reminders.html" onclick="return emdreminders_popup('../markdown_reminders.html');" title="Markdown syntax reminders (new floated window)" target="_blank">Markdown Reminders</a></li>
            <li><a href="index.php?page=form">Test of a form field</a></li>
            <li><a href="index.php?doc=Apache-Handler-HOWTO.md&amp;md=process">Apache Handler HOWTO</a></li>
            <li><a href="index.php?doc=MD_syntax.md">MD_syntax.md</a><ul>
                <li><a href="index.php?doc=MD_syntax.md">plain text version</a></li>
                <li><a href="index.php?doc=MD_syntax.md&amp;md=process">markdown parsed version</a></li>
            </ul></li>
            <li><a href="index.php?doc=../README.md">Package README.md</a><ul>
                <li><a href="index.php?doc=../README.md">plain text version</a></li>
                <li><a href="index.php?doc=../README.md&amp;md=process">markdown parsed version</a></li>
            </ul></li>
        </ul>

        <div class="info">
            <p><a href="http://github.com/atelierspierrot/markdown-extended">See online on GitHub</a></p>
            <p class="comment">The sources of this plugin are hosted on <a href="http://github.com">GitHub</a>. To follow sources updates, report a bug or read opened bug tickets and any other information, please see the GitHub website above.</p>
        </div>

        <div class="info" id="menu_socials">
            <!-- AddThis Button BEGIN -->
            <div class="addthis_toolbox addthis_default_style addthis_16x16_style">
            <a href="http://github.com/atelierspierrot/markdown-extended" target="_blank" title="GitHub">
                <span class="at16nc at300bs at15nc atGitHub"></span>
            </a>
            <a class="addthis_button_email"></a>
            <a class="addthis_button_print"></a>
            <a class="addthis_button_compact"></a><a class="addthis_counter addthis_bubble_style"></a>
            </div>
            <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=undefined"></script>
            <!-- AddThis Button END -->
        </div>

        <p class="credits" id="user_agent"></p>
    </nav>

    <div id="content" role="main">

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
$output_bag = \MarkdownExtended\MarkdownExtended::get('OutputFormatBag');
$menu = $output_bag->getHelper()
    ->getToc($mde_content, $output_bag->getFormater(), array(
        'title_level'=>'2', 'class'=>'menu'
    ));
?>
<?php if (!empty($menu)) : ?>
<aside id="page_menu">
    <?php echo $menu; ?>
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
        <?php if ($mde_content->getLastUpdate()) : ?>
            <p class="credits">Last update of this page <time datetime="<?php
                echo $mde_content->getLastUpdate()->format('c')
            ?>"><?php echo $mde_content->getLastUpdate()->format('F j, Y, g:i a'); ?></time>.</p>
        <?php endif; ?>
    </article>
<?php elseif (!empty($page) && file_exists($page)) : ?>
    <article>
        <?php include $page; ?>
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
            <a href="http://github.com/atelierspierrot/markdown-extended">atelierspierrot/markdown-extended</a> package by <a href="https://github.com/PieroWbmstr">Piero Wbmstr</a> under <a href="http://opensource.org/licenses/GPL-3.0">GNU GPL v.3</a> license.
        </div>
    </footer>

    <div class="back_menu" id="short_navigation">
        <a href="#" title="See table of contents" id="short_tableofcontents_handler"><span class="text">Table of contents</span></a>
        &nbsp;|&nbsp;
        <a href="#" title="See navigation menu" id="short_menu_handler"><span class="text">Navigation Menu</span></a>
        &nbsp;|&nbsp;
        <a href="#bottom" title="Go to the bottom of the page"><span class="text">Go to bottom&nbsp;</span>&darr;</a>
        &nbsp;|&nbsp;
        <a href="#top" title="Back to the top of the page"><span class="text">Back to top&nbsp;</span>&uarr;</a>
        <ul id="short_menu" class="menu" role="navigation"></ul>
        <ul id="short_tableofcontents" class="menu" role="navigation"></ul>
    </div>

    <div id="message_box" class="msg_box"></div>
    <a id="bottom"></a>

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
    activateNavigationMenu();
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
<?php if ($js_code) : ?>
<script id="js_code">
$(function() {

// list manifest content
    initHandler( 'manifest' );
    var manifest_url = '../composer.json';
    var manifest_ul = $('#manifest').find('ul');
    getPluginManifest(manifest_url, function(data){
        manifest_ul.append( getNewInfoItem( data.title, 'title' ) );
        manifest_ul.append( getNewInfoItem( data.version, 'version' ) );
        manifest_ul.append( getNewInfoItem( data.description, 'description' ) );
        manifest_ul.append( getNewInfoItem( data.license, 'license' ) );
        manifest_ul.append( getNewInfoItem( data.homepage, 'homepage', data.homepage ) );
    });

// list GitHub infos
    initHandler( 'github' );
    var github = 'https://api.github.com/repos/atelierspierrot/markdown-extended/';
    // commits list
    var github_commits = $('#github').find('#commits_list');
    getGitHubCommits(github, function(data){
        if (data!==undefined && data!==null) {
            $.each(data, function(i,o) {
                if (o!==null && typeof o==='object' && o.commit.message!==undefined && o.commit.message.length)
                    github_commits.append( getNewInfoItem( o.commit.message, (o.commit.committer.date || ''), (o.commit.url || '') ) );
            });
        } else {
            github_commits.append( getNewInfoItem( 'No commit for now.', '' ) );
        }
    });
    // bugs list
    var github_bugs = $('#github').find('#bugs_list');
    getGitHubBugs(github, function(data){
        if (data!==undefined && data!==null) {
            $.each(data, function(i,o) {
                if (o!==null && typeof o==='object' && o.title!==undefined && o.title.length)
                    github_bugs.append( getNewInfoItem( o.title, (o.created_at || ''), (o.html_url || '') ) );
            });
        } else {
            github_bugs.append( getNewInfoItem( 'No opened bug for now.', '' ) );
        }
    });

});
</script>
<?php endif; ?>
</body>
</html>
