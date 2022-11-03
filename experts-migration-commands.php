<?php
function ubc_experts_insert_new_terms ( $terms, $post_id, $taxonomy ) {
	if ( empty( $terms ) ) {
		return;
	}

	$wp_terms = array();
	foreach($terms as $term) {

			$wp_term = get_term_by('slug', $term->slug, $taxonomy );
			if ( !is_wp_error( $wp_term ) || !$wp_term == '' ) {
				$wp_terms[] = intval( $wp_term->term_id );
			}	
		
	}


	$wp_terms = wp_set_object_terms( $post_id, $wp_terms, $taxonomy, true );

}
function ubc_experts_process_terms($terms){
	if ( ! empty( $terms ) ) {
		$processed_terms = array();
		foreach( $terms as $term ) {
			$term_link = get_term_link( $term );
			$processed_terms[] = "<a href='$term_link'>$term->name</a>";
		}
		return implode(', ', $processed_terms);
	}
	
}
function ubc_experts_process_array_keys($array, $keys, $string, $seperator ){
	foreach( $keys as $key ){
		if ( array_key_exists( $key, $array ) && $array[$key] !== "" ) {
			$string .= $array[$key] . $seperator;
		}
	}
	return $string;
}
function ubc_experts_profile_processing_position( $positions ){
	$processed_positions = '';

	if ( empty( $positions ) ) {
		return $processed_positions; 
	}
	foreach( $positions as $position ){		
		$processed_positions .= $position['position'];
		if ( $position['organization'] ) {
			$processed_positions .= ', '.$position['organization'];
		}
		if ( $position['url'] ) {
			$processed_positions = ubc_experts_process_wrap_link($processed_positions, $position['url']);
		}	
		
	}
	return $processed_positions; 

}

function ubc_experts_process_wrap_link($string, $url) {
	if ( ! $string || ! $url ) {
		return;
	}
	$processed_string = "<a href='$url'>$string</a>";
	return $processed_string;
} 
function process_social_media( $social_media ){
	$social_media_options = array(
		array( 	"type"        => "ubc-blog", 	
				"label"       => "UBC Blog", 
				"service_url" => "http://blogs.ubc.ca/",	
				"user_url"    => "http://blogs.ubc.ca/{value}" ),
		array( 	"type"        => "ubc-wiki", 	
				"label"       => "UBC Wiki",
				"service_url" => "http://wiki.ubc.ca/",		
				"user_url"    => "http://wiki.ubc.ca/User:{value}" ),
		array( 	"type"        => "twitter", 		
				"label"       => "Twitter",
				"service_url" => "http://twitter.com/",			
				"user_url"    => "http://twitter.com/{value}" ),
		array( 	"type"        => "facebook",		
				"label"       => "Facebook",
				"service_url" => "http://www.facebook.com/",			
				"user_url"    => "http://www.facebook.com/{value}" ),
		array( 	"type"        => "google-plus", 	
				"label"       => "Google+",
				"service_url" => "http://plus.google.com/",		
				"user_url"    => "http://plus.google.com/{value}" ),
		array( 	"type"        => "linked-in",	
				"label"       => "LinkedIn",
				"service_url" => "http:/ca.linkedin.com/",			
				"user_url"    => "http://ca.linkedin.com/in/{value}" ), 
		array( 	"type"        => "delicious",	
				"label"       => "Delicious",
				"service_url" => "http://www.delicious.com/",			
				"user_url"    => "http://www.delicious.com/{value}" ),
		array( 	"type"        => "picasa",		
				"label"       => "Picasa",
				"service_url" => "http://picasaweb.google.com/",
				"user_url"    => "http://picasaweb.google.com/{value}" ),
		array(  "type"        => "flickr",		
				"label"       => "Flickr",
				"service_url" => "http://www.flickr.com/",				
				"user_url"    => "http://www.flickr.com/photos/{value}" ),
		array( 	"type"        => "tumblr",		
				"label"       => "Tumblr",
				"service_url" => "http://tumblr.com/",			
				"user_url"    => "http://{value}.tumblr.com" ), 
		array( 	"type"        => "blogger",		
				"label"       => "Blogger",
				"service_url" => "http://blogspot.com/",			
				"user_url"    => "http://{value}.blogspot.com/" ), 
		array( 	"type"        => "posterous",	
				"label"       => "Posterous",
				"service_url" => "http://posterous.com/",	
				"user_url"    => "http://{value}.posterous.com" ),
		array( 	"type"        => "wordpress-com",
				"label"       => "WordPress.com",
				"service_url" => "http://wordpress.com/",	
				"user_url"    => "http://{value}.wordpress.com" ),
		array( 	"type"        => "youtube",		
				"label"       => "YouTube",
				"service_url" => "http://youtube.com/",		
				"user_url"    => "http://youtube.com/{value}" ),
		array( 	"type"        => "vimeo",		
				"label"       => "Vimeo",
				"service_url" => "http://vimeo.com/",			
				"user_url"    => "http://vimeo.com/{value}" ),
		array( 	"type"        => "wikipedia",		
				"label"       => "Wikipedia",
				"service_url" => "http://wikipedia.org/",			
				"user_url"    => "http://wikipedia.org/wiki/User:{value}" ),
		array( 	"type"        => "slideshare",		
				"label"       => "SlideShare",
				"service_url" => "http://www.slideshare.net/",			
				"user_url"    => "http://www.slideshare.net/{value}" ),
	);
	foreach( $social_media_options as $option ){
		if ( $social_media['option'] == $option['label'] ) {
			return '<!-- wp:paragraph --><p><a href="'.str_replace( '{value}', $social_media['username'], $option['user_url'] ).'"><strong>'.$option['label'].'/ </strong> '.$social_media['username'].'</a></p><!-- /wp:paragraph -->';
		}
	}	
	
}

