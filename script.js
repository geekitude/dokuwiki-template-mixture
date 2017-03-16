/**
 *  We handle several device classes based on browser width.
 *
 *  - desktop:   > __tablet_width__ (as set in style.ini)
 *  - mobile:
 *    - tablet   <= __tablet_width__
 *    - phone    <= __phone_width__
 */

jQuery(function(){
    //var resizeTimer;
    dw_page.makeToggle('#dokuwiki__aside h3.toggle','#dokuwiki__aside div.content');

    //tpl_dokuwiki_mobile();
    //jQuery(window).on('resize',
    //    function(){
    //        if (resizeTimer) clearTimeout(resizeTimer);
    //        resizeTimer = setTimeout(tpl_dokuwiki_mobile,200);
    //    }
    //);

    // increase sidebar length to match content (desktop mode only)
    //var $sidebar = jQuery('.desktop #dokuwiki__aside');
    //if($sidebar.length) {
    //    var $content = jQuery('#dokuwiki__content div.page');
    //    $content.css('min-height', $sidebar.height());
    //}
});
