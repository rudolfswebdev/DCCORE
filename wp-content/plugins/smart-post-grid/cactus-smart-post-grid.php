<?php

/*
Plugin Name: Smart Posts Grid
Description: Smart Post Grid is the best plugin to showcase your posts
Author: CactusThemes
Version: 1.0.1
Author URI: http://www.cactusthemes.com
*/

define( 'SPG_PATH', plugin_dir_url( __FILE__ ) );
require_once ('admin/plugin-options.php');
// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

class cactusSmartPostGrid{
	public $spg_id = 1;
	//construct
	public function __construct()
    {
		add_shortcode( 's_post_grid', array( $this, 'spg_parse_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'spg_frontend_scripts' ) );

		add_action( 'wp_ajax_spg_content_render', array( $this, 'spg_content_render') );
		add_action( 'wp_ajax_nopriv_spg_content_render', array( $this, 'spg_content_render') );
		add_action( 'after_setup_theme', array( $this, 'spg_visual_composer') );

		add_filter( 'mce_external_plugins', array( $this, 'regplugins'));
		add_filter( 'mce_buttons_3', array( $this, 'regbtns') );
        
        add_action('init', array($this, 'init'));
    }
    
    function init(){
        load_plugin_textdomain( 'spg', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }
	
	/*
	 * Setup and do shortcode
	 */
	public function spg_parse_shortcode($atts, $content){
		$spg_options 			= $this->spg_get_all_option();
		
		$id 					= isset($atts['id']) ? $atts['id'] : '';
		$title        			= isset($atts['title']) ? $atts['title'] : '';  
		$title_link 			= isset($atts['title_link']) && $atts['title_link'] != '' ?  $atts['title_link'] : '#';
		$heading_icon  			= isset($atts['heading_icon']) ? $atts['heading_icon'] : '';
		$layout 				= isset($atts['layout']) ? $atts['layout'] : '1';
		$heading_style			= isset($atts['heading_style']) ? $atts['heading_style'] : '1'; //1-gradient, 2-bar
		$filter_style			= isset($atts['filter_style']) ? $atts['filter_style'] : '1'; //1-link, 2-tab, 3-carousel
		$cats 					= isset($atts['cats']) ? $atts['cats'] : '';
		$view_all_text 			= isset($atts['view_all_text']) && $atts['view_all_text'] != '' ? $atts['view_all_text'] : esc_html('VIEW ALL',"spg");	
			
		$atts['ajax_url']		= home_url( 'wp-admin/admin-ajax.php' );
		$cat_arr = array_filter(explode(",", $cats));
		
		if($heading_icon != ''){
			$icon_class = 'section-icon';
		}else{
			$icon_class = '';	
		}
		
		global $_cactus_scb_count;
		$_cactus_scb_count = $_cactus_scb_count ? $_cactus_scb_count : 0;
		$_cactus_scb_count++;
		
		ob_start();
		do_action('spg_before_grid');
		?>
	    <section <?php echo ($id != '' ? ('id="' . esc_attr($id)  . '"') : '');?> class="cactus-spg scb-heading-<?php echo esc_attr($heading_style); ?> scb-content-<?php echo esc_attr($layout); ?> scb-filter-<?php echo esc_attr($filter_style); ?> scb-id-<?php echo esc_attr($_cactus_scb_count); ?>">
	        <div class="section-inner">
	            <div class="section-header">
	                <?php if($title){ ?>
	                	<a href="<?php echo esc_url($title_link); ?>" class="btn btn-primary btn-sm <?php echo $icon_class != '' ? $icon_class : ''; ?>">
							<?php echo $heading_icon != '' ? '<i class="main-color-1 fa '.esc_attr($heading_icon).'"></i>' : ''; ?>
							<?php echo esc_html($title); ?></a>
					<?php } ?>
	                <?php if($filter_style == 1 && $title_link && $view_all_text != ' '){ ?>
	                     <a href="<?php echo esc_url($title_link); ?>" class="scb-filter-1-btn font-nav"><span class="font-nav-inner"><?php echo $view_all_text != ' ' ? esc_html($view_all_text) : ''; ?></span> <i class="fa fa-angle-right"></i></a>
	                     
	                <?php }elseif($filter_style == 2){ ?>
	                    <div class="scb-filter-2-btns">
	                        <a href="#scb-<?php echo esc_attr($_cactus_scb_count); ?>-all" data-toggle="tab" class="btn btn-grey btn-sm focus"><?php esc_html_e('ALL',"spg"); ?></a>
	                        <?php if(is_array($cat_arr) && count($cat_arr)){
								foreach($cat_arr as $cat_id){
									$atts_ajax = $atts;
									$atts_ajax['cats'] = $cat_id;
									if($atts['post_type'] == 'product'){
										$this_term = get_term_by((is_numeric($cat_id)?'id':'slug'),$cat_id,'product_cat');
									}else{
										$this_term = get_term_by((is_numeric($cat_id)?'id':'slug'),$cat_id,'category');
									}	
									?>
	                        		<a href="#scb-<?php echo esc_attr($_cactus_scb_count.'-'.$cat_id); ?>" data-toggle="tab" class="btn btn-grey btn-sm" data-ajax='<?php echo json_encode($atts_ajax); ?>'><?php echo $this_term->name;?>
	                                </a>
	                        <?php }//foreach
							} ?>
	                    </div>
	                    
	                <?php }//if filter ?>
	            </div>
	            <div class="section-content tab-content">
	            	<?php if($filter_style == 2){ echo '<div role="tabpanel" class="tab-pane fade in active" id="scb-'.esc_attr($_cactus_scb_count).'-all">'; } ?>
	                <?php $this->spg_content_render($atts); ?>
	                <?php if($filter_style == 2){
						echo '</div>';
						
						if(is_array($cat_arr) && count($cat_arr)){
							foreach($cat_arr as $cat_id){?>
								<div role="tabpanel" class="tab-pane fade" id="scb-<?php echo esc_attr($_cactus_scb_count.'-'.$cat_id); ?>"></div>
					<?php	}//foreach
						}
					}//if filter ?>
	            </div>
	        </div><!--/section-inner-->
	    </section><!--/ub-scb-->
	    
	    <?php
		wp_reset_postdata();
		do_action('spg_after_grid');
		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
	}

	//render content
	function spg_content_render($atts){
		if($atts == ''){
			$is_ajax = 1;
			$atts = $_GET;
		}
		
		global $show_meta, $show_category_tag,$layout;
		$post_type 				= isset($atts['post_type']) && $atts['post_type'] != '' ? $atts['post_type'] : 'post';
		$count 					= isset($atts['count']) && $atts['count'] != '' ? $atts['count'] : '10';
		$condition 				= isset($atts['condition']) ? $atts['condition'] : 'latest';
		$order 					= isset($atts['order']) ? $atts['order'] : 'DESC';
		$cats 					= isset($atts['cats']) ? $atts['cats'] : '';
		$tags 					= isset($atts['tags']) ? $atts['tags'] : '';
		$featured 				= isset($atts['featured']) ? $atts['featured'] : '';
		$ids 					= isset($atts['ids']) ? $atts['ids'] : '';	
		$show_category_tag 		= isset($atts['show_category_tag']) ? $atts['show_category_tag'] : '1';
		$layout 				= isset($atts['layout']) ? $atts['layout'] : '1';
		$filter_style			= isset($atts['filter_style']) ? $atts['filter_style'] : '1'; //1-link, 2-tab, 3-carousel
		$show_meta 				= isset($atts['show_meta']) ? $atts['show_meta'] : '1';
		$column 				= isset($atts['column']) ? $atts['column'] : '3';

		
		$page='1';
		
		$the_query = $this->spg_shortcode_query($count,$condition,$order,$cats,$tags,$featured,$ids,$page,$post_type);
		if($filter_style==3){//carousel
			echo '<div class="is-carousel single-carousel" data-navigation=1 data-dots=0 data-autoplay=0 data-fade=1>';
		}
		echo '<div class="row">';
		
		if($layout==2){
			if ( $the_query->have_posts() ) {
				$item_count = 0;
				while ( $the_query->have_posts() ) { $the_query->the_post();
					$item_count++;
					$item_col = 6;
					$title_class = '';
					$thumb_size = 'medium';
				?>
				<?php if($item_count%4==1 && $item_count>1){ echo '</div><div class="row">';} ?>
				<div class="scb-col-item col-sm-<?php echo esc_attr($item_col); ?>">
					<?php $this->_scb_item_render($thumb_size, $title_in_thumb=0, $title_class); ?>
				</div><!--/scb-col-item-->
			<?php
				}//while have_posts
			}//if have_posts
			
			
		}elseif($layout==3){
			if ( $the_query->have_posts() ) {
				$item_count = 0;
				while ( $the_query->have_posts() ) { $the_query->the_post();
					$item_count++;
					$item_col = 6;
					$title_class = 'h5';
					$thumb_size = 'thumbnail';
					$item_class = 'scb-small-item';
					
					if($item_count%4==1){
						$thumb_size = 'large';
						$title_class = 'h4';
						$item_class = '';
					}
				?>
				<?php if($item_count%4==1 && $item_count>1){ echo '</div><div class="row">';} ?>
				<div class="scb-col-item col-sm-<?php echo esc_attr($item_col); ?>">
					<?php $this->_scb_item_render($thumb_size, $title_in_thumb=$item_count%4==1, $title_class, $item_class); ?>
				</div><!--/scb-col-item-->
			<?php
				}//while have_posts
			}//if have_posts
			
			
		}elseif($layout == 4){
			
			if($column == '1'){
				$item_col_wrap = '12';
			}else if ($column == '2'){
				$item_col_wrap = '6';
			}else{
				$item_col_wrap = '4';
			}
			
			echo '<div class="col-sm-'.$item_col_wrap.'">';
			if ( $the_query->have_posts() ) {
				
				$item_count = 0;
				while ( $the_query->have_posts() ) { $the_query->the_post();
					$item_count++;
					$item_col = 0;
					$title_class = 'h5';
					$thumb_size = 'thumbnail';
					$item_class = 'scb-small-item';
					
					if($item_count%4==1){
						$thumb_size = 'large';
						$title_class = 'h4';
						$item_class = '';
					}
				?>
				<?php
					if($column == '1'){
						if($item_count%4==1 && $item_count>1){
							echo '</div></div><div class="row"><div class="col-sm-'.$item_col_wrap.'">';
						}
					}else if ($column == '2'){
						if($item_count%8==1 && $item_count>1){
							echo '</div></div><div class="row"><div class="col-sm-'.$item_col_wrap.'">';
						}elseif($item_count%4==1 && $item_count>1){
							echo '</div><div class="col-sm-'.$item_col_wrap.'">';
						} 
					}else{
						if($item_count%12==1 && $item_count>1){
							echo '</div></div><div class="row"><div class="col-sm-'.$item_col_wrap.'">';
						}elseif($item_count%4==1 && $item_count>1){
							echo '</div><div class="col-sm-'.$item_col_wrap.'">';
						} 
					}
				
				?>
				
				<div class="scb-col-item col-sm-<?php echo esc_attr($item_col); ?>">
					<?php $this->_scb_item_render($thumb_size, $title_in_thumb=$item_count%4==1, $title_class, $item_class); ?>
				</div><!--/scb-col-item-->
			<?php
				}//while have_posts
			}//if have_posts
			echo '</div>'; //col-sm-6
			
			
		}elseif($layout==5 || $layout==6){
			if($column == '1'){
				$item_col = '12 col-sm-12';
			}else if ($column == '2'){
				$item_col = '6 col-sm-6';
			}else{
				$item_col = '4 col-sm-6';
			}
			if ( $the_query->have_posts() ) {
				$item_count = 0;
				while ( $the_query->have_posts() ) { $the_query->the_post();
					$item_count++;
					$title_class = 'h5';
					$thumb_size = 'thumbnail';
					$item_class = 'scb-small-item';
				?>
				<?php
				if($item_count%6==1 && $item_count>1){
					echo '</div><div class="row">';
				}?>
				<div class="scb-col-item col-md-<?php echo esc_attr($item_col); ?>">
					<?php $this->_scb_item_render($thumb_size, $title_in_thumb=0, $title_class, $item_class); ?>
				</div><!--/scb-col-item-->
			<?php
				}//while have_posts
			}//if have_posts
	
			
			
		}elseif($layout==7){
			if ( $the_query->have_posts() ) {
				$divide_number = 3;
				if($column == '1'){
					$item_col = '12';
				}else if ($column == '2'){
					$item_col = '6';
					$divide_number = 2;
				}else{
					$item_col = '4';
				}
				$item_count = 0;
				while ( $the_query->have_posts() ) { $the_query->the_post();
					$item_count++;
					$title_class = '';
					$thumb_size = 'medium';
				?>
				<?php if($item_count%$divide_number==1 && $item_count>1){ echo '</div><div class="row">';} ?>
				<div class="scb-col-item col-md-<?php echo esc_attr($item_col); ?>">
					<?php $this->_scb_item_render($thumb_size, $title_in_thumb=0, $title_class); ?>
				</div><!--/scb-col-item-->
			<?php
				}//while have_posts
			}//if have_posts
			
			
		}elseif($layout==8){
			if ( $the_query->have_posts() ) {
				$item_count = 0;
				while ( $the_query->have_posts() ) { $the_query->the_post();
					$item_count++;
					$item_col = 'col-md-3 col-sm-6';
					$title_class = '';
					$thumb_size = 'medium';
				?>
				<?php if($item_count%8==1 && $item_count>1){ echo '</div><div class="row">';} ?>
				<div class="scb-col-item <?php echo esc_attr($item_col); ?>">
					<?php $this->_scb_item_render($thumb_size, $title_in_thumb=0, $title_class); ?>
				</div><!--/scb-col-item-->
			<?php
				}//while have_posts
			}//if have_posts
			
			
		}elseif($layout==9){
			if ( $the_query->have_posts() ) {
				if($column == '1'){
					$item_col = '12 col-sm-12';
				}else if ($column == '2'){
					$item_col = '6 col-sm-6';
				}else{
					$item_col = '4 col-sm-6';
				}
				$item_count = 0;
				while ( $the_query->have_posts() ) { $the_query->the_post();
					$item_count++;
					$title_class = 'h3';
					$thumb_size = 'medium';
				?>
				<?php if($item_count%6==1 && $item_count>1 && $filter_style==3){ echo '</div><div class="row">';} ?>
				<div class="scb-col-item col-md-<?php echo esc_attr($item_col); ?>">
					<?php $this->_scb_item_render($thumb_size, $title_in_thumb=0, $title_class, $item_class='scb-masonry-item'); ?>
				</div><!--/scb-col-item-->
			<?php
				}//while have_posts
				
				//script
				wp_enqueue_script('isotope');
				wp_add_inline_script ('isotope', '
				jQuery(window).load(function() {
					jQuery(".scb-content-9 .row").isotope({
					  itemSelector: ".scb-col-item",
					  percentPosition: true,
					  masonry: {
						columnWidth: ".scb-col-item"
					  },
					});
				});
				', 'after' );
				
			}//if have_posts
			
			
		}else{ //style 1
			if ( $the_query->have_posts() ) {
				$item_count = 0;
				while ( $the_query->have_posts() ) { $the_query->the_post();
					$item_count++;
					$item_col = 3;
					$title_class = '';
					$thumb_size = 'medium';
					
					if($item_count%3==1){
						$item_col = 6;
						$thumb_size = 'large';
						$title_class = 'h2';
					}
				?>
				<?php if($item_count%3==1 && $item_count>1){ echo '</div><div class="row">';} ?>
				<div class="scb-col-item col-sm-<?php echo esc_attr($item_col); ?>">
					<?php $this->_scb_item_render($thumb_size, $title_in_thumb=$item_count%3==1, $title_class); ?>
				</div><!--/scb-col-item-->
			<?php
				}//while have_posts
			}//if have_posts
			
			
		}//if style
		
		echo '</div><!--.row-->';
		if($filter_style == 3){//carousel
			echo '</div>';
		}
		wp_reset_postdata();
	}
	
	
	//render item
	function _scb_item_render($thumb_size='medium', $title_in_thumb=0, $title_class='', $item_class=''){
		$spg_options = $this->spg_get_all_option();
		
		add_filter( 'excerpt_length', array( $this, 'spg_excerpt_length') );
		global $show_meta, $show_category_tag,$layout;
		
		$show_meta = @$spg_options['spg_hide_meta']?0:$show_meta;
		$show_category_tag = @$spg_options['spg_hide_category']?0:$show_category_tag;
		
		do_action('spg_before_post_item');
		?>
		
		<div class="scb-item <?php echo esc_attr($item_class); ?>">
			<?php if($item_class=='scb-masonry-item'){ ?>
			<div class="item-content-ontop text-center">
				<div class="item-label-incontent font-nav">
					<?php if($show_category_tag == '1'){ $this->spg_get_category($show_class=0);} ?>
				</div>
				<h3 class="item-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
				<?php if($show_meta == '1') { $this->spg_sc_item_meta();} ?>
			</div>
			<?php } ?>
			<?php if(has_post_thumbnail()){ ?>
			
			<div class="item-thumbnail dark-div">
                <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
					<?php echo $this->spg_thumbnail($thumb_size); ?>
                
					<?php if(@$spg_options['spg_hide_icon']!=1){
						if(get_post_format()=='video'){?>
                        <span class="item-icon"><i class="fa fa-play-circle-o"></i></span>
                    <?php }elseif(get_post_format()=='audio'){?>
                        <span class="item-icon"><i class="fa fa-volume-up"></i></span>
                    <?php }elseif(get_post_format()=='gallery'){?>
                        <span class="item-icon"><i class="fa fa-picture-o"></i></span>
                    <?php }elseif(get_post_format()=='quote'){?>
                        <span class="item-icon"><i class="fa fa-quote-right"></i></span>
                    <?php }
					}?>
                    
                    <?php if($title_in_thumb){?>
                        <div class="item-content-inthumb">
                            <h4 class="<?php echo esc_attr($title_class); ?> item-title"><?php the_title(); ?></h4>
                            <?php if($show_meta == '1') { $this->spg_sc_item_meta(0);} ?>
                        </div>
                    <?php }?>
                </a>
				<div class="item-label">
					<?php if($show_category_tag == '1'){ $this->spg_get_category($show_class=1);} ?>
				</div>
			</div>
			<?php } //has thumb?>
			<div class="item-content">
				<?php if(!$title_in_thumb && $item_class!='scb-masonry-item'){?>
					<h4 class="<?php echo esc_attr($title_class); ?> item-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
					<?php if($show_meta == '1') { $this->spg_sc_item_meta();} ?>
				<?php }?>
				
				<div class="item-excerpt">
					<?php the_excerpt(); ?>
				</div><!-- .excerpt -->
				<?php if($item_class=='scb-masonry-item'){ ?>
					<?php $category = get_the_category();?>
					
					<a class="item-readmore-line item-readmore-line-<?php echo esc_attr($category[0]->term_id);?>-hover" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><i>+</i></a>
					
				<?php }elseif(@$spg_options['spg_hide_readmore']!=1){ ?>
					<a class="item-readmore font-nav" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><i class="fa fa-plus-circle"></i> <span><?php esc_html_e('READ MORE',"spg"); ?></span></a>
				<?php }//if masonry ?>
			</div>
			<div class="clearfix"></div>
		</div><!--/scb-item-->
	<?php
		remove_filter( 'excerpt_length', array( $this, 'spg_excerpt_length') );
		do_action('spg_after_post_item');
	}
	
	function spg_excerpt_length( $length ) {
		$spg_options = $this->spg_get_all_option();
		$length = @$spg_options['spg_excerpt'] ? $spg_options['spg_excerpt'] : 18;
		return $length;
	}
	
	//Register Visual composer
	function spg_visual_composer(){
		if(function_exists('vc_map')){
		vc_map(array(
				"name"		=> esc_html__("Smart Posts Grid", "spg"),
				"base"		=> "s_post_grid",
				"class"		=> "wpb_vc_posts_slider_widget",
				"params" => 	array(
					array(
					"type" => "textfield",
					"heading" => esc_html__("Title", "spg"),
					"param_name" => "title",
					"value" => "",
					"description" => ''
				),
				array(
					"type" => "textfield",
					"heading" => esc_html__("Title Link", "spg"),
					"param_name" => "title_link",
					"value" => "",
					"description" => ''
				),
				array(
					"type" => "dropdown",
					"admin_label" => true,
					"heading" => esc_html__("Layout", "spg"),
					"param_name" => "layout",
					"value" => array(
						esc_html__("Layout 1","spg")=>'1',
						esc_html__("Layout 2","spg")=>'2',
						esc_html__("Layout 3","spg")=>'3',
						esc_html__("Layout 4","spg")=>'4',
						esc_html__("Layout 5","spg")=>'5',
						esc_html__("Layout 6","spg")=>'6',
						esc_html__("Layout 7","spg")=>'7',
						esc_html__("Layout 8","spg")=>'8',
						esc_html__("Layout 9","spg")=>'9',
					),
					"description" => esc_html__("Choose box layout.", "spg")
				),
				array(
					"type" => "dropdown",
					"heading" => esc_html__("Column", "spg"),
					"param_name" => "column",
					"std" => "3",
					"value" => array(
						esc_html__("1 column","spg")=>'1',
						esc_html__("2 columns","spg")=>'2',
						esc_html__("3 columns","spg")=>'3',
					),
					"dependency" => array(
							"element" => "layout",
							"value" => array( '4', '5', '6', '7', '9' ),
					 ),
					"description" => esc_html__("Choose Column for layout  4,5,6,7,9", "spg")
				),
				array(
					"type" => "textfield",
					"admin_label" => true,
					"heading" => esc_html__("Count", "spg"),
					"param_name" => "count",
					"value" => "",
					"description" => esc_html__('Number of items to show', "spg")
				),
				array(
					"type" => "textfield",
					"admin_label" => true,
					"heading" => esc_html__("Post Type", "spg"),
					"param_name" => "post_type",
					"value" => "",
					"description" => esc_html__('Post type slug to query', "spg")
				),	
				array(
					"type" => "dropdown",
					"admin_label" => true,
					"heading" => esc_html__("Condition", "spg"),
					"param_name" => "condition",
					"value" => array(
						esc_html__("Latest","spg")=>'latest',
						esc_html__("Most viewed*","spg")=>'view',
						esc_html__("Most Liked*","spg")=>'like',
						esc_html__("Most commented","spg")=>'comment',
						esc_html__("Title","spg")=>'title',
						esc_html__("IDs (only available when using IDs parameter)","spg")=>'input',
						esc_html__("Random","spg")=>'random',
	
					),
					"description" => esc_html__("Condition to query items (*need WTI Like post & Top 10 plugin)", "spg")
				),
				array(
					"type" => "dropdown",
					"admin_label" => true,
					"heading" => esc_html__("Order by", "spg"),
					"param_name" => "order",
					"value" => array( 
						esc_html__("Descending", "spg") => "DESC", 
						esc_html__("Ascending", "spg") => "ASC" ),
					"description" => esc_html__('Designates the ascending or descending order', "spg")
				),
				array(
				  "type" => "textfield",
				  "heading" => esc_html__("Categories", "spg"),
				  "param_name" => "cats",
				  "description" => esc_html__("list of categories (ID) to query items from, separated by a comma.", "spg")
				),
				array(
					"type" => "textfield",
					"heading" => esc_html__("Tags", "spg"),
					"param_name" => "tags",
					"value" => "",
					"description" => esc_html__('list of tags to query items from, separated by a comma. For example: tag-1, tag-2, tag-3', "spg")
				),
				array(
					"type" => "textfield",
					"heading" => esc_html__("IDs", "spg"),
					"param_name" => "ids",
					"value" => "",
					"description" => esc_html__('list of post IDs to query, separated by a comma. If this value is not empty, cats, tags and featured are omitted', "spg")
				),
				array(
					"type" => "dropdown",
					"admin_label" => true,
					"heading" => esc_html__("Filter/arrange Style", "spg"),
					"param_name" => "filter_style",
					"value" => array( 
						esc_html__("No", "spg") => "1",
						esc_html__("Categories Filter", "spg") => "2",
						esc_html__("Carousel", "spg") => "3" ), 
					"description" => esc_html__('Choose filter/arrange style', "spg")
				),
				array(
					"type" => "dropdown",
					"admin_label" => true,
					"heading" => esc_html__("Heading Style", "spg"),
					"param_name" => "heading_style",
					"value" => array( 
					esc_html__("Gradient", "spg") => "1", 
					esc_html__("Bar", "spg") => "2" ),
					"description" => ''
				),
				array(
				  "type" => "textfield",
				  "heading" => esc_html__("Heading Icon", "spg"),
				  "param_name" => "heading_icon",
				  "description" => esc_html__("Name of Font icon.", "spg"),
				),
				array(
					"type" => "dropdown",
					"heading" => esc_html__("Show category tag", "spg"),
					"param_name" => "show_category_tag",
					"value" => array( 
						esc_html__("Yes", "spg") => "1", 
						esc_html__("No", "spg") => "0" ),
				),
				array(
					"type" => "dropdown",
					"heading" => esc_html__("Show Meta", "spg"),
					"param_name" => "show_meta",
					"value" => array( 
						esc_html__("Yes", "spg") => "1", 
						esc_html__("No", "spg") => "0" ),
					"description" => ''
				),
				array(
				  "type" => "textfield",
				  "heading" => esc_html__("Change View All text", "spg"),
				  "param_name" => "view_all_text",
				  "description" => esc_html__("Set custom title for view all button, leave blank to get default or leave a space to hide it.", "spg"),
				),
				
				)
			));
		}
	}
	
	/*
	 * Get all plugin options
	 */
	public static function spg_get_all_option(){
		global $spg_options;
		$spg_options = get_option('spg_options_group');
		$spg_options['spg_criteria'] = isset($spg_options['spg_criteria'])?$spg_options['spg_criteria']:'';
		$spg_options['spg_position'] = isset($spg_options['spg_position'])?$spg_options['spg_position']:'bottom';
		$spg_options['spg_float'] = isset($spg_options['spg_float'])?$spg_options['spg_float']:'block';
		$spg_options['spg_fontawesome'] = isset($spg_options['spg_fontawesome'])?$spg_options['spg_fontawesome']:0;
		$spg_options['spg_title']= isset($spg_options['spg_title'])?$spg_options['spg_title']:'';
		$spg_options['spg_user_rate']= isset($spg_options['spg_user_rate'])?$spg_options['spg_user_rate']:'all';
		$spg_options['spg_rate_type']= isset($spg_options['spg_rate_type'])?$spg_options['spg_rate_type']:'point';
		return $spg_options;
	}
	
	/*
	 * Load js and css
	 */
	function spg_frontend_scripts(){
		$spg_options = $this->spg_get_all_option();
		
	  	wp_enqueue_script( 'spg_js', SPG_PATH.'js/main.js', array('jquery'), 1, true );
		wp_enqueue_script( 'slick', SPG_PATH.'js/slick/slick.min.js', array( 'jquery' ),'1.6',true );
		wp_register_script( 'isotope', SPG_PATH.'js/isotope.pkgd.min.js', array( 'jquery' ),'3.0',true );
		
		if(isset($spg_options['spg_bootstrap']) && $spg_options['spg_bootstrap']==0){
			wp_enqueue_style('bootstrap-core', SPG_PATH.'css/bootstrap-core.css');
		}
		wp_enqueue_style( 'slick-style', SPG_PATH.'js/slick/slick.css');
		wp_enqueue_style( 'slick-theme', SPG_PATH.'js/slick/slick-theme.css');
		wp_enqueue_style( 'spg_css', SPG_PATH.'/css/style.css');
		
		if($spg_options['spg_fontawesome']==0){
			wp_enqueue_style('font-awesome', SPG_PATH.'font-awesome/css/font-awesome.min.css'); //remove load font awesome
		}
	}


	function regbtns($buttons)
	{
		array_push($buttons, 's_post_grid');
		return $buttons;
	}

	function regplugins($plgs)
	{
		$plgs['s_post_grid'] = SPG_PATH . 'js/button.js';
		return $plgs;
	}

	function spg_shortcode_query($number,$conditions,$sort_by,$categories,$tags,$featured,$ids,$paged,$post_type='post') {
		if($conditions=='view' && $ids==''){
			  
			$args = array(
				'daily' => 0,
				'post_types' =>'post',
			);
			$ids = $this->spg_get_tptn_pop_posts($args);
			
			$args = array(
				'post_type' => $post_type,
				'posts_per_page' => $number,
				'post__in'=> $ids,
				'orderby'=> 'post__in',
				'post_status' => 'publish',
				'tag' => $tags,
				'ignore_sticky_posts' => 1
			);	
						
		}elseif($conditions=='comment' && $ids==''){
			$args = array(
				'post_type' => $post_type,
				'posts_per_page' => $number,
				'orderby' => 'comment_count',
				'order' => $sort_by,
				'post_status' => 'publish',
				'tag' => $tags
				);
				
		}elseif($conditions=='high_rated' && $ids==''){
			$args = array(
				'post_type' => $post_type,
				'posts_per_page' => $number,
				'meta_key' => '_count-views_all',
				'orderby' => 'meta_value_num',
				'order' => $sort_by,
				'post_status' => 'publish',
				'tag' => $tags,
				'ignore_sticky_posts' => 1
				);
		} elseif($ids!=''){
			$ids = explode(",", $ids);
			$gc = array();
			$flag=0;
			foreach ( $ids as $grid_cat ) {
				$flag++;
				array_push($gc, $grid_cat);
			}
			$args = array(
				'post_type' => $post_type,
				'posts_per_page' => $number,
				'order' => 'post__in',
				'post_status' => 'publish',
				'tag' => $tags,
				'post__in' =>  $gc,
				'ignore_sticky_posts' => 1);

		} elseif($ids=='' && $conditions=='latest'){
			$args = array(
				'post_type' => $post_type,
				'posts_per_page' => $number,
				'order' => $sort_by,
				'post_status' => 'publish',
				'tag' => $tags,
				'ignore_sticky_posts' => 1);
				
		} elseif($ids=='' && $conditions=='like'){
			global $wpdb;	
			$time_range = 'all';
			//$show_type = $instance['show_type'];
			$order_by = 'ORDER BY like_count DESC, post_title';
			$show_excluded_posts = get_option('wti_like_post_show_on_widget');
			$excluded_post_ids = explode(',', get_option('wti_like_post_excluded_posts'));
			
			if(!$show_excluded_posts && count($excluded_post_ids) > 0) {
				$where = "AND post_id NOT IN (" . get_option('wti_like_post_excluded_posts') . ")";
			}
			else {$where = '';}
			$query = "SELECT post_id, SUM(value) AS like_count, post_title FROM `{$wpdb->prefix}wti_like_post` L, {$wpdb->prefix}posts P ";
			$query .= "WHERE L.post_id = P.ID AND post_status = 'publish' AND value > -1 $where GROUP BY post_id $order_by";
			$posts = $wpdb->get_results($query);
			//$cates_ar = $cates;
			$p_data = array();
			//print_r($posts);
			if(count($posts) > 0) {
				foreach ($posts as $post) {
					$p_data[] = $post->post_id;
				}
			}

			$args = array(
				'post_type' => $post_type,
				'posts_per_page' => $number,
				'orderby'=> 'post__in',
				'order' => 'ASC',
				'post_status' => 'publish',
				'tag' => $tags,
				'post__in' =>  $p_data,
				'ignore_sticky_posts' => 1);
		} else {
			if($conditions == 'random'){ $conditions = 'rand';}
			$args = array(
				'post_type' => $post_type,
				'posts_per_page' => $number,
				'order' => $sort_by,
				'orderby' => $conditions, /* title or modified */
				'post_status' => 'publish',
				'tag' => $tags,
				'ignore_sticky_posts' => 1);
		}
		if($featured==1 && $ids==''){
			$args += array('meta_key' => 'featured_post', 'meta_value' => 'yes');
		}
		
		if($post_type=='product'){
			if(!is_array($categories) && $categories!='') {
				$categories = explode(",",$categories);
				if(is_numeric($categories[0])){
					$args['tax_query'] = array(
						array(
							'taxonomy' => 'product_cat',
							'field'    => 'id',
							'terms'    => $categories,
							'operator' => 'IN',
						)
					);
				}else{
					$args['tax_query'] = array(
						array(
							'taxonomy' => 'product_cat',
							'field'    => 'slug',
							'terms'    => $categories,
							'operator' => 'IN',
						)
					);
				}
			}elseif(count($categories) > 0 && $categories!=''){
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'product_cat',
						'field'    => 'id',
						'terms'    => $categories,
						'operator' => 'IN',
					)
				);
			}
			
			if(!is_array($tags) && $tags!='') {
				$tags = explode(",",$tags);
				if(is_numeric($tags[0])){
					$args['tax_query'] = array(
						array(
							'taxonomy' => 'product_tag',
							'field'    => 'id',
							'terms'    => $tags,
							'operator' => 'IN',
						)
					);
				}else{
					$args['tax_query'] = array(
						array(
							'taxonomy' => 'product_tag',
							'field'    => 'slug',
							'terms'    => $tags,
							'operator' => 'IN',
						)
					);
				}
				$args['tag'] = '';
			}elseif(count($tags) > 0 && $tags!=''){
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'product_cat',
						'field'    => 'id',
						'terms'    => $tags,
						'operator' => 'IN',
					)
				);
				$args['tag'] = '';
			}
		}elseif(!is_array($categories)) {
			if(isset($categories)){
				$cats = explode(",",$categories);
				if(is_numeric($cats[0])){
					//$args += array('category__in' => $cats);
					$args['category__in'] = $cats;
				}else{			 
					$args['category_name'] = $categories;
				}
			}
		}else if(count($categories) > 0){
			$args += array('category__in' => $categories);
		}
		
		if($args['post_type']=='attachment'){
			$args['post_status']='inherit';
		}
		
		//print_r($args);
		if($paged){$args['paged'] = $paged;}
		
		$args = apply_filters('spg_query_param', $args);
		
		$query = new WP_Query($args);
		
		return $query;
	}
	