/** 
* ## OPTIONS
*
* <page>
* : The page of the query we are in.
*
* [--page=<page>]
* : What page in the query we are processing.
* ---
* default: 1
* ---
*
* ## EXAMPLES
*
*     wp bam_ubc_process_experts 3
*
*/
function bam_ubc_process_experts( $args, $assoc_args ) {

	$gutenberg_template = '<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"33.33%%"} -->
<div class="wp-block-column" style="flex-basis:33.33%%">
<!-- wp:image {"id":10000,"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="%s" alt="" class="wp-image-10000"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"66.66%%"} -->
<div class="wp-block-column" style="flex-basis:66.66%%"><!-- wp:heading -->
<h2>%s</h2>
<!-- /wp:heading -->

<!-- wp:heading {"level":3} -->
<h3>%s</h3>
<!-- /wp:heading -->

<!-- wp:heading {"level":4} -->
<h4>%s</h4>
<!-- /wp:heading -->

<!-- wp:heading {"level":4} -->
<h4>%s</h4>
<!-- /wp:heading -->

<!-- wp:heading {"level":4} -->
<h4>%s</h4>
<!-- /wp:heading -->

<!-- wp:heading {"level":3} -->
<h3><br>Fields</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>%s</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3><br>Expertise</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>%s</p>
<!-- /wp:paragraph -->


</div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns -->
<div class="wp-block-columns">
<!-- wp:column {"width":"33.33%%"} -->
<div class="wp-block-column" style="flex-basis:33.33%%">
<!-- wp:heading {"level":3} -->
<h3>Contact Information</h3>
<!-- /wp:heading -->
%s 

%s 

%s

%s

%s

%s
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"66.66%%"} -->
<div class="wp-block-column" style="flex-basis:66.66%%">

%s

%s 

%s

%s
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->';

	$paged = ( $args[0] ) ? $args[0] : 1;

	
	$profile_args = array(
		'post_type'			=> 'profile_cct',
		'posts_per_page'    => 30,
		'paged' 			=> $paged
	);
	
	$profiles = new WP_Query($profile_args);
	if ( $profiles->have_posts() ) {
		while( $profiles->have_posts() ){
			$profiles->the_post();
			$post_id = get_the_ID();
			// taxonomies
			$faculties = get_the_terms( $post_id, 'profile_cct_faculty' );
			$campuses = get_the_terms( $post_id, 'profile_cct_campus' );
			$fields = get_the_terms( $post_id, 'profile_cct_field' );
			$faculty_links = ubc_experts_process_terms($faculties);
			$campus_links = ubc_experts_process_terms($campuses);
			$field_links = ubc_experts_process_terms($fields);

			//meta
			$profile_meta = get_post_meta($post_id, 'profile_cct', true);
			$profile_pic = get_post_meta($post_id, '_thumbnail_id', true);
			$profile_pic_url = wp_get_attachment_image_url($profile_pic, 'full');
			$name_keys = array('salutations','first','middle','last','credentials' );
			$name = ubc_experts_process_array_keys($profile_meta['name'], $name_keys, '', ' ');
			$positions = ubc_experts_profile_processing_position($profile_meta['position']);
			$departments = array();
			if ( ! empty($profile_meta['department']) ){
				foreach( $profile_meta['department'] as $d ){
					if ( $d['url']) {
						$departments[] = ubc_experts_process_wrap_link($d['department'], $d['url']);
					}else{
						$departments[] = $d['department'];
					}
				}
			}
			$department_list = implode(', ', $departments);
			
			$expertise = $profile_meta['research']['textarea'];

			$pronouns = '';
			if ( $profile_meta["clone_preferred_name_pronouns_"][0]["text"] != '' ) {
				$pronouns = '<!-- wp:paragraph --><p>Chosen name and pronouns</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>' . $profile_meta["clone_preferred_name_pronouns_"][0]["text"].'</p><!-- /wp:paragraph -->';
			}
			$pronunciation = '';
			if ( $profile_meta["clone_pronunciation"]["textarea"] ) {
				$pronunciation = '<!-- wp:paragraph --><p>Pronunciation</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>' . $profile_meta["clone_pronunciation"]["textarea"] .'</p><!-- /wp:paragraph -->';
			}
			$email_list = array();
			foreach( $profile_meta['email'] as $email ){
				$email_list[] = '<!-- wp:paragraph --><p><a href="mailto:'.$email["email"].'">'.$email["email"].'</a></p><!-- /wp:paragraph -->';
			}
			
			$phone_list = array();
			foreach( $profile_meta['phone'] as $phone ){
				if ( $phone["tel-2"] && $phone["tel-3"] ) {
					$phone_list[] = '<!-- wp:paragraph --><p class="telephone tel">'.$phone["option"].': '.$phone["tel-1"].'-'.$phone["tel-2"].'-'.$phone["tel-3"].' '.$phone["extension"].'</p><!-- /wp:paragraph -->';

				}
			}
			$social_media_list = array();
			foreach( $profile_meta['social'] as $social ){
				$social_media_list[] = process_social_media( $social );
			}
			$website_list = array();
			foreach( $profile_meta['website'] as $website ){
				$website_list[] = '<!-- wp:paragraph --><p><a href="'.$website["website"].'">'.( $website["site-title"] ? $website["site-title"] : $website["website"]).'</a></p><!-- /wp:paragraph -->';
			}
			$languages = '';
			if ( $profile_meta['clone_other_languages'] ) {
				$languages_heading = '<!-- wp:heading {"level":3} -->
				<h3>Languages</h3><!-- /wp:heading -->';
				foreach( $profile_meta['clone_other_languages'] as $language ){
					if ( $language['text'] != '') {
						$languages .= '<!-- wp:paragraph --><p>'.$language['text'].'</p><!-- /wp:paragraph -->';
					}
				}
				if ( $languages && $languages != '' ){
					$languages = $languages_heading . $languages;
				} 
				
			}
			$news_feed = '';
			if ( $profile_meta['clone_news_feed']['textarea'] ) {
				$news_feed = '<!-- wp:heading {"level":3} -->
				<h3>In the Media</h3>
				<!-- /wp:heading --><!-- wp:html -->'.$profile_meta['clone_news_feed']['textarea'].'<!-- /wp:html -->';
				
			}
			$media_gallery = '';
			if ( $profile_meta['clone_wordpress_gallery']['textarea'] ) {
				$media_gallery = '<!-- wp:heading {"level":3} -->
				<h3>Image Gallery</h3>
				<!-- /wp:heading --><!-- wp:shortcode -->'.$profile_meta['clone_wordpress_gallery']['textarea'].'<!-- /wp:shortcode -->';
				
			}

			$embeds = '';
			
			if ( $profile_meta['clone_media_embed']['textarea'] ) {

				preg_match_all('/(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=|\/user\/\S+|\/ytscreeningroom\?v=))([\w\-]{10,12})\b/', $profile_meta['clone_media_embed']['textarea'], $match);

				if ( ! empty( $match[1] ) ){
					$embeds = '<!-- wp:heading {"level":3} -->
					<h3>Videos</h3><!-- /wp:heading -->';
					foreach( $match[1] as $id ){
						$embeds .= '<!-- wp:embed {"url":"https://youtu.be/'.$id.'","type":"video","providerNameSlug":"youtube","responsive":true,"className":"wp-embed-aspect-16-9 wp-has-aspect-ratio"} -->
		<figure class="wp-block-embed is-type-video is-provider-youtube wp-block-embed-youtube wp-embed-aspect-16-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">
		https://youtu.be/'.$id.'
		</div></figure>
		<!-- /wp:embed --> ';
					}
				}					
			}

			$formatted_content = sprintf( $gutenberg_template, 
				$profile_pic_url, 
				$name, 
				$positions, 
				$faculty_links,
				$department_list, 
				$campus_links,
				$field_links,
				$expertise,
				$pronouns,
				$pronunciation,
				implode('', $email_list),
				implode('', $phone_list),
				implode('', $website_list),
				implode('', $social_media_list),
				$languages,
				$news_feed,
				$media_gallery,
				$embeds
			);

			$maybe_update =  get_post_meta( $post_id, 'related_post', true );
			$id = ( $maybe_update ? $maybe_update : '' );

			$excerpt = "<h3>$positions</h3>$expertise</p>";
			$expert_post = array(
				'ID'			=> $id,
				'post_title'	=> $name,
				'post_content'  => $formatted_content,
				'post_status'   => 'publish',
				'post_excerpt'		=> $excerpt

			);
			$new_post_id = wp_insert_post( $expert_post, false );

			// save post meta 			
			if ( $profile_meta["clone_pronunciation"]["textarea"] ) {
				update_post_meta( $new_post_id, 'self_identification', $profile_meta["clone_pronunciation"]["textarea"] );
			}
			if ( $profile_meta["clone_internal_memo"]["textarea"] ) {
				update_post_meta( $new_post_id, 'internal_memo', $profile_meta["clone_internal_memo"]["textarea"] );
			}
			update_post_meta( $new_post_id, '_thumbnail_id', $profile_pic );

			update_post_meta( $post_id, 'related_post', $new_post_id );

			ubc_experts_insert_new_terms($faculties, $new_post_id, 'category');
			ubc_experts_insert_new_terms($campuses, $new_post_id, 'category');
			ubc_experts_insert_new_terms($fields, $new_post_id, 'profile_field');
		}
	}
	
	$paged++;
	$subsite = $args[1];
	if ( $paged <= $profiles->max_num_pages ) {
		WP_CLI::runcommand( 'bam_ubc_process_experts ' . $paged . ' ' , array( 'launch' => true ) );
	}
}
WP_CLI::add_command( 'bam_ubc_process_experts', 'bam_ubc_process_experts' );

