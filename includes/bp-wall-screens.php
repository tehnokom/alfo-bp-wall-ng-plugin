<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Activity screen 'News Feed' index
 * 
 */
function bp_wall_activity_screen_newsfeed_activity() {
	do_action( 'bp_wall_activity_screen_newsfeed_activity' );
    bp_core_load_template( apply_filters( 'bp_wall_activity_template_newsfeed_activity', 'members/single/home' ) );
}

function bp_wall_activity_screen_wall_activity() {
	do_action( 'bp_wall_activity_screen_wall_activity' );
    bp_core_load_template( apply_filters( 'bp_wall_activity_template_wall_activity', 'activity/index-wall' ) );
}

/**
 * filter the activity home, member home and group home and replace them
 * with the news templates 
 *
 */
function bp_wall_load_template_filter( $found_template, $templates ) {
	global $bp, $bp_deactivated;
	echo "<pre>bp_wall_load_template_filter(".print_r(array( $found_template, $templates ),1).")</pre>";
    if (bp_wall_is_bp_default())
		echo " BPDEF ";
    if (bp_is_current_component( 'activity' ))
		echo " ACT ";
	if (bp_is_current_component( 'groups' ))
		echo " GRP "	;
	if (bp_is_current_component( 'settings' )) 
		echo "SETT";
	if ( bp_is_group_home() )
		echo " GRPHOME ";
	if (bp_is_active( 'activity' ))  
		echo " ACTACT ";
	# !false 
	# || 
	# !( false || false || true )
	# &&
	# (! false || true )
	if ( 
		#!bp_wall_is_bp_default() 
		#|| 
		!( bp_is_current_component( 'activity' ) || (bp_is_current_component( 'groups' )) || (bp_is_current_component( 'settings' ))) 
		&&
		( !bp_is_group_home() || !bp_is_active( 'activity' ) ) 
	   ) 
	{
		echo " RET1 ";
		return $found_template; 
	}
	$templates_dir = "/templates/bp-legacy/buddypress/";
    
	if ( $templates[0] == "members/single/home.php" ) {
        $template = 'members/single/home-wall.php';
        if ( file_exists( STYLESHEETPATH . '/' . $template ) )
            $found_template = STYLESHEETPATH . '/' . $template;
        else
            $found_template = dirname( __FILE__ ) . $templates_dir . $template;
		echo "FOUND:".$found_template;
        return $found_template;

    }elseif ( $templates[0] == "activity/index.php" ) {
        $template = 'activity/index-wall.php';
        if ( file_exists( STYLESHEETPATH . '/' . $template ) )
            $found_template = STYLESHEETPATH . '/' . $template;
        else
            $found_template = dirname( __FILE__ ) . $templates_dir . $template;

        return $found_template;

    } elseif ( $templates[0] == "groups/single/home.php" )  {
         $template = 'groups/single/home-wall.php';
        if ( file_exists( STYLESHEETPATH . '/' . $template ) )
            $found_template = STYLESHEETPATH . '/' . $template;
        else
            $found_template = dirname( __FILE__ ) . $templates_dir . $template;

        return $found_template;
    }elseif ( $templates[0] == "activity/timeline/index.php" ) {
        $template = 'activity/index-timeline.php';
        if ( file_exists( STYLESHEETPATH . '/' . $template ) )
            $found_template = STYLESHEETPATH . '/' . $template;
        else
            $found_template = dirname( __FILE__ ) . $templates_dir . $template;

        return $found_template;
    }
	
	foreach ( (array) $templates as $template ) {
		
		if ( file_exists( STYLESHEETPATH . '/' . $template ) )
			$filtered_templates[] = STYLESHEETPATH . '/' . $template;
		elseif (file_exists( dirname( __FILE__ ) . $templates_dir . '/' . $template))
			$filtered_templates[] = dirname( __FILE__ ) . $templates_dir . $template;
	}

	if (!empty($filtered_templates)) {
		$found_template = $filtered_templates[0];
		echo " SOMETHING: ". print_r($found_template,1);
	}
	
		echo " RET2 ";
	
	#return apply_filters( 'bp_wall_load_template_filter', $found_template );
	return $found_template;
}
#add_filter( 'bp_located_template', 'bp_wall_load_template_filter', 10, 2 );

