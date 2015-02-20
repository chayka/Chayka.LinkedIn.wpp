<?php

namespace Chayka\LinkedIn;

use Chayka\Helpers\CurlHelper;
use Chayka\Helpers\Util;
use Chayka\WP\Models\UserModel;
use Chayka\WP\MVC\Controller;
use Chayka\Helpers\InputHelper;
use Chayka\WP\Helpers\JsonHelper;

class LinkedinController extends Controller{

    public function init(){
        // NlsHelper::load('main');
         InputHelper::captureInput();
    }

    public function loginAction(){
        //	AclHelper::apiAuthRequired();
	    $accessToken = InputHelper::checkParam('oauth_token')->required()->getValue();
	    $userID = InputHelper::checkParam('member_id')->required()->getValue();
        InputHelper::validateInput(true);
	    $me = CurlHelper::get('https://api.linkedin.com/v1/people/~:(id,email-address,first-name,last-name,summary,picture-url,picture-urls::(original))', [
		    'oauth_token' => $accessToken,
		    'format' => 'json'
	    ]);
//	    JsonHelper::respond($me);
	    if(Util::getItem($me, 'errorCode')){
		    JsonHelper::respondError('', 'authentication_failed');
	    }

	    $user = null;
	    if (Util::getItem($me, 'id') === $userID) {
		    $email = Util::getItem($me, 'emailAddress');
		    if($email){
			    /**
			     * LinkedIn provided LinkedIn user email, trying to find WP user with the same email
			     */
			    $user = UserModel::selectByEmail($email);
			    if($user){
				    /**
				     * There is WP user with such email, marking him with FB user ID
				     */
				    $user->updateMeta('linkedin_user_id', $userID);
			    }
		    }
		    if(!$user){
			    /**
			     * There are no user with such email or FB has not provided us with user email
			     * Trying to fetch by user ID
			     */
			    $user = UserModel::query()
			                     ->metaQuery('linkedin_user_id', $userID)
			                     ->selectOne();
		    }
		    if (!$user) {
			    /**
			     * No user found, creating new one
			     */
			    $user = new UserModel();
			    $firstName = Util::getItem($me, 'firstName');
			    $lastName = Util::getItem($me, 'lastName');
			    $name = join(' ', [$firstName, $lastName]);
			    $niceName = sanitize_title(Util::translit(strtolower(join('.', [$firstName, $lastName]))));
			    $wpUserId = $user->setLogin('in' . $userID)
			                     ->setEmail($email?$email:$userID . "@linkedin.com")
			                     ->setDisplayName($name)
			                     ->setFirstName($firstName)
			                     ->setLastName($lastName)
			                     ->setNicename($niceName)
			                     ->setPassword(wp_generate_password(12, false))
			                     ->insert();
			    if ($wpUserId) {
				    $user->updateMeta('linkedin_user_id', $userID);
				    $user->updateMeta('source', 'linkedin');
				    $user = UserModel::selectById($user->getId());
			    }
		    }

		    $pictureUrls = Util::getItem($me, 'pictureUrls');
		    $pictureUrls = Util::getItem($pictureUrls, 'values');
		    $pictureUrl = Util::getItem($pictureUrls, 0);
		    $user->updateMeta('linkedin_avatar_128', Util::getItem($me, 'pictureUrl'));
		    if($pictureUrl){
			    $user->updateMeta('linkedin_avatar_original', $pictureUrl);
		    }
		    /**
		     * Authenticating WP user
		     */
		    $secure_cookie = is_ssl();
		    wp_set_auth_cookie($user->getId(), false, $secure_cookie);
		    do_action('wp_login', $user->getLogin(), $user->getWpUser());
		    JsonHelper::respond($user);
	    }

	    JsonHelper::respondError('', 'authentication_failed');

    }
}