function bam_ubc_process_fields_terms (){

	// taxonomies
	$faculties = get_terms( 'profile_cct_faculty' );
	$campuses = get_terms( 'profile_cct_campus' );
	$fields = get_terms( 'profile_cct_field' );
	if ( !empty( $fields ) ) {
		foreach( $fields as $field ){

			if ( $field->parent ) {

				// get parent term
				$get_parent = get_term_by( 'term_id', $field->parent, 'profile_cct_field' );
				// get parent field
				$get_parent_field = get_term_by('slug', $get_parent->slug, 'profile_field');

				// check if parent exists.
				if ( is_wp_error( $get_parent_field ) || $get_parent_field =='' ) {
					// insert part term
					$wp_parent_term = wp_insert_term( $get_parent->name, 'profile_field', array( 'slug' => $get_parent->slug ) );

					// check if child term exists.
					$get_field = get_term_by('slug', $field->slug, 'profile_field');
					if ( is_wp_error( $get_field ) || $get_field =='' ) {
						$wp_term = wp_insert_term( $field->name, 'profile_field', array( 'slug' => $field->slug, 'parent' => $wp_parent_term->term_id ) );
					}
				} else {
					
					$get_field = get_term_by('slug', $field->slug, 'profile_field');
					if ( is_wp_error( $get_field ) || $get_field =='' ) {
						$wp_term = wp_insert_term( $field->name, 'profile_field', array( 'slug' => $field->slug, 'parent' => $get_parent_field->term_id ) );
					}
				}	
			}else {

				$get_field = get_term_by('slug', $field->slug, 'profile_field');

				if ( is_wp_error( $get_field ) || $get_field == '' ) {
					$wp_term = wp_insert_term( $field->name, 'profile_field', array( 'slug' => $field->slug ) );	
				}	
			}
		}	
	}
	if ( !empty( $faculties ) ) {
		$faculties_parent = get_term_by('slug', 'faculties', 'category');

		foreach( $faculties as $faculty ){ 

			if ( !is_wp_error( $faculty ) ) {
				$wp_term = wp_insert_term( $faculty->name, 'category', array( 'slug' => $faculty->slug, 'parent' => $faculties_parent->term_id ) );	

			}	
		}	
	}

	if ( !empty( $campuses ) ) {
		$campus_parent = get_term_by('slug', 'campuses', 'category');

		foreach( $campuses as $campus ){ 

			if ( !is_wp_error( $campus ) ) {
				$wp_term = wp_insert_term( $campus->name, 'category', array( 'slug' => $campus->slug, 'parent' => $campus_parent->term_id ) );	
			}	
		}	
	}
}
WP_CLI::add_command( 'bam_ubc_process_fields_terms', 'bam_ubc_process_fields_terms' );
