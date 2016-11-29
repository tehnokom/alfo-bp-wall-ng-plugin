<?php
/**
 * BP Wall Functions 
 *  
 * @package BP-Wall
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Add new message before comments how many people liked an item
 *
 */
function bp_wall_add_likes_comments() {
	$activity_id = (int) bp_get_activity_id();
	
	if ( !isset( $activity_id ) || $activity_id == 0 )
		return false;
	
	$count = (int)bp_activity_get_meta( $activity_id, 'favorite_count' );
	
	if ( $count == 0 )
		return false;

	$like_html = false;

	if ( $count == 1 )
		$like_html = sprintf( __( '<ul><li class="activity-like-count">%s person like this.</li></ul>', 'bp-wall' ), number_format_i18n( $count ) );
	elseif ( $count > 1 ) {
		$like_html = sprintf( __( '<ul><li class="activity-like-count">%s people likes this.</li></ul>', 'bp-wall' ), number_format_i18n( $count ) );
	}

	echo $like_html;
}

/**
 * Output the 'checked' value, if needed, for a given group_security on the group create/admin screens
 *
 * @since 0.9.8.2
 *
 * @param string      $setting The setting you want to check against ('members',
 *                             'mods', or 'admins').
 * @param object|bool $group   Optional. Group object. Default: current group in loop.
 */
function bp_wall_group_show_post_security_setting( $setting, $group = false ) {
	$group_id = isset( $group->id ) ? $group->id : false;

	$post_security = bp_wall_group_get_post_security( $group_id );

	if (is_array($post_security) && in_array($setting,$post_security) ) {
		echo ' checked="checked"';
	}
}

/**
 * Get the security setting of a group.
 *
 * This function can be used either in or out of the loop.
 *
 * @since 0.9.8.2
 *
 * @param int|bool $group_id Optional. The ID of the group whose status you want to
 *                           check. Default: the displayed group, or the current group
 *                           in the loop.
 * @return bool|string Returns false when no group can be found. Otherwise
 *                     returns the group invite status, from among 'members',
 *                     'mods', and 'admins'.
 */
function bp_wall_group_get_post_security( $group_id = false ) {
	global $groups_template;

	if ( !$group_id ) {
		$bp = buddypress();

		if ( isset( $bp->groups->current_group->id ) ) {
			// Default to the current group first.
			$group_id = $bp->groups->current_group->id;
		} elseif ( isset( $groups_template->group->id ) ) {
			// Then see if we're in the loop.
			$group_id = $groups_template->group->id;
		} else {
			return false;
		}
	}

	$post_security = groups_get_groupmeta( $group_id, 'post_security' );
	// Backward compatibility. When 'post_security' is not set, fall back to a default value.
	if ( !$post_security ) {
		$post_security = apply_filters( 'bp_wall_group_post_security_fallback', 'admins' );
	}
	 

	/**
	 * Filters the invite status of a group.
	 *
	 * Invite status in this case means who from the group can send invites.
	 *
	 * @since 0.9.8.2
	 *
	 * @param string $post_security Membership level needed to send an invite.
	 * @param int    $group_id      ID of the group whose status is being checked.
	 */
	return apply_filters( 'bp_wall_group_get_post_security', $post_security, $group_id );
}

/**
 * Can a user post to a specified group?
 *
 * @since 0.9.8.2
 *
 * @param int $group_id The group ID to check.
 * @param int $user_id  The user ID to check.
 * @return bool
 */
