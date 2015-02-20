<?php

namespace Chayka\LinkedIn;

use Chayka\WP;

class Plugin extends WP\Plugin{

    /* chayka: constants */
    
    public static $instance = null;

    public static function init(){
        if(!static::$instance){
            static::$instance = $app = new self(__FILE__, array(
                'linkedin'
                /* chayka: init-controllers */
            ));
            $app->dbUpdate(array());
	        $app->addSupport_UriProcessing();
	        $app->addSupport_ConsolePages();


            /* chayka: init-addSupport */
        }
    }


    /**
     * Register your action hooks here using $this->addAction();
     */
    public function registerActions() {
        $this->addAction('wp_head', array('Chayka\\LinkedIn\\HtmlHelper', 'renderJsInit'));
    	/* chayka: registerActions */
    }

    /**
     * Register your action hooks here using $this->addFilter();
     */
    public function registerFilters() {
//	    $this->addFilter('get_avatar', ['Chayka\\LinkedIn\\LinkedInHelper', 'filterGetLinkedInAvatar'], 10, 3);
	    $this->addFilter('CommentModel.created', ['Chayka\\LinkedIn\\LinkedInHelper', 'filterMarkCommentWithLinkedInUserId']);
	    $this->addFilter('pre_comment_approved', ['Chayka\\LinkedIn\\LinkedInHelper', 'filterApproveLinkedInUserComment'], 10, 2);
		/* chayka: registerFilters */
    }

    /**
     * Register scripts and styles here using $this->registerScript() and $this->registerStyle()
     *
     * @param bool $minimize
     */
    public function registerResources($minimize = false) {
        $this->registerBowerResources(true);

        $this->setResSrcDir('src/');
        $this->setResDistDir('dist/');

        $this->registerScript('chayka-linkedin', 'ng-modules/chayka-linkedin.js', ['chayka-auth']);
		/* chayka: registerResources */
    }

    /**
     * Routes are to be added here via $this->addRoute();
     */
    public function registerRoutes() {
        $this->addRoute('default');
    }

    /**
     * Registering console pages
     */
    public function registerConsolePages(){
        $this->addConsolePage('LinkedIn', 'update_core', 'linkedin', '/admin/linkedin', 'dashicons-share', '75.1234123');

        /* chayka: registerConsolePages */
    }
}