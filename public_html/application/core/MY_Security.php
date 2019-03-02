<?php
class MY_Security extends CI_Security {

    public function __construct(){
        parent::__construct();
    }

    public function csrf_show_error(){
        // show_error('The action you have requested is not allowed.');  // default code

        // force page "refresh" - redirect back to itself with sanitized URI for security
        // a page refresh restores the CSRF cookie to allow a subsequent login
		header('Location: /Home/csrf_redirect', TRUE, 302);
        // header('Location: ' . htmlspecialchars($_SERVER['REQUEST_URI']), TRUE, 200);
    }
}