<?php
/**
 * Template Functions
 *
 * This file provides template specific custom functions that are
 * not provided by the DokuWiki core.
 * It is common practice to start each function with an underscore
 * to make sure it won't interfere with future core functions.
 */

// must be run from within DokuWiki
if (!defined('DOKU_INC')) die();

/**
 * copied to core (available since Detritus)
 */
if (!function_exists('tpl_toolsevent')) {
    function tpl_toolsevent($toolsname, $items, $view='main') {
        $data = array(
            'view'  => $view,
            'items' => $items
        );

        $hook = 'TEMPLATE_'.strtoupper($toolsname).'_DISPLAY';
        $evt = new Doku_Event($hook, $data);
        if($evt->advise_before()){
            foreach($evt->data['items'] as $k => $html) echo $html;
        }
        $evt->advise_after();
    }
}

/**
 * MIXTURE TEMPLATE FUNCTIONS
 *
 * @author Simon Delage <simon.geekitude@gmail.com>
 */

/**
 * INITALIZE
 * 
 * Load usefull informations and plugins' helpers.
 */
function php_mixture_init() {
    // DokuWiki core globals
    global $conf, $ID, $INFO, $JSINFO, $lang;
    // New global variables
    //global $mixture, $uhp, $trs, $translationHelper, $tags;
    global $mixture, $uhp, $trs, $editorAvatar, $userAvatar, $browserlang;

    // To use when we need to ignore `discussion` namespace
    $id = str_replace("discussion:", "", $ID);
    // DokuWiki core public page (depends on 'user' value in 'conf/interwiki.conf')
    $interwiki = getInterwiki();
    $mixturePublicId = ltrim(str_replace('{NAME}', $_SERVER['REMOTE_USER'], $interwiki['user']),':');

    // GET CURRENT VISITOR's BROWSER LANGUAGE
    $browserlang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

    // HELPER PLUGINS
    // Preparing usefull plugins' helpers
    // Userhomepage
    if (!empty($_SERVER['REMOTE_USER'])) {
        if (!plugin_isdisabled('userhomepage')) {
            $uhpHelper = plugin_load('helper','userhomepage');
            $uhp = $uhpHelper->getElements();
            if ((isset($mixturePublicId)) and (!isset($uhp['public']))) {
                $uhp['public'] = array();
                $uhp['public']['id'] = $mixturePublicId;
                if ((tpl_getLang('public_page') != null) and (!isset($uhp['public']['string']))) {
                    $uhp['public']['string'] = tpl_getLang('public_page');
                }
            }
        } else {
            // Without Userhomepage plugin, Public Page namespace is set by 'user' value in 'conf/interwiki.conf' and Private page is unknown
            $uhp = array();
            $uhp['private'] = null;
            $uhp['public'] = array();
            $uhp['public']['id'] = $mixturePublicId;
            $uhp['public']['string'] = tpl_getLang('public_page');
        }
    }

    // Translations
    $trs = array();
    if (!plugin_isdisabled('translation')) {
        $trs['defaultLang'] = $conf['lang'];
        if (isset($conf['lang_before_translation'])) {
            $trs['defaultLang'] = $conf['lang_before_translation'];
        }
        $translationHelper = plugin_load('helper','translation');
//dbg($translationHelper->showTranslations());
//dbg($conf['plugin']['translation']);
        if ($conf['plugin']['translation']['dropdown']) {
            $trs['dropdown'] = $translationHelper->showTranslations();
        }
        //$trs['helper'] = str_replace("cur", "pageId", $trs['helper']);
//        if (strpos($trs['helper'], 'form') !== false) {
//            //$tmp = explode("option", $trs['helper']);
//            //$tmp0: <div class="plugin_translation"><span>Traductions de cette page:</span> <form action="/doku.php" id="translation__dropdown"><select name="id" class="wikilink1"><
////dbg($tmp);
//        } else {
//        }
//dbg($trs['helper']);
        $trs['parts'] = $translationHelper->getTransParts($id);
//dbg($trs['parts']);        
        //$trs['lng'] = $translationHelper->getLangPart($ID);
        //$trs['translations'] = $translationHelper->getAvailableTranslations($ID);
//dbg($conf);
//dbg($conf['plugin']['translation']['translations']);
        if (isset($conf['plugin']['translation']['translations'])) {
            $languages = explode(" ", $conf['plugin']['translation']['translations']);
            sort($languages);
        } else {
            $languages = array();
        }
        if (!in_array($trs['defaultLang'], $languages)) {
            array_push($languages, $trs['defaultLang']);
        }
        sort($languages);
//dbg($languages);
        $trs['translations'] = array();
        foreach ($languages as $lc) {
            //if (strpos($conf['plugin']['translation']['translations'], $lc) !== false) {
                $translation = $translationHelper->buildTransID($lc, $trs['parts'][1]);
            //} else {
            //    $translation = $translationHelper->buildTransID("", $trs['parts'][1]);
            //}
//dbg($translation);
            $trs['translations'][$lc] = ltrim($translation[0], ":");
            if (page_exists($translation[0])) {
                $classes = "wikilink1";
            } else {
                $classes = "wikilink2";
            }
//dbg(tpl_link(wl($ID), $lc, 'class="'.$classes.'"', true));
            //$trs['links'][$lc] = tpl_link(wl($trs['translations'][$lc]), $lc, 'class="'.$classes.'"', true);
            //if ($lc == $trs['parts'][0]) {
//            if ($lc == $trs['parts'][0]) {
//                $classes .= " cur";
//                // ADDING TITLE WOULD BE NICE BUT THIS DOESN'T WORK
//                // $trs['links'][$lc] = tpl_link(wl($ID), $lc, 'class="'.$classes.'" title="'.$lang[$lc].'"', true);
//                $trs['links'][$lc] = tpl_link(wl($ID), $lc, 'class="'.$classes.'" title="'.$lang[$lc].'"', true);
//                //$trs['translations'][$lc] = ltrim($trs['translations'][$lc], ":");
////dbg($lc);
//            } else {
//dbg($trs['translations'][$lc]." vs ".$ID);
            if ($trs['translations'][$lc] != $ID) {
                //if ($lc == null) {
                //    $trs['links'][$conf['lang']] = $translationHelper->getTransItem($lc, $trs['parts'][1]);
                //} else {
                //    $trs['links'][$lc] = $translationHelper->getTransItem($lc, $trs['parts'][1]);
                //}
//                if (strpos($conf['plugin']['translation']['translations'], $lc) !== false) {
                    $trs['links'][$lc] = $translationHelper->getTransItem($lc, $trs['parts'][1]);
//                    //$trs['translations'][$lc] = ltrim($trs['translations'][$lc], ":");
////dbg("là!".$lc);
//                } else {
//                    $trs['links'][$lc] = $translationHelper->getTransItem("", $trs['parts'][1]);
//                    //$trs['translations'][$lc] = $trs['parts'][1];
////dbg("ici!".$lc);
//                }
            }
        }
            foreach ($trs['links'] as $lc => $link) {
                $trs['links'][$lc] = str_replace("<li><div class='li'>", "", $trs['links'][$lc]);
                $trs['links'][$lc] = str_replace("<li><div class='li cur'>", "", $trs['links'][$lc]);
                $trs['links'][$lc] = str_replace("</div></li>", "", $trs['links'][$lc]);
                $trs['links'][$lc] = str_replace("  ", " ", $trs['links'][$lc]);
//                if (strpos($link, "cur") !== false) {
//                    $trs['links'][$lc] = substr($trs['links'][$lc], 24);
//                    $trs['links'][$lc] = substr($trs['links'][$lc], 0, -11);
//                } elseif (strpos($link, "div") !== false) {
//                    $trs['links'][$lc] = substr($trs['links'][$lc], 20);
//                    $trs['links'][$lc] = substr($trs['links'][$lc], 0, -11);
//                }
            }
//        if (!in_array($conf['lang'], $languages)) {
////dbg("ici");
//            $trs['translations'][$conf['lang']] = $trs['parts'][1];
//            if (page_exists($trs['parts'][1])) {
//                $classes = "wikilink1";
//            } else {
//                $classes = "wikilink2";
//            }
//            if ($trs['parts'][1] == $ID) {
//                $classes .= " active";
//                unset($trs['translations'][$conf['lang']]);
//            }
//            //$trs['links'][$conf['lang']] = tpl_link(wl($trs['translations'][$conf['lang']]), $trs['parts'][1], 'class="'.$classes.'"', true);
//            //$trs['links'][$conf['lang']] = tpl_link(wl($trs['translations'][$conf['lang']]), $conf['lang'], 'class="'.$classes.'"', true);
//            $trs['links'][$conf['lang']] = $translationHelper->getTransItem($conf['lang'], $trs['parts'][1]);
//        } else {
//            $defaultLanguageLink = $trs['links'][$conf['lang']];
//            unset($trs['links'][$conf['lang']]);
//            $trs['links'][$conf['lang']] = $defaultLanguageLink;
//        }
//dbg($trs);
//dbg($conf['plugin']['translation']);
    }

    // CURRENT NS AND PATH
    // Get current namespace and corresponding path (resulting path will correspond to namespace's pages, media or conf files)
    $mixture['currentNs'] = getNS(cleanID($id));
    if ((isset($trs['parts'][1])) and ($trs['parts'][1] != null)) {
        if (strpos($conf['plugin']['translation']['translations'], $conf['lang']) !== false) {
            $mixture['baseNs'] = $conf['lang'].":".getNS(cleanID($trs['parts'][1]));
        } else {
            $mixture['baseNs'] = getNS(cleanID($trs['parts'][1]));
        }
    } else {
        $mixture['baseNs'] = $mixture['currentNs'];
    }
    if ($mixture['currentNs'] != null) {
        $mixture['currentPath'] = "/".str_replace(":", "/", $mixture['currentNs']);
    } else {
        $mixture['currentPath'] = "/";
    }

    // CURRENT NS AND PARENTS
    // Look for all start pages starting from current namespace up to wiki root
    $mixture['parents'] = php_mixture_file($conf['start'], "cumulate");
    if ($mixture['parents'] != null) {
        foreach ($mixture['parents'] as $key => $value) {
            $tmp = explode("pages/", $value);
            $tmpPath = explode(".txt", $tmp[1]);
            $tmpId = str_replace("/", ":", $tmpPath[0]);
            $mixture['parents'][$key] = $tmpId;
        }
        // Order start pages from current ns' one to furthest parent
        $mixture['parents'] = array_reverse($mixture['parents']);
        $mixture['parents'] = array_unique($mixture['parents']);
    }
//dbg($mixture['parents']);

//    // SUB NAMESPACES
//    // Look for all sub namespaces with a start page
//    //$mixture['children'] = php_mixture_file($conf['start'], "children");
//    $mixture['children'] = array();
//    // collect last change date of each?
//    // order by date?
//    $subnspaths = array_filter(glob(str_replace("//", "/", DOKU_CONF.'../'.$conf['savedir'].'/pages/'.$mixture['currentNs'].'/*')), 'is_dir');
//    //dbg($subnspaths);
//    foreach ($subnspaths as $value) {
//        $path_parts = pathinfo(explode("pages", $value)[1]);
//        // Keep going if we're not at wiki start page or if ns is not supposed to be excluded from main navigation
//        if (($ID != $conf['start']) or (strpos(tpl_getConf('navExclude'), $path_parts['basename']) === false)) {
//            // Build decent NS id of possible start page (no matter if it exists or not)
//            $nsId = str_replace("\\", "", str_replace("/", ":", $path_parts['dirname']).":".$path_parts['basename'].":".$conf['start']);
//            array_push($mixture['children'], $nsId);
//        }
//    }
//dbg($mixture['children']);

    // TREE (index from current NS)
// ADD TEST(S) ABOUT ELEMENTS REQUIRING TREE BEFORE COLLECTING IT
    $mixture['tree'] = php_mixture_tree($mixture['currentNs'].":");
//dbg($mixture['tree']);

    // LAST CHANGES (build list)
    // Retrieve number of last changes to show and proceed if matching `lastchangesWhere` settings
    if ((strpos(tpl_getConf('elements'), 'topbar_lastchanges') !== false) and ((tpl_getConf('lastChangesWhere') == "anywhere") or ((tpl_getConf('lastChangesWhere') == "any_start_page") and (strpos($ID, $conf['start']) !== false)) or ((tpl_getConf('lastChangesWhere') == "wiki_root") and ($ID == $conf['start'])))) {
        $showLastChanges = intval(end(explode(',', tpl_getConf('lastChanges'))));
        $flags = '';
        if (strpos(tpl_getConf('lastChanges'), 'skip_deleted') !== false) {
            $flags = RECENTS_SKIP_DELETED;
        }
        if (strpos(tpl_getConf('lastChanges'), 'skip_minors') !== false) {
            $flags += RECENTS_SKIP_MINORS;
        }
        if (strpos(tpl_getConf('lastChanges'), 'skip_subspaces') !== false) {
            $flags += RECENTS_SKIP_SUBSPACES;
        }
        if (tpl_getConf('lastChangesWhat') == 'media') {
            $flags += RECENTS_MEDIA_CHANGES;
        } elseif (tpl_getConf('lastChangesWhat') == 'both') {
            $flags += RECENTS_MEDIA_PAGES_MIXED;
        }
        $mixture['recents'] = getRecents(0,$showLastChanges,$mixture['currentNs'],$flags);
    }

    // TOPBAR LINKS
    if (strpos(tpl_getConf('elements'), 'topbar_links') !== false) {
        $topbarFiles = php_mixture_file(tpl_getConf('topbar'), tpl_getConf('topbarFrom'), "page", $mixture['baseNs']);
        if ($topbarFiles != null) {
            $prevValue = null;
            if (is_string($topbarFiles)) {
                $mixture['topbarLinks'] .= "\n".io_readFile($topbarFiles, false);
            } else {
                // Making sure each value in array is unique (so we don't process same topbar file twice)
                $topbarFiles = array_unique($topbarFiles);
                foreach ($topbarFiles as $value) {
                    //$mixture['topbarLinks'] .= "\n".io_readFile($value, false);
                    $mixture['topbarLinks'] .= io_readFile($value, false);
                }
            }
            // Use the built-in parser to render data as HTML
            $mixture['topbarLinks'] = p_render('xhtml',p_get_instructions($mixture['topbarLinks']), $info);
            //$mixture['topbarLinks'] = str_replace("<ul>", "<ul id='news-bar-links' class='dropdown-content'>", $mixture['topbarLinks']);
            $mixture['topbarLinks'] = str_replace("<ul>", "<ul class='dropdown-content'>", $mixture['topbarLinks']);
        }
    }

    // IMAGES
    // Search for namespace special images set as adaptive by settings (logo, banner, widebanner and potential last "sidebar header" image)
    if (strpos(tpl_getConf('elements'), 'header_logo') !== false) { $mixture['images']['logo'] = null; }
    if (strpos(tpl_getConf('elements'), 'header_banner') !== false) { $mixture['images']['banner'] = null; }
    if (strpos(tpl_getConf('elements'), 'widebanner') !== false) { $mixture['images']['widebanner'] = null; }
    if (strpos(tpl_getConf('elements'), 'sidebar_cover') !== false) { $mixture['images']['sidebar_cover'] = null; }
    if (count($mixture['images']) != null) {
        foreach ($mixture['images'] as $key => $value) {
        //if (strpos(tpl_getConf('namespaceImages'), $key) !== false) {
            $mixture['images'][$key] = php_mixture_file($key, "inherit", "media", $mixture['baseNs']);
        //}
        }
    }
//dbg($mixture['images']);
//    $lastImageTitle = end(explode(",", tpl_getConf('namespaceImages')));
//    // If 'namespaceImages' other image is set, get it
//    if (strpos("banner,logo,widebanner,cover", $lastImageTitle) === false) {
//        $mixture['images']['other'] = php_mixture_file($lastImageTitle, "namespace", "media", $mixture['baseNs']);
//        $mixture['images']['other']['label'] = ucfirst($lastImageTitle);
//    }

    // GLYPHS
    // Search for default or custum default SVG glyphs
    $mixture['glyphs']['calendar'] = null;
    $mixture['glyphs']['ellipsis'] = null;
    $mixture['glyphs']['home'] = null;
    $mixture['glyphs']['lastchanges'] = null;
    $mixture['glyphs']['link'] = null;
    $mixture['glyphs']['parent'] = null;
    $mixture['glyphs']['search'] = null;
    $mixture['glyphs']['trace'] = null;
    $mixture['glyphs']['translation'] = null;
    $mixture['glyphs']['userprivate'] = null;
    $mixture['glyphs']['userpublic'] = null;
    $mixture['glyphs']['youarehere'] = null;
    foreach ($mixture['glyphs'] as $key => $value) {
        if (is_file(DOKU_CONF."tpl/mixture/".$key.".svg")) {
            $mixture['glyphs'][$key] = file_get_contents(DOKU_CONF."tpl/mixture/".$key.".svg");
        } else {
            $mixture['glyphs'][$key] = file_get_contents(".".tpl_basedir()."svg/".$key.".svg");
        }
    }
//dbg($mixture['glyphs']);

    //if (strpos(tpl_getConf('elements'), 'header_logo') !== false) { $mixture['images']['logo'] = null; }
    //if (strpos(tpl_getConf('elements'), 'header_banner') !== false) { $mixture['images']['banner'] = null; }
    //if (strpos(tpl_getConf('elements'), 'widebanner') !== false) { $mixture['images']['widebanner'] = null; }
    //if (strpos(tpl_getConf('elements'), 'sidebar_cover') !== false) { $mixture['images']['sidebar_cover'] = null; }
    //if (count($mixture['images']) != null) {
    //    foreach ($mixture['images'] as $key => $value) {
    //    //if (strpos(tpl_getConf('namespaceImages'), $key) !== false) {
    //        $mixture['images'][$key] = php_mixture_file($key, "inherit", "media", $mixture['baseNs']);
    //    //}
    //    }
    //}

    // STYLE
    $mixture['replacements'] = array();
    $style = array();
    // Look for a customized 'style.ini' generated by Styling plugin
    if (is_file(DOKU_CONF."tpl/mixture/style.ini")) {
        $style = parse_ini_file(DOKU_CONF."tpl/mixture/style.ini", true);
    // Or load template's default 'style.ini'
    } else {
    //if (is_file(tpl_incdir()."style.ini")) {
        $style = parse_ini_file (tpl_incdir()."style.ini", true);
    }
    // Look for a "namspaced" customized 'style.ini' in current namespace's "conf" folder (and overwrite previous values)
    $nsStyleIni = php_mixture_file("style", "inherit", "conf", $namespaced['baseNs']);
    if (is_file($nsStyleIni)) {
        $nsStyle = parse_ini_file($nsStyleIni, true);
        foreach ($nsStyle['replacements'] as $key => $value) {
//            $namespaced['style']['replacements'][$key] = $value;
            $style['replacements'][$key] = $value;
        }
    }
    $mixture['replacements'] = $style['replacements'];

    // JSINFO
    // Add a value for connected user (false if none, true otherwise)
    if (empty($_SERVER['REMOTE_USER'])) {
        $JSINFO['user'] = false;
    } else {
        $JSINFO['user'] = true;
    }
    // Store options into $JSINFO for later use
    //$JSINFO['ScrollDelay'] = tpl_getConf('scrollDelay');
    if (strpos(tpl_getConf('elements'), 'lastchanges') !== false) {
        $JSINFO['LoadNewsTicker'] = true;
    } else {
        $JSINFO['LoadNewsTicker'] = false;
    }
    //$JSINFO['ScrollspyToc'] = tpl_getConf('scrollspyToc');
//dbg($JSINFO);

    // DEBUG
    // Adding test alerts if debug is enabled
    if ($_GET['debug'] == true) {
        msg("This is an error [-1] alert with a <a href='?doku.php'>dummy link</a>", -1);
        msg("This is an info [0] message with a <a href='?doku.php'>dummy link</a>", 0);
        msg("This is a success [1] message with a <a href='?doku.php'>dummy link</a>", 1);
        msg("This is a notification [2] with a <a href='?doku.php'>dummy link</a>", 2);
    }
}

