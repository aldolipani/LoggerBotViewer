(function() {
	var app = angular.module('loggerBot', []);
	
	app.controller('usersCtrl', function($scope, $http) {
		var users = this;
		users.emails = [];
		users.userlogs = [];
		
		$http.get("http://kronos.ifs.tuwien.ac.at:8081/userlist").success(function(response) {
			$scope.users.emails = response;
		});
		
		$scope.showAll = function() { 
			$http.get("http://kronos.ifs.tuwien.ac.at:8081/log").success(function(response) {
				users.userlogs = response;
			});
		};
		
		$scope.show = function(email) {
			$http.get("http://kronos.ifs.tuwien.ac.at:8081/log?user=" + email).success(function(response) {
				users.userlogs = response;
			});
		};
	});
})();