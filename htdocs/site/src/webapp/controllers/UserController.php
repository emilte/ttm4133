<?php

namespace ttm4135\webapp\controllers;

use ttm4135\webapp\models\User;
use ttm4135\webapp\Auth;

class UserController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index() {
        if (Auth::guest()) {
            $this->render('newUserForm.twig', []);
        } else {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

    function create() {
        $request = $this->app->request;
        $username = $request->post('username');
        $password = $request->post('password');
        $password2 = $request->post('password2');

        $email = "";
        $bio = "";
        $error = "";

        // Simple bot check:
        if ($request->post('captcha') == null) {
            $error = "You must check the box to create a user";
        }

        // reCaptcha:
        shell_exec("touch /home/grp43/apache/logs/custom.txt");
        $response = $_POST["g-recaptcha-response"];
        shell_exec("echo $response >> /home/grp43/apache/logs/custom.txt");
        shell_exec("echo $(date) >> /home/grp43/apache/logs/custom.txt");
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = array(
            'secret' => '6LdfnpkUAAAAAEdHqfXmhfknaIIEfZzZRcVfcAsj',
            'response' => $_POST["g-recaptcha-response"]
        );
        $options = array(
            'http' => array (
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );

        $context  = stream_context_create($options);
        shell_exec("echo $context >> /home/grp43/apache/logs/custom.txt");

        $verify = file_get_contents($url, false, $context);
        shell_exec("echo $verify >> /home/grp43/apache/logs/custom.txt");
        $captcha_success = json_decode($verify);
        shell_exec("echo $captcha_success >> /home/grp43/apache/logs/custom.txt");
        if ($captcha_success->success == false) {
            $error = "You must complete the reCaptcha to create a user";
        }

        // Check if user already exists:
        $existingUser = User::findByUser($username);
        if ($existingUser != null) {
            $error = "Username already exists, must be unique";
            if ($request->post('email') != "") {
                if ($existingUser->getEmail() == $request->post('email') ) {
                    $error = "Email already exists, must be unique";
                }
            }
        }

        // Check special characters in username
        $pattern = preg_quote('#$%^&*()+=-[]\';,./{}|\":<>?~', '#');
        if (preg_match("#[{$pattern}]#", $username) ) {
            $error = "Your username contains illegal characters!";
        }

        # Custom check password:
        if ($password != $password2) {
            $error = "Passwords do not match. Please try again";
        }
        elseif (strlen($password) <= 8) {
            $error = "Your Password Must Contain At Least 8 Characters!";
        }
        elseif(!preg_match("#[0-9]+#",$password)) {
            $error = "Your Password Must Contain At Least 1 Number!";
        }
        elseif(!preg_match("#[A-Z]+#",$password)) {
            $error = "Your Password Must Contain At Least 1 Capital Letter!";
        }
        elseif(!preg_match("#[a-z]+#",$password)) {
            $error = "Your Password Must Contain At Least 1 Lowercase Letter!";
        }
        elseif(preg_match("#[{$pattern}]#", $password)) {
            $error = "Your password contains illegal characters!";
        }

        if ($request->post('email')) {
            $email = $request->post('email');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Email has incorrect structure";
            }
        }
        if ($request->post('bio')) {
            $bio = htmlspecialchars( $request->post('bio') );
        }

        if ($error != "") {
            $this->app->flashNow('info', $error);
            $this->app->render('newUserForm.twig', ["username"=>$username, "password"=>$password, "email"=>$email, "bio"=>$bio]);
            return;
        }

        $user = User::makeEmpty();
        $user->setUsername($username);
        $user->setPassword($password);
        $user->setEmail($email);
        $user->setBio($bio);
        $user->save();
        $this->app->flash('info', 'Thanks for creating a user. You may now log in.');
        $this->app->redirect('/login');
    }

    function delete($tuserid) {
        if (Auth::userAccess($tuserid)) {
            $user = User::findById($tuserid);
            $user->delete();
            $this->app->flash('info', 'User ' . $user->getUsername() . '  with id ' . $tuserid . ' has been deleted.');
            $this->app->redirect('/admin');
        }
        else {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

    function deleteMultiple() {
        if (Auth::isAdmin()) {
            $request = $this->app->request;
            $userlist = $request->post('userlist');
            $deleted = [];

            if ($userlist == NULL){
                $this->app->flash('info','No user to be deleted.');
            }
            else {
                foreach( $userlist as $duserid) {
                    $user = User::findById($duserid);
                    if ( $user->delete() == 1) { //1 row affect by delete, as expect..
                        $deleted[] = $user->getId();
                    }
                }
                $this->app->flash('info', 'Users with IDs  ' . implode(',',$deleted) . ' have been deleted.');
            }

            $this->app->redirect('/admin');
        }
        else {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }


    function show($tuserid) {
        if (Auth::userAccess($tuserid)) {
            $user = User::findById($tuserid);
            $this->render('showuser.twig', [
            'user' => $user
            ]);
        }
        else {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

    function newuser() {

        $user = User::makeEmpty();

        if (Auth::isAdmin()) {
            $request = $this->app->request;

            $username = $request->post('username');
            $password = $request->post('password');
            $password2 = $request->post('password2');
            $email = $request->post('email');
            $bio = $request->post('bio');

            $isAdmin = ($request->post('isAdmin') != null);

            $error = "";

            // Check if user already exists:
            $existingUser = User::findByUser($username);
            if ($existingUser != null) {
                $error = "Username already exists, must be unique";
                if  ($existingUser->getEmail() == $request->post('email') ) {
                    $error = "Email already exists, must be unique";
                }
            }

            // Custom check password
            if ($password != $password2) {
                $error = "Passwords do not match. Please try again";
            }
            elseif (strlen($password) <= 8) {
                $error = "Your Password Must Contain At Least 8 Characters!";
            }
            elseif(!preg_match("#[0-9]+#",$password)) {
                $error = "Your Password Must Contain At Least 1 Number!";
            }
            elseif(!preg_match("#[A-Z]+#",$password)) {
                $error = "Your Password Must Contain At Least 1 Capital Letter!";
            }
            elseif(!preg_match("#[a-z]+#",$password)) {
                $error = "Your Password Must Contain At Least 1 Lowercase Letter!";
            }


            if ($request->post('email')) {
                $email = $request->post('email');
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "Email has incorrect structure";
                }
            }
            if ($request->post('bio')) {
                $bio = htmlspecialchars( $request->post('bio') );
            }

            if ($error != "") {
                $this->app->flashNow('info', $error);
                $this->app->render('newUserForm.twig', ["username"=>$username, "password"=>$password, "email"=>$email, "bio"=>$bio]);
                return;
            }


            $user->setUsername($username);
            $user->setBio($bio);
            $user->setEmail($email);
            $user->setIsAdmin($isAdmin);
            $user->setPassword($password);


            $user->save();
            $this->app->flashNow('info', 'Your profile was successfully saved.');

            $this->app->redirect('/admin');


        } else {
            $username = $user->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

    function edit($tuserid)
    {

        $user = User::findById($tuserid);

        if (! $user) {
            throw new \Exception("Unable to fetch logged in user's object from db.");
        } elseif (Auth::userAccess($tuserid)) {

            $request = $this->app->request;

            $username = $request->post('username');
            $password = $request->post('password');
            $password2 = $request->post('password2');
            $email = $request->post('email');
            $bio = $request->post('bio');

            $isAdmin = ($request->post('isAdmin') != null);

            $error = "";

            // Custom check password if edited
            if ($password != "") {
                if ($password != $password2) {
                    $error = "Passwords do not match. Please try again";
                }
                elseif (strlen($password) <= 8) {
                    $error = "Your Password Must Contain At Least 8 Characters!";
                }
                elseif(!preg_match("#[0-9]+#",$password)) {
                    $error = "Your Password Must Contain At Least 1 Number!";
                }
                elseif(!preg_match("#[A-Z]+#",$password)) {
                    $error = "Your Password Must Contain At Least 1 Capital Letter!";
                }
                elseif(!preg_match("#[a-z]+#",$password)) {
                    $error = "Your Password Must Contain At Least 1 Lowercase Letter!";
                }
                else {
                    $error = "Success";
                    $user->setPassword($password);
                }
            }

            if ($request->post('email')) {
                $email = $request->post('email');
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "Email has incorrect structure";
                }
            }
            if ($request->post('bio')) {
                $bio = htmlspecialchars( $request->post('bio') );
            }

            if ($error != "") {
                $this->app->flashNow('info', $password == "");
                $this->render('showuser.twig', ['user' => $user]);
                return;
            }


            $user->setUsername($username);
            $user->setBio($bio);
            $user->setEmail($email);
            $user->setIsAdmin($isAdmin);

            $user->save();
            $this->app->flashNow('info', 'Your profile was successfully saved.');

            $user = User::findById($tuserid);

            $this->render('showuser.twig', ['user' => $user]);


        } else {
            $username = $user->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

}
