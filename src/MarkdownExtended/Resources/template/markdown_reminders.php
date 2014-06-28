<!--//
/**
 * Markdown syntax reminders (for PHP Markdown Extended class)
 *
 * This document is a standalone HTML page presenting a complete review of the Markdown syntax
 * used with the **PHP Markdown Extended** version (*PHP class*).
 *
 * Alternatly, you can embed this content in an existing HTML page. It's been built to be
 * independant from its context (some CSS exceptions may happend).
 *
 * To open this document as a helper in a new browser window, you can use:
 *
 *    <script type="text/javascript">
 *    var mdereminders_window; // use this variable to interact with the cheat sheet window
 *    function mdereminders_popup(url){
 *      if (!url) url='markdown_reminders.html?popup';
 *      if (url.lastIndexOf("popup")==-1) url += (url.lastIndexOf("?")!=-1) ? '&popup' : '?popup';
 *      mdereminders_window = window.open(url, 'markdown_reminders',
 *           'directories=0,menubar=0,status=0,location=0,scrollbars=1,resizable=1,fullscreen=0,width=840,height=380,left=120,top=120');
 *      mdereminders_window.focus();
 *      return false;
 *    }
 *    </script>
 *    <a href="markdown_reminders.html" onclick="return mdereminders_popup();" title="Markdown syntax reminders (new floated window)" target="_blank">
 *        Markdown syntax reminders</a> 
 *
 * You can add arguments when constructing the Javascript handler object for two special features:
 * -   `?popup`: a closer link will be add,
 * -   `?plaintext`: content will be written as plain text (no hidden content).
 *
 * To do so, you can pass a GET argument in the URL if it is a popup, or define a `MDEremindersInit` variable.
 *
 *     var MDEremindersInit='plaintext';
 *
 * This tool was largely inspired by GitHub's wiki editor (sic).
 *
 * @see         Markdown, written by John Gruber <http://daringfireball.net/>
 * @see         Markdown Extra, written by Michel Fortin <http://michelf.com/>
 * @see         (peg) MultiMarkdown, written by Fletcher Penney <http://fletcherpenney.net/>
 * @see         PHP Markdown Extended, written by Pierre Cassat <http://e-piwi.fr/>
 */
//-->
<style type="text/css">
<!--//
#mdereminders         { display: block; position: relative; margin: 0px; padding: 0px; }
#mdereminders_wrapper { 
  display: block; position: relative; margin: 10px; padding: 8px 12px; width: 784px; height: 324px;
  font: 13px/1.4em Helvetica, Arial, freesans, sans-serif !important; color: #404040;
  background-color: #f9f9f9; border: 1px solid #EEEEEE; line-height: 1.4em;
  -moz-border-radius: 10px; -webkit-border-radius: 10px; border-radius: 10px; }
#mdereminders_wrapper, #mdereminders_wrapper li, #mdereminders_wrapper a,
#mdereminders_wrapper th, #mdereminders_wrapper td, #mdereminders_wrapper p { 
  font: 13px/1.4em Helvetica, Arial, freesans, sans-serif; color: #404040; }
#mdereminders_wrapper ul, #mdereminders_wrapper li { list-style-type: none; margin: 0px; padding: 0px; }
#mdereminders_wrapper a { font: inherit; text-decoration: none; color: #4183c4; }
#mdereminders_wrapper a:hover, #mdereminders_wrapper a:active { text-decoration: underline }
#mdereminders_wrapper code { 
    font: 11px normal Monaco, Verdana, Sans-serif; background-color: #f9f9f9; border: 1px solid #D0D0D0; color: #002166;
    padding: 1px 8px; display: inline; }
#mdereminders_wrapper pre      { 
  font-family: Monaco, Verdana, Sans-serif; display: block; overflow: auto; width: auto !important; background-color: #f9f9f9; }
