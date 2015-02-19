<?php

namespace Chayka\LinkedIn;

use Chayka\WP\MVC\Controller;
use Chayka\Helpers\InputHelper;
use Chayka\WP\Helpers\JsonHelper;

class LinkedinController extends Controller{

    public function init(){
        // NlsHelper::load('main');
        // InputHelper::captureInput();
    }

    public function loginAction(){
        //	AclHelper::apiAuthRequired();

        InputHelper::validateInput(true);

		$valid = true;

		if(!$valid){
			JsonHelper::respondError("Scary error message");
		}

		try{
			//	do something usefull
			
			$payload = array(
			);

			JsonHelper::respond($payload);

		}catch(\Exception $e){
			JsonHelper::respondException($e);
		}
    }
}