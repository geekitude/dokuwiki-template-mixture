![Mixture - Dokuwiki template](/images/Mixture_800x160.png)
# dokuwiki-template-mixture
Experimental template based on DW's minimal Starter template, [KNACCS](http://knacss.com/) framework and [Schnaps.it](http://schnaps.it/) HTML5 template :)

* See template.info.txt for main info
* See LICENSE for license info

## Main features

* Namespace dependent CSS placeholders (mostly, or maybe only, for colors and fonts)
* Namespace dependent special images (banner, logo, sidebar header, widebanner (maybe) and a potential special last one that could be displayed in a CSS lightbox (a large portrait for example)
* Topbar with a "news ticker" showing last changes in current namespace and a potential collection of links (as presented [here](https://www.dokuwiki.org/tips:topbar))
* Google Fonts : each of main text, headings, condensed text (mostly nav bar) and monospaced text (```code``` syntax) can use a different font
* Easy to replace individual SVG glyphs collected from  [Font Awesome](http://fontawesome.io/) with [IcoMoon](https://icomoon.io/) (this doesn't include sidebar and Toc toglles, Interwiki links and Search field placehoder that can only be customized through CSS)
* Optionnal pseudo tabs proposing links to other pages of current namespace
* Sidebar can be wrapped inside main content instead of blocking a long column
* ToC and Sidebar can both be extracted from main content when there's enough room: Toc becomes fixed while Sidebar scrolls with main content as usual (will hopefully be sticky some day)
* Optional "scrollspy" ToC on wide screen
* Dokuwiki's standard include hooks (based on [this document](https://www.dokuwiki.org/include_hooks) and starter template) as well as a few additions that can be easily put in place simply renaming corresponding `.html.sample` file located in the template directory (e.g. *lib/tpl/mixture/*) into `.html`
  * *meta.html* : just before HTML head closing tag (use this to add additional styles or metaheaders)
  * *title.html* : replace default basic site title by anything you want like a multi-colors string (note that site title should allways be an html string, not an image)
  * *banner.html*(*) : replace image banner with some more dynamic and smarter HTML code
  * *header.html*(*) : right at the begining of nav area
  * *sidebarheader.html* : before *sidebar* content (but after sidebar header image)
  * *sidebarfooter.html* : after *sidebar* content
  * *pageheader.html* : below *breadcrumbs*, above the actual page content
  * *pagefooter.html* : inside Namespaced footer, below  the last changed Date
  * *footer.html* : at the very end of the page just before the body closing tag
(*) these include hooks take the place of a standard element, they are not added to default page layout

## Third Party Modules

* [KNACSS - 6.1.2](http://knacss.com/) a lightweight CSS framework based on Flexbox
* [Advanced News Ticker - 1.0.11](http://risq.github.io/jquery-advanced-news-ticker/), licensed under [GNU General Public License v2.0](https://www.gnu.org/licenses/gpl-2.0.en.html)
* [Web Font Loader - 1.6.28](https://github.com/typekit/webfontloader) to nicely load fonts from Google Web Fonts, licensed under [Apache License 2.0](https://www.apache.org/licenses/LICENSE-2.0)
* [JDENTICON - 1.4.0](https://jdenticon.com/) to add modern and highly recognizable identicons, licensed under [zlib License](https://www.zlib.net/zlib_license.html)

Font used for Mixture logo is : [RollandinEmilie by Emilie Rollandin](http://www.archistico.com/).

Special thanks to :
* Giuseppe Di Terlizzi, author of [Bootstrap3](https://www.dokuwiki.org/template:bootstrap3) DokuWiki template who nicely acepted that I copy some of his code to build admin dropdown menu.

## Tips

### SVG glyphs
  * to replace a glyph, place your SVG file, renamed exactly like original one, in ```.../conf/tpl/mixture``` folder (create it if needed)
  * IcoMoon's Font Awesome glyphs all have a height of 28px and it might give unexpected results if you replace one with another of different height
  * SVG files used should not contain a <title> node (or it will be used as glyph tooltip, overriding any Dokuwiki tooltip string), so glyphs from IcoMoon must be edited