/**
 * Load sub-template for non-BP pages
 *
 */

function bp_wall_after_member_body_action () {
	if ( bp_is_user_settings_security() ) {
		bp_wall_load_sub_template( array( 'members/single/settings/security.php'  ), true );
	}
}
add_action('bp_after_member_body','bp_wall_after_member_body_action');

/*
 * Filtering locate_template
 *
 */


function bp_wall_replace_locate_template () {
	
}

/**
 * Replace BP pages
 *
 */

function bp_wall_template_part_filter( $templates, $slug, $name ) {
	#echo $slug;
	#error_log($slug);
	/*if ( 'activity/index' == $slug  ) {
		//return bp_buffer_template_part( 'activity/index-wall' );
		$templates[0] = 'activity/index-wall.php';
	}
	else*/ if ( 'activity/entry' == $slug  ) {
		//return bp_buffer_template_part( 'activity/index-wall' );
		$templates[0] = 'activity/entry-wall.php';
		#echo "OK!";
	}
	elseif ( 'activity/wall-security' == $slug  ) {
		$templates[0] = 'activity/wall-security.php';
		//return bp_buffer_template_part( 'members/single/home-wall' );
	}
	elseif ( 'members/single/activity' == $slug  ) {
		$templates[0] = 'members/single/activity-wall.php';
		//return bp_buffer_template_part( 'members/single/home-wall' );
	} 
	elseif ( 'groups/single/activity' == $slug  ) {
		$templates[0] = 'groups/single/activity-wall.php';
		//return bp_buffer_template_part( 'members/single/home-wall' );
	}
	elseif ( groups_get_current_group()->slug.'_manage'.'group-security' == $slug  ) {
		$templates[0] = 'groups/single/admin/group-security.php';
		//return bp_buffer_template_part( 'members/single/home-wall' );
	}
	elseif ( 'activity/timeline' == $slug  ) {
		$templates[0] = 'activity/index-timeline.php';
		//return bp_buffer_template_part( 'members/single/home-wall' );
	}


	return $templates;
	//return bp_get_template_part( 'members/single/plugins' );
  
}
 
function bp_wall_filter_template_content() {
   // bp_buffer_template_part( 'activity/index-wall' );
}

/**
* Load sub template
* http://buddypress.trac.wordpress.org/ticket/2198
* 
*/
function bp_wall_load_sub_template( $template = false, $require_once = true ) {
	if( empty( $template ) )
        return false;
    if( bp_wall_is_bp_default() ) {
    	if ( $located_template = apply_filters( 'bp_located_template', locate_template( $template , false ), $template ) )	
			load_template( apply_filters( 'bp_load_template', $located_template ), $require_once );
    
    } else {
		#echo "not default";
		if ( $located_template = apply_filters( 'bp_located_template', bp_locate_template( $template , false ), $template ) )	{
			#echo  "located!";
			load_template( apply_filters( 'bp_load_template', $located_template ), $require_once );
		}
        #echo "PATH for ".print_r($template,1).":". bp_get_template_part( $template );

    }
}

/**
 * Check if is buddypress default theme
 * 
 */
function bp_wall_is_bp_default() {
    // if active theme is BP Default or a child theme, then we return true
    // as i was afraid a BuddyPress theme that is not relying on BP Default might
    // be active, i added a BuddyPress version check.
    // I imagine that once version 1.7 will be released, this case will disappear.

    if(current_theme_supports('buddypress') || in_array( 'bp-default', array( get_stylesheet(), get_template() ) )  || ( defined( 'BP_VERSION' ) && version_compare( BP_VERSION, '1.7', '<' ) )) {
		return true;
	} else {
	    // check to see if the 'buddypress' tag is in the theme 
	    // some bp themes are not yet compatible to bp 1.7 but the plugin is updated

   		// get current theme
	    $theme = wp_get_theme();
	    // get current theme's tags
	    $theme_tags = ! empty( $theme->tags ) ? $theme->tags : array();

	    // or if stylesheet is 'bp-default'
	    $backpat = in_array( 'buddypress', $theme_tags );    
	    if($backpat) {
			return true;
		} else {
			return false;
		}
   }
}


