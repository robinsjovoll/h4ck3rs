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

        self::$pdo->exec($q1);

        print "[ttm4135] Done creating all SQL tables.".PHP_EOL;

        self::insertDummyUsers();
    }

    static function insertDummyUsers() {


        $q1 = "INSERT INTO users(username, password, isadmin) VALUES ('admin', 'admin', 1)";
        $q2 = "INSERT INTO users(username, password) VALUES ('bob', 'bob')";

        self::$pdo->exec($q1);
        self::$pdo->exec($q2);

        print "[ttm4135] Done inserting dummy users.".PHP_EOL;
    }

    static function addUser($user, $update = true) {
        if ($user->id !== null && !$update) {
            return false;
        }

        if ($user->id !== null) {
            $query = User::UPDATE_QUERY;
            $parameters = [$user->username, $user->password, $user->email, $user->bio, $user->isAdmin, $user->id];
        } else {
            $query = self::$pdo->prepare(User::INSERT_QUERY);
            $parameters = [$user->username, $user->password, $user->email, $user->bio, $user->isAdmin];
        }

        $statement = self::$pdo->prepare($query);
        $result = $statement->execute($parameters);

        print "[ttm4135] Done inserting user.".PHP_EOL;
        return $result;
    }



    static function down() {
        $q1 = "DROP TABLE users";

        self::$pdo->exec($q1);

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