#mdereminders_wrapper pre code { border: none; padding: 4px; display: block; }
#mdereminders_wrapper table    { border: none; padding: 0px; margin:0px; font: inherit; }
#mdereminders_wrapper th, #mdereminders_wrapper td   { border: 1px dotted #ccc; padding: 2px; }
.mdereminders_entry    { display: none; height: 142px; }
.clear                { clear: both; }
.mdereminders_subblock { display:none; }
#mdereminders_title    { display: block; }
#mdereminders_title h2 { 
  margin: 0px; padding: 0px 0px 4px 0px; font: inherit; font-weight: bold; color: #999999; display: inline-block; float: left; }
#mdereminders_closer   { float: right; display: none; }

/* Specifics for JS */
#mdereminders.js #mdereminders_block1, 
#mdereminders.js #mdereminders_block2, 
#mdereminders.js #mdereminders_block3, 
#mdereminders.js #mdereminders_infos { 
  position: relative; margin: 0px; overflow: auto; padding: 0px 0px 10px 0px;
  background-color: #f9f9f9; font: inherit; display: inline-block;  }
#mdereminders.js #mdereminders_block1, 
#mdereminders.js #mdereminders_block2, 
#mdereminders.js #mdereminders_block3 { 
  float: left; height: 200px; border: 1px solid #EEEEEE; border-left: 0px; overflow-x: hidden }
#mdereminders.js #mdereminders_block1   { width: 160px; border-left: 1px solid #EEEEEE;  }
#mdereminders.js #mdereminders_block2   { width: 160px; }
#mdereminders.js #mdereminders_block3   { height: 198px; width: 436px; background: #fff; padding: 0px 12px 12px 12px; font: inherit; }
#mdereminders.js #mdereminders_infos    { width: 782px; height: 76px; position: relative; margin-top: 6px; }
#mdereminders.js #mdereminders_infos ul { margin-top: 10px; }
#mdereminders.js #mdereminders_reset    { float: right; margin: 6px 12px 0 12px; text-align: right; }
#mdereminders_block1 ul, #mdereminders_block2 ul, #mdereminders_infos ul { list-style-type: none; margin: 0px; padding: 0px; }
#mdereminders_infos li { padding-left: 12px; font-size: 11px }
#mdereminders_block1 li a, #mdereminders_block2 li a { 
    display: block; margin: 0px; padding: 4px 12px; font: inherit; font-weight: bold; width: 136px; }
#mdereminders_block1 li a.active, #mdereminders_block2 li a.active,
#mdereminders_block1 li a:hover, #mdereminders_block2 li a:hover { 
    padding: 3px 12px; font: inherit; font-weight: bold; color: #404040;
    background-color: #fff; text-decoration: none; border: 1px solid #f0f0f0;
    border-right: 0px; border-left: 0px; }
#mdereminders.js div.mdereminders_entry   { height: 142px; width: 426px; padding-top: 0px; }
#mdereminders.js .mdereminders_title,
#mdereminders.js .mdereminders_backtop    { display: none; }

/* Specifics for no JS */
#mdereminders.nojs .mdereminders_subblock    { display:block; }
#mdereminders.nojs .mdereminders_entry       { display: block; height: auto !important;background: #fff; padding: 4px 8px; }
#mdereminders.nojs #mdereminders_wrapper     { width: 784px; height: auto !important; }
#mdereminders.nojs .mdereminders_title       { font-weight: bold; color: #4183c4; padding-bottom: 4px; }
#mdereminders.nojs .mdereminders_backtop     { text-align: right; font-size: 10px; }
#mdereminders.nojs #mdereminders_reset       { display: none; }
#mdereminders.nojs .mdereminders_subblock li { padding-left: 22px; }
#mdereminders.nojs #mdereminders_block1, 
#mdereminders.nojs .mdereminders_subblock, 
#mdereminders.nojs .mdereminders_entry, 
#mdereminders.nojs #mdereminders_infos {
  margin-bottom: 12px; padding-bottom: 4px; border-bottom: 1px dotted #EEEEEE; }

