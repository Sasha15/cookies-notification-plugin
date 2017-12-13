<?php

/**
 * Plugin Name:       Cookies notification
 * Description:       This is a plugin for cookies notification
 * Version:           1.0.0
 * Author:            Alex Brovko
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

class Cookies_Notifications {
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this,'mw_enqueue_color_picker') );
		add_action( 'admin_menu', array( $this, 'create_plugin_settings_page' ) );
		add_action( 'admin_init', array( $this, 'setup_sections' ) );
		add_action( 'admin_init', array( $this, 'setup_fields' ) );
		add_action( 'admin_init', array( $this,'save_wp_editor_fields'));
		add_action( 'wp_head', array($this, 'enqueue_banner_styles'));
		add_action( 'wp_footer',  array( $this,'display_banner') );
	}


	function mw_enqueue_color_picker( ) {
		//Add needed Colorpicker js and css
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'my-script-handle', plugins_url('js/my-script.js', __FILE__  ), array( 'wp-color-picker' ), false, true );
	}

	function enqueue_banner_styles(){
		wp_enqueue_style( 'my-styles-handle', plugins_url('css/custom-styles.css', __FILE__  ) );
		wp_enqueue_script( 'my-front-script-handle', plugins_url('js/front-script.js', __FILE__  ),false, false, true );
	}

	public function create_plugin_settings_page() {
		// Add the menu item and page

		$page_title = 'Cookies Notifications Page';
		$menu_title = 'Cookies Notifications';
		$capability = 'manage_options';
		$slug = 'cookies_notifications_fields';
		$callback = array( $this, 'plugin_settings_page_content' );
		$icon = 'dashicons-admin-plugins';
		$position = 100;

		add_menu_page( $page_title, $menu_title, $capability, $slug, $callback, $icon, $position );
	}

	public function plugin_settings_page_content() {
		// Add content and fields to plugin page
	?>
		<div class="wrap">
			<h2>Cookies Notifications</h2>
			<form method="post" action="options.php">
				<?php

				settings_fields( 'cookies_notifications_fields' );
				do_settings_sections( 'cookies_notifications_fields' );

				$content = get_option('specialcontent');
				$editor_id = 'specialcontent';
				$settings  = array (
					'wpautop'          => true,   // Whether to use wpautop for adding in paragraphs. Note that the paragraphs are added automatically when wpautop is false.
					'media_buttons'    => false,   // Whether to display media insert/upload buttons
					'textarea_name'    => $editor_id,   // The name assigned to the generated textarea and passed parameter when the form is submitted.
					'textarea_rows'    => get_option( 'default_post_edit_rows', 20 ),  // The number of rows to display for the textarea
					'tabindex'         => '',     // The tabindex value used for the form field
					'editor_css'       => '',     // Additional CSS styling applied for both visual and HTML editors buttons, needs to include <style> tags, can use "scoped"
					'editor_class'     => '',     // Any extra CSS Classes to append to the Editor textarea
					'teeny'            => false,  // Whether to output the minimal editor configuration used in PressThis
					'dfw'              => false,  // Whether to replace the default fullscreen editor with DFW (needs specific DOM elements and CSS)
					'tinymce'          => true,   // Load TinyMCE, can be used to pass settings directly to TinyMCE using an array
					'quicktags'        => true,   // Load Quicktags, can be used to pass settings directly to Quicktags using an array. Set to false to remove your editor's Visual and Text tabs.
					'drag_drop_upload' => true    // Enable Drag & Drop Upload Support (since WordPress 3.9)
				);
				wp_editor(stripslashes($content), $editor_id, $settings);

				submit_button();
				?>
			</form>
		</div> <?php
	}

	function save_wp_editor_fields(){
		if(isset($_POST['specialcontent'])){
			update_option('specialcontent', $_POST['specialcontent']);
		}
	}


	public function setup_sections() {
		add_settings_section( 'cookies_notification_section', 'Cookies Notification Settings', array( $this, 'section_callback' ), 'cookies_notifications_fields' );
	}
	public function section_callback() {
		echo 'Settings for Notification which appear in banner';
	}
	public function setup_fields() {
		$fields = array(
			array(
				'uid' => 'cookies_notification_first_field',
				'label' => 'Link (e.g. full rules of cookies using)',
				'section' => 'cookies_notification_section',
				'type' => 'text',
				'options' => false,
				'placeholder' => 'put url on official cookies rules',
				'helper' => '',
				'supplemental' => '',
				'default' => '',
				'colorpicker' => false
			),
			array(
				'uid' => 'background_color_field',
				'label' => 'Notification Background Color',
				'section' => 'cookies_notification_section',
				'type' => 'text',
				'options' => false,
				'placeholder' => 'choose background color',
				'helper' => '',
				'supplemental' => '',
				'default' => '#ccc',
				'colorpicker' => true
			),
			array(
				'uid' => 'button_color_field',
				'label' => 'Notification Button Color',
				'section' => 'cookies_notification_section',
				'type' => 'text',
				'options' => false,
				'placeholder' => 'Choose Button color',
				'helper' => '',
				'supplemental' => '',
				'default' => '#990000',
				'colorpicker' => true
			)
		);
		foreach( $fields as $field ){
			add_settings_field( $field['uid'], $field['label'], array( $this, 'field_callback' ), 'cookies_notifications_fields', $field['section'], $field );
			register_setting( 'cookies_notifications_fields', $field['uid'] );
		}
	}
	public function field_callback($arguments) {
		$value = get_option( $arguments['uid'] );
		if( ! $value ) { // If no value exists
			$value = $arguments['default']; // Set to our default
		}
		if( $arguments['type'] =='text' ){
			if($arguments['colorpicker'] == true){
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" class="my-color-field"/>', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value);
			}else{
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s"/>', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value);
			}


		}
		if( $helper = $arguments['helper'] ){
			printf( '<span class="helper"> %s</span>', $helper );
		}
		if( $supplimental = $arguments['supplemental'] ){
			printf( '<p class="description">%s</p>', $supplimental );
		}
	}
	public function display_banner(){
		$banner_text = get_option('specialcontent');
		$additional_link = get_option('cookies_notification_first_field');
		$banner_color = get_option('background_color_field');
		$button_color = get_option('button_color_field');
		if(isset($banner_text) && !empty($banner_text)){
			ob_start(); ?>
			<div id="br-cookies-notification-wrapper" style="background-color: <?php echo $banner_color ; ?>">
				<?php echo $banner_text ; ?>
				<?php if($additional_link): ?>
					<p class="br-additional-link"><a href="<?php echo $additional_link ?>" target="_blank"><?php _e('Link on resource', 'br-cookies-notification') ?></a></p>
				<?php endif; ?>
				<div class="br-button-container">
					<a href="#" class="br-accept-button" data-accept="true" style="background-color:<?php echo $button_color ; ?>"><?php _e('Accept', 'br-cookies-notification'); ?></a>
					<a href="#" class="br-cancel-button" style="background-color:<?php echo $button_color ; ?>"><?php _e('Cancel', 'br-cookies-notification'); ?></a>
				</div>
			</div>
		<?php $br_notification_output = ob_get_clean();
		echo $br_notification_output;
		}
	}
}
new Cookies_Notifications();

?>