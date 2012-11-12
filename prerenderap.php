<?php
/*
Plugin Name: Prerender and Prefetch
Donate link: http://frantorres.es/prerender-and-prefetch-wp-plugin/
Plugin URI: http://frantorres.es/prerender-and-prefetch-wp-plugin/
Author: FranTorres
Author URI: http://frantorres.es/
Description: Puts Prerender and Prefetch tag in the page. Allowing compatible navigators to do a pre-load of the page you figure the visitor is going to go.
Requires at least: 3.1
Tested up to: 3.4.2
Version: 0.93
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: prerender, prefetch, preload, speed, load, page

@TODO: Internacionalitation.
@TODO: Improve admin panel order with nice and cool things like jQuery's hide and show elements.
@TODO: Let define a "per-element" url in the editor panel.
@TODO: Do this with more elements, and specially post_types.
@TODO: Security checks.
@TODO: Validate settings form.
*/


/* Initial config */
function prerenderap_install() {
        //$options = get_option('prerenderap'); /* testing purpose */
	//print_r ($options);

	$initconfig=array ( 'load' => '4.00' , 'choose' => '0' , 'blog' => '1' , 'blog_refer' => 'postn' , 'blog_postn' => '0' , 'blog_refer_taxonomy_category' => '1' , 'blog_refer_taxonomy_post_tag' => '4' , 'blog_refer_defined' => '', 'single' => 1 , 'single_refer' => 'home' , 'single_refer_taxonomy_category' => 1 , 'single_refer_taxonomy_post_tag' => 4 , 'single_refer_defined' => '', 'frontpage' => 1 , 'frontpage_refer' => 'next_pagination' , 'frontpage_refer_taxonomy_category' => 1 , 'frontpage_refer_taxonomy_post_tag' => 4 , 'frontpage_refer_defined' => '', 'archive' => 1 , 'archive_refer' => 'postn' , 'archive_postn' => 0 , 'archive_refer_taxonomy_category' => 1 , 'archive_refer_taxonomy_post_tag' => 4 , 'archive_refer_defined' => ''); 
	add_option( 'prerenderap', $initconfig, 'yes' );
}
register_activation_hook(__FILE__,'prerenderap_install'); 

// If we are at WordPress Admin side, load the file for option page
if ( is_admin() )
	require plugin_dir_path( __FILE__ ).'admin.php';

add_action( 'wp_head', 'prerenderap' );

/* We have all this choices of links for each post/page type, we just look what were the user settings and we obey */
function prerenderap_posttype_choices($type){
	global $wp_query;
        $options = get_option('prerenderap');	

	switch ($options[$type."_refer"]){
		/* Post no */
		case 'postn':
			if (is_numeric($options[$type."_postn"]) && $options[$type."_postn"]!="-1"){
				$offset=$options[$type."_postn"];
				if (get_query_var( 'paged' )>1){
					$offset+=(get_query_var( 'paged' )-1)*get_option('posts_per_page');
				}
				/* Retrieve the post. TODO: We don't need while loop isn't? */
				$the_query = new WP_Query( array_merge($wp_query->query, array('posts_per_page' => 1, 'offset' => $offset ) ) );
				if ( $the_query->have_posts() ) : $the_query->the_post();
					$url=get_permalink();	
				 endif;	
			}
		break;
		/* Portada */
		case 'front':
		    $url=site_url('/');
		break;

		/* Home */
		case 'home':
		    $url=home_url('/');
		break;

		/* Next element */
		case 'next':
		    $url=get_permalink(get_adjacent_post(false,'',true));
		break;

		/* Next in pagination */
		case 'next_pagination':
		    $npl=explode('"',get_next_posts_link()); 
		    $url=$npl[1];
		break;

		/* Category */
		case 'category':
		    $url=get_category_link($options[$type."_refer_taxonomy_category"]);
		break;

		/* post_tag */
		case 'post_tag':
		    $url=get_tag_link($options[$type."_refer_taxonomy_post_tag"]);
		break;

		/* Menu TODO*/
		case 'menu':

		break;

		/* Defined */
		case 'defined':
			$url=$options[$type."_refer_defined"];
		break;
	}
	return $url;
}

/* Generate metatag in this page, if it has to be here */
function prerenderap()
{
   global  $post;
   $options = get_option('prerenderap');	
   $loadmax = $options['load'];
   $loadnow = sys_getloadavg();

   $posttype = get_post_type($post);

   //echo "\n\n<!-- Prerender LOAD ".$loadnow[0]." -->"; /* testing purpose */

   if($loadmax >= $loadnow[0]){
	$url="";

	/* Options: Be in single page */
	if (is_single() && $options['single']){
                $type='single';
 		$url=prerenderap_posttype_choices($type);
	}

	/* Options: Be in archive page */
	if (is_archive() && $options['archive']){
                $type='archive';
 		$url=prerenderap_posttype_choices($type);
	}

	/* Options: Be in front page */
	if (is_front_page() && $options['frontpage']){
                $type='frontpage';
 		$url=prerenderap_posttype_choices($type);
	}

	/* Options: Be in blog page */
	if (is_home() && $options['blog']){
        	$type='blog';
 		$url=prerenderap_posttype_choices($type);
	}


	/* Finish him! */
 	if (!empty($url)){

		if ($options['choose'] == 0){
			$methodchoosen="prerender prefetch";
		} else if ($options['choose'] == 1){
			$methodchoosen="prefetch";
		} else if ($options['choose'] == 2){
			$methodchoosen="prerender";
		}

        	echo "<!-- Prerender and Prefetch --><link href=\"".$url."\" rel=\"".$methodchoosen."\" />\n\n";
	}


   } else { // If server's load is higer than max configured load...
	echo "<!-- Prerender didn't fire because of high server load -->";
   }

}
