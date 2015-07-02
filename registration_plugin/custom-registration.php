
<?php

/*
  Plugin Name: Custom Registration
  Plugin URI: http://code.tutsplus.com
  Description: Updates user rating based on number of posts.
  Version: 1.0
  Author: Agbonghama Collins
  Author URI: http://tech4sky.com
 */

function custom_registration_function() {
    if (isset($_POST['submit'])) {
        registration_validation(
            $_POST['username'],
            $_POST['password'],
            $_POST['email'],
            $_POST['address_1'],
            $_POST['fname'],
            $_POST['lname'],
            $_POST['city']

        );

        // sanitize user form input
        global $username, $password, $email, $address, $first_name, $last_name, $city;
        $username	= 	sanitize_user($_POST['username']);
        $password 	= 	esc_attr($_POST['password']);
        $email 		= 	sanitize_email($_POST['email']);

        $address 	= 	esc_url($_POST['address_1']);
        $first_name = 	sanitize_text_field($_POST['fname']);
        $last_name 	= 	sanitize_text_field($_POST['lname']);
        $city 	= 	sanitize_text_field($_POST['city']);


        // call @function complete_registration to create the user
        // only when no WP_error is found
        complete_registration(
            $username,
            $password,
            $email,
            $address,
            $first_name,
            $last_name,
            $city

        );
    }

    registration_form(
        $username,
        $password,
        $email,
        $address,
        $first_name,
        $last_name,
        $city

    );
}

function registration_form( $username, $password, $email, $address, $first_name, $last_name, $city ) {
    echo '
    <style>
    @import url(http://meyerweb.com/eric/tools/css/reset/reset.css);
    .error-container{
     background-color: #f95252;
        -webkit-border-radius: 5px 5px 5px 5px;
        -moz-border-radius: 5px 5px 5px 5px;
        border-radius: 5px 5px 5px 5px;
    }
    .register-msg{
        width:100%;
        padding-bottom:5px;
        padding-left:10px !important;
        color:#fff;

    }
    .register-msg:first-child{
    padding-top:5px;

    }
    .entry-header h1 {
        background-color: #f95252;
        -webkit-border-radius: 20px 20px 0 0;
        -moz-border-radius: 20px 20px 0 0;
        border-radius: 20px 20px 0 0;
        color: #fff;
        font-size: 28px;
        padding: 20px 26px;
        margin-bottom:0 !important;
    }
    .entry-header h1:before {
        font-family: "dashicons";
       content: "\f337";
        font-size:30px;
        vertical-align: middle;
        margin-right:5px;
    }
    .register-container{
    background-color: #fff;
        -webkit-border-radius: 0 0 20px 20px;
        -moz-border-radius: 0 0 20px 20px;
        border-radius: 0 0 20px 20px;
        padding: 20px 26px;
    }


	input{
		margin-bottom:4px;
	}
	</style>
	';

    echo '
    <form class="register-container" action="' . $_SERVER['REQUEST_URI'] . '" method="post">
	<div>
	<label for="username">Username <strong>*</strong></label>
	<input type="text" name="username" value="' . (isset($_POST['username']) ? $username : null) . '">
	</div>

	<div>
	<label for="firstname">First Name</label>
	<input type="text" name="fname" value="' . (isset($_POST['fname']) ? $first_name : null) . '">
	</div>

	<div>
	<label for="lastname">Last Name</label>
	<input type="text" name="lname" value="' . (isset($_POST['lname']) ? $last_name : null) . '">
	</div>

	<div>
	<label for="email">Email <strong>*</strong></label>
	<input type="text" name="email" value="' . (isset($_POST['email']) ? $email : null) . '">
	</div>

	<div>
	<label for="password">Password <strong>*</strong></label>
	<input type="password" name="password" value="' . (isset($_POST['password']) ? $password : null) . '">
	</div>

    <div>
	<label for="city">City <strong>*</strong></label>

	<input type="text" name="city" value="' . (isset($_POST['city']) ? $city : null) . '">
	</div>

	<div>
	<label for="address">Address <strong>*</strong></label>
	<input type="text" name="address_1" value="' . (isset($_POST['address_1']) ? $address : null) . '">
	</div>


	<input type="submit" name="submit" value="Register"/>
	</form>
	';
}

function registration_validation( $username, $password, $email, $address, $first_name, $last_name, $city)  {
    global $reg_errors;
    $reg_errors = new WP_Error;

    if ( empty( $username ) || empty( $password ) || empty( $email ) || empty($city) || empty($address) ) {
        $reg_errors->add('field', 'Required form field is missing');
    }

    if ( strlen( $username ) < 4 ) {
        $reg_errors->add('username_length', 'Username too short. At least 4 characters is required');
    }

    if ( username_exists( $username ) )
        $reg_errors->add('user_name', 'Sorry, that username already exists!');

    if ( !validate_username( $username ) ) {
        $reg_errors->add('username_invalid', 'Sorry, the username you entered is not valid');
    }

    if ( strlen( $password ) < 5 ) {
        $reg_errors->add('password', 'Password length must be greater than 5');
    }

    if ( !is_email( $email ) ) {
        $reg_errors->add('email_invalid', 'Email is not valid');
    }

    if ( email_exists( $email ) ) {
        $reg_errors->add('email', 'Email Already in use');
    }


    if ( is_wp_error( $reg_errors ) ) {
            echo '<div class="error-container">';
        foreach ( $reg_errors->get_error_messages() as $error ) {
            echo '<div class="register-msg">';
            echo '<strong>ERROR</strong>:';
            echo $error . '<br/>';

            echo '</div>';

        }
        echo '</div>';
    }
}

function complete_registration() {
    global $reg_errors, $username, $password, $email, $address, $first_name, $last_name, $city;
    if ( count($reg_errors->get_error_messages()) < 1 ) {
        $userdata = array(
            'user_login'	=> 	$username,
            'user_email' 	=> 	$email,
            'user_pass' 	=> 	$password,
            'user_address' 	=> 	$address,
            'first_name' 	=> 	$first_name,
            'last_name' 	=> 	$last_name,
            'user_city' 	=> 	$city,

        );
        $user = wp_insert_user( $userdata );
        echo 'Registration complete. Goto <a href="' . get_site_url() . '/wp-login.php">login page</a>.';
    }
}

// Register a new shortcode: [cr_custom_registration]
add_shortcode('cr_custom_registration', 'custom_registration_shortcode');

// The callback function that will replace [book]
function custom_registration_shortcode() {
    ob_start();
    custom_registration_function();
    return ob_get_clean();
}