if ( class_exists( 'BP_Theme_Compat' ) ) {
   
    //mod:bp1.7
    class BP_Wall_Theme_Compat {
     
        /**
         * Setup the bp plugin component theme compatibility
         */
        public function __construct() { 
            /* this is where we hook bp_setup_theme_compat !! */
            add_action( 'bp_setup_theme_compat', array( $this, 'is_bp_plugin' ) );
        }
     
        /**
         * Are we looking at something that needs theme compatability?
         */
        public function is_bp_plugin() {
            // first we reset the post
            add_action( 'bp_template_include_reset_dummy_post_data', array( $this, 'directory_dummy_post' ) );
            // then we filter ‘the_content’ thanks to bp_replace_the_content
            add_filter( 'bp_replace_the_content', array( $this, 'directory_content'    ) );
        }

        /**
         * Update the global $post with directory data
         */
        public function directory_dummy_post() {

        }
        /**
         * Filter the_content with bp-plugin index template part
         */
        public function directory_content() {
          // bp_buffer_template_part( 'members/single/follow' );
        }
    }
     
    //new BP_Wall_Theme_Compat();

    function bp_wall_add_template_stack( $templates ) {
       // if ( ( bp_is_user_activity() || bp_is_activity_component() || bp_is_group_home() ) && !bp_wall_is_bp_default() ) {
        
        if ( ( bp_is_user() || bp_is_activity_component() || bp_is_group() ) && !bp_wall_is_bp_default() )
        //for bp 1.5 <
        //if ( ( bp_is_member() || bp_is_activity_component() || bp_is_group() ) && !bp_wall_is_bp_default() )
            $templates[] = BP_WALL_PLUGIN_DIR . '/includes/templates/bp-legacy/buddypress';
       // }

        return $templates;
    }
     
    add_filter( 'bp_get_template_stack', 'bp_wall_add_template_stack', 10, 1 );
}


/**
 * Handle the display of a group's Admin pages.
 *
 * @since 0.9.8.2
 */
function bp_wall_groups_screen_group_admin() {
	if ( !bp_is_groups_component() || !bp_is_current_action( 'admin' ) )
		return false;

	if ( bp_action_variables() )
		return false;
	bp_core_redirect( bp_get_group_permalink( groups_get_current_group() ) . 'admin/group-security/' );

}

/**
 * Handle the display of a group's admin/group-settings page.
 *
 * @since 0.9.8.2
 */
function bp_wall_groups_screen_group_admin_post_security() {
	if ( 'group-security' != bp_get_group_current_admin_tab() )
		return false;

	if ( ! bp_is_item_admin() )
		return false;
	$bp = buddypress();
	// If the edit form has been submitted, save the edited details.
	if ( isset( $_POST['save'] ) ) {
		// Checked against a whitelist for security.
		$allowed_post_security = apply_filters( 'groups_allowed_post_security', array( 'members', 'mods', 'admins' ) );
		$allowed_comment_security = apply_filters( 'groups_allowed_comment_security', array( 'members', 'mods', 'admins' ) );
		$prefix = "group-post-wall-security-";
		$prefix_comment = "group-comment-wall-security-";
		foreach ($allowed_post_security as $postsec) {
			$id = $prefix.$postsec;
			if (isset( $_POST[$id] ) && in_array( $_POST[$id], (array) $allowed_post_security )) {
				$post_security[] =  $_POST[$id];
			}
		}
		foreach ($allowed_comment_security as $commentsec) {
			$id = $prefix_comment.$commentsec;
			if (isset( $_POST[$id] ) && in_array( $_POST[$id], (array) $allowed_comment_security )) {
				$comment_security[] =  $_POST[$id];
			}
		}
		// Check the nonce.
		if ( !check_admin_referer( 'groups_edit_group_security') )
			return false;

		if ( !bp_wall_groups_edit_group_security( $_POST['group-id'], $post_security ) ) {
			bp_core_add_message( __( 'There was an error updating group security settings. Please try again.', 'buddypress' ), 'error' );
		} else {
			bp_core_add_message( __( 'Group security settings were successfully updated.', 'buddypress' ) );
		}
		
		if ( !bp_wall_groups_edit_group_comment_security( $_POST['group-id'], $comment_security ) ) {
			bp_core_add_message( __( 'There was an error updating group comment security settings. Please try again.', 'buddypress' ), 'error' );
		} else {
			bp_core_add_message( __( 'Group comment security settings were successfully updated.', 'buddypress' ) );
		}

		/**
		 * Fires before the redirect if a group settings has been edited and saved.
		 *
		 * @since 0.9.8.2
		 *
		 * @param int $id ID of the group that was edited.
		 */
		do_action( 'bp_wall_groups_group_security_settings_edited', $bp->groups->current_group->id );

		bp_core_redirect( bp_get_group_permalink( groups_get_current_group() ) . 'admin/group-security/' );
	}

	/**
	 * Fires before the loading of the group admin/group-settings page template.
	 *
	 * @since 0.9.8.2
	 *
	 * @param int $id ID of the group that is being displayed.
	 */
	do_action( 'bp_wall_groups_screen_group_admin_post_security', $bp->groups->current_group->id );

	/**
	 * Filters the template to load for a group's admin/group-settings page.
	 *
	 * @since 0.9.8.2
	 *
	 * @param string $value Path to a group's admin/group-settings template.
	 */
	bp_core_load_template( apply_filters( 'bp_wall_groups_template_group_admin_settings', 'groups/single/home-wall' ) );
}
add_action( 'bp_screens', 'bp_wall_groups_screen_group_admin_post_security' );


