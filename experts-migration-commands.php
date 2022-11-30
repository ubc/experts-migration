<?php

class Bam_Experts_Migration {
	/**
	 * Collection of media contacts. 
	 * 
	 * During the process we need to add media context to content based on faculty or campus. This array of reusable block IDs to be applied against.
	 * 
	 * @since 0.1
	 * @var array
	 * 
	 */
	protected $media_contacts = array();

	/**
	 * Take a collection of old terms and check if they exist within new taxonomy.
	 * 
	 * Transferring terms from profiles plugin based taxonomies requires us checking if they exist within new taxonomies. If they do we add them to the post we are working with.  
	 * 
	 * @since 0.1
	 * 
	 * @param array 	$terms 		An array of WP_term objects 
	 * @param int 		$post_id 	The ID for the current post being processed 
	 * @param string 	$taxonomy	The name of the original taxonomy we are migrating terms to  
	 *  		
	 */
	protected function ubc_experts_insert_new_terms ( $terms, $post_id, $taxonomy ) {
		if ( empty( $terms ) ) {
			return;
		}
	
		$wp_terms = array();
		foreach($terms as $term) {
	
				$wp_term = get_term_by('slug', $term->slug, $taxonomy );
				if ( !is_wp_error( $wp_term ) || !$wp_term == '' ) {
					$wp_terms[] = absint( $wp_term->term_id );
				}	
			
		}
	
		$wp_terms = wp_set_object_terms( absint($post_id), $wp_terms, sanitize_title_with_dashes($taxonomy), true );
	
	}
	/**
	 * Process the terms associated with a post and add a link in the content. 
	 * 
	 * Take the terms associated with a post and generate a content friendly list of links to the term landing page. 
	 *
	 * @param array $terms An array of WP_term objects
	 * 
	 */
	protected function ubc_experts_process_terms($terms){
		// Bail early if we're not passed any $terms. Return empty string as this method returns strings when successful.
		if ( empty( $terms ) ) {
			return '';
		}
		
		$processed_terms = array();

		foreach( $terms as $term ) {
			$term_link = get_term_link( $term );

			if ( is_wp_error( $term_link ) ){
				continue;
			}

			$processed_terms[] = "<a href='".esc_url( $term_link )."'>".wp_kses_post($term->name)."</a>";	
		}

		return implode(', ', $processed_terms);
	
		
	}
	/**
	 * Process the contents of a array to generate custom string.
	 * 
	 * Some of the meta information is saved in a way that we need to process it into a specifically formatted string. 
	 *
	 * @param array $array The array of content
	 * @param array $keys The keys used within the $array array
	 * @param string $string The string to append all non empty array entries into
	 * @param string $seperator what we use to separate entries within the string
	 * 
	 * @return string
	 */
	protected function ubc_experts_process_array_keys($array, $keys, $string = '', $seperator ){
		foreach( $keys as $key ){
			if ( array_key_exists( $key, $array ) && $array[$key] !== "" ) {
				$string .= $array[$key] . $seperator;
			}
		}
		return $string;
	}
	/**
	 * Process position meta information.
	 * 
	 * Positions for each expert may or may not have links so each position must be processed to handle both.
	 *
	 * @param array $positions Array of positions for the expert, may or may not include a url for the organization.
	 * @return string
	 */
	protected function ubc_experts_profile_processing_position( $positions ){
		$processed_positions = '';
	
		if ( empty( $positions ) ) {
			return $processed_positions; 
		}
		foreach( $positions as $position ){		
			$processed_positions .= '<!-- wp:heading {"level":3} --><h3>'.$position['position'];
			if ( array_key_exists( 'organization', $position ) && $position['organization'] != '' ) {
				$processed_positions .= ', '.$position['organization'];
			}
			if ( array_key_exists( 'url', $position ) && $position['url']  ) {
				$processed_positions = $this->ubc_experts_process_wrap_link($processed_positions, $position['url']);
			}	
			$processed_positions .= '</h3><!-- /wp:heading -->';
		}

		return $processed_positions; 
	
	}
	/**
	 * Wraps a string with a link
	 * 
	 * Process the various meta that may have links associated with text. 
	 *
	 * @param string $string The link text
	 * @param string $url The link to wrap the text
	 * @return string
	 */
	protected function ubc_experts_process_wrap_link($string, $url) {
		if ( ! $string || ! $url ) {
			return;
		}

		$processed_string = "<a href='". esc_url($url) ."'>".wp_kses_post($string)."</a>";
		return $processed_string;
	} 

