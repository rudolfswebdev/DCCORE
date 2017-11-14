<?php
/*
 * add option page
 */
add_action('admin_menu', 'spg_plugin_settings');
function spg_plugin_settings(){
    add_menu_page('Post Grid', 'Post Grid', 'administrator', 'spg_settings', 'spg_display_settings', 'dashicons-schedule');
}
function register_spg_setting() {
	register_setting( 'spg_options_group', 'spg_options_group', 'spg_options_validate' );
	//grid settings
	add_settings_section('spg_settings_grid','','spg_settings_grid_html','spg_settings');
	add_settings_field('spg_thumb_size_small',esc_html__('Thumbnail size for small positions','urbannews'),'spg_thumb_size_small_html','spg_settings','spg_settings_grid');
	add_settings_field('spg_thumb_size_medium',esc_html__('Thumbnail size for medium positions','urbannews'),'spg_thumb_size_medium_html','spg_settings','spg_settings_grid');
	add_settings_field('spg_thumb_size_large',esc_html__('Thumbnail size for large positions','urbannews'),'spg_thumb_size_large_html','spg_settings','spg_settings_grid');
	add_settings_field('spg_hide_category',esc_html__('Force hide Category label in Thumbnail','urbannews'),'spg_hide_category_html','spg_settings','spg_settings_grid');
	add_settings_field('spg_hide_icon',esc_html__('Force hide Post Format Icon','urbannews'),'spg_hide_icon_html','spg_settings','spg_settings_grid');
	add_settings_field('spg_hide_meta',esc_html__('Force hide Post Meta (author, date)','urbannews'),'spg_hide_meta_html','spg_settings','spg_settings_grid');
	add_settings_field('spg_hide_readmore',esc_html__('Force hide Read more Links','urbannews'),'spg_hide_readmore_html','spg_settings','spg_settings_grid');
	add_settings_field('spg_excerpt',esc_html__('Custom Excerpt Length','urbannews'),'spg_excerpt_html','spg_settings','spg_settings_grid');

	//other settings
	add_settings_section('spg_settings_other','','spg_settings_other_html','spg_settings');
	add_settings_field('spg_fontawesome',esc_html__('Turn off Font Awesome','urbannews'),'spg_fontawesome_html','spg_settings','spg_settings_other');
	add_settings_field('spg_bootstrap',esc_html__('Turn off Bootstrap','urbannews'),'spg_bootstrap_html','spg_settings','spg_settings_other');
} 
add_action( 'admin_init', 'register_spg_setting' );

function spg_admin_style(){
	wp_enqueue_style('font-awesome', SPG_PATH.'font-awesome/css/font-awesome.min.css');
}
add_action('admin_enqueue_scripts', 'spg_admin_style');
/*
 * render option page
 */
function spg_display_settings(){
$spg_options = get_option('spg_options_group');
$spg_heading_title_font = isset($spg_options['spg_heading_title_font'])?$spg_options['spg_heading_title_font']:'';
$spg_heading_subtitle_font = isset($spg_options['spg_heading_subtitle_font'])?$spg_options['spg_heading_subtitle_font']:'';
$spg_post_title_font = isset($spg_options['spg_post_title_font'])?$spg_options['spg_post_title_font']:'';
$spg_post_excerpt_font = isset($spg_options['spg_post_excerpt_font'])?$spg_options['spg_post_excerpt_font']:'';
?>
</pre>

<div class="wrap">
  <div class="hhtv-setting-page">
    <h1 class="hhtv-head"><i class="fa fa-th"></i> Smart Post Grid Settings</h1>
    <div class="hhtv-setting-content">
    <?php if(isset($_GET['settings-updated'])&&$_GET['settings-updated']==true) {?>
    	<div class="form-group">
            <div class="form-label"></div>
            <div class="form-control">
            	<i class="fa fa-check"></i> Settings were saved.
            </div>
         </div>
    <?php } ?>
    <form action="options.php" method="post" name="options" id="hhtv-form" class="tmr-data">
    	<?php settings_errors('med-settings-errors'); ?>
        <?php
			settings_fields('spg_options_group');
			do_settings_sections('spg_settings');
		?>
      	<div class="form-group">
            <div class="form-label"></div>
            <div class="form-control">
            	<button type="submit" title="Update Default Setting" name="submit" class="button"><i class="fa fa-check"></i> Update</button>
            </div>
      	</div>
    </form>
    </div>
  </div>
</div>
<pre>
<?php
}
//header for setting section
function spg_settings_grid_html(){ ?>
	<h2 class="option-group"><i class="fa fa-th-large"></i> Grid settings</h2>
<?php 
}

