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

        $e = shell_exec( 'cd /home/grp43/apache/ && /usr/bin/git reset –hard HEAD && git pull' );
        $var1 = "Test";
        $s = $var1 . " " . $e;
        shell_exec( 'touch /home/grp43/hei.txt');
        shell_exec( "echo '" . $s . "' > /home/grp43/hei.txt");
        shell_exec( "echo 'test' >> /home/grp43/hei.txt");
        shell_exec( "echo {$e} >> /home/grp43/hei.txt");
        shell_exec( "echo {$var1} >> /home/grp43/hei.txt");

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