/**
 * Stolen from AcMenu plugin ^^
 * Build the tree directory starting from current namespace to the
 * very end.
 *
 * @param (str) $base_ns the name of the namespace, where was found
 *              the AcMenu's syntax, of the form:
 *              <base_ns>:
 * @param (str) $level the level of indentation from which start
 * @return (arr) $tree the tree directory of the form:
 *              array {
 *              [(str) "<short_id>"] => array {
 *                     ["id"] => (str) "<id>"
 *                     ["type"] => (str) "ns"
 *                     ["sub"] => array {
 *                                [0] => array {
 *                                       ["id"] => (str) "<id>"
 *                                       ["type"] => (str) "pg"
 *                                       }
 *                                [i] => array {...}
 *                                }
 *                     }
 *              {...}
 *              }
 *              where:
 *              ["<short_id>"] is a NS' ID without start page at the end (ie ":sample_ns" instead of ":sample_ns:start"
 *              ["type"] = "ns" means "namespace"
 *              ["type"] = "pg" means "page"
 *              so that only namespace can have ["sub"] namespaces
 *
 * By default we want current NS content and 1 sub-level NS' content
 *
 */
function php_mixture_tree($base_ns, $level = -1, $max_level = 1) {
    global $INFO, $conf;

    $tree = array();
    $level = $level + 1;

    // Stop if reaching requested depth of index
    if ($level > $max_level) { return $tree; }

    //$dir = $conf["savedir"] ."/pages/" . str_replace(":", "/", $base_ns) . "/";
    $dir = $conf["savedir"] ."/pages/" . str_replace(":", "/", $base_ns);
    if (is_dir($dir)) {
        $files = array_diff(scandir($dir), array('..', '.'));
        foreach ($files as $file) {
            if (is_file($dir . $file) == true) {
                $namepage = basename($file, ".txt");
                $id = cleanID($base_ns . $namepage);
                if (isHiddenPage($id) == false) {
                    if (auth_quickaclcheck($id) >= AUTH_READ) {
                        //$title = p_get_first_heading($id);
                        //if (isset($title) == false) {
                        //    $title = $namepage;
                        //}
                        //$tree[] = array("title" => $title,
                        //                "url" => $id,
                        //                "level" => $level,
                        //                "type" => "pg");
                        $tree[] = array("id" => $id,
                                    "type" => "pg");
                    }
                }
            } elseif (is_dir($dir . $file) == true) {
                //$short_id = cleanID($base_ns . $file);
                //$id = $short_id . ":"  . $conf["start"];
                $id = cleanID($base_ns . $file) . ":"  . $conf["start"];
                if ($conf['sneaky_index'] == 1 and auth_quickaclcheck($id) < AUTH_READ) {
                    continue;
                } else {
                    //$title = p_get_first_heading($id);
                    //if (isset($title) == false) {
                    //    $title = $file;
                    //}
                    //$tree[$id] = array("title" => $title,
                    //                "url" => $id,
                    //                "level" => $level,
                    //                "type" => "ns",
                    //                "sub" => mixture_tree($base_ns . $file . ":", $level));
                    //$tree[$short_id] = array("id" => $id,
                    $tree[] = array("id" => $id,
                                "type" => "ns",
                                "sub" => php_mixture_tree($base_ns . $file . ":", $level));
                }
            }
        }
    }
    return $tree;
}

