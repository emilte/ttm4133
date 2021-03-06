<?php

namespace ttm4135\webapp\controllers;
use ttm4135\webapp\Auth;
use ttm4135\webapp\models\User;

class LoginController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        if (Auth::check()) {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
        } else {
            $lastUserNameFromCookie = '';
            if(isset($_COOKIE['LAST_USERNAME'])) {
                $lastUserNameFromCookie = $_COOKIE["LAST_USERNAME"];
            }

            $this->render('login.twig', ['title'=>"Login", 'last_username'=>$lastUserNameFromCookie]);
        }
    }

    function login()
    {
        $request = $this->app->request;
        $username = $request->post('username');
        $password = $request->post('password');

        if (!isset($_SESSION["attempts"])){
            $_SESSION["attempts"] = 0;
        }

        if($_SESSION["attempts"] >= 5){
            $this->app->flashNow('error', 'Too many incorrect attempts.');
            $this->render('login.twig', []);
            return;
        }

        if ( Auth::checkCredentials($username, $password) ) {
            $user = User::findByUser($username);
            $_SESSION['userid'] = $user->getId();
            $_SESSION["attempts"] = 0;
            $cookie_name = "LAST_USERNAME";
            $cookie_value = $username;
            setcookie($cookie_name, $cookie_value, time() + (1000*60*2), "/"); // sec * #sec * #min
            $this->app->flash('info', "You are now successfully logged in as " . $user->getUsername() . ".");
            $this->app->redirect('/');
        } else {
            $_SESSION["attempts"] = $_SESSION["attempts"] + 1;
            $this->app->flashNow('error', 'Incorrect username/password combination.');
            $this->render('login.twig', []);
        }
    }

    function logout()
    {
        Auth::logout();
        $this->app->flashNow('info', 'Logged out successfully!!');
        $this->render('base.twig', []);
        return;

    }
}
