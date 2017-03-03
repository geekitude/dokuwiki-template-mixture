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

  <body id="dokuwiki__top" class="<?php echo tpl_classes(); ?>">
  <header id="dokuwiki__header" role="banner" class="pam">
        <?php tpl_includeFile('header.html') ?>
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
        <?php html_msgarea() ?>
  </header>
  <div id="dokuwiki__site" class="flex-container <?php echo ($showSidebar) ? 'hasSidebar' : ''; ?>">
            <!-- ********** ASIDE ********** -->
            <?php if ($showSidebar): ?>
    <aside id="dokuwiki__aside" class="mod wsidebar pam aside">
      <nav id="navigation" role="navigation">
        <ul class="pam">
          <li class="pam inbl">Picon bière</li>
          <li class="pam inbl">Melfor</li>
          <li class="pam inbl">Carola</li>
          <li class="pam inbl">Kuglof</li>
          <li class="pam inbl">Wurscht</li>
        </ul>
      </nav>
      <p>Lorem Salu bissame ! Wie geht's les samis ? Hans apporte moi une Wurschtsalad avec un picon bitte, s'il te plaît. Voss ? Une Carola et du Melfor ? Yo dû, espèce de Knäckes, ch'ai dit un picon !</p>
    </aside>
	        <?php endif; ?>
    <aside id="dokuwiki__toc" class="mod pam aside">
      <p>Lorem Salu bissame ! Wie geht's les samis ? Hans apporte moi une Wurschtsalad avec un picon bitte, s'il te plaît. Voss ? Une Carola et du Melfor ? Yo dû, espèce de Knäckes, ch'ai dit un picon !</p>
    </aside>
    <main id="main" role="main" class="flex-item-fluid pam">
      <p>Yoo ch'ai lu dans les DNA que le Racing a encore perdu contre Oberschaeffolsheim. Verdammi et moi ch'avais donc parié deux knacks et une flammekueche. Ah so ? T'inquiète, ch'ai ramené du schpeck, du chambon, un kuglopf et du schnaps dans mon rucksack. Allez, s'guelt ! Wotch a kofee avec ton bibalaekaess et ta wurscht ? Yeuh non che suis au réchime, je ne mange plus que des Grumbeere light et che fais de la chym avec Chulien. Tiens, un rottznoz sur le comptoir.</p>
      <p>Tu restes pour le lotto-owe ce soir, y'a baeckeoffe ? Yeuh non, merci vielmols mais che dois partir à la Coopé de Truchtersheim acheter des mänele et des rossbolla pour les gamins. Hopla tchao bissame ! Consectetur adipiscing elit amet elementum nullam bissame bredele Heineken picon bière gal sed risus, condimentum Verdammi ch'ai ac réchime météor barapli s'guelt quam, non Christkindelsmärik blottkopf, Carola tellus rucksack vielmols, Gal !</p>
      <p>Yoo ch'ai lu dans les DNA que le Racing a encore perdu contre Oberschaeffolsheim. Verdammi et moi ch'avais donc parié deux knacks et une flammekueche. Ah so ? T'inquiète, ch'ai ramené du schpeck, du chambon, un kuglopf et du schnaps dans mon rucksack. Allez, s'guelt ! Wotch a kofee avec ton bibalaekaess et ta wurscht ? Yeuh non che suis au réchime, je ne mange plus que des Grumbeere light et che fais de la chym avec Chulien. Tiens, un rottznoz sur le comptoir.</p>
      <p>Tu restes pour le lotto-owe ce soir, y'a baeckeoffe ? Yeuh non, merci vielmols mais che dois partir à la Coopé de Truchtersheim acheter des mänele et des rossbolla pour les gamins. Hopla tchao bissame ! Consectetur adipiscing elit amet elementum nullam bissame bredele Heineken picon bière gal sed risus, condimentum Verdammi ch'ai ac réchime météor barapli s'guelt quam, non Christkindelsmärik blottkopf, Carola tellus rucksack vielmols, Gal !</p>
      <p>Yoo ch'ai lu dans les DNA que le Racing a encore perdu contre Oberschaeffolsheim. Verdammi et moi ch'avais donc parié deux knacks et une flammekueche. Ah so ? T'inquiète, ch'ai ramené du schpeck, du chambon, un kuglopf et du schnaps dans mon rucksack. Allez, s'guelt ! Wotch a kofee avec ton bibalaekaess et ta wurscht ? Yeuh non che suis au réchime, je ne mange plus que des Grumbeere light et che fais de la chym avec Chulien. Tiens, un rottznoz sur le comptoir.</p>
      <p>Tu restes pour le lotto-owe ce soir, y'a baeckeoffe ? Yeuh non, merci vielmols mais che dois partir à la Coopé de Truchtersheim acheter des mänele et des rossbolla pour les gamins. Hopla tchao bissame ! Consectetur adipiscing elit amet elementum nullam bissame bredele Heineken picon bière gal sed risus, condimentum Verdammi ch'ai ac réchime météor barapli s'guelt quam, non Christkindelsmärik blottkopf, Carola tellus rucksack vielmols, Gal !</p>
      <p>Yoo ch'ai lu dans les DNA que le Racing a encore perdu contre Oberschaeffolsheim. Verdammi et moi ch'avais donc parié deux knacks et une flammekueche. Ah so ? T'inquiète, ch'ai ramené du schpeck, du chambon, un kuglopf et du schnaps dans mon rucksack. Allez, s'guelt ! Wotch a kofee avec ton bibalaekaess et ta wurscht ? Yeuh non che suis au réchime, je ne mange plus que des Grumbeere light et che fais de la chym avec Chulien. Tiens, un rottznoz sur le comptoir.</p>
      <p>Tu restes pour le lotto-owe ce soir, y'a baeckeoffe ? Yeuh non, merci vielmols mais che dois partir à la Coopé de Truchtersheim acheter des mänele et des rossbolla pour les gamins. Hopla tchao bissame ! Consectetur adipiscing elit amet elementum nullam bissame bredele Heineken picon bière gal sed risus, condimentum Verdammi ch'ai ac réchime météor barapli s'guelt quam, non Christkindelsmärik blottkopf, Carola tellus rucksack vielmols, Gal !</p>
      <p>Yoo ch'ai lu dans les DNA que le Racing a encore perdu contre Oberschaeffolsheim. Verdammi et moi ch'avais donc parié deux knacks et une flammekueche. Ah so ? T'inquiète, ch'ai ramené du schpeck, du chambon, un kuglopf et du schnaps dans mon rucksack. Allez, s'guelt ! Wotch a kofee avec ton bibalaekaess et ta wurscht ? Yeuh non che suis au réchime, je ne mange plus que des Grumbeere light et che fais de la chym avec Chulien. Tiens, un rottznoz sur le comptoir.</p>
      <p>Tu restes pour le lotto-owe ce soir, y'a baeckeoffe ? Yeuh non, merci vielmols mais che dois partir à la Coopé de Truchtersheim acheter des mänele et des rossbolla pour les gamins. Hopla tchao bissame ! Consectetur adipiscing elit amet elementum nullam bissame bredele Heineken picon bière gal sed risus, condimentum Verdammi ch'ai ac réchime météor barapli s'guelt quam, non Christkindelsmärik blottkopf, Carola tellus rucksack vielmols, Gal !</p>
    </main>
  </div>
  <footer id="footer" role="contentinfo" class="pam">
    Lorem Elsass ipsum lacus leverwurscht Wurschtsalad mamsell Gal. gewurztraminer turpis, suspendisse commodo Oberschaeffolsheim ornare aliquam semper Miss Dahlias Mauris turpis sagittis kuglopf eleifend dignissim baeckeoffe geht's Richard Schirmeck mollis habitant schnaps ante et sit leo schpeck sit Salu bissame Salut bisamme varius quam. amet elementum nullam bissame bredele Heineken picon bière gal sed risus, condimentum Verdammi ch'ai ac réchime météor barapli s'guelt quam, non Christkindelsmärik blottkopf, Carola tellus rucksack vielmols, Gal !
  </footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-XXXXXXXX-X', 'XXXXXXXXXXX.TLD');
      ga('send', 'pageview');
    </script>
  </body>
</html>