<?php
/**
 * Buddypress Wall Template Tags
 *
 * @package BP-Wall
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/*
add_action("bp_before_member_activity_post_form","bp_wall_before_member_activity_post_form");

function bp_wall_before_member_activity_post_form(){
	if ( is_user_logged_in() && !bp_current_action() || bp_is_current_action( 'just-me' ) )    
		locate_template( array( 'activity/post-form.php'), true );

}
 */
add_action("bp_after_group_settings_admin","bp_wall_group_settings_post_security");
function bp_wall_group_settings_post_security(){
?>

<fieldset class="group-post-security">

	<h4><legend><?php _e( 'Group Post Security', 'buddypress' ); ?></legend></h4>

	<p><?php _e( 'Who can post to group wall?', 'buddypress' ); ?></p>

	<div class="checkbox">

		<label for="group-post-wall-security-members"><input type="checkbox" name="group-post-wall-security-members" id="group-post-wall-security-members" value="members"<?php bp_wall_group_show_post_security_setting( 'members' ); ?> /> <?php _e( 'All group members', 'buddypress' ); ?></label>

		<label for="group-post-wall-security-mods"><input type="checkbox" name="group-post-wall-security-mods" id="group-post-wall-security-mods" value="mods"<?php bp_wall_group_show_post_security_setting( 'mods' ); ?> /> <?php _e( 'Group admins and mods only', 'buddypress' ); ?></label>

		<label for="group-post-wall-security-admins"><input type="checkbox" name="group-post-wall-security-admins" id="group-post-wall-security-admins" value="admins"<?php bp_wall_group_show_post_security_setting( 'admins' ); ?> /> <?php _e( 'Group admins only', 'buddypress' ); ?></label>

	</div>

</fieldset>

<?php
}
?>