//header for setting section
function spg_settings_other_html(){ ?>
	<h2 class="option-group"><i class="fa fa-plus-circle"></i> Other settings</h2>
<?php 
}


//render size options
function spg_thumb_size_small_html(){
	$spg_options = get_option('spg_options_group');
	$spg_thumb_small = isset($spg_options['spg_thumb_small'])?$spg_options['spg_thumb_small']:'thumbnail';
?>
    <select name="spg_options_group[spg_thumb_small]">
    <?php 
    $sizes = spg_list_thumbnail_sizes();
    foreach( $sizes as $size => $atts ): ?>
        <option value="<?php echo $size ?>" <?php echo $spg_thumb_small==$size?'selected':'' ?> ><?php echo $size . ' ' . implode( 'x', $atts ) ?></option>
    <?php endforeach; ?>
    </select>
<?php
}
function spg_thumb_size_medium_html(){
	$spg_options = get_option('spg_options_group');
	$spg_thumb_medium = isset($spg_options['spg_thumb_medium'])?$spg_options['spg_thumb_medium']:'medium';
?>
    <select name="spg_options_group[spg_thumb_medium]">
    <?php 
    $sizes = spg_list_thumbnail_sizes();
    foreach( $sizes as $size => $atts ): ?>
        <option value="<?php echo $size ?>" <?php echo $spg_thumb_medium==$size?'selected':'' ?> ><?php echo $size . ' ' . implode( 'x', $atts ) ?></option>
    <?php endforeach; ?>
    </select>
<?php
}
function spg_thumb_size_large_html(){
	$spg_options = get_option('spg_options_group');
	$spg_thumb_large = isset($spg_options['spg_thumb_large'])?$spg_options['spg_thumb_large']:'large';
?>
    <select name="spg_options_group[spg_thumb_large]">
    <?php 
    $sizes = spg_list_thumbnail_sizes();
    foreach( $sizes as $size => $atts ): ?>
        <option value="<?php echo $size ?>" <?php echo $spg_thumb_large==$size?'selected':'' ?> ><?php echo $size . ' ' . implode( 'x', $atts ) ?></option>
    <?php endforeach; ?>
    </select>
<?php
}

//render options fields
function spg_hide_category_html(){
	$spg_options = get_option('spg_options_group');
	$spg_hide_category = isset($spg_options['spg_hide_category'])?$spg_options['spg_hide_category']:0;
	$array = array(
		array(
			'name'=>'spg_options_group[spg_hide_category]',
			'value' => 0,
			'label' => 'Show',
			'icon' => 'fa fa-eye fa-2x',
		),
		array(
			'name'=>'spg_options_group[spg_hide_category]',
			'value' => '1',
			'label' => 'Hide',
			'icon' => 'fa fa-eye-slash fa-2x',
		)
	);
	spg_image_radio($spg_hide_category,$array);?>
    <span> Choose Hide will overwrite all shortcodes setting</span>
    <?php
}
function spg_hide_icon_html(){
	$spg_options = get_option('spg_options_group');
	$spg_hide_icon = isset($spg_options['spg_hide_icon'])?$spg_options['spg_hide_icon']:0;
	$array = array(
		array(
			'name'=>'spg_options_group[spg_hide_icon]',
			'value' => 0,
			'label' => 'Show',
			'icon' => 'fa fa-eye fa-2x',
		),
		array(
			'name'=>'spg_options_group[spg_hide_icon]',
			'value' => '1',
			'label' => 'Hide',
			'icon' => 'fa fa-eye-slash fa-2x',
		)
	);
	spg_image_radio($spg_hide_icon,$array);?>
    <span> Choose to Hide all Post Format Icons</span>
    <?php
}
function spg_hide_meta_html(){
	$spg_options = get_option('spg_options_group');
	$spg_hide_meta = isset($spg_options['spg_hide_meta'])?$spg_options['spg_hide_meta']:0;
	$array = array(
		array(
			'name'=>'spg_options_group[spg_hide_meta]',
			'value' => 0,
			'label' => 'Show',
			'icon' => 'fa fa-eye fa-2x',
		),
		array(
			'name'=>'spg_options_group[spg_hide_meta]',
			'value' => '1',
			'label' => 'Hide',
			'icon' => 'fa fa-eye-slash fa-2x',
		)
	);
	spg_image_radio($spg_hide_meta,$array);?>
    <span> Choose Hide will overwrite all shortcodes setting</span>
    <?php
}

