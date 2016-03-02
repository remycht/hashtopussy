<?php
/**
 * Handles the login sessions
 * 
 * @author Sein
 */
class Login {
	private $user = null;
	private $valid = false;
	private $session = null;
	
	/**
	 * @var int Defines the time a login is valid without any action
	 */
	public static $loginSessionLimit = 600;
	
	/**
	 * Creates a Login-Instance and checks automatically if there is a session
	 * running. It updates the session lifetime again up to the session limit.
	 */
	public function __construct(){
		$this->user = null;
		$this->session = null;
		$this->valid = false;
		if(isset($_COOKIE['session'])){
			$session = $_COOKIE['session'];
			$sF = new SessionFactory();
			$filter1 = new QueryFilter("sessionKey", $session, "=");
			$filter2 = new QueryFilter("isOpen", "1", "=");
			$filter3 = new QueryFilter("lastAction", time() - self::$loginSessionLimit, ">");
			$check = $sF->filter(array('filter' => array($filter1, $filter2, $filter3)));
			if($check === null || sizeof($check) == 0){
				setcookie("session", "", time() - self::$loginSessionLimit); //delete invalid or old cookie
				return;
			}
			$s = $check[0];
			$uF = new UserFactory();
			$this->user = $uF->get($s->getUserId());
			if($this->user !== null){
				$this->valid = true;
				$this->session = $s;
				$s->setLastAction(time());
				$sF->update($s);
				setcookie("session", $s->getSessionKey(), time() + self::$loginSessionLimit);
			}
		}
	}
	
	/**
	 * Returns true if the user currently is loggedin with a valid session
	 */
	public function isLoggedin(){
		return $this->valid;
	}
	
	/**
	 * Logs the current user out and closes his session
	 */
	public function logout(){
		$sF = new SessionFactory();
		$this->session->setIsOpen(0);
		$sF->update($this->session);
		setcookie("session", "", time() - self::$loginSessionLimit);
	}
	
	/**
	 * Returns the uID of the currently logged in user, if the user is not logged
	 * in, the uID will be -1
	 */
	public function getUserID(){
		return $this->user->getId();
	}
	
	public function getUser(){
		return $this->user;
	}
	
	public function isAdministrator(){
		if(!$this->valid){
			return false;
		}
		else if($this->user->getIsAdmin() == '1'){
			return true;
		}
		return false;
	}
	
	/**
	 * Executes a login with given username and password (plain)
	 * 
	 * @param string $user username of the user to be logged in
	 * @param string $password password which was entered on login form
	 * @return true on success and false on failure
	 */
	public function login($email, $password){
		if($this->valid == true){
			return false;
		}
		$uF = new UserFactory();
		$filter = new QueryFilter("email", $email, "=");
		$check = $uF->filter(array('filter' => array($filter)));
		if($check === null || sizeof($check) == 0){
			return false;
		}
		$user = $check[0];
		if($user->getIsValid() != 1){
			return false;
		}
		else if($user->getIsConfirmed() != 1){
			return false;
		}
		else if(!Encryption::passwordVerify($user->getEmail(), $password, $user->getPasswordSalt(), $user->getPasswordHash())){
			return false;
		}
		$this->user = $user;
		$startTime = time();
		$s = new Session(0, $user->getId(), 1, "", $startTime, $startTime);
		$sF = new SessionFactory();
		$s = $sF->save($s);
		if($s === null){
			return false;
		}
		$sessionKey = Encryption::sessionHash($s->getId(), $startTime, $user->getEmail());
		$s->setSessionKey($sessionKey);
		$sF->update($s);
		
		$this->valid = true;
		setcookie("session", "$sessionKey", time() + self::$loginSessionLimit);
		return true;
	}
}



