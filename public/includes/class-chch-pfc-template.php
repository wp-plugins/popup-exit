<?php
/**
 * Pop-Up CC - Close FREE
 *
 * @package   ChChPFCTemplate
 * @author    Chop-Chop.org <shop@chop-chop.org>
 * @license   GPL-2.0+
 * @link      https://shop.chop-chop.org
 * @copyright 2014 
 */

/**
 * @package ChChPFCTemplate
 * @author  Chop-Chop.org <shop@chop-chop.org>
 */
class ChChPFCTemplate { 

	private $template, $template_base, $post_id = 0;

	function __construct($template, $template_base, $post_id = 0) {
		$this->plugin = ChChPopUpClose::get_instance(); 
		
		$this->template = $template;
		$this->template_base = $template_base;
		$this->post_id = $post_id; 
		 
	} 
	
	function get_template_options(){
		if(!$options = get_post_meta($this->post_id, '_'.$this->template.'_template_data',true)){
			if(file_exists(CHCH_PFC_PLUGIN_DIR . 'public/templates/'.$this->template_base.'/'.$this->template.'/defaults.php'))
			{
				$options = (include(CHCH_PFC_PLUGIN_DIR . 'public/templates/'.$this->template_base.'/'.$this->template.'/defaults.php'));
			}
		}
		 
		return $options;
	} 
	
	function get_template_option($base, $option){
		
		$all_options = $this->get_template_options();
		
		if(isset($all_options[$base][$option])){
			
			return $all_options[$base][$option];
			
		} elseif(file_exists(CHCH_PFC_PLUGIN_DIR . 'public/templates/'.$this->template_base.'/'.$this->template.'/defaults.php')) {
			
			$default_options = (include(CHCH_PFC_PLUGIN_DIR . 'public/templates/'.$this->template_base.'/'.$this->template.'/defaults.php'));
			
			if(isset($default_options[$base][$option])){ 
				return $default_options[$base][$option];
			}
		}
		 
		return '';
	} 
	
	
	function get_template(){ 
		$template_options = $this->get_template_options(); 
		$id = $this->post_id;
		include(CHCH_PFC_PLUGIN_DIR . 'public/templates/'.$this->template_base.'/'.$this->template.'/index.php' );  
	}
	
	/**
	 * ChChSliderProTemplate::build_css()
	 * 
	 * @return
	 */
	function build_css(){
		$options = $this->get_template_options();  
    $prefix = '#modal-' . $this->post_id;
	  $css = "<style>\n";   
     
    /*$css .= $this->build_css_regule($prefix.' .scc-arrows a:hover',array('color' => $options['arrows']['hover']));  
    
    $css .= $this->build_css_regule($prefix.' .scc-pagination > a',array('background' => $options['pagination']['color']));  
     
      */ 
     
		$css .= $this->build_css_regule($prefix.' .modal-inner',array('background-color' => $options['background']['color']));
	 
    switch ( $options['background']['type']) {
      case 'image':
				$css .= $this->build_css_regule($prefix.' .modal-inner',array('background-image' => $options['background']['image'],'background-size' => 'cover'));  
        break;

      case 'pattern':
				$css .= $this->build_css_regule($prefix.' .modal-inner',array('background-image' => $options['background']['image'],'background-repeat' => $options['background']['repeat'])); 
        break;
    }   
		$css .= "</style>\n"; 
	
		return $css; 
	}
  
  /**
   * Build css regule
   * 
   * @param string $selector - css rule selector
   * @param array $style_atts - css properties array - [property][value]
   * 
   * @return string $css_rule - css rule - .selector { property: value !important;}
   */
  private function build_css_regule($selector, $style_atts){
    $css_rule = '';
    
    if(!empty($selector) && is_array($style_atts)){
      $css_rule .= sprintf("%s {\n",$selector); 
      
      foreach($style_atts as $property => $value){
				if($property != 'background-image') {
					$css_rule .= sprintf("\t\t%s:%s !important;\n",$property,$value);   		
				} else {
					$css_rule .= sprintf("\t\t%s:url(\"%s\") !important;\n",$property,$value); 		
				}
        
      } 
      $css_rule .= "\t}\n";    
    }
    
    return $css_rule;  
  } 
	
