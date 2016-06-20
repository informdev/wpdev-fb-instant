<?php
/*
Plugin Name: WP Developers | Facebook Instant Articles
Plugin URI: http://wpdevelopers.com
Description: Take advantage of Facebook's Instant Articles.
Version: 1.0.3
Author: Tyler Johnson
Author URI: http://tylerjohnsondesign.com/
Copyright: Tyler Johnson
Text Domain: wpdevfbinstant
Copyright 2016 WP Developers. All Rights Reserved.
*/

/**
Plugin Activation & Deactivation
**/

// Create New Feed
function wpdev_fb_instant_feed() {
    add_feed('instant', 'wpdev_fb_instant_feed_template');
}
add_action('init', 'wpdev_fb_instant_feed');

// Feed Template
function wpdev_fb_instant_feed_template() {
    include (plugin_dir_path(__FILE__) . 'templates/wpdev-fb-instant-feed.php');
}

// On Activation Flush Permalinks
function wpdev_fb_instant_activate() {
  // Trigger Feed Creation
  wpdev_fb_instant_feed();

  // Trigger Permalink Flush
  flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'wpdev_fb_instant_activate');

// On Deactivation Flush Permalinks
function wpdev_fb_instant_deactivate() {
    // Trigger Permalink Flush
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'wpdev_fb_instant_deactivate');

/**
Plugin updates
**/
require 'plugin-update-checker/plugin-update-checker.php';
$wpdevClassName = PucFactory::getLatestClassVersion('PucGitHubChecker');
$wpdevUpdateChecker = new $wpdevClassName(
    'https://github.com/LibertyAllianceGit/wpdev-fb-instant',
    __FILE__,
    'master'
);
$wpdevUpdateChecker->setAccessToken('4921ce230f2bd252dd1fafc7afeac812ddf091de');

/**
Enqueue Plugin Files
**/
function wpdev_fb_instant_files() {
        wp_enqueue_style( 'wpdev-fb-instant-admin-css', plugin_dir_url(__FILE__) . 'css/wpdev-fb-instant-admin-css.css' );
}
add_action('admin_enqueue_scripts', 'wpdev_fb_instant_files');

/**
Create Meta Box
**/

// Get Meta Information
function wpdev_fb_instant_get_meta( $value ) {
	global $post;

	$field = get_post_meta( $post->ID, $value, true );
	if ( ! empty( $field ) ) {
		return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
	} else {
		return false;
	}
}

// Add Meta Box
function wpdev_fb_instant_add_meta_box() {
	add_meta_box(
		'wpdev_fb_instant-wpdevelopers-facebook-instant-articles',
		__( 'Instant Articles', 'wpdev_fb_instant' ),
		'wpdev_fb_instant_html',
		'post',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'wpdev_fb_instant_add_meta_box' );

// Create HTML Output
function wpdev_fb_instant_html( $post) {
	wp_nonce_field( '_wpdev_fb_instant_nonce', 'wpdev_fb_instant_nonce' ); ?>
	<p><input type="checkbox" name="wpdev_fb_instant_enable_article_for_instant_articles" id="wpdev_fb_instant_enable_article_for_instant_articles" value="enable-article-for-instant-articles" <?php echo ( wpdev_fb_instant_get_meta( 'wpdev_fb_instant_enable_article_for_instant_articles' ) === 'enable-article-for-instant-articles' ) ? 'checked' : ''; ?>>
		<label for="wpdev_fb_instant_enable_article_for_instant_articles"><?php _e( 'Enable Article for Instant Articles', 'wpdev_fb_instant' ); ?></label>	</p><?php
}

// Save Information
function wpdev_fb_instant_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! isset( $_POST['wpdev_fb_instant_nonce'] ) || ! wp_verify_nonce( $_POST['wpdev_fb_instant_nonce'], '_wpdev_fb_instant_nonce' ) ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['wpdev_fb_instant_enable_article_for_instant_articles'] ) )
		update_post_meta( $post_id, 'wpdev_fb_instant_enable_article_for_instant_articles', esc_attr( $_POST['wpdev_fb_instant_enable_article_for_instant_articles'] ) );
	else
		update_post_meta( $post_id, 'wpdev_fb_instant_enable_article_for_instant_articles', null );
}
add_action( 'save_post', 'wpdev_fb_instant_save' );

/**
Create Options Page
**/