function bp_wall_groups_user_can_post( $group_id = 0, $user_id = 0 ) {
	$can_post = false;
	$post_security    = false;

	// If $user_id isn't specified, we check against the logged-in user.
	if ( ! $user_id ) {
		$user_id = bp_loggedin_user_id();
	}

	// If $group_id isn't specified, use existing one if available.
	if ( ! $group_id ) {
		$group_id = bp_get_current_group_id();
	}

	if ( $user_id ) {
			$post_security = bp_wall_group_get_post_security( $group_id );

			if (is_array($post_security)) {
				foreach ( $post_security as $sec ) { 
					switch ( $sec ) {
					case 'admins' :
						if ( groups_is_user_admin( $user_id, $group_id ) ) {
							$can_post = true;
						}
						break;

					case 'mods' :
						if ( groups_is_user_mod( $user_id, $group_id ) || groups_is_user_admin( $user_id, $group_id ) ) {
							$can_post = true;
						}
						break;

					case 'members' :
						if ( groups_is_user_member( $user_id, $group_id ) ) {
							$can_post = true;
						}
						break;
					}
				}
			}
	}

	/**
	 * Filters whether a user can post to a group.
	 *
	 * @since 0.9.8.2
	 *
	 * @param bool $can_post         Whether the user can post to group wall
	 * @param int  $group_id         The group ID being checked
	 * @param bool $post_security    The group's current post security
	 * @param int  $user_id          The user ID being checked
	 */
	return apply_filters( 'bp_wall_groups_user_can_post', $can_post, $group_id, $post_security, $user_id );
}

/**
 * Is the current page is group's admin security screen?
 *
 * Eg http://example.com/groups/mygroup/admin/group-security/.
 *
 * @since 0.9.8.2
 *
 * @return bool True if the current page is part of a single group's admin.
 */


function bp_is_group_admin_security_page() {
	return (bool) ( bp_is_single_item() && bp_is_groups_component() && bp_is_current_action( 'admin' ) && bp_is_action_variable( 'group-security', 0 ) );
}


/**
 * Edit the security for a group.
 *
 * @since 0.9.8.2
 *
 * @param int    $group_id       ID of the group.
 * @param string $post_security  Group post security settings.
 * @return bool True on success, false on failure.
 */
function bp_wall_groups_edit_group_security( $group_id, $post_security ) {

	if ( empty( $post_security ) ) {
		groups_delete_groupmeta($group_id, 'post_security');
		return true;
	}
	
	if (!groups_get_groupmeta( $group_id, 'post_security' )) {
		return groups_add_groupmeta( $group_id, 'post_security', $post_security, false);
	} else {
		if (groups_delete_groupmeta($group_id, 'post_security')) {
			return groups_add_groupmeta( $group_id, 'post_security', $post_security, false);
		} else {
			return false;
		}
	}

	/**
	 * Fired after a group's details are updated.
	 *
	 * @since 0.9.8.2
	 *
	 * @param int             $group_id          ID of the group.
	 */
	do_action( 'bp_wall_groups_security_updated', $group_id );

	return true;
}

/**
 * Is the current page is user's security screen?
 *
 * Eg http://example.com/users/username/settings/security/.
 *
 * @since 0.9.8.3
 *
 * @return bool True if the current page is part of a single group's admin.
 */

function bp_is_user_settings_security() {
	return (bool) ( bp_is_user() && bp_is_settings_component() && bp_is_current_action( 'security' ) );
}

/**
 * Output the 'checked' value, if needed, for a given member_security on the wall security screen
 *
 * @since 0.9.8.3
 *
 * @param string      $setting The setting you want to check against ('friends',
 *                             'fof' (friends of friends), or 'everyone').
 */
function bp_wall_member_show_wall_security_setting( $setting ) {
	$user_id = isset( $user->id ) ? $user->id : false;

	$wall_security = bp_wall_member_get_wall_security( $user_id );

	if (is_array($wall_security) && in_array($setting,$wall_security) ) {
		echo ' checked="checked"';
	}
}

/**
 * Get the security setting of a members wall.
 *
 * This function can be used either in or out of the loop.
 *
 * @since 0.9.8.3
 *
 * @param int|bool $user_id Optional. The ID of the user whose status you want to
 *                           check. Default: the displayed group, or the current group
 *                           in the loop.
 * @return bool|string Returns false when no group can be found. Otherwise
 *                     returns the group invite status, from among 'members',
 *                     'mods', and 'admins'.
 */
