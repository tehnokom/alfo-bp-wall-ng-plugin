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
function bp_wall_screen_user_security() {
	do_action( 'bp_wall_screen_user_security' );
	bp_core_load_template( apply_filters( 'bp_wall_template_user_security', 'members/single/security' ) );
}

/**
 * filter the activity home, member home and group home and replace them
 * with the news templates 
 *
 */
function bp_wall_load_template_filter( $found_template, $templates ) {
	global $bp, $bp_deactivated;
	if ( !bp_wall_is_bp_default() || 
           #!bp_is_current_component( 'activity' ) &&
           !( bp_is_current_component( 'activity' ) || (bp_is_current_component( 'groups' ))) &&
           ( !bp_is_group_home() || !bp_is_active( 'activity' ) ) ) {
        return $found_template; 
    }
	#echo "bp_wall_load_template_filter :".print_r($found_template,1).":". print_r($templates,1);

	$templates_dir = "/templates/bp-default/";
    
	//if( bp_is_user_profile() && )
	//Only filter the template location when we're on the follow component pages.

	//if (bp_wall_is_bp_default()) {
    /*
	if ( $templates[0] == "members/single/home.php") {
		$found_template = dirname( __FILE__ ) . $templates_dir . 'members/single/home-wall.php';
		return $found_template;
	}elseif ( $templates[0] == "activity/index.php") {
		$found_template = dirname( __FILE__ ) . $templates_dir . 'activity/index-wall.php';
		return $found_template;
	} elseif ( $templates[0] == "groups/single/home.php" )	{
		$found_template = dirname( __FILE__ ) . $templates_dir . 'groups/single/home-wall.php';
		return $found_template;
	}
     */
	
	if ( $templates[0] == "members/single/home.php" ) {
        $template = 'members/single/home-wall.php';
        if ( file_exists( STYLESHEETPATH . '/' . $template ) )
            $found_template = STYLESHEETPATH . '/' . $template;
        else
            $found_template = dirname( __FILE__ ) . $templates_dir . $template;

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

    }elseif ( $templates[0] == "members/single/security.php" ) {
        $template = 'members/single/security.php';
        if ( file_exists( STYLESHEETPATH . '/' . $template ) )
            $found_template = STYLESHEETPATH . '/' . $template;
        else
            $found_template = dirname( __FILE__ ) . $templates_dir . $template;

        return $found_template;
    }elseif ( $templates[0] == "groups/single/admin/group-security.php" ) {
        $template = 'groups/single/admin/group-security.php';
        if ( file_exists( STYLESHEETPATH . '/' . $template ) )
            $found_template = STYLESHEETPATH . '/' . $template;
        else
            $found_template = dirname( __FILE__ ) . $templates_dir . $template;

        return $found_template;
    }

	foreach ( (array) $templates as $template ) {
		
		if ( file_exists( STYLESHEETPATH . '/' . $template ) )
			$filtered_templates[] = STYLESHEETPATH . '/' . $template;
		else
			$filtered_templates[] = dirname( __FILE__ ) . $templates_dir . $template;
	}

	$found_template = $filtered_templates[0];
	
	return apply_filters( 'bp_wall_load_template_filter', $found_template );
}
add_filter( 'bp_located_template', 'bp_wall_load_template_filter', 10, 2 );

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
        bp_get_template_part( $template );

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

    if(current_theme_supports('buddypress') || in_array( 'bp-default', array( get_stylesheet(), get_template() ) )  || ( defined( 'BP_VERSION' ) && version_compare( BP_VERSION, '1.7', '<' ) ))
   		return true;
   else {
	    // check to see if the 'buddypress' tag is in the theme 
	    // some bp themes are not yet compatible to bp 1.7 but the plugin is updated

   		// get current theme
	    $theme = wp_get_theme();
	    // get current theme's tags
	    $theme_tags = ! empty( $theme->tags ) ? $theme->tags : array();

	    // or if stylesheet is 'bp-default'
	    $backpat = in_array( 'buddypress', $theme_tags );    
	    if($backpat)
	    	return true;
   		else 
   			return false;
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
 * @since 1.0.0
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
 * @since 1.0.0
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
		/** This filter is documented in bp-groups/bp-groups-admin.php */
		$allowed_post_security = apply_filters( 'groups_allowed_post_security', array( 'members', 'mods', 'admins' ) );
		$prefix = "group-post-wall-security-";
		foreach ($allowed_post_security as $postsec) {
			$id = $prefix.$postsec;
			if (isset( $_POST[$id] ) && in_array( $_POST[$id], (array) $allowed_post_security )) {
				$post_security[] =  $_POST[$id];
			}
		}
		// Check the nonce.
		if ( !check_admin_referer( 'groups_edit_group_security') )
			return false;
		#echo "<pre>",print_r(array($_POST,$allowed_post_security,$post_security),1);
		#exit;


		if ( !bp_wall_groups_edit_group_security( $_POST['group-id'], $post_security ) ) {
			bp_core_add_message( __( 'There was an error updating group security settings. Please try again.', 'buddypress' ), 'error' );
		} else {
			bp_core_add_message( __( 'Group security settings were successfully updated.', 'buddypress' ) );
		}

		/**
		 * Fires before the redirect if a group settings has been edited and saved.
		 *
		 * @since 1.0.0
		 *
		 * @param int $id ID of the group that was edited.
		 */
		do_action( 'bp_wall_groups_group_security_settings_edited', $bp->groups->current_group->id );

		bp_core_redirect( bp_get_group_permalink( groups_get_current_group() ) . 'admin/group-security/' );
	}

	/**
	 * Fires before the loading of the group admin/group-settings page template.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id ID of the group that is being displayed.
	 */
	do_action( 'bp_wall_groups_screen_group_admin_post_security', $bp->groups->current_group->id );

	/**
	 * Filters the template to load for a group's admin/group-settings page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to a group's admin/group-settings template.
	 */
	bp_core_load_template( apply_filters( 'bp_wall_groups_template_group_admin_settings', 'groups/single/home-wall' ) );
}
add_action( 'bp_screens', 'bp_wall_groups_screen_group_admin_post_security' );



function bp_wall_settings_screen_security() {

	if ( bp_action_variables() ) {
		bp_do_404();
		return;
	}

	/**
	 * Filters the template file path to use for the notification settings screen.
	 *
	 * @since 1.6.0
	 *
	 * @param string $value Directory path to look in for the template file.
	 */
	#bp_core_load_template( apply_filters( 'bp_wall_settings_screen_security_settings', 'members/single/settings/security' ) );
	echo "OK";
}
