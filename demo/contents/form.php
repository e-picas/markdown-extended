<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$data = [
    'meta_title' => 'Form field test',
    'javascript' => '',
    'mde_info' => true,
];

// process
$form_content = '';
$textarea_content = '';
if (!empty($_POST)) {
    $textarea_content = htmlspecialchars($_POST['mde_content']);
    $options = ['config_file' => \MarkdownExtended\API\Kernel::getResourcePath('config-simple', 'config')];
    $mde_content = \MarkdownExtended\MarkdownExtended::parseString($textarea_content, $options);
    if (!empty($mde_content)) {
        $form_content = <<<CTT
            <fieldset>
                    <legend>Rendering of your comment:</legend>
                    <pre>{$mde_content->getBody()}</pre>
            </fieldset>
            CTT;
    }
}

$data['content'] = <<<CTT
    <form action="index.php?page=form" method="post">
        {$form_content}
        <fieldset>
            <legend>Markdown text field</legend>
            <div class="form-group">
                <label for="mde_input_content" class="control-label">Input a comment with Markdown tags:</label>
                <textarea name="mde_content" id="mde_input_content" class="form-control" style="width: 100%; heightm: auto; min-height: 200px">{$textarea_content}</textarea>
                <p class="help-block">Allowed tags are : emphasis (bold and italic), links and images, code spans and blocks, blockquotes, simple lists and definitions lists, abbreviations, horizontal rules and tables.</p>
            </div>
        </fieldset>
        <button type="submit" class="btn btn-primary">Submit</button>
        <button type="reset" class="btn btn-default">Reset</button>
    </form>
    <div class="clear-fix"></div>
    CTT;

return $data;
