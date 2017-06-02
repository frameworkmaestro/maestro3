
/*
var msg=$("<div class=\"datagrid-mask-msg\" style=\"display:block;left:50%\"></div>").html(opts.loadMsg).appendTo(_1cb);
msg._outerHeight(40);
msg.css({marginLeft:(-msg.outerWidth()/2),lineHeight:(msg.height()+"px")});
*/

function ajaxindicatorstart(text)
{
    if(jQuery('body').find('#resultLoading').attr('id') != 'resultLoading'){
//        jQuery('body').append('<div id="resultLoading" style="display:none"><div><img src="ajax-loader.gif"><div>'+text+'</div></div><div class="bg"></div></div>');
        var msg=$("<div id='resultLoading' class=\"datagrid-mask-msg\" style=\"display:block;left:50%\"></div>").html(text).appendTo('body');
        msg._outerHeight(40);
        msg.css({marginLeft:(-msg.outerWidth()/2),lineHeight:(msg.height()+"px")});
    }

    /*
    if(jQuery('body').find('#resultLoading').attr('id') != 'resultLoading'){
        jQuery('body').append('<div id="resultLoading" style="display:none"><div><img src="ajax-loader.gif"><div>'+text+'</div></div><div class="bg"></div></div>');
    }

    jQuery('#resultLoading').css({
        'width':'100%',
        'height':'100%',
        'position':'fixed',
        'z-index':'10000000',
        'top':'0',
        'left':'0',
        'right':'0',
        'bottom':'0',
        'margin':'auto'
    });

    jQuery('#resultLoading .bg').css({
        'background':'#000000',
        'opacity':'0.7',
        'width':'100%',
        'height':'100%',
        'position':'absolute',
        'top':'0'
    });

    jQuery('#resultLoading>div:first').css({
        'width': '250px',
        'height':'75px',
        'text-align': 'center',
        'position': 'fixed',
        'top':'0',
        'left':'0',
        'right':'0',
        'bottom':'0',
        'margin':'auto',
        'font-size':'16px',
        'z-index':'10',
        'color':'#ffffff'

    });

    jQuery('#resultLoading .bg').height('100%');
    */
    jQuery('#resultLoading').fadeIn(300);
    jQuery('body').css('cursor', 'wait');
}

function ajaxindicatorstop()
{
    /*
    jQuery('#resultLoading .bg').height('100%');
    */
    jQuery('#resultLoading').fadeOut(300);
    jQuery('body').css('cursor', 'default');
}

jQuery(document).ajaxStart(function () {
    //show ajax indicator
    ajaxindicatorstart('Processing... please wait...');
}).ajaxStop(function () {
    //hide ajax indicator
    ajaxindicatorstop();
});