	/**
	 * Generate the reusable blocks used for Media Contacts
	 * 
	 * Each expert needs a media contact added into the content. This function ensures the reusable blocks are already created to be injected.
	 *
	 */
	protected function generate_media_contacts(){
		$erik_rolfsen['title'] = 'Erik Rolfsen Media Relations';	
		$erik_rolfsen['content'] = '<!-- wp:heading {"level":3} -->
<h3>Media Contact</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Erik Rolfsen</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Senior Media Relations Strategist</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Tel: 604.822.2644</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Cell: 604.209.3048</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Email: <a href="mailto:erik.rolfsen@ubc.ca">erik.rolfsen@ubc.ca</a></p>
<!-- /wp:paragraph -->';
		
		$patty_wellborn['title'] = 'Patty Wellborn Media Relations';
		$patty_wellborn['content'] = '<!-- wp:heading {"level":3} -->
		<h3>Media Contact</h3>
		<!-- /wp:heading -->
		
		<!-- wp:paragraph -->
		<p>Patty Wellborn</p>
		<!-- /wp:paragraph -->
		
		<!-- wp:paragraph -->
		<p>Media Relations Strategist</p>
		<!-- /wp:paragraph -->
		
		<!-- wp:paragraph -->
		<p>Tel: 250.317.0293</p>
		<!-- /wp:paragraph -->
		
		<!-- wp:paragraph -->
		<p>Email: <a href="mailto:patty.wellborn@ubc.ca">patty.wellborn@ubc.ca</a></p>
		<!-- /wp:paragraph -->';

		$lou_corpuz_bosshart['title'] = 'Lou Corpuz-Bosshart Media Relations';
		$lou_corpuz_bosshart['content'] = '<!-- wp:heading {"level":3} -->
		<h3>Media Contact</h3>
		<!-- /wp:heading -->
		
		<!-- wp:paragraph -->
		<p>Lou Corpuz-Bosshart</p>
		<!-- /wp:paragraph -->
		
		<!-- wp:paragraph -->
		<p>Media Relations Specialist</p>
		<!-- /wp:paragraph -->
		
		<!-- wp:paragraph -->
		<p>Tel: 604.822.2048</p>
		<!-- /wp:paragraph -->
		
		<!-- wp:paragraph -->
		<p>Cell: 604.999.0473</p>
		<!-- /wp:paragraph -->
		
		<!-- wp:paragraph -->
		<p>Email: <a href="mailto:lou.bosshart@ubc.ca">lou.bosshart@ubc.ca</a></p>
		<!-- /wp:paragraph -->';

		$alex_walls['title'] = 'Alex Walls Media Relations';
		$alex_walls['content'] = '<!-- wp:heading {"level":3} -->
		<h3>Media Contact</h3>
		<!-- /wp:heading -->
		
		<!-- wp:paragraph -->
		<p>Alex Walls</p>
		<!-- /wp:paragraph -->
		
		<!-- wp:paragraph -->
		<p>Media Relations Specialist</p>
		<!-- /wp:paragraph -->
		
		<!-- wp:paragraph -->
		<p>Tel: 604.822.4636</p>
		<!-- /wp:paragraph -->
		
		<!-- wp:paragraph -->
		<p>Cell: 778.984.6173</p>
		<!-- /wp:paragraph -->
		
		<!-- wp:paragraph -->
		<p>Email: <a href="mailto:alex.walls@ubc.ca">alex.walls@ubc.ca</a></p>
		<!-- /wp:paragraph -->';

		$collins_maina['title'] = 'Collins Maina Media Relations';	
		$collins_maina['content'] = '<!-- wp:heading {"level":3} -->
		<h3>Media Contact</h3>
		<!-- /wp:heading -->
		
		<!-- wp:paragraph -->
		<p>Collins Maina</p>
		<!-- /wp:paragraph -->
		
		<!-- wp:paragraph -->
		<p>Assistant Media Relations Specialist</p>
		<!-- /wp:paragraph -->
		
		<!-- wp:paragraph -->
		<p>Tel:  604.802.0779</p>
		<!-- /wp:paragraph -->
		
		<!-- wp:paragraph -->
		<p>Email: <a href="mailto:collins.maina@ubc.ca">collins.maina@ubc.ca</a></p>
		<!-- /wp:paragraph -->';

		$erik_rolfsen['title'] = 'Erik Rolfsen Media Relations';
		$erik_rolfsen['content'] = '<!-- wp:heading {"level":3} -->
		<h3>Media Contact</h3>
		<!-- /wp:heading -->
		
		<!-- wp:paragraph -->
		<p>Erik Rolfsen</p>
		<!-- /wp:paragraph -->
		
		<!-- wp:paragraph -->
		<p>Media Relations Specialist</p>
		<!-- /wp:paragraph -->
		
		<!-- wp:paragraph -->
		<p>Tel: 604.822.2644</p>
		<!-- /wp:paragraph -->
		
		<!-- wp:paragraph -->
		<p>Cell: 604.209.3048</p>
		<!-- /wp:paragraph -->
		
		<!-- wp:paragraph -->
		<p>Email: <a href="mailto:erik.rolfsen@ubc.ca">erik.rolfsen@ubc.ca</a></p>
		<!-- /wp:paragraph -->';


		$collins_post = array(
			'post_title'	=> wp_strip_all_tags( sanitize_title_with_dashes( $collins_maina['title'] ) ),
			'post_content'  => wp_kses_post( $collins_maina['content'] ),
			'post_status'   => 'publish',
			'post_type'		=> 'wp_block'

		);
		$collins_maina_id = wp_insert_post( $collins_post, false );
		if ( !is_wp_error( $collins_maina_id ) ) {
			$collins_maina['id'] = absint( $collins_maina_id );
		}	
		
		$alex_post = array(
			'post_title'	=> wp_strip_all_tags( sanitize_title_with_dashes( $alex_walls['title'] ) ),
			'post_content'  => wp_kses_post( $alex_walls['content'] ),
			'post_status'   => 'publish',
			'post_type'		=> 'wp_block'

		);
		$alex_walls_id = wp_insert_post( $alex_post, false );
		if ( !is_wp_error( $alex_walls_id ) ) {
			$alex_walls['id'] = absint( $alex_walls_id );
		}	 

		$lou_post = array(
			'post_title'	=> wp_strip_all_tags( sanitize_title_with_dashes( $lou_corpuz_bosshart['title'] ) ),
			'post_content'  => wp_kses_post( $lou_corpuz_bosshart['content'] ),
			'post_status'   => 'publish',
			'post_type'		=> 'wp_block'

		);
		$lou_corpuz_bosshart_id = wp_insert_post( $lou_post, false );
		if ( !is_wp_error( $lou_corpuz_bosshart_id ) ) {
			$lou_corpuz_bosshart['id'] = absint( $lou_corpuz_bosshart_id );
		}	

		$patty_post = array(
			'post_title'	=> wp_strip_all_tags( sanitize_title_with_dashes( $patty_wellborn['title'] ) ),
			'post_content'  => wp_kses_post( $patty_wellborn['content'] ),
			'post_status'   => 'publish',
			'post_type'		=> 'wp_block'

		);
		$patty_wellborn_id = wp_insert_post( $patty_post, false );
		if ( !is_wp_error( $patty_wellborn_id ) ) {
			$patty_wellborn['id'] = absint( $patty_wellborn_id );
		}
		$erik_post = array(
			'post_title'	=> wp_strip_all_tags( sanitize_title_with_dashes( $erik_rolfsen['title'] ) ),
			'post_content'  => wp_kses_post( $erik_rolfsen['content'] ),
			'post_status'   => 'publish',
			'post_type'		=> 'wp_block'

		);
		$erik_rolfsen_id = wp_insert_post( $erik_post, false );
		if ( !is_wp_error( $erik_rolfsen_id ) ) {
			$erik_rolfsen['id'] = absint( $erik_rolfsen_id );
		}
		$this->media_contacts = array(	
			// 'Erik Rolfsen' 
			'arts' 						=> $erik_rolfsen['id'],	
			'medicine' 					=> $erik_rolfsen['id'],	
			'library' 					=> $erik_rolfsen['id'],
			// 'Lou Corpuz-Bosshart'	
			'applied_science' 			=> $lou_corpuz_bosshart['id'],	
			'forestry' 					=> $lou_corpuz_bosshart['id'],	
			'pharmaceutical_sciences' 	=> $lou_corpuz_bosshart['id'],
			// 'Alex Walls'
			'law' 						=> $alex_walls['id'],	
			'science' 					=> $alex_walls['id'],
			// Collins Maina	
			'education' 				=> $collins_maina['id'],	
			'land_and_food_systems' 	=> $collins_maina['id'],	
			'sauder_school_of_business' => $collins_maina['id'],	
			// Patty Wellborn
			'okanagan'					=> $patty_wellborn['id']
		);

	}
	/**
	 * Process social media data 
	 * 
	 * Processes expert related social media meta data and create links to social media platforms
	 *
	 * @param array $social_media array of social media associated with expert
	 * @return string
	 */
	protected function process_social_media( $social_media ){
		$social_media_options = array(
			array( 	"type"        => "ubc-blog", 	
					"label"       => "UBC Blog", 
					"service_url" => "https://blogs.ubc.ca/",	
					"user_url"    => "https://blogs.ubc.ca/{value}" ),
			array( 	"type"        => "ubc-wiki", 	
					"label"       => "UBC Wiki",
					"service_url" => "https://wiki.ubc.ca/",		
					"user_url"    => "https://wiki.ubc.ca/User:{value}" ),
			array( 	"type"        => "twitter", 		
					"label"       => "Twitter",
					"service_url" => "https://twitter.com/",			
					"user_url"    => "https://twitter.com/{value}" ),
			array( 	"type"        => "facebook",		
					"label"       => "Facebook",
					"service_url" => "https://www.facebook.com/",			
					"user_url"    => "https://www.facebook.com/{value}" ),
			array( 	"type"        => "google-plus", 	
					"label"       => "Google+",
					"service_url" => "https://plus.google.com/",		
					"user_url"    => "https://plus.google.com/{value}" ),
			array( 	"type"        => "linked-in",	
					"label"       => "LinkedIn",
					"service_url" => "https:/ca.linkedin.com/",			
					"user_url"    => "https://ca.linkedin.com/in/{value}" ), 
			array( 	"type"        => "delicious",	
					"label"       => "Delicious",
					"service_url" => "https://www.delicious.com/",			
					"user_url"    => "https://www.delicious.com/{value}" ),
			array( 	"type"        => "picasa",		
					"label"       => "Picasa",
					"service_url" => "https://picasaweb.google.com/",
					"user_url"    => "https://picasaweb.google.com/{value}" ),
			array(  "type"        => "flickr",		
					"label"       => "Flickr",
					"service_url" => "https://www.flickr.com/",				
					"user_url"    => "https://www.flickr.com/photos/{value}" ),
			array( 	"type"        => "tumblr",		
					"label"       => "Tumblr",
					"service_url" => "https://tumblr.com/",			
					"user_url"    => "https://{value}.tumblr.com" ), 
			array( 	"type"        => "blogger",		
					"label"       => "Blogger",
					"service_url" => "https://blogspot.com/",			
					"user_url"    => "https://{value}.blogspot.com/" ), 
			array( 	"type"        => "posterous",	
					"label"       => "Posterous",
					"service_url" => "https://posterous.com/",	
					"user_url"    => "https://{value}.posterous.com" ),
			array( 	"type"        => "wordpress-com",
					"label"       => "WordPress.com",
					"service_url" => "https://wordpress.com/",	
					"user_url"    => "https://{value}.wordpress.com" ),
			array( 	"type"        => "youtube",		
					"label"       => "YouTube",
					"service_url" => "https://youtube.com/",		
					"user_url"    => "https://youtube.com/{value}" ),
			array( 	"type"        => "vimeo",		
					"label"       => "Vimeo",
					"service_url" => "https://vimeo.com/",			
					"user_url"    => "https://vimeo.com/{value}" ),
			array( 	"type"        => "wikipedia",		
					"label"       => "Wikipedia",
					"service_url" => "https://wikipedia.org/",			
					"user_url"    => "https://wikipedia.org/wiki/User:{value}" ),
			array( 	"type"        => "slideshare",		
					"label"       => "SlideShare",
					"service_url" => "https://www.slideshare.net/",			
					"user_url"    => "https://www.slideshare.net/{value}" ),
		);
		foreach( $social_media_options as $option ){
			if ( $social_media['option'] == $option['label'] ) {
				return '<!-- wp:paragraph --><p><a href="'.esc_url( str_replace( '{value}', $social_media['username'], $option['user_url'] ) ).'"><strong>'.$option['label'].'/ </strong> '.$social_media['username'].'</a></p><!-- /wp:paragraph -->';
			}
		}	
		
	}
	