// Add Mixture specific classes to HTML body
function php_mixture_classes() {
    global $ACT, $mixture;

    $classes = " ";
    if (tpl_getConf("extractToC")) {
        $classes .= "extractToC ";
    }
    if (tpl_getConf("scrollspyToC")) {
        $classes .= "scrollspyToC ";
    }
    if (tpl_getConf("extractSidebar")) {
        $classes .= "extractSidebar ";
    }
    if (tpl_getConf("wrappedSidebar")) {
        $classes .= "wrappedSidebar ";
    } else {
        $classes .= "unwrappedSidebar ";
    }
    if ($_GET['debug'] == true) {
        $classes .= "debug ";
    }

    return rtrim($classes, " ");
}

function php_mixture_file($fileName, $where, $type = "page", $searchns = null, $returnId = false) {
    global $conf, $mixture;

    if ($searchns == null) {
        $searchns = $mixture['currentNs'];
    }
    $searchnspath = str_replace(":", "/", $searchns);

    if ($type == "conf") {
        $path = DOKU_CONF."tpl/mixture/".$searchnspath;
    } elseif ($type == "media") {
        $path = $conf['savedir']."/media/".$searchnspath;
    } else {
        $path = $conf['savedir']."/pages/".$searchnspath;
    }

    if ($where == 'namespace') {
        $ns = null;
        // Search in currentNS, untranslatedNS or both?
        // Only search untranslated namespace
        $ns = array('/'.$searchnspath);
    } elseif (($where == 'inherit') or ($where == 'cumulate')) {
        $ns = null;
        // List current namespace then all it's parents' up to last parent before root
        $tmp = explode(":", $searchns);
        for ($i=0; $i<count($tmp); $i++) {
            $ns[$i] = $ns[$i-1].'/'.$tmp[$i];
        }
        // Order namespaces from current one to furthest parent
        $ns = array_reverse($ns);
        // Add strings to force searching in media root and :wiki namespace
        array_push($ns, '/wiki');
        array_push($ns, '');
    } elseif ($where == 'root') {
        $ns = array('/');
    } else {
        return null;
    }

    // Prepare data array to return for the cases where we need all results (ie. topbar)
    $multiReturn = array();
    // Reverse $ns array order again to cumulate from root to current namespace
    if ($where == "cumulate") {
        $ns = array_reverse($ns);
    }
    // Search listed namespace(s) for jpg, gif and finally png image or txt page with requested filename
    foreach ($ns as $value) {
        // In case we are in a farm, we have to make sure we search in animal's data or conf dir by starting at DOKU_CONF directory (will however work if not in a farm)
        // If file extension is specified...
        if (count(explode('.', $fileName)) > 1) {
            if ($type == "media") {
                $result = glob(DOKU_CONF.'../'.$conf['savedir'].'/media'.$value.'/'.$fileName);
            } else {
                $result = glob(DOKU_CONF.'../'.$conf['savedir'].'/pages'.$value.'/'.$fileName);
            }
        } elseif ($type == "media") {
            $result = glob(DOKU_CONF.'../'.$conf['savedir'].'/media'.$value.'/'.$fileName.'.{jpg,gif,png}', GLOB_BRACE);
            //$result = glob($path.$fileName.'.{jpg,gif,png}', GLOB_BRACE);
            // If no result, let's try in template images
            if ($result == null) {
                $result = glob(DOKU_CONF.'../lib/tpl/mixture/images/'.$fileName.'.{jpg,gif,png}', GLOB_BRACE);
            }
        } elseif ($type == "conf") {
            $result = glob(DOKU_CONF.'tpl/mixture'.$value.'/'.$fileName.'.ini');
        } else {
            $result = glob(DOKU_CONF.'../'.$conf['savedir'].'/pages'.$value.'/'.$fileName.'.txt');
        }
        // If a result was found, we're looking for first match (IN MOST CASES), wich looks like "/var/www/dokufarm/<animal>/conf/../'.$conf['savedir'].'/<media or pages>/<namespace>/$fileName.<some_extension>
        if ($result[0] != null) {
            if ($type == "media") {
                $imageSize = getimagesize($result[0]);
            } else {
                $imageSize = null;
            }
            // Get rid of potential misformated string
            $result[0] = str_replace('//', '/', $result[0]);
            // If we want ALL results
            if ($where == "cumulate") {
                array_push($multiReturn, $result[0]);
            // If we're looking for a 'conf' file, let's return full file path
            } elseif ($type == "media") {
                $tmp = str_replace("/", ":", explode("media", $result[0])[1]);
                return array('mediaId' => $tmp, 'filePath' => $result[0], 'imageSize' => $imageSize);
            } else {
                if ($returnId) {
                    $path_parts = pathinfo(explode("pages", $result[0])[1]);
                    return str_replace("/", ":", $path_parts['dirname']).":".$path_parts['filename'];
                } else {
                    return $result[0];
                }
            }
        }
    }
    // if $multiReturn contains at least 1 element, return it
    if (count($multiReturn) > 0) {
        //dbg($multiReturn);
        return $multiReturn;
    }
}

