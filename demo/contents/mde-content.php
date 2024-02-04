<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// parse concerned document
$mde_content = \MarkdownExtended\MarkdownExtended::parseSource($doc, $parse_options);

// prepare returned data
$data = [
    'file_path' => str_replace(dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR, '', $doc),
    'meta_title' => $mde_content->getTitle(),
    'meta_description' => $mde_content->getMetadata('description'),
    'metadata' => $mde_content->getMetadataFormatted(),
    'mde_info' => true,
];

// light-weight content for PHP dump
$mdtodump = clone $mde_content;
$dumpinfo = '*** deleted for dump clarity ***';
$mdtodump
    ->setContent($dumpinfo)
    ->setBody($dumpinfo)
    ->setSource($dumpinfo)
;
$dump = var_export($mdtodump, true);

// server handler
if (substr_count($doc, dirname(__DIR__)) > 0 && substr_count(strtolower($_SERVER['SERVER_SOFTWARE']), 'apache') > 0) {
    $messages[] = <<<CTT
        Server handler tests: <a href="$doc_uri" role="tab">handled content</a>&nbsp;|&nbsp;<a href="$doc_uri?plain" role="tab">original content</a>
CTT;
}

// data content
if ($notab !== true) {
    $data['content'] = <<<CTT
        <div role="tabpanel">
          <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#mde-parsed" aria-controls="mde-parsed" role="tab" data-toggle="tab">Parsed content</a></li>
            <li role="presentation"><a href="#plain-text" aria-controls="plain-text" role="tab" data-toggle="tab">Plain text version</a></li>
            <li role="presentation"><a href="#php-dump" aria-controls="php-dump" role="tab" data-toggle="tab">Object dump</a></li>
          </ul>
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active" id="mde-parsed">
                <div class="well text-success">Below is the rendering of parsed content.</div>
                {$mde_content->getBody()}
                {$mde_content->getNotesFormatted()}
            </div>
            <div role="tabpanel" class="tab-pane fade" id="plain-text">
                <div class="well text-success">Below is the raw markdown original content.</div>
                <pre>{$mde_content->getSource()}</pre>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="php-dump">
                <div class="well text-success">Below is a dump of the PHP \MarkdownExtended\Content object issued from parsing.</div>
                <pre>{$dump}</pre>
            </div>
          </div>
        </div>
CTT;

    // data JS
    $data['javascript'] = <<<CTT
        function updateTabs() {
            var _hash = window.location.hash.substring(1);
            if (_hash != ''){
                var _tab = $('.tab-content div#'+_hash+'.tab-pane');
                if (_tab.length > 0) {
                    $('.fade.in.active').removeClass('in active');
                    _tab.addClass('in active');
                    $('.nav-tabs .active').removeClass('active');
                    $('.nav-tabs [aria-controls='+_hash+']').parent('li').addClass('active');
                }
            }
        }
        $(function() {
            updateTabs();
            $(window).on('hashchange', updateTabs);
        });
CTT;

} else {
    $data['content'] = <<<CTT
                {$mde_content->getBody()}
                {$mde_content->getNotesFormatted()}
CTT;
}

// return data
return $data;
