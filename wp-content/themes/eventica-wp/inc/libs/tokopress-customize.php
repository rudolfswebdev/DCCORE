<?php 
/**
 * This function incorporates code from 
 * 
 * 1) Kirki Customizer Framework.
 * 
 * The Kirki Customizer Framework, Copyright Aristeides Stathopoulos (@aristath),
 * is licensed under the terms of the GNU GPL, Version 2 (or later).
 * 
 * @link http://kirki.org
 * 
 * 2) Alpha Color Picker Customizer Control
 * 
 * The Alpha Color Picker Customizer Control, Copyright BraadMartin,
 * is licensed under the terms of the GNU GPL, Version 3 (or later).
 * 
 * @link https://github.com/BraadMartin/components
 * 
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function tokopress_customize_setting_type() {
	return apply_filters( 'tokopress_customize_setting_type', 'theme_mod' );
}

function tokopress_customize_setting_db() {
	return apply_filters( 'tokopress_customize_setting_db', THEME_NAME );
}

function tokopress_customize_get_mod( $field ) {
	if ( ! $field['setting'] ) {
		return false;
	}
	if ( ! isset( $field['default'] ) ) {
		$field['default'] = false;
	}
	if ( ! isset( $field['setting_type'] ) ) {
		$field['setting_type'] = tokopress_customize_setting_type();
	}
	if ( ! isset( $field['setting_db'] ) ) {
		$field['setting_db'] = '';
	}
	if ( $field['setting_type'] == 'option' ) {
		$value = get_option( $field['setting'], $field['default'] );
	}
	elseif ( $field['setting_type'] == 'option_mod' ) {
		if ( $field['setting_db'] ) {
			$setting_db = $field['setting_db'];
		}
		else {
			$setting_db = tokopress_customize_setting_db();
		}
		$setting_db_value = get_option( $setting_db );
		if ( isset($setting_db_value[$field['setting']]) ) {
			$value = $setting_db_value[$field['setting']];
		}
		else {
			$value = $field['default'];
		}
	}
	else {
		$value = get_theme_mod( $field['setting'], $field['default'] );
	}
	return $value;
}

function tokopress_sanitize_checkbox( $checked ) {
	return ( ( isset( $checked ) && true == $checked ) ? true : false );
}

function tokopress_sanitize_css( $css ) {
	return wp_strip_all_tags( $css );
}

function tokopress_sanitize_dropdown_pages( $page_id, $setting ) {
	$page_id = absint( $page_id );
	return ( 'publish' == get_post_status( $page_id ) ? $page_id : $setting->default );
}

function tokopress_sanitize_email( $email, $setting ) {
	$email = sanitize_email( $email );
	return ( ! null( $email ) ? $email : $setting->default );
}

function tokopress_sanitize_hex_color( $hex_color, $setting ) {
	$hex_color = sanitize_hex_color( $hex_color );
	return ( ! null( $hex_color ) ? $hex_color : $setting->default );
}

function tokopress_sanitize_html( $html ) {
	return wp_filter_post_kses( $html );
}

function tokopress_sanitize_image( $image, $setting ) {
    $mimes = array(
        'jpg|jpeg|jpe' => 'image/jpeg',
        'gif'          => 'image/gif',
        'png'          => 'image/png',
        'bmp'          => 'image/bmp',
        'tif|tiff'     => 'image/tiff',
        'ico'          => 'image/x-icon'
    );
    $file = wp_check_filetype( $image, $mimes );
    return ( $file['ext'] ? $image : $setting->default );
}

function tokopress_sanitize_nohtml( $nohtml ) {
	return wp_filter_nohtml_kses( $nohtml );
}

function tokopress_sanitize_number_absint( $number, $setting ) {
	$number = absint( $number );
	return ( $number ? $number : $setting->default );
}

function tokopress_sanitize_number_range( $number, $setting ) {
	$number = absint( $number );
	$choices = $setting->manager->get_control( $setting->id )->choices;
	$min = ( isset( $choices['min'] ) ? $choices['min'] : $number );
	$max = ( isset( $choices['max'] ) ? $choices['max'] : $number );
	$step = ( isset( $choices['step'] ) ? $choices['step'] : 1 );
	return ( $min <= $number && $number <= $max && is_int( $number / $step ) ? $number : $setting->default );
}

function tokopress_sanitize_select( $input, $setting ) {
	$input = sanitize_key( $input );
	$choices = $setting->manager->get_control( $setting->id )->choices;
	return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
}

function tokopress_sanitize_url( $url ) {
	return esc_url_raw( $url );
}

function tokopress_sanitize_unfiltered( $html ) {
	if ( current_user_can('unfiltered_html') ) {
		return $html;
	}
	else {
		return wp_filter_post_kses( $html );
	}
}

add_action( 'customize_register', 'tokopress_customize_register', 10 );
function tokopress_customize_register( $wp_customize ){

	if ( ! isset( $wp_customize ) ) {
		return;
	}

	class TokoPress_Customize_Slider_Control extends WP_Customize_Control {
		public $type = 'tokopress-slider';
		public function render_content() { 
			$type = $this->choices['type'];
			$value = $this->value();
			if ( 'font-size' == $type && 0 == $value ) {
				$value = '';
			}
			$slider_value = $value;
			if ( !$slider_value ) {
				$slider_value = 0;
			}
			?>
			<label>
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;
				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php endif; ?>
			</label>
			<div class="slider-input">
				<input type="text" class="control-slider-input" id="input_<?php echo $this->id; ?>" value="<?php echo esc_attr( $value ); ?>" <?php $this->link(); ?>/> <span class="slider-unit"><?php echo $this->choices['unit']; ?></span>
			</div>
			<div id="slider_<?php echo $this->id; ?>" class="control-slider" data-type="<?php echo esc_attr($type); ?>" data-value="<?php echo esc_attr($slider_value); ?>" data-min="<?php echo esc_attr($this->choices['min']); ?>" data-max="<?php echo esc_attr($this->choices['max']); ?>" data-step="<?php echo esc_attr($this->choices['step']); ?>"></div>
			<?php
		}
	}
	
	class TokoPress_Customize_Radio_Buttonset_Control extends WP_Customize_Control {
		public $type = 'tokopress-radio-buttonset';
		public function render_content() {
			if ( empty( $this->choices ) ) {
				return;
			}
			$name = '_customize-radio-'.$this->id;
			?>
			<label>
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;
				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php endif; ?>
			</label>
			<div id="input_<?php echo $this->id; ?>" class="buttonset control-buttonset">
				<?php foreach ( $this->choices as $value => $label ) : ?>
					<input type="radio" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $name ); ?>" id="<?php echo $this->id.esc_attr( $value ); ?>" <?php $this->link(); checked( $this->value(), $value ); ?>>
						<label for="<?php echo $this->id.esc_attr( $value ); ?>">
							<?php echo esc_html( $label ); ?>
						</label>
					</input>
				<?php endforeach; ?>
			</div>
			<?php
		}
	}

	class TokoPress_Customize_Radio_Image_Control extends WP_Customize_Control {
		public $type = 'tokopress-radio-image';
		public function render_content() {
			if ( empty( $this->choices ) ) {
				return;
			}
			$name = '_customize-radio-'.$this->id;
			?>
			<label>
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;
				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php endif; ?>
			</label>
			<div id="input_<?php echo $this->id; ?>" class="control-image">
				<?php foreach ( $this->choices as $value => $label ) : ?>
					<input class="image-select" type="radio" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $name ); ?>" id="<?php echo $this->id.esc_attr( $value ); ?>" <?php $this->link(); checked( $this->value(), $value ); ?>>
						<label for="<?php echo $this->id.esc_attr( $value ); ?>">
							<img src="<?php echo esc_html( $label ); ?>">
						</label>
					</input>
				<?php endforeach; ?>
			</div>
			<?php
		}
	}

	class TokoPress_Customize_Dropdown_Categories_Control extends WP_Customize_Control {
		public $type = 'tokopress-dropdown-categories';
		public function render_content() {
			?>
			<label>
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;
				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php endif; ?>
				<?php $dropdown = wp_dropdown_categories(
					array(
						'name'              => '_customize-dropdown-categories-' . $this->id,
						'echo'              => 0,
						'show_option_none'  => __( '&mdash; Select &mdash;', 'tokopress' ),
						'option_none_value' => '0',
						'selected'          => $this->value(),
					)
				);
				$dropdown = str_replace( '<select', '<select ' . $this->get_link(), $dropdown );
				echo $dropdown;
				?>
			</label>
			<?php
		}
	}

	class TokoPress_Customize_Custom_CSS_Control extends WP_Customize_Control {
		public $type = 'tokopress-custom-css';
		public function render_content() {
			?>
			<label>
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;
				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php endif; ?>
				<textarea rows="20" <?php $this->link(); ?> style="width:100%;resize:vertical;">
					<?php echo esc_textarea( $this->value() ); ?>
				</textarea>
			</label>
			<?php
		}
	}

	class TokoPress_Customize_Heading_Control extends WP_Customize_Control {
		public $type = 'tokopress-heading';
		public function render_content() {
			?>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>
			<?php
		}
	}

	class TokoPress_Customize_Warning_Control extends WP_Customize_Control {
		public $type = 'tokopress-warning';
		public function render_content() {
			?>
			<div class="customize-control-warning">
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>
			</div>
			<?php
		}
	}

	class TokoPress_Customize_Select2_Control extends WP_Customize_Control {
		public $type = 'tokopress-select2';
		public function render_content() {
			if ( empty( $this->choices ) )
				return;
			?>
			<label>
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;
				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php endif; ?>
				<select <?php $this->link(); ?> id="select2-<?php echo $this->id; ?>" class="control-select2">
					<?php
					foreach ( $this->choices as $value => $label )
						echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->value(), $value, false ) . '>' . $label . '</option>';
					?>
				</select>
			</label>
			<?php
		}
	}
	
	class TokoPress_Customize_Color_Control extends WP_Customize_Control {
		public $type = 'tokopress-color';
		public function render_content() {
			$palette = false;
			$show_opacity = true; 
			?>
			<label>
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;
				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php endif; ?>
				<input class="tokopress-color-control" type="text" data-show-opacity="<?php echo $show_opacity; ?>" data-palette="<?php echo esc_attr( $palette ); ?>" data-default-color="<?php echo esc_attr( $this->settings['default']->default ); ?>" <?php $this->link(); ?>  />
			</label>
			<?php
		}
	}

	$fields = apply_filters( 'tokopress_customize_controls', array() );

	if ( ! empty( $fields ) ) {
		foreach ( $fields as $field ) {

			$defaults = array(
				'setting_type'			=> tokopress_customize_setting_type(),
				'setting'				=> '',
				'setting_db'			=> '',
				'section'				=> 'colors',
				'panel'					=> '',
				'title'					=> '',
				'type'					=> '',
				'priority'				=> 10,
				'label'					=> '',
				'description'			=> '',
				'choices'				=> array(),
				'input_attrs'			=> array(),
				'default'				=> '',
				'capability'			=> 'edit_theme_options',
				'sanitize_callback'		=> '',
				'active_callback'		=> '',
				'transport'				=> 'refresh',
			);

			if ( 'textarea' == $field['type'] ) {
				$defaults['sanitize_callback'] = 'tokopress_sanitize_html';
			}
			elseif ( 'email' == $field['type'] ) {
				$defaults['sanitize_callback'] = 'tokopress_sanitize_email';
			}
			elseif ( 'url' == $field['type'] ) {
				$defaults['sanitize_callback'] = 'tokopress_sanitize_url';
			}
			elseif ( 'checkbox' == $field['type'] ) {
				$defaults['sanitize_callback'] = 'tokopress_sanitize_checkbox';
			}
			elseif ( 'dropdown-pages' == $field['type'] ) {
				$defaults['sanitize_callback'] = 'tokopress_sanitize_dropdown_pages';
			}
			elseif ( 'select' == $field['type'] ) {
				$defaults['sanitize_callback'] = 'tokopress_sanitize_select';
			}
			elseif ( 'image' == $field['type'] ) {
				$defaults['sanitize_callback'] = 'tokopress_sanitize_image';
			}
			elseif ( 'custom-css' == $field['type'] ) {
				$defaults['sanitize_callback'] = 'tokopress_sanitize_css';
			}
			else {
				$defaults['sanitize_callback'] = 'tokopress_sanitize_nohtml';
			}

			if ( !empty( $field['style'] ) ) {
				$defaults['transport'] = 'postMessage';
			} 
			if ( 'font' == $field['type'] ) {
				$defaults['transport'] = 'postMessage';
			} 
			if ( in_array( $field['type'], array( 'slider', 'font-size' ) ) ) {
				$defaults['default'] = 0;
			} 

			$field = wp_parse_args( $field, $defaults );

			if ( $field['setting'] && $field['type'] ) {

				$setting = $field['setting'];
				if ( $field['setting_type'] == 'option_mod' ) {
					if ( $field['setting_db'] ) {
						$setting_db = $field['setting_db'];
					}
					else {
						$setting_db = tokopress_customize_setting_db();
					}
					$field['setting_type'] = 'option';
					$setting = $setting_db.'['.$field['setting'].']';
				}
				else {
					$field['setting_type'] = 'theme_mod';
				}

				if ( in_array( $field['type'], array( 'slider', 'font-size', 'sidebar-width' ) ) ) {
					if ( !isset($field['choices']['min']) ) {
						$field['choices']['min'] = 0;
					}
					if ( !isset($field['choices']['max']) ) {
						$field['choices']['max'] = 100;
					}
					if ( !isset($field['choices']['step']) ) {
						$field['choices']['step'] = 1;
					}
					if ( !isset($field['choices']['unit']) ) {
						$field['choices']['unit'] = 'px';
					}
					if ( !isset($field['choices']['type']) ) {
						$field['choices']['type'] = $field['type'];
					}
				}

				if ( 'panel' == $field['type'] ) {
					$wp_customize->add_panel( 
						$field['setting'], 
						array(
							'title'				=> $field['title'],
							'priority'			=> $field['priority'],
						)
					);
				}
				elseif ( 'section' == $field['type'] ) {
					if ( $field['panel'] ) {
						$wp_customize->add_section( 
							$field['setting'], 
							array(
								'title'				=> $field['title'],
								'description'		=> $field['description'],
								'panel'				=> $field['panel'],
								'priority'			=> $field['priority'],
							) 
						);
					}
					else {
						$wp_customize->add_section( 
							$field['setting'], 
							array(
								'title'				=> $field['title'],
								'description'		=> $field['description'],
								'priority'			=> $field['priority'],
							) 
						);
					}
				}
				else {
					$wp_customize->add_setting(
						$setting,
						array(
							'default'			=> $field['default'],
							'type'				=> $field['setting_type'],
							'capability'		=> $field['capability'],
							'sanitize_callback'	=> $field['sanitize_callback'],
							'transport'			=> $field['transport'],
						)
					);
					if ( in_array( $field['type'], array( 'checkbox', 'dropdown-pages', 'text', 'textarea', 'email', 'number', 'tel', 'url' ) ) ) {
						$wp_customize->add_control(
							$setting,
							array(
								'settings'		=> $setting,
								'section'		=> $field['section'],
								'type'			=> $field['type'],
								'priority'		=> $field['priority'],
								'label'			=> $field['label'],
								'description'	=> $field['description'],
								'active_callback' => $field['active_callback'], 
							)
						);
					}
					elseif ( in_array( $field['type'], array( 'radio', 'select' ) ) && !empty( $field['choices'] ) ) {
						$wp_customize->add_control(
							$setting,
							array(
								'settings'		=> $setting,
								'section'		=> $field['section'],
								'type'			=> $field['type'],
								'priority'		=> $field['priority'],
								'label'			=> $field['label'],
								'description'	=> $field['description'],
								'choices'		=> $field['choices'],
								'active_callback' => $field['active_callback'], 
							)
						);
					}
					elseif ( 'color' == $field['type'] ) {
						$wp_customize->add_control(
							// new WP_Customize_Color_Control( 
							new TokoPress_Customize_Color_Control( 
								$wp_customize,
								$setting,
								array(
									'settings'		=> $setting,
									'section'		=> $field['section'],
									'priority'		=> $field['priority'],
									'label'			=> $field['label'],
									'description'	=> $field['description'],
									'active_callback' => $field['active_callback'], 
								)
							)
						);
					}
					elseif ( 'image' == $field['type'] ) {
						$wp_customize->add_control(
							new WP_Customize_Image_Control( 
								$wp_customize,
								$setting,
								array(
									'settings'		=> $setting,
									'section'		=> $field['section'],
									'priority'		=> $field['priority'],
									'label'			=> $field['label'],
									'description'	=> $field['description'],
									'active_callback' => $field['active_callback'], 
								)
							)
						);
					}
					elseif ( in_array( $field['type'], array( 'slider', 'font-size', 'sidebar-width' ) ) ) {
						$wp_customize->add_control(
							new TokoPress_Customize_Slider_Control( 
								$wp_customize,
								$setting,
								array(
									'settings'		=> $setting,
									'section'		=> $field['section'],
									'priority'		=> $field['priority'],
									'label'			=> $field['label'],
									'description'	=> $field['description'],
									'choices'		=> $field['choices'],
									'active_callback' => $field['active_callback'], 
								)
							)
						);
					}
					elseif ( 'radio-buttonset' == $field['type'] ) {
						$wp_customize->add_control(
							new TokoPress_Customize_Radio_Buttonset_Control( 
								$wp_customize,
								$setting,
								array(
									'settings'		=> $setting,
									'section'		=> $field['section'],
									'priority'		=> $field['priority'],
									'label'			=> $field['label'],
									'description'	=> $field['description'],
									'choices'		=> $field['choices'],
									'active_callback' => $field['active_callback'], 
								)
							)
						);
					}
					elseif ( 'radio-image' == $field['type'] ) {
						$wp_customize->add_control(
							new TokoPress_Customize_Radio_Image_Control( 
								$wp_customize,
								$setting,
								array(
									'settings'		=> $setting,
									'section'		=> $field['section'],
									'priority'		=> $field['priority'],
									'label'			=> $field['label'],
									'description'	=> $field['description'],
									'choices'		=> $field['choices'],
									'active_callback' => $field['active_callback'], 
								)
							)
						);
					}
					elseif ( 'dropdown-categories' == $field['type'] ) {
						$wp_customize->add_control(
							new TokoPress_Customize_Dropdown_Categories_Control( 
								$wp_customize,
								$setting,
								array(
									'settings'		=> $setting,
									'section'		=> $field['section'],
									'priority'		=> $field['priority'],
									'label'			=> $field['label'],
									'description'	=> $field['description'],
									'choices'		=> $field['choices'],
									'active_callback' => $field['active_callback'], 
								)
							)
						);
					}
					elseif ( 'custom-css' == $field['type'] ) {
						$wp_customize->add_control(
							new TokoPress_Customize_Custom_CSS_Control( 
								$wp_customize,
								$setting,
								array(
									'settings'		=> $setting,
									'section'		=> $field['section'],
									'priority'		=> $field['priority'],
									'label'			=> $field['label'],
									'description'	=> $field['description'],
									'choices'		=> $field['choices'],
									'active_callback' => $field['active_callback'], 
								)
							)
						);
					}
					elseif ( 'heading' == $field['type'] ) {
						$wp_customize->add_control(
							new TokoPress_Customize_Heading_Control( 
								$wp_customize,
								$setting,
								array(
									'settings'		=> $setting,
									'section'		=> $field['section'],
									'priority'		=> $field['priority'],
									'label'			=> $field['label'],
									'description'	=> $field['description'],
									'active_callback' => $field['active_callback'], 
								)
							)
						);
					}
					elseif ( 'warning' == $field['type'] ) {
						$wp_customize->add_control(
							new TokoPress_Customize_Warning_Control( 
								$wp_customize,
								$setting,
								array(
									'settings'		=> $setting,
									'section'		=> $field['section'],
									'priority'		=> $field['priority'],
									'label'			=> $field['label'],
									'description'	=> $field['description'],
									'active_callback' => $field['active_callback'], 
								)
							)
						);
					}
					elseif ( 'font' == $field['type'] ) {
						$wp_customize->add_control(
							new TokoPress_Customize_Select2_Control( 
								$wp_customize,
								$setting,
								array(
									'settings'		=> $setting,
									'section'		=> $field['section'],
									'priority'		=> $field['priority'],
									'label'			=> $field['label'],
									'description'	=> $field['description'],
									'choices'		=> tokopress_get_fonts(),
									'active_callback' => $field['active_callback'], 
								)
							)
						);
					}
				}
			}
		}
	}
}

add_action( 'customize_controls_enqueue_scripts', 'tokopress_customize_controls_enqueue_scripts' );
function tokopress_customize_controls_enqueue_scripts() {
	$version = '0.0.7';
	wp_enqueue_style( 'tokopress-select2', get_template_directory_uri() . '/css/select2.min.css', array(), '4.0.3' );
	wp_enqueue_script( 'tokopress-select2', get_template_directory_uri() . '/js/select2.min.js', array('jquery'), '4.0.3', false );
	wp_enqueue_script( 'tokopress-customize', get_template_directory_uri() . '/js/customize.js', array( 'jquery', 'jquery-ui-slider', 'jquery-ui-button', 'wp-color-picker', 'tokopress-select2' ), $version, true );
	wp_enqueue_style( 'tokopress-customize', get_template_directory_uri() . '/css/customize.css', array( 'wp-color-picker', 'tokopress-select2' ), $version );
}

add_filter( 'customize_save_after', 'tokopress_customize_save_after' );
function tokopress_customize_save_after() {
	$style = ' ';
	$fields = apply_filters( 'tokopress_customize_controls', array() );
	$fonts = array();
	if ( ! empty( $fields ) ) {
		foreach ( $fields as $field ) {
			$defaults = array(
				'setting_type'			=> tokopress_customize_setting_type(),
				'setting'				=> '',
				'setting_db'			=> '',
				'type'					=> '',
				'default'				=> '',
				'style'					=> '',
			);
			$field = wp_parse_args( $field, $defaults );
			if ( in_array( $field['type'], array( 'color', 'slider', 'image' ) ) && $field['setting'] && !empty( $field['style'] ) ) {
				if ( $value = tokopress_customize_get_mod( $field ) ) {
					$style .= str_replace( '[value]', $value, $field['style'] );
				}
			}
			elseif ( in_array( $field['type'], array( 'radio', 'select', 'radio-buttonset', 'radio-image' ) ) && $field['setting'] && !empty( $field['style'] ) ) {
				if ( $value = tokopress_customize_get_mod( $field ) ) {
					if ( isset( $field['style'][$value] ) ) {
						$style .= $field['style'][$value];
					}
				}
			}
			elseif ( in_array( $field['type'], array( 'font' ) ) && $field['setting'] && isset( $field['selector'] ) && $field['selector'] ) {
				$value = tokopress_customize_get_mod( $field );
				if ( $value && $value != 'default' && in_array( $value, array( 'serif', 'sans-serif' ) ) ) {
					$style .= $field['selector'].'{font-family:'.$value.';}';
				}
				else {
					$style .= $field['selector'].'{font-family:"'.$value.'";}';
					$fonts[$value] = $value;
				}
			}
			elseif ( in_array( $field['type'], array( 'font-size' ) ) && $field['setting'] && !empty( $field['style'] ) ) {
				$value = tokopress_customize_get_mod( $field );
				if ( $value > 0 ) {
					$style .= str_replace( '[value]', $value, $field['style'] );
				}
			}
			elseif ( in_array( $field['type'], array( 'sidebar-width' ) ) && $field['setting'] && !empty( $field['style'] ) ) {
				$value = tokopress_customize_get_mod( $field );
				if ( $value > 0 ) {
					$sidebar_style = $field['style'];
					$sidebar_style = str_replace( '[value]', $value, $sidebar_style );
					$sidebar_style = str_replace( '[100_value]', (100-$value), $sidebar_style );
					$style .= $sidebar_style;
				}
			}
			elseif ( ! function_exists( 'wp_update_custom_css_post' ) && in_array( $field['type'], array( 'custom-css' ) ) && $field['setting'] ) {
				if ( $value = tokopress_customize_get_mod( $field ) ) {
					if ( function_exists( 'tokopress_minify_css' ) ) {
						$style .= tokopress_minify_css( $value );
					}
					else {
						$style .= $value;
					}
				}
			}
		}
	}
	// Migrate any existing Custom CSS to the core option added in WordPress 4.7.
	if ( function_exists( 'wp_update_custom_css_post' ) ) {
		$css = get_theme_mod( 'tokopress_customcss' );
		if ( $css ) {
			$core_css = wp_get_custom_css(); 
			$return = wp_update_custom_css_post( $core_css . PHP_EOL . PHP_EOL . $css );
			if ( ! is_wp_error( $return ) ) {
				remove_theme_mod( 'tokopress_customcss' );
			}
		}
	}
	set_theme_mod( 'tokopress_customize_css', $style );
	set_theme_mod( 'tokopress_customize_fonts', $fonts );
}

add_action( 'tokopress_custom_styles', 'tokopress_style_customize_css' );
function tokopress_style_customize_css( $style ) {
	if ( ! is_customize_preview() ) {
		$css = get_theme_mod( 'tokopress_customize_css' );
		if ( $css === false ) {
			tokopress_customize_save_after();
			$css = get_theme_mod( 'tokopress_customize_css' );
		}
		echo tokopress_sanitize_css( $css );
	}
	if ( ! function_exists( 'wp_update_custom_css_post' ) ) {
		$customcss = get_theme_mod( 'tokopress_customcss' );
		echo tokopress_sanitize_css( $customcss );
	}
}

add_action( 'wp_enqueue_scripts', 'tokopress_enqueue_googlefonts' );
function tokopress_enqueue_googlefonts() {
	if ( ! is_customize_preview() ) {
		$fonts = get_theme_mod('tokopress_customize_fonts');
		if ( $fonts === false ) {
			tokopress_customize_save_after();
			$fonts = get_theme_mod( 'tokopress_customize_fonts' );
		}
		if ( is_array($fonts) && !empty( $fonts ) ) {
			$googlefonts = array();
			foreach ($fonts as $font) {
				$googlefonts[] = urlencode($font).':400,700';
			}
			$stylesheet = 'https://fonts.googleapis.com/css?family='.implode( '|', $googlefonts );
			wp_enqueue_style( 'googlefonts', $stylesheet );
		}
	}
}

add_action( 'wp_head', 'tokopress_customize_preview_head_css', 9999 );
function tokopress_customize_preview_head_css() {
	if ( ! is_customize_preview() ) {
		return;
	}
	$fields = apply_filters( 'tokopress_customize_controls', array() );
	if ( ! empty( $fields ) ) {
		foreach ( $fields as $field ) {
			$defaults = array(
				'setting_type'			=> tokopress_customize_setting_type(),
				'setting'				=> '',
				'setting_db'			=> '',
				'type'					=> '',
				'default'				=> '',
				'style'					=> '',
			);
			$field = wp_parse_args( $field, $defaults );
			if ( $field['setting_type'] == 'option_mod' ) {
				if ( $field['setting_db'] ) {
					$setting_db = $field['setting_db'];
				}
				else {
					$setting_db = tokopress_customize_setting_db();
				}
				$setting = $setting_db.'['.$field['setting'].']';
			}
			else {
				$setting = $field['setting'];
			}
			if ( in_array( $field['type'], array( 'color', 'slider', 'image' ) ) && $field['setting'] && !empty( $field['style'] ) ) {
				if ( $value = tokopress_customize_get_mod( $field ) ) {
					$style = str_replace( '[value]', $value, $field['style'] );
					echo '<style type="text/css" id="customize_'.$field['setting'].'">'.$style.'</style>';
				}
			}
			elseif ( in_array( $field['type'], array( 'radio', 'select', 'radio-buttonset', 'radio-image' ) ) && $field['setting'] && !empty( $field['style'] ) ) {
				if ( $value = tokopress_customize_get_mod( $field ) ) {
					if ( isset( $field['style'][$value] ) ) {
						$style = $field['style'][$value];
						echo '<style type="text/css" id="customize_'.$field['setting'].'">'.$style.'</style>';
					}
				}
			}
			elseif ( in_array( $field['type'], array( 'font' ) ) && $field['setting'] && isset( $field['selector'] ) && $field['selector'] ) {
				$value = tokopress_customize_get_mod( $field );
				if ( $value && $value != 'default' ) {
					if ( in_array( $value, array( 'serif', 'sans-serif' ) ) ) {
						echo '<style type="text/css" id="customize_'.$field['setting'].'">'.$field['selector'].'{ font-family: '.$value.'; }</style>';
					}
					else {
						echo '<link id="font_'.$field['setting'].'" href="https://fonts.googleapis.com/css?family='.urlencode($value).'" rel="stylesheet" type="text/css"><style type="text/css" id="customize_'.$field['setting'].'">'.$field['selector'].'{ font-family: "'.$value.'"; }</style>';
					}
				}
			}
			elseif ( in_array( $field['type'], array( 'font-size' ) ) && $field['setting'] && !empty( $field['style'] ) ) {
				$value = tokopress_customize_get_mod( $field );
				if ( $value > 0 ) {
					$style = str_replace( '[value]', $value, $field['style'] );
					echo '<style type="text/css" id="customize_'.$field['setting'].'">'.$style.'</style>';
				}
			}
			elseif ( in_array( $field['type'], array( 'sidebar-width' ) ) && $field['setting'] && !empty( $field['style'] ) ) {
				$value = tokopress_customize_get_mod( $field );
				if ( $value ) {
					$style = $field['style'];
					$style = str_replace( '[value]', $value, $style );
					$style = str_replace( '[100_value]', (100-$value), $style );
					echo '<style type="text/css" id="customize_'.$field['setting'].'">'.$style.'</style>';
				}
			}
		}
	}
}

add_action( 'customize_preview_init', 'tokopress_customize_preview_init' );
function tokopress_customize_preview_init() {
	add_action( 'wp_enqueue_scripts', 'tokopress_customize_preview_init_jquery' );
	add_action( 'wp_footer', 'tokopress_customize_preview_init_script_css', 20 );
}

function tokopress_customize_preview_init_jquery() {
	wp_enqueue_script( 'jquery' );
}

function tokopress_customize_preview_init_script_css() {
	$fields = apply_filters( 'tokopress_customize_controls', array() );
	if ( ! empty( $fields ) ) {
		echo '<script type="text/javascript"> ( function( $ ) { head = $(\'head\'); ';
		foreach ( $fields as $field ) {
			$defaults = array(
				'setting_type'			=> tokopress_customize_setting_type(),
				'setting'				=> '',
				'setting_db'			=> '',
				'type'					=> '',
				'default'				=> '',
				'style'					=> '',
			);
			$field = wp_parse_args( $field, $defaults );
			if ( $field['setting_type'] == 'option_mod' ) {
				if ( $field['setting_db'] ) {
					$setting_db = $field['setting_db'];
				}
				else {
					$setting_db = tokopress_customize_setting_db();
				}
				$setting = $setting_db.'['.$field['setting'].']';
			}
			else {
				$setting = $field['setting'];
			}
			if ( in_array( $field['type'], array( 'color', 'slider', 'image' ) ) && $field['setting'] && !empty( $field['style'] ) ) {
				$style_to = str_replace( '[value]', "' + to + '", $field['style'] );
				echo 'wp.customize(\''.$setting.'\',function( value ) { value.bind(function(to) { style = $(\'#customize_'.$field['setting'].'\'); style.remove(); if ( to ) { $(\'<style type="text/css" id="customize_'.$field['setting'].'">'.$style_to.'</style>\').appendTo( head ); } }); });';
			}
			elseif ( in_array( $field['type'], array( 'radio', 'select', 'radio-buttonset', 'radio-image' ) ) && $field['setting'] && !empty( $field['style'] ) ) {
				echo 'var customize_'.$field['setting'].' = '.json_encode( $field['style'] ).'; wp.customize(\''.$setting.'\',function( value ) { value.bind(function(to) { style = $(\'#customize_'.$field['setting'].'\'); style.remove(); if ( to ) { $(\'<style type="text/css" id="customize_'.$field['setting'].'">\' + customize_'.$field['setting'].'[to] + \'</style>\').appendTo( head ); } }); });';
			}
			elseif ( in_array( $field['type'], array( 'font' ) ) && $field['setting'] && isset( $field['selector'] ) && $field['selector'] ) {
				$value = tokopress_customize_get_mod( $field );
				echo 'wp.customize(\''.$setting.'\',function( value ) { value.bind(function(to) { style = $(\'#customize_'.$field['setting'].'\'); style.remove(); if ( to && to != \'default\' ) { if ( to == \'serif\' || to == \'sans-serif\' ) { $(\'<style type="text/css" id="customize_'.$field['setting'].'">'.$field['selector'].'{ font-family: \' + to + \'; }</style>\').appendTo( head ); } else { $(\'<style type="text/css" id="customize_'.$field['setting'].'">'.$field['selector'].'{ font-family: "\' + to + \'"; }</style>\').appendTo( head ); font = $(\'#font_'.$field['setting'].'\'); font.remove(); $(\'<link id="font_'.$field['setting'].'" href="https://fonts.googleapis.com/css?family=\' + to.replace(/ /g, \'+\') + \'" rel="stylesheet" type="text/css">\').appendTo( head ); } } }); });';
			}
			elseif ( in_array( $field['type'], array( 'font-size' ) ) && $field['setting'] && !empty( $field['style'] ) ) {
				$value = tokopress_customize_get_mod( $field );
				$style_to = str_replace( '[value]', "' + to + '", $field['style'] );
				echo 'wp.customize(\''.$setting.'\',function( value ) { value.bind(function(to) { style = $(\'#customize_'.$field['setting'].'\'); style.remove(); if ( to > 0 ) { $(\'<style type="text/css" id="customize_'.$field['setting'].'">'.$style_to.'</style>\').appendTo( head ); } }); });';
			}
			elseif ( in_array( $field['type'], array( 'sidebar-width' ) ) && $field['setting'] && !empty( $field['style'] ) ) {
				$style_to = $field['style'];
				$style_to = str_replace( '[value]', "' + to + '", $style_to );
				$style_to = str_replace( '[100_value]', "' + ( 100 - to ) + '", $style_to );
				echo 'wp.customize(\''.$setting.'\',function( value ) { value.bind(function(to) { style = $(\'#customize_'.$field['setting'].'\'); style.remove(); if ( to > 0 ) { $(\'<style type="text/css" id="customize_'.$field['setting'].'">'.$style_to.'</style>\').appendTo( head ); } }); });';
			}
		}
		echo '} )( jQuery ); </script>';
	}
}

function tokopress_get_fonts() {
	$fonts = array (
	'' => esc_html__( '-- select font --', 'tokopress' ),
	'sans-serif' => 'sans-serif',
	'serif' => 'serif',
	'Roboto' => 'Roboto',
	'Open Sans' => 'Open Sans',
	'Lato' => 'Lato',
	'Slabo 27px' => 'Slabo 27px',
	'Roboto Condensed' => 'Roboto Condensed',
	'Oswald' => 'Oswald',
	'Source Sans Pro' => 'Source Sans Pro',
	'Montserrat' => 'Montserrat',
	'Raleway' => 'Raleway',
	'PT Sans' => 'PT Sans',
	'Roboto Slab' => 'Roboto Slab',
	'Merriweather' => 'Merriweather',
	'Open Sans Condensed' => 'Open Sans Condensed',
	'Lora' => 'Lora',
	'Droid Sans' => 'Droid Sans',
	'Ubuntu' => 'Ubuntu',
	'Droid Serif' => 'Droid Serif',
	'Arimo' => 'Arimo',
	'Playfair Display' => 'Playfair Display',
	'PT Serif' => 'PT Serif',
	'Noto Sans' => 'Noto Sans',
	'Titillium Web' => 'Titillium Web',
	'PT Sans Narrow' => 'PT Sans Narrow',
	'Poppins' => 'Poppins',
	'Muli' => 'Muli',
	'Bitter' => 'Bitter',
	'Indie Flower' => 'Indie Flower',
	'Noto Serif' => 'Noto Serif',
	'Dosis' => 'Dosis',
	'Hind' => 'Hind',
	'Fjalla One' => 'Fjalla One',
	'Oxygen' => 'Oxygen',
	'Inconsolata' => 'Inconsolata',
	'Anton' => 'Anton',
	'Cabin' => 'Cabin',
	'Arvo' => 'Arvo',
	'Nunito' => 'Nunito',
	'Fira Sans' => 'Fira Sans',
	'Crimson Text' => 'Crimson Text',
	'Lobster' => 'Lobster',
	'Roboto Mono' => 'Roboto Mono',
	'Yanone Kaffeesatz' => 'Yanone Kaffeesatz',
	'Libre Baskerville' => 'Libre Baskerville',
	'Bree Serif' => 'Bree Serif',
	'Josefin Sans' => 'Josefin Sans',
	'Exo 2' => 'Exo 2',
	'Merriweather Sans' => 'Merriweather Sans',
	'Asap' => 'Asap',
	'Abel' => 'Abel',
	'Abril Fatface' => 'Abril Fatface',
	'Quicksand' => 'Quicksand',
	'Pacifico' => 'Pacifico',
	'Varela Round' => 'Varela Round',
	'Karla' => 'Karla',
	'Ubuntu Condensed' => 'Ubuntu Condensed',
	'Work Sans' => 'Work Sans',
	'Gloria Hallelujah' => 'Gloria Hallelujah',
	'Rubik' => 'Rubik',
	'Play' => 'Play',
	'Signika' => 'Signika',
	'Alegreya' => 'Alegreya',
	'Shadows Into Light' => 'Shadows Into Light',
	'Dancing Script' => 'Dancing Script',
	'Cuprum' => 'Cuprum',
	'Archivo Narrow' => 'Archivo Narrow',
	'Questrial' => 'Questrial',
	'Catamaran' => 'Catamaran',
	'Francois One' => 'Francois One',
	'Exo' => 'Exo',
	'Archivo Black' => 'Archivo Black',
	'Acme' => 'Acme',
	'Maven Pro' => 'Maven Pro',
	'EB Garamond' => 'EB Garamond',
	'PT Sans Caption' => 'PT Sans Caption',
	'Vollkorn' => 'Vollkorn',
	'Libre Franklin' => 'Libre Franklin',
	'Amatic SC' => 'Amatic SC',
	'Ropa Sans' => 'Ropa Sans',
	'Patua One' => 'Patua One',
	'Pathway Gothic One' => 'Pathway Gothic One',
	'Rokkitt' => 'Rokkitt',
	'Architects Daughter' => 'Architects Daughter',
	'Source Serif Pro' => 'Source Serif Pro',
	'Josefin Slab' => 'Josefin Slab',
	'Crete Round' => 'Crete Round',
	'Orbitron' => 'Orbitron',
	'Source Code Pro' => 'Source Code Pro',
	'Passion One' => 'Passion One',
	'Lobster Two' => 'Lobster Two',
	'Comfortaa' => 'Comfortaa',
	'News Cycle' => 'News Cycle',
	'Alegreya Sans' => 'Alegreya Sans',
	'Righteous' => 'Righteous',
	'Cinzel' => 'Cinzel',
	'Satisfy' => 'Satisfy',
	'Khula' => 'Khula',
	'Frank Ruhl Libre' => 'Frank Ruhl Libre',
	'Monda' => 'Monda',
	'ABeeZee' => 'ABeeZee',
	'Hammersmith One' => 'Hammersmith One',
	'Istok Web' => 'Istok Web',
	'Noticia Text' => 'Noticia Text',
	'Old Standard TT' => 'Old Standard TT',
	'Kaushan Script' => 'Kaushan Script',
	'Poiret One' => 'Poiret One',
	'Courgette' => 'Courgette',
	'Boogaloo' => 'Boogaloo',
	'Quattrocento Sans' => 'Quattrocento Sans',
	'BenchNine' => 'BenchNine',
	'Yellowtail' => 'Yellowtail',
	'Cookie' => 'Cookie',
	'Kanit' => 'Kanit',
	'Gudea' => 'Gudea',
	'Rajdhani' => 'Rajdhani',
	'Philosopher' => 'Philosopher',
	'Cormorant Garamond' => 'Cormorant Garamond',
	'Domine' => 'Domine',
	'Pontano Sans' => 'Pontano Sans',
	'Days One' => 'Days One',
	'Playfair Display SC' => 'Playfair Display SC',
	'Teko' => 'Teko',
	'Sanchez' => 'Sanchez',
	'Quattrocento' => 'Quattrocento',
	'Alfa Slab One' => 'Alfa Slab One',
	'Arapey' => 'Arapey',
	'Permanent Marker' => 'Permanent Marker',
	'Armata' => 'Armata',
	'Russo One' => 'Russo One',
	'Baloo Bhaina' => 'Baloo Bhaina',
	'Chewy' => 'Chewy',
	'Cardo' => 'Cardo',
	'Yantramanav' => 'Yantramanav',
	'Tinos' => 'Tinos',
	'Cabin Condensed' => 'Cabin Condensed',
	'Audiowide' => 'Audiowide',
	'Cantarell' => 'Cantarell',
	'Amiri' => 'Amiri',
	'Economica' => 'Economica',
	'Ruda' => 'Ruda',
	'Shrikhand' => 'Shrikhand',
	'Concert One' => 'Concert One',
	'Ek Mukta' => 'Ek Mukta',
	'Nunito Sans' => 'Nunito Sans',
	'Handlee' => 'Handlee',
	'Great Vibes' => 'Great Vibes',
	'Antic Slab' => 'Antic Slab',
	'Cairo' => 'Cairo',
	'Khand' => 'Khand',
	'Shadows Into Light Two' => 'Shadows Into Light Two',
	'Bad Script' => 'Bad Script',
	'Luckiest Guy' => 'Luckiest Guy',
	'Vidaloka' => 'Vidaloka',
	'Damion' => 'Damion',
	'Neuton' => 'Neuton',
	'Tangerine' => 'Tangerine',
	'Sigmar One' => 'Sigmar One',
	'Volkhov' => 'Volkhov',
	'Coming Soon' => 'Coming Soon',
	'Kreon' => 'Kreon',
	'Fredoka One' => 'Fredoka One',
	'Droid Sans Mono' => 'Droid Sans Mono',
	'Heebo' => 'Heebo',
	'Patrick Hand' => 'Patrick Hand',
	'Kalam' => 'Kalam',
	'Didact Gothic' => 'Didact Gothic',
	'Sarala' => 'Sarala',
	'Hind Siliguri' => 'Hind Siliguri',
	'Marck Script' => 'Marck Script',
	'Sacramento' => 'Sacramento',
	'Rambla' => 'Rambla',
	'Paytone One' => 'Paytone One',
	'Coda' => 'Coda',
	'Neucha' => 'Neucha',
	'Gentium Book Basic' => 'Gentium Book Basic',
	'Sintony' => 'Sintony',
	'Amaranth' => 'Amaranth',
	'Hind Vadodara' => 'Hind Vadodara',
	'Rock Salt' => 'Rock Salt',
	'Playball' => 'Playball',
	'Jura' => 'Jura',
	'Julius Sans One' => 'Julius Sans One',
	'Bevan' => 'Bevan',
	'Sorts Mill Goudy' => 'Sorts Mill Goudy',
	'Adamina' => 'Adamina',
	'Glegoo' => 'Glegoo',
	'Unica One' => 'Unica One',
	'Signika Negative' => 'Signika Negative',
	'Varela' => 'Varela',
	'Actor' => 'Actor',
	'Alice' => 'Alice',
	'Martel' => 'Martel',
	'Pragati Narrow' => 'Pragati Narrow',
	'Alegreya Sans SC' => 'Alegreya Sans SC',
	'Lusitana' => 'Lusitana',
	'Copse' => 'Copse',
	'PT Mono' => 'PT Mono',
	'Syncopate' => 'Syncopate',
	'Homemade Apple' => 'Homemade Apple',
	'Calligraffitti' => 'Calligraffitti',
	'Caveat' => 'Caveat',
	'Marvel' => 'Marvel',
	);
	$fonts = apply_filters( 'tokopress_fonts', $fonts );
	return $fonts;
}
