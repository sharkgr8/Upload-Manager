/*global angular */
'use strict';

function fustyFlowFactory(opts) {
    var flow = new Flow(opts);
    if (flow.support) {
        return flow;
    }
    return new FustyFlow(opts);
}

/**
 * The main app module
 * @name app
 * @type {angular.Module}
 */
var app = angular.module('audioApp', ['flow','ajoslin.promise-tracker'], function ($interpolateProvider) {
    //To accomodate Laravel Blade Template
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');

})
    .controller('uploadCtrl', ['$scope', '$http', 'promiseTracker', '$timeout', function($scope, $http, promiseTracker, $timeout){
        $scope.uploader = {};
        $scope.uploadFrm = {};
        $scope.uploadFrm.filenames = [];
        $scope.uploadFrm.titles  = [];
        $scope.uploadFrm.tags  = [];
        
        // Inititate the promise tracker to track form submissions.
        $scope.uploadFrm.progress = promiseTracker();

    // Form submit handler.
    $scope.uploadFrm.submit = function(form) {
      // Trigger validation flag.
      $scope.uploadFrm.submitted = true;

      // If form is invalid, return and let AngularJS show validation errors.
      if (form.$invalid) {
        return;
      }

      // Default values for the request.
      var params = {                
        'filenames' : $scope.uploadFrm.filenames,
        'titles' : $scope.uploadFrm.titles,
        'tags' : $scope.uploadFrm.tags       
      };

      // Perform JSONP request.
      var $promise = $http.post('saveToDB', params)
        .success(function(data, status, headers) {
          if (data.status == 'OK') {
            $scope.uploadFrm.filenames = [];
            $scope.uploadFrm.titles  = [];
            $scope.uploadFrm.tags  = [];
            $scope.uploadFrm.messages = 'Your form has been sent!';
            $scope.uploadFrm.submitted = false;
          } else {
            $scope.uploadFrm.messages = 'Oops, we received your request, but there was an error processing it.';
            console.log(data);
          }
        })
        .error(function(data, status, headers) {
          $scope.uploadFrm.progress = data;
          $scope.uploadFrm.messages = 'There was a network error. Try again later.';
          console.log(data);
        })
        .finally(function() {
          // Hide status messages after three seconds.
          $timeout(function() {
            $scope.uploadFrm.messages = null;
          }, 3000);
        });

      // Track the request and show its progress to the user.
      $scope.uploadFrm.progress.addPromise($promise);
  };
//        $scope.uploadFrm.submitTheForm = function(item, event) {
//          console.log("--> Submitting form");
//          var dataObject = {
//             filenames : $scope.uploadFrm.filenames
//             ,titles  : $scope.uploadFrm.titles
//             ,tags : $scope.uploadFrm.tags   
//          };
//
//          var responsePromise = $http.post("saveToDB", dataObject, {});
//          responsePromise.success(function(dataFromServer, status, headers, config) {
//             console.log(dataFromServer);
//          });
//           responsePromise.error(function(data, status, headers, config) {
//             alert("Submitting form failed!");
//          });
//        }

        
        $scope.cancelUpload = function(files, bulk) {
            var filenames = [];
            var flowObj = $scope.uploader.flow;

            for (var i=0; i < files.length; i++) {
                filenames.push(files[i].name);
            }

            $http.post('removeFiles', {filenames:filenames}).
                success(function(data, status, headers, config) {
                    // this callback will be called asynchronously
                    // when the response is available
                    if(bulk)
                        return flowObj.cancel();
                    else
                        return files[0].cancel();
                }).
                error(function(data, status, headers, config) {
                    // called asynchronously if an error occurs
                    // or server returns response with an error status.
                    return 'Can\'t remove files from transfers queue';
                });
        }

        $scope.$on('flow::fileAdded', function (event, $flow, flowFile) {
            var filename = flowFile.name;
            var d = new Date();
            var extension = filename.substr(filename.lastIndexOf('.')) || '';
            filename = filename.substr(0, filename.lastIndexOf('.')) || filename;
            filename = filename.replace(/(\s|[^a-zA-Z0-9])/g, "-");
            filename = filename+'-'+ d.getDate()+d.getMonth()+d.getFullYear()+d.getHours()+d.getMinutes()+d.getSeconds()+extension;
            flowFile.name = filename;
            return true;
        });

        $scope.$on('flow::fileSuccess', function (event, $flow, flowFile) {
            console.log(flowFile);
        });
    }])

    .config(['flowFactoryProvider',  function (flowFactoryProvider) {
        // Can be used with different implementations of Flow.js
        flowFactoryProvider.factory = fustyFlowFactory;

        flowFactoryProvider.defaults = {
            target: 'uploadServer',
            permanentErrors: [404, 500, 501],
            maxChunkRetries: 1,
            chunkRetryInterval: 5000,
            simultaneousUploads: 4,
            chunkSize: 1024 * 1024,
            testChunks: false,
            throttleProgressCallbacks: 0
        };
        flowFactoryProvider.on('catchAll', function (event) {
            console.log('catchAll', arguments);

        });


    }])
    .filter('bytes', function () {
        return function (bytes, precision) {
            if (isNaN(parseFloat(bytes)) || !isFinite(bytes) || bytes === 0) return '-';
            if (typeof precision === 'undefined') precision = 1;
            var units = ['bytes', 'kB', 'MB', 'GB', 'TB', 'PB'],
                number = Math.floor(Math.log(bytes) / Math.log(1024));
            return (bytes / Math.pow(1024, Math.floor(number))).toFixed(precision) + ' ' + units[number];
        }
    });