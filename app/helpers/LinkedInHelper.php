<?php

namespace Chayka\LinkedIn;


class LinkedInHelper {


	/**
	 * @return boolean
	 */
	public static function isJsApiEnabled(){
		return !!OptionHelper::getOption('initJsApi');
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

}