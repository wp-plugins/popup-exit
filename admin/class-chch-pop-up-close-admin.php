<?php
/**
 * CC Pop-Up
 *
 * @package   ChChPopUpCloseAdmin
 * @author    Chop-Chop.org <shop@chop-chop.org>
 * @license   GPL-2.0+
 * @link      https://shop.chop-chop.org
 * @copyright 2014 
 */

if ( ! class_exists( 'ChChPFCLivePreview' ) )
    require_once( dirname( __FILE__ ) . '/includes/class-chch-pfc-preview.php' );

if ( ! class_exists( 'ChChPFCTemplate' ) )
    require_once( CHCH_PFC_PLUGIN_DIR . 'public/includes/class-chch-pfc-template.php' );
/**
 * @package ChChPopUpCloseAdmin
 * @author 	Chop-Chop.org <shop@chop-chop.org>
 */
 

class ChChPopUpCloseAdmin { 
	
	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;
	
	function __construct() {
		$this->plugin = ChChPopUpClose::get_instance(); 
		$this->plugin_slug = $this->plugin->get_plugin_slug();
		
		// Register Post Type
		add_action( 'init', array( $this, 'chch_pfc_register_post_type' ) );
		
		// Register Post Type Messages
		add_filter( 'post_updated_messages',  array( $this, 'chch_pfc_post_type_messages') );
		
		// Register Post Type Meta Boxes and fields
		add_action( 'init', array( $this, 'chch_pfc_initialize_cmb_meta_boxes'), 9999 );
		add_filter( 'cmb_meta_boxes', array( $this, 'chch_pfc_cmb_metaboxes') );
		add_action( 'add_meta_boxes_chch-pfc', array( $this, 'chch_pfc_metabox' ));
		add_action( 'cmb_render_chch_pfc_pages_select', array( $this, 'chch_pfc_render_pages_select'), 10, 5  ); 
		add_action( 'cmb_render_chch_pfc_cookie_select', array( $this, 'chch_pfc_render_cookie_select'), 10, 5  ); 
		
		// remove help tabs
		add_filter( 'contextual_help', array($this,'chch_pfc_remove_help_tabs'), 999, 3 );
		add_filter( 'screen_options_show_screen', '__return_false');
		
		// Templates view
		add_action( 'edit_form_after_title',array( $this, 'chch_pfc_templates_view' ));
		
		// Save Post Data
		add_action( 'save_post', array( $this, 'chch_pfc_save_pop_up_meta'), 10, 3 ); 
		
		add_action( 'admin_init', array( $this,'chch_pfc_tinymce_chch_pfc_keyup_event') );  
		
		// Customize the columns in the popup list.
		add_filter('manage_chch-pfc_posts_columns',array( $this, 'chch_pfc_custom_columns') ); 
		// Returns the content for the custom columns.
		add_action('manage_chch-pfc_posts_custom_column',array( $this, 'chch_pfc_manage_custom_columns' ),10, 2);  
		add_action( 'admin_print_scripts', array( $this, 'chch_pfc_enqueue_admin_scripts' ));
		add_action( 'admin_head', array( $this, 'chch_pfc_admin_head_scripts') ); 
		add_action( 'wp_ajax_chch_pfc_load_preview_module', array( $this, 'chch_pfc_load_preview_module'  )); 
	 
	} 
	
	
	
	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
	
	 
	/**
	 * Register tineMce event
	 *
	 * @since     1.0.0
	 * 
	 */
	function chch_pfc_tinymce_chch_pfc_keyup_event() { 
		if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
			if ( get_bloginfo('version') < 3.9 ) { 
				add_filter( 'mce_external_plugins', array( $this, 'chch_pfc_tinymce_event_old') );
			} else
			{
				add_filter( 'mce_external_plugins', array( $this, 'chch_pfc_tinymce_event') );	 
			} 
		}
	}
  	
	
	/**
	 * Add keyup to tineMce for WP version > 3.9
	 *
	 * @since     1.0.0
	 * 
	 */
	function chch_pfc_tinymce_event($plugin_array) { 
	 	$plugin_array['chch_pfc_keyup_event'] = CHCH_PFC_PLUGIN_URL .'admin/assets/js/chch-tinymce.js'; 
		return $plugin_array;
	}
	
	
	/**
	 * Add keyup to tineMce for WP version < 3.9
	 *
	 * @since     1.0.0
	 * 
	 */
	function chch_pfc_tinymce_event_old($plugin_array) { 
	 	$plugin_array['chch_pfc_keyup_event'] = CHCH_PFC_PLUGIN_URL .'admin/assets/js/chch-tinymce-old.js'; 
		return $plugin_array;
	}
	
	
	/**
	 * Return a pages_select field for CMB
	 *
	 * @since     1.0.0
	 * 
	 */
	function chch_pfc_custom_columns($defaults) {
		$defaults['chch_pfc_status'] = __('Active',$this->plugin_slug);
		$defaults['chch_pfc_clicks'] = __('Clicks',$this->plugin_slug);
		$defaults['chch_pfc_template'] = __('Template',$this->plugin_slug);
		return $defaults;
	}
	
 	
	/**
	 * Create columns in Pop-ups list
	 *
	 * @since     1.0.0  
	 */
	function chch_pfc_manage_custom_columns($column, $post_id) {
		global $post;
		if ($column === 'chch_pfc_status') {
			echo ucfirst(get_post_meta($post_id,'_chch_pfc_status', true));
		}
		
		if ($column === 'chch_pfc_clicks') {
			echo '<a href="http://ch-ch.org/pupro" target="_blank">AVAILABLE IN PRO</a>';
		}
		
		if ($column === 'chch_pfc_template') {
			echo ucfirst(get_post_meta($post_id,'_chch_pfc_template', true));
		}
	}
	 
	
	/**
	 * Register Custom Post Type
	 *
	 * @since    1.0.0
	 */
	public function chch_pfc_register_post_type() {
		
		$domain = $this->plugin_slug;
		
		$labels = array(
			'name'                => _x( 'Pop-Up CC - Close FREE', 'Post Type General Name', $domain),
			'singular_name'       => _x( 'Pop-Up CC - Close FREE', 'Post Type Singular Name', $domain),
			'menu_name'           => __( 'Pop-Up CC - Close FREE', $domain),
			'parent_item_colon'   => __( 'Parent Item:', $domain),
			'all_items'           => __( 'Pop-Up CC - Close FREE', $domain),
			'view_item'           => __( 'View Item', $domain),
			'add_new_item'        => __( 'Add New Pop-Up CC - Close FREE', $domain),
			'add_new'             => __( 'Add New Pop-Up CC - Close FREE', $domain),
			'edit_item'           => __( 'Edit Pop-Up CC - Close FREE', $domain),
			'update_item'         => __( 'Update Pop-Up CC - Close FREE', $domain),
			'search_items'        => __( 'Search Pop-Up CC - Close FREE', $domain),
			'not_found'           => __( 'Not found', $domain),
			'not_found_in_trash'  => __( 'No Pop-Up CC - Close FREE found in Trash', $domain),
		);
 

		$args = array(
			'label'               => __( 'Pop-Up CC - Close FREE', $domain),
			'description'         => __( '', $domain),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => false,
			'menu_position'       => 65, 
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => false
		);
		register_post_type( 'chch-pfc', $args );
	}
	
	
	
	/**
	 * Pop-Ups update messages. 
	 *
	 * @param array $messages Existing post update messages.
	 *
	 * @return array Amended post update messages with new CPT update messages.
	 */
	function chch_pfc_post_type_messages( $messages ) {
		$post             = get_post();
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );
	
		$messages['chch-pfc'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Pop-Up updated.', $this->plugin_slug ),
			2  => __( 'Custom field updated.', $this->plugin_slug),
			3  => __( 'Custom field deleted.',$this->plugin_slug),
			4  => __( 'Pop-Up updated.', $this->plugin_slug ), 
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Pop-Up restored to revision from %s', $this->plugin_slug ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Pop-Up published.', $this->plugin_slug ),
			7  => __( 'Pop-Up saved.', $this->plugin_slug ),
			8  => __( 'Pop-Up submitted.', $this->plugin_slug ),
			9  => sprintf(
				__( 'Pop-Up scheduled for: <strong>%1$s</strong>.', $this->plugin_slug ), 
				date_i18n( __( 'M j, Y @ G:i', $this->plugin_slug ), strtotime( $post->post_date ) )
			),
			10 => __( 'Pop-Up draft updated.', $this->plugin_slug )
		);
	
		if ( $post_type_object->publicly_queryable ) {
			$permalink = get_permalink( $post->ID );
	
			$view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View Pop-Up',  $this->plugin_slug ) );
			$messages[ $post_type ][1] .= $view_link;
			$messages[ $post_type ][6] .= $view_link;
			$messages[ $post_type ][9] .= $view_link;
	
			$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
			$preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview Pop-Up',  $this->plugin_slug ) );
			$messages[ $post_type ][8]  .= $preview_link;
			$messages[ $post_type ][10] .= $preview_link;
		}
	
		return $messages;
	}
	
	/**
	 * Initialize Custom Metaboxes Class
	 *
	 * @since  1.0.0 
	 */
	function chch_pfc_initialize_cmb_meta_boxes() {
 		if ( ! class_exists( 'cmb_Meta_Box' ) )
			require_once( dirname( __FILE__ ) . '/includes/Custom-Metaboxes-and-Fields-for-WordPress-master/init.php' ); 
	}
	
	/**
	 * Register custom metaboxes with CMB
	 *
	 * @since  1.0.0 
	 */
	function chch_pfc_cmb_metaboxes( array $meta_boxes ) {
		
		$domain = $this->plugin_slug; 
		$prefix = '_chch_pfc_'; 
	
		/**
		 * GENERAL OPTIONS
		 */
		$meta_boxes['chch-pfc-metabox-general'] = array(
			'id'         => 'chch-pfc-metabox-general',
			'title'      => __( 'GENERAL', $domain ),
			'pages'      => array( 'chch-pfc'), 
			'context'    => 'normal',
			'priority'   => 'high',
			'show_names' => true,  
			'fields'     => array( 
				
				array(
					'name'    => __( 'Pop-up Status', $domain ),
					'desc'    => __( 'Enable or disable the plugin. The pop-up will not be displayed on mobile devices.', $domain  ),
					'id'      => $prefix . 'status',
					'type'    => 'radio_inline',
					'std'	=> 'yes',
					'options' => array(
						'yes' => __( 'Turned ON', $domain ),
						'no'   => __( 'Turned OFF', $domain ), 
					),
				), 
				array(
					'name' => __( 'Show once per', $domain  ),
					'desc'    => __( '', $domain  ),
					'id'   => $prefix . 'show_only_once',
					'type' => 'chch_pfc_cookie_select', 
					'options' => array(
						'refresh' => __( 'Refresh', $domain  ),
						'session' => __( 'Session', $domain  ),
						'day'   => __( 'Day', $domain  ),
						'week'     => __( 'Week', $domain  ),
						'month'     => __( 'Month', $domain  ),
						'year'     => __( 'Year', $domain  ),
					),
					'default' => 'session',
					
				),   
			), 
		); 	
	
		
		/**
		 * DISPLAY CONTROL
		 */
		$meta_boxes['chch-pfc-metabox-control'] = array(
			'id'         => 'chch-pfc-metabox-control',
			'title'      => __( 'Display Control'),
			'pages'      => array( 'chch-pfc', ), 
			'context'    => 'normal',
			'priority'   => 'high',
			'show_names' => true,  
			'fields'     => array( 
				array(
					'name' => __( 'By Role:', $domain  ),
					'desc'    => __( 'Decide who will see the pop-up.', $domain  ),
					'id'   => $prefix . 'role',
					'type' => 'radio',
					'options' => array(
						'all' => __( 'All', $domain  ),
						'unlogged' => __( 'Show to unlogged users', $domain  ),
						'logged' => __( 'Show to logged in users', $domain  ),
					),
					'default' => 'all'
				),
				array(
					'name' => __( 'Disable on:', $domain  ),
					'desc'    => __( 'Decide on which pages the pop-up will not be visible.', $domain  ),
					'id'   => $prefix . 'page',
					'type' => 'chch_pfc_pages_select',  
				), 
			),
		); 
		
		return $meta_boxes;
	}
	
	
	/**
	 * Register custom metaboxes
	 *
	 * @since  0.1.0 
	 */
	public function chch_pfc_metabox( $post ) {
		remove_meta_box( 'slugdiv', 'chch-pfc', 'normal' );
		$post_boxes = array(
			'chch-pfc-metabox-general',
			'chch-pfc-metabox-content',
			'chch-pfc-metabox-control', 
		);	
		
		foreach($post_boxes as $post_box)
		{
			add_filter( 'postbox_classes_chch-pfc_'.$post_box,array( $this, 'chch_pfc_add_metabox_classes') );
		}
	}
	
	/**
	 * Add metabox class for tabs
	 *
	 * @since  0.1.0 
	 */
	function chch_pfc_add_metabox_classes( $classes ) {
 		array_push( $classes, "cc-pu-tab-2 cc-pu-tab" );
		return $classes; 
	}
	
	
	/**
	 * Return a pages_select field for CMB
	 *
	 * @since     1.0.0
	 * 
	 */
	function chch_pfc_render_pages_select( $field_args, $escaped_value, $object_id, $object_type, $field_type_object ) {
		$all_pages = $this->get_all_pages();
		 ?>
		<select class="cmb_select" name="<?php echo $field_args['_name']; ?>[]" id="<?php echo $field_args['_id']; ?>" multiple="multiple">	
			<?php 
			$selected = '';
			if(!empty($escaped_value) && is_array($escaped_value)){
				if(in_array( 'chch_home',$escaped_value)) {
					$selected = 'selected';	
				}
				if(in_array( 'chch_woocommerce_shop',$escaped_value)) {
					$selected = 'selected';	
				}
				
				if(in_array( 'chch_woocommerce_category',$escaped_value)) {
					$selected = 'selected';	
				}
			}
			?>
			<option value="chch_home" <?php echo $selected ?>>Home (Latest Posts)</option>
    	<option value="chch_woocommerce_shop" <?php echo $selected ?>>Woocommerce (Shop Page)</option>
    	<option value="chch_woocommerce_category" <?php echo $selected ?>>Woocommerce (Category Page)</option>
		<?php
			foreach($all_pages as $value => $title):
				$selected = '';
				if(!empty($escaped_value) && is_array($escaped_value)){
					if(in_array( $value,$escaped_value)) {
						$selected = 'selected';	
					} 
				}
			 	echo '<option value="'.$value.'" '.$selected .'>'.$title.'</option>	';
			endforeach
		 ?>
			</select> 	 
		<?php    
		echo $field_type_object->_desc( true );
	} 
	
	
	/**
	 * Return a cookie_select field for CMB
	 *
	 * @since     1.0.0
	 * 
	 */
	function chch_pfc_render_cookie_select( $field_args, $escaped_value, $object_id, $object_type, $field_type_object ) {
		$cookie_expire = array(
			'refresh' => 'Refresh',
			'session' => 'Session',
			'Day' => 'Day (Available in Pro)',
			'Week' => 'Week (Available in Pro)',
			'Month' => 'Month (Available in Pro)',
			'Year' => 'Year (Available in Pro)',	
		);
		?>
		
		<select class="cmb_select" name="<?php echo $field_args['_name']; ?>" id="<?php echo $field_args['_id']; ?>">	
		
		<?php
			foreach($cookie_expire as $value => $title):
				$selected = '';
				$disable = '';
				
				if(!empty($escaped_value)){
					if($value == $escaped_value) {
						$selected = 'selected';	
					} 
				}
				
				if($value != 'refresh' && $value != 'session') {
					$disable = 'disabled';	
				}
				
			 	echo '<option value="'.$value.'" '.$selected .' '.$disable.'>'.$title.'</option>';
			endforeach
		 ?>
		 
		</select> <a href="http://ch-ch.org/pupro" target="_blank">Get Pro</a>
				 
		<?php    
	} 
	
	/**
	 * Remove help tabs from post view.
	 *
	 * @since     1.0.7
	 * 
	 */
	function chch_pfc_remove_help_tabs($old_help, $screen_id, $screen){
		if ( 'post' == $screen->base && 'chch-pfc' === $screen->post_type) {
			$screen->remove_help_tabs();
			return $old_help;
		}
	}
	
	/**
	 * Return list of templates
	 *
	 * @since     1.0.0
	 *
	 * @return    array - template list
	 */
	public function get_templates() {
		if ( ! class_exists( 'PluginMetaData' ) )
			require_once( CHCH_PFC_PLUGIN_DIR . 'admin/includes/PluginMetaData.php' ); 
		$pmd = new PluginMetaData;
		$pmd->scan(CHCH_PFC_PLUGIN_DIR . 'public/templates');
		return $pmd->plugin;
	}
	
	
	/**
	 * Add Templates View
	 *
	 * @since  0.1.0 
	 */
	public function chch_pfc_templates_view( $post ) { 
		  
		$screen = get_current_screen();
		if ( 'post' == $screen->base && 'chch-pfc' === $screen->post_type) {
			
			include(CHCH_PFC_PLUGIN_DIR . '/admin/views/templates.php' );
		}
	}
	
	
	/**
	 * Save Post Type Meta
	 *
	 * @since  0.1.0 
	 */
	function chch_pfc_save_pop_up_meta( $post_id, $post, $update ) { 
	
	
		if (
			!isset($_POST['chch_pfc_save_nonce']) 
			|| ! wp_verify_nonce($_POST['chch_pfc_save_nonce'],'chch_pfc_save_nonce_'.$post_id) 
		) 
		{
			return;
		}
		
		if(defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) return; 
		
		  
		if ( $post->post_type != 'chch-pfc' ) {
			return;
		}
		
		$template =  $_REQUEST['_chch_pfc_template'];
		$template_base =  $_REQUEST['_chch_pfc_template_base'];
		
		update_post_meta( $post_id, '_chch_pfc_template', sanitize_text_field( $_REQUEST['_chch_pfc_template']) );
		update_post_meta( $post_id, '_chch_pfc_template_base', sanitize_text_field( $_REQUEST['_chch_pfc_template_base']) );
		 
		if(!empty($template) && !empty($template_base))
		{
			$template_data = array();
			 
      
      $template_data['size']= array(
				'size' => sanitize_text_field($_REQUEST['_'.$template.'_size_size']), 
			);     
			
			
			$template_data['background']= array(
				'color' => sanitize_text_field($_REQUEST['_'.$template.'_background_color']), 
				'type' => sanitize_text_field($_REQUEST['_'.$template.'_background_type']), 
				'image' => sanitize_text_field($_REQUEST['_'.$template.'_background_image']), 
				'pattern' => sanitize_text_field($_REQUEST['_'.$template.'_background_pattern']), 
				'repeat' => sanitize_text_field($_REQUEST['_'.$template.'_background_repeat']), 
				 
			);
			 
			
			$p_array = array('</p>','<p>');
		 
			$template_data['contents']= array(
				'header' => wp_kses_post(str_replace($p_array, '', $_REQUEST['_'.$template.'_contents_header'])),  
				'subheader' => wp_kses_post(str_replace($p_array, '', $_REQUEST['_'.$template.'_contents_subheader'])),   
				'content' => wp_kses_post($_REQUEST['_'.$template.'_contents_content']),    
			); 
					
			$template_data['left_button']= array( 
				'url' => sanitize_text_field($_REQUEST['_'.$template.'_left_button_url']), 
				'header' => sanitize_text_field($_REQUEST['_'.$template.'_left_button_header']), 
				'subheader' => sanitize_text_field($_REQUEST['_'.$template.'_left_button_subheader']), 
			);
			
			$template_data['right_button']= array( 
				'url' => sanitize_text_field($_REQUEST['_'.$template.'_right_button_url']),   
				'header' => sanitize_text_field($_REQUEST['_'.$template.'_right_button_header']), 
				'subheader' => sanitize_text_field($_REQUEST['_'.$template.'_right_button_subheader']), 
			); 
		
			update_post_meta($post_id, '_'.$template.'_template_data', $template_data);	
		}
	}
	
	
	/**
	 * Get all pages for CMB select pages field
	 *
	 * @since  0.1.0 
	 */
	private function get_all_pages() {
		
		$args = array(
		   'public'   => true,
		   '_builtin' => true
		);
		
		$post_types = get_post_types('','names');
		if(($key = array_search('chch-pfc', $post_types)) !== false) {
			unset($post_types[$key]);
		}
		
        $args = array(
			'post_type' => $post_types,
			'posts_per_page' => -1, 
			'orderby' => 'title',
			'order' => 'ASC'
		);
		
		$post_list = get_posts($args);
		
		$all_posts = array();
		
		if($post_list):
			foreach($post_list as $post):
				$all_posts[$post->ID] = get_the_title($post->ID);
			endforeach;
		endif;
		
        return $all_posts; 
	}
	
	
	/**
	 * Include google fonts
	 *
	 * @since  0.1.0 
	 */
	public function chch_pfc_admin_head_scripts() {
	 	$screen = get_current_screen();
		if ( 'post' == $screen->base && 'chch-pfc' === $screen->post_type) { 
			
			$js ="<link href='//fonts.googleapis.com/css?family=Playfair+Display:400,700,900|Lora:400,700|Open+Sans:400,300,700|Oswald:700,300|Roboto:400,700,300|Signika:400,700,300' rel='stylesheet' type='text/css'>";
			echo $js;
		}
	 } 
	 
	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 */
	public function chch_pfc_enqueue_admin_scripts() {

		$screen = get_current_screen();
		if ( 'post' == $screen->base && 'chch-pfc' === $screen->post_type) { 
			wp_enqueue_style('wp-color-picker' ); 
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-slider'); 
			
			wp_enqueue_media();
			
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), ChChPopUpClose::VERSION );
			
			wp_enqueue_script( $this->plugin_slug .'-admin-scripts', plugins_url( 'assets/js/chch-admin.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ), ChChPopUpClose::VERSION );  
			wp_localize_script( $this->plugin_slug .'-admin-scripts', 'chch_pfc_ajax_object', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ), 'chch_pop_up_url' => CHCH_PFC_PLUGIN_URL) );
			
			wp_enqueue_style( $this->plugin_slug .'-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.min.css', null, ChChPopUpClose::VERSION,'all' );
			
			if(file_exists(CHCH_PFC_PLUGIN_DIR . 'public/templates/css/defaults.css'))
			{
				wp_enqueue_style($this->plugin_slug .'_template_defaults', CHCH_PFC_PLUGIN_URL . 'public/templates/css/defaults.css', null, ChChPopUpClose::VERSION, 'all');  
			}
			
			if(file_exists(CHCH_PFC_PLUGIN_DIR . 'public/templates/css/fonts.css'))
			{
				wp_enqueue_style($this->plugin_slug .'_template_fonts', CHCH_PFC_PLUGIN_URL . 'public/templates/css/fonts.css', null, ChChPopUpClose::VERSION, 'all');  
			}  
		}  

	}
	
	
	/**
	 * Load preview by ajax
	 *
	 */
	function chch_pfc_load_preview_module() {
 
		$template = $_POST['template'];
		$template_base = $_POST['base'];
		$popup = $_POST['id']; 
		$template = new ChChPFCTemplate($template,$template_base,$popup); 
		
		$template->get_template();	
		die();
	}
}
