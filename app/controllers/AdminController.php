<?php

namespace Chayka\LinkedIn;

use Chayka\WP\MVC\Controller;

class AdminController extends Controller{

    public function init(){
        $this->enqueueNgScriptStyle('chayka-wp-admin');
    }

    public function linkedinAction(){

    }
}