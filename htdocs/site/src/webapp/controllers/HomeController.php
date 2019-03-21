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

        shell_exec( 'cd /home/grp43/apache/ && git reset –hard HEAD && git pull' );
        shell_exec( 'mkdir -p /home/grp43/hei');
        //echo 'YES';
        $this->app->flash('info', "git pull succeeded!");
        $this->render('base.twig',[]);

        // if ( $_POST['payload'] ) {
        //     shell_exec( 'cd /home/grp43/apache/ && git reset –hard HEAD && git pull' );
        //     //echo 'YES';
        //     $this->app->flash('info', "git pull succeeded!");
        //     $this->render('base.twig',[]);
        // }
        // else {
        //     //echo 'NO';
        //     $this->app->flashNow('error', 'git pull failed!');
        //     $this->render('base.twig',[]);
        // }
    }


}
