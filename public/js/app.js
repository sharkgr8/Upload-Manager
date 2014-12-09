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
var app = angular.module('audioApp', ['flow'], function ($interpolateProvider) {
    //To accomodate Laravel Blade Template
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');

})
    .controller('uploadCtrl', ['$scope', '$http', function($scope, $http){
        $scope.uploader = {};

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
            console.log($flow);
            var filename = flowFile.name;
            var d = new Date();
            var extension = filename.substr(filename.lastIndexOf('.')) || '';
            filename = filename.substr(0, filename.lastIndexOf('.')) || filename;
            filename = filename.replace(/(\s|[^a-zA-Z0-9])/g, "-");
            filename = filename+'-'+ d.getDate()+d.getMonth()+d.getFullYear()+d.getHours()+d.getMinutes()+d.getSeconds()+extension;
            flowFile.name = filename;
            return true;
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