/**
 * Handle the display of a member's wall security page.
 *
 * @since 0.9.8.3
 */

function bp_wall_screen_user_security() {
	if ( ! ( bp_is_item_admin() && bp_is_user_settings() ) )
		return false;
	
	$bp = buddypress();
	// If the edit form has been submitted, save the edited details.
	
	if ( isset( $_POST['submit'] ) ) {
		// Checked against a whitelist for security.
		$allowed_wall_security = apply_filters( 'members_allowed_post_security', array( 'everyone', 'friends', 'fof' ) );
		$allowed_wall_comment_security = apply_filters( 'members_allowed_comment_security', array( 'everyone', 'friends', 'fof' ) );
		
		
		$prefix = "member-post-wall-security-";
		$prefix_comment = "member-comment-wall-security-";
		foreach ($allowed_wall_security as $postsec) {
			$id = $prefix.$postsec;
			if (isset( $_POST[$id] ) && in_array( $_POST[$id], (array) $allowed_wall_security )) {
				$wall_security[] =  $_POST[$id];
			}
		}
		foreach ($allowed_wall_comment_security as $commentsec) {
			$id = $prefix_comment.$commentsec;
			if (isset( $_POST[$id] ) && in_array( $_POST[$id], (array) $allowed_wall_comment_security )) {
				$comment_security[] =  $_POST[$id];
			}
		}
		// Check the nonce.
		if ( !check_admin_referer( 'bp_wall_members_settings_security') )
			return false;

		$user = $bp->displayed_user->id;

		if ( !bp_wall_member_edit_security( $user, $wall_security ) ) {
			bp_core_add_message( __( 'There was an error updating wall security settings. Please try again.', 'buddypress' ), 'error' );
		} else {
			bp_core_add_message( __( 'Wall security settings were successfully updated.', 'buddypress' ) );
		}
		
		if ( !bp_wall_member_edit_comment_security( $user, $comment_security ) ) {
			bp_core_add_message( __( 'There was an error updating wall comments security settings. Please try again.', 'buddypress' ), 'error' );
		} else {
			bp_core_add_message( __( 'Wall comments security settings were successfully updated.', 'buddypress' ) );
		}

		/**
		 * Fires before the redirect if a wall security has been edited and saved.
		 *
		 * @since 0.9.8.3
		 *
		 * @param int $id ID of the group that was edited.
		 */
		do_action( 'bp_wall_members_security_settings_edited', $user);

		bp_core_redirect( bp_member_permalink() . 'security/' );
	}

	/**
	 * Fires before the loading of the user wall security page template.
	 *
	 * @since 0.9.8.3
	 *
	 * @param int $id ID of the user that is being displayed.
	 */
	do_action( 'bp_wall_screen_wall_security', $user );

	/**
	 * Filters the template to load for user wall page template
	 *
	 * @since 0.9.8.3
	 *
	 * @param string $value Path to a user wall  page template.
	 */
	bp_core_load_template( apply_filters( 'bp_wall_template_user_security', 'members/single/home' ) );
}
add_action( 'bp_screens', 'bp_wall_screen_user_security' );