/* Specifics for the helper */
#mdereminders_closer a.helper,
#mdereminders_reset a.helper              { position: relative; font-size: 11px; text-decoration: none; }
#mdereminders_closer a.helper span,
#mdereminders_reset a.helper span         { display: none; text-decoration: none; }
#mdereminders_closer a.helper:hover, 
#mdereminders_closer a.helper:active,
#mdereminders_reset a.helper:hover, 
#mdereminders_reset a.helper:active       { background: none; /* bug IE */ z-index: 100; cursor: help; text-decoration: none; }
#mdereminders_closer a.helper.href:hover, 
#mdereminders_closer a.helper.href:active,
#mdereminders_reset a.helper.href:hover, 
#mdereminders_reset a.helper.href:active  { cursor: pointer; }
#mdereminders_closer a.helper:hover span, 
#mdereminders_closer a.helper:active span,
#mdereminders_reset a.helper:hover span, 
#mdereminders_reset a.helper:active span  { 
  display: inline-block; position: absolute;  top: -12px; right: 18px; padding: 6px;
  text-decoration: none; color: #404040; width: 420px !important; height: auto;
  background: #EEEEEE; border: 1px solid #ffffff; font-size: 11px;
  -moz-border-radius: 4px; -webkit-border-radius: 4px; border-radius: 4px; }
#mdereminders_closer a.helper:hover span, 
#mdereminders_closer a.helper:active span { width: 120px !important; }
#mdereminders_wrapper a.helper:hover, #mdereminders_wrapper a.helper:active,
#mdereminders_wrapper a.helper:hover span, #mdereminders_wrapper a.helper:active span { 
  text-decoration: none !important; }
//-->
</style>
<script type="text/javascript">
<!--//