/**
 * PREPARE UI LINKS DATA
 * 
 * Prepare data needed for UI links (logo, title, tagline or banner).
 *
 * @param string   $element UI element for wich the link is requested
 */
function php_mixture_ui_link($element) {
    global $conf, $ID;
    global $mixture;

//dbg($element);
//dbg($classes);
    //if ($element == "titleLink") {
    //    $classes = "color-primary";
    //} elseif ($element == "taglineLink") {
    //    $classes = "color-primary";
    //}
//dbg(tpl_getConf($element));
    if (($element != null) && (tpl_getConf($element) != "none")) {
        if (tpl_getConf($element) == "parent_namespace") {
            // if there's only one known parent we're on wiki start page and there's no need for a link
            if (count($mixture['parents']) == 1) {
                //return array('target' => wl($mixture['parents'][0]), 'label' => tpl_getLang('parent_namespace'));
                return null;
            // if there's 2 known parents first one is current ns start page and 2nd one is wiki home
            } elseif ((count($mixture['parents']) == 2) and ($mixture['parents'][1] == $conf['start'])) {
                return array('target' => wl($mixture['parents'][1]), 'label' => tpl_getLang('wikihome'), 'accesskey' => "h", 'classes' => $classes);
            // if there's more than 2 known parents first one is current ns start page and we want 2nd one (parent ns start page)
            } elseif (count($mixture['parents']) > 1) {
                return array('target' => wl($mixture['parents'][1]), 'label' => tpl_getLang('parentns'), 'classes' => $classes);
            } else {
                return array('target' => wl(), 'label' => tpl_getLang('wikihome'), 'accesskey' => "h", 'classes' => $classes);
            }
        } elseif (tpl_getConf($element) == "namespace_start") {
            // if there's at least one parent and current page isn't a start page we want a link to current ns start page
            if ((count($mixture['parents']) >= 1) && (strpos($ID, $conf['start']) === false)) {
//dbg($classes);
                return array('target' => wl($mixture['parents'][0]), 'label' => tpl_getLang('nshome'), 'classes' => $classes);
            //} elseif ((count($mixture['parents']) >= 1) && (strpos($ID, $conf['start']) !== false)) {
            //    return null;
            } else {
                //return array('target' => wl(), 'label' => tpl_getLang('wikihome'), 'accesskey' => "h");
                return null;
            }
        } elseif (tpl_getConf($element) == "dynamic") {
            // if we know more than one parent and current page isn't a start page we're on a random page and we want current NS start page
            if ((count($mixture['parents']) > 1) && (strpos($ID, $conf['start']) === false)) {
                return array('target' => wl($mixture['parents'][0]), 'label' => tpl_getLang('nshome'), 'classes' => $classes);
            // if we know 2 parents and current page is a start page, we want parent NS start page wich happens to be wiki home
//            } elseif ((count($mixture['parents']) == 2) && (strpos($ID, $conf['start']) !== false)) {
//                return array('target' => wl(), 'label' => tpl_getLang('wikihome'), 'accesskey' => "h", 'classes' => $classes);
            // if we know only one parent and current page isn't a start page we're on random page of wiki root and we want wiki start page OR we know 2 parents and current page is a start page, we want parent NS start page wich happens to be wiki home
            } elseif (((count($mixture['parents']) == 1) && (strpos($ID, $conf['start']) === false)) or ((count($mixture['parents']) == 2) && (strpos($ID, $conf['start']) !== false))) {
//            // if we know only one parent and current page isn't a start page we're on random page of wiki root and we want wiki start page
//            } elseif ((count($mixture['parents']) == 1) && (strpos($ID, $conf['start']) === false)) {
                return array('target' => wl(), 'label' => tpl_getLang('wikihome'), 'accesskey' => "h", 'classes' => $classes);
// WHAT ABOUT GOING TO LANDING FROM WIKI STAR PAGE? ACTUALLY LINKS TO WIKI START WHEN ALLREADY THERE
            // if we know at least one parent but current page is a start page, we want parent NS start page
            } elseif ((count($mixture['parents']) > 1) && (strpos($ID, $conf['start']) !== false)) {
                return array('target' => wl($mixture['parents'][1]), 'label' => tpl_getLang('parentns'), 'classes' => $classes);
            } else {
                return array('target' => wl(), 'label' => tpl_getLang('wikihome'), 'accesskey' => "h", 'classes' => $classes);
            }
        } elseif (tpl_getConf($element) == "image_namespace_start") {
//                                //$imageParent = _namespaced_imageParent($mixture['images'][tpl_getConf('sidebarImage')]['mediaId']);
////dbg($mixture['images'][tpl_getConf('sidebarImage')]['mediaId']);
//                                //$imageParent = _namespaced_file($conf['start'], "inherit", "page", $mixture['images'][tpl_getConf('sidebarImage')]['mediaId'], true);
            $imageParent = php_mixture_file($conf['start'], "namespace", "page", substr($mixture['images'][tpl_getConf('sidebarImage')]['mediaId'], 0, strrpos($mixture['images'][tpl_getConf('sidebarImage')]['mediaId'], ':')), true);
////dbg($imageParent);
//                                tpl_link(
//                                    wl($imageParent),
//                                    '<img src="'.ml($mixture['images'][tpl_getConf('sidebarImage')]['mediaId'],'',true).'" width="100%" height="auto" title="'.$imageParent.'" alt="*'.tpl_getConf('sidebarImage').'*" />'
//                                );
            if ($imageParent != ":".$ID) {
                return array('target' => wl($imageParent), 'label' => $imageParent);
            } else {
                return false;
            }
        } elseif (tpl_getConf($element) == "other") {
            if ((isset($mixture['images']['other']['mediaId'])) and ($mixture['images']['other']['mediaId'] != null)) {
//dbg($element);
                $classes = "hasOverlay";
                //return ml("ars5:sigrid:portrait.jpg",'',false);
                return array('target' => ml($mixture['images']['other']['mediaId'],'',true), 'label' => $mixture['images']['other']['label'], 'classes' => $classes);
            } else {
                return null;
            }
            //} elseif (($element == "sidebarImageLink") and (tpl_getConf("sidebarImageLink") == "other") and (isset($mixture['images']['other']['mediaId'])) and ($mixture['images']['other']['mediaId'] != null)) {
            //    $classes = "hasOverlay";
            //    return array('target' => ml($mixture['images']['other']['mediaId'],'',true), 'label' => $mixture['images']['other']['label'], 'classes' => $classes);
            //} elseif (tpl_getConf($element) == "image_namespace_start") {
//dbg("bingo");
//dbg($element);
//dbg(tpl_getConf($element));
//            $imageParent = _namespaced_imageParent($mixture['images'][tpl_getConf($element)]['mediaId']);
//dbg($imageParent);
                            //if ($imageParent != null) {
                            //    tpl_link(
                            //        wl($imageParent),
                            //        '<img id="sidebarImage" src="'.$sidebarImage.'" width="100%" height="auto" title="'.$imageParent.'" alt="*'.tpl_getConf('sidebarImage').'*" />'
                            //    );

        } else {
//dbg(tpl_getConf($element));
            return array('target' => wl(), 'label' => tpl_getLang('wikihome'), 'accesskey' => "h", 'classes' => $classes);
        }
    } else {
        return null;
    }
}

