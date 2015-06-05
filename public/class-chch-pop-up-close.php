<?php
/**
 * Pop-Up CC - Close FREE
 *
 * @package   ChChPopUpClose
 * @author    Chop-Chop.org <shop@chop-chop.org>
 * @license   GPL-2.0+
 * @link      https://shop.chop-chop.org
 * @copyright 2014 
 */

if ( ! class_exists( 'ChChPFCTemplate' ) )
    require_once( CHCH_PFC_PLUGIN_DIR . 'public/includes/class-chch-pfc-template.php' );
	
/**
 * @package ChChPopUpClose
 * @author  Chop-Chop.org <shop@chop-chop.org>
 */
class ChChPopUpClose {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/** 
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'chch-pfc';
	
	/** 
	 *
	 * @since    1.0.0
	 *
	 * @var      array
	 */
	private $pop_ups = array();

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		
		// Get all active Pop-Ups
		$this->pop_ups = $this->get_pop_ups(); 
		
		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) ); 
  		
		// Include public fancing styles and scripts
		add_action( 'wp_enqueue_scripts', array($this,'chch_pfc_template_scripts') );
		
		// Include fonts on front-end
		add_action('wp_head', array( $this, 'chch_pfc_hook_fonts' ) );
		
		// Display active Pop-Ups on front-end
		add_action('wp_footer', array( $this, 'chch_pfc_show_popup' )); 

	}
	
	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
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
	 * Get All Active Pop-Ups IDs
	 *
	 * @since  0.1.0
	 *
	 * @return   array - Pop-Ups ids
	 */
	private function get_pop_ups() {
		$list = array();
		
		$args = array(
			'post_type' => 'chch-pfc',
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key'     => '_chch_pfc_status',
					'value'   => 'yes', 
				),
			),
		);
		
		$pop_ups = get_posts( $args);
		
		if ( $pop_ups ) {
			foreach ( $pop_ups as $pop_up ) {
				$list[] = $pop_up->ID;
			}
		} 	 
		return $list;
	}
	
	
	/**
	 * Include Templates scripts on Front-End
	 *
	 * @since  0.1.0
	 *
	 * @return   array - Pop-Ups ids
	 */
	function chch_pfc_template_scripts() { 
		
		$pop_ups = $this->pop_ups;
		
		if(!empty($pop_ups)) {
			
			if(file_exists(CHCH_PFC_PLUGIN_DIR . 'public/templates/css/defaults.css')) {
				wp_enqueue_style($this->plugin_slug .'_template_defaults', CHCH_PFC_PLUGIN_URL . 'public/templates/css/defaults.css', null, ChChPopUpClose::VERSION, 'all');  
			}
				
			if(file_exists(CHCH_PFC_PLUGIN_DIR . 'public/templates/css/fonts.css')) {
				wp_enqueue_style($this->plugin_slug .'_template_fonts', CHCH_PFC_PLUGIN_URL . 'public/templates/css/fonts.css', null, ChChPopUpClose::VERSION, 'all');  
			}
			
			if(file_exists(CHCH_PFC_PLUGIN_DIR . 'public/templates/m-1/css/base.css')){
				wp_enqueue_style($this->plugin_slug .'_base_m-1', CHCH_PFC_PLUGIN_URL . 'public/templates/m-1/css/base.css', null, ChChPopUpClose::VERSION, 'all');  
				  
			} 
			
			 
			if(file_exists(CHCH_PFC_PLUGIN_DIR . 'public/assets/js/jquery-cookie/jquery.cookie.js')){	
				wp_enqueue_script( $this->plugin_slug .'jquery-cookie', CHCH_PFC_PLUGIN_URL . 'public/assets/js/jquery-cookie/jquery.cookie.js', array('jquery') );
				
			}
			
			if(file_exists(CHCH_PFC_PLUGIN_DIR . 'public/assets/js/public.js')){	
				wp_enqueue_script( $this->plugin_slug .'public-script', CHCH_PFC_PLUGIN_URL . 'public/assets/js/public.js', array('jquery') ); 
				wp_localize_script( $this->plugin_slug .'public-script', 'chch_pfc_ajax_object', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ), 'chch_pop_up_url' => CHCH_PFC_PLUGIN_URL) );
			}
			 
		
			foreach($pop_ups as $id)
			{
				
				$template_id = get_post_meta( $id, '_chch_pfc_template', true);
				$template_base = get_post_meta( $id, '_chch_pfc_template_base', true);
				
				if(file_exists(CHCH_PFC_PLUGIN_DIR . 'public/templates/'.$template_base.'/'.$template_id.'/css/style.css')){
					wp_enqueue_style($this->plugin_slug .'_style_'.$template_id, CHCH_PFC_PLUGIN_URL . 'public/templates/'.$template_base.'/'.$template_id.'/css/style.css', null, ChChPopUpClose::VERSION, 'all');  
					  
				}   
			}
		}	
			
	} 
	
	
	/**
	 * Include fonts on front-end
	 *
	 * @since  0.1.0
	 */
	function chch_pfc_hook_fonts() {
	
		$output="<link href='//fonts.googleapis.com/css?family=Playfair+Display:400,700,900|Lora:400,700|Open+Sans:400,300,700|Oswald:700,300|Roboto:400,700,300|Signika:400,700,300' rel='stylesheet' type='text/css'>";
	
		echo $output;
	}
	 
	 
	/**
	 * Display Pop-Up on Front-End
	 *
	 * @since  0.1.0
	 */
	 public function chch_pfc_show_popup() {
		  
		$pop_ups = $this->pop_ups;
		 
		if(!empty($pop_ups))
		{
			foreach($pop_ups as $id)
			{ 
				
				$user_role = get_post_meta( $id, '_chch_pfc_role', true);
				$user_login = is_user_logged_in();
				
				if($user_role == 'logged' && !$user_login) {
					continue;	
				}
				
				if($user_role == 'unlogged' && $user_login) {
					continue;	
				}
				
				$pages = get_post_meta( $id, '_chch_pfc_page', true);
				
				if(is_array( $pages)){
					if(is_home()) {
						if(in_array('chch_home', $pages)) {
							continue; 	
						} else {
							$array_key = array_search(get_the_ID(), $pages);
							if($array_key){
								unset($pages[$array_key]);	
							}
						} 	
					} 
					
					if(in_array('chch_woocommerce_shop', $pages)) {
						if(function_exists('is_shop')){  
							if(is_shop()){ 
								continue;		
							}
						}  	
					} 
					
					if(in_array('chch_woocommerce_category', $pages)) {
						if(function_exists('is_product_category')){ 
							if(is_product_category()){ 
								continue;		
							}
						}  	
					}
					
					if(in_array(get_the_ID(), $pages)){
						continue;		
					} 
				}
				
				
				$template_id = get_post_meta( $id, '_chch_pfc_template', true);
				$template_base = get_post_meta( $id, '_chch_pfc_template_base', true);
				
				
				echo '<div style="display:none;" id="modal-'.$id.'" class="'.$template_id.'">'; 
				  
				$template = new ChChPFCTemplate($template_id,$template_base,$id);
				echo $template->build_css();
				$template->get_template();
				echo $template->build_js();
				
				echo '</div>';   
			}
		}
	} 
	
	
}
