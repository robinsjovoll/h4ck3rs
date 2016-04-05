<?

namespace ttm4135\webapp\extras;
class Handlers
{
//TODO: REMOVE
    static function sanify($data, $encoding = 'UTF-8')
    {
        return htmlspecialchars($data, ENT_QUOTES | ENT_HTML401, $encoding);
    }

    static function valid_certificate()
    {
        $staffCaTag = 'CN=Staff CA,OU=Telematics,O=NORGES TEKNISK-NATURVITENSKAPELIGE UNIVERSITET NTNU,C=NO';
        $studentCaTag = 'CN=Student CA,OU=Telematics,O=NORGES TEKNISK-NATURVITENSKAPELIGE UNIVERSITET NTNU,C=NO';
        $organizationCaTag = 'OU=Telematics,O=NORGES TEKNISK-NATURVITENSKAPELIGE UNIVERSITET NTNU,C=NO';

        /*
         * Forcing these to be strings, to hinder array injections.
         */
        if (!(is_string(ssl_client_subject_dn()) && is_string(ssl_client_issuer_dn()))) {
            return false;
        }

        /*
         * NB: if $_SERVER['xxxx'] is set to "...a[]" the input will evaluate as a
         * null array, and the expression will evaluate to true by default.
         * Therefore we have the check above.
         */
        return (strpos(ssl_client_subject_dn(), $organizationCaTag) !== false)
        && ((ssl_client_issuer_dn() === $studentCaTag) || (ssl_client_issuer_dn() === $staffCaTag)
        );
    }

    static function set_username_cookie($username)
    {
        $cookie_name = "username";

        if (!isset($_COOKIE[$cookie_name])) {
            $cookie_value = $username;
            $thirty_days_from_now = time() + (86400 * 30);

            //Cookie values
            //($name, $value = null, $expire = null, $path = null, $domain = null, $secure = null, $httponly = null)
            //TODO: Secify domain
            setcookie(
                $cookie_name,
                $cookie_value,
                $thirty_days_from_now,
                null,
                null,
                true,
                true
            );
        }
    }

    static function nav_class($page)
    {
        if (is_active($page)) {
            return ' class="active"';
        } else {
            return '';
        }
    }

    static function is_active($page)
    {
        /* Remove any trailing slashes, such that /admin/
         * and /admin gives the same behaviour.
         */
        $request_uri = $_SERVER['REQUEST_URI'];
        $request_php = str_replace(".php", "", $request_uri);
        $request_page = rtrim($request_php, '/');

        return $page === $request_page;
    }

    static function session_username_isset()
    {
        return isset($_SESSION['username']);
    }

    static function status_isset()
    {
        return isset($_GET['status']);
    }

    static function get_session_username()
    {
        return sanify($_SESSION['username']);
    }

    static function get_cookie_username()
    {
        return sanify($_COOKIE["username"]);
    }

    static function get_username()
    {
        return sanify($_GET['username']);
    }

    static function get_status()
    {
        return sanify($_GET['status']);
    }

    static function status_success()
    {
        return get_status() === '1';
    }

    static function ssl_client_subject_dn()
    {
        return $_SERVER['SSL_CLIENT_S_DN'];
    }

    static function ssl_client_issuer_dn()
    {
        return $_SERVER['SSL_CLIENT_I_DN'];
    }
}

