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
$showSidebar = page_findnearest($conf['sidebar']);
?><!doctype html>
<html class="no-js" lang="<?php echo $conf['lang'] ?>" dir="<?php echo $lang['direction'] ?>">
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
  <body id="dokuwiki__top" class="<?php echo tpl_classes();?><?php echo ($showSidebar) ? ' hasSidebar' : ''; ?>">
    <?php tpl_includeFile('header.html') ?>
    <!-- ********** HEADER ********** -->
    <header id="dokuwiki__header" role="banner" class="pam">
      <h1><?php tpl_link(wl(),$conf['title'],'accesskey="h" title="[H]"') ?></h1>
      <?php if ($conf['tagline']): ?>
        <p class="claim"><?php echo $conf['tagline'] ?></p>
      <?php endif ?>
      <p class="a11y skip">
        <a href="#dokuwiki__content"><?php echo $lang['skip_to_content'] ?></a>
      </p>
      <!-- BREADCRUMBS -->
      <?php if($conf['breadcrumbs']){ ?>
        <div class="breadcrumbs"><?php tpl_breadcrumbs() ?></div>
      <?php } ?>
      <?php if($conf['youarehere']){ ?>
        <div class="breadcrumbs"><?php tpl_youarehere() ?></div>
      <?php } ?>
      <hr />
      <!-- ALERTS -->
      <?php html_msgarea() ?>
    </header>
    <div id="dokuwiki__site" class="flex-container <?php echo ($showSidebar) ? 'hasSidebar' : ''; ?>">
      <!-- ********** ASIDE ********** -->
      <?php if ($showSidebar): ?>
        <aside id="dokuwiki__aside" class="mod pas aside">
          <h3 class="toggle"><?php echo $lang['sidebar'] ?></h3>
          <nav id="navigation" role="navigation">
            <?php tpl_includeFile('sidebarheader.html') ?>
            <?php tpl_include_page($conf['sidebar'], 1, 1) ?>
            <?php tpl_includeFile('sidebarfooter.html') ?>
            <hr class="a11y" />
          </nav>
        </aside>
	    <?php endif; ?>
      <!-- ********** CONTENT ********** -->
      <main id="dokuwiki__content" role="main" class="flex-item-fluid pam">
        <?php tpl_flush() ?>
        <?php tpl_includeFile('pageheader.html') ?>
        <article class="page group">
          <!-- wikipage start -->
          <?php tpl_content() ?>
          <!-- wikipage stop -->
        </article>
        <?php tpl_flush() ?>
        <?php tpl_includeFile('pagefooter.html') ?>
      </main>
    </div>
    <!-- ********** FOOTER ********** -->
    <footer id="dokuwiki__footer" role="contentinfo" class="pam">
      <hr />
      <div class="doc"><?php tpl_pageinfo() ?></div>
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
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
      ga('create', 'UA-XXXXXXXX-X', 'XXXXXXXXXXX.TLD');
      ga('send', 'pageview');
    </script>
    <div class="no"><?php tpl_indexerWebBug() /* provide DokuWiki housekeeping, required in all templates */ ?></div>
  </body>
</html>