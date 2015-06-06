<?php

namespace Chayka\LinkedIn;


use Chayka\Helpers\Util;
use Chayka\WP\Models\CommentModel;
use Chayka\WP\Models\UserModel;

class LinkedInHelper {


	/**
	 * @return boolean
	 */
	public static function isJsApiEnabled(){
		return !!OptionHelper::getOption('initJsApi');
	}

	/**
	 * @return boolean
	 */
	public static function isJsApiLazy(){
		return !!OptionHelper::getOption('lazyJsApi');
	}

	/**
	 * @return string
	 */
	public static function getApiKey(){
		return OptionHelper::getOption('apiKey');
	}

	/**
	 * @return string
	 */
	public static function getSecretKey(){
		return OptionHelper::getOption('secretKey');
	}

	/**
	 * @return string
	 */
	public static function getOAuthUserToken(){
		return OptionHelper::getOption('oAuthUserToken');
	}

	/**
	 * @return string
	 */
	public static function getOAuthUserSecret(){
		return OptionHelper::getOption('oAuthUserSecret');
	}

	/**
	 * Replace GrAvatar with FBAvatar
	 *
	 * @param $avatar
	 * @param $id_or_email
	 * @param int $size
	 *
	 * @return mixed
	 */
	public static function filterGetLinkedInAvatar($avatar, $id_or_email, $size = 96){
		if(!$id_or_email){
			return $avatar;
		}
		$user = null;
		if(is_object($id_or_email)){
			$user = UserModel::unpackDbRecord($id_or_email);
		}else{
			$user = is_email($id_or_email)?
				UserModel::selectByEmail($id_or_email):
				UserModel::selectById($id_or_email);
		}
		if($user){
			$metaFbUseId = $user->getMeta('linkedin_user_id');
			if($metaFbUseId){
				if(!intval($size)){
					$size = 96;
				}
				$avatarUrl = sprintf('//graph.facebook.com/%s/picture?type=square&width=%d&height=%d', $metaFbUseId, (int)$size, $size);
				return preg_replace("%src='[^']*'%", "src='$avatarUrl'", $avatar);
			}
		}else{
		}

		return $avatar;
	}

	/**
	 * Used to display comment FB avatar
	 *
	 * @param CommentModel $comment
	 * @return CommentModel
	 */
	public static function filterMarkCommentWithLinkedInUserId($comment){
		if($comment->getUserId()){
			$user = UserModel::selectById($comment->getUserId());
			if($user && $user->getMeta('linkedin_user_id')){
				$comment->updateMeta('linkedin_user_id', $user->getMeta('linkedin_user_id'));
			}
		}
		return $comment;
	}

	/**
	 * Used for instant comment approval
	 *
	 * @param $approved
	 * @param $rawComment
	 *
	 * @return bool
	 */
	public static function filterApproveLinkedInUserComment($approved, $rawComment){
		$userId = Util::getItem($rawComment, 'user_id');
		if(!$approved && $userId){
			$user = UserModel::selectById($userId);
			if($user && $user->getMeta('linkedin_user_id')){
				$approved = true;
			}
		}
		return $approved;
	}
}