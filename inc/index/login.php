<?php

// enable if error reporting is on
if ($debug === true) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

//includes
spl_autoload_register(function ($class) {
	if ($class !== 'index') {
		if ($class !== 'index' && file_exists('lib/' . strtolower($class) . '.class.php')) {
			include 'lib/' . strtolower($class) . '.class.php';
		}
	}
});

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$submitted = filter_input(INPUT_POST, 'Submitted', FILTER_SANITIZE_STRING);

if (empty($_SESSION['authenticated']) && !empty($_COOKIE['rememberMe']) && !empty($_COOKIE['email'])) {
	$_SESSION['authenticated'] = $_COOKIE['email'];
	if (!empty($_SESSION['dest_url'])) {
		header('Location: ' . $_SESSION['dest_url']);
	} else {
		header('Location: /channels');
	}
}

if ($submitted === 'Login') {
	$user = new user();
	$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
	$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
	$status = $user->login($email, $password);
	if ($status === true) {
		header('Location: /channels');
	}
}

if ($submitted === 'Register') {
	if ($reg_open === true) {
		$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
		$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
		$displayname = filter_input(INPUT_POST, 'displayname', FILTER_SANITIZE_STRING);

		// verify all required info is present before executing register
		if (empty($email) || empty($password) || empty($displayname)) {
			$status = 'Please enter a valid email address, display name, and a password.';
		} else {
			$user = new user();
			$status = $user->register($email, $password, $displayname);
			if ($status === true) {
				$status = '<br />Account created.';
			}
		}
	} else {
		$status = '<br />Account creation currently disabled.<br /><br />Please contact issues@rirnef.net for details.';
	}
}

// check if we're trying to verify an account
// and if the URL has both required pieces of information
// Get Request URI and break into components
$request = trim(filter_input(INPUT_SERVER, 'REQUEST_URI'), '/');
$uriVars = explode('/', $request, 4);

if (!empty($uriVars[1])) {
	$vcheck = $uriVars[1];
	if ($vcheck === 'verify') {
		$vemail = $uriVars[2];
		$vcode = $uriVars[3];
		if (!empty($vemail) && !empty($vcode)) {
			$user = new user();
			$vstatus = $user->verify($vemail, $vcode);

			// let user know how it went
			if ($vstatus === 'true') {
				$status = 'Account verification successful! You may now log in.';
			} else {
				$status = $vstatus;
			}
		}
	}
}
?>
<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
	<div class="mdl-card mdl-shadow--2dp login-form" action="">
		<div class="mdl-card__title-login">

			<div class="mdl-tabs__tab-bar-login">
				<a href="#login" class="mdl-tabs__tab mdl-tabs__tab-half-width is-active">Log In</a>
				<a href="#register" class="mdl-tabs__tab mdl-tabs__tab-half-width">Register</a>
			</div>
			</div>



		<div class="mdl-tabs__panel is-active" id="login">
			<div class="mdl-card__supporting-text">
				<form action="" method="POST" class="form" id="loginForm">
					<div class="form__article">
						<div class="mdl-grid">
							<div class="mdl-cell mdl-cell--12-col mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
								<input class="mdl-textfield__input" type="email" name="email" id="emailAddress"/>
								<label class="mdl-textfield__label" for="emailAddress">Email Address</label>
							</div>						
                       	</div>

						<div class="mdl-grid">
							<div class="mdl-cell mdl-cell--12-col mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
								<input class="mdl-textfield__input" type="password" name="password" id="Password"/>
								<label class="mdl-textfield__label" for="Password">Password</label>
							</div>
						</div>




						<div class="form__action">
							<label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="rememberLogin">
								<input type="checkbox" name="rememberMe" value="true" id="rememberLogin" class="mdl-checkbox__input">
								<span class="mdl-checkbox__label">Remember Me</span>
							</label>
							<button type="submit" name="Submitted" value="Login" form="loginForm" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">
								Login
							</button>
						</div>
						<div class="mdl-grid">
							<div class="mdl-cell mdl-cell--12-col mdl-typography--text-center">
								<?php
								if (!empty($status)) {
									echo '<br />' . $status;
								}
								?>
							</div>
							<div class="mdl-cell mdl-cell--12-col forgot-pass">
								<span class="mdl-typography--text-left">Forgot your password? <a href="/lostpass">Click here</a></span>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="mdl-tabs__panel" id="register">
			<div class="mdl-card__supporting-text">
				<?php if ($reg_open === true) { ?>
					<form action="" method="POST" class="form" id="registerForm">
						<div class="form__article">

							<div class="mdl-grid">
								<div class="mdl-cell mdl-cell--12-col mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
									<input class="mdl-textfield__input" type="email" name="email" id="emailAddress"/>
									<label class="mdl-textfield__label" for="emailAddress">Email Address</label>
								</div>
							</div>

							<div class="mdl-grid">
								<div class="mdl-cell mdl-cell--12-col mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
									<input class="mdl-textfield__input" type="text" name="displayname" id="displayName"/>
									<label class="mdl-textfield__label" for="displayName">Display Name</label>
								</div>
							</div>

							<div class="mdl-grid">
								<div class="mdl-cell mdl-cell--12-col mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
									<input class="mdl-textfield__input" type="password" name="password" id="Password"/>
									<label class="mdl-textfield__label" for="Password">Password</label>
								</div>
							</div>

							<div class="form__action-register">
								<button type="submit" name="Submitted" value="Register" form="registerForm" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">
									Register
								</button>
							</div>

							<div class="mdl-grid">
								<div class="mdl-cell mdl-cell--12-col mdl-typography--text-center">
									<?php
									if (!empty($status)) {
										echo '<br />' . $status;
									}
									?>
								</div>
							</div>
						</div>
					</form>
				<?php } else { ?>

					<div class="mdl-grid">
						<span>Registration currently closed. <br /><br />Please contact <a href="mailto:<?= $reply_email ?>"><?= $reply_email ?></a> for more info.</span>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>