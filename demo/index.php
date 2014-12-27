<?php
/**
 * Show errors at least initially
 *
 * `E_ALL` => for hard dev
 * `E_ALL & ~E_STRICT` => for hard dev in PHP5.4 avoiding strict warnings
 * `E_ALL & ~E_NOTICE & ~E_STRICT` => classic setting
 */
@ini_set('display_errors','1'); @error_reporting(E_ALL);
//@ini_set('display_errors','1'); @error_reporting(E_ALL & ~E_STRICT);
//@ini_set('display_errors','1'); @error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

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
    $classLoader = new SplClassLoader('MDE_Overrides', __DIR__.'/src');
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
    <p><a id="classinfo_handler" class="handler" title="Infos from the MarkdownExtended class"><span class="fa fa-caret-right"></span>&nbsp;Current class infos</a></p>
    <div id="classinfo"><p>$class_info</p></div>
EOT;
        $options = array();
//        $options['output_format'] = '\MDE_Overrides\MyHTMLOutput';
        if (file_exists($doc)) {
            $info .= <<<EOT
    <p><a id="plaintext_handler" class="handler" title="See plain text link"><span class="fa fa-caret-right"></span>&nbsp;Original <em>$doc</em> document</a></p>
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

$output_bag = !empty($mde_content) ? \MarkdownExtended\MarkdownExtended::get('OutputFormatBag') : null;
$menu = !empty($mde_content) ? $output_bag->getHelper()
    ->getToc($mde_content, $output_bag->getFormatter(), array(
        'title_level'=>'2', 'class'=>'menu'
    )) : null;

?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
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
<!-- Bootstrap -->
<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
    <link rel="stylesheet" href="assets/styles.css" />
    <script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML"></script>
<script type="text/javascript">
var emdreminders_window; // use this variable to interact with the cheat sheet window
function emdreminders_popup(url){
    if (!url) url='markdown_reminders.html?popup';
    if (url.lastIndexOf("popup")==-1) url += (url.lastIndexOf("?")!=-1) ? '&popup' : '?popup';
    emdreminders_window = window.open(url, 'markdown_reminders', 
       'directories=0,menubar=0,status=0,location=0,scrollbars=1,resizable=1,fullscreen=0,width=840,height=380,left=120,top=120');
    emdreminders_window.focus();
    return false; 
}
</script>
</head>
<body>
    <!--[if lt IE 7]>
        <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
    <![endif]-->

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">MarkdownExtended</a>
            </div>
            <div class="collapse navbar-collapse">
                <ul id="navigation_menu" class="nav navbar-nav" role="navigation">
                    <li><a href="index.php">Usage</a></li>
                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Documentation <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <?php foreach (scandir(__DIR__.'/../docs') as $f) :
                                if (!in_array($f, array('.','..')) && !is_dir($f)) : ?>
                                    <li><a href="index.php?doc=../docs/<?php echo basename($f); ?>&amp;md=process">
                                            <?php echo str_replace(array('_','-'), ' ', str_replace('.md', '', basename($f))); ?>
                                        </a></li>
                                <?php endif; endforeach; ?>
                        </ul>
                    </li>
                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Tests <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="index.php?page=form">Test of a form field</a></li>
                            <li><a href="index.php?doc=MD_syntax.md">MD_syntax.md</a><ul>
                                    <li><a href="index.php?doc=MD_syntax.md">plain text version</a></li>
                                    <li><a href="index.php?doc=MD_syntax.md&amp;md=process">markdown parsed version</a></li>
                                </ul></li>
                            <li><a href="index.php?doc=../README.md">Package README.md</a><ul>
                                    <li><a href="index.php?doc=../README.md">plain text version</a></li>
                                    <li><a href="index.php?doc=../README.md&amp;md=process">markdown parsed version</a></li>
                                </ul></li>
                        </ul>
                    </li>
                    <li><a href="../markdown_reminders.html" onclick="return emdreminders_popup('../markdown_reminders.html');" title="Markdown syntax reminders (new floated window)" target="_blank">Markdown Reminders</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right" role="navigation">
<?php if (!empty($menu)) : ?>
                    <li><a href="#" title="See table of contents" id="short_tableofcontents_handler"><span class="text">TOC</span></a></li>
<?php endif; ?>
                    <li><a href="#bottom" title="Go to the bottom of the page">&darr;</a></li>
                    <li><a href="#top" title="Back to the top of the page">&uarr;</a></li>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </div>

    <div class="container">

        <a id="top"></a>

        <header role="banner">
            <h1>The PHP "<em>MarkdownExtended</em>" package <br><small>A complete PHP 5.3 package for Markdown Extended syntax parsing</small></h1>
            <div class="hat">
                <p>These pages show and demonstrate the use and functionality of the <a href="http://github.com/piwi/markdown-extended">piwi/markdown-extended</a> PHP package you just downloaded.</p>
            </div>
        </header>

        <div id="content" role="main">

<?php if (!empty($error)) : ?>
            <div class="message error">
                <?php echo $error; ?>
            </div>
<?php endif; ?>

<?php if (!empty($info)) : ?>
            <nav class="main-nav jumbotron">
                <?php echo $info; ?>
            </nav>
<?php endif; ?>

<?php if (!empty($mde_content)) : ?>
            <article>

    <?php if (!empty($menu)) : ?>
                <aside id="page_menu" class="pull-right">
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
                <p class="credits small text-right">Last update of this page <time datetime="<?php
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
    </div>

    <footer id="footer">
        <div class="container">
            <div class="text-muted pull-left">
                This page is <a href="" title="Check now online" id="html_validation">HTML5</a> & <a href="" title="Check now online" id="css_validation">CSS3</a> valid.
            </div>
            <div class="text-muted pull-right">
                <a href="http://github.com/piwi/markdown-extended">piwi/markdown-extended</a> package by <a href="https://github.com/piwi">@piwi</a> under <a href="http://spdx.org/licenses/BSD-3-Clause">BSD 3 Clause</a> license.
                <p class="text-muted small" id="user_agent"></p>
            </div>
        </div>
    </footer>

    <div id="message_box" class="msg_box"></div>
    <a id="bottom"></a>

<!-- jQuery lib -->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

<!-- Bootstrap -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

<!-- jQuery.tablesorter plugin
<script src="assets/js/jquery.tablesorter.min.js"></script>
-->

<!-- jQuery.highlight plugin -->
<script src="assets/js/highlight.js"></script>

<!-- scripts for demo -->
<script src="assets/scripts.js"></script>

<script>
$(function() {
    getToHash();
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
        manifest_ul.append( getNewInfoItem( data.name, 'title' ) );
        if (data.version) {
            manifest_ul.append( getNewInfoItem( data.version, 'version' ) );
        } else if (data.extra["branch-alias"] && data.extra["branch-alias"]["dev-master"]) {
            manifest_ul.append( getNewInfoItem( data.extra["branch-alias"]["dev-master"], 'version' ) );
        }
        manifest_ul.append( getNewInfoItem( data.description, 'description' ) );
        manifest_ul.append( getNewInfoItem( data.license, 'license' ) );
        manifest_ul.append( getNewInfoItem( data.homepage, 'homepage', data.homepage ) );
    });

// list GitHub infos
    initHandler( 'github' );
    var github = 'https://api.github.com/repos/piwi/markdown-extended/';
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
