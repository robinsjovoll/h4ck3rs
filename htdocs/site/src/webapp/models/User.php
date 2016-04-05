<?php

namespace ttm4135\webapp\models;

use ttm4135\webapp\Sql;

class User
{
    const INSERT_QUERY          = "INSERT INTO users(username, password, email, bio, isadmin) VALUES(?, ?, ?, ?, ?)";
    const UPDATE_QUERY          = "INSERT INTO users(username, password, email, bio, isadmin, id) VALUES(?, ?, ?, ?, ?, ?)";
    const DELETE_QUERY          = "DELETE FROM users WHERE id='%s'";
    const FIND_BY_NAME_QUERY    = "SELECT * FROM users WHERE username=?";
    const FIND_BY_ID_QUERY      = "SELECT * FROM users WHERE id=?";
    const ALL_QUERY             = "SELECT * FROM users";

    protected $id = null;
    protected $username;
    protected $password;
    protected $email;
    protected $bio = 'Bio is empty.';
    protected $isAdmin = 0;

    static $app;


    static function make($id, $username, $password, $email, $bio, $isAdmin )
    {
        $user = new User();
        $user->id       = $id;
        $user->username = $username;
        $user->password = $password;
        $user->email    = $email;
        $user->bio      = $bio;
        $user->isAdmin  = $isAdmin;

        return $user;
    }

    static function makeEmpty()
    {
        return new User();
    }

    /**
     * Insert or update a user object to db.
     */
    function save()
    {
        return Sql::addUser($this);
    }

    function getId()
    {
        return $this->id;
    }

    function getUsername()
    {
        return $this->username;
    }

    function getPassword()
    {
        return $this->password;
    }

    function getEmail()
    {
        return $this->email;
    }

    function getBio()
    {
        return $this->bio;
    }

    function isAdmin()
    {
        return $this->isAdmin === "1";
    }

    function setId($id)
    {
        $this->id = $id;
    }

    function setUsername($username)
    {
        $this->username = $username;
    }

    function setPassword($password)
    {
        $this->password = $password;
    }

    function setEmail($email)
    {
        $this->email = $email;
    }

    function setBio($bio)
    {
        $this->bio = $bio;
    }
    function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }


    static function makeFromSql($row)
    {
        return User::make(
            $row['id'],
            $row['username'],
            $row['password'],
            $row['email'],
            $row['bio'],
            $row['isadmin']
        );
    }

}


  User::$app = \Slim\Slim::getInstance();