function MarkdownExtendedReminders(){}
(function() {
// Constructor / Init
    this._this = null; // singleton
    this._blocks = [ 'premenu_block', 'menu_block', 'content_block' ];
    this._options = {
        debug: false,
        special_key: 72, // 'h' or 'H'
        blocks_class: "mdereminders_block",
        premenu_block: "mdereminders_block1",
        menu_block: "mdereminders_block2",
        content_block: "mdereminders_block3",
        link_hide: "mdereminders_menuitem",
        link_show: "mdereminders_menuitem active",
        display_show: "inline-block",
        display_hide: "none",
        closer: "mdereminders_closer"
    };
    this._actives = {};
    this._opened = {};
    this._dom = {};
    this._specialKey = false;
    this._no_object = false;
    this._init = function(a){
        var _query = a || window.location.search;
        if (_query && (_query.toLowerCase() == '?plaintext' || _query.toLowerCase() == 'plaintext')) {
            this._no_object=true;
            return this;
        }
        if (this._this!==null) return this._this;
        for( i=0; i<this._blocks.length; i++)
            this.setDom( this._blocks[i], document.getElementById( this.getOpt( this._blocks[i] ) ) );
        if (_query && (_query.toLowerCase() == '?popup' || _query.toLowerCase() == 'popup'))
            this.showClosingTag();
        this._this = this;
        return this;
    };
    this._dbg = function(str){
        if(this.getOpt('debug')==true && window.console && window.console.log) window.console.log(str);
    };
    this.defined = function(x){ return typeof x != 'undefined'; };
// Getters / Setters
    this.isEmpty = function(){
        return this._no_object;
    };
    this.keyPressed = function(e){
        var event = e || window.event, _this = MarkdownExtendedReminders._init();
        if (event.keyCode == _this.getOpt('special_key'))
            _this._specialKey = true;
    };
    this.prevBlock = function( block ){
        var _num = this.findBlockIndex( block );
        return this._blocks[_num-1] || null;
    };
    this.nextBlock = function( block ){
        var _num = this.findBlockIndex( block );
        return this._blocks[_num+1] || null;
    };
    this.getOpt = function(a,b){
        return this._options[a] || b;
    };
    this.getDom = function(a){
        return this._dom[a] || null;
    };
    this.setDom = function(a,b){
        this._dom[a] = b;
    };
    this.getOpened = function(a){
        if (this.defined(this._opened[a])) return this._opened[a];
        else return null;
    };
    this.setOpened = function(a,b){
        this._opened[a] = b;
    };
    this.getActive = function(a){
        if (this.defined(this._actives[a])) return this._actives[a];
        else return null;
    };
    this.setActive = function(a,b){
        this._actives[a] = b;
    };
    this.getHash = function(){
        var myhash = location.hash;
        if (myhash) return myhash.substr(1);
        else return null;
    };
// Finders / Builders
    this.showClosingTag = function(){
        var _closer = document.getElementById( this.getOpt("closer") );
        if (_closer) _closer.style.display = this.getOpt("display_show");
    };
    this.findBlockIndex = function( block ){
        for( i=0; i<this._blocks.length; i++) {
            if (this._blocks[i] == block) return i;
        }
        return null;
    };
    this.findBlock = function( blockid, block ){
        if (!this.defined(block)) block = 'content_block';
        var _this = this.getDom( block );
        if (_this) {
            var _child = _this.childNodes;
            if (_child) {
                for (var i=0; i<_child.length; i++) {
                    if (_child[i].id == blockid) return block;
                }
            }
        }
        var _prev = this.prevBlock( block );
        if (_prev) return this.findBlock( blockid, _prev );
        else return null;
    };
    this.findPosition = function( el, block ){
        if (!this.defined(block)) block = 'menu_block';
        var _this = this.getDom( block );
        if (_this) {
            var _child_li = _this.getElementsByTagName("li");
            if (_child_li) {
                for (var i=0; i<_child_li.length; i++) {
                    if (_child_li[i].getElementsByTagName("a")[0] == el) return i;
                }
            }
        }
        var _prev = this.prevBlock( block );
        if (_prev) return this.findPosition( el, _prev );
        else return null;
    };
    this.getHandlerByPosition = function( block, position ){
        this._dbg("Entering 'getHandlerByPosition()' function with block="+block+" | position="+position);
        var _this = this.getDom( block );
        if (_this) {
            var _child_li = _this.getElementsByTagName("li");
            if (_child_li && this.defined(_child_li[position]))
                return _child_li[position].getElementsByTagName("a")[0];
        }
        return null;
    };
    this.getBlockById = function( blockid ){
        this._dbg("Entering 'getBlockById()' function with blockid="+blockid );
        for ( i=0; i<this._blocks.length; i++) {
            var _this = this.getDom( this._blocks[i] );
            if (_this && _this.id == blockid) return this._blocks[i];
        }
        return null;
    };
    this.findLinker = function( hash, block ){
        this._dbg("Entering 'findLinker()' function with hash="+hash+" | block="+block );
        if (!this.defined(block)) block = 'menu_block';
        var _this = this.getDom( block );
        if (_this) {
            var _child_li = _this.getElementsByTagName("li");
            if (_child_li) {
                for (var i=0; i<_child_li.length; i++) {
                    var myhref = _child_li[i].getElementsByTagName("a")[0].href;
                    if (myhref && myhref.indexOf("#")) {
                        var myhrefhash = myhref.substr( myhref.indexOf("#")+1 );
                        if (myhrefhash == hash)
                            return new Array( _child_li[i].parentNode.parentNode.id, i );
                    }
                }
            }
        }
        return null;
    };
    this.findClosestDiv = function( el ){
        this._dbg("Entering 'findClosestDiv()' function with el="+el );
        var _myel = el.parentNode;
        if (_myel) {
            if (_myel.nodeName.toLowerCase() != 'div' && _myel.className != this.getOpt('blocks_class')) {
                while ((_myel.nodeName.toLowerCase() != 'div') || (_myel.className != this.getOpt('blocks_class'))) {
                    _myel = _myel.parentNode;
                    if (!_myel) break;
                }
            }
            return _myel;
        }
        return null;
    };
    this.buildLocation = function( hash ){
        var _url = document.location.href, _hash = this.getHash();
        if (_hash) _url = _url.replace(_hash, hash);
        else if (_url.lastIndexOf("#") == _url.length-1) _url += hash;
        else _url += '#'+hash;
        return _url;
    };
// Process
    this.blockToggler = function( blockid, what, handler ){
        this._dbg( "Entering function 'blockToggler()' with blockid="+blockid+" | what="+what+' | handler='+handler );
        var _this = document.getElementById( blockid );
        if (_this) {
            if (!this.defined(what))
                what = (_this.style.display==this.getOpt('display_hide') ? this.getOpt('display_show') : this.getOpt('display_hide'));
            _this.style.display = what;
            if (what == this.getOpt('display_show')) {
                var _myblock = this.getBlockById( this.findClosestDiv(_this).id ),
                      _oprev = this.getOpened( _myblock );
                this.setOpened( _myblock, blockid );
                if (_oprev) this.blockToggler( _oprev, this.getOpt('display_hide') );
            }
        }
    };
    this.handlerToggler = function( handler, what ){
        if (handler) {
            this._dbg( "Entering function 'handlerToggler()' with handler="+handler+" | what="+what );
            handler.className = what;
            if (what == this.getOpt('link_show')) {
                var _myblock = this.getBlockById( this.findClosestDiv(handler).id ),
                    _oprev = this.getActive( _myblock );
                this.setActive( _myblock, this.findPosition( handler, _myblock ) );
                if (_oprev>=0) this.handlerToggler( this.getHandlerByPosition(_myblock, _oprev), this.getOpt('link_hide'));
            }
        }
    };
    this.clearBlock = function( block, full ){
        this._dbg( "Clearing block "+block );
        var _oprev = this.getOpened( block ),
              _aprev = this.getActive( block );
        if (_oprev!==null && full!==false) {
            this.blockToggler( _oprev, this.getOpt('display_hide') );
            this.setOpened( block, null );
        }
        if (_aprev!==null) {
            this.handlerToggler( this.getHandlerByPosition(block, _aprev), this.getOpt('link_hide'));
            this.setActive( block, null );
        }
    };
// User Interface
    this.openBlock = function( openid, blockid, handler, close_others ){
        this._dbg( "Entering function 'openBlock()' with openid="+openid+" | blockid="+blockid+' | handler='+handler );
        if (this._specialKey==true) {
            var _url = this.buildLocation( openid );
            if (_url) {
                window.location = _url;
                this._specialKey = false;
            }
        }
        var _myblock = this.getBlockById( blockid ),
            _this = this.getDom( _myblock );
        if (_this) {
            var _child = _this.childNodes,
                    _opd = this.getOpened(_myblock),
                    _act = this.getActive(_myblock);
            if (_child) {
                for (var i=0; i<_child.length; i++) {
                    if (this.defined( _child[i].id )) {
                        if (_child[i].id == openid) {
                            if (!this.defined(_opd) || _opd != _child[i].id)
                                this.blockToggler( _child[i].id, this.getOpt('display_show'), handler );
                            if (_act)
                                this.handlerToggler( this.getHandlerByPosition(_myblock, _act), this.getOpt('link_hide'));
                            if (handler)
                                this.handlerToggler( handler, this.getOpt('link_show') );
                            if (close_others!==false) {
                                var _next = this.nextBlock( _myblock );
                                while (_next) {
                                    this.clearBlock( _next );
                                    _next = this.nextBlock( _next );
                                }
                            }
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    };
    this.openAndFollow = function( hash ){
        this._dbg( "Executing function 'openAndFollow()' with hash="+hash );
        if (hash) {
            var myblock = this.findBlock(hash);
            if (myblock) {
                var _opened = this.openBlock(hash, this.getDom( myblock ).id ),
                        _prev = this.prevBlock( myblock );
                if (!_opened && _prev) {
                    var mylinker = this.findLinker(hash, _prev);
                    if (this.defined(mylinker[1])) {
                        var _nopened = this.openBlock(mylinker[0], this.getDom( _prev ).id, this.getHandlerByPosition(_prev, mylinker[1]), false );
                        if (_nopened===true)
                            this.handlerToggler( this.getHandlerByPosition(_prev, mylinker[1]), this.getOpt('link_show'));
                    }
                    var _nprev = this.prevBlock( _prev );
                    if (_nprev) {
                        var _nlinker = this.findLinker(mylinker[0], _nprev);
                        if (this.defined(_nlinker[1]))
                            this.handlerToggler( this.getHandlerByPosition(_nprev, _nlinker[1]), this.getOpt('link_show'));
                    }
                    return false;
                }
            }
        }
        return true;
    };
    this.deepLinker = function(){
        var myhash = this.getHash();
        this._dbg( "Executing function 'deepLinker()', found hash="+myhash );
        if (myhash) return this.openAndFollow( myhash );
    };
    this.clearBlocks = function(){
        for( i=0; i<this._blocks.length; i++) {
            if (i!=0) this.clearBlock( this._blocks[i] );
            else this.clearBlock( this._blocks[i], false );
        }
    };
}).apply(MarkdownExtendedReminders);

//-->
</script>
<div id="mdereminders" class="nojs">
<div id="mdereminders_wrapper">

    <div id="mdereminders_title">
        <h2>Markdown Extended syntax reminders</h2>
        <div id="mdereminders_closer">
            <a class="helper href" href="#" onclick="return closeReminders();"><big>&nbsp;&Chi;&nbsp;</big><span><strong>Close this window</strong></span></a>
        </div>
    </div>
    <br class="clear" />
    <div id="mdereminders_block1" class="mdereminders_block">
        <ul>
            <li><a class="mdereminders_menuitem" href="#blockelements" onclick="return openBlock('blockelements', 'mdereminders_block2', this);" title="See this section">Block Elements</a></li>
            <li><a class="mdereminders_menuitem" href="#spanelements" onclick="return openBlock('spanelements', 'mdereminders_block2', this);" title="See this section">Span Elements</a></li>
            <li><a class="mdereminders_menuitem" href="#miscellaneous" onclick="return openBlock('miscellaneous', 'mdereminders_block2', this);" title="See this section">Miscellaneous</a></li>
        </ul>
    </div>

    <div id="mdereminders_block2" class="mdereminders_block">

        <div id="blockelements" class="mdereminders_subblock">
            <ul>
<?php foreach ($block_contents as $_content) : ?>
                <li><a class="mdereminders_menuitem" href="#<?php echo $_content->getId(); ?>" onclick="return openBlock('<?php echo $_content->getId(); ?>', 'mdereminders_block3', this);" title="See this section"><?php echo $_content->getMetadata('title'); ?></a></li>
<?php endforeach; ?>
            </ul>
        </div>

        <div id="spanelements" class="mdereminders_subblock">
            <ul>
<?php foreach ($span_contents as $_content) : ?>
                <li><a class="mdereminders_menuitem" href="#<?php echo $_content->getId(); ?>" onclick="return openBlock('<?php echo $_content->getId(); ?>', 'mdereminders_block3', this);" title="See this section"><?php echo $_content->getMetadata('title'); ?></a></li>
<?php endforeach; ?>
            </ul>
        </div>

        <div id="miscellaneous" class="mdereminders_subblock">
            <ul>
<?php foreach ($misc_contents as $_content) : ?>
                <li><a class="mdereminders_menuitem" href="#<?php echo $_content->getId(); ?>" onclick="return openBlock('<?php echo $_content->getId(); ?>', 'mdereminders_block3', this);" title="See this section"><?php echo $_content->getMetadata('title'); ?></a></li>
<?php endforeach; ?>
            </ul>
        </div>

    </div>

    <div id="mdereminders_block3" class="mdereminders_block">

<?php foreach ($block_contents as $_content) : ?>
        <div id="<?php echo $_content->getId(); ?>" class="mdereminders_entry">
            <div class="mdereminders_title"><?php echo $_content->getMetadata('title'); ?></div>
            <?php echo $_content->getBody(); ?>
            <div class="mdereminders_backtop"><a href="#mdereminders" title="Back to top">top</a></div>
        </div>
<?php endforeach; ?>

<?php foreach ($span_contents as $_content) : ?>
        <div id="<?php echo $_content->getId(); ?>" class="mdereminders_entry">
            <div class="mdereminders_title"><?php echo $_content->getMetadata('title'); ?></div>
            <?php echo $_content->getBody(); ?>
            <div class="mdereminders_backtop"><a href="#mdereminders" title="Back to top">top</a></div>
        </div>
<?php endforeach; ?>

<?php foreach ($misc_contents as $_content) : ?>
        <div id="<?php echo $_content->getId(); ?>" class="mdereminders_entry">
            <div class="mdereminders_title"><?php echo $_content->getMetadata('title'); ?></div>
            <?php echo $_content->getBody(); ?>
            <div class="mdereminders_backtop"><a href="#mdereminders" title="Back to top">top</a></div>
        </div>
<?php endforeach; ?>

    </div>
    <br class="clear" />
    <div id="mdereminders_infos">
        <div id="mdereminders_reset">
            <a class="helper href" href="#" onclick="return clearBlocks();"><big>&nbsp;&#8617;&nbsp;</big><span><strong>Reset this page</strong><br />By clicking on this link, document will be reloaded without any opened link.</span></a>
            <br />
            <a class="helper" href="javascript:void(0);"><big>&nbsp;&oplus;&nbsp;</big><span><strong>Keyboard chortcut ['H' = 'href']</strong><br />A keyboard shortcut is set to load the URL links in the navigation bar of your browser by pressing letter 'H' (<em>case-insensitive</em>) when clicking on a link.</span></a>
            <br />
            <a class="helper href" href="<?php echo $mde_home; ?>" target="_blank"><big>&nbsp;&copy;&nbsp;</big><span><strong><em><?php echo $mde_name; ?></em> is an <em>open source</em> application</strong><br />Follow this link to get the source code from GitHub repository.</span></a>
            <br />
            <a class="helper href" href="<?php echo $mde_home; ?>/releases/tag/<?php echo MarkdownExtended\MarkdownExtended::MDE_VERSION; ?>" target="_blank"><?php echo MarkdownExtended\MarkdownExtended::MDE_VERSION; ?><span>Based on the <em><?php echo $mde_name; ?></em> package version <strong><?php echo $mde_version; ?></strong></span></a>
        </div>
        <ul>
            <li><strong>Markdown</strong> is a text-to-HTML conversion tool written by <a href="http://daringfireball.net/" title="See http://daringfireball.net/">John Gruber</a> - &copy; 2004 John Gruber (<em>Perl</em> script).</li>
            <li><strong>Markdown Extra</strong> is a PHP extended version written by <a href="http://michelf.com/" title="See http://michelf.com/">Michel Fortin</a> - &copy; 2009 Michel Fortin (<em>PHP</em> script).</li>
            <li><strong>(peg) MultiMarkdown</strong> is a C extended version wirtten by <a href="http://fletcherpenney.net/" title="See http://fletcherpenney.net/">Fletcher Penney</a> - &copy; 2010-2011 Fletcher T. Penney (<em>C</em> and <em>Perl</em> script).</li>
            <li><strong><?php echo $mde_name; ?></strong> is a PHP extended version wirtten by <a href="http://e-piwi.fr/" title="See http://e-piwi.fr/">Pierre Cassat</a> - &copy; 2012 Pierre Cassat & contributors (<em>PHP</em> script).</li>
            <li>All versions are licensed under the terms of the <a href="http://opensource.org/licenses/BSD-3-Clause" title="Read the license online" target="_blank">BSD-3-Clause open source license</a>.</li>
        </ul>
    </div>
</div>
</div>
<script type="text/javascript">
<!--//
// the cheat sheet object
var mdereminders_obj = MarkdownExtendedReminders._init( typeof(MDEremindersInit)=='undefined' ? null : MDEremindersInit );
function openBlock(a,b,c){ return mdereminders_obj.isEmpty() || mdereminders_obj.openBlock(a,b,c); }
function clearBlocks(){ return mdereminders_obj.isEmpty() || mdereminders_obj.clearBlocks(); }
function openAndFollow(a){ return mdereminders_obj.isEmpty() || mdereminders_obj.openAndFollow(a); }
function closeReminders(){ window.close(); var _opnr=window.opener; if(_opnr)_opnr.focus(); return false; }
if (!mdereminders_obj.isEmpty()){
    // for accessibility
    document.getElementById("mdereminders").className="js";
    // for keyboard shortcut
    document.onkeydown = mdereminders_obj.keyPressed;
    // launch deep linking when doc is loaded
    window.onload = mdereminders_obj.deepLinker();
}
//-->
</script>
