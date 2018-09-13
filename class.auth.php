<?php
// Prevent direct access to the plugin
if ( !defined( 'ABSPATH' ) ) exit( 'Good try! :)' );

// Class for Initiating the authentication process
class Surbma_Secure_Login {

	// Construct Initiate functions
	public function __construct() {
		add_filter( 'authenticate', array( $this, 'wpmailauth_authenticate' ), 30, 3 );
	}

	/**
	 * @param string $user_token The verification code
	 * @param int    $user   The user OBJECT
	 *
	 * @return boolean
	 */
	public function wpmail_auth_sendmail( $user_token, $user ) {
		delete_user_meta( $user->ID, 'wpmailauth_token' );
		add_user_meta( $user->ID, 'wpmailauth_token', $user_token, true );

		$userdata   = get_userdata( $user->ID );
		$user_name  = $userdata->user_login;
		$user_email = $userdata->user_email;

		$auth_url = home_url() . '/wp-login.php?user_id=' . $user->ID . '&wpmailauth_token=' . $user_token;
		$message = '<html><body>';
		$message .= 'Hi ' . $user_name . ', <br />Your authorization token code is: <strong>' . $user_token . '</strong><br />You can alternatively use the following url to login: <br />' . $auth_url;
		$message .= '</body></html>';
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

		wp_mail( $user_email, 'Your secure token to login', $message, $headers );
	}

	/**
	 * Render the second step in the authentication.
	 *
	 * @param int      $id     The user ID
	 * @param WP_Error $error  Any notices/errors
	 *
	 * @return void
	 */
	public function wpmailauth_render_login( $id, $user_token, $error = null ) {
		global $wpdb;
		login_header( __('Log In - Verify Your Login'), '', $error );
		$url = esc_url( site_url( 'wp-login.php', 'login_post' ) );
		include_once( 'includes/template.php' );
		login_footer();
		die(0);
	}

	/**
	 * @param WP_User $user The user object
	 *
	 * @return $user
	 */
	public function wpmailauth_authenticate( $user, $username, $password ) {
		if ( $user instanceof WP_User ) {
			$user_token = wp_generate_password(6);

			$this->wpmail_auth_sendmail( $user_token, $user );
			$id = $user->ID;
			$user = $this->wpmailauth_render_login( $id, $user_token );
		} else {
			$userObject = isset( $_GET['user_id'] ) ? get_user_by( 'id', $_GET['user_id'] ) : false;
			$token = isset( $_GET['wpmailauth_token'] ) ? $_GET['wpmailauth_token'] : false;

			if ( $userObject !== false && $token !== false ) {
				if ( $token !== get_user_meta( $userObject->ID, 'wpmailauth_token', true ) ) {
					$error = new WP_Error;
					$error->add( 'wpmailauth', __( 'The pin you entered was invalid.' ) );
					$id = $userObject->ID;
					$user = $this->wpmailauth_render_login( $id, $user_token, $error );
				} else {
					delete_user_meta( $userObject->ID, 'wpmailauth_token' );
					$user = $userObject;
				}
			}
		}
		return $user;
	}

}

new Surbma_Secure_Login();
