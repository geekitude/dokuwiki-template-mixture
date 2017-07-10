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
                        <div class="flex-container-h flexjustifybetween">
                            <ul class="flex-container-h">
                                <?php if (strpos(tpl_getConf('elements'), 'topbar_date') !== false) : ?>
                                    <li id="mixture__topbar_date" class="camelcase">
                                        <span class="label glyph-18" title="<?php print php_mixture_date("long"); ?>">
                                            <?php echo $mixture['glyphs']['calendar']; ?>
                                        </span>
                                        <span class="text">
                                            <?php 
                                                print php_mixture_date("long");
                                            ?>
                                        </span>
                                    </li>
                                <?php endif; ?>
                                <?php if (count($mixture['recents']) >= 1) : ?>
                                    <li id="js_lastchanges_container" class="flex-container-h">
                                        <strong>
                                            <span class="label glyph-18" title="<?php echo $lang['btn_recent']; ?>">
                                                <?php echo $mixture['glyphs']['lastchanges']; ?>
                                            </span>
                                            <span class="a11y">
                                                <?php print $lang['btn_recent'] ?>:
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
                <div id="mixture__branding" class="flex-container-h flexjustifybetween">
                    <div id="mixture__branding_start" class="flex-container-h flexjustifycenter flexaligncenter">
                        <?php if ($mixture['images']['logo'] != null) : ?>
                            <div id="mixture__branding_logo">
                                <?php
                                    $logoImage = ml($mixture['images']['logo']['mediaId'],'',true);
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
                                        print '<img id="mixture__branding_logo_image" src="'.$logoImage.'" title="'.$title.'" alt="*'.$title.'*" '.$mixture['images']['logo']['imageSize'][3].' />';
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
                                <h1><?php tpl_link(wl(),$conf['title'],'accesskey="h" title="'.tpl_getLang('wikihome').' [H]"') ?></h1>
                            <?php endif; ?>
                            <?php if ($conf['tagline']): ?>
                                <?php
                                    print "<p class='tagline'>";
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
                                    print "</p>";
                                ?>
                            <?php endif ?>
                        </div>
                    </div>
                    <div id="mixture__branding_end" class="flex-container-h flexjustifycenter flexaligncenter">
                        <?php if (file_exists(tpl_incdir().'banner.html')) : ?>
                            <?php tpl_includeFile('banner.html'); ?>
                        <?php elseif ($mixture['images']['banner'] != null) : ?>
                            <div id="mixture__branding_banner">
                                <?php
                                    $bannerImage = ml($mixture['images']['banner']['mediaId'],'',true);
                                    if ($mixture['images']['banner']['mediaId'] != null) {
                                        $bannerImage = ml($mixture['images']['banner']['mediaId'],'',true);
                                    } else {
                                        $bannerImage = "/lib/tpl/mixture/images/banner.png";
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
                                        print '<img id="mixture__branding_banner_image" src="'.$bannerImage.'" title="'.$title.'" alt="*'.$title.'*" '.$mixture['images']['banner']['imageSize'][3].' />';
                                    }
                                ?>
                            </div><!-- /#mixture__branding_banner -->
                        <?php endif ?>
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
                <?php
                    if (isset($trs['dropdown'])) {
                        print $trs['dropdown'];
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
                                print "<li class='trs'>".$value."</li>";
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
                            <div style="width: 100%; text-align: center"><img src="/lib/tpl/mixture/images/sidebar.jpg" title="sample" alt="*sidebar*" width="230" height="300" /></div>
                            <?php
                                if (isset($mixture['images']['sidebar']['mediaId'])) {
                                    $sidebarImage = ml($mixture['images']['sidebar']['mediaId'],'',true);
                                    $link = php_mixture_ui_link("sidebarLink", substr($mixture['images']['sidebar']['mediaId'], 0, strrpos($mixture['images']['sidebar']['mediaId'], ':') + 1));
                                    $title = ucwords(tpl_getConf('sidebar_header'));
                                    if ($link != null) {
                                        tpl_link(
                                            $link['target'],
                                            '<img id="mixture__sidebar_header_image" src="'.$sidebarImage.'" title="'.$link['label'].'" alt="*'.$title.'*" '.$mixture['images']['sidebar']['imageSize'][3].' />'
                                        );
                                    } else {
                                        print '<img src="'.$sidebarImage.'" title="'.$title.'" alt="*'.$title.'*" '.$mixture['images']['sidebar']['imageSize'][3].' />';
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
                    </article>
                    <!-- <hr /> -->
                    <?php tpl_flush() ?>
                    <?php tpl_includeFile('pagefooter.html') ?>
                </div><!-- /#mixture__content -->
            </main>
            <!-- ********** FOOTER ********** -->
            <footer id="mixture__footer" role="contentinfo" class="pam pt0">
                <div class="pageInfo small"><span><?php tpl_pageinfo() ?></span></div>
                <?php tpl_includeFile('footerheader.html'); ?>
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
