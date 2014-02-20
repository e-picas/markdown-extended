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

// contents settings
$parser_options = array();
$templater_options = array();

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

// -----------------------------------
// Page Contents
// -----------------------------------

$mde_parser = \MarkdownExtended\MarkdownExtended::create();

$mde_contents_typo = array(
    'strong'=>array(
        '**hello**', '__hello__'
    ),
    'italic'=>array(
        '*hello*', '_hello_'
    ),
    'link'=>array(
        '<http://example.com/>',
        '<email@example.com>',
        '[an hypertext link](http://example.com/ "Optional link title")',
    ),
    'anchor'=>array(
        '## title with anchor {#anchor}

some text

a link to [declared anchor](#anchor)'
    ),
    'code'=>array(
        '`function()`'
    ),
    'image'=>array(
        '![Alt text](http://upload.wikimedia.org/wikipedia/commons/5/5a/Wikipedia-logo-v2-fr.png "Optional image title")'
    ),
    'abbreviation'=>array(
        'A paragraph with the word HTML.

*[HTML]: Hyper-Text Markup Language'
    ),
    'headers'=>array(
        'my title level 1
================

my title level 2
----------------', '# my title level 1
### my title level 3'
    ),
    'horizontal-rule'=>array(
        '----','****','____'
    ),
);

$mde_contents_bloc = array(
    'blockquote'=>array(
        '> This is my blockquote
> and a second line ...',
        '> (http://source.com) This is my blockquote
> and a second line ...'
    ),
    'pre-formated'=>array(
        '    // this is my "pre" block
    $var = val_fct();', '~~~~
My code here
~~~~', '~~~~html
My code here
~~~~'
    ),
    'definitions-list'=>array(
        'Word
:   Definition content (first one)
    with a two-lines text

:   Second definition for this term...'
    ),
    'unordered-list'=>array(
        '-   first item
-   second item','*   first item
*   second item','+   first item
+   second item'
    ),
    'ordered-list'=>array(
        '1.   first item
1.   second item'
    ),
    'paragraph'=>array(
        'This is my first paragraph.

And this is my second,
on two lines ...'
    ),
    'table'=>array(
        '| First Header  | Second Header | Third Header |
| ------------- | ------------: | :----------: |
| Content Cell  | Content right-aligned | Content center-aligned |
| Content Cell  | Content on two columns ||'
    ),
    'footnote'=>array(
        'A paragraph with a footnote[^footnote_one] note.

[^footnote_one]: Footnote content',
        'A paragraph with a referenced glossary term[^myterm] ...

[^myterm]: glossary: the term defined (an optional sort key)
The term definition ... which may be multi-line.',
        'This is a statement that should be attributed to its source [p. 23][#Doe:2006].

[#Doe:2006]: John Doe. *Some Big Fancy Book*.  Vanity Press, 2006.'
    ),
);

$mde_contents_misc = array(
    'references'=>array(
        'A paragraph with a referenced [hypertext link][myid] and some more text embedding an
image: ![image for the test][myimage].

[myid]: http://example.com/ "Optional link title"
[myimage]: http://example.com/test.com "Optional image title" width=40px height=40px'
    ),
    'escaping'=>array(
        '\\ backslash','\. dot','\! exclamation point','\# hash mark','\* asterisk','\+ plus sign',
        '\- hyphen','\_ underscore','\` backtick quote','\(\) parentheses',
        '\[\] brackets','\{\} curly brackets'
    ),
    'inclusion'=>array(
        '<!-- @inclusion-test.md@ -->',
        '<!-- @not-found.md@ -->'
    ),
);

$footnotes = array();
?><!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Markdown Extended syntax cheat sheet</title>
    <meta name="description" content="MarkdownExtended syntax cheat sheet - A complete PHP 5.3 version of the Markdown syntax parser" />
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="assets/html5boilerplate/css/normalize.css" />
    <link rel="stylesheet" href="assets/html5boilerplate/css/main.css" />
    <script src="assets/html5boilerplate/js/vendor/modernizr-2.6.2.min.js"></script>
    <link rel="stylesheet" href="assets/styles.css" />
<style>
table.mde-cheat-sheet {
    border-collapse: collapse;
    font-size: 10pt;
}
table.mde-cheat-sheet th {
    background: #eee;
    border: 1px solid #ccc;
}
table.mde-cheat-sheet tbody th {
    color: #7D7D7D;
    font-variant: italic;
}
table.mde-cheat-sheet td {
    padding: 2px 4px;
    border: 1px solid #ccc;
}
</style>
</head>
<body>
    <!--[if lt IE 7]>
        <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
    <![endif]-->

    <a id="top"></a>
    <header role="banner">
        <h1>The <em>Markdown Extended</em> syntax cheat sheet</h1>
        <h2 class="slogan">
            The sources of the "<em>Markdown Extended</em>" package are hosted on <a href="http://github.com">GitHub</a>. To follow sources updates, report a bug or read opened bug tickets and any other information, please see the GitHub website <a href="http://github.com/atelierspierrot/markdown-extended">atelierspierrot/markdown-extended</a>.
        </h2>
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
    </header>

    <nav>
        <ul id="navigation_menu" class="menu" role="navigation">
            <li><a href="#typographic">Typographic rules</a>
            <ul>
<?php foreach ($mde_contents_typo as $name=>$samples) : ?>
                <li><a href="#<?php echo $name; ?>"><?php echo ucfirst($name); ?></a></li>
<?php endforeach; ?>
            </ul>
            </li>
            <li><a href="#bloc">Bloc rules</a>
            <ul>
<?php foreach ($mde_contents_bloc as $name=>$samples) : ?>
                <li><a href="#<?php echo $name; ?>"><?php echo ucfirst($name); ?></a></li>
<?php endforeach; ?>
            </ul>
            </li>
            <li><a href="#miscellaneous">Miscellaneous</a>
            <ul>
<?php foreach ($mde_contents_misc as $name=>$samples) : ?>
                <li><a href="#<?php echo $name; ?>"><?php echo ucfirst($name); ?></a></li>
<?php endforeach; ?>
            </ul>
            </li>
        </ul>
    </nav>

    <div id="content" role="main">

    <article>

    <h3>Version & Manifest</h3>

    <div class="info">
        <p><a id="classinfo_handler" class="handler" title="Infos from the MarkdownExtended class">Current class infos</a></p>
        <div id="classinfo"><p><?php echo \MarkdownExtended\Helper::info(true); ?></p></div>
    </div>

    <div class="info">
        <p><a id="manifest_handler" class="handler" title="Infos extracted from your package version manifest">Full package manifest</a></p>
        <div id="manifest">
            <ul class="list_infos"></ul>
            <p class="credits">Infos extracted from your current package's "composer.json" manifest file.</p>
        </div>
    </div>

    <div class="info">
        <p><a id="github_handler" class="handler" title="Infos extracted from the repository on GitHub.com">Current repository infos</a></p>
        <div id="github">
            <strong>Last commits</strong>
            <ul id="commits_list"></ul>
            <strong>Last bugs</strong>
            <ul id="bugs_list"></ul>
            <p class="credits">Infos requested to the package sources repository on GitHub.com.</p>
        </div>
    </div>

    <h3>Syntax cheat sheet</h3>

    <table class="mde-cheat-sheet">
        <thead>
        <tr>
            <th>Name</th>
            <th>Sample</th>
            <th>Rendering</th>
        </tr>
        </thead>
        <tbody>

        <tr>
            <th colspan="3" id="typographic">Typographic rules</th>
        </tr>
<?php foreach ($mde_contents_typo as $name=>$samples) : 
    if (!is_array($samples)) $samples = array($samples);
?>
        <tr>
            <td id="<?php echo $name; ?>" rowspan="<?php echo count($samples)?>"><?php echo $name; ?></td>
    <?php foreach ($samples as $i=>$sample_item) : ?>
        <?php if ($i>1) : ?>
        <tr>
        <?php endif; ?>
            <td><pre><?php echo htmlentities($sample_item); ?></pre></td>
            <td><?php
                echo $mde_parser
                    ->transformString($sample_item, $parser_options)
                    ->getBody();
                $notes = $mde_parser->getContent()->getNotes();
                if (!empty($notes)) $footnotes = array_merge($footnotes, $notes);
            ?></td>
        </tr>
    <?php endforeach; ?>
<?php endforeach; ?>

        <tr>
            <th colspan="3" id="bloc">Bloc rules</th>
        </tr>
<?php foreach ($mde_contents_bloc as $name=>$samples) : 
    if (!is_array($samples)) $samples = array($samples);
?>
        <tr>
            <td id="<?php echo $name; ?>" rowspan="<?php echo count($samples)?>"><?php echo $name; ?></td>
    <?php foreach ($samples as $i=>$sample_item) : ?>
        <?php if ($i>1) : ?>
        <tr>
        <?php endif; ?>
            <td><pre><?php echo htmlentities($sample_item); ?></pre></td>
            <td><?php
                echo $mde_parser
                    ->transformString($sample_item, $parser_options)
                    ->getBody();
                $notes = $mde_parser->getContent()->getNotes();
                if (!empty($notes)) $footnotes = array_merge($footnotes, $notes);
            ?></td>
        </tr>
    <?php endforeach; ?>
<?php endforeach; ?>

        <tr>
            <th colspan="3" id="miscellaneous">Miscellaneous</th>
        </tr>
<?php foreach ($mde_contents_misc as $name=>$samples) : 
    if (!is_array($samples)) $samples = array($samples);
?>
        <tr>
            <td id="<?php echo $name; ?>" rowspan="<?php echo count($samples)?>"><?php echo $name; ?></td>
    <?php foreach ($samples as $i=>$sample_item) : ?>
        <?php if ($i>1) : ?>
        <tr>
        <?php endif; ?>
            <td><pre><?php echo htmlentities($sample_item); ?></pre></td>
            <td><?php
                echo $mde_parser
                    ->transformString($sample_item, $parser_options)
                    ->getBody();
                $notes = $mde_parser->getContent()->getNotes();
                if (!empty($notes)) $footnotes = array_merge($footnotes, $notes);
            ?></td>
        </tr>
    <?php endforeach; ?>
<?php endforeach; ?>

        </tbody>
    </table>
    <?php if (!empty($footnotes)) : ?>
    <div class="footnotes">
        <ol>
        <?php foreach ($footnotes as $id=>$note_content) : ?>
            <li id="<?php echo $note_content['note-id']; ?>"><?php echo $note_content['text']; ?></li>
        <?php endforeach; ?>
        </ol>
    </div>
    <?php endif; ?>
    </article>

    </div>

    <footer id="footer">
        <div class="credits float-left">
            This page is <a href="" title="Check now online" id="html_validation">HTML5</a> & <a href="" title="Check now online" id="css_validation">CSS3</a> valid.
        </div>
        <div class="credits float-right">
            <a href="http://github.com/atelierspierrot/markdown-extended">atelierspierrot/markdown-extended</a> package by <a href="https://github.com/pierowbmstr">Piero Wbmstr</a> under <a href="http://spdx.org/licenses/BSD-3-Clause">BSD 3 Clause</a> license.
        </div>
    </footer>

    <div class="back_menu" id="short_navigation">
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
    addCSSValidatorLink('assets/styles.css');
    addHTMLValidatorLink();

// class infos
    initHandler('classinfo');

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
</body>
</html>