/**
 * RETURN WIKI OR PAGE TITLE AND TAGLINE OR WIKI TITLE
 * 
 * @param string   $element UI element for wich the string is requested
 */
function php_mixture_branding($element) {
    global $ID, $conf, $ACT;

    if ((tpl_getConf('dynamicBranding') == true) && ($ID != $conf['start']) && (($ACT == "show") or ($ACT == "edit") or ($ACT == "preview"))) {
        if ($element == "title") {
//dbg(tpl_pagetitle('', false));
//dbg(tpl_pagetitle('', true));
//dbg(tpl_pagetitle($ID, false));
//dbg(tpl_pagetitle($ID, true));
            return php_mixture_pagetitle($ID);
        } elseif ($element == "tagline") {
            return $conf['title'];
        } else {
            return false;
        }
    } else {
        if ($element == "title") {
            return $conf['title'];
        } elseif ($element == "tagline") {
            return $conf['tagline'];
        } else {
            return false;
        }
    }
}


/**
 * Returns the name of the given page (current one if none given).
 *
 * If useheading is enabled this will use the first headline else
 * the given ID is used.
 *
 * @param string $id page id
 */
function php_mixture_pagetitle($target = null, $context = null) {
    //global $ACT, $INPUT, $conf, $lang;
    global $trs, $conf;

    // By default, page name will be equal to it's ID
    $name = $target;

    // If `useheading` DW's setting is enabled for navigation links, try to get that first heading
    if(useHeading('navigation')) {
        $first_heading = p_get_first_heading($target);
        if($first_heading) $name = $first_heading;
    }

    /* Get rid of ugly DW IDs (should work for pages without `useheading` as well as NS start pages)
     * Code taken here : https://www.dokuwiki.org/tips:underscores
     */
    if (strstr($name, ':') == '') {
        $name = utf8_ucfirst(strtr($name,'_',' '));
    } else {
        if (substr(strrchr($name, ':'), 1 ) == $conf['start']) {
            $name = substr($name, 0, strlen($name) - strlen($conf['start']) - 1);
            if (strstr($name, ':') == '') {
                $name = utf8_ucfirst(strtr($name,'_',' '));
            } else {
                $name = utf8_ucfirst(substr(strrchr(strtr($name,'_',' '), ':'), 1 ));
            }
        } else {
            $name = utf8_ucfirst(substr(strrchr(strtr($name,'_',' '), ':'), 1 ));
        }
    }

    // CROISSANT PLUGIN
    if ((($context == "breadcrumbs") || ($context == "lastchanges") || ($context == "pagenav")) && (p_get_metadata($target, 'plugin_croissant_bctitle') != null)) {
      $name = p_get_metadata($target, 'plugin_croissant_bctitle');
    }

    return hsc($name);
}

