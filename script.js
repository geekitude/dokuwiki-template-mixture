if (JSINFO.LoadNewsTicker) {
    /* DOKUWIKI:include js/jquery.newsTicker-1.0.11.min.js */
}

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
var screen_mode;


function js_mixture_resize(){

    // the z-index in mobile.css is (mis-)used purely for detecting the screen mode here
    screen_mode = jQuery('#mixture__helper').css('z-index') + '';

    // determine our device pattern
    // TODO: consider moving into dokuwiki core
    switch (screen_mode) {
        case '1000':
            if (device_class.match(/phone/)) return;
            device_class = 'mobile phone';
            jQuery('#js_lastchanges_container').hide();
            break;
        case '1001':
            if (device_class.match(/tablet/)) return;
            device_class = 'mobile tablet';
            jQuery('#js_lastchanges_container').show();
            break;
//        case '1002':
//            if (device_class.match(/medium/)) return;
//            device_class = 'desktop medium';
//            break;
//        case '1003':
//            if (device_class.match(/large/)) return;
//            device_class = 'desktop large';
//            break;
//        case '1004':
//            if (device_class.match(/wide/)) return;
//            device_class = 'desktop wide';
//            break;
        case '2001':
            if (device_class.match(/extracted-toc/)) return;
            device_class = 'desktop extracted-toc';
            jQuery('#js_lastchanges_container').show();
            break;
        case '2002':
            if (device_class.match(/extracted-sidebar/)) return;
            device_class = 'desktop extracted-sidebar';
            jQuery('#js_lastchanges_container').show();
            break;
        default:
            if (device_class == 'desktop') return;
            jQuery('#js_lastchanges_container').show();
            device_class = 'desktop';
    }

    jQuery('html').removeClass(device_classes).addClass(device_class);

    // handle some layout changes based on change in device
    var $aside = jQuery('#mixture__aside h3.toggle');
    var $toc = jQuery('#dw__toc h3');

//    if (device_class.match(/extracted-toc/)){
//        // toc expanded
//        if($toc.length) {
//            $toc[0].setState(1);
//        }
//    } else {
//        // toc expanded and toggle shown
//        if($toc.length) {
//            $toc[0].setState(1);
//            //$toc.show();
//        }
//    }

//    if (device_class.match(/extracted-sidebar/)){
//        // sidebar expanded and toggle hidden
//        if($aside.length) {
//            $aside[0].setState(1);
//            //$aside.hide();
//            //$toc.hide();
//        }
//    } else {
//        // sidebar expanded and toggle shown
//        if($aside.length) {
//            $aside[0].setState(1);
//            //$aside.show();
//        }
//    }

//    if (device_class == 'desktop') {
    if (device_class.match(/desktop/)){
        // reset for desktop mode
        if($aside.length) {
            $aside[0].setState(1);
            //if (jQuery("body").hasClass("wrappedSidebar")) {
              //$aside.hide();
            //}
            //} else {
            //  $aside.hide();
            //}
        }
        if($toc.length) {
            $toc[0].setState(1);
//            $toc.removeClass('is-disabled');
//            $tocicon.show();
        }
    }

//    if (device_class.match(/mobile tablet/)){
//        // reset for tablet mode
//        if($aside.length) {
//            $aside[0].setState(1);
//            //$aside.show();
//        }
//        if($toc.length) {
//            $toc[0].setState(1);
//            //$toc.removeClass('is-disabled');
//            //$tocicon.show();
//            //$toc.show();
//        }
//    }
    if (device_class.match(/mobile/)){
        // toc and sidebar collapsed (toggles with titles shown)
        if($aside.length) {
            $aside.show();
            $aside[0].setState(-1);
        }
        if($toc.length) {
            $toc[0].setState(-1);
            //$toc.removeClass('is-disabled');
            //$tocicon.show();
            //$toc.show();
        }
    }

    //var $pagenav = document.querySelector('#mixture__pagenav');
//    //if( ($pagenav.offsetHeight < $pagenav.scrollHeight) || ($pagenav.offsetWidth < $pagenav.scrollWidth)){
//    if( ($pagenav.offsetHeight < $pagenav.scrollHeight) || ($pagenav.offsetWidth < $pagenav.scrollWidth)){
//        // pagenav has overflow
//$pagenav.style.background = "yellow";
//        jQuery('html').addClass("forceDropdownPagenav");
//    } else {
//$pagenav.style.background = "green";
//        // pagenav fits in page
//        jQuery('html').removeClass("forceDropdownPagenav");
//    }
    var pagenav_width = 0;
    var page_width = jQuery('#mixture__pagenav').width();
    var pageid_width = jQuery('#mixture__pagenav .pageId').width();
//console.log(page_width-pageid_width);
    jQuery('#mixture__pagenav .dropdown-content li').each(function() {
        pagenav_width += jQuery(this).outerWidth( true );
    });
    //console.log(pagenav_width);
    if(pagenav_width > page_width-pageid_width){
        // pagenav has overflow
//$pagenav.style.background = "yellow";
        jQuery('html').addClass("forceDropdownPagenav");
    } else {
//$pagenav.style.background = "green";
        // pagenav fits in page
        jQuery('html').removeClass("forceDropdownPagenav");
    }
    jQuery('#mixture__pagenav_ellipsis').css("opacity","1");
}


jQuery(function(){
    var resizeTimer;
    dw_page.makeToggle('#mixture__aside h3.toggle','#mixture__aside div.content');

    js_mixture_resize();
    
    // RESIZE WATCHER
    jQuery(window).resize(function(){
            if (resizeTimer) clearTimeout(resizeTimer);
            resizeTimer = setTimeout(js_mixture_resize,200);
        }
    );

});

jQuery(document).ready(function() {

    // the z-index in mobile.css is (mis-)used purely for detecting the screen mode here
    screen_mode = jQuery('#mixture__helper').css('z-index') + '';

    // Prepare last changes ticker
    jQuery('.js-lastchanges').newsTicker({
        max_rows: 1,
        row_height: parseFloat(jQuery("#js_lastchanges_container").css("font-size")) + 6,
        speed: 600,
        direction: 'up',
        duration: 4000,
        autostart: 1,
        pauseOnHover: 1
    });
    if (screen_mode != '1000') {
        jQuery('#js_lastchanges_container').show();
    }

});