	/**
	 * ChChSliderProTemplate::build_js()
   * 
   * Build js script for slider.
   * 
   * @uses get_template_options()
	 * 
	 * @return string $js - js code for slider cc
	 */
	function build_js(){ 
	
		$id = $this->post_id;

    $mobile_header = "\t\t\tif($(window).width() > 1024){\n";
    $mobile_footer = "\t\t\t}\n";

    if ( get_post_meta( $id, '_chch_pfc_show_on_mobile', true)) {
      $mobile_header = '';
      $mobile_footer = '';
    }

    if ( get_post_meta( $id, '_chch_pfc_show_only_on_mobile', true)) {
      $mobile_header = "\t\t\tif($(window).width() < 1025){\n";
      $mobile_footer = "\t\t\t}\n";
    }
  

		$js = "<script type=\"text/javascript\">\n\tjQuery(function($) {\n";
		//cookie check
		$js .= sprintf("\t\tif(!Cookies.get('shown_modal_%s')){ \n",$id);
		
		//check display on mobile
		$js .= $mobile_header;
		
		//showing popup 
		$js .= "\t\t\t\t$(document).on('mouseleave', function(e){\n";
		$js .= sprintf("\t\t\t\t\t$('#modal-%s').not('.chch_shown').show('fast');\n",$id);
		$js .= sprintf("\t\t\t\t\t$('#modal-%s').addClass('chch_shown');\n",$id); 
		//calculate vertical position
		$js .= "\t\t\t\t\twindowPos = $(window).scrollTop();\n";
		$js .= "\t\t\t\t\twindowHeight = $(window).height();\n";
		$js .= sprintf("\t\t\t\t\tpopupHeight = $( '#modal-%s .modal-inner' ).outerHeight();\n",$id);
		$js .= "\t\t\t\t\tpopupPosition = windowPos + ((windowHeight - popupHeight)/2);\n";
		$js .= sprintf("\t\t\t\t\t$('#modal-%s .pop-up-cc').css('top',Math.abs(popupPosition));\n",$id);  
		$js .= "\t\t\t\t});\n"; 
		$js .= $mobile_footer;
		$js .= "\t\t}\n\t});\n</script>\n";

		return $js;

	}

  function enqueue_template_style() {

    $options = $this->get_template_options();

    if ( file_exists( CHCH_PFC_PLUGIN_DIR . 'public/templates/css/defaults.css')) {
      wp_enqueue_style( $this->plugin_slug . '_template_defaults', CHCH_PFC_PLUGIN_URL . 'public/templates/css/defaults.css', null, ChChPopUpClose::VERSION, 'all');
    }

    if ( file_exists( CHCH_PFC_PLUGIN_DIR . 'public/templates/css/fonts.css')) {
      wp_enqueue_style( $this->plugin_slug . '_template_fonts', CHCH_PFC_PLUGIN_URL . 'public/templates/css/fonts.css', null, ChChPopUpClose::VERSION, 'all');
    }

    if ( file_exists( CHCH_PFC_PLUGIN_DIR . 'public/templates/m-1/css/base.css')) {
      wp_enqueue_style( 'base_' . $this->template_base, CHCH_PFC_PLUGIN_URL . 'public/templates/m-1/css/base.css', null, ChChPopUpClose::VERSION, 'all');

    }


    if ( file_exists( CHCH_PFC_PLUGIN_DIR . 'public/assets/js/jquery-cookie/jquery.cookie.js')) {
      wp_enqueue_script( $this->plugin_slug . 'jquery-cookie', CHCH_PFC_PLUGIN_URL . 'public/assets/js/jquery-cookie/jquery.cookie.js', array( 'jquery'), ChChPopUpClose::VERSION);

    }

    if ( file_exists( CHCH_PFC_PLUGIN_DIR . 'public/assets/js/public.js')) {
      wp_enqueue_script( $this->plugin_slug . 'public-script', CHCH_PFC_PLUGIN_URL . 'public/assets/js/public.js', array( 'jquery'), ChChPopUpClose::VERSION);
      wp_localize_script( $this->plugin_slug . 'public-script', 'chc_pfc_ajax_object', array( 'ajaxUrl' => admin_url( 'admin-ajax.php')));
    }

    if ( file_exists( CHCH_PFC_PLUGIN_DIR . 'public/templates/' . $this->template_base . '/' . $this->template . '/css/style.css')) {
      wp_enqueue_style( 'style_' . $this->template, CHCH_PFC_PLUGIN_URL . 'public/templates/' . $this->template_base . '/' . $this->template . '/css/style.css', null, ChChPopUpClose::VERSION, 'all');

    } 

  }
	
	
}