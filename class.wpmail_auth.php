<?php
// A File-nak közvetlen hozzáférés tiltása
if (!function_exists('add_action')) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

/*===============================================================
=    Class for Initiating the authentication process           	=
=================================================================*/
if (!class_exists('WPMAILAUTH')):

class WPMAILAUTH {

/**
 * construct Initiate functions 
 */
public function __construct(){
	add_filter('authenticate', array($this, 'wpmailauth_authenticate'), 21);	
}
/**
 * @param string $user_token The verification code
 * @param int    $user   The user OBJECT
 *
 * @return boolean
 */
public function wpmail_auth_sendmail($user_token, $user, $user_serialized_code) {
	if (is_object($user)) {
		if (!add_user_meta($user->ID, 'wpmailauth_token', $user_token, true)) {
			$userdata   = get_userdata($user->ID);
			$user_email = $userdata->user_email;

			update_user_meta($user->ID, 'wpmailauth_token', $user_token);
			/**************************************************************************************************/
			$userdata   = get_userdata($user->ID);
			$user_name  = $userdata->user_login;
			$user_email = $userdata->user_email;

			$auth_url = home_url().'/wp-login.php?user_id='.$user->ID.'&wpmailauth_token='.$user_token;
			$message  = 'Hi'.$user_name.', <br />Your authorization token code is: <strong>'.$user_token.'</strong><br />You can alternatively use the following url to login: <br />'.$auth_url;
			wp_mail($user_email, 'Your new password and authorization token', $message);
			/**************************************************************************************************/
			return true;
		}
		if (!add_user_meta($user->ID, 'wpmailauth_url_serialized', $user_serialized_code, true)) {
			update_user_meta($user->ID, 'wpmailauth_url_serialized', $user_serialized_code);
			return true;
		}
	} else {
		if (!add_user_meta($user, 'wpmailauth_token', $user_token, true)) {
			update_user_meta($user, 'wpmailauth_token', $user_token);
			/**************************************************************************************************/
			global $wpdb;
			$userdata   = get_userdata($user);
			$user_name  = $userdata->user_login;
			$user_email = $userdata->user_email;

			$auth_url = home_url().'/wp-login.php?user_id='.$user.'&wpmailauth_token='.$user_token;
			$message  = 'Hi'.$user_name.', <br />Your authorization token code is: <strong>'.$user_token.'</strong><br />You can alternatively use the following url to login: <br />'.$auth_url;
			wp_mail($user_email, 'Your new password and authorization token', $message);
			/**************************************************************************************************/
			return true;
		}
		if (!add_user_meta($user->ID, 'wpmailauth_url_serialized', $user_serialized_code, true)) {
			update_user_meta($user->ID, 'wpmailauth_url_serialized', $user_serialized_code);
			return true;
		}
		return true;
	}
}
/**
 * Render the second step in the authentication.
 *
 * @param int      $id     The user ID
 * @param WP_Error $error  Any notices/errors
 *
 * @return void
 */
public function wpmailauth_render_login($id, $user_token, $error = null) {
	global $wpdb;
	login_header(__('Log In - Verify Your Login'), '', $error);
	$url = esc_url(site_url('wp-login.php', 'login_post'));
	include_once('includes/template.php');
	login_footer();
	die(0);
	}
	/**
	 * @param WP_User $user The user object
	 *
	 * @return $user
	 */
	public function wpmailauth_authenticate($user) {

		if ($user instanceof WP_User) {
			$user_token           = wp_generate_password(6);
			$user_serialized_code = urlencode(wp_generate_password(15));

			if ($user_token !== get_user_meta($user->ID, 'wpmailauth_token', true)) {
				if ($this->wpmail_auth_sendmail($user_token, $user->ID, $user_serialized_code)) {
					$error = new WP_Error;
					$error->add('wpmailauth', __('Verification token: '), 'message');

					return $this->wpmailauth_render_login($user->ID, $user_token, $error);
				}
			}
			return $user;
		}

		$user  = isset($_GET['user_id'])?get_user_by('id', $_GET['user_id']):null;
		$token = isset($_GET['wpmailauth_token'])?$_GET['wpmailauth_token']:false;

		if ($user && $token !== false) {

			if ($token !== get_user_meta($user->ID, 'wpmailauth_token', true)) {
				$error = new WP_Error;
				$error->add('wpmailauth', __('The pin you entered was invalid.'));
				if(isset($user_token)){
					return $this->wpmailauth_render_login($user->ID, $user_token, $error);
				}
			}
		}
		return $user;
	}	
}

endif;

function LOADWPMAILAUTH() {return new WPMAILAUTH();}
LOADWPMAILAUTH();