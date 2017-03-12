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
function mixture_init() {
    global $ID, $conf, $JSINFO;
    // New global variables
    global $mixture, $uhp, $trs, $translationHelper, $tags;
    $id = str_replace("discussion:", "", $ID);
    //reuse the CSS dispatcher functions without triggering the main function
    define('SIMPLE_TEST', 1);
    require_once(DOKU_INC . 'lib/exe/css.php');

    // HELPER PLUGINS
    // Preparing usefull plugins' helpers
    $interwiki = getInterwiki();
    $mixturePublicId = ltrim(str_replace('{NAME}', $_SERVER['REMOTE_USER'], $interwiki['user']),':');
    if (!plugin_isdisabled('userhomepage')) {
        $uhpHelper = plugin_load('helper','userhomepage');
        $uhp = $uhpHelper->getElements();
//dbg($uhp);
        if ((isset($mixturePublicId)) and (!isset($uhp['public']))) {
            $uhp['public'] = array();
            $uhp['public']['id'] = $mixturePublicId;
            if ((tpl_getLang('public_page') != null) and (!isset($uhp['public']['string']))) {
                $uhp['public']['string'] = tpl_getLang('public_page');
            }
        }
    } else {
        // Without Userhomepage plugin, Public Page namespace is set by 'user' value in 'conf/interwiki.conf'
        $uhp = array();
        $uhp['private'] = null;
        $uhp['public'] = array();
        $uhp['public']['id'] = $mixturePublicId;
        $uhp['public']['string'] = tpl_getLang('public_page');
    }
//dbg($uhp);
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
            $trs['translations'][$lc] = $translation[0];
            if (page_exists($translation[0])) {
                $classes = "wikilink1";
            } else {
                $classes = "wikilink2";
            }
            //$trs['links'][$lc] = tpl_link(wl($trs['translations'][$lc]), $lc, 'class="'.$classes.'"', true);
            if ($lc == $trs['parts'][0]) {
                $classes .= " cur";
                $trs['links'][$lc] = tpl_link(wl($ID), $lc, 'class="'.$classes.'"', true);
                $trs['translations'][$lc] = ltrim($trs['translations'][$lc], ":");
            } else {
                //if ($lc == null) {
                //    $trs['links'][$conf['lang']] = $translationHelper->getTransItem($lc, $trs['parts'][1]);
                //} else {
                //    $trs['links'][$lc] = $translationHelper->getTransItem($lc, $trs['parts'][1]);
                //}
                if (strpos($conf['plugin']['translation']['translations'], $lc) !== false) {
                    $trs['links'][$lc] = $translationHelper->getTransItem($lc, $trs['parts'][1]);
                    $trs['translations'][$lc] = ltrim($trs['translations'][$lc], ":");
//dbg("là!");
                } else {
                    $trs['links'][$lc] = $translationHelper->getTransItem("", $trs['parts'][1]);
                    $trs['translations'][$lc] = $trs['parts'][1];
//dbg("ici!");
                }
            }
//dbg($trs['translations']['en']);
//            if ($trs['translations'][$lc] == $ID) {
//dbg("là?".$lc);
//dbg($trs['links']['en']);
//                $classes .= " active";
//                unset($trs['translations'][$lc]);
//            }
            foreach ($trs['links'] as $lc => $link) {
                if (strpos($link, "div") !== false) {
                    $trs['links'][$lc] = substr($trs['links'][$lc], 20);
                    $trs['links'][$lc] = substr($trs['links'][$lc], 0, -11);
                }
            }
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
}

/**
 * PRINT THE BREADCRUMBS TRACE, adapted from core (template.php) to use a CSS separator solution and respect existing/non-existing page link colors
 *
 * @return bool
 */
