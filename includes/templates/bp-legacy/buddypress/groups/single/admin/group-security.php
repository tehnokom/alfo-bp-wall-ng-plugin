<?php
/**
 * BuddyPress - Groups Admin - Group Settings
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>
<div class="item-list-tabs no-ajax" id="subnav" aria-label="<?php esc_attr_e( 'Group secondary navigation', 'buddypress' ); ?>" role="navigation">
	<ul>
		<?php bp_group_admin_tabs(); ?>
	</ul>
</div><!-- .item-list-tabs -->

<? do_action( 'bp_before_group_admin_security_form' ); ?>

<form action="<?php bp_group_admin_form_action(); ?>" name="group-settings-security-form" id="group-settings-security-form" class="standard-form" method="post" enctype="multipart/form-data">

	<?php
	/**
	 * Fires inside the group admin form and before the content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_before_group_admin_content' ); ?>

<h2 class="bp-screen-reader-text"><?php _e( 'Manage Group Security', 'buddypress' ); ?></h2>

<?php

/**
 * Fires before the group settings admin display.
 *
 * @since 1.1.0
 */
do_action( 'bp_wall_before_group_settings_admin' ); ?>


<fieldset class="group-post-security">

	<h4><legend><?php _e( 'Group Post Security', 'buddypress' ); ?></legend></h4>

	<p><?php _e( 'Who can post to group wall?', 'buddypress' ); ?></p>

	<div class="checkbox">

		<label for="group-post-wall-security-members"><input type="checkbox" name="group-post-wall-security-members" id="group-post-wall-security-members" value="members"<?php bp_wall_group_show_post_security_setting( 'members' ); ?> /> <?php _e( 'All group members', 'buddypress' ); ?></label>

		<label for="group-post-wall-security-mods"><input type="checkbox" name="group-post-wall-security-mods" id="group-post-wall-security-mods" value="mods"<?php bp_wall_group_show_post_security_setting( 'mods' ); ?> /> <?php _e( 'Group admins and mods only', 'buddypress' ); ?></label>

		<label for="group-post-wall-security-admins"><input type="checkbox" name="group-post-wall-security-admins" id="group-post-wall-security-admins" value="admins"<?php bp_wall_group_show_post_security_setting( 'admins' ); ?> /> <?php _e( 'Group admins only', 'buddypress' ); ?></label>
	</div>

</fieldset>

<?php do_action( 'bp_wall_before_group_comment_settings_admin' ); ?>


<fieldset class="group-comment-security">

	<h4><legend><?php _e( 'Group Comment Security', 'buddypress' ); ?></legend></h4>

	<p><?php _e( 'Who can comment group wall?', 'buddypress' ); ?></p>

	<div class="checkbox">

		<label for="group-comment-wall-security-members"><input type="checkbox" name="group-comment-wall-security-members" id="group-comment-wall-security-members" value="members"<?php bp_wall_group_show_comment_security_setting( 'members' ); ?> /> <?php _e( 'All group members', 'buddypress' ); ?></label>

		<label for="group-comment-wall-security-mods"><input type="checkbox" name="group-comment-wall-security-mods" id="group-comment-wall-security-mods" value="mods"<?php bp_wall_group_show_comment_security_setting( 'mods' ); ?> /> <?php _e( 'Group admins and mods only', 'buddypress' ); ?></label>

		<label for="group-comment-wall-security-admins"><input type="checkbox" name="group-comment-wall-security-admins" id="group-comment-wall-security-admins" value="admins"<?php bp_wall_group_show_comment_security_setting( 'admins' ); ?> /> <?php _e( 'Group admins only', 'buddypress' ); ?></label>
	</div>

</fieldset>


<?php

/**
 * Fires after the group settings admin display.
 *
 * @since 1.1.0
 */
do_action( 'bp_wall_after_group_settings_admin' ); ?>

<p><input type="submit" value="<?php esc_attr_e( 'Save Changes', 'buddypress' ); ?>" id="save" name="save" /></p>
<?php wp_nonce_field( 'groups_edit_group_security' ); ?>
    <input type="hidden" name="group-id" id="group-id" value="<?php bp_group_id(); ?>" />