/**
 * PRINT A DATE
 * 
 * @param string    $type "long" for long date based on 'dateString' setting, "short" for numeric
 * @param integer   $timestamp timestamp to use (null for current server time)
 * @param bool      $clock if true, add hour to the result
 * @param bool      $print if true, print the result instead of returning it
 */
function php_mixture_date($type, $timestamp = null, $clock = false, $printResult = false) {
    if (tpl_getConf('dateLocale') != null) {
        setlocale(LC_TIME, explode(",", tpl_getConf('dateLocale')));
    }
    $format = tpl_getConf('dateString');
    if ($clock) {
        $format .= ' %H:%M';
    }
    if ($timestamp == null) {
        $result = utf8_encode(ucwords(strftime($format)));
    } else {
        $result = utf8_encode(ucwords(strftime($format, $timestamp)));
    }
    if ($printResult) {
        print $result;
        return true;
    } else {
        return $result;
    }
}

/**
 * PRINT LAST CHANGES LIST
 * 
 * Print an <ul> loaded with @param last changes.
 *
 * @param integer   $n number of last changes to show in the list
 */
function php_mixture_lastchanges($context = null) {
    global $mixture, $conf, $lang;

    $mediaPath = str_replace("/pages", "/media", $conf['datadir']);
    $i = 0;
    foreach ($mixture['recents'] as $key => $value) {
        $details = null;
        if ($value['sum'] != null) {
            $details = ucfirst(rtrim($value['sum'], "."));
        } else {
            $details = ucfirst(rtrim(str_replace(":", "", $lang['mail_changed']), chr(0xC2).chr(0xA0)));
        }
        if ($value['date'] != null) {
            $details .= " (".php_mixture_date("long", $value['date']).")";
        }
        if ($context == "landing") {
            $details .= ".";
        }
        //print '<li title="'.$value['id'].'">';
        print '<li title="'.$details.'">';
            if ($value['media']) {
                if (is_file($mediaPath."/".str_replace(":", "/", $value['id']))) {
                    $exist = "wikilink1";
                } else {
                    $exist = "wikilink2";
                }
            } else {
                if (page_exists($value['id'])) {
                    $exist = "wikilink1";
                } else {
                    $exist = "wikilink2";
                }
            }
            $pageName = php_mixture_pagetitle($value['id'], "lastchanges");
            if ($value['media']) {
                tpl_link(
                    ml($value['id'],'',false),
                    $pageName,
                    'class="'.$exist.' medialink"'
                );
            } else {
                tpl_link(
                    wl($value['id']),
                    $pageName,
                    'class="'.$exist.'"'
                );
            }
            $by = null;
            if ($value['user'] != null) {
                $by = " ".$lang['by']." ";
            }
            if ($context == null) {
                //print '<span class="display-none xs-display-initial md-display-none wd-display-initial">'.$by.'<span class="text-capitalize"><bdi>'.$value['user'].'</bdi></span></span>';
                //print '<span class="display-none xs-display-initial">'.$by.'<span class="camelcase"><bdi>'.$value['user'].'</bdi></span></span>';
                print '<span>'.$by.'<span class="camelcase"><bdi>'.$value['user'].'</bdi></span></span>';
            }
            $i++;
        print '</li>';
    }
}

