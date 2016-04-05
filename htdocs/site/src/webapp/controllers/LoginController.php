<?php

namespace ttm4135\webapp\controllers;
use ttm4135\webapp\Auth;
use ttm4135\webapp\models\User;
use ttm4135\webapp\extras\Handlers;
use ttm4135\webapp\Sql;
use ttm4135\webapp\extras\CSRF;

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
            $this->render('login.twig', ['title'=>"Login"]);
        }
    }

    function login()
    {
        $request = $this->app->request;
        $username = $request->post('username');
        $password = $request->post('password');
        $name  = $_POST['CSRFname'];
        $token = $_POST['CSRFtoken'];
        if (!CSRF::csrfguard_validate_token($name, $token)) {
            header('Location: /');
        }else {
            if (Auth::checkCredentials($username, $password)) {
                Handlers::set_username_cookie($username);
                $user = Sql::getUserByUsername($username); //TODO: Move to correct location
                $_SESSION['userid'] = $user->getId();
                $this->app->flash('info', "You are now successfully logged in as " . $user->getUsername() . ".");
                $this->app->redirect('/');
            } else {
                $this->app->flashNow('error', 'Incorrect username/password combination.');
                $this->render('login.twig', []);
            }
        }
    }

    function logout()
    {
        $name = $_POST['CSRFname'];
        $token = $_POST['CSRFtoken'];
        if (!CSRF::csrfguard_validate_token($name, $token)) {
            header('Location: /');
        } else {
            Auth::logout();
            $this->app->flashNow('info', 'Logged out successfully!!');
            $this->render('base.twig', []);
        }
        return;
       
    }
}
