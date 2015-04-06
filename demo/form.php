<?php
// show errors at least initially
@ini_set('display_errors','1'); @error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

// set a default timezone to avoid PHP5 warnings
$dtmz = date_default_timezone_get();
date_default_timezone_set( !empty($dtmz) ? $dtmz:'Europe/Paris' );

$class_info = \MarkdownExtended\Helper::info(true);
$info = <<<EOT
    <p><a id="classinfo_handler" class="handler" title="Infos from the MarkdownExtended class">Current class infos</a></p>
    <div id="classinfo"><p>$class_info</p></div>
EOT;

// process
if (!empty($_POST)) {
    $posted = htmlspecialchars($_POST['mde_content']);
    $options = array('config_file'=>\MarkdownExtended\Config::SIMPLE_CONFIGFILE);
    $mde_content = \MarkdownExtended\MarkdownExtended::create()
        ->transformString($posted, $options);
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

<form action="index.php?page=form" method="post">
<?php if (!empty($mde_content)) : ?>
    <fieldset>
        <legend>Rendering of your comment:</legend>
        <?php echo $mde_content->getBody(); ?>
    </fieldset>
<?php endif; ?>
    <fieldset>
        <legend>Markdown text field</legend>
        <label for="mde_input_content">Input a comment with Markdown tags:</label>
        <textarea name="mde_content" id="mde_input_content" style="width: 100%; heightm: auto; min-height: 200px"><?php
        if (!empty($mde_content)) echo $mde_content->getSource();
        ?></textarea>
        <p class="comment">Allowed tags are : emphasis (bold and italic), links and images, code spans and blocks, blockquotes, simple lists and definitions lists, abbreviations, horizontal rules and tables.</p>
    </fieldset>
    <input type="submit" />
    <input type="reset" />
</form>