	function spg_get_tptn_pop_posts( $args = array() ) {
		if(!is_plugin_active('top-10/top-10.php')){
			return;
		}
		
		
		$args = array();
		$trending_by 		= ot_get_option('trending_by','most_viewed');
		$timerange 			= ot_get_option('time_range','week');
		$time_range 		= '';
		$trending_limmit	= '10';
		$ids = '';
		$trending_exclude 	= ot_get_option('trending_exclude');
		
		if($trending_by == 'most_viewed'){
			if($timerange=='day')
			{
				
					$args = array(
						'daily' => 1,
						'daily_range' => 1,
						'post_types' =>'post',
					);

			}elseif($timerange=='week'){

					$args = array(
						'daily' => 1,
						'daily_range' => 7,
						'post_types' =>'post',
						'limit' => $trending_limmit,
						'exclude_post_ids' => $trending_exclude,
					);
					
			}elseif($timerange=='month'){
				
					$args = array(
						'daily' => 1,
						'daily_range' => 30,
						'post_types' =>'post',
						'limit' => $trending_limmit, 
						'exclude_post_ids' => $trending_exclude,
					);
					
			}elseif($timerange=='year'){
				
					$args = array(
						'daily' => 1,
						'daily_range' => 365,
						'post_types' =>'post',
						'limit' => $trending_limmit, 
						'exclude_post_ids' => $trending_exclude,
					);
			}else{
					$args = array(
						'daily' => 0,
						'post_types' =>'post',
						'limit' => $trending_limmit,
						'exclude_post_ids' => $trending_exclude, 
					);
			}
		}
		
		global $wpdb, $tptn_settings;
		if($tptn_settings==''){ $tptn_settings = array();}	
		// Initialise some variables
		if($tptn_settings)
		$fields = '';
		$where = '';
		$join = '';
		$groupby = '';
		$orderby = '';
		$limits = '';
	
		$defaults = array(
			'daily' => true,
			'strict_limit' => true,
			'posts_only' => false,
		);
	
		// Merge the $defaults array with the $tptn_settings array
		$defaults = array_merge( $defaults, $tptn_settings );
	
		// Parse incomming $args into an array and merge it with $defaults
		$args = wp_parse_args( $args, $defaults );
		if ( $args['daily'] ) {
			$table_name = $wpdb->base_prefix . 'top_ten_daily';
		} else {
			$table_name = $wpdb->base_prefix . 'top_ten';
		}
		
	
		$limit = $args['limit'];
	
		// If post_types is empty or contains a query string then use parse_str else consider it comma-separated.
		if ( ! empty( $args['post_types'] ) && false === strpos( $args['post_types'], '=' ) ) {
			$post_types = explode( ',', $args['post_types'] );
		} else {
			parse_str( $args['post_types'], $post_types );	// Save post types in $post_types variable
		}
	
		// If post_types is empty or if we want all the post types
		if ( empty( $post_types ) || 'all' === $args['post_types'] ) {
			$post_types = get_post_types( array(
				'public'	=> true,
			) );
		}
	
		$blog_id = get_current_blog_id();
	
		$current_time = current_time( 'timestamp', 0 );
		$from_date = $current_time - ( $args['daily_range'] * DAY_IN_SECONDS );
		$from_date = gmdate( 'Y-m-d' , $from_date );
	
		/**
		 *
		 * We're going to create a mySQL query that is fully extendable which would look something like this:
		 * "SELECT $fields FROM $wpdb->posts $join WHERE 1=1 $where $groupby $orderby $limits"
		 */
	
		// Fields to return
		$fields[] = 'ID';
		$fields[] = 'postnumber';
		$fields[] = ( $args['daily'] ) ? 'SUM(cntaccess) as sumCount' : 'cntaccess as sumCount';
	
		$fields = implode( ', ', $fields );
	
		// Create the JOIN clause
		$join = " INNER JOIN {$wpdb->posts} ON postnumber=ID ";
	
		// Create the base WHERE clause
		$where .= $wpdb->prepare( ' AND blog_id = %d ', $blog_id );				// Posts need to be from the current blog only
		$where .= " AND $wpdb->posts.post_status = 'publish' ";					// Only show published posts
	
		if ( $args['daily'] ) {
			$where .= $wpdb->prepare( " AND dp_date >= '%s' ", $from_date );	// Only fetch posts that are tracked after this date
		}
		
		// Convert exclude post IDs string to array so it can be filtered
		$exclude_post_ids = explode( ',', $args['exclude_post_ids'] );
	
		/**
		 * Filter exclude post IDs array.
		 *
		 * @param array   $exclude_post_ids  Array of post IDs.
		 */
		$exclude_post_ids = apply_filters( 'tptn_exclude_post_ids', $exclude_post_ids );
	
		// Convert it back to string
		$exclude_post_ids = implode( ',', array_filter( $exclude_post_ids ) );
	
		if ( '' != $exclude_post_ids ) {
			$where .= " AND $wpdb->posts.ID NOT IN ({$exclude_post_ids}) ";
		}
		$where .= " AND $wpdb->posts.post_type IN ('" . join( "', '", $post_types ) . "') ";	// Array of post types
	
		// How old should the posts be?
		if ( $args['how_old'] ) {
			$where .= $wpdb->prepare( " AND $wpdb->posts.post_date > '%s' ", gmdate( 'Y-m-d H:m:s', $current_time - ( $args['how_old'] * DAY_IN_SECONDS ) ) );
		}
	
		// Create the base GROUP BY clause
		if ( $args['daily'] ) {
			$groupby = ' postnumber ';
		}
	
		// Create the base ORDER BY clause
		$orderby = ' sumCount DESC ';
	
		// Create the base LIMITS clause
		$limits .= $limit ? $wpdb->prepare( ' LIMIT %d ', $limit ): '';	
	
		if ( ! empty( $groupby ) ) {
			$groupby = " GROUP BY {$groupby} ";
		}
		if ( ! empty( $orderby ) ) {
			$orderby = " ORDER BY {$orderby} ";
		}
	
		$sql = "SELECT DISTINCT $fields FROM {$table_name} $join WHERE 1=1 $where $groupby $orderby $limits";
		$results = $wpdb->get_results( $sql );
		$ids = array();
		foreach ( $results as $result ) {
			$ids[] = $result->ID;
		}
		return $ids;
	}
	
