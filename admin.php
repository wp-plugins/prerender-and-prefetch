<?php
/**
 * Function to create an options page
 */
add_action( 'admin_menu', 'prerenderap_page' );

function prerenderap_page() {
	$prerenderap_admin_hook = add_options_page( 'Prerender and Prefetch Settings', 'Prerender and Prefetch', 'manage_options', 'prerenderap', 'prerenderap_options_page' );
	
	// add CSS styles specific to our options page on our options page only
	add_action( "admin_head-{$prerenderap_admin_hook}", 'prerenderap_admin_style' );	
}

/**
 * Function to add CSS styles on our Options page
 */
/* TODO */
function prerenderap_admin_style() {
?>
	<style type="text/css">
.prerenderapmiddle{
width:49%;
margin:0;
padding:0;
float:left;
}

.prerenderapmiddleright{
width:35%;
margin-right:5%;
padding:0;
float:right;
}
	</style>
<?php
}

/**
 * Function to draw the options page
 */
function prerenderap_options_page() {
?>
	<div class="wrap">
		<?php screen_icon( 'plugins' ); ?>
		<h2>Prerender and Prefetch Setting</h2>
		
		<form action="options.php" method="post">
			<?php settings_fields( 'prerenderap_options' ); ?>
			<?php do_settings_sections( 'prerenderap' ); ?>
			<br />
			<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Save' ); ?>" />
		</form>
	</div>
<?php	
}

/**
 * WordPress Settings API to save plugin's data
 */
add_action( 'admin_init', 'prerenderap_init' );

function prerenderap_init() {
	register_setting(
		'prerenderap_options',	// same to the settings_field
		'prerenderap',		// options name
		'prerenderap_validate'	// validation callback
	);
	add_settings_section(
		'default',			// settings section (a setting page must have a default section, since we created a new settings page, we need to create a default section too)
		'Main settings',		// section title
		'prerenderap_section_text',		// text for the section
		'prerenderap'		// specify the output of this section on the options page, same as in do_settings_section
	);
	add_settings_field('prerenderap_load', 'Server load to stop','prerenderap_load','prerenderap','default');
	add_settings_field('prerenderap_choose', 'Choose method','prerenderap_choose','prerenderap','default');

	add_settings_field('prerenderap_blog', // field ID
		'Be in blog page',	// Field title
		'prerenderap_ask',	// display callback
		'prerenderap',		// which settings page?
		'default','blog'			// which settings section?
	);
	add_settings_field('prerenderap_blog_refer', 'Refer to...','prerenderap_refer','prerenderap','default','blog');

	add_settings_field('prerenderap_single', 'Be in single pages','prerenderap_ask','prerenderap','default','single');
	add_settings_field('prerenderap_single_refer', 'Refer to...','prerenderap_refer','prerenderap','default','single');

	add_settings_field('prerenderap_frontpage', 'Be in front page','prerenderap_ask','prerenderap','default','frontpage');
	add_settings_field('prerenderap_frontpage_refer', 'Refer to...','prerenderap_refer','prerenderap','default','frontpage');

	add_settings_field('prerenderap_archive', 'Be in archive page','prerenderap_ask','prerenderap','default','archive');
	add_settings_field('prerenderap_archive_refer', 'Refer to...','prerenderap_refer','prerenderap','default','archive');

}