	/** 
	 * 
	 * 
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
	*     wp bam_ubc_experts process_experts 3
	*
	*/
	public function process_experts( $args, $assoc_args ) {
	
		$gutenberg_template = '<!-- wp:columns -->
	<div class="wp-block-columns"><!-- wp:column {"width":"33.33%%"} -->
	<div class="wp-block-column" style="flex-basis:33.33%%">
	%s
	</div>
	<!-- /wp:column -->
	
	<!-- wp:column {"width":"66.66%%"} -->
	<div class="wp-block-column" style="flex-basis:66.66%%"><!-- wp:heading -->
	<h1>%s</h1>
	<!-- /wp:heading -->
	
	%s
	
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

	%s
	
	<!-- wp:block {"ref":%d} /-->
	</div>
	<!-- /wp:column -->
	
	<!-- wp:column {"width":"66.66%%"} -->
	<div class="wp-block-column" style="flex-basis:66.66%%">
	<!-- wp:heading {"level":3} -->
	<h3>Expertise</h3>
	<!-- /wp:heading -->
	<!-- wp:paragraph -->
	<p>%s</p>
	<!-- /wp:paragraph -->

	%s
	
	%s 
	
	%s
	
	%s
	</div>
	<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->';
	
		$paged = ( $args[0] ) ? absint($args[0]) : 1;
	
		$profile_args = array(
			'post_type'			=> 'profile_cct',
			'posts_per_page'    => 30,
			'paged' 			=> $paged
		);
		
		$profiles = new WP_Query($profile_args);
		if ( $profiles->have_posts() ) {
	
			while( $profiles->have_posts() ){
				$profiles->the_post();
				$post_id = absint( get_the_ID() );

				if ( $post_id == 0 ) {
					continue;
				}
				// taxonomies

				$faculties = get_the_terms( $post_id, 'profile_cct_faculty' );
				$faculty_links = $this->ubc_experts_process_terms($faculties);

				$campuses = get_the_terms( $post_id, 'profile_cct_campus' );
				$campus_links = $this->ubc_experts_process_terms($campuses);

				$fields = get_the_terms( $post_id, 'profile_cct_field' );
				$field_links = $this->ubc_experts_process_terms($fields);
	
				//meta
				$profile_meta = get_post_meta($post_id, 'profile_cct', true);
				$profile_pic = get_post_meta($post_id, '_thumbnail_id', true);

				if ( $profile_pic && $profile_pic != '' ) {
					$profile_pic_url = wp_get_attachment_image_url($profile_pic, 'full');

					$image_container = 	'<!-- wp:image {"id":10000,"sizeSlug":"full","linkDestination":"none"} -->
				<figure class="wp-block-image size-full"><img src="%s" alt="" class="wp-image-10000"/></figure>
				<!-- /wp:image -->';
					$profile_pic_url = sprintf( $image_container, $profile_pic_url );

				} else {
					// using a default image 
					$profile_pic_url = '<!-- wp:html --><div class="empty-photo">
					<img decoding="async" src="https://experts.news.ubc.ca/files/2014/03/ubc_logo.png" alt="UBC Logo">
					</div><!-- /wp:html -->';
				}
				

				if ( !array_key_exists('name', $profile_meta) && emtpy( $profile_meta['name'] ) ) {	
					// if there is no name we move to the next expert.
					continue;
				}

				$name_keys = array('salutations','first','middle','last','credentials' );
				$slug_keys = array( 'first','middle','last' );

				$name = '';
				$slug = '';
				$first = true;
				foreach( $name_keys as $key ){
					if ( array_key_exists( $key, $profile_meta['name'] ) && $profile_meta['name'][$key] !== "" ) {
						if ( ( $key == 'salutations' || $key == 'first' ) && $first  ) {
							$name .= $profile_meta['name'][$key] ;
							$first = false;
						} else
						
						if ( $key == 'credentials' ) {
							$name .= ', ' . $profile_meta['name'][$key] ;
						} else {
							$name .= ' ' . $profile_meta['name'][$key];
						}

						if ( in_array( $key, $slug_keys ) ) {

							$slug .= $profile_meta['name'][$key].' ';
						}
					}
				}

				$positions = '';
				if ( array_key_exists('position', $profile_meta) && $profile_meta['position'] ) {
					$positions = $this->ubc_experts_profile_processing_position($profile_meta['position']);
				}

				$departments = array();
				if ( array_key_exists('department', $profile_meta) && ! empty($profile_meta['department']) ){
					foreach( $profile_meta['department'] as $d ){
						if ( $d['url']) {
							$departments[] = $this->ubc_experts_process_wrap_link($d['department'], $d['url']);
						}else{
							$departments[] = $d['department'];
						}
					}
				}
				$department_list = implode(', ', $departments);
				
				$expertise = '';
				if ( array_key_exists('research', $profile_meta) && $profile_meta['research']['textarea'] ){
					$expertise = wp_kses_post($profile_meta['research']['textarea']);
				}
	
				$pronouns = '';
				if ( array_key_exists('clone_preferred_name_pronouns_', $profile_meta) && $profile_meta["clone_preferred_name_pronouns_"][0]["text"] != '' ) {
					$pronouns = '<!-- wp:paragraph --><p><strong>Chosen name and pronouns: </strong></p><!-- /wp:paragraph --><!-- wp:paragraph --><p>' . wp_kses_post($profile_meta["clone_preferred_name_pronouns_"][0]["text"]).'</p><!-- /wp:paragraph -->';
				}
				
				$pronunciation = '';
				if ( array_key_exists('clone_pronunciation', $profile_meta) && $profile_meta["clone_pronunciation"]["textarea"] ) {
					$pronunciation = '<!-- wp:paragraph --><p>Pronunciation</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>' . wp_kses_post($profile_meta["clone_pronunciation"]["textarea"]) .'</p><!-- /wp:paragraph -->';
				}
				
				$email_list = array();
				if ( array_key_exists('email', $profile_meta) && !empty($profile_meta["email"]) ) {
					foreach( $profile_meta['email'] as $email ){
						$email_list[] = '<!-- wp:paragraph --><p><a href="mailto:'.esc_url($email["email"]).'">'.$email["email"].'</a></p><!-- /wp:paragraph -->';
					}
				}

				$phone_list = array();
				if ( array_key_exists('phone', $profile_meta) && !empty($profile_meta["phone"]) ) {
					foreach( $profile_meta['phone'] as $phone ){
						if ( $phone["tel-2"] && $phone["tel-3"] ) {
							$phone_label = ( $phone["option"] ? $phone["option"] : 'Tel' ).': ';
							$phone_ext = ( $phone["extension"] ? 'Ext'.$phone["extension"] : '' );
							$phone_list[] = '<!-- wp:paragraph --><p class="telephone tel">'.$phone_label.' '.$phone["tel-1"].'-'.$phone["tel-2"].'-'.$phone["tel-3"].' '.$phone_ext.'</p><!-- /wp:paragraph -->';
		
						}
					}
				}
				
				$social_media_list = array();
				if ( array_key_exists('social', $profile_meta) && !empty($profile_meta["social"]) ) {
					foreach( $profile_meta['social'] as $social ){
						$social_media_list[] = $this->process_social_media( $social );
					}
				}

				$website_list = array();
				if ( array_key_exists('website', $profile_meta) && !empty($profile_meta["website"]) ) {
					foreach( $profile_meta['website'] as $website ){
						$website_list[] = '<!-- wp:paragraph --><p><a href="'.esc_url($website["website"]).'">'.( $website["site-title"] ? $website["site-title"] : $website["website"]).'</a></p><!-- /wp:paragraph -->';
					}
				}

				if ( empty( $this->media_contacts ) ) {

					$this->generate_media_contacts();

				}

				$media_contact = '';
				if ( $campuses[0]->slug == "okanagan" ) {
					// Patty Wellborn
					$media_contact = $this->media_contacts['okanagan'];
				}
				
				if ( $faculties ){
					foreach( $this->media_contacts as $key => $contact ){
						if ( $faculties[0]->slug == $key ) {
							$media_contact = $contact;	 
							break;
						}
					}
				}

				$languages = '';
				if ( $profile_meta['clone_other_languages'] ) {
					$languages_heading = '<!-- wp:heading {"level":3} -->
					<h3>Languages</h3><!-- /wp:heading -->';
					foreach( $profile_meta['clone_other_languages'] as $language ){
						if ( $language['text'] != '') {
							$languages .= '<!-- wp:paragraph --><p>'.wp_kses_post( $language['text']).'</p><!-- /wp:paragraph -->';
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
					<!-- /wp:heading --><!-- wp:html -->'.wp_kses_post($profile_meta['clone_news_feed']['textarea']).'<!-- /wp:html -->';
					
				}
				$supervisor = '';
				if ( $profile_meta['clone_full_name_as_it_should_appear_in_media'][0]['text'] ) {
					$supervisor = '<!-- wp:paragraph -->
					<p><strong>Supervisor</strong></p>
					<!-- /wp:paragraph --><!-- wp:paragraph --><p>'.$profile_meta['clone_full_name_as_it_should_appear_in_media'][0]['text'].'</p><!-- /wp:paragraph -->';
					
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
					wp_kses_post($positions), 
					$faculty_links,
					$department_list, 
					$campus_links,
					$field_links,
					$pronouns,
					$pronunciation,
					implode('', $email_list),
					implode('', $phone_list),
					implode('', $website_list),
					implode('', $social_media_list),
					$supervisor,
					$media_contact,
					wp_kses_post($expertise),
					$languages,
					$news_feed,
					$media_gallery,
					$embeds
				);
	
				$maybe_update =  get_post_meta( $post_id, 'related_post', true );
				//check if post exists
				$id = ( FALSE === get_post_status( $maybe_update ) ? '' : $maybe_update );
	
				$excerpt = "<h3>".wp_kses_post($positions)."</h3>".wp_kses_post($expertise)."</p>";
				
				if ( $slug == "" ) {
					$slug = $name;
				}

				$expert_post = array(
					'ID'			=> $id,
					'post_title'	=> $name,
					'post_content'  => $formatted_content,
					'post_status'   => 'publish',
					'post_excerpt'	=> $excerpt,
					'post_name'		=> $slug
				);

				$new_post_id = wp_insert_post( $expert_post, false );

				if ( is_wp_error( $new_post_id ) ||  $new_post_id == 0  ) {
					continue;
				}

				// save post meta 			
				if ( $profile_meta["clone_pronunciation"]["textarea"] ) {
					update_post_meta( $new_post_id, 'self_identification', $profile_meta["clone_self_identification"]["textarea"] );
				}
				if ( $profile_meta["clone_internal_memo"]["textarea"] ) {
					update_post_meta( $new_post_id, 'internal_memo', $profile_meta["clone_internal_memo"]["textarea"] );
				}
				update_post_meta( $new_post_id, '_thumbnail_id', $profile_pic );
	
				update_post_meta( $post_id, 'related_post', $new_post_id );
	
				$this->ubc_experts_insert_new_terms($faculties, $new_post_id, 'category');
				$this->ubc_experts_insert_new_terms($campuses, $new_post_id, 'category');
				$this->ubc_experts_insert_new_terms($fields, $new_post_id, 'profile_field');
				// remove uncategorized
				wp_remove_object_terms($new_post_id, 1, 'category');

				$expert_parent = get_term_by('slug', 'expert', 'category');

				if ( !is_wp_error( $expert_parent ) ) {
					$wp_terms = wp_set_object_terms( absint($new_post_id), $expert_parent->term_id, 'category', true );
				}
			}
		}
		
		$paged++;

		if ( $paged <= $profiles->max_num_pages ) {
			$this->process_experts(array($paged), array());
		}
	}
	
	public function process_fields_terms (){
	
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
					if ( is_wp_error( $get_parent ) ) {
						continue;
					}
					// check if parent exists.
					if ( is_wp_error( $get_parent_field ) || $get_parent_field =='' ) {
						
						// insert parent term
						$wp_parent_term = wp_insert_term( $get_parent->name, 'profile_field', array( 'slug' => sanitize_title_with_dashes($get_parent->slug) ) );
	
						// check if child term exists.
						$get_field = get_term_by('slug', $field->slug, 'profile_field');
						if ( is_wp_error( $get_field ) || $get_field =='' ) {
							$wp_term = wp_insert_term( $field->name, 'profile_field', array( 'slug' => sanitize_title_with_dashes($field->slug), 'parent' => $wp_parent_term->term_id ) );
						}
					} else {
						
						$get_field = get_term_by('slug', $field->slug, 'profile_field');
						if ( is_wp_error( $get_field ) || $get_field =='' ) {
							$wp_term = wp_insert_term( $field->name, 'profile_field', array( 'slug' => sanitize_title_with_dashes($field->slug), 'parent' => $get_parent_field->term_id ) );
						}
					}	
				}else {
	
					$get_field = get_term_by('slug', $field->slug, 'profile_field');
	
					if ( is_wp_error( $get_field ) || $get_field == '' ) {
						$wp_term = wp_insert_term( $field->name, 'profile_field', array( 'slug' => sanitize_title_with_dashes($field->slug) ) );	
					}	
				}
			}	
		}
		if ( !empty( $faculties ) ) {
			$faculties_parent = get_term_by('slug', 'faculties', 'category');
			if ( !is_wp_error( $faculties_parent ) ) {
				foreach( $faculties as $faculty ){ 
	
					if ( !is_wp_error( $faculty ) ) {
						$wp_term = wp_insert_term( $faculty->name, 'category', array( 'slug' => $faculty->slug, 'parent' => $faculties_parent->term_id ) );	
		
					}	
				}	
			}
				
		}
	
		if ( !empty( $campuses ) ) {
			$campus_parent = get_term_by('slug', 'campuses', 'category');
			if ( !is_wp_error( $campus_parent ) ) {

				foreach( $campuses as $campus ){ 
		
					if ( !is_wp_error( $campus ) ) {
						$wp_term = wp_insert_term( $campus->name, 'category', array( 'slug' => $campus->slug, 'parent' => $campus_parent->term_id ) );	
					}	
				}	
			}
		}
	}
}

WP_CLI::add_command( 'bam_ubc_experts', 'Bam_Experts_Migration' );

