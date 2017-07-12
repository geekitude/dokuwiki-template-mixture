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
var pagenav_width = 0;

function js_mixture_resize(){

    // the z-index of #mixture__helper div is (mis-)used on purpose for detecting the screen mode here
    screen_mode = jQuery('#mixture__helper').css('z-index') + '';

    // determine our device pattern
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
    var $bannertools = jQuery('#mixture__banner_nav h3.toggle');
    var $aside = jQuery('#mixture__sidebar h3.toggle');
    var $toc = jQuery('#dw__toc h3');

    if (device_class.match(/desktop/)){
        // reset for desktop mode
        if($bannertools.length) {
            $bannertools[0].setState(1);
        }
        if($aside.length) {
            $aside[0].setState(1);
        }
        if($toc.length) {
            $toc[0].setState(1);
        }
    }

    if (device_class.match(/mobile/)){
        // toc and sidebar collapsed (toggles with titles shown)
        if($bannertools.length) {
            $bannertools[0].setState(-1);
        }
        if($aside.length) {
            $aside.show();
            $aside[0].setState(-1);
        }
        if($toc.length) {
            $toc[0].setState(-1);
        }
    }

}

function js_mixture_branding(){
    // fix wiki title and tagline horizontal alignment when window is so tiny they go under logo
    var brandingHeight = jQuery('#mixture__branding_start').height();
    var brandingLogoHeight = jQuery('#mixture__branding_logo').height();
    var brandingTextHeight = jQuery('#mixture__branding_text').height();
    if (brandingHeight > brandingLogoHeight + brandingTextHeight) {
        jQuery('#mixture__branding_text').css("text-align","center");
    }
    var brandingWidth = jQuery('#mixture__branding_start').width();
    var brandingLogoWidth = jQuery('#mixture__branding_logo').width();
    var brandingTextWidth = jQuery('#mixture__branding_text').width();
    if (brandingWidth > brandingLogoWidth + brandingTextWidth) {
        jQuery('#mixture__branding_text').css("text-align","initial");
    }
    //var brandingWidth = jQuery('#mixture__branding').width();
    //var brandingStartWidth = jQuery('#mixture__branding_start').width();
    //var brandingEndWidth = jQuery('#mixture__branding_end').width();
    //var brandingStartLeft = jQuery('#mixture__branding_start').offset().left;
    //var brandingEndLeft = jQuery('#mixture__branding_end').offset().left;
    //console.log(brandingStartLeft);
    //console.log(brandingEndLeft);
    //if ((brandingWidth < brandingStartWidth + brandingEndWidth) && (brandingStartLeft != brandingEndLeft)) {
    ////if (brandingWidth < brandingStartWidth + brandingEndWidth) {
    //    console.log("eh ben?");
    //    jQuery('#mixture__branding_start').css("padding-bottom","1rem");
    //}
}

function js_mixture_pagenav(){
    var page_width = jQuery('#mixture__pagenav').width();
    var pageid_width = jQuery('#mixture__pagenav div.pageId').outerWidth(true);
    var pagetrs_width = 0;
    jQuery('#mixture__pagenav li.trs').each(function() {
        pagetrs_width += jQuery(this).outerWidth(true);
    });
    // 10 pixels substracted to add just a little security in the process
    var available = page_width - pageid_width - pagetrs_width - 10;

    if(pagenav_width > available){
        // pagenav has overflow
        jQuery('body').removeClass("inline-pagenav-dropdown");
    } else {
        // pagenav fits in page
        jQuery('body').addClass("inline-pagenav-dropdown");
    }
}

jQuery(document).ready(function() {

    // the z-index in mobile.css is (mis-)used purely for detecting the screen mode here
    screen_mode = jQuery('#mixture__helper').css('z-index') + '';

    // Get current pagenav width
    jQuery('#mixture__pagenav li.tab').each(function() {
        pagenav_width += jQuery(this).outerWidth(true);
    });

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

    // Show last changes ticker
    if (screen_mode != '1000') {
        jQuery('#js_lastchanges_container').show();
    }

    // Prepare resize watcher and proceed a resize function first run to adjust layout
    jQuery(function(){
        var resizeTimer;
        dw_page.makeToggle('#mixture__sidebar h3.toggle','#mixture__sidebar div.content');
        dw_page.makeToggle('#mixture__banner_nav h3.toggle','#mixture__banner_nav div.content');

        // Proceed first run of resize watcher functions
        js_mixture_resize();
        js_mixture_pagenav();
        js_mixture_branding();
        // Show some hidden elements only after jQuery initialisation
        jQuery('#mixture__pagenav_nsindex').css("opacity","1");

        // RESIZE WATCHER
        jQuery(window).resize(function(){
            // PageNav needs a very fast reaction (switching it is not a heavy process)
            js_mixture_pagenav();
            // Branding text needs a fast reaction time but can occur after PageNav
            js_mixture_branding();
            // Other resize actions (mainly asides' toggles) can be less reactive without harming user experience
            if (resizeTimer) clearTimeout(resizeTimer);
            resizeTimer = setTimeout(js_mixture_resize,200);
        });

    });

});
