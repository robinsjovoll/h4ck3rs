<?php
/**
 * Created by IntelliJ IDEA.
 * User: Robin
 * Date: 05.04.2016
 * Time: 17:24
 */

class CSRF
{
    private $name;
    private $token;

    function __construct()
    {
        $this->name = "CSRFGuard_" . mt_rand(0, mt_getrandmax());
        $this->token = $this->csrfguard_generate_token($this->name);
        $this->store_in_session($this->name, $this->token);
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    private function csrfguard_generate_token($unique_form_name)
    {
        return hash("sha256", mt_rand(0, mt_getrandmax()));
    }

    public static function csrfguard_validate_token($name, $token_value)
    {
        $token = self::get_from_session($name);
        if ($token === false) {
            return false;
        } elseif ($token === $token_value) {
            $result = true;
        } else {
            $result = false;
        }
        self::unset_session($name);
        return $result;
    }

    private function store_in_session($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    private static function unset_session($key)
    {
        $_SESSION[$key] = ' ';
        unset($_SESSION[$key]);
    }

    private static function get_from_session($key)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return false;
        }
    }
}