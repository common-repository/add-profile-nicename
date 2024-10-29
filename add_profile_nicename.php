<?php
/*
Plugin Name: Add Profile Nicename
Plugin URI: https://webone-sendai.co.jp/profile_nicename_update/
Description: edit nicename when user add
Version: 1.0.0
Author: WenOne
Author URI: https://webone-sendai.co.jp
License: GPLv2 or later
*/

add_action( 'show_user_profile', 'add_nicename_field' );
add_action( 'edit_user_profile', 'add_nicename_field' );
add_action( 'user_profile_update_errors', 'apn_display_errors', 10, 3 );

function add_nicename_field() {
    global $profileuser;

	?>
	<h2>User Infomation</h2>
	<table class="form-table">
	<tbody>
	<tr>
		<th><label for="user_nicename">Nicename</label></th>
		<td>
		<input type="text" name="user_nicename" value="<?php echo esc_attr( $profileuser->user_nicename ); ?>" />
                <p>The number of characters should be 10 or less.</p>
		</td>
	</tr>
	</tbody></table>
	<?php
}


function apn_display_errors( $errors, $update, $user ) {
	if ( ! $update ) {
		return;
	}

	if ( empty( $user->ID ) ) {
		return;
	}

	check_admin_referer( 'update-user_' . $user->ID );

	if ( isset( $_POST['user_nicename'] ) ) {
		$new_nicename = sanitize_user( wp_unslash( $_POST['user_nicename'] ), true );

		if ( empty( $new_nicename ) ) {
			return;
		}

		$exists = get_user_by( 'id', $user->ID )->user_nicename;
		if ( $new_nicename === $exists ) {
			return;
		}

		if ( mb_strlen( $new_nicename ) > 20 ) {
			$errors->add( 'user_nicename_too_long', '<strong>ERROR</strong>: You can enter up to 20 characters.' );
			return;
		}

		if ( get_user_by( 'slug', $new_nicename ) ) {
                        $message = __('<strong>ERROR</strong>: The author slug, %1$s, already exists.', 'edit-author-slug');
			$errors->add( 'user_nicename_exists', sprintf($message, '<strong><em>' . ($new_nicename) . '</em></strong>'));
			return;
		}

		$user->user_nicename = $new_nicename;
    }
}
?>