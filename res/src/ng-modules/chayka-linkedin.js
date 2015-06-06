'use strict';
angular.module('chayka-auth')
    .factory('linkedin', ['$translate', 'ajax', 'auth', function($translate, ajax, auth){
        var $ = angular.element;
        var lnkdn = {

            $scope: null,

            autoAuth: false,

            notAuthorized: false,
            IN: window.IN,
            currentUser: window.Chayka.Users.currentUser,

            getIN: function(){
                if(!lnkdn.IN){
                    if(window.IN) {
                        // Here we subscribe to the auth.authResponseChange JavaScript event. This event is fired
                        // for any authentication related change, such as login, logout or session refresh. This means that
                        // whenever someone who was previously logged out tries to log in again, the correct case below
                        // will be handled.
                        lnkdn.IN = window.IN;
                        lnkdn.IN.Event.on(window.IN, 'auth', lnkdn.onStatusChanged);
                        if(lnkdn.autoAuth){
                            lnkdn.onLoginButtonClicked(lnkdn.autoAuth);
                        }
                        //this.parseXFBML();

                    }else{
                        var script = $('#linkedin_script');
                        if(script.attr('type') === 'text/template'){
                            $('<script type="text/javascript">')
                                .attr('src', script.attr('src'))
                                .text(script.text())
                                .insertAfter(script);
                            script.remove();
                        }
                    }
                }
                return lnkdn.IN;
            },

            getInUserId: function(){
                return lnkdn.currentUser.meta.in_user_id;
            },

            onStatusChanged: function(response) {
                // Here we specify what we do with the response anytime this event occurs.
                //if(lnkdn.getInUserId() !== response.authResponse.userID) {
                lnkdn.onInLogin(lnkdn.IN.ENV.auth);
                //}
                //lnkdn.IN.API.Raw("/people/~")
                //    .result(function(data){
                //        console.dir({'lnkdn.success': data});
                //    })
                //    .error(function(data){
                //        console.dir({'lnkdn.error': data});
                //    });
            },

            onLoginButtonClicked: function(event){
                lnkdn.autoAuth = event;
                if(event) {
                    event.preventDefault();
                }
                if(lnkdn.getIN()){
                    lnkdn.IN.User.authorize(lnkdn.onStatusChanged, lnkdn);
                }
            },

            onInLogin: function(INResponse){
                console.dir({INResponse: INResponse});

                ajax.post('/api/linkedin/login', INResponse, {
                    spinner: false,
                    showMessage: false,
                    errorMessage: $translate.instant('message_error_auth_failed'),
                    success: function(data){
                        lnkdn.$scope.$emit('Chayka.Users.currentUserChanged', data.payload);
                    },
                    complete: function(data){
                    }
                });
            }
        };

        auth.LinkedIn = lnkdn;

        return lnkdn;
    }])
    .directive('authLinkedinButton', ['linkedin', function(lnkdn){
        var $ = angular.element;

        return {
            restrict: 'A',
            link: function($scope, element){
                lnkdn.getIN();
                lnkdn.$scope = $scope;
                $(document).on('logout', lnkdn.logout);
                $(element).click(lnkdn.onLoginButtonClicked);
                $scope.$on('Chayka.Users.currentUserChanged', function(user){
                    console.dir({'LinkedIn.currentUserChanged': user});
                });

            }
        };
    }])
;