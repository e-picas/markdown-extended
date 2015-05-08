<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!isset($contents))
    $contents = array();
if (!isset($contents['meta_title']))
    $contents['meta_title'] = '';
if (!isset($contents['meta_description']))
    $contents['meta_description'] = \MarkdownExtended\MarkdownExtended::DESC;
if (!isset($contents['metadata']))
    $contents['metadata'] = '';
if (!isset($contents['menu']))
    $contents['menu'] = '';
if (!isset($contents['javascript']))
    $contents['javascript'] = '';

if (!isset($messages))
    $messages = array();
if (isset($contents['mde_info']) && $contents['mde_info']===true)
    $messages[] = 'Current class: <strong>'.\MarkdownExtended\MarkdownExtended::getAppInfo(true).'</strong>';
if (isset($contents['file_path']))
    $messages[] = 'Current document: <strong>'.$contents['file_path'].'</strong>';

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php
        echo !empty($contents['meta_title']) ? $contents['meta_title'] . ' &bull; ' : '';
        echo \MarkdownExtended\MarkdownExtended::NAME;
    ?></title>
    <meta name="description" content="<?php echo $contents['meta_description']; ?>" />
    <?php echo $contents['metadata']; ?>
    <!-- Bootstrap from CDN -->
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome from CDN -->
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.css" rel="stylesheet">
    <!-- MathJax from CDN -->
    <script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML"></script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="assets/styles.css" />
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
                <a class="navbar-brand" href="#"><?php echo \MarkdownExtended\MarkdownExtended::NAME; ?></a>
            </div>
            <div class="collapse navbar-collapse">
                <ul id="navigation_menu" class="nav navbar-nav" role="navigation">
                    <li><a href="index.php">Usage</a></li>
                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Documentation <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
<?php foreach ($documentations as $f) : ?>
                            <li><a href="index.php?doc=<?php echo $f; ?>&notab=1">
                                    <?php echo str_replace(array('_', '-'), ' ', str_replace('.md', '', basename($f))); ?>
                                </a></li>
<?php endforeach; ?>
                        </ul>
                    </li>
                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Tests <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="index.php?page=form">Test of a form field</a></li>
<?php foreach ($demonstrations as $f) : ?>
                            <li><a href="index.php?doc=<?php echo $f; ?>"><?php echo $f; ?></a><ul>
                                    <li><a href="index.php?doc=<?php echo $f; ?>#plain-text">plain text version</a></li>
                                    <li><a href="index.php?doc=<?php echo $f; ?>">markdown parsed version</a></li>
                                </ul></li>
<?php endforeach; ?>
                        </ul>
                    </li>
                    <li><a href="http://docs.ateliers-pierrot.fr/markdown-extended/">API</a></li>
                    <li><a href="http://github.com/piwi/markdown-extended">Sources</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right" role="navigation">
<?php if (!empty($contents['menu'])) : ?>
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

        <header role="banner" class="page-header">
            <h1>
                <em><?php echo \MarkdownExtended\MarkdownExtended::SHORTNAME; ?></em><br>
                <small><?php echo \MarkdownExtended\MarkdownExtended::DESC; ?></small>
            </h1>
            <div class="hat">
                <p>These pages show and demonstrate the use and functionality of the <a href="<?php echo \MarkdownExtended\MarkdownExtended::LINK; ?>"><?php echo $package['name']; ?></a> PHP package you just downloaded.</p>
            </div>
        </header>

        <div id="content" role="main">

<?php if (!empty($errors)) : ?>
            <div class="alert alert-warning">
                <?php echo implode($errors, '<br>'); ?>
            </div>
<?php endif; ?>

<?php if (!empty($messages)) : ?>
            <nav class="alert alert-success">
                <?php echo implode($messages, '<br>'); ?>
            </nav>
<?php endif; ?>

            <article>

<?php if (!empty($contents['menu'])) : ?>
                <aside id="page_menu" class="pull-right">
                    <?php echo $contents['menu']; ?>
                </aside>
<?php endif; ?>

<?php if (!empty($contents['content'])) : ?>
                <?php echo $contents['content']; ?>
<?php endif; ?>

<?php if (!empty($contents['notes'])) : ?>
                <div class="footnotes">
                    <ol>
    <?php foreach ($contents['notes'] as $id=>$note_content) : ?>
                        <li id="<?php echo $note_content['note-id']; ?>"><?php echo $note_content['text']; ?></li>
    <?php endforeach; ?>
                    </ol>
                </div>
<?php endif; ?>
    <?php /*if ($mde_content->getLastUpdate()) : ?>
                <p class="credits small text-right">Last update of this page <time datetime="<?php
                    echo $mde_content->getLastUpdate()->format('c')
                ?>"><?php echo $mde_content->getLastUpdate()->format('F j, Y, g:i a'); ?></time>.</p>
    <?php endif;*/ ?>
            </article>

        </div>
    </div>

    <footer id="footer">
        <div class="container">
            <div class="text-muted pull-right">
                <a href="<?php echo $package['homepage']; ?>"><?php echo $package['name']; ?></a> package by <a href="https://github.com/piwi">@piwi</a> under <a href="http://spdx.org/licenses/BSD-3-Clause">BSD 3 Clause</a> license.
                <p class="text-muted small" id="user_agent"></p>
            </div>
        </div>
    </footer>

    <div id="message_box" class="msg_box"></div>
    <a id="bottom"></a>

<!-- jQuery lib from CDN -->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

<!-- Bootstrap from CDN -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

<!-- jQuery.highlight plugin -->
<script src="assets/js/highlight.js"></script>

<!-- scripts for demo -->
<script src="assets/scripts.js"></script>

<script>
$(function() {
    getToHash();
    $("#user_agent").html( navigator.userAgent );
    $('pre').each(function(i,o) {
        var dl = $(this).attr('data-language');
        if (dl) {
            $(this).addClass('code')
                .highlight({indent:'tabs', code_lang: 'data-language'});
        }
    });
});
<?php if (!empty($contents['javascript'])) : ?>
    <?php echo $contents['javascript']; ?>
<?php endif; ?>
</script>
</body>
</html>
