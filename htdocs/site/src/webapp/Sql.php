<?php

namespace ttm4135\webapp;

use ttm4135\webapp\models\User;

class Sql
{
    static $pdo;

    function __construct()
    {
    }

    /**
     * Create tables.
     */
    static function up() {
        $q1 = "CREATE TABLE users (id INTEGER PRIMARY KEY, username VARCHAR(50), password VARCHAR(50), email varchar(50),  bio varhar(50), isadmin INTEGER);";

        self::executeUpdate($q1);

        print "[ttm4135] Done creating all SQL tables.".PHP_EOL;

        self::insertDummyUsers();
    }

    static function insertDummyUsers() {

        $insertQuery = "INSERT INTO users(username, password, isadmin) VALUES (?, ?, ?)";
        self::executeUpdate($insertQuery, ['hackers', 'ed88459e', 1]);
        self::executeUpdate($insertQuery, ['bob', 'bob', 0]);

        print "[ttm4135] Done inserting dummy users.".PHP_EOL;
    }

    private static function executeUpdate($query, $parameters = array()) {
        $statement = self::$pdo->prepare($query);
        $result = $statement->execute($parameters);

        return $result;
    }

    private  static function executeQuery($query, $parameters = array()) {
        $statement = self::$pdo->prepare($query);
        $statement->execute($parameters);

        return $statement;
    }

    static function getUserByUsername($username) {
        $statement = Sql::executeQuery(User::FIND_BY_NAME_QUERY, [$username]);

        $row = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($row === null) {
            return null;
        }

        return User::makeFromSql($row);
    }

    static function getUserById($id) {
        $statement = Sql::executeQuery(User::FIND_BY_ID_QUERY, [$id]);

        $row = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($row === null) {
            return null;
        }

        return User::makeFromSql($row);
    }

    static function deleteUser($user) {
        if ($user->getId() === null) {
            return false;
        }

        return self::executeUpdate(User::DELETE_QUERY, [$user->getId()]);
    }

    static function getAllUsers() {
        $results = Sql::executeQuery(User::ALL_QUERY);

        $users = [];

        foreach ($results as $row) {
            $user = User::makeFromSql($row);
            array_push($users, $user);
        }

        return $users;
    }

    static function addUser($user, $update = true) {
        if ($user->getId() !== null && !$update) {
            return false;
        }

        if ($user->getId() !== null) {
            $query = User::UPDATE_QUERY;
            $parameters = [$user->getUsername(), $user->getPassword(), $user->getEmail(), $user->getBio(), $user->isAdmin(), $user->getId()];
        } else {
            $query = User::INSERT_QUERY;
            $parameters = [$user->getUsername(), $user->getPassword(), $user->getEmail(), $user->getBio(), $user->isAdmin()];
        }

        $result = Sql::executeUpdate($query, $parameters);

        print "[ttm4135] Done inserting user.".PHP_EOL;
        return $result;
    }


    static function down() {
        Sql::executeUpdate("DROP TABLE users");

        print "[ttm4135] Done deleting all SQL tables.".PHP_EOL;
    }

}
try {
    // Create (connect to) SQLite database in file
    Sql::$pdo = new \PDO('sqlite:/home/gr11/apache/htdocs/site/app.db'); //TODO: Use username and password?
    // Set errormode to exceptions
    Sql::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
} catch(\PDOException $e) {
    echo $e->getMessage();
    exit();
}
