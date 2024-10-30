/**
 * Admin side AngularJS app
 */
var mcbAdminApp = angular.module('mcbAdminApp', ['oitozero.ngSweetAlert']);

mcbAdminApp.controller('AdminCtrl', function ($scope, Api, $timeout, SweetAlert) {

    $scope.UNKNOWN_ERROR_CODE = 9999;
    $scope.INVALID_TOKEN_ERROR_CODE = 800;
    $scope.ADMIN_VERIFICATION_FAILED_ERROR_CODE = 700;
    $scope.GET_UPDATES_FAILED_ERROR_CODE = 704;
    $scope.FBM_ADMIN_VERIFICATION_FAILED = 720;

    $scope.loading = {
        verifyTelegram: false,
        verifyFbm: false,
        storeTelegramToken: false,
        generateFbmToken: false,
        settingClient: false
    };

    $scope.data = {
        messagingClient: "telegram"
    };

    $scope.errors = {
        telegramVerificationError: false,
        fbmVerificationError: false
    };

    $scope.telegramInvalidated = false;
    $scope.telegramVerified = false;
    $scope.fbmInvalidated = false;
    $scope.fbmVerified = false;

    $scope.toggleTelegramConfig = function (state) {
        $scope.editTelegramConfig = state;
    };

    $scope.toggleFbmConfig = function (state) {
        $scope.editFbmConfig = state;
    };

    // This is a hack to get the input data from the PHP options array
    $timeout(function () {
        $scope.data.messagingClient = angular.element('#selected-client-input').val();
        $timeout(function () {
            $scope.fbmToken = angular.element('#fbm-token-input').val();
        });
    });

    $scope.clientSelected = function (client) {
        $timeout(function () {
            $scope.fbmToken = angular.element('#fbm-token-input').val();
        });

        $scope.data.messagingClient = client;

        $scope.loading.settingClient = true;
        Api.setSelectedClient(client)
            .then(function (response) {
                console.log('setSelectedClient', response);
            }, function (error) {
                console.warn('setSelectedClient', error)
            }).finally(function () {
                $scope.loading.settingClient = false;
            });

        console.log('client', client);
    };

    /**
     * Verify the Telegram token and email
     */
    $scope.verifyTelegram = function () {
        console.log("Verify telegram");
        $scope.telegramInvalidated = true;

        $scope.loading.verifyTelegram = true;
        $scope.errors.telegramVerificationError = false;
        $scope.telegramVerified = false;

        Api.verifyTelegram().then(function (response) {
            console.log("VERIFY TELEGRAM RES:", response);
            $scope.telegramVerified = true;
        }, function (error) {
            console.warn("TELEGRAM VERIFICATION FAILED:", error);
            if (error.data && error.data.data && error.data.data.errorCode) {
                $scope.errors.telegramVerificationError = error.data.data.errorCode;
            }
            else {
                $scope.errors.telegramVerificationError = $scope.UNKNOWN_ERROR_CODE;
            }
        }).finally(function () {
            $scope.loading.verifyTelegram = false;
        });
    };


    /**
     * Store the Telegram token in the database
     */
    $scope.tokenStored = false;
    $scope.storeTelegramToken = function (token) {

        if (!$scope.mcbGeneralSettingsForm.$valid || !token || token.length < 1) {
            $scope.errors.telegramTokenStoreError = $scope.INVALID_TOKEN_ERROR_CODE;
            return;
        }

        $scope.telegramInvalidated = true;

        console.log("Store Telegram token", token);

        $scope.tokenStored = false;
        $scope.loading.storeTelegramToken = true;
        $scope.errors.telegramTokenStoreError = false;

        Api.storeTelegramToken(token).then(function (response) {
            console.log("TELEGRAM TOKEN STORE RES:", response);
            $scope.tokenStored = true;
        }, function (error) {
            console.warn("TELEGRAM TOKEN STORE FAILED:", error);
        }).finally(function () {
            $scope.loading.storeTelegramToken = false;
        });
    }

    /* ------------- FBM ----------------- */

    /**
     * Verify (connect) Facebook Messenger to this site
     */
    $scope.fbmVerified = false;
    $scope.verifyFbm = function () {
        console.log("Verify facebook messenger");
        $scope.errors.fbmVerificationError = false;
        $scope.fbmInvalidated = true;
        $scope.loading.verifyFbm = true;
        Api.verifyFbm().then(function (response) {
            console.log('VERIFY FBM RES:', response);
            $scope.fbmVerified = true;
        }, function (error) {
            console.warn("FBM VERIFICATION FAILED:", error);
            $scope.errors.fbmVerificationError = $scope.FBM_ADMIN_VERIFICATION_FAILED;
        }).finally(function () {
            $scope.loading.verifyFbm = false;
        });
    };

    /**
     * Generate Facebook messenger token
     * @param strings
     */
    $scope.generateFbmToken = function (strings) {

        function generate() {
            $scope.loading.generateFbmToken = true;
            Api.generateFbmToken()
                .then(function (response) {
                    var token = response.data.token;

                    console.log('FBM token:', token);
                    $scope.fbmToken = token;
                }, function (error) {
                    console.warn("FBM TOKEN GENERATION FAILED:", error);
                }).finally(function () {
                    $scope.loading.generateFbmToken = false;
                })
        }

        if ($scope.fbmToken) {
            SweetAlert.swal({
                    title: MCB_JS_STRINGS['GENERAL.ARE_YOU_SURE'],
                    text: MCB_JS_STRINGS['ADMIN_FBM.GENERATE_TOKEN_WARNING.BODY'],
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: MCB_JS_STRINGS['GENERAL.YES_DO_IT'],
                    cancelButtonText: MCB_JS_STRINGS['GENERAL.CANCEL'],
                    closeOnConfirm: true
                },
                function (confirm) {
                    if (confirm) {
                        generate()
                    }
                });
        }
        else {
            generate();
        }


    }

});

mcbAdminApp.controller('HowToUseCtrl', function ($scope) {
    $scope.test = 'how-to-se';
});


mcbAdminApp.factory('Api', function ($http, $httpParamSerializerJQLike) {

    var wpRequest = function (action, data) {

        // Fetched globally from WP
        var url = ajaxurl;

        var payload = {
            data: data,
            action: action
        };

        return $http({
            url: url,
            method: 'POST',
            data: $httpParamSerializerJQLike(payload), // Make sure to inject the service you choose to the controller
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded' // Note the appropriate header
            }
        });
    };

    return {
        verifyTelegram: function () {
            return wpRequest('mcb_verify_telegram');
        },
        verifyFbm: function () {
            return wpRequest('mcb_verify_fbm');
        },
        storeTelegramToken: function (token) {
            return wpRequest('mcb_store_telegram_token', token);
        },
        generateFbmToken: function () {
            return wpRequest('mcb_generate_fbm_token');
        },
        setSelectedClient: function (client) {
            return wpRequest('mcb_select_client', {client: client});
        }
    }
});