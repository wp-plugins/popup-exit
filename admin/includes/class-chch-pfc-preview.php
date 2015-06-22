<?php

/**
 * Pop-Up CC - Close FREE
 *
 * @package   ChChPopUpClosePostType
 * @author    Chop-Chop.org <shop@chop-chop.org>
 * @license   GPL-2.0+
 * @link      https://shop.chop-chop.org
 * @copyright 2014 
 */

if ( !class_exists( 'ChChPFCTemplate' ) )
  require_once ( CHCH_PFC_PLUGIN_DIR . 'public/includes/class-chch-pfc-template.php' );

/**
 * @package ChChPopUpClosePostType
 * @author  Chop-Chop.org <shop@chop-chop.org>
 */
class ChChPFCLivePreview {

  private $template_id, $template_base, $template_name, $template_options, $options_prefix;

  public $fields = array();

  function __construct( $template, $template_base, $template_name ) {
    $this->plugin = ChChPopUpClose::get_instance();
    $this->plugin_slug = $this->plugin->get_plugin_slug();

    $this->template_id = $template;

    $this->template_name = $template_name;

    $this->template_base = $template_base;

    $this->options_prefix = '_' . $this->template_id . '_';

    $this->template = new ChChPFCTemplate( $this->template_id, $this->template_base, get_the_ID() );

    $this->template_options = $this->template->get_template_options();

  }

  /**
   * Build preview view
   *
   * @since    0.1.0
   */
  public function build_preview() {

    echo '<div class="cc-pu-customize-form" id="cc-pu-customize-form-' . $this->template_id . '" style="display:none;">';

    echo '<div class="cc-pu-customize-controls">';

    //preview options header
    echo '
			<div class="cc-pu-customize-header-actions">
				<input name="publish" id="publish-customize" class="button button-primary button-large" value="Save &amp; Close" accesskey="p" type="submit" />  
				<a class="cc-pu-customize-close" href="#" data-template="' . $this->template_id . '">
					<span class="screen-reader-text">Close</span>
				</a> 
		</div>';

    //preview options overlay - start
    echo '<div class="cc-pu-options-overlay">';

    //preview customize info
    echo '<div class="cc-pu-customize-info">
				<span class="preview-notice">
					You are customizing <strong class="template-title">' . $this->template_name . ' Template</strong>
				</span>
			</div><!--#customize-info-->';

    //preview options accordion wrapper - start
    echo '<div class="customize-theme-controls"  class="accordion-section">';

    // build options sections

    echo $this->build_options();

    echo '
				</div><!--.accordion-section-->
			</div><!--.cc-pu-options-overlay-->
		</div><!--#cc-pu-customize-controls-->';

    echo '<div id="cc-pu-customize-preview-' . $this->template_id . '" class="cc-pu-customize-preview" style="position:relative;">';

    echo '</div>';
    echo '</div>';

  }

