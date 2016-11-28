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
 * @since 1.5.0
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
 * @since 1.5.0
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
	 * @since 1.5.0
	 *
	 * @param string $post_security Membership level needed to send an invite.
	 * @param int    $group_id      ID of the group whose status is being checked.
	 */
	return apply_filters( 'bp_wall_group_get_post_security', $post_security, $group_id );
}

/**
 * Can a user post to a specified group?
 *
 * @since 1.5.0
 * @since 2.2.0 Added the $user_id parameter.
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
	 * @since 1.5.0
	 * @since 2.2.0 Added the $user_id parameter.
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
 * @since 1.1.0
 *
 * @return bool True if the current page is part of a single group's admin.
 */


function bp_is_group_admin_security_page() {
	return (bool) ( bp_is_single_item() && bp_is_groups_component() && bp_is_current_action( 'admin' ) && bp_is_action_variable( 'group-security', 0 ) );
}


/**
 * Edit the security for a group.
 *
 * @since 1.0.0
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
	 * @since 2.2.0
	 *
	 * @param int             $value          ID of the group.
	 * @param BP_Groups_Group $old_group      Group object, before being modified.
	 * @param bool            $notify_members Whether to send an email notification to members about the change.
	 */
	do_action( 'bp_wall_groups_security_updated', $group_id );

	return true;
}