/**
 * PRINT THE BREADCRUMBS TRACE, adapted from core (template.php) to use a CSS separator solution and respect existing/non-existing page link colors
 *
 * @return bool
 */
function php_mixture_breadcrumbs() {
    global $lang, $conf, $uhp, $ID, $mixture;

    //check if enabled
    if(!$conf['breadcrumbs']) return false;

    $crumbs = breadcrumbs(); //setup crumb trace
//dbg($crumbs);
    // Make sure current page crumb is last in list (this also occurs with 'dokuwiki' template so it seems to be a core code minor bug)
    // COULD BE FIXED WITH FOLLOWING LINE BUT THIS BREAKS TWISTIENAV AS IT IS BASED ON CORE BREADCRUMBS()
    //$value = $crumbs[$ID];
    //unset($crumbs[$ID]);
    //$crumbs = array_merge($crumbs); 
    //$crumbs[$ID] = $value;
//dbg($crumbs);


    if (count($crumbs) > 0) {
        //render crumbs, highlight the last one
        print '<ul>';
//        if (tpl_getConf('breadcrumbsStyle') == "classic") {
            print '<li><span class="small-hidden medium-hidden large-hidden glyph-20 label" title="'.rtrim($lang['breadcrumb'], ':').'">'.$mixture['glyphs']['trace'].'</span><span class="tiny-hidden label">'.$lang['breadcrumb'].'</span></li>';
//        }
        $last = count($crumbs);
        $i    = 0;
        foreach($crumbs as $target => $name) {
            $i++;
            print '<li>';
              //if (page_exists($target)) {
              //  $class = "wikilink1";
              //} else {
              //  $class = "wikilink2";
              //}
              if (count(explode(":",$target)) == 1) { $target = ":".$target; }
              //if (p_get_metadata($target, 'plugin_croissant_bctitle') != null) {
              //  tpl_pagelink($target, p_get_metadata($target, 'plugin_croissant_bctitle'));
              //} else {
              //  tpl_pagelink($target);
              //}
              php_mixture_icon($target);
              tpl_pagelink(":".$target, php_mixture_pagetitle($target, "breadcrumbs"));
            print '</li>';
        }
        echo "</ul>";
        return true;
    } else {
        return false;
    }
}

/**
 * PRINT HIERARCHICAL BREADCRUMBS, adapted from core (template.php) to use a CSS separator solution and respect existing/non-existing page link colors
 *
 * This code was suggested as replacement for the usual breadcrumbs.
 * It only makes sense with a deep site structure.
 *
 * @return bool
 */