  private function build_options() {

    $fields['general'] = array(
      'name' => 'General',
      'field_groups' => array(
        array(
          'option_group' => 'size',
          'title' => 'Size',
          'fields' => array( array(
              'type' => 'select_class_switcher',
              'name' => 'size',
              'target' => '.pop-up-cc',
              'desc' => 'Size:',
              'options' => array( 'chch-close-small' => 'Small', 'chch-close-big' => 'Big' ) ), ),
          ),
        array(
          'option_group' => 'none',
          'title' => 'Overlay',
          'disable' => true,
          'fields' => array(
            array(
              'type' => 'color_picker',
              'name' => 'color',
              'target' => 'none',
              'attr' => 'background-color',
              'desc' => 'Color:',
              ),
            array(
              'type' => 'slider',
              'name' => 'opacity',
              'min' => '0',
              'max' => '1.0',
              'step' => '0.1',
              'target' => 'none',
              'attr' => 'opacity',
              'desc' => 'Opacity:',
              ),
            ),
          ),
        ),
      );

    $fields['borders'] = array(
      'name' => 'Border',
      'field_groups' => array( array(

          'option_group' => 'none',
          'title' => 'Borders',
          'disable' => true,
          'fields' => array(
            array(
              'type' => 'slider',
              'min' => '0',
              'max' => '50',
              'step' => '1',
              'name' => 'radius',
              'target' => 'none',
              'attr' => 'border-radius',
              'unit' => 'px',
              'desc' => 'Border Radius:',
              ),
            array(
              'type' => 'slider',
              'min' => '0',
              'max' => '50',
              'step' => '1',
              'name' => 'width',
              'target' => 'none',
              'attr' => 'border-width',
              'unit' => 'px',
              'desc' => 'Width:',
              ),
            array(
              'type' => 'select',
              'name' => 'style',
              'target' => 'none',
              'attr' => 'border-style',
              'desc' => 'Border Style:',
              'options' => array(
                'solid' => 'Solid',
                'dashed' => 'Dashed',
                'dotted' => 'Dotted',
                ),
              ),
            array(
              'type' => 'color_picker',
              'name' => 'color',
              'target' => 'none',
              'attr' => 'border-color',
              'desc' => 'Color:',
              ),
            ),
          ), ),
      );

    $fields['background'] = array(
      'name' => 'Background',
      'field_groups' => array( array(
          'option_group' => 'background',
          'title' => 'Background',
          'fields' => array(
            array(
              'type' => 'color_picker',
              'name' => 'color',
              'target' => '.modal-inner',
              'attr' => 'background-color',
              'desc' => 'Color:',
              ),
            array(
              'type' => 'revealer_group',
              'name' => 'type',
              'desc' => 'Background Type:',
              'target' => '.modal-inner',
              'attr' => 'background-image',

              'options' => array(
                'no' => 'No Image',
                'image' => 'Image',
                'pattern' => 'Pattern',
                ),
              'revaeals' => array(
                array(
                  'section_title' => 'Background Image',
                  'section_id' => 'image',
                  'fields' => array( array(
                      'type' => 'upload',
                      'name' => 'image',
                      'target' => '.modal-inner',
                      'attr' => 'background-image',
                      'desc' => 'Enter a URL or upload an image:',
                      ), ),
                  ),
                array(
                  'section_title' => 'Background Pattern',
                  'section_id' => 'pattern',
                  'fields' => array(
                    array(
                      'type' => 'upload',
                      'name' => 'pattern',
                      'target' => '.modal-inner',
                      'attr' => 'background-image',
                      'desc' => 'Enter a URL or upload an image:',
                      ),
                    array(
                      'type' => 'select',
                      'name' => 'repeat',
                      'target' => '.modal-inner',
                      'attr' => 'background-repeat',
                      'desc' => 'Pattern Repeat:',
                      'options' => array(
                        'repeat' => 'Repeat',
                        'repeat-x' => 'Repeat-x',
                        'repeat-y' => 'Repeat-y',
                        'no-repeat' => 'No Repeat',
                        ),
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ), ),
      );

    $fields['fonts'] = array(
      'name' => 'Fonts and Colors',
      'field_groups' => array(
        array(

          'option_group' => 'none',
          'disable' => true,
          'title' => 'Header',
          'fields' => array(
            array(
              'type' => 'select',
              'name' => 'font',
              'target' => 'none',
              'options' => array( 'Open Sans' => 'Open Sans', ),
              'desc' => 'Header Font:',
              ),
            array(
              'type' => 'color_picker',
              'name' => 'color',
              'target' => 'none',
              'attr' => 'color',
              'desc' => 'Color:',
              ),
            ),
          ),
        array(

          'option_group' => 'none',
          'disable' => true,
          'title' => 'Subheader',
          'fields' => array(
            array(
              'type' => 'select',
              'name' => 'font',
              'target' => 'none',
              'options' => array( 'Open Sans' => 'Open Sans', ),
              'desc' => 'Subheader Font:',
              ),
            array(
              'type' => 'color_picker',
              'name' => 'color',
              'target' => 'none',
              'attr' => 'color',
              'desc' => 'Color:',
              ),
            ),
          ),
        array(

          'option_group' => 'none',
          'disable' => true,
          'title' => 'Content',
          'fields' => array(
            array(
              'type' => 'select',
              'name' => 'font',
              'target' => 'none',
              'options' => array( 'Open Sans' => 'Open Sans', ),
              'desc' => 'Content Font:',
              ),
            array(
              'type' => 'color_picker',
              'name' => 'color',
              'target' => 'none',
              'attr' => 'color',
              'desc' => 'Color:',
              ),
            ),
          ),
        array(

          'option_group' => 'none',
          'disable' => true,
          'title' => 'Link',
          'fields' => array(
            array(
              'type' => 'select',
              'name' => 'font',
              'target' => 'none',
              'options' => array( 'Open Sans' => 'Open Sans', ),
              'desc' => 'Font:',
              ),
            array(
              'type' => 'color_picker',
              'name' => 'color',
              'target' => 'none',
              'attr' => 'color',
              'desc' => 'Color:',
              ),
            ),
          ),
        ),
      );

    $fields['content'] = array(
      'name' => 'Content',
      'field_groups' => array(
        array(
          'option_group' => 'contents',
          'title' => 'Content',
          'fields' => array(
            array(
              'type' => 'editor',
              'name' => 'header',
              'target' => '.cc-pu-header-section h2',
              'desc' => 'Header:',
              ),
            array(
              'type' => 'editor',
              'name' => 'subheader',
              'target' => '.cc-pu-subheader-section h3',
              'desc' => 'Subheader:',
              ),
            array(
              'type' => 'editor',
              'name' => 'content',
              'target' => '.cc-pu-content-section',
              'desc' => 'Content:',
              ),
            ),
          ),
        array(
          'option_group' => 'left_button',
          'title' => 'Button Left',
          'fields' => array(
            array(
              'type' => 'text',
              'action' => 'text',
              'name' => 'url',
              'attr' => 'href',
              'class' => 'remover',
              'target' => '.cc-pu-btn-left',
              'desc' => 'Button URL (if there is no URL provided, the button will not be displayed):',
              ),
            array(
              'type' => 'text',
              'action' => 'text',
              'name' => 'header',
              'target' => '.cc-pu-btn-left span',
              'desc' => 'Button Header Text:',
              ),
            array(
              'type' => 'text',
              'action' => 'text',
              'name' => 'subheader',
              'target' => '.cc-pu-btn-left small',
              'desc' => 'Button Subheader Text:',
              ),
            ),
          ),
        array(
          'option_group' => 'right_button',
          'title' => 'Button Right',
          'fields' => array(
            array(
              'type' => 'text',
              'action' => 'text',
              'name' => 'url',
              'attr' => 'href',
              'class' => 'remover',
              'target' => '.cc-pu-btn-right',
              'desc' => 'Button URL (if there is no URL provided, the button will not be displayed):',
              ),
            array(
              'type' => 'text',
              'action' => 'text',
              'name' => 'header',
              'target' => '.cc-pu-btn-right span',
              'desc' => 'Button Header Text:',
              ),
            array(
              'type' => 'text',
              'action' => 'text',
              'name' => 'subheader',
              'target' => '.cc-pu-btn-right small',
              'desc' => 'Button Subheader Text:',
              ),
            ),
          ),
        ),
      );

    $fields['button'] = array(
      'name' => 'Buttons',
      'field_groups' => array(
        array(
          'option_group' => 'none',
          'disable' => true,
          'title' => 'Close Button',
          'fields' => array(
            array(
              'type' => 'color_picker',
              'name' => 'color',
              'target' => 'none',
              'attr' => 'color',
              'desc' => 'Button Color:',
              ),
            array(
              'type' => 'color_picker',
              'name' => 'background',
              'target' => 'none',
              'attr' => 'background-color',
              'desc' => 'Background Color:',
              ),
            ),
          ),
        array(
          'option_group' => 'none',
          'disable' => true,
          'title' => 'Global Settings',
          'fields' => array(
            array(
              'type' => 'slider',
              'min' => '0',
              'max' => '50',
              'step' => '1',
              'name' => 'radius',
              'target' => 'none',
              'desc' => 'Border Radius:',
              ),
            array(
              'type' => 'select',
              'name' => 'border_style',
              'target' => 'none',
              'attr' => 'border-style',
              'desc' => 'Border Style:',
              'options' => array(
                'solid' => 'Solid',
                'dashed' => 'Dashed',
                'dotted' => 'Dotted',
                ),
              ),
            array(
              'type' => 'slider',
              'min' => '0',
              'max' => '50',
              'step' => '1',
              'name' => 'border_width',
              'target' => 'none',
              'attr' => 'border-width',
              'desc' => 'Border Width:',
              ),
            array(
              'type' => 'select',
              'name' => 'font',
              'target' => 'none',
              'options' => array( 'Open Sans' => 'Open Sans', ),
              'desc' => 'Button Header Font:',
              ),
            array(
              'type' => 'select',
              'name' => 'font',
              'target' => 'none',
              'options' => array( 'Open Sans' => 'Open Sans', ),
              'desc' => 'Button Subheader Font:',
              ),
            ),
          ),
        array(
          'option_group' => 'none',
          'disable' => true,
          'title' => 'Left Button',
          'fields' => array(
            array(
              'type' => 'color_picker',
              'name' => 'background',
              'target' => 'none',
              'attr' => 'background-color',
              'desc' => 'Button Color:',
              ),
            array(
              'type' => 'color_picker',
              'name' => 'color',
              'target' => 'none',
              'attr' => 'border-color',
              'desc' => 'Border Color:',
              ),

            array(
              'type' => 'color_picker',
              'name' => 'header_color',
              'target' => 'none',
              'attr' => 'color',
              'desc' => 'Button Header Text Color:',
              ),

            array(
              'type' => 'color_picker',
              'name' => 'subheader_color',
              'target' => 'none',
              'attr' => 'color',
              'desc' => 'Button Subheader Text Color:',
              ),
            ),
          ),
        array(
          'option_group' => 'none',
          'disable' => true,
          'title' => 'Right Button',
          'fields' => array(
            array(
              'type' => 'color_picker',
              'name' => 'background',
              'target' => 'none',
              'attr' => 'background-color',
              'desc' => 'Button Color:',
              ),
            array(
              'type' => 'color_picker',
              'name' => 'color',
              'target' => 'none',
              'attr' => 'border-color',
              'desc' => 'Border Color:',
              ),
            array(
              'type' => 'color_picker',
              'name' => 'header_color',
              'target' => 'none',
              'attr' => 'color',
              'desc' => 'Button Header Text Color:',
              ),
            array(
              'type' => 'color_picker',
              'name' => 'subheader_color',
              'target' => 'none',
              'attr' => 'color',
              'desc' => 'Button Subheader Text Color:',
              ),
            ),
          ),
        ),
      );

    return $this->build_tabs( $fields );
  }

  private function build_tabs( $fields ) {
    if ( !is_array( $this->fields ) )
      return;

    $controls = '';
    $i = 0;
    foreach ( $fields as $field ):

      $section_name = !empty( $field['name'] ) ? $field['name'] : 'Section';
      $controls .= '
				<h3 class="accordion-section-title" tabindex="' . $i . '">
					' . $section_name . '
					<span class="screen-reader-text">Press return or enter to expand</span> 
				</h3>';
      $controls .= '<div class="accordion-section-content">';

      foreach ( $field['field_groups'] as $option ):
        $controls .= $this->build_sections( $option );
      endforeach;
      $i++;
      $controls .= '</div>';
    endforeach;

    return $controls;
  }

  /**
   * Build fields groups
   *
   * @since     1.0.0
   *
   * @return    $section - html
   */
  private function build_sections( $fields ) {
    if ( !is_array( $fields ) )
      return;

    $section = '<div class="cc-pu-fields-wrapper">';

    if ( isset( $fields['disable'] ) ) {
      $section .= '
				<div class="cc-pu-overlay">
					<a href="http://ch-ch.org/pupro" target="_blank">AVAILABLE IN PRO</a>
				</div>';
    }

    $section .= '<h4>' . $fields['title'] . '</h4>';

    foreach ( $fields['fields'] as $field ):
      $type_func = 'build_field_' . $field['type'];
      $section .= $this->$type_func( $field, $fields['option_group'] );
    endforeach;

    $section .= ' </div>';

    return $section;

  }

  /**
   * Build slider field
   *
   * @since     1.0.0
   *
   * @return    $option_html - html
   */
  private function build_field_slider( $field, $options_group ) {

    $option_html = '<label>';
    $option_html .= '<span class="customize-control-title">' . $field['desc'] . '</span>';

    $option_html .= '<script type="text/javascript">
						jQuery(document).ready( function ($) { 
							 $( "#' . $this->template_id . '_' . $field['name'] . '-slider" ).slider({
								max: 1,
								min: 0,
								step: 0.1,
								value: 0 
							});
									 
						}); 
						</script>
						<div id="' . $this->template_id . '_' . $field['name'] . '-slider"></div>';

    $option_html .= '</label>';

    return $option_html;

  }

  /**
   * Build color picker field
   *
   * @since     1.0.0
   *
   * @return    $option_html - html
   */
  private function build_field_color_picker( $field, $options_group ) {

    $option_html = '<label class="cc-pu-option-active">';
    $option_html .= '<span class="customize-control-title">' . $field['desc'] . '</span>';
    $option_html .= '<input type="text" ';
    $option_html .= $this->build_field_attributes( $field, $options_group );
    $option_html .= '>';
    $option_html .= '</label>';

    return $option_html;
  }

  private function build_field_revealer( $field, $options_group ) {

    $options_prefix = $this->options_prefix;
    $template = $this->template_id;

    $name = $options_prefix . $options_group . '_' . $field['name'];
    $id = str_replace( '_', '-', $name );
    $target = $id . '-revealer';

    $options = $this->template_options[$options_group];

    $checked = $options[$field['name']] ? 'checked' : '';

    $option_html = '<label><span class="customize-control-title">' . $field['desc'] . '</span>';
    $option_html .= '
		<input 
			type="checkbox" 
			name="' . $name . '"
			id="' . $id . '" 
			class="revealer"
			data-customize-target="' . $target . '"    
			data-template="' . $template . '" 
			' . $checked . '
		>';

    $option_html .= '</label>';

    $hide = $options[$field['name']] ? '' : 'hide-section';

    $option_html .= '<div class="' . $hide . '" id="' . $target . '">';

    foreach ( $field['revaeals']['fields'] as $reveals ):
      $type_func = 'build_field_' . $reveals['type'];
      $option_html .= $this->$type_func( $reveals, $options_group );
    endforeach;

    $option_html .= '</div>';

    return $option_html;

  }

  /**
   * Build revealer group field
   *
   * @since     1.0.0
   *
   * @return    $option_html - html
   */
  private function build_field_revealer_group( $field, $options_group ) {

    $options_prefix = $this->options_prefix;
    $template = $this->template_id;

    $option_name = $field['name'];
    $name = $options_prefix . $options_group . '_' . $field['name'];
    $group = $options_group . '-' . $field['name'] . '-group';

    $options = $this->template_options[$options_group];

    $option_html = '<label>';
    $option_html .= '<span class="customize-control-title">' . $field['desc'] . '</span>';

    $option_html .= '<select 
						name="' . $name . '" 
						class="revealer-group" 
						data-group="' . $group . '"  
						data-customize-target="' . $field['target'] . '"  
						data-attr="' . $field['attr'] . '" 
						data-template="' . $template . '"  
						> ';

    if ( !empty( $field['options'] ) ):
      foreach ( $field['options'] as $val => $desc ):
        $selected = '';
        if ( $options[$field['name']] == $val ) {
          $selected = 'selected';
        }
        $option_html .= '<option value="' . $val . '" ' . $selected . '>' . $desc . '</option> ';
      endforeach;
    endif;

    $option_html .= '</select>';
    $option_html .= '</label>';

    foreach ( $field['revaeals'] as $reveals ):
      $hide = 'hide-section';
      if ( $this->template_options[$options_group][$option_name] == $reveals['section_id'] ) {
        $hide = 'cc-pu-option-active';
      }

      $option_html .= '<div class="' . $hide . ' ' . $group . ' revealer-wrapper" id="' . $reveals['section_id'] . '">';

      foreach ( $reveals['fields'] as $field ):
        $type_func = 'build_field_' . $field['type'];
        $option_html .= $this->$type_func( $field, $options_group );
      endforeach;

      $option_html .= '</div>';
    endforeach;

    return $option_html;

  }

  /**
   * Build text field
   *
   * @since     1.0.0
   *
   * @return    $option_html - html
   */
  private function build_field_text( $field, $options_group ) {

    $option_html = '<label class="cc-pu-option-active">';
    $option_html .= '<span class="customize-control-title">' . $field['desc'] . '</span>';

    $option_html .= '<input type="text" ';
    $option_html .= $this->build_field_attributes( $field, $options_group );
    $option_html .= '>';

    $option_html .= '</label>';

    return $option_html;

  }

  private function build_field_upload( $field, $options_group ) {
    $options_prefix = $this->options_prefix;
    $template = $this->template_id;

    $name = $options_prefix . $options_group . '_' . $field['name'];

    $options = $this->template_options[$options_group];

    $option_html = '<label><span class="customize-control-title">' . $field['desc'] . '</span>';
    $option_html .= '<input  
						type="text" 
						name="' . $name . '"
						id="' . $name . '" 
						value = "' . $options[$field['name']] . '"
						class="cc-pu-customize-style"
						data-customize-target="' . $field['target'] . '"  
						data-attr="' . $field['attr'] . '"  
						data-template="' . $template . '"  
						>';
    $option_html .= '<input class="cc-pu-image-upload button" type="button" value="Upload Image" data-target="' . $name . '"/>
							<br />' . $field['desc'];
    $option_html .= '</label>';
    return $option_html;

  }

  /**
   * Build select field
   *
   * @since     1.0.0
   *
   * @return    $option_html - html
   */
  private function build_field_select( $field, $options_group ) {

    $option_html = '<label><span class="customize-control-title">' . $field['desc'] . '</span>';

    $option_html .= '<select ';
    $option_html .= $this->build_field_attributes( $field, $options_group );
    $option_html .= '>';

    $option_html .= $this->build_field_values( $field, $options_group );

    $option_html .= '</select></label>';
    return $option_html;

  }

  private function build_field_font( $field, $options_group ) {
    $options_prefix = $this->options_prefix;
    $template = $this->template_id;

    $name = $options_prefix . $options_group . '_' . $field['name'];
    $options = $this->template_options[$options_group];

    $option_html = '<label><span class="customize-control-title">' . $field['desc'] . '</span>';
    $option_html .= '<select 
						name="' . $name . '" 
						data-id ="' . $options_group . '-font" 
						class="cc-pu-fonts cc-pu-customize-style"
						data-customize-target="' . $field['target'] . '"  
						data-attr="' . $field['attr'] . '"  
						data-template="' . $template . '"  
						> ';

    $fonts = $this->getFonts();

    if ( !empty( $fonts ) ):
      foreach ( $fonts as $val => $desc ):
        $selected = '';
        if ( $options[$field['name']] == $val ) {
          $selected = 'selected';
        }
        $option_html .= '<option value="' . $val . '" ' . $selected . '>' . $desc . '</option> ';
      endforeach;
    endif;
    $option_html .= '</select></label>';
    return $option_html;

  }

  private function build_field_editor( $field, $options_group ) {
    $options_prefix = $this->options_prefix;
    $template = $this->template_id;

    $options = $this->template_options[$options_group];

    $name = $options_prefix . $options_group . '_' . $field['name'];

    ob_start();

    $settings = array(
      'editor_class' => 'cc-pu-customize-content',
      'media_buttons' => false,
      'quicktags' => false,
      'textarea_name' => $name,
      'tinymce' => array(
        'toolbar1' => ', bold,italic,underline,link,unlink,forecolor,undo,redo',
        'toolbar2' => '',
        'toolbar3' => '' ) );

    echo '<label><span class="customize-control-title">' . $field['desc'] . '</span>';
    wp_editor( $options[$field['name']], $field['name'] . '_' . $template, $settings );

    echo '</label>';
    $option_html = ob_get_clean();
    return $option_html;

  }

  private function getFonts( $sort = 'alpha' ) {
    if ( $fonts = get_transient( get_class( $this ) . '_' . $sort ) ) {

    } else {
      $fonts = $this->fetchFonts( $sort );
      set_transient( get_class( $this ) . '_' . $sort, $fonts, 604800 ); // week
    }
    return $fonts;
  }

  private function fetchFonts( $sort = 'alpha' ) {

    $google_api_url = 'https://www.googleapis.com/webfonts/v1/webfonts?&sort=' . $sort;
    $response = wp_remote_retrieve_body( wp_remote_get( $google_api_url, array( 'sslverify' => false ) ) );

    $fallback = false;
    if ( $response && !is_wp_error( $response ) ) {
      $data = json_decode( $response, true );
      if ( isset( $data['error'] ) ) {
        $fallback = true;
      }
    } else {
      $fallback = true;
    }

    if ( $fallback ) {
      $font_list_file = CHCH_PUC_PLUGIN_DIR . 'admin/assets/js/api-fallback.json';
      $font_file_handle = fopen( $font_list_file, "r" );
      $contents = fread( $font_file_handle, filesize( $font_list_file ) );
      fclose( $font_file_handle );
      $data = json_decode( $contents, true );
    }

    $items = $data['items'];
    $fonts = array();
    foreach ( $items as $item ) {
      $fonts[str_replace( " ", "+", $item['family'] )] = $item['family'];
    }

    return $fonts;
  }

  /**
   * Build select radio
   *
   * @since     1.0.0
   *
   * @return    $option_html - html
   */
  private function build_field_select_class_switcher( $field, $options_group ) {
    $option_html = '';

    $radio_option = $this->template_options[$options_group][$field['name']];
    $option_html = '<label><span class="customize-control-title">' . $field['desc'] . '</span>';

    $option_html .= '<select class="select-class-switcher" data-old="' . $radio_option . '" ';
    $option_html .= $this->build_field_attributes( $field, $options_group );
    $option_html .= '>';

    $option_html .= $this->build_field_values( $field, $options_group );

    $option_html .= '</select></label>';
    return $option_html;

  }

  /**
   * Return field attributes
   *
   * @since     1.0.0
   *
   * @return    $option_html - html
   */
  function build_field_attributes( $atts, $options_group ) {

    $type = $atts['type'];

    $attributes = ' ';

    if ( isset( $atts['name'] ) && !empty( $atts['name'] ) ) {
      $name = $this->options_prefix . $options_group . '_' . $atts['name'];
    } else {
      $name = $this->options_prefix . $options_group . '_field';
    }

    if ( isset( $atts['id'] ) && !empty( $atts['id'] ) ) {
      $id = $atts['id'];
    } else {
      $id = $name;
    }

    $target = '';
    if ( isset( $atts['target'] ) && !empty( $atts['target'] ) ) {
      $target = $atts['target'];
    }

    $unit = '';
    if ( isset( $atts['unit'] ) && !empty( $atts['unit'] ) ) {
      $unit = $atts['unit'];
    }

    $attr = '';
    if ( isset( $atts['attr'] ) && !empty( $atts['attr'] ) ) {
      $attr = $atts['attr'];
    }

    $value = $this->build_field_values( $atts, $options_group );

    $action = '';

    if ( isset( $atts['action'] ) && !empty( $atts['action'] ) ) {
      if ( $atts['target'] !== 'none' ) {
        switch ( $atts['action'] ) {
          case 'css':
            $action = 'cc-pu-customize-style';
            break;
          case 'text':
            $action = 'cc-pu-customize-content';
            break;
        }
      }
    } else {
      switch ( $type ) {
        case 'color_picker':
          $action = 'cc-pu-colorpicker';
          break;

        case 'revealer':
          $action = 'revealer';
          break;

        case 'revealer_group':
          $action = 'revealer-group';
          break;

        case 'font':
          $action = 'cc-pu-fonts';
          break;

      }

      if ( ( $type != 'revealer' || $type != 'revealer_group' || $type != 'text' ) && $atts['target'] !== 'none' ) {
        $action .= ' cc-pu-customize-style';
      }
    }

    if ( isset( $atts['class'] ) && !empty( $atts['class'] ) ) {
      $action .= ' ' . $atts['class'];
    }

    $attributes .= 'name="' . $name . '" ';
    $attributes .= 'id="' . $id . '" ';
    $attributes .= 'class="' . $action . '" ';
    $attributes .= 'data-template="' . $this->template_id . '" ';
    $attributes .= 'data-customize-target="' . $target . '" ';

    if ( $unit ) {
      $attributes .= 'data-unit="' . $unit . '" ';
    }

    if ( $attr ) {
      $attributes .= 'data-attr="' . $attr . '" ';
    }

    $exclude_types = array(
      'revealer',
      'revealer_group',
      'select',
      'select_class_switcher' );
    if ( !in_array( $type, $exclude_types ) ) {
      $attributes .= 'value="' . $value . '" ';
    }

    return $attributes;
  }

  /**
   * get field values
   *
   * @since     1.0.0
   *
   * @return    $option_html - html
   */
  function build_field_values( $atts, $options_group ) {
    $option = '';
    if ( isset( $this->template_options[$options_group][$atts['name']] ) ) {
      $option = $this->template_options[$options_group][$atts['name']];
    }

    switch ( $atts['type'] ):
      case 'select':
      case 'select_class_switcher':
        $select_option = '';
        foreach ( $atts['options'] as $val => $desc ):
          $selected = '';
          if ( $option == $val ) {
            $selected = 'selected';
          }
          $select_option .= '<option value="' . $val . '" ' . $selected . '>' . $desc . '</option> ';
        endforeach;
        return $select_option;
        break;

      default:

        if ( !empty( $option ) ):
          return $option;
        else:
          return '';
        endif;

          break;
        endswitch;
        }
    }