function bp_wall_member_get_wall_security( $user_id = false ) {

	if ( !$user_id ) {
		$bp = buddypress();
		$user_id = $bp->displayed_user->id;
	}

	$wall_security = get_user_meta( $user_id, 'wall_security', true );

	// Backward compatibility. When 'post_security' is not set, fall back to a default value.
	if ( !$wall_security ) {
		$wall_security = apply_filters( 'bp_wall_member_post_security_fallback', array('') );
	}
	 

	/**
	 * Filters the wall security.
	 *
	 * @since 0.9.8.3
	 *
	 * @param string $wall_security Membership level needed to post to a wall.
	 * @param int    $user_id      ID of the user whose status is being checked.
	 */
	return apply_filters( 'bp_wall_member_get_wall_security', $wall_security, $user_id );
}

/**
 * Can a user post to a other_user wall?
 *
 * @since 0.9.8.3
 *
 * @param int $user_id  The user ID to check.
 * @param int $other_user_id The other user ID to check.
 * @return bool
 */
function bp_wall_user_can_post( $user_id = 0, $other_user_id = 0 ) {
	global $bp,$bp_wall;
	$can_post = false;
	$wall_security    = false;

	// If $user_id isn't specified, we check against the logged-in user.
	if ( ! $user_id ) {
		$user_id = bp_loggedin_user_id();
	}

	// If $other_user_id isn't specified, use existing one if available.
	if ( ! $other_user_id ) {
		$other_user_id = $bp->displayed_user->id;
	}

	if ( $other_user_id ) {
		if ($user_id == $other_user_id) {
			// User can always post to it's wall
			$can_post = true;
		} else {
			$wall_security = bp_wall_member_get_wall_security( $other_user_id );

			if (is_array($wall_security)) {
				foreach ( $wall_security as $sec ) { 
					switch ( $sec ) {
					case 'everyone' :
						if ( is_user_logged_in() ) {
							$can_post = true;
						}
						break;

					case 'friends' :
						if (  $bp_wall->is_myfriend( $other_user_id ) ) {
							$can_post = true;
						}
						break;

					case 'fof' :
						if ( $bp_wall->is_fof( $other_user_id ) ) {
							$can_post = true;
						}
						break;
					}
				}
			}
		}
	}

	/**
	 * Filters whether a user can post to a user's wall.
	 *
	 * @since 0.9.8.3
	 *
	 * @param bool $can_post         Whether the user can post to group wall
	 * @param int  $user_id         The user ID being checked
	 * @param bool $wall_security    The current wall security
	 * @param int  $other_user_id          The other_user ID being checked
	 */
	return apply_filters( 'bp_wall_groups_user_can_post', $can_post, $user_id, $wall_security, $other_user_id );
}


/**
 * Edit the security for a wall.
 *
 * @since 0.9.8.3
 *
 * @param int    $user_id       ID of the user.
 * @param string $wall_security  Wall post security settings.
 * @return bool True on success, false on failure.
 */
function bp_wall_member_edit_security( $user_id, $wall_security ) {

	if ( empty( $wall_security ) ) {
		delete_user_meta($user_id, 'wall_security');
		return true;
	}
	
	if (!get_user_meta( $user_id, 'wall_security', true )) {
		return add_user_meta( $user_id, 'wall_security', $wall_security, false);
	} else {
		if (delete_user_meta($user_id, 'wall_security')) {
			return add_user_meta( $user_id, 'wall_security', $wall_security, false);
		} else {
			return false;
		}
	}

	/**
	 * Fired after a group's details are updated.
	 *
	 * @since 0.9.8.3
	 *
	 * @param int             $user_id          ID of the user.
	 */
	do_action( 'bp_wall_security_updated', $user_id );

	return true;
}

