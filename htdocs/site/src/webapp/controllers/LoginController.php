<?php

namespace ttm4135\webapp\controllers;
use ttm4135\webapp\Auth;
use ttm4135\webapp\models\User;
use ttm4135\webapp\extras\Handlers;
use ttm4135\webapp\Sql;

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
            $handler = "";
            if(Handlers::cookie_username_isset()) {
                $handler = Handlers::get_cookie_username();
            }
            $this->render('login.twig', ['title'=>"Login", 'cookieUsername'=>$handler]);
        }
    }

    function login()
    {
        $request = $this->app->request;
        $username = $request->post('username');
        $password = $request->post('password');
        $user = Sql::getUserByUsername($username);
        $hashedPassword = $user->getPassword();
        $this->app->flashNow('info', $password . " checkingPass: " . $user->getPassword() . " verify: " . password_verify($password, $hashedPassword) . " checking: " .  Auth::checkCredentials($username, $password, $hashedPassword));
        if ( Auth::checkCredentials($username, $password, $hashedPassword) ) {
            Handlers::set_username_cookie($username);
            $user = Sql::getUserByUsername($username);
            $_SESSION['userid'] = $user->getId();
            $this->app->flash('info', "You are now successfully logged in as " . $user->getUsername() . ".");
            $this->app->redirect('/');
        } else {
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