function mixture_breadcrumbs() {
    global $lang, $conf, $uhp, $ID;

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
            print '<li><span class="md-display-none" title="'.rtrim($lang['breadcrumb'], ':').'">'.mixture_glyph("breadcrumbs").'</span><span class="display-none md-display-initial">'.$lang['breadcrumb'].'</span></li>';
//        }
        $last = count($crumbs);
        $i    = 0;
        foreach($crumbs as $target => $name) {
            $i++;
            $class = mixture_breadcrumbsClass($target);
            print '<li'.$class.'>';
              if (page_exists($target)) {
                $class = "wikilink1";
              } else {
                $class = "wikilink2";
              }
              if (count(explode(":",$target)) == 1) { $target = ":".$target; }
              if (p_get_metadata($target, 'plugin_croissant_bctitle') != null) {
                tpl_pagelink($target, p_get_metadata($target, 'plugin_croissant_bctitle'));
              } else {
                tpl_pagelink($target);
              }
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
function mixture_youarehere() {
    global $conf, $ID, $lang, $trs;

    // check if enabled
    if(!$conf['youarehere']) return false;

    $parts = explode(':', $ID);
    $count = count($parts);

    print '<ul>';
//    if (tpl_getConf('breadcrumbsStyle') == "classic") {
        print '<li><span class="md-display-none" title="'.rtrim($lang['youarehere'], ':').'">'.mixture_glyph("youarehere").'</span><span class="display-none md-display-initial">'.$lang['youarehere'].'</span></li>';
//    }
    // print the startpage unless we're in translated namespace (in wich case trace will start with current language start page)
    //if ((isset($trs['parts'][0])) and (isset($trs['defaultLang'])) and ($trs['parts'][0] == $trs['defaultLang'])) {
    if (((isset($trs['parts'][0])) and (isset($trs['defaultLang'])) and ($trs['parts'][0] == $trs['defaultLang'])) or ((!plugin_isdisabled('translation')) and (strpos($conf['plugin']['translation']['translations'], $trs['defaultLang']) === false)) or (plugin_isdisabled('translation'))) {
        echo '<li class="home">';
            tpl_pagelink(':'.$conf['start']);
        echo '</li>';
    }
    // print intermediate namespace links
    $part = '';
    for($i = 0; $i < $count - 1; $i++) {
        $part .= $parts[$i].':';
        $page = $part;
        //if($page == $conf['start']) continue; // Skip startpage
        $class = mixture_breadcrumbsClass($page);
        // output
        // skip if current target leads to untranslated wiki start
//        if ((isset($trs['defaultLang'])) and ($page != $trs['defaultLang'].":")) {
            echo "<li$class>";
            if (p_get_metadata($page.$conf['start'], 'plugin_croissant_bctitle') != null) {
                tpl_pagelink($page, p_get_metadata($page.$conf['start'], 'plugin_croissant_bctitle'));
            } else {
                tpl_pagelink($page);
            }
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
    $class = mixture_breadcrumbsClass($page);
    echo "<li$class>";
        if (p_get_metadata($page, 'plugin_croissant_bctitle') != null) {
            tpl_pagelink($page, p_get_metadata($page, 'plugin_croissant_bctitle'));
        } else {
            tpl_pagelink($page);
        }
    echo "</li>";
    echo "</ul>";
    return true;
}

/**
 * SELECT BREADCRUMBS SPECIAL CLASS IF NEEDED
 *
 * @return string
 */
function mixture_breadcrumbsClass($target = null) {
    global $ID, $conf, $uhp, $translationHelper,$trs;

    $classes = "";
//dbg($target);
//dbg($uhp);
//dbg(substr($uhp['private']['id'], 0, 0-strlen($conf['start'])));
    if ($target != null) {
//        if (tpl_getConf('breadcrumbsGlyphs')) {
            if ($target == $conf['start']) {
                $classes .= " home";
            } elseif (($uhp['private']['id'] != null) and (($target == $uhp['private']['id']) or ($target == substr($uhp['private']['id'], 0, 0-strlen($conf['start']))) or (substr($target, 0, strlen(substr($uhp['private']['id'], 0, 0-strlen($conf['start'])))) == substr($uhp['private']['id'], 0, 0-strlen($conf['start']))))) {
                $classes .= " userprivate";
            } elseif (($target == $uhp['public']['id']) and ($target != "user:start")) {
                $classes .= " userpublic";
            } elseif (isset($translationHelper)) {
                $tmp = $translationHelper->getTransParts($target);
                //if (($tmp[0] != null) and ($tmp[0] != $conf['lang'])) {
                // If first part of $ID is a language code other than default language
                if (($tmp[0] != null) and ($tmp[0] != $trs['defaultLang'])) {
                    $classes .= " translated";
                }
            }
//        }
        if (($target == $ID) or ($target == rtrim($ID, $conf['start']))) {
            $classes .= " active";
        }
    }
    if ($classes != null) {
        return ' class="'.ltrim($classes, " ").'"';
    } else {
        return null;
    }
}

/**
 * RETURN GLYPH CORRESPONDING TO GIVEN ACTION
 * 
 * Returns action's glyph with classes depending on context (main menu, dropdown, ...).
 *
 * @param string    $action
 * @param string    $context (null|pagetools|dropdown|button)
 * @param bool      $button
 * @return string|null
 */
function mixture_glyph($action, $context = null) {
    global $mixture;

    if (isset($mixture['glyphs'][$action])) {
        $icon = $mixture['glyphs'][$action];
    } else {
        $icon = $mixture['glyphs']['default'];
    }
    if (($context == 'dropdown') or ($context == 'modal')) {
        return "<i class='fa fa-fw text-alt ".$icon."'></i> ";
    } elseif (($context == 'scroll-up') or ($context == 'scroll-down')) {
        return "<i class='fa ".$icon." fa-stack-1x fa-inverse'></i>";
    } elseif ($context == 'close') {
        return "<i class='fa fa-2x ".$icon."'></i>";
    } else {
        return "<i class='fa ".$icon."'></i>";
    }
}