function prerenderap_section_text() {
?>
<div class="prerenderapmiddle">¿What is Prerender and Prefetch? Nice question. It's a new-navigators technique (ok i'm a liar, Mozilla do it from 2003!) that <strong>loads in background the next page you believe the visitor is going to visit</strong>.<br/><br/>
So... What's the improvement? <strong>That page will load pretty fast!</strong> amazingly fast! BOOM!*<br/>In compatible navigators<br/><br/>
And... What are the inconveniences? In every page you have it activated, a visitor with a navigator that supports Prerender or Prefetch (Chrome and Firefox, basically), will load ¡two pages at a time!, it means, ¡double load for the server! (Because of that <strong>you have below a server's load limit setting</strong>) and need for more resources like bandwidth (just be sure you have a powerfull server)<br/><br/>
Mmm... How i know what page is going to enter the visitor? Well, you just have to <strong>statistically figure it out</strong> and configure below. 86.74% chances the default config is ok for a blog kind WordPress. And remember, <strong>you have just a chance</strong>; Per page only one another page will be loaded in background. So, you can set below what page is going to do the Prerender or Prefetch.<br/>
</div>

<div class="prerenderapmiddleright">
<h2>Basics</h2>
<ul><li>Please, <strong>configure "Server load to stop"</strong>, 4.00 is a nice value for a standar not-too-big and not-too-loaded multiprocessor server. If i were you, i will set it at<br/> ( [# of CPUs of the system]-([# of CPUs of the system]*0.25) )<br/>You can know more about this value here <a href="http://en.wikipedia.org/wiki/Load_(computing)">here</a></li>
<li><strong>Prerender</strong> is the Chrome's method, it loads and render all the page. <strong>Prefetch</strong> is the Firefox and maybe-future-standar of HTML5, it just loads the HTML.</li>
</ul>
</div>
<?php
}

/**
 * Functions to show forms.
 */
/* Form limiting server load */
function prerenderap_load() {
	$options = get_option( 'prerenderap' );
	$load = sys_getloadavg();
	echo "<input id=\"prerenderap_load\" type=\"text\" name=\"prerenderap[load]\" value='{$options['load']}' size='3'> Current load ".$load[0];

}

/* Form choosing between prerender, prefetch or both */
function prerenderap_choose() {
	$options = get_option( 'prerenderap' );
	echo "<select id='prerenderap_choose' name='prerenderap[choose]'>";
	if ($options['choose'] == 0){ $selected="selected"; } else { $selected=""; }
	echo "<option value=\"0\" ".$selected.">Prerender & Prefetch</option>";
	if ($options['choose'] == 1){ $selected="selected"; } else { $selected=""; }
	echo "<option value=\"1\" ".$selected.">Just Prefetch (Just loads HTML, saves server's load and bandwidth)</option>";
	if ($options['choose'] == 2){ $selected="selected"; } else { $selected=""; }
	echo "<option value=\"2\" ".$selected.">Just Prerender (¿Why are you gonna do that?)</option>";
	echo "</select>";
        echo "<hr>";
}

/* Part of the form (number of posts in homepage) */
function prerenderap_postn($type) {
	$options = get_option( 'prerenderap' );
	$ppp = get_option('posts_per_page');
	$out="";
	$out.= "<select id='prerenderap_".$type."_postn' name='prerenderap[".$type."_postn]'>";
    	$out.= "<option value=\"-1\">-None-</option>";
        for ($i=1; $i<=$ppp; $i++){
          if ($options[$type.'_postn']==($i-1)){
	    $selected="selected";
	  } else {
	    $selected="";
          }
	  $out.= "<option value=\"".($i-1)."\" ".$selected.">".$i."</option>";
	}
	$out.= "</select>";
	return $out;
}


/* Part of the form  (Check if a input is checked or not) */
function prerenderap_check_checked($type,$who){
	$options = get_option( 'prerenderap' );
        $option=$type."_refer";
	if ($options[$option]==$who){
		return "checked";
	} else {
		return false;
	}
}

/* Part of the form  (A select of categories and tags) */
function prerenderap_taxonomy_select($type,$taxonomy){
	$options = get_option( 'prerenderap' );
	echo "<input id=\"prerenderap_".$type."_refer\" type=\"radio\" name=\"prerenderap[".	$type."_refer]\" value='$taxonomy' ".prerenderap_check_checked($type,$taxonomy).">$taxonomy <br/>";
	$args = array(
		'show_option_all'    => '',
		'show_option_none'   => '',
		'orderby'            => 'ID', 
		'order'              => 'ASC',
		'show_count'         => 0,
		'hide_empty'         => 1, 
		'child_of'           => 0,
		'exclude'            => '',
		'echo'               => 1,
		'selected'           => $options[$type.'_refer_taxonomy_'.$taxonomy],
		'hierarchical'       => 0, 
		'name'               => 'prerenderap['.$type.'_refer_taxonomy_'.	$taxonomy.']',
		'id'                 => '',
		'class'              => 'postform',
		'depth'              => 0,
		'tab_index'          => 0,
		'taxonomy'           => $taxonomy,
		'hide_if_empty'      => false
	);
	
	wp_dropdown_categories( $args );
	
	echo "<br/>";
}

/* For the current $type, check if you want to enable it or not */
function prerenderap_ask($type) {
	$options = get_option( 'prerenderap' );
	if ($options[$type]){
	  $checked="checked";
	}
	echo "<input id=\"prerenderap_".$type."\" type=\"checkbox\" name=\"prerenderap[".$type."]\" value='1' $checked>";
}

/* For the current $type, show options to set what is going to be the pre-loaded page */
function prerenderap_refer($type) {
	$options = get_option( 'prerenderap' );

	if ($type=="blog" || $type=="archive" || $type=="search" || $type=="archive" || $type=="tag"){
	/*if (!is_singular($type)){ *//*TODO this doesn't works ¿anything clever? */
		echo "<input id=\"prerenderap_".$type."_refer\" type=\"radio\" name=\"prerenderap[".$type."_refer]\" value='postn' ".prerenderap_check_checked($type,"postn").">post number... ".prerenderap_postn($type)." <br/>";
	}
	
	echo "<input id=\"prerenderap_".$type."_refer\" type=\"radio\" name=\"prerenderap[".$type."_refer]\" value='next' ".prerenderap_check_checked($type,"next").">next post <br/>";

	echo "<input id=\"prerenderap_".$type."_refer\" type=\"radio\" name=\"prerenderap[".$type."_refer]\" value='next_pagination' ".prerenderap_check_checked($type,"next_pagination").">next page in pagination <br/>";

	if ($type!="blog"){
		echo "<input id=\"prerenderap_".$type."_refer\" type=\"radio\" name=\"prerenderap[".$type."_refer]\" value='front' ".prerenderap_check_checked($type,"front").">front page <br/>";
	}

	if ($type!="home"){
		echo "<input id=\"prerenderap_".$type."_refer\" type=\"radio\" name=\"prerenderap[".$type."_refer]\" value='home' ".prerenderap_check_checked($type,"home").">home page <br/>";
	}

	$taxonomy="category";
	prerenderap_taxonomy_select($type,$taxonomy);
	$taxonomy="post_tag";
	prerenderap_taxonomy_select($type,$taxonomy);

	/* TODO : Get from a menu */ // echo "<input id=\"prerenderap_".$type."_refer\" type=\"radio\" name=\"prerenderap[".$type."_refer]\" value='menu' ".prerenderap_check_checked($type,"menu").">menu element <br/>";

	echo "<input id=\"prerenderap_".$type."_refer\" type=\"radio\" name=\"prerenderap[".$type."_refer]\" value='defined' ".prerenderap_check_checked($type,"defined").">defined url <input id=\"prerenderap_".$type."_refer\" type=\"text\" name=\"prerenderap[".$type."_refer_defined]\" value='{$options[$type.'_refer_defined']}' ".prerenderap_check_checked($type,"defined").">";

}

/* Mmmm TODO (Yep, this is embarrassing) */
function prerenderap_validate( $input ) {
	/*if ( !is_numeric($input['blog']) ) {
		add_settings_error(
			'prerenderap_blog',				// title (?)
			'prerenderap_blog_url_error',			// error ID (?)
			'Does not looks like a bool',	// error message
			'error'						// message type
		);
	}
	if ( !is_numeric($input['blog_postn']) ) {
		add_settings_error(
			'prerenderap_blog_postn',				// title (?)
			'prerenderap_blog_postn_url_error',			// error ID (?)
			'Does not looks like a right number of posts',	// error message
			'error'						// message type
		);
	}*/
	return $input;	
}