function spg_hide_readmore_html(){
	$spg_options = get_option('spg_options_group');
	$spg_hide_readmore = isset($spg_options['spg_hide_readmore'])?$spg_options['spg_hide_readmore']:0;
	$array = array(
		array(
			'name'=>'spg_options_group[spg_hide_readmore]',
			'value' => 0,
			'label' => 'Show',
			'icon' => 'fa fa-eye fa-2x',
		),
		array(
			'name'=>'spg_options_group[spg_hide_readmore]',
			'value' => '1',
			'label' => 'Hide',
			'icon' => 'fa fa-eye-slash fa-2x',
		)
	);
	spg_image_radio($spg_hide_readmore,$array);?>
    <span> Choose to hide all Readmore links</span>
    <?php
}
function spg_excerpt_html(){
	$spg_options = get_option('spg_options_group');
	$spg_excerpt = isset($spg_options['spg_excerpt'])?$spg_options['spg_excerpt']:18; ?>
    <input type="number" name="spg_options_group[spg_excerpt]" title="Custom Excerpt Length" placeholder="" value="<?php echo $spg_excerpt ?>" />
    <span>Number of words (Ex: 18) </span>
<?php
}


function spg_fontawesome_html(){
	$spg_options = get_option('spg_options_group');
	$spg_fontawesome = isset($spg_options['spg_fontawesome'])?$spg_options['spg_fontawesome']:'0';
	?>
    <div class="spg_fontawesome_checkbox">
    <input type="checkbox" <?php echo $spg_fontawesome?'checked':'' ?> name="spg_options_group[spg_fontawesome]" value="1" /><span> Turn off loading plugin's Font Awesome. Check if your theme has already loaded this library</span>
    </div>
<?php
}

function spg_bootstrap_html(){
	$spg_options = get_option('spg_options_group');
	$spg_bootstrap = isset($spg_options['spg_bootstrap'])?$spg_options['spg_bootstrap']:'0';
	?>
    <div class="spg_bootstrap_checkbox">
    <input type="checkbox" <?php echo $spg_bootstrap?'checked':'' ?> name="spg_options_group[spg_bootstrap]" value="1" /><span> Turn off loading plugin's Bootstrap Core CSS. Check if your theme has already loaded Bootstrap library</span>
    </div>
<?php
}


//validate
function spg_options_validate( $input ) {
    return $input;  
}

/*
 * build radio image select
 */
function spg_image_radio($option,$array){
?>
<span class="image-select">
	<?php foreach($array as $item){ ?>
    <input type="radio" name="<?php echo $item['name'] ?>" id="<?php echo $item['name'] ?>-<?php echo $item['value'] ?>" value="<?php echo $item['value'] ?>" <?php echo ($option==$item['value'])?'checked':'' ?> />
    <label for="<?php echo $item['name'] ?>-<?php echo $item['value'] ?>" class="<?php echo ($option==$item['value'])?'selected':'' ?>" ><i class="<?php echo $item['icon'] ?> icon-large"></i><br>
    <?php echo $item['label'] ?></label>
    <?php } ?>
</span>
<?php
}
/*
 * enqueue admin scripts
 */
function spg_admin_scripts() {
    wp_enqueue_script('jquery');
	//wp_enqueue_script('jscolor', SPG_PATH.'js/jscolor/jscolor.js', array('jquery'));
	wp_enqueue_script('spg_admin', plugins_url( 'admin.js', __FILE__ ), array('jquery'));
	wp_enqueue_style('spg_admin', plugins_url( 'admin.css', __FILE__ ));
	wp_enqueue_style('font-awesome', SPG_PATH.'font-awesome/css/font-awesome.min.css');
}
add_action( 'admin_enqueue_scripts', 'spg_admin_scripts' );
/*
 * get list image sizes
 */
function spg_list_thumbnail_sizes(){
	global $_wp_additional_image_sizes;
	$sizes = array();
	foreach( get_intermediate_image_sizes() as $s ){
		$sizes[ $s ] = array( 0, 0 );
		if( in_array( $s, array( 'thumbnail', 'medium', 'large' ) ) ){
			$sizes[ $s ][0] = get_option( $s . '_size_w' );
			$sizes[ $s ][1] = get_option( $s . '_size_h' );
		}else{
			if( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $s ] ) )
			$sizes[ $s ] = array( $_wp_additional_image_sizes[ $s ]['width'], $_wp_additional_image_sizes[ $s ]['height'], );
		}
	}
	return $sizes;
}