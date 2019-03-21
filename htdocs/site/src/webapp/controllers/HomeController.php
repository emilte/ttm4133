<?php

namespace ttm4135\webapp\controllers;

use ttm4135\webapp\models\User;
use ttm4135\webapp\Auth;

class HomeController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->render('base.twig', []);
        } else {
            $this->render('base.twig',[]);
        }
    }

    function pull()
    {
        if ( $_POST['payload'] ) {
            shell_exec( 'cd /home/grp43/apache/ && git reset –hard HEAD && git pull' );
            $this->app->flash('info', "git pull succeeded!");
            $this->render('base.twig',[]);
        }
        else {
            $this->app->flashNow('error', 'git pull failed!');
            $this->render('base.twig',[]);
        }
    }


}
