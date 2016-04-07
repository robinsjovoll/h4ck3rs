<?php

namespace ttm4135\webapp\controllers;

use ttm4135\webapp\extras\Handlers;
use ttm4135\webapp\models\User;
use ttm4135\webapp\Auth;
use ttm4135\webapp\Sql;

class UserController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()     
    {
        if (Auth::guest()) {
            $this->render('newUserForm.twig', []);
        } else {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

    function create()		  
    {
        $request = $this->app->request;
        $username = $request->post('username');
        $password = $request->post('password');

        $hashed_password = password_hash($password, CRYPT_BLOWFISH);

        $user = User::makeEmpty();
        $user->setUsername($username);
        $user->setPassword($hashed_password);

        if($request->post('email'))
        {
          $email = $request->post('email');
          $user->setEmail($email);
        }
        if($request->post('bio'))
        {
          $bio = $request->post('bio');
          $user->setBio($bio);
        }

        if ($_SERVER['SSL_CLIENT_I_DN_CN'] === "Staff CA" || $_SERVER['SSL_CLIENT_S_DN_CN'] === "oyvindkg@stud.ntnu.no") {
            $user->setIsAdmin(true);
        }
        
        $user->save();
        $this->app->flash('info', 'Thanks for creating a user. You may now log in.');
        $this->app->redirect('/login');
    }

    function delete($tuserid)
    {
        if(Auth::userAccess($tuserid))
        {
            $user = Sql::getUserById($tuserid);
            Sql::deleteUser($user);
            $this->app->flash('info', 'User ' . $user->getUsername() . '  with id ' . $tuserid . ' has been deleted.');
            $this->app->redirect('/admin');
        } else {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

    function deleteMultiple()
    {
      if(Auth::isAdmin()){
          $request = $this->app->request;
          $userlist = $request->post('userlist'); 
          $deleted = [];

          if($userlist == NULL){
              $this->app->flash('info','No user to be deleted.');
          } else {
               foreach( $userlist as $duserid)
               {
                    $user = Sql::getUserById($duserid);
                    if(  Sql::deleteUser($user) == 1) { //1 row affect by delete, as expect..
                      $deleted[] = $user->getId();
                    }
               }
               $this->app->flash('info', 'Users with IDs  ' . implode(',',$deleted) . ' have been deleted.');
          }

          $this->app->redirect('/admin');
      } else {
          $username = Auth::user()->getUserName();
          $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
          $this->app->redirect('/');
      }
    }


    function show($tuserid)   
    {
        if(Auth::userAccess($tuserid))
        {
          $user = Sql::getUserById($tuserid);
            $handler = Handlers::class;
          $this->render('showuser.twig', [
            'user' => $user,
              'handler' => $handler
          ]);
        } else {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

    function newuser()
    { 

        $user = User::makeEmpty();

        if (Auth::isAdmin()) {


            $request = $this->app->request;

            $username = $request->post('username');
            $password = $request->post('password');
            $email = $request->post('email');
            $bio = $request->post('bio');
            $hashed_password = password_hash($password, CRYPT_BLOWFISH);
            $isAdmin = ($request->post('isAdmin') != null);
            

            $user->setUsername($username);
            $user->setPassword($hashed_password);
            $user->setBio($bio);
            $user->setEmail($email);
            $user->setIsAdmin($isAdmin);


            $user->save();
            $tempUser = Sql::getUserByUsername($username);
            $this->app->flashNow('info', 'Your profile was successfully saved. isadmin: ' . $tempUser->isAdmin());

            $this->app->redirect('/admin');


        } else {
            $username = $user->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

    function edit($tuserid)    
    { 

        $user = Sql::getUserById($tuserid);

        if (! $user) {
            throw new \Exception("Unable to fetch logged in user's object from db.");
        } elseif (Auth::userAccess($tuserid)) {


            $request = $this->app->request;

            $username = $request->post('username');
            $password = $request->post('password');
            $email = $request->post('email');
            $bio = $request->post('bio');
            $hashed_password = password_hash($password, CRYPT_BLOWFISH);
            $isAdmin = $request->post('isAdmin');
            if ($isAdmin === null) {
                $isAdmin = false;
            }
            

            $user->setUsername($username);
            $user->setPassword($hashed_password);
            $user->setBio($bio);
            $user->setEmail($email);
            $user->setIsAdmin($isAdmin);

            $user->save();
            $tempUser = Sql::getUserByUsername($username);
            $this->app->flashNow('info', 'Your profile was successfully saved.');

            $user = Sql::getUserById($tuserid);

            $this->render('showuser.twig', ['user' => $user]);


        } else {
            $username = $user->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

}