class WPDevFBIA {
	private $wpdev_fbia_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'wpdev_fbia_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'wpdev_fbia_page_init' ) );
	}

	public function wpdev_fbia_add_plugin_page() {
		add_menu_page(
			'WPDev FBIA', // page_title
			'WPDev FBIA', // menu_title
			'manage_options', // capability
			'wpdev-fbia', // menu_slug
			array( $this, 'wpdev_fbia_create_admin_page' ), // function
			'dashicons-smartphone', // icon_url
			100 // position
		);
	}

	public function wpdev_fbia_create_admin_page() {
		$this->wpdev_fbia_options = get_option( 'wpdev_fbia_option_name' ); ?>

		<div class="wpdev-fbia-wrap wrap">
			<img src="<?php echo plugin_dir_url(__FILE__) . 'admin/wpdev-fbia-logo.png'; ?>" />
      <hr class="wpdev-hr"/>
			<p>Thank you for enabling the WPDev Facebook Instant Articles plugin! You can find your Instant Articles feed <a href="<?php echo get_bloginfo('url'); ?>/feed/instant" target="_blank">HERE<a/>.</p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'wpdev_fbia_option_group' );
					do_settings_sections( 'wpdev-fbia-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function wpdev_fbia_page_init() {
		register_setting(
			'wpdev_fbia_option_group', // option_group
			'wpdev_fbia_option_name', // option_name
			array( $this, 'wpdev_fbia_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'wpdev_fbia_setting_section', // id
			'Settings', // title
			array( $this, 'wpdev_fbia_section_info' ), // callback
			'wpdev-fbia-admin' // page
		);

		add_settings_field(
			'number_of_posts_0', // id
			'Number of Posts', // title
			array( $this, 'number_of_posts_0_callback' ), // callback
			'wpdev-fbia-admin', // page
			'wpdev_fbia_setting_section' // section
		);

		add_settings_field(
			'enable_ads_1', // id
			'Enable Ads', // title
			array( $this, 'enable_ads_1_callback' ), // callback
			'wpdev-fbia-admin', // page
			'wpdev_fbia_setting_section' // section
		);

		add_settings_field(
			'ad_id_1_2', // id
			'Ad ID 1', // title
			array( $this, 'ad_id_1_2_callback' ), // callback
			'wpdev-fbia-admin', // page
			'wpdev_fbia_setting_section' // section
		);

		add_settings_field(
			'ad_id_2_3', // id
			'Ad ID 2', // title
			array( $this, 'ad_id_2_3_callback' ), // callback
			'wpdev-fbia-admin', // page
			'wpdev_fbia_setting_section' // section
		);

		add_settings_field(
			'ad_id_3_4', // id
			'Ad ID 3', // title
			array( $this, 'ad_id_3_4_callback' ), // callback
			'wpdev-fbia-admin', // page
			'wpdev_fbia_setting_section' // section
		);

    add_settings_field(
			'ad_id_4_5', // id
			'Ad ID 4', // title
			array( $this, 'ad_id_4_5_callback' ), // callback
			'wpdev-fbia-admin', // page
			'wpdev_fbia_setting_section' // section
		);

		add_settings_field(
			'enable_analytics_5', // id
			'Enable Analytics', // title
			array( $this, 'enable_analytics_5_callback' ), // callback
			'wpdev-fbia-admin', // page
			'wpdev_fbia_setting_section' // section
		);

		add_settings_field(
			'analytics_id_6', // id
			'Analytics ID', // title
			array( $this, 'analytics_id_6_callback' ), // callback
			'wpdev-fbia-admin', // page
			'wpdev_fbia_setting_section' // section
		);

		add_settings_field(
			'enable_instant_articles_group_tracking_7', // id
			'Enable IA Group Tracking', // title
			array( $this, 'enable_instant_articles_group_tracking_7_callback' ), // callback
			'wpdev-fbia-admin', // page
			'wpdev_fbia_setting_section' // section
		);
	}

	public function wpdev_fbia_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['number_of_posts_0'] ) ) {
			$sanitary_values['number_of_posts_0'] = sanitize_text_field( $input['number_of_posts_0'] );
		}

		if ( isset( $input['enable_ads_1'] ) ) {
			$sanitary_values['enable_ads_1'] = $input['enable_ads_1'];
		}

		if ( isset( $input['ad_id_1_2'] ) ) {
			$sanitary_values['ad_id_1_2'] = sanitize_text_field( $input['ad_id_1_2'] );
		}

		if ( isset( $input['ad_id_2_3'] ) ) {
			$sanitary_values['ad_id_2_3'] = sanitize_text_field( $input['ad_id_2_3'] );
		}

		if ( isset( $input['ad_id_3_4'] ) ) {
			$sanitary_values['ad_id_3_4'] = sanitize_text_field( $input['ad_id_3_4'] );
		}

    if ( isset( $input['ad_id_4_5'] ) ) {
			$sanitary_values['ad_id_4_5'] = sanitize_text_field( $input['ad_id_4_5'] );
		}

		if ( isset( $input['enable_analytics_5'] ) ) {
			$sanitary_values['enable_analytics_5'] = $input['enable_analytics_5'];
		}

		if ( isset( $input['analytics_id_6'] ) ) {
			$sanitary_values['analytics_id_6'] = sanitize_text_field( $input['analytics_id_6'] );
		}

		if ( isset( $input['enable_instant_articles_group_tracking_7'] ) ) {
			$sanitary_values['enable_instant_articles_group_tracking_7'] = $input['enable_instant_articles_group_tracking_7'];
		}

		return $sanitary_values;
	}

	public function wpdev_fbia_section_info() {

	}

	public function number_of_posts_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="wpdev_fbia_option_name[number_of_posts_0]" id="number_of_posts_0" value="%s">',
			isset( $this->wpdev_fbia_options['number_of_posts_0'] ) ? esc_attr( $this->wpdev_fbia_options['number_of_posts_0']) : ''
		);
	}

	public function enable_ads_1_callback() {
		printf(
			'<input type="checkbox" name="wpdev_fbia_option_name[enable_ads_1]" id="enable_ads_1" value="enable_ads_1" %s> <label for="enable_ads_1">Enable Facebook ads. Ad placement will be automatic.</label>',
			( isset( $this->wpdev_fbia_options['enable_ads_1'] ) && $this->wpdev_fbia_options['enable_ads_1'] === 'enable_ads_1' ) ? 'checked' : ''
		);
	}

	public function ad_id_1_2_callback() {
		printf(
			'<input class="regular-text" type="text" name="wpdev_fbia_option_name[ad_id_1_2]" id="ad_id_1_2" value="%s">',
			isset( $this->wpdev_fbia_options['ad_id_1_2'] ) ? esc_attr( $this->wpdev_fbia_options['ad_id_1_2']) : ''
		);
	}

	public function ad_id_2_3_callback() {
		printf(
			'<input class="regular-text" type="text" name="wpdev_fbia_option_name[ad_id_2_3]" id="ad_id_2_3" value="%s">',
			isset( $this->wpdev_fbia_options['ad_id_2_3'] ) ? esc_attr( $this->wpdev_fbia_options['ad_id_2_3']) : ''
		);
	}

	public function ad_id_3_4_callback() {
		printf(
			'<input class="regular-text" type="text" name="wpdev_fbia_option_name[ad_id_3_4]" id="ad_id_3_4" value="%s">',
			isset( $this->wpdev_fbia_options['ad_id_3_4'] ) ? esc_attr( $this->wpdev_fbia_options['ad_id_3_4']) : ''
		);
	}

  public function ad_id_4_5_callback() {
		printf(
			'<input class="regular-text" type="text" name="wpdev_fbia_option_name[ad_id_4_5]" id="ad_id_4_5" value="%s">',
			isset( $this->wpdev_fbia_options['ad_id_4_5'] ) ? esc_attr( $this->wpdev_fbia_options['ad_id_4_5']) : ''
		);
	}

	public function enable_analytics_5_callback() {
		printf(
			'<input type="checkbox" name="wpdev_fbia_option_name[enable_analytics_5]" id="enable_analytics_5" value="enable_analytics_5" %s> <label for="enable_analytics_5">Enable Google Analytics tracking.</label>',
			( isset( $this->wpdev_fbia_options['enable_analytics_5'] ) && $this->wpdev_fbia_options['enable_analytics_5'] === 'enable_analytics_5' ) ? 'checked' : ''
		);
	}

	public function analytics_id_6_callback() {
		printf(
			'<input class="regular-text" type="text" name="wpdev_fbia_option_name[analytics_id_6]" id="analytics_id_6" value="%s">',
			isset( $this->wpdev_fbia_options['analytics_id_6'] ) ? esc_attr( $this->wpdev_fbia_options['analytics_id_6']) : ''
		);
	}

	public function enable_instant_articles_group_tracking_7_callback() {
		printf(
			'<input type="checkbox" name="wpdev_fbia_option_name[enable_instant_articles_group_tracking_7]" id="enable_instant_articles_group_tracking_7" value="enable_instant_articles_group_tracking_7" %s> <label for="enable_instant_articles_group_tracking_7">Enable the Google Analytics tracking group, for detailed analytics.</label>',
			( isset( $this->wpdev_fbia_options['enable_instant_articles_group_tracking_7'] ) && $this->wpdev_fbia_options['enable_instant_articles_group_tracking_7'] === 'enable_instant_articles_group_tracking_7' ) ? 'checked' : ''
		);
	}

}
if ( is_admin() )
	$wpdev_fbia = new WPDevFBIA();

// Get Plugin Options
$wpdev_fbia_options = get_option( 'wpdev_fbia_option_name' );
// Create Plugin Variables
$wpdevfbia_numposts = $wpdev_fbia_options['number_of_posts_0']; // Number of Posts
$wpdevfbia_enableads = $wpdev_fbia_options['enable_ads_1']; // Enable Ads
$wpdevfbia_ad_1 = $wpdev_fbia_options['ad_id_1_2']; // Ad ID 1
$wpdevfbia_ad_2 = $wpdev_fbia_options['ad_id_2_3']; // Ad ID 2
$wpdevfbia_ad_3 = $wpdev_fbia_options['ad_id_3_4']; // Ad ID 3
$wpdevfbia_ad_4 = $wpdev_fbia_options['ad_id_4_5']; // Ad ID 3
$wpdevfbia_enableanalytics = $wpdev_fbia_options['enable_analytics_5']; // Enable Analytics
$wpdevfbia_analyticsid = $wpdev_fbia_options['analytics_id_6']; // Analytics ID
$wpdevfbia_grouptrack = $wpdev_fbia_options['enable_instant_articles_group_tracking_7']; // Enable Instant Articles Group Tracking
