<?php
/*
 * configuration metadata
 *
 */

/*$meta['discussionNs']       = array('string');*/
/*$meta['hideTools']          = array('onoff');*/
/*$meta['scrollDelay']        = array('numeric');*/
/*$meta['pageLayout']         = array('multichoice', '_choices' => array('quartered','boxed'));*/
$meta['pageTitle']          = array('onoff'); /* outside home, replace wiki title by page title (and tagline by wiki title) */
$meta['extractToC']          = array('onoff'); /* move ToC out of main content as soon as there's enough room */
$meta['scrollspyToC']          = array('onoff'); /* enable scrollspy ToC (requires previous option to be enabled) */
$meta['extractSidebar']          = array('onoff'); /* move Sidebar out of main content as soon as there's enough room */
$meta['stickySidebar']          = array('onoff'); /* try to keep Sidebar always in viewport (buggy right now) */
$meta['wrappedSidebar']          = array('onoff'); /* wrap content around sidebar (potential side effects with syntax plugins, for exemple, does not work well with larg WRAP elements) */
