<?php
/**
 * DokuWiki Starter Template
 *
 * @link     http://dokuwiki.org/template:starter
 * @author   Anika Henke <anika@selfthinker.org>
 * @license  GPL 2 (http://www.gnu.org/licenses/gpl.html)
 */

if (!defined('DOKU_INC')) die();
@require_once(dirname(__FILE__).'/tpl_functions.php');
header('X-UA-Compatible: IE=edge,chrome=1');

global $mixture, $uhp, $trs;
// Reset $mixture to make sure we don't inherit any value from previous page
$mixture = array();
php_mixture_init();

$hasSidebar = page_findnearest($conf['sidebar']);
$showSidebar = $hasSidebar && ($ACT=='show');
?><!doctype html>
<html class="no-js" lang="<?php echo $conf['lang'] ?>" dir="<?php echo ($_GET['dir'] <> null) ? $_GET['dir'] : $lang['direction']; ?>" class="no-js">
    <head>
        <meta charset="UTF-8">
        <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge"><![endif]-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <title><?php tpl_pagetitle() ?> [<?php echo strip_tags($conf['title']) ?>]</title>
        <script>(function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)</script>
        <?php tpl_metaheaders() ?>
        <?php echo tpl_favicon(array('favicon', 'mobile')) ?>
        <?php tpl_includeFile('meta.html') ?>
    </head>
    <body id="dokuwiki__top" class="<?php echo tpl_classes();?><?php echo ($showSidebar) ? ' showSidebar' : ''; ?><?php echo php_mixture_classes();?> inline-pagenav-dropdown">
        <div id="mixture__site">
            <!-- ********** HEADER ********** -->
            <header id="mixture__header" role="banner" class="pam">
                <!-- TOPBAR (with date & last changes) -->
                <?php if ((strpos(tpl_getConf('elements'), 'topbar_date') !== false) or (strpos(tpl_getConf('elements'), 'topbar_lastchanges') !== false) or (strpos(tpl_getConf('elements'), 'topbar_links') !== false)) : ?>
                    <div id="mixture__topbar" class="smaller clearfix">
                        <div class="flex-container-h justify-between">
                            <ul class="flex-container-h">
                                <?php if (strpos(tpl_getConf('elements'), 'topbar_date') !== false) : ?>
                                    <li id="mixture__topbar_date" class="camelcase">
                                        <span class="label glyph-18" title="<?php echo php_mixture_date("long"); ?>">
                                            <?php echo $mixture['glyphs']['calendar']; ?>
                                        </span>
                                        <span class="text">
                                            <?php 
                                                echo php_mixture_date("long");
                                            ?>
                                        </span>
                                    </li>
                                <?php endif; ?>
                                <?php if ((strpos($conf['disableactions'], 'search') === false) && (count($mixture['recents']) >= 1)) : ?>
                                    <li id="js_lastchanges_container" class="flex-container-h">
                                        <strong>
                                            <span class="label glyph-18" title="<?php echo $lang['btn_recent']; ?>">
                                                <?php echo $mixture['glyphs']['recent']; ?>
                                            </span>
                                            <span class="a11y">
                                                <?php echo $lang['btn_recent'] ?>:
                                            </span>
                                        </strong>
                                        <ul class="<?php if (count($mixture['recents']) > 1) { echo 'js-lastchanges'; } else { echo 'lastchange'; } ?>">
                                            <?php
                                                php_mixture_lastchanges();
                                            ?>
                                        </ul>
                                    </li><!-- /#js_lastchanges_container -->
                                <?php endif; ?>
                            </ul>
                            <?php if ($mixture['topbarLinks'] != null) : ?>
                                <ul class="flex-container-h">
                                    <li id="mixture__topbar_links" class="camelcase dropdown">
                                        <span class="label glyph-18" title="<?php echo tpl_getLang('relatedlinks'); ?>">
                                            <?php echo $mixture['glyphs']['link']; ?>
                                        </span>
                                        <?php echo $mixture['topbarLinks']; ?>
                                    </li>
                                </ul>
                            <?php endif ?>
                        </div><!-- /.flex-container-h -->
                        <hr class="mts mb0" />
                    </div><!-- /#mixture__topbar -->
                <?php endif; ?>
                <p class="a11y skip">
                    <a href="#mixture__content"><?php echo $lang['skip_to_content'] ?></a>
                </p>
                <?php tpl_includeFile('headerheader.html') ?>
                <!-- BRANDING -->
                <div id="mixture__branding" class="flex-container-h justify-between">
                    <div id="mixture__branding_start" class="flex-container-h items-center">
                        <?php if ($mixture['images']['logo'] != null) : ?>
                            <div id="mixture__branding_logo">
                                <?php
                                    /*$logoImage = ml($mixture['images']['logo']['mediaId'],'',true);*/
                                    if ($mixture['images']['logo']['mediaId'] != null) {
                                        $logoImage = ml($mixture['images']['logo']['mediaId'],'',true);
                                    } else {
                                        $logoImage = "/lib/tpl/mixture/images/logo.png";
                                    }
                                    $link = php_mixture_ui_link("logoLink", substr($mixture['images']['logo']['mediaId'], 0, strrpos($mixture['images']['logo']['mediaId'], ':') + 1));
                                    $title = "Logo";
                                    if ($link != null) {
                                        tpl_link(
                                            $link['target'],
                                            '<img id="mixture__branding_logo_image" src="'.$logoImage.'" title="'.$link['label'].'" alt="*'.$title.'*" '.$mixture['images']['logo']['imageSize'][3].' />'
                                        );
                                    } else {
                                        echo '<img id="mixture__branding_logo_image" src="'.$logoImage.'" title="'.$title.'" alt="*'.$title.'*" '.$mixture['images']['logo']['imageSize'][3].' />';
                                    }
                                ?>
                            </div><!-- /#mixture__branding_logo -->
                        <?php endif ?>
                        <div id="mixture__branding_text">
                            <?php if (file_exists(tpl_incdir().'title.html')) : ?>
                                <?php tpl_includeFile('title.html'); ?>
                            <?php elseif ((tpl_getConf('dynamicBranding') == 1) && ($ID <> $conf['start']) && ($ACT == 'show')): ?>
                                <h1 id="mixture__title">
                                    <?php
                                        // display wiki title as a link depending on titleLink setting
                                        $link = php_mixture_ui_link("titleLink");
                                        $text = php_mixture_branding("title");
                                        if ($link != null) {
                                            $label = $link['label'];
                                            if ($link['accesskey'] != null) {
                                                $label .= " [".strtoupper($link['accesskey'])."]";
                                                $accesskey = 'accesskey="'.$link['accesskey'].'" ';
                                            }
                                            tpl_link(
                                                $link['target'],
                                                //'<span>'.$text.'</span>',
                                                '<span>'.$text.'</span>',
                                                $accesskey.'title="'.$label.'" class="'.$link['classes'].'"'
                                            );
                                        } else {
                                            echo '<span>'.$text.'</span>';
                                        }
                                    ?>
                                </h1>
                            <?php else: ?>
                                <h1 id="mixture__title"><?php tpl_link(wl(),$conf['title'],'accesskey="h" title="'.tpl_getLang('wikihome').' [H]"'); ?></h1>
                            <?php endif; ?>
                            <?php if ($conf['tagline']): ?>
                                <?php
                                    echo "<p id='mixture__tagline'>";
                                        // display wiki tagline as a link depending on taglineLink setting
                                        $link = php_mixture_ui_link("taglineLink");
                                        $text = php_mixture_branding("tagline");
                                        if ($link != null) {
                                            $label = $link['label'];
                                            if ($link['accesskey'] != null) {
                                                $label .= " [".strtoupper($link['accesskey'])."]";
                                                $accesskey = 'accesskey="'.$link['accesskey'].'" ';
                                            }
                                            tpl_link(
                                                $link['target'],
                                                '<span>'.$text.'</span>',
                                                $accesskey.'title="'.$label.'" class="'.$link['classes'].'"'
                                            );
                                        } else {
                                            echo '<span>'.$text.'</span>';
                                        }
                                    echo "</p>";
                                ?>
                            <?php endif ?>
                        </div>
                    </div>
                    <div id="mixture__branding_end" class="flex-container-h items-center">
                        <div class="content">
                            <?php tpl_includeFile('bannerheader.html'); ?>
                            <?php if (file_exists(tpl_incdir().'banner.html')) : ?>
                                <?php tpl_includeFile('banner.html'); ?>
                            <?php elseif ($mixture['images']['banner'] != null) : ?>
                                <div id="mixture__branding_banner">
                                    <?php
                                        /*$bannerImage = ml($mixture['images']['banner']['mediaId'],'',true);*/
                                        if ($mixture['images']['banner']['mediaId'] != null) {
                                            $bannerImage = ml($mixture['images']['banner']['mediaId'],'',true);
                                        } else {
                                            $bannerImage = "/lib/tpl/mixture/images/banner.jpg";
                                        }
                                        $link = php_mixture_ui_link("bannerLink", substr($mixture['images']['banner']['mediaId'], 0, strrpos($mixture['images']['banner']['mediaId'], ':') + 1));
                                        $title = "Banner";
                                        if ($link != null) {
                                            if ($link['accesskey'] != null) {
                                                $link['label'] .= " [".strtoupper($link['accesskey'])."]";
                                                $accesskey = 'accesskey="'.$link['accesskey'].'" ';
                                            }
                                            tpl_link(
                                                $link['target'],
                                                '<img id="mixture__branding_banner_image" src="'.$bannerImage.'" '.$accesskey.'title="'.$link['label'].'" alt="*'.$title.'*" '.$mixture['images']['banner']['imageSize'][3].' />'
                                            );
                                        } else {
                                            echo '<img id="mixture__branding_banner_image" src="'.$bannerImage.'" title="'.$title.'" alt="*'.$title.'*" '.$mixture['images']['banner']['imageSize'][3].' />';
                                        }
                                    ?>
                                </div><!-- /#mixture__branding_banner -->
                            <?php endif ?>
                            <?php tpl_includeFile('bannerfooter.html'); ?>
                            <?php if (tpl_getConf('mainNav') == "classic") : ?>
                                <nav id="mixture__classic_nav" class="main-navigation" role="navigation">
                                    <h3 class="toggle tools"><?php echo $lang['tools']; ?></h3>
                                    <div class="content">
                                        <section id="mixture__usertools" class="clearfix">
                                            <ul>
                                                <!-- SEARCH FORM (if "search" action isn't disabled) -->
                                                <?php if (strpos($conf['disableactions'], 'search') === false) : ?>
                                                    <li id="dw__search" class="widget search-wrap">
                                                        <?php php_mixture_searchform() ?>
                                                    </li>
                                                <?php endif ?>
                                                <!-- USER MENU -->
                                                <li id="mixture__classic_nav_user" class="dropdown">
                                                    <?php
                                                        if ($_SERVER['REMOTE_USER'] != NULL) {
                                                            if ($mixture['images']['userAvatar']['img']) {
                                                                echo '<span id="mixture__user_avatar">';
                                                                    echo $mixture['images']['userAvatar']['img'];
                                                                echo '</span>';
                                                            } else {
                                                                echo "<span class='label glyph-32' title='".$INFO['userinfo']['name'].' ('.$_SERVER['REMOTE_USER'].')'."'>".$mixture['glyphs']['profile']."</span>";
                                                            }
                                                            echo "<ul class='dropdown-content'>";
                                                                if ($uhp['private']['id']) {
                                                                    print '<li>';
                                                                        tpl_link(wl($uhp['private']['id']),php_mixture_glyph($uhp['private']['id'], "usertools", $uhp['private']['string'],true)." ".$uhp['private']['string'],' title="'.$uhp['private']['id'].'"');
                                                                    print '</li>';
                                                                }
                                                                if ($uhp['public']['id']) {
                                                                    print '<li>';
                                                                        tpl_link(wl($uhp['public']['id']),php_mixture_glyph($uhp['public']['id'], "usertools", $uhp['public']['string'],true)." ".$uhp['public']['string'],' title="'.$uhp['public']['id'].'"');
                                                                    print '</li>';
                                                                }
                                                                php_mixture_action("profile");
                                                                php_mixture_action("logout");
                                                                // DW's event process kept in case a plugin needs it
                                                                tpl_toolsevent('usertools', array());
                                                            echo "</ul>";
                                                        } else {
                                                            // if "register" action is disabled, show a simple link to login action
                                                            if (strpos($conf['disableactions'], 'register') !== false) {
                                                                tpl_action('login', 1, '', 0, '', '', "<span class='label glyph-32' title='".$lang['usertools']."'>".$mixture['glyphs']['login']."</span>");
                                                            // if "register" action is enabled, show both login or register actions
                                                            } else {
                                                                echo "<span class='label glyph-32' title='".$lang['usertools']."'>".$mixture['glyphs']['login']."</span>";
                                                                echo "<ul class='dropdown-content'>";
                                                                    php_mixture_action("register");
                                                                    php_mixture_action("login");
                                                                    // DW's event process kept in case a plugin needs it
                                                                    tpl_toolsevent('usertools', array());
                                                                echo "</ul>";
                                                            }
                                                        }
                                                    ?>
                                                </li><!-- /#mixture__classic_nav_user -->
                                                <!-- ADMIN MENU -->
                                                <?php
                                                    if (($_SERVER['REMOTE_USER'] != NULL) && ($INFO['isadmin'])) {
                                                        echo '<li id="mixture__classic_nav_admin" class="dropdown">';
                                                            echo "<span class='label glyph-32' title='".$lang['btn_admin']."'>".$mixture['glyphs']['admin']."</span>";
                                                            echo "<ul class='dropdown-content'>";
                                                                php_mixture_admin();
                                                            echo "</ul>";
                                                        echo '</li><!-- /#mixture__classic_nav_admin -->';
                                                    }
                                                ?>
                                            </ul>
                                        </section><!-- /#mixture__usertools -->
                                        <!-- OTHER SITE TOOLS -->
                                        <section id="mixture__sitetools" class="clearfix">
                                            <h3 class="a11y"><?php echo $lang['site_tools'] ?></h3>
                                            <ul>
                                                <?php
                                                    php_mixture_action("home");
                                                    php_mixture_action("recent");
                                                    php_mixture_action("media");
                                                    php_mixture_action("index");
                                                    // DW's event process kept in case a plugin needs it
                                                    tpl_toolsevent('sitetools', array());
                                                ?>
                                            </ul>
                                        </section><!-- /#mixture__sitetools -->
                                    </div><!-- /.content -->
                                </nav><!-- /#mixture__classic_nav -->
                            <?php endif ?>
                        </div><!-- /.content -->
                    </div>
                </div><!-- /#mixture__branding -->
                <aside id="mixture__alerts">
                    <!-- ALERTS -->
                    <?php html_msgarea() ?>
                </aside>
                <!-- BREADCRUMBS -->
                <?php if (($conf['breadcrumbs']) or ($conf['youarehere'])) { ?>
                    <div class="breadcrumbs flex-container-v small">
                        <?php if ($conf['breadcrumbs']) { ?>
                            <div class="trace"><?php php_mixture_breadcrumbs() ?></div>
                        <?php } ?>
                        <?php if ($conf['youarehere']) { ?>
                            <div class="youarehere"><?php php_mixture_youarehere() ?></div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <!-- <div class="clearfix"><hr /></div> -->
                <?php tpl_includeFile('headerfooter.html') ?>
            </header>
            <aside id="mixture__pagenav" class="flex-container-h">
                <?php if ($mixture['images']['widebanner'] != null) : ?>
                    <div id="mixture__widebanner">
                        <?php
                            if ($mixture['images']['widebanner']['mediaId'] != null) {
                                $widebannerImage = ml($mixture['images']['widebanner']['mediaId'],'',true);
                            } else {
                                $widebannerImage = "/lib/tpl/mixture/images/widebanner.jpg";
                            }
                            $title = "Widebanner";
                            echo '<img id="mixture__widebanner_image" src="'.$widebannerImage.'" title="'.$title.'" alt="*'.$title.'*" '.$mixture['images']['widebanner']['imageSize'][3].' />';
                        ?>
                    </div><!-- /#mixture__branding_banner -->
                <?php endif ?>
                <?php
                    if (isset($trs['dropdown'])) {
                        echo $trs['dropdown'];
                    }
                ?>
                <ul class="small flex-container-h">
                    <li>
                        <div class="pageId"><span><?php echo hsc($ID) ?></span></div>
                    </li>
                    <?php
                        // List current page's translation(s), existing or not (if Translation plugin isn't set to use a dropdown)
                        if ((!isset($trs['dropdown'])) && (is_array($trs['links'])) && (count($trs['links']) >= 1)) {
                            foreach($trs['links'] as $key => $value) {
                                echo "<li class='translation'>".$value."</li>";
                            }
                        }
                    ?>
                    <?php if (strpos(tpl_getConf('elements'), 'pagenav_nsindex') !== false) : ?>
                        <li id="mixture__pagenav_nsindex" class="dropdown">
                            <span class="label glyph-18" title="<?php echo tpl_getLang('otherpages'); ?>">
                                <?php echo $mixture['glyphs']['ellipsis']; ?>
                            </span>
                            <ul class="dropdown-content">
                                <?php php_mixture_pagenav(); ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </aside>
            <main role="main" class="<?php echo (tpl_getConf('wrappedSidebar') == 0) ? 'flex-container' : ''; ?>">
                <!-- ********** ASIDE ********** -->
                <?php if ($showSidebar) : ?>
                    <aside id="mixture__sidebar" class="mod aside">
                        <h3 class="toggle"><?php echo $lang['sidebar'] ?></h3>
                        <div class="content" role="complementary">
                            <?php
                                if ($mixture['images']['sidebar'] != null) {
                                    /*$sidebarImage = ml($mixture['images']['sidebar']['mediaId'],'',true);*/
                                    if ($mixture['images']['sidebar']['mediaId'] != null) {
                                        $sidebarImage = ml($mixture['images']['sidebar']['mediaId'],'',true);
                                    } else {
                                        $sidebarImage = "/lib/tpl/mixture/images/sidebar.jpg";
                                    }
                                    $link = php_mixture_ui_link("sidebarLink", substr($mixture['images']['sidebar']['mediaId'], 0, strrpos($mixture['images']['sidebar']['mediaId'], ':') + 1));
                                    $title = ucwords(tpl_getConf('sidebarImg'));
                                    if ($link != null) {
                                        tpl_link(
                                            $link['target'],
                                            '<img id="mixture__sidebar_header_image" src="'.$sidebarImage.'" title="'.$link['label'].'" alt="*'.$title.'*" '.$mixture['images']['sidebar']['imageSize'][3].' />'
                                        );
                                    } else {
                                        echo '<img id="mixture__sidebar_header_image" src="'.$sidebarImage.'" title="'.$title.'" alt="*'.$title.'*" '.$mixture['images']['sidebar']['imageSize'][3].' />';
                                    }
                                }
                            ?>
                            <?php tpl_includeFile('sidebarheader.html') ?>
                            <?php tpl_include_page($hasSidebar) ?>
                            <?php tpl_includeFile('sidebarfooter.html') ?>
                        </div>
                    </aside>
                <?php endif; ?>
                <!-- ********** CONTENT ********** -->
                <div id="mixture__content" class="flex-item-fluid pam">
                    <?php tpl_flush() ?>
                    <?php tpl_includeFile('pageheader.html') ?>
                    <article class="page group">
                        <!-- wikipage start -->
                            <?php tpl_content() ?>
                        <!-- wikipage stop -->
                        <?php if (($INFO['exists']) && (tpl_getConf('pageFooterStyle') == 'mixture')) : ?>
                            <?php tpl_includeFile('pagefooter.html') ?>
                            <div id="mixture__docinfo" class="small clearfix">
                                <div class="entry-meta flex-container-h items-center justify-center">
                                    <span class="flex-container-h items-center">
                                        <?php
                                            if ($mixture['images']['editorAvatar']) {
                                                echo '<span id="mixture__editor_avatar" class="flex-container-h items-center">';
                                                    echo $mixture['images']['editorAvatar']['img'];
                                                echo '</span>';
                                            } else {
                                                echo "<span class='label glyph-24' title='".tpl_getLang('lasteditor')."'>".$mixture['glyphs']['user']."</span>";
                                            }
                                        ?>
                                        <?php echo ($INFO['editor']) ? '<bdi>'.editorinfo($INFO['editor']).'</bdi>' : $lang['external_edit']; ?>
                                    </span>
                                    <span class="flex-container-h items-center"><span class="label glyph-24" title="<?php echo tpl_getLang('lastmoddate'); ?>"><?php echo $mixture['glyphs']['calendar']; ?></span><?php echo ($INFO['lastmod']) ? '<bdi>'.dformat($INFO['lastmod']).'</bdi>' : ''; ?></span>
                                    <span class="flex-container-h items-center"><span class="label glyph-24" title="<?php echo tpl_getLang('pagepath'); ?>"><?php echo $mixture['glyphs']['folder']; ?></span>
                                        <?php
                                            $fn = $INFO['filepath'];
                                            if(!$conf['fullpath']) {
                                                if($INFO['rev']) {
                                                    $fn = str_replace($conf['olddir'].'/', '', $fn);
                                                } else {
                                                    $fn = str_replace($conf['datadir'].'/', '', $fn);
                                                }
                                            }
                                            $fn   = utf8_decodeFN($fn);
                                            echo '<bdi>'.$fn.'</bdi>';
                                        ?>
                                    </span>
                                </div>
                            </div>
                        <?php endif ?>
                    </article>
                    <!-- <hr /> -->
                    <?php tpl_flush() ?>
                    <?php tpl_includeFile('pagefooter.html') ?>
                </div><!-- /#mixture__content -->
            </main>
            <!-- ********** FOOTER ********** -->
            <footer id="mixture__footer" role="contentinfo" class="pam pt0">
                <?php tpl_includeFile('pagefooter.html') ?>
                <?php if (($INFO['exists']) && (tpl_getConf('pageFooterStyle') == 'dokuwiki')) : ?>
                    <div id="mixture__docinfo" class="small clearfix">
                        <?php tpl_pageinfo() ?>
                    </div>
                <?php endif ?>
                <?php tpl_includeFile('footerheader.html'); ?>
                <div class="tools">
                    <!-- PAGE TOOLS -->
                    <div id="dokuwiki__pagetools">
                        <h3><?php echo $lang['page_tools'] ?></h3>
                        <ul>
                            <?php
                                tpl_toolsevent('pagetools', array(
                                    'edit'      => tpl_action('edit', 1, 'li', 1),
                                    'revisions' => tpl_action('revisions', 1, 'li', 1),
                                    'backlink'  => tpl_action('backlink', 1, 'li', 1),
                                    'subscribe' => tpl_action('subscribe', 1, 'li', 1),
                                    'revert'    => tpl_action('revert', 1, 'li', 1),
                                    'top'       => tpl_action('top', 1, 'li', 1),
                                ));
                            ?>
                        </ul>
                    </div><!-- /#dokuwiki__pagetools -->
                    <!-- USER TOOLS -->
                    <?php if ($conf['useacl']): ?>
                        <div id="dokuwiki__usertools">
                            <h3><?php echo $lang['user_tools'] ?></h3>
                            <ul>
                                <?php
                                    if (!empty($_SERVER['REMOTE_USER'])) {
                                        echo '<li class="user">';
                                            tpl_userinfo();
                                        echo '</li>';
                                    }
                                ?>
                                <?php
                                    tpl_toolsevent('usertools', array(
                                        'admin'     => tpl_action('admin', 1, 'li', 1),
                                        'profile'   => tpl_action('profile', 1, 'li', 1),
                                        'register'  => tpl_action('register', 1, 'li', 1),
                                        'login'     => tpl_action('login', 1, 'li', 1),
                                    ));
                                ?>
                            </ul>
                        </div>
                    <?php endif ?>
                </div><!-- /.tools -->
                <?php tpl_license('button') ?>
                <?php tpl_includeFile('footerfooter.html'); ?>
            </footer>
            <?php tpl_includeFile('footer.html') ?>
        </div><!-- /#mixture__site -->
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
                ga('create', 'UA-XXXXXXXX-X', 'XXXXXXXXXXX.TLD');
                ga('send', 'pageview');
        </script>
        <div id="mixture__helper" class="not-visible">Mixture width: <span> </span></div><?php /* helper to detect CSS media query in script.js and eventually display it if adding `&debug=1` to url*/ ?>
        <div class="not-visible"><?php tpl_indexerWebBug() /* provide DokuWiki housekeeping, required in all templates */ ?></div>
    </body>
</html>
