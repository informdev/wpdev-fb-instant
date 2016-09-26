<?php
/**
Grab Plugin Variables
**/
global $wpdevfbia_numposts;
global $wpdevfbia_enableads;
global $wpdevfbia_ad_1;
global $wpdevfbia_ad_2;
global $wpdevfbia_ad_3;
global $wpdevfbia_ad_4;
global $wpdevfbia_enableanalytics;
global $wpdevfbia_analyticsid;
global $wpdevfbia_grouptrack;

/**
Setup Plugin Variables
**/
// Create Post Number
if(!empty($wpdevfbia_numposts)) {
  $postnums = $wpdevfbia_numposts;
} else {
  $postnums = 10;
}

// Create Ad array
$fbadid = array();
if(!empty($wpdevfbia_ad_1)) {
  $fbadid[] = $wpdevfbia_ad_1;
}
if (!empty($wpdevfbia_ad_2)) {
  $fbadid[] = $wpdevfbia_ad_2;
}
if (!empty($wpdevfbia_ad_3)) {
  $fbadid[] = $wpdevfbia_ad_3;
}
if (!empty($wpdevfbia_ad_4)) {
  $fbadid[] = $wpdevfbia_ad_4;
}

header('Content-Type: '.feed_content_type('rss-http').'; charset='.get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/">
<?php do_action('rss2_ns'); ?>
<channel>
        <title><?php bloginfo_rss('name'); ?></title>
        <link><?php bloginfo_rss('url'); ?></link>
        <description><?php bloginfo_rss('description'); ?></description>
        <lastBuildDate><?php echo date('Y-m-d') . 'T' . date('G:i:s+00:00'); ?></lastBuildDate>
        <language>en</language>
        <?php // WP_Query arguments
          $args = array (
          	'post_type'              => array( 'post' ),
          	'post_status'            => array( 'publish' ),
          	'nopaging'               => false,
          	'posts_per_page'         => $postnums,
          	'ignore_sticky_posts'    => true,
          	'order'                  => 'DESC',
          	'orderby'                => 'date',
          	'meta_query'             => array(
          		array(
          			'key'       => 'wpdev_fb_instant_enable_article_for_instant_articles',
          			'value'     => 'enable-article-for-instant-articles',
          		),
          	),
          );

          // The Query
          $instantarticles = new WP_Query( $args );

          // The Loop
          if ( $instantarticles->have_posts() ) {
          	while ( $instantarticles->have_posts() ) {
          		$instantarticles->the_post();

              // Setup Items
              $gettitle = get_the_title();
              $title = apply_filters( 'the_title_rss', $gettitle );
              $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
              // Get the Content
              $getcontent = get_the_content();
              $pcontent = wpautop( $getcontent );
              $entities = array(
              "&nbsp;",
              "&lt;",
              "&gt;",
              "&amp;",
              "&cent;",
              "&pound;",
              "&yen;",
              "&euro;",
              "&copy;",
              "&reg;",
              );
              $entitycontent = str_replace('', '&nbsp;', $pcontent);
              // Clean the Content
              $patternclean = array(
                "/<abrr.*?>.*?<\/abbr>/",
                "/<acronym.*?>.*?<\/acronym>/",
                "/<applet.*?>.*?<\/applet>/",
                "/<area.*?>.*?<\/applet>/",
                "/<audio.*?>.*?<\/audio>/",
                "/<base.*?>.*?<\/base>/",
                "/<basefont.*?>.*?<\/basefont>/",
                "/<bdi.*?>.*?<\/bdi>/",
                "/<bdo.*?>.*?<\/bdo>/",
                "/<big.*?>.*?<\/big>/",
                "/<br.*?>/",
                "/<button.*?>.*?<\/button>/",
                "/<canvas.*?>.*?<\/canvas>/",
                "/<col.*?>.*?<\/col>/",
                "/<colgroup.*?>.*?<\/colgroup>/",
                "/<datalist.*?>.*?<\/datalist>/",
                "/<dd.*?>.*?<\/dd>/",
                "/<details.*?>.*?<\/details>/",
                "/<dfn.*?>.*?<\/dfn>/",
                "/<dialog.*?>.*?<\/dialog>/",
                "/<dir.*?>.*?<\/dir>/",
                "/<div.*?>.*?<\/div>/",
                "/<dl.*?>.*?<\/dl>/",
                "/<dt.*?>.*?<\/dt>/",
                "/<embed.*?>.*?<\/embed>/",
                "/<fieldset.*?>.*?<\/fieldset>/",
                "/<font.*?>.*?<\/font>/",
                "/<footer.*?>.*?<\/footer>/",
                "/<form.*?>.*?<\/form>/",
                "/<frame.*?>.*?<\/frame>/",
                "/<frameset.*?>.*?<\/frameset>/",
                "/<hr.*?>/",
                "/<html.*?>.*?<\/html>/",
                "/<i.*?>.*?<\/i>/",
                "/<ins.*?>.*?<\/ins>/",
                "/<kbd.*?>.*?<\/kbd>/",
                "/<keygen.*?>.*?<\/keygen>/",
                "/<legend.*?>.*?<\/legend>/",
                "/<link.*?>.*?<\/link>/",
                "/<main.*?>.*?<\/main>/",
                "/<map.*?>.*?<\/map>/",
                "/<mark.*?>.*?<\/mark>/",
                "/<menu.*?>.*?<\/menu>/",
                "/<menuitem.*?>.*?<\/menuitem>/",
                "/<meta.*?>.*?<\/meta>/",
                "/<meter.*?>.*?<\/meter>/",
                "/<nav.*?>.*?<\/nav>/",
                "/<noframes.*?>.*?<\/noframes>/",
                "/<noscript.*?>.*?<\/noscript>/",
                "/<object.*?>.*?<\/object>/",
                "/<optgroup.*?>.*?<\/optgroup>/",
                "/<option.*?>.*?<\/option>/",
                "/<output.*?>.*?<\/output>/",
                "/<param.*?>.*?<\/param>/",
                "/<progress.*?>.*?<\/progress>/",
                "/<q.*?>.*?<\/q>/",
                "/<rp.*?>.*?<\/rp>/",
                "/<rt.*?>.*?<\/rt>/",
                "/<ruby.*?>.*?<\/ruby>/",
                "/<s.*?>.*?<\/s>/",
                "/<samp.*?>.*?<\/samp>/",
                "/<section.*?>.*?<\/section>/",
                "/<select.*?>.*?<\/select>/",
                "/<strike.*?>.*?<\/strike>/",
                "/<style.*?>.*?<\/style>/",
                "/<sub.*?>.*?<\/sub>/",
                "/<summary.*?>.*?<\/summary>/",
                "/<sup.*?>.*?<\/sup>/",
                "/<table.*?>.*?<\/table>/",
                "/<tbody.*?>.*?<\/tbody>/",
                "/<td.*?>.*?<\/td>/",
                "/<textarea.*?>.*?<\/textarea>/",
                "/<tfoot.*?>.*?<\/tfoot>/",
                "/<th.*?>.*?<\/th>/",
                "/<thead.*?>.*?<\/thead>/",
                "/<time.*?>.*?<\/time>/",
                "/<title.*?>.*?<\/title>/",
                "/<tr.*?>.*?<\/tr>/",
                "/<track.*?>.*?<\/track>/",
                "/<tt.*?>.*?<\/tt>/",
                "/<var.*?>.*?<\/var>/",
                "/<wbr.*?>.*?<\/wbr>/",
                "/&mdash;/",
                "/(<strong.*?>)(.*?)(<\/strong.*?>)/",
                "/(<b.*?>)(.*?)(<\/b.*?>)/",
                "/(<address.*?>)(.*?)(<\/address.*?>)/",
                "/(<caption.*?>)(.*?)(<\/caption.*?>)/",
                "/(<center.*?>)(.*?)(<\/center.*?>)/",
                "/(<cite.*?>)(.*?)(<\/cite.*?>)/",
                "/(<code.*?>)(.*?)(<\/code.*?>)/",
                "/(<del.*?>)(.*?)(<\/del.*?>)/",
                "/(<em.*?>)(.*?)(<\/em.*?>)/",
                "/(<label.*?>)(.*?)(<\/label.*?>)/",
                "/(<pre.*?>)(.*?)(<\/pre.*?>)/",
                "/(<small.*?>)(.*?)(<\/small.*?>)/",
                "/(<source.*?>)(.*?)(<\/source.*?>)/",
                "/(<span.*?>)(.*?)(<\/span.*?>)/",
                "/(<u.*?>)(.*?)(<\/u.*?>)/",
                "/(<h3.*?>)(.*?)(<\/h3.*?>)/",
                "/(<h4.*?>)(.*?)(<\/h4.*?>)/",
                "/(<h5.*?>)(.*?)(<\/h5.*?>)/",
                "/(<h6.*?>)(.*?)(<\/h6.*?>)/",
                "/(<p.*?\"?>)/",
                "/(<p><a.*?><img.*src=\")(.*?)(\".*\/><\/a><\/p>)/",
                "/(<p><img.*?src=\")(.*?)(\".*?\/><\/p>)/",
                "/(<p>\[caption.*?\]<a.*?\"><img.*?src=\")(.*?)(\".*?<\/a>)(.*?)(\[\/caption.*?<\/p>)/",
                "/(<p>\[caption.*?\]<img.*?src=\")(.*?)(\".*?\/>)(.*?)(\[\/caption.*?<\/p>)/",
                "/(<p.*?>\[.*?\]<\/p>)/",
                "/(<p><iframe.*?src=\")(.*?vine\.co.*?)(\".*?><\/iframe><script.*?<\/script><\/p>)/",
                "/(<blockquote.*?twitter-tweet\"|<blockquote.*?instagram-media\")/",
                "/(<p><script.*?src=\")(.*?platform.instagram.*?|.*?platform.twitter.*?)(\".*?<\/script><\/p>)/",
                "/(<p>)(https.*?twitter.*?)(<\/p>)/",
                "/(<p><iframe.*?src=\")(.*?youtu.*?)(\".*?<\/iframe><\/p>)/",
                "/(<p>)(.*?youtube.*?)(<\/p>)/",
		            "/(<p>)(.*?youtu..*?\/)(.*?)(<\/p>)/",
                "/(<p><\/p>)/",
              );
              $replaceclean = array(
                "", // Remove abrr
                "", // Remove acronym
                "", // Remove applet
                "", // Remove area
                "", // Remove audio
                "", // Remove base
                "", // Remove basefont
                "", // Remove bdi
                "", // Remove bdo
                "", // Remove big
                "", // Remove br
                "", // Remove button
                "", // Remove canvas
                "", // Remove col
                "", // Remove colgroup
                "", // Remove datalist
                "", // Remove dd
                "", // Remove details
                "", // Remove dfn
                "", // Remove dialog
                "", // Remove dir
                "", // Remove div
                "", // Remove dl
                "", // Remove dt
                "", // Remove embed
                "", // Remove fieldset
                "", // Remove font
                "", // Remove footer
                "", // Remove form
                "", // Remove frame
                "", // Remove frameset
                "", // Remove hr
                "", // Remove html
                "", // Remove i
                "", // Remove ins
                "", // Remove kbd
                "", // Remove keygen
                "", // Remove legend
                "", // Remove link
                "", // Remove main
                "", // Remove map
                "", // Remove mark
                "", // Remove menu
                "", // Remove menuitem
                "", // Remove meta
                "", // Remove meter
                "", // Remove nav
                "", // Remove noframes
                "", // Remove noscript
                "", // Remove object
                "", // Remove optgroup
                "", // Remove option
                "", // Remove output
                "", // Remove param
                "", // Remove progress
                "", // Remove q
                "", // Remove rp
                "", // Remove rt
                "", // Remove ruby
                "", // Remove s
                "", // Remove samp
                "", // Remove section
                "", // Remove select
                "", // Remove strike
                "", // Remove style
                "", // Remove sub
                "", // Remove summary
                "", // Remove sup
                "", // Remove table
                "", // Remove tbody
                "", // Remove td
                "", // Remove textarea
                "", // Remove tfoot
                "", // Remove th
                "", // Remove thead
                "", // Remove time
                "", // Remove title
                "", // Remove tr
                "", // Remove track
                "", // Remove tt
                "", // Remove var
                "", // Remove wbr
                "", // Remove &mdash;
                "$2", // Keep Content, Remove strong
                "$2", // Keep Content, Remove b
                "$2", // Keep Content, Remove address
                "$2", // Keep Content, Remove caption
                "$2", // Keep Content, Remove center
                "$2", // Keep Content, Remove cite
                "$2", // Keep Content, Remove code
                "$2", // Keep Content, Remove del
                "$2", // Keep Content, Remove em
                "$2", // Keep Content, Remove label
                "$2", // Keep Content, Remove pre
                "$2", // Keep Content, Remove small
                "$2", // Keep Content, Remove source
                "$2", // Keep Content, Remove span
                "$2", // Keep Content, Remove u
                "<p>$2</p>", // Replace H3
                "<p>$2</p>", // Replace H4
                "<p>$2</p>", // Replace H5
                "<p>$2</p>", // Replace H6 https://www.youtube.com/watch?v=ztmF73bri_s
                "<p>", // Remove Classes in p
                "<figure><img src=\"$2\" /></figure>", // Setup img with link
                "<figure><img src=\"$2\" /></figure>", // Setup img without link
                "<figure><img src=\"$2\" /><figcaption class=\"op-vertical-below\"><cite class=\"op-vertical-below op-center\">$4</cite></figcaption></figure>", // Setup img with caption and with link
                "<figure><img src=\"$2\" /><figcaption class=\"op-vertical-below\"><cite class=\"op-vertical-below op-center\">$4</cite></figcaption></figure>", // Setup img with caption and without link
                "", // Remove shortcodes
                "<figure class=\"op-interactive\"><iframe src=\"$2\" width=\"600\" height=\"600\"></iframe></figure>", // Vine embed
                "<figure class=\"op-interactive\"><iframe>$0", // Instagram/Twitter embed start
                "$0</iframe></figure>", // Instagram/Twitter embed end
                "<figure class=\"op-social\"><iframe><blockquote class=\"twitter-tweet\" data-lang=\"en\"><p lang=\"en\" dir=\"ltr\"><a href=\"$2\"></a></blockquote>
                <script async src=\"//platform.twitter.com/widgets.js\" charset=\"utf-8\"></script></iframe></figure>", // Twitter Link Embed
                "<figure class=\"op-interactive\"><iframe width=\"560\" height=\"315\" src=\"$2\"></iframe></figure>", // YouTube embed
                "<figure class=\"op-interactive\"><iframe width=\"560\" height=\"315\" src=\"$2\"></iframe></figure>", // YouTube embed link
            	  "<figure class=\"op-interactive\"><iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/watch?v=$3\"></iframe></figure>", // YouTube embed link
                "", // Remove empty p
              );
              $cleanedcontent = preg_replace($patternclean, $replaceclean, $entitycontent);
              ?>
              <item>
                    <title><?php echo $title; ?></title>
                    <link><?php echo get_permalink(); ?></link>
                    <guid><?php echo wp_get_shortlink(); ?></guid>
                    <pubDate><?php echo get_the_date('Y-m-d') . 'T' . get_the_date('G:i:s+00:00'); ?></pubDate>
                    <author><?php echo get_the_author(); ?></author>
                    <description><?php echo get_the_post_thumbnail(); ?>
                      <?php echo wp_trim_words( $cleanedcontent, 40, '...' ); ?>
                    </description>
                    <content:encoded>
                      <![CDATA[
                        <!doctype html>
                            <html lang="en" prefix="op: http://media.facebook.com/op#">
                            <head>
                              <meta charset="utf-8">
                              <link rel="canonical" href="<?php echo get_permalink(); ?>">
                              <meta property="op:markup_version" content="v1.0">
                              <meta property="fb:use_automatic_ad_placement" content="true">
                            </head>
                            <body>
                                <article>
                                    <header>
                                        <!-- title -->
                        				            <h1><?php echo $title; ?></h1>

                                        <!-- publication date/time -->
                        				            <time class="op-published" datetime="<?php echo get_the_date('Y-m-d') . 'T' . get_the_date('G:i:s+00:00'); ?>"><?php echo get_the_date('M j, Y, g:i a'); ?></time>

                        				        <!-- modification date/time -->
                        				            <time class="op-modified" datetime="<?php echo get_the_modified_date('Y-m-d') . 'T' . get_the_modified_date('G:i:s+00:00'); ?>"><?php echo get_the_modified_date('M j, Y, g:i a'); ?></time>

                        				        <!-- author(s) -->
                                            <address>
                                                <a><?php echo get_the_author(); ?></a>
                                                <?php echo get_the_author_meta('description'); ?>
                                            </address>

                        				        <!-- cover -->
                        				            <figure>
                                                <img src="<?php echo $image[0]; ?>" />
                                            </figure>
                                        <?php if(!empty($wpdevfbia_enableads) && !empty($fbadid)) { ?>
                        				        <!-- Advertisements -->
                                            <section class="op-ad-template">
                                              <?php if(!empty($fbadid)) {
                                                  $count = 0;
                                                  foreach($fbadid as $fbid) {
                                                    $count++;
                                                    if($count == 1) {
                                                      $addefault = ' op-ad-default';
                                                    } else {
                                                      $addefault = '';
                                                    } ?>

                                                    <figure class="op-ad<?php echo $addefault; ?>">
                                                        <iframe width="300" height="250" style="border:0; margin:0;" src="https://www.facebook.com/adnw_request?placement=<?php echo $fbid; ?>&adtype=banner300x250"></iframe>
                                                    </figure>

                                                  <?php }
                                                }?>
                                            </section>
                                          <?php } ?>
                                        </header>
                                    <!-- body -->
                                    <?php echo $cleanedcontent; ?>
                                    <!-- Google Analytics -->
                                        <?php if(!empty($wpdevfbia_enableanalytics) && !empty($wpdevfbia_analyticsid)) { ?>
                                        <figure class="op-tracker">
                                            <iframe>
                                              <script>
                                                (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                                                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                                                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                                                })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

                                                ga('create', '<?php echo $wpdevfbia_analyticsid; ?>', 'auto');
                                                <?php if(!empty($wpdevfbia_grouptrack)) { ?>
                                                ga('set', 'contentGroup1', 'Instant Articles');
                                                <?php } ?>
                                                ga('send', 'pageview');
                                              </script>
                                            </iframe>
                                        </figure>
                                        <?php } ?>
                                        <footer>
                                          <small>Copyright <?php echo date('Y'); ?> <a href="<?php echo get_bloginfo('url'); ?>"><?php echo get_bloginfo('name'); ?></a></small>
                                        </footer>
                                </article>
                            </body>
                        </html>            ]]>
                  </content:encoded>
            </item>
          	<?php }
          } else {
          	echo 'No posts.';
          }

          // Restore original Post Data
          wp_reset_postdata(); ?>
</channel>
</rss>