	function spg_thumbnail($size='medium', $post_id = -1){
		$spg_options = get_option('spg_options_group');
		if($size=='thumbnail'){
			$size = @$spg_options['spg_thumb_small']?$spg_options['spg_thumb_small']:$size;
		}elseif($size=='medium'){
			$size = @$spg_options['spg_thumb_medium']?$spg_options['spg_thumb_medium']:$size;
		}elseif($size=='large'){
			$size = @$spg_options['spg_thumb_large']?$spg_options['spg_thumb_large']:$size;
		}
		
		if($post_id == -1){ //if there is no ID
			$post_id = get_the_ID();
		}

		//get attachment id
		if(get_post_type($post_id)=='attachment'){
			$attachment_id = $post_id;
		}else{
			$attachment_id = get_post_thumbnail_id($post_id);
		}
		
		//return
		if(function_exists('wp_get_attachment_image_srcset')){
						
			$html = '<img src="'.wp_get_attachment_image_url( $attachment_id, $size ).'" ';
     		$html .= wp_get_attachment_image_srcset( $attachment_id, $size )?'srcset="'.wp_get_attachment_image_srcset( $attachment_id, $size ).'" ':'';
     		$html .= (wp_get_attachment_image_sizes( $attachment_id, $size ) && wp_get_attachment_image_srcset( $attachment_id, $size ))?'sizes="'.wp_get_attachment_image_sizes( $attachment_id, $size ).'" ':'';
			$html .= 'alt="'.esc_attr(get_the_title($attachment_id)).'"/>';
						
			return $html;
			
		} else {
			return wp_get_attachment_image($attachment_id, $size);
		}
	}
	
