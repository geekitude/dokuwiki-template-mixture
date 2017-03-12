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
        if (tpl_getConf('breadcrumbsGlyphs')) {
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
        }
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
    global $namespaced;

    if (isset($namespaced['glyphs'][$action])) {
        $icon = $namespaced['glyphs'][$action];
    } else {
        $icon = $namespaced['glyphs']['default'];
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
