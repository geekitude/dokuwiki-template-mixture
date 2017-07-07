<?php
/*
 * configuration metadata
 *
 */

/*$meta['discussionNs']       = array('string');*/
/*$meta['hideTools']          = array('onoff');*/
/*$meta['scrollDelay']        = array('numeric');*/
/*$meta['pageLayout']         = array('multichoice', '_choices' => array('quartered','boxed'));*/
$meta['elements']           = array('multicheckbox',
                               '_choices' => array('topbar_date','topbar_lastchanges','topbar_links','header_logo','header_banner','widebanner','sidebar_cover','pagenav_nsindex'));
$meta['dateLocale']         = array('string');
$meta['dateString']         = array('string');
$meta['lastChanges']        = array('multicheckbox', '_choices' => array('skip_deleted','skip_minors','skip_subspaces')); /* [other] field should contain a single integer, the number of last changes to show */
$meta['lastChangesWhat']    = array('multichoice', '_choices' => array('pages','media','both'));
$meta['lastChangesWhere']   = array('multichoice', '_choices' => array('anywhere','any_start_page','wiki_root'));
$meta['topbar']             = array('string','_pattern' => '/^(|[a-zA-Z\-:]+)$/'); /* name of pages containing topbar links */
$meta['topbarFrom']         = array('multichoice','_choices' => array('root','namespace','inherit','cumulate')); /* get topbar links from wiki root only / current ns only / current ns and all parents */
/*$meta['logoLink']           = array('multichoice','_choices' => array('none','home','parent_namespace','namespace_start','dynamic', 'other')); /* dynamic: current ns start page on random pages, parent ns start page for sub ns start page, home for root ns, landing area on home while "image" will give some kind of lightbox or modal to a large image in same namespace (name set with "logoLinkImage" setting) and will default to "none" if image doesn't exist */
$meta['titleLink']          = array('multichoice','_choices' => array('none','home','parent_namespace','namespace_start','dynamic')); /* dynamic: current ns start page on random pages, parent ns start page for sub ns start page, home for root ns, landing area on home */
/*$meta['taglineLink']        = array('multichoice','_choices' => array('none','home','parent_namespace','namespace_start','dynamic')); /* dynamic: current ns start page on random pages, parent ns start page for sub ns start page, home for root ns, landing area on home */
/*$meta['bannerLink']        = array('multichoice','_choices' => array('none','home','parent_namespace','namespace_start','dynamic')); /* dynamic: current ns start page on random pages, parent ns start page for sub ns start page, home for root ns, landing area on home */
$meta['dynamicBranding']    = array('onoff'); /* outside home page, wiki title is replaced by page title and tagline by wiki title */
$meta['exclusions']         = array('multicheckbox', '_choices' => array('sidebar','topbar','playground:*','user:*','wiki:*'));/* exclude these pages or namespaces from mixture indexes (ie nsindex and subcards) for non admins */
$meta['extractToC']          = array('onoff'); /* move ToC out of main content as soon as there's enough room */
$meta['scrollspyToC']          = array('onoff'); /* enable scrollspy ToC (requires previous option to be enabled) */
$meta['extractSidebar']          = array('onoff'); /* move Sidebar out of main content as soon as there's enough room */
$meta['stickySidebar']          = array('onoff'); /* try to keep Sidebar always in viewport (buggy right now) */
$meta['wrappedSidebar']          = array('onoff'); /* wrap content around sidebar (potential side effects with syntax plugins, for exemple, does not work well with larg WRAP elements) */