	function spg_get_category($show_class,$echo=true)
	{	$output = '';
		if($show_class == '1'){
			$show_class = 'btn btn-sm btn-black';
		}else{
			$show_class = 'no-class';
		}
		$categories = get_the_category();
			if ( ! empty( $categories ) ) {
				foreach( $categories as $category ) {
					$cat_name 	= $category->name;
					$cat_url 	= get_category_link( $category->term_id );
				}
				$output .= '<a class="'.esc_attr($show_class).' cat-label-attr-'.esc_attr($category->term_id).'" href="' . esc_url($cat_url) . '" title="' .esc_html__('View all posts in ',"spg") . esc_attr($cat_name) . '">' . esc_html( $cat_name ) . '</a>';
				if($echo)
					echo wp_kses($output,array('a'=>array('href'=>array(),'title' => array(), 'class' => array())));
				else
					return $output;
			}
	}
	
	function spg_sc_item_meta($author_link=1) {
		if(get_post_type()=='product' && class_exists(WC_Product)){
			$product = new WC_Product( get_the_ID() );
			if ( $price_html = $product->get_price_html() ) : ?>
                <div class="item-meta"><?php echo $price_html; ?></div>
            <?php endif;
		}else{
		?>
		<div class="item-meta">
		    <span class="item-time"><?php the_time( get_option( 'date_format' ) ); ?></span>
		    <span class="item-dot">.</span>
		    <span class="item-author">
            <?php if(!$author_link){
				echo esc_html__('by ',"spg").get_the_author();
			}else{ ?>
            	<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" class="author-url"><?php echo esc_html__('by ',"spg").get_the_author(); ?></a>
            <?php } ?>
            </span>
		</div>
		<?php }
	}
	
}

$cactusSmartPostGrid = new cactusSmartPostGrid();