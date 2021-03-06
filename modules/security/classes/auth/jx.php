<?php


class Auth_Jx extends Auth_Jelly {


    /**
	 * Logs a user in.
	 *
	 * @param   string   username
	 * @param   string   password
	 * @param   boolean  enable auto-login
	 * @return  boolean
	 */
	public function _login($user, $password, $remember) {
		// Make sure we have a user object
        //Jx_Debug::dump($user,'User passed in');
		$user = $this->_get_object($user);

        //Jx_Debug::dump(null,'In _login');
        //Jx_Debug::dump($user->password,'User password after getting object');
        //Jx_Debug::dump($password,'Password passed in');
		// If the passwords match, perform a login
		if (Jx_Acl::check_for_cap('allow_login', $user) AND $user->password === $password) {
			if ($remember === TRUE) {
				// Create a new autologin token
				$token = Model::factory('user_token');

				// Set token data
				$token->user = $user->id;
				$token->expires = time() + $this->_config['lifetime'];

				$token->create();

				// Set the autologin Cookie
				Cookie::set('authautologin', $token->token, $this->_config['lifetime']);
			}

			// Finish the login
            //Jx_Debug::dump(null, 'finishing login');
			$this->complete_login($user);

			return TRUE;
		}

		// Login failed
		return FALSE;
	}

}
