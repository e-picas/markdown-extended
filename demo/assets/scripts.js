/* Scripts for demo */

function initHandler( _name, opened ){
    var elt_handler = $('#'+_name+'_handler'),
        elt_handler_icon = elt_handler.find('.fa'),
        elt_block = $('#'+_name);
    if (opened==undefined || opened==false) {
        elt_block.hide();
    } else {
        elt_handler_icon.removeClass('fa-caret-right').addClass('fa-caret-down');
    }
    elt_handler.click(function(){ 
        elt_block.toggle('slow');
        if (elt_handler_icon.hasClass('fa-caret-down')) {
            elt_handler_icon.removeClass('fa-caret-down').addClass('fa-caret-right');
        } else {
            elt_handler_icon.removeClass('fa-caret-right').addClass('fa-caret-down');
        }
    });
}

function getNewLi( str ){
    return $('<li />').html(str);
}

function getNewA( href, str ){
    return $('<a />', {'href':href}).html(str);
}

function getNewInfoItem( str, title, href ){
    var strong = $('<strong />').html( href!==undefined ? getNewA(href, str) : str );
    return getNewLi( title+' : ' ).append(strong);
}

function getPluginManifest( url, callback ) {
    $.ajax(url, {
        error: function(jqXHR, textStatus, error) {
            addMessage('AJAX error! ['+textStatus+(error ? ' : '+error : '')+']');
            return false;
        },
        success: function(data) { callback.apply(this, [data]); }
    });
}

function getGitHubCommits( github, callback ) {
    $.ajax(github+'commits', {
        method: 'GET',
        crossDomain: true,
        data: { page: 1, per_page: 5 },
        dataType: 'json',
        error: function(jqXHR, textStatus, error) {
            addMessage('AJAX error! ['+textStatus+(error ? ' : '+error : '')+']');
            return false;
        },
        success: function(data, textStatus, jqXHR) { 
            if (data.length>1 || data[0]!==undefined) {
                callback.apply(this, [data]);
            } else {
                callback.apply(this, [null]);
            }
        }
    });
}

function getGitHubBugs( github, callback ) {
    $.ajax(github+'issues', {
        method: 'GET',
        crossDomain: true,
        data: { page: 1, per_page: 5 },
        dataType: 'json',
        error: function(jqXHR, textStatus, error) {
            addMessage('AJAX error! ['+textStatus+(error ? ' : '+error : '')+']');
            return false;
        },
        success: function(data, textStatus, jqXHR) {
            if (data.length>1 || data[0]!==undefined) {
                callback.apply(this, [data]);
            } else {
                callback.apply(this, [null]);
            }
        }
    });
}

function getUrlFilenameAndQuery( url ){
    var filename, qm = url.lastIndexOf('#');
    if (qm!==-1) { filename = url.substr(0,qm); }
    else { filename = url; }
    return filename.substring(filename.lastIndexOf('/')+1);
}

function getUrlFilename( url ){
    var filename, qm = url.lastIndexOf('?');
    if (qm!==-1) { filename = url.substr(0,qm); }
    else { filename = url; }
    return filename.substring(filename.lastIndexOf('/')+1);
}

function getToHash(){
    var _hash = window.location.hash;
    if (_hash!==undefined) {
        var hash = $('#'+_hash.replace('#', ''));
        if (hash.length) {
            var poz = hash.position();
            $("html:not(:animated),body:not(:animated)").animate({ scrollTop: poz.top });
        }
    }
}

function addCSSValidatorLink( css_filename ){
    var url = window.location.href,
        cssfile = url.replace(/(.*)\/.*(\.html$)/i, '$1/'+css_filename);
    $('#footer a#css_validation').attr('href', 'http://jigsaw.w3.org/css-validator/validator?uri='+encodeURIComponent(cssfile));
}

function addHTMLValidatorLink( url ){
    if (url===undefined || url===null) { var url = window.location.href; }
    $('#footer a#html_validation').attr('href', 'http://html5.validator.nu/?showimagereport=yes&showsource=yes&doc='+encodeURIComponent(url));
}

function addMessage( str ){
    var msg = $('<span />').html(str),
        msgbox = $('#message_box');
    msgbox.append(msg);
    if (!msgbox.is(':visible')) { msgbox.show(1000); }
    msgbox.delay(5000).hide(1000);
}
