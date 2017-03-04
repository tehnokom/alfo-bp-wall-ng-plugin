<?php
/**
 * BuddyPress - Members Settings Notifications
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/settings/profile.php */
?>
<h2 class="bp-screen-reader-text"><?php
	/* translators: accessibility text */
	_e( 'Wall security settings', 'buddypress' );
?></h2>

<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/security'; ?>" method="post" class="standard-form" id="settings-form">
	<?php

	/**
	 * Fires at the top of the member template notification settings form.
	 *
	 * @since 1.0.0
	 */
	do_action( 'bp_wall_members_settings_security' ); ?>

<fieldset class="wall-security">
	<h4><legend><?php _e( 'Wall Post Security', 'buddypress' ); ?></legend></h4>

	<p><?php _e( 'Who can post to member\'s wall?', 'buddypress' ); ?></p>

	<div class="checkbox">

		<label for="member-post-wall-security-friends"><input type="checkbox" name="member-post-wall-security-friends" id="member-post-wall-security-friends" value="friends"<?php bp_wall_member_show_wall_security_setting( 'friends' ); ?> /> <?php _e( 'Friends', 'buddypress' ); ?></label>

		<label for="member-post-wall-security-fof"><input type="checkbox" name="member-post-wall-security-fof" id="member-post-wall-security-fof" value="fof"<?php bp_wall_member_show_wall_security_setting( 'fof' ); ?> /> <?php _e( 'Friends of friends', 'buddypress' ); ?></label>

		<label for="member-post-wall-security-everyone"><input type="checkbox" name="member-post-wall-security-everyone" id="member-post-wall-security-everyone" value="everyone"<?php bp_wall_member_show_wall_security_setting( 'everyone' ); ?> /> <?php _e( 'Everyone', 'buddypress' ); ?></label>

	</div>

</fieldset>

	<?php do_action( 'bp_wall_members_settings_comment_security' ); ?>

<fieldset class="wall-comment-security">
	<h4><legend><?php _e( 'Wall Comment Security', 'buddypress' ); ?></legend></h4>

	<p><?php _e( 'Who can comment member\'s wall posts?', 'buddypress' ); ?></p>

	<div class="checkbox">

		<label for="member-comment-wall-security-friends"><input type="checkbox" name="member-comment-wall-security-friends" id="member-comment-wall-security-friends" value="friends"<?php bp_wall_member_show_wall_comment_security_setting( 'friends' ); ?> /> <?php _e( 'Friends', 'buddypress' ); ?></label>

		<label for="member-comment-wall-security-fof"><input type="checkbox" name="member-comment-wall-security-fof" id="member-comment-wall-security-fof" value="fof"<?php bp_wall_member_show_wall_comment_security_setting( 'fof' ); ?> /> <?php _e( 'Friends of friends', 'buddypress' ); ?></label>

		<label for="member-comment-wall-security-everyone"><input type="checkbox" name="member-comment-wall-security-everyone" id="member-comment-wall-security-everyone" value="everyone"<?php bp_wall_member_show_wall_comment_security_setting( 'everyone' ); ?> /> <?php _e( 'Everyone', 'buddypress' ); ?></label>

	</div>

</fieldset>
	<?php

	/**
	 * Fires before the display of the submit button for user notification saving.
	 *
	 * @since 1.5.0
	 */
	do_action( 'bp_wall_members_settings_security_before_submit' ); ?>

	<div class="submit">
		<input type="submit" name="submit" value="<?php esc_attr_e( 'Save Changes', 'buddypress' ); ?>" id="submit" class="auto" />
	</div>

	<?php

	/**
	 * Fires after the display of the submit button for user notification saving.
	 *
	 * @since 1.5.0
	 */
	do_action( 'bp_wall_members_settings_security_after_submit' ); ?>

	<?php wp_nonce_field('bp_wall_members_settings_security' ); ?>

</form>

<?php

/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/settings/profile.php */
do_action( 'bp_after_member_settings_template' ); ?>
