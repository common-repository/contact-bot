/**
 * Client side AngularJS app
 */
var mcbApp = angular.module('mcbApp', ['ngAnimate']);

mcbApp.controller('ChatCtrl', function($scope, Api, $timeout) {

	var GET_UPDATES_TIMEOUT = 2000; // In milliseconds
	var INITIAL_MESSAGE_TIMEOUT = 1000; // In milliseconds

	$scope.DEBUG = false;

	$scope.messages = [];

	$timeout(function() {
		$scope.messages.push({src: "admin", text: "Hi there :)"});
	}, INITIAL_MESSAGE_TIMEOUT);

	$scope.submitMessage = function(text, src) {

		// Get message
		var message = {
			src: src,
			text: text
		};

		// Clear input
		$scope.chatMessage = "";
		$scope.messages.push(message);

		Api.sendMessage(message).then(function(response) {
			console.log("RESPONSE:", response);
		}, function(error) {
			console.warn("ERROR:", error);
		});
	};

	/**
	 * Poll the server for messages for this user
	 */
	var poll = function() {
		console.log("Sending getUpdates request...");
		Api.getUpdates().then(function(response) {
			console.log("RESPONSE:", response);
			if (response.data && response.data != 'null') {
				// Map updates to the simple structure
				var updates = _.map(response.data, function(m) {
					return {
						src: m.src,
						text: m.message.text
					};
				});

				$scope.messages = $scope.messages.concat(updates);
			}

		}, function(error) {
			console.warn("ERROR:", error);
		}).finally(function() {
			// Only schedule in production
			if (!$scope.DEBUG) {
				// Schedule the next call
				$timeout(function() {
					poll();
				}, GET_UPDATES_TIMEOUT);
			}
		});
	};

	// Begin polling
	if (!$scope.DEBUG) {
		poll();
	}

	if ($scope.DEBUG) {
		/**
		 * TODO: temp only, remove when done debugging
		 */
		$scope.getUpdates = function() {
			poll();
		};
	}

});

mcbApp.factory('Api', function($http, $httpParamSerializerJQLike) {

	var wpRequest = function(action, data) {

		// Fetched globally from WP
		var url = ajax_object.ajax_url;

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

	var HOST = location.search.indexOf('host=localhost') > -1 ? 'localhost' : 'remote';

	return {
		sendMessage: function(message) {
			message.host = HOST;
			return wpRequest('mcb_handle_message', message);
		},

		getUpdates: function() {
			return wpRequest('mcb_get_updates', { host: HOST });
		}
	}
});

mcbApp.directive('schrollBottom', function () {
	return {
		scope: {
			schrollBottom: "="
		},
		link: function (scope, element) {
			scope.$watchCollection('schrollBottom', function (newValue) {
				if (newValue)
				{
					var el = angular.element(element);
					el.scrollTop(el[0].scrollHeight);
				}
			});
		}
	}
});
