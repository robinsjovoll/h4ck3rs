<?
//TODO: REMOVE
//function sanify($data, $encoding = 'UTF-8') {
//    return htmlspecialchars($data, ENT_QUOTES | ENT_HTML401, $encoding);
//}

function valid_certificate() {
    $staffCaTag        = 'CN=Staff CA,OU=Telematics,O=NORGES TEKNISK-NATURVITENSKAPELIGE UNIVERSITET NTNU,C=NO';
    $studentCaTag      = 'CN=Student CA,OU=Telematics,O=NORGES TEKNISK-NATURVITENSKAPELIGE UNIVERSITET NTNU,C=NO';
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

function set_username_cookie($username) {
    $cookie_name = "username";

    if (!isset($_COOKIE[$cookie_name])) {
        $cookie_value         = $username;
        $thirty_days_from_now = time() + (86400 * 30);

        setcookie(
            $cookie_name,
            $cookie_value,
            $thirty_days_from_now
        );
    }
}

function nav_class($page) {
    if (is_active($page)) {
        return ' class="active"';
    } else {
        return '';
    }
}

function is_active($page) {
    /* Remove any trailing slashes, such that /admin/
     * and /admin gives the same behaviour. 
     */
    $request_uri  = $_SERVER['REQUEST_URI'];
    $request_php  = str_replace(".php", "", $request_uri);
    $request_page = rtrim($request_php, '/');

    return $page === $request_page;
}

function session_username_isset() {
    return isset($_SESSION['username']);
}

function status_isset() {
    return isset($_GET['status']);
}

function get_session_username() {
    return sanify($_SESSION['username']);
}

function get_cookie_username() {
    return sanify($_COOKIE["username"]);
}

function get_username() {
    return sanify($_GET['username']);
}

function get_status() {
    return sanify($_GET['status']);
}

function status_success() {
    return get_status() === '1';
}

function ssl_client_subject_dn() {
    return $_SERVER['SSL_CLIENT_S_DN'];
}

function ssl_client_issuer_dn() {
    return $_SERVER['SSL_CLIENT_I_DN'];
}

