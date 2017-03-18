/**
 *  We handle several device classes based on browser width.
 *
 *  - desktop:   > 1201px
 *  - mobile:
 *    - tablet   >= 544px
 *    - phone    <= 543px
 *  And a special state when ToC and/or Sidebar are "extracted"
 */
var device_class = ''; // not yet known
var device_classes = 'extractedtoc extractedsb desktop mobile tablet phone';


function mixture_mobile(){

    // the z-index in mobile.css is (mis-)used purely for detecting the screen mode here
    var screen_mode = jQuery('#screen__mode').css('z-index') + '';

    // determine our device pattern
    // TODO: consider moving into dokuwiki core
    switch (screen_mode) {
        case '1000':
            if (device_class.match(/phone/)) return;
            device_class = 'mobile phone';
            break;
        case '1001':
            if (device_class.match(/tablet/)) return;
            device_class = 'mobile tablet';
            break;
        case '2001':
        case '2002':
            device_class = 'extracted desktop';
            break;
        default:
            if (device_class == 'desktop') return;
            device_class = 'desktop';
    }

    jQuery('html').removeClass(device_classes).addClass(device_class);

    // handle some layout changes based on change in device
    var $aside = jQuery('#dokuwiki__aside h3.toggle');
    var $toc = jQuery('#dw__toc h3');
    var $tocicon = jQuery('#dw__toc h3 strong');

    if (device_class.match(/extracted/)){
        // toc expand and aside hide
        if($toc.length) {
            $toc[0].setState(1);
            $toc.addClass('is-disabled');
            $tocicon.hide();
        }
        if($aside.length) {
            $aside[0].setState(1);
            $aside.hide();
        }
    }
    if (device_class == 'desktop') {
        // reset for desktop mode
        if($aside.length) {
            $aside[0].setState(1);
            $aside.hide();
        }
        if($toc.length) {
            $toc[0].setState(1);
            $toc.removeClass('is-disabled');
            $tocicon.show();
        }
    }
    if (device_class.match(/mobile tablet/)){
        // reset for tablet mode
        if($aside.length) {
            $aside[0].setState(1);
            $aside.hide();
        }
        if($toc.length) {
            $toc[0].setState(1);
            $toc.removeClass('is-disabled');
            $tocicon.show();
        }
    }
    if (device_class.match(/mobile phone/)){
        // toc and sidebar hiding
        if($aside.length) {
            $aside.show();
            $aside[0].setState(-1);
        }
        if($toc.length) {
            $toc[0].setState(-1);
            $toc.removeClass('is-disabled');
            $tocicon.show();
        }
    }
}


jQuery(function(){
    var resizeTimer;
    dw_page.makeToggle('#dokuwiki__aside h3.toggle','#dokuwiki__aside div.content');

    mixture_mobile();
    jQuery(window).on('resize',
        function(){
            if (resizeTimer) clearTimeout(resizeTimer);
            resizeTimer = setTimeout(mixture_mobile,200);
        }
    );
});
