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
    global $conf, $ID, $INFO, $auth, $JSINFO, $lang;
    // New global variables
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
    // Avatar
    if ((!plugin_isdisabled('avatar')) && (tpl_getConf('avatar') == "avatar-plugin")) {
        $avatarHelper = plugin_load('helper','avatar');
    }
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
        if ($conf['plugin']['translation']['dropdown']) {
            $trs['dropdown'] = $translationHelper->showTranslations();
        }
        $trs['parts'] = $translationHelper->getTransParts($id);
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
        $trs['translations'] = array();
        foreach ($languages as $lc) {
            $translation = $translationHelper->buildTransID($lc, $trs['parts'][1]);
            $trs['translations'][$lc] = ltrim($translation[0], ":");
            if (page_exists($translation[0])) {
                $classes = "wikilink1";
            } else {
                $classes = "wikilink2";
            }
            if ($trs['translations'][$lc] != $ID) {
                $trs['links'][$lc] = $translationHelper->getTransItem($lc, $trs['parts'][1]);
            }
        }
        foreach ($trs['links'] as $lc => $link) {
            $trs['links'][$lc] = str_replace("<li><div class='li'>", "", $trs['links'][$lc]);
            $trs['links'][$lc] = str_replace("<li><div class='li cur'>", "", $trs['links'][$lc]);
            $trs['links'][$lc] = str_replace("</div></li>", "", $trs['links'][$lc]);
            $trs['links'][$lc] = str_replace("  ", " ", $trs['links'][$lc]);
        }
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
    // Search for namespace special images depending on settings (logo, banner, widebanner and potential last "sidebar header" image)
    if (tpl_getConf('logoImg') != null) {
        $mixture['images']['logo'] = php_mixture_file(tpl_getConf('logoImg'), tpl_getConf('imagesFrom'), "media", $mixture['baseNs']);
    }
    if (tpl_getConf('bannerImg') != null) {
        $mixture['images']['banner'] = php_mixture_file(tpl_getConf('bannerImg'), tpl_getConf('imagesFrom'), "media", $mixture['baseNs']);
    }
    if (tpl_getConf('widebannerImg') != null) {
        $mixture['images']['widebanner'] = php_mixture_file(tpl_getConf('widebannerImg'), tpl_getConf('imagesFrom'), "media", $mixture['baseNs']);
    }
    if (tpl_getConf('sidebarImg') != null) {
        $mixture['images']['sidebar'] = php_mixture_file(tpl_getConf('sidebarImg'), tpl_getConf('imagesFrom'), "media", $mixture['baseNs']);
    }
    if (tpl_getConf('avatar') != "none") {
        if ($_SERVER['REMOTE_USER'] != NULL) {
            $user = array();
            $user['login'] = $_SERVER['REMOTE_USER']; // current user's login
            $user['fullname'] = $INFO['userinfo']['name']; // current user's fullname
            $user['mail'] = $INFO['userinfo']['mail']; // current user's mail
            // Firstly try to get a local avatar
            $mixture['images']['userAvatar'] = php_mixture_file($user['login'], "namespace", "media", tpl_getConf('avatarNs'));
            $mixture['images']['userAvatar']['img'] = '<span id="mixture__user_avatar" title="'.$user['fullname'].'"><img src="'.ml($mixture['images']['userAvatar']['mediaId'],'',true).'" alt="*'.$user['fullname'].'*" width="32" height="32" /></span>';
            // Keep going if we didn't get a local avatar
            if ($mixture['images']['userAvatar']['mediaId'] == null) {
                // ... then try to get an image from Avatar plugin if we didn't get a local avatar and if it's required by setting and avatar plugin's helper has been loaded
                if ((tpl_getConf("avatar") == "avatar-plugin") && ($avatarHelper)) {
                    $mixture['images']['userAvatar']['img'] = '<span id="mixture__user_avatar">'.$avatarHelper->getXHTML($user['mail'], $user['fullname'], 'center', 32).'</span>';
                    // adding a border to JPEG images (`png` and `gif` images most likely have a transparent background and shouldn't need a border to fit)
                    if (strpos($mixture['images']['userAvatar']['img'], '.jpg') !== false) {
                        //$mixture['images']['userAvatar']['img'] = str_replace("mediacenter photo fn", "mediacenter borders", $mixture['images']['userAvatar']['img']);
                        $mixture['images']['userAvatar']['img'] = str_replace("mediacenter photo fn", "mediacenter", $mixture['images']['userAvatar']['img']);
                    }
                // ... then try a jdenticon
                } else {
                    $mixture['images']['userAvatar']['svg'] = '<span id="mixture__user_avatar" title="'.$user['fullname'].'"><svg width="32" height="32" data-jdenticon-hash="'.hash('md5', $user['mail']).'" alt="*'.$user['fullname'].'*" class="mediacenter"></svg>';
                }
            }
        }
        if ($INFO['editor'] != NULL) {
            if ($auth) {
                $editorAuthInfo = $auth->getUserData($INFO['editor']);
                $editor = array();
                $editor['login'] = $INFO['editor']; // current page's editor's login
                $editor['fullname'] = $editorAuthInfo['name']; // current page's editor's full name
                $editor['mail'] = $editorAuthInfo['mail']; // current page's editor's mail
                // Firstly try to get a local avatar
                $mixture['images']['editorAvatar'] = php_mixture_file($editor['login'], "namespace", "media", tpl_getConf('avatarNs'));
                $mixture['images']['editorAvatar']['img'] = '<span id="mixture__editor_avatar" title="'.$editor['fullname'].'"><img src="'.ml($mixture['images']['editorAvatar']['mediaId'],'',true).'" alt="*'.$editor['fullname'].'*" width="32" height="32" /></span>';
                // Keep going if we didn't get a local avatar
                if ($mixture['images']['editorAvatar']['mediaId'] == null) {
                    // ... then try to get an image from Avatar plugin if we didn't get a local avatar and if it's required by setting and avatar plugin's helper has been loaded
                    if ((tpl_getConf("avatar") == "avatar-plugin") && ($avatarHelper)) {
                        $mixture['images']['editorAvatar']['img'] = '<span id="mixture__editor_avatar">'.$avatarHelper->getXHTML($editor['mail'], $editor['fullname'], 'center', 32).'</span>';
                        // adding a border to JPEG images (`png` and `gif` images most likely have a transparent background and shouldn't need a border to fit)
                        if (strpos($mixture['images']['editorAvatar']['img'], '.jpg') !== false) {
                            //$mixture['images']['editorAvatar']['img'] = str_replace("mediacenter photo fn", "mediacenter borders", $mixture['images']['editorAvatar']['img']);
                            $mixture['images']['editorAvatar']['img'] = str_replace("mediacenter photo fn", "mediacenter", $mixture['images']['editorAvatar']['img']);
                        }
                    // ... then try a jdenticon
                    } else {
                        $mixture['images']['editorAvatar']['svg'] = '<span id="mixture__editor_avatar" title="'.$editor['fullname'].'"><svg width="32" height="32" data-jdenticon-hash="'.hash('md5', $editor['mail']).'" alt="*'.$editor['fullname'].'*" class="mediacenter"></svg>';
                    }
                }
            }
        }
    }

    // GLYPHS
    // Search for default or custum default SVG glyphs
    $mixture['glyphs']['acl'] = null;
    $mixture['glyphs']['admin'] = null;
    $mixture['glyphs']['calendar'] = null;
    $mixture['glyphs']['config'] = null;
    $mixture['glyphs']['discussion'] = null;
    $mixture['glyphs']['ellipsis'] = null;
    $mixture['glyphs']['extension'] = null;
    $mixture['glyphs']['home'] = null;
    $mixture['glyphs']['index'] = null;
    $mixture['glyphs']['link'] = null;
    $mixture['glyphs']['login'] = null;
    $mixture['glyphs']['logout'] = null;
    $mixture['glyphs']['media'] = null;
    $mixture['glyphs']['parent'] = null;
    $mixture['glyphs']['popularity'] = null;
    $mixture['glyphs']['profile'] = null;
    $mixture['glyphs']['recent'] = null;
    $mixture['glyphs']['refresh'] = null;
    $mixture['glyphs']['register'] = null;
    $mixture['glyphs']['revert'] = null;
    $mixture['glyphs']['search'] = null;
    $mixture['glyphs']['styling'] = null;
    $mixture['glyphs']['trace'] = null;
    $mixture['glyphs']['translation'] = null;
    $mixture['glyphs']['upgrade'] = null;
    $mixture['glyphs']['userprivate'] = null;
    $mixture['glyphs']['users'] = null;
    $mixture['glyphs']['youarehere'] = null;
    foreach ($mixture['glyphs'] as $key => $value) {
        if (is_file(DOKU_CONF."tpl/mixture/".$key.".svg")) {
            $mixture['glyphs'][$key] = file_get_contents(DOKU_CONF."tpl/mixture/".$key.".svg");
        } else {
            $mixture['glyphs'][$key] = file_get_contents(".".tpl_basedir()."svg/".$key.".svg");
        }
    }

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
    if (tpl_getConf('avatar') != "none") {
        $JSINFO['LoadJdenticon'] = true;
    } else {
        $JSINFO['LoadJdenticon'] = false;
    }
    //$JSINFO['ScrollspyToc'] = tpl_getConf('scrollspyToc');

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

    $dir = $conf["savedir"] ."/pages/" . str_replace(":", "/", $base_ns);
    if (is_dir($dir)) {
        $files = array_diff(scandir($dir), array('..', '.'));
        foreach ($files as $file) {
            if (is_file($dir . $file) == true) {
                $namepage = basename($file, ".txt");
                $id = cleanID($base_ns . $namepage);
                if (isHiddenPage($id) == false) {
                    if (auth_quickaclcheck($id) >= AUTH_READ) {
                        $tree[] = array("id" => $id,
                                    "type" => "pg");
                    }
                }
            } elseif (is_dir($dir . $file) == true) {
                $id = cleanID($base_ns . $file) . ":"  . $conf["start"];
                if ($conf['sneaky_index'] == 1 and auth_quickaclcheck($id) < AUTH_READ) {
                    continue;
                } else {
                    $tree[] = array("id" => $id,
                        "type" => "ns",
                        "sub" => php_mixture_tree($base_ns . $file . ":", $level
                    ));
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
    if ($mixture['images']['widebanner'] != null) {
        $classes .= "hasWidebanner ";
    } else {
        $classes .= "noWidebanner ";
    }
    if ($_GET['debug'] == true) {
        $classes .= "debug ";
    }

    return rtrim($classes, " ");
}

//php_mixture_file(tpl_getConf('banner'), tpl_getConf('imagesFrom'), "media", $mixture['baseNs']);
function php_mixture_file($fileName, $where, $type = "page", $searchns = null, $returnId = false) {
//dbg($fileName." + ".$where." + ".$type." + ".$searchns." + ".$returnId);
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
    // if we were looking for an image, let's try default Mixture images
    } elseif ($type == "media") {
        $result = glob(DOKU_CONF.'../lib/tpl/mixture/images/'.$fileName.'.{jpg,gif,png}', GLOB_BRACE);
        if ($result[0] != null) {
            $imageSize = getimagesize($result[0]);
            return array('mediaId' => null, 'filePath' => $result[0], 'imageSize' => $imageSize);
        }
    }
}

/**
 * PREPARE UI LINKS
 * 
 * Prepare data needed for UI links (logo, title, tagline, banner or tools).
 *
 * @param string   $element UI element for wich the link is requested
 */
function php_mixture_ui_link($element, $basens = null) {
    global $conf, $ID;
    global $mixture;

    
    if (($element != null) && (tpl_getConf($element) != "none")) {
        if (tpl_getConf($element) == "parent_namespace") {
            // if there's only one known parent we're on wiki start page and there's no need for a link to a parent
            if (count($mixture['parents']) == 1) {
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
                return array('target' => wl($mixture['parents'][0]), 'label' => tpl_getLang('nshome'), 'classes' => $classes);
            } elseif ($basens != null) {
                $target = ltrim(php_mixture_file($conf['start'],"inherit","page",$basens,true), ":");
                return array('target' => wl($target), 'label' => $target, 'classes' => "");
            } else {
                return null;
            }
        } elseif (tpl_getConf($element) == "dynamic") {
            // if we know more than one parent and current page isn't a start page we're on a random page and we want current NS start page
            if ((count($mixture['parents']) > 1) && (strpos($ID, $conf['start']) === false)) {
                return array('target' => wl($mixture['parents'][0]), 'label' => tpl_getLang('nshome'), 'classes' => $classes);
            // if we know only one parent and current page isn't a start page we're on random page of wiki root and we want wiki start page OR we know 2 parents and current page is a start page, we want parent NS start page wich happens to be wiki home
            } elseif (((count($mixture['parents']) == 1) && (strpos($ID, $conf['start']) === false)) or ((count($mixture['parents']) == 2) && (strpos($ID, $conf['start']) !== false))) {
                return array('target' => wl(), 'label' => tpl_getLang('wikihome'), 'accesskey' => "h", 'classes' => $classes);
            // if we know at least one parent and current page is not a start page, we want parent NS start page
            } elseif ((count($mixture['parents']) > 1) && (strpos($ID, $conf['start']) !== false)) {
                return array('target' => wl($mixture['parents'][1]), 'label' => tpl_getLang('parentns'), 'classes' => $classes);
            } else {
                return array('target' => wl(), 'label' => tpl_getLang('wikihome'), 'accesskey' => "h", 'classes' => $classes);
            }
        } elseif (tpl_getConf($element) == "image_namespace_start") {
            $imageParent = php_mixture_file($conf['start'], "namespace", "page", substr($mixture['images'][tpl_getConf('sidebarImage')]['mediaId'], 0, strrpos($mixture['images'][tpl_getConf('sidebarImage')]['mediaId'], ':')), true);
            if ($imageParent != ":".$ID) {
                return array('target' => wl($imageParent), 'label' => $imageParent);
            } else {
                return false;
            }
        } elseif (tpl_getConf($element) == "other") {
            if ((isset($mixture['images']['other']['mediaId'])) and ($mixture['images']['other']['mediaId'] != null)) {
                $classes = "hasOverlay";
                //return ml("ars5:sigrid:portrait.jpg",'',false);
                return array('target' => ml($mixture['images']['other']['mediaId'],'',true), 'label' => $mixture['images']['other']['label'], 'classes' => $classes);
            } else {
                return null;
            }
        } else {
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
        echo $result;
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
        echo '<li title="'.$details.'">';
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
                echo '<span>'.$by.'<span class="camelcase"><bdi>'.$value['user'].'</bdi></span></span>';
            }
            $i++;
        echo '</li>';
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

    // Make sure current page crumb is last in list (this also occurs with 'dokuwiki' template so it seems to be a core code minor bug)
    // COULD BE FIXED WITH FOLLOWING LINE BUT THIS BREAKS TWISTIENAV AS IT IS BASED ON CORE BREADCRUMBS()
    //$value = $crumbs[$ID];
    //unset($crumbs[$ID]);
    //$crumbs = array_merge($crumbs); 
    //$crumbs[$ID] = $value;


    if (count($crumbs) > 0) {
        //render crumbs, highlight the last one
        echo '<ul>';
        echo '<li><span class="small-hidden medium-hidden large-hidden glyph-20 label" title="'.rtrim($lang['breadcrumb'], ':').'">'.$mixture['glyphs']['trace'].'</span><span class="tiny-hidden label">'.$lang['breadcrumb'].'</span></li>';
        $last = count($crumbs);
        $i    = 0;
        foreach($crumbs as $target => $name) {
            $i++;
            echo '<li>';
              if (count(explode(":",$target)) == 1) { $target = ":".$target; }
              php_mixture_pagelink($target);
            echo '</li>';
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

    echo '<ul>';
    echo '<li><span class="small-hidden medium-hidden large-hidden glyph-20 label" title="'.rtrim($lang['youarehere'], ':').'">'.$mixture['glyphs']['youarehere'].'</span><span class="tiny-hidden label">'.$lang['youarehere'].'</span></li>';
    // print the startpage unless we're in translated namespace (in wich case trace will start with current language start page)
    if (((isset($trs['parts'][0])) && ((strlen($trs['parts'][0]) == 0) || ($trs['parts'][0] == $trs['defaultLang']))) || (plugin_isdisabled('translation'))) {
        echo '<li>';
            php_mixture_pagelink($conf['start']);
        echo '</li>';
    }
    // print intermediate namespace links
    $part = '';
    for($i = 0; $i < $count - 1; $i++) {
        $part .= $parts[$i].':';
        $page = $part;
        if (substr($page, -1) == ":") { $page .= $conf['start']; }
        echo '<li>';
            php_mixture_pagelink($page);
        echo "</li>";
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
    echo "<li>";
        php_mixture_pagelink($page);
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
function php_mixture_glyph($target = null, $context = "breadcrumbs", $label = null, $return = false) {
    global $mixture, $trs, $conf, $lang;

    //if ($what == "page") {
        $tmp = explode(":", ltrim($target, ":"));
        if ($context == "breadcrumbs") {
            // Add glyph before user's public page
            if ((count($tmp) == 2) && (($tmp[0] == "user") or ($tmp[0] == $conf['plugin']['userhomepage']['public_pages_ns']))) {
                $glyph =  '<span class="glyph-18" title="'.tpl_getLang('publicpage').'">'.$mixture['glyphs']['users'].'</span>';
            // Add glyph before user's private namespace
            } elseif ((count($tmp) == 3) && (($tmp[0] == "user") or ($tmp[0] == $conf['plugin']['userhomepage']['public_pages_ns'])) && ($tmp[2] == $conf['start'])) {
                $glyph =  '<span class="glyph-18" title="'.tpl_getLang('privatens').'">'.$mixture['glyphs']['userprivate'].'</span>';
            // Add a flag SVG image before translations
            } elseif ((strlen($tmp[0]) == 2) && ($tmp[0] != $trs['defaultLang']) && (strpos($conf['plugin']['translation']['translations'], $tmp[0]) !== false)) {
                $glyph =  '<span class="glyph-18" title="<'.$tmp[0].'>">'.$mixture['glyphs']['translation'].'</span>';
            // Add a house SVG image before home
            } elseif (ltrim($target, ":") == $conf['start']) {
                $glyph =  '<span class="glyph-18" title="'.tpl_getLang('wikihome').'">'.$mixture['glyphs']['home'].'</span>';
            } else {
                $glyph =  '<span class="glyph-18" title="*Unknown*">'.$mixture['glyphs']['default'].'</span>';
            }
        } elseif ($context == "action") {
            if ($target == "home") {
                $glyph =  '<span class="glyph-18" title="'.$label.'">'.$mixture['glyphs']['home'].'</span>';
            } elseif (($target == "advanced") || ($target == "confmanager")){
                $glyph =  '<span class="glyph-18" title="'.$label.'">'.$mixture['glyphs']['config'].'</span>';
            } elseif ($target == "custombuttons") {
                $glyph =  '<span class="glyph-18" title="'.$label.'">'.$mixture['glyphs']['admin'].'</span>';
            } elseif ($target == "searchindex") {
                $glyph =  '<span class="glyph-18" title="'.$label.'">'.$mixture['glyphs']['index'].'</span>';
            } elseif ($target == "usermanager") {
                $glyph =  '<span class="glyph-18" title="'.$label.'">'.$mixture['glyphs']['users'].'</span>';
            } else {
                $glyph =  '<span class="glyph-18" title="'.$label.'">'.$mixture['glyphs'][$target].'</span>';
            }
        } else {
            //$glyph =  '<span class="glyph-18" title="'.$target.' ('.$context.' '.$page.')">'.$mixture['glyphs']['default'].'</span>';
            $glyph =  '<span class="glyph-18" title="'.$target.' ('.$context.')">'.$mixture['glyphs']['default'].'</span>';
        }
    //} else {
    //    $glyph =  '<span class="glyph-18" title="'.$target.' ('.$context.' '.$page.')">'.$mixture['glyphs']['default'].'</span>';
    //}

    if ($return == true) {
        return $glyph;
    } else {
        echo $glyph;
    }
}

/**
 * Adapted from tpl_admin.php file of Bootstrap3 template by Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 */
function php_mixture_admin() {
    global $ID, $ACT, $auth, $conf;

    $admin_plugins = plugin_list('admin');
    $tasks = array('usermanager', 'acl', 'extension', 'config', 'styling', 'revert', 'popularity', 'upgrade');
    $addons = array_diff($admin_plugins, $tasks);
    $adminmenu = array(
        'tasks' => $tasks,
        'addons' => $addons
    );
    foreach ($adminmenu['tasks'] as $task) {
        if(($plugin = plugin_load('admin', $task, true)) === null) continue;
//        if($plugin->forAdminOnly() && !$INFO['isadmin']) continue;
        if($task == 'usermanager' && ! ($auth && $auth->canDo('getUsers'))) continue;
        $label = $plugin->getMenuText($conf['lang']);
        if (! $label) continue;
        if ($task == "popularity") { $label = preg_replace("/\([^)]+\)/","",$label); }
        if (($ACT == 'admin') and ($_GET['page'] == $task)) { $class = ' class="action active"'; } else { $class = ' class="action"'; }
        //dbg($task);
        echo sprintf('<li><a href="%s" title="%s"%s>%s%s</a></li>', wl($ID, array('do' => 'admin','page' => $task)), $label, $class, php_mixture_glyph($task, "action", $label, true), $label);
    }
    $f = fopen(DOKU_INC.'inc/lang/'.$conf['lang'].'/adminplugins.txt', 'r');
    $line = fgets($f);
    fclose($f);
    $line = preg_replace('/=/', '', $line);
    if (count($adminmenu['addons']) > 0) {
        echo '<li class="dropdown-header"><span>'.$line.'</span></li><hr/>';
        foreach ($adminmenu['addons'] as $task) {
            if(($plugin = plugin_load('admin', $task, true)) === null) continue;
            if ($task == "move_tree") {
                $parts = explode('<a href="%s">', $plugin->getLang('treelink'));
                $label = substr($parts[1], 0, -4);
            } else {
                $label = $plugin->getMenuText($conf['lang']);
            }
            if($label == null) { $label = ucfirst($task); }
            if (($ACT == 'admin') and ($_GET['page'] == $task)) { $class = ' class="action active"'; } else { $class = ' class="action"'; }
            echo sprintf('<li><a href="%s" title="%s"%s>%s %s</a></li>', wl($ID, array('do' => 'admin','page' => $task)), ucfirst($task), $class, php_mixture_glyph($task, "action", ucfirst($label), true), ucfirst($label));
        }
    }
    echo '<li class="dropdown-header"><span>'.tpl_getLang('cache').'</span></li><hr/>';
    echo '<li><a href="';
        echo wl($ID, array("do" => $_GET['do'], "page" => $_GET['page'], "purge" => "true"));
    echo '" class="action">'.php_mixture_glyph("refresh", "action", ucfirst($label),true).tpl_getLang('purgepagecache').'</a></li>';
    echo '<li><a href="'.DOKU_URL.'lib/exe/js.php" class="action">'.php_mixture_glyph("refresh", "action", ucfirst($label),true).tpl_getLang('purgejscache').'</a></li>';
    echo '<li><a href="'.DOKU_URL.'lib/exe/css.php" class="action">'.php_mixture_glyph("refresh", "action", ucfirst($label),true).tpl_getLang('purgecsscache').'</a></li>';
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
            echo "<li class='tab'>".tpl_link(wl($value['id']), $pagename, 'class="'.$classes.'" title="'.$value['id'].'"', true)."</li>";
        }
    }
}

/**
 * SEARCH FORM
 * Adapted from core to have more control over input placeholder and button content
 *
 * See original function in inc/template.php for details
 */
function php_mixture_searchform($ajax = true, $autocomplete = true) {
    global $lang, $ACT, $QUERY;
    global $mixture;
    // don't print the search form if search action has been disabled
    if(!actionOK('search')) return false;
    echo '<form id="dw__search" class="" action="'.wl().'" accept-charset="utf-8" method="get" role="search">';
    echo '<div class="form-group">';
    echo '<input type="hidden" name="do" value="search" />';
    echo '<input type="text" ';
    if($ACT == 'search') echo 'value="'.htmlspecialchars($QUERY).'" ';
    if(!$autocomplete) echo 'autocomplete="off" ';
    echo 'id="qsearch__in" accesskey="f" name="id" class="form-control" title="[F]" placeholder="'.$lang['btn_search'].'" />';
    echo '<button type="submit" title="'.$lang['btn_search'].'"><span class="label glyph-24">'.$mixture['glyphs']['search'].'</span></button>';
    if ($ajax) echo '<div id="qsearch__out" class="navbar-form ajax_qsearch JSpopup';
        if($autocomplete) echo ' autocomplete';
    echo '"></div>';
    echo '</div>';
    echo '</form>';
    return true;
}

/**
 * Print a link to custom (like "home") or standard action
 *
 * @param string        $action action command
 */
function php_mixture_action($action) {
    global $lang;
    // if action isn't disabled within DW's setting
    if (strpos($conf['disableactions'], $action) === false) {
        echo "<li>";
            // "home" isn't a DW's action so building the link is specific
            if ($action == "home") {
                tpl_link(wl(),php_mixture_glyph("home", "action", tpl_getLang('wikihome'), true).tpl_getLang('wikihome'),'class="action home" accesskey="h" title="'.tpl_getLang('wikihome').' [H]"');
            // "logout" is a DW's action but uses same action name than "login" and Mixture needs to make a difference to serve correct glyph
            } elseif ($action == "logout") {
                tpl_action("login", 1, '', 0, "", "", php_mixture_glyph("logout", "action", $lang['btn_login'], true).$lang['btn_logout']);
            } else {
                tpl_action($action, 1, '', 0, "", "", php_mixture_glyph($action, "action", $lang['btn_'.$action], true).$lang['btn_'.$action]);
            }
        echo "</li>";
    }
}

function php_mixture_pagelink($target) {
    global $ID;

    if ($target == $ID) {
        $classes = "curid";
    }
    if (page_exists($target)) {
        $classes .= " wikilink1";
    } else {
        $classes = " wikilink2";
    }
    $pageName = php_mixture_pagetitle($target);
    tpl_link(
        wl($target),
        php_mixture_glyph($target, "breadcrumbs", $pageName, true).$pageName,
        'class="'.ltrim($classes, " ").'" title="'.$target.'"'
    );
}
