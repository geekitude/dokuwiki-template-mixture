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
  <body id="dokuwiki__top" class="<?php echo tpl_classes();?><?php echo ($showSidebar) ? ' showSidebar' : ''; ?><?php echo php_mixture_classes();?>">
    <div id="mixture__site">
      <?php tpl_includeFile('header.html') ?>
      <!-- ********** HEADER ********** -->
      <header id="mixture__header" role="banner" class="pam">
        <!-- TOPBAR (with date & last changes) -->
        <?php if ((strpos(tpl_getConf('elements'), 'news_date') !== false) or (strpos(tpl_getConf('elements'), 'news_lastchanges') !== false) or (strpos(tpl_getConf('elements'), 'news_links') !== false)) : ?>
            <div id="mixture__topbar" class="small clearfix">
                <div class="left">
                    <ul>
                        <?php if (strpos(tpl_getConf('elements'), 'news_date') !== false) : ?>
                            <li id="mixture__topbar_date" class="camelcase">
                                <span>
                                    <?php
                                        print php_mixture_date("long");
                                    ?>
                                </span>
                            </li>
                        <?php endif; ?>
                        <?php if (count($mixture['recents']) >= 1) : ?>
                            <li id="js_lastchanges_container">
                                <strong>
                                    <span class="glyph" title="<?php echo $lang['btn_recent'] ?>">
                                        <?php echo $mixture['glyphs']['feed']; ?>
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
                            </li><!-- #js_lastchanges_container -->
                        <?php endif; ?>
                    </ul>
                </div>
                <?php if ($colormag['topbarLinks'] != null) : ?>
                    <div class="right">
                        <?php echo $colormag['topbarLinks']; ?>
                    </div>
                <?php endif ?>
                <hr class="mt0 mb0" />
            </div><!-- #mixture__topbar -->
        <?php endif; ?>
        <?php if ((tpl_getConf('dynamicBranding') == 1) && ($ID<>'start') && ($ACT=='show')): ?>
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
          <?php if (($ID<>'start') && ($ACT=='show')): ?>
            <p class="tagline"><?php echo $conf['title'] ?></p>
          <?php else: ?>
            <p class="tagline"><?php echo $conf['tagline'] ?></p>
          <?php endif; ?>
        <?php endif ?>
        <p class="a11y skip">
          <a href="#mixture__content"><?php echo $lang['skip_to_content'] ?></a>
        </p>
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
        <aside id="mixture__pageIdInfo" class="flex-container-h pam pt0 pb0">
          <div class="pageId small"><span><?php echo hsc($ID) ?></span></div>
          <div class="pageInfo small"><span><?php tpl_pageinfo() ?></span></div>
          <hr />
        </aside>
      </header>
      <main role="main" class="<?php echo (tpl_getConf('wrappedSidebar') == 0) ? 'flex-container' : ''; ?>">
        <!-- ********** ASIDE ********** -->
        <?php if ($showSidebar) : ?>
          <aside id="mixture__aside" class="mod aside">
            <h3 class="toggle"><?php echo $lang['sidebar'] ?></h3>
            <div class="content" role="complementary">
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
          <hr />
          <?php tpl_flush() ?>
          <?php tpl_includeFile('pagefooter.html') ?>
        </div>
      </main>
      <!-- ********** FOOTER ********** -->
      <footer id="mixture__footer" role="contentinfo" class="pam pt0">
        <div class="tools">
          <!-- SITE TOOLS -->
          <div id="dokuwiki__sitetools">
            <h3><?php echo $lang['site_tools'] ?></h3>
            <?php tpl_searchform() ?>
            <ul>
              <?php
                tpl_toolsevent('sitetools', array(
                  'recent'    => tpl_action('recent', 1, 'li', 1),
                  'media'     => tpl_action('media', 1, 'li', 1),
                  'index'     => tpl_action('index', 1, 'li', 1),
                ));
              ?>
            </ul>
          </div>
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
          </div>
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
        </div>
        <?php tpl_license('button') ?>
      </footer>
      <?php tpl_includeFile('footer.html') ?>
    </div><!-- /#dokuwiki__site -->
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