function php_mixture_youarehere() {
    global $conf, $ID, $lang, $trs, $mixture;

    // check if enabled
    if(!$conf['youarehere']) return false;

    $parts = explode(':', $ID);
    $count = count($parts);

    print '<ul>';
//    if (tpl_getConf('breadcrumbsStyle') == "classic") {
//        print '<li><span class="glyph-16 label" title="'.rtrim($lang['youarehere'], ':').'">'.$mixture['glyphs']['location'].$mixture['glyphs']['map'].'</span><span class="tiny-hidden">'.$lang['youarehere'].'</span></li>';
        print '<li><span class="small-hidden medium-hidden large-hidden glyph-20 label" title="'.rtrim($lang['youarehere'], ':').'">'.$mixture['glyphs']['youarehere'].'</span><span class="tiny-hidden label">'.$lang['youarehere'].'</span></li>';
//    }
    // print the startpage unless we're in translated namespace (in wich case trace will start with current language start page)
    //if ((isset($trs['parts'][0])) and (isset($trs['defaultLang'])) and ($trs['parts'][0] == $trs['defaultLang'])) {
    // this was a test to also enable adding untranslated start page before translated start page but this is not very logic and dosn't work at all since DW transforms link into one leading to translated ns
    //if (((isset($trs['parts'][0])) and (isset($trs['defaultLang'])) and ($trs['parts'][0] == $trs['defaultLang'])) or ((!plugin_isdisabled('translation')) and (isset($trs['defaultLang'])) and (strpos($conf['plugin']['translation']['translations'], $trs['defaultLang']) === false)) or (plugin_isdisabled('translation'))) {
    //if (((isset($trs['parts'][0])) and (isset($trs['defaultLang'])) and ($trs['parts'][0] == $trs['defaultLang'])) or  (plugin_isdisabled('translation'))) {
    //if ((isset($trs['parts'][0])) and (strpos($conf['plugin']['translation']['translations'], $trs['defaultLang']) === false)) {
    if (((isset($trs['parts'][0])) && ((strlen($trs['parts'][0]) == 0) || ($trs['parts'][0] == $trs['defaultLang']))) || (plugin_isdisabled('translation'))) {
        print '<li>';
            php_mixture_icon($conf['start']);
            tpl_pagelink(":".$conf['start'], php_mixture_pagetitle($conf['start'], "breadcrumbs"));
        print '</li>';
    }
    // print intermediate namespace links
    $part = '';
    for($i = 0; $i < $count - 1; $i++) {
        $part .= $parts[$i].':';
        $page = $part;
        if (substr($page, -1) == ":") { $page .= $conf['start']; }
        //if($page == $conf['start']) continue; // Skip startpage
        // skip if current target leads to untranslated wiki start
//        if ((isset($trs['defaultLang'])) and ($page != $trs['defaultLang'].":")) {
            print '<li>';
            //if (p_get_metadata($page.$conf['start'], 'plugin_croissant_bctitle') != null) {
            //    tpl_pagelink($page, p_get_metadata($page.$conf['start'], 'plugin_croissant_bctitle'));
            //} else {
            //    tpl_pagelink($page);
            //}
            php_mixture_icon($page);
            tpl_pagelink(":".$page, php_mixture_pagetitle($page, "breadcrumbs"));
            //dbg($page);
            echo "</li>";
//        }
    }

    // print current page, skipping start page, skipping for namespace index
    resolve_pageid('', $page, $exists);
    if(isset($page) && $page == $part.$parts[$i]) {
        echo "</ul>";
        return true;
    }
    $page = $part.$parts[$i];
    if ($page == $conf['start']) {
        echo "</ul>";
        return true;
    }
    print '<li>';
        php_mixture_icon($page);
        //if (p_get_metadata($page, 'plugin_croissant_bctitle') != null) {
        //    tpl_pagelink(":".$page, p_get_metadata($page, 'plugin_croissant_bctitle'));
        //} else {
        //    tpl_pagelink(":".$page);
        //}
        tpl_pagelink(":".$page, php_mixture_pagetitle($page, "breadcrumbs"));
    echo "</li>";
    echo "</ul>";
    return true;
}

/**
 * PRINT OR RETURN A GLYPH
 * 
 * @param string    $target : a page id or action
 * @param string    $context : nav/breadcrumbs/...
 * @param string    $target : action/page
 * @param bool      $print return result if true, print it if flase
 */
function php_mixture_icon($target = null, $context = "breadcrumbs", $what = "page", $return = false) {
    global $mixture, $trs, $conf;

    if ($what == "page") {
      $tmp = explode(":", ltrim($target, ":"));
      if ($context == "breadcrumbs") {
        // Add glyph before user's public page
        if ((count($tmp) == 2) && (($tmp[0] == "user") or ($tmp[0] == $conf['plugin']['userhomepage']['public_pages_ns']))) {
          //dbg("ici?".$name);
          $icon =  '<span class="glyph-18" title="'.tpl_getLang('publicpage').'">'.$mixture['glyphs']['userpublic'].'</span>';
        // Add glyph before user's private namespace
        } elseif ((count($tmp) == 3) && (($tmp[0] == "user") or ($tmp[0] == $conf['plugin']['userhomepage']['public_pages_ns'])) && ($tmp[2] == $conf['start'])) {
          //dbg("ici?".$name);
          $icon =  '<span class="glyph-18" title="'.tpl_getLang('privatens').'">'.$mixture['glyphs']['userprivate'].'</span>';
        // Add a flag SVG image before translations
        } elseif ((strlen($tmp[0]) == 2) && ($tmp[0] != $trs['defaultLang']) && (strpos($conf['plugin']['translation']['translations'], $tmp[0]) !== false)) {
          //dbg("ici?".$name);
          //$name = "<".$tmp[1].">".$name;
          $icon =  '<span class="glyph-18" title="<'.$tmp[0].'>">'.$mixture['glyphs']['translation'].'</span>';
        // Add a house SVG image before home
        } elseif (ltrim($target, ":") == $conf['start']) {
          //dbg("sob?".$name.tpl_getLang('wikihome'));
          $icon =  '<span class="glyph-18" title="'.tpl_getLang('wikihome').'">'.$mixture['glyphs']['home'].'</span>';
        }
      } else {
        //dbg("là?".$name);
      }
    } else {
      //dbg("grr?".$name);
    }

    if ($return == true) {
      return $icon;
    } else {
      print $icon;
    }
}

/**
 * PAGE NAV
 * 
 * Print page nav elements
 */
function php_mixture_pagenav() {
    global $trs, $mixture, $ID, $conf, $INFO;

    $exclusions = "";
    if (!$INFO['isadmin']) {
        if (strpos(tpl_getConf('exclusions'), 'sidebar') !== false) {
            $exclusions .= $conf['sidebar'];
        }
        if (strpos(tpl_getConf('exclusions'), 'topbar') !== false) {
            $exclusions .= tpl_getconf('topbar');
        }
    }
    // List other pages in same namespace
    foreach($mixture['tree'] as $key => $value) {
        if (($value['type'] == "pg") && ($value['id'] != $ID) && (strpos($exclusions, end(explode(":", $value['id']))) === false)) {
            if (page_exists($value['id'])) {
                $classes = "wikilink1";
            } else {
                $classes = "wikilink2";
            }
            $pagename = php_mixture_pagetitle($value['id'], "pagenav");
            print "<li class='tab'>".tpl_link(wl($value['id']), $pagename, 'class="'.$classes.'" title="'.$value['id'].'"', true)."</li>";
        }
    }
}
