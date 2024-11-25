angular.module('configuracion', ['ngRoute', 'ngSanitize', 'ngResource', 'ui.bootstrap'])
    .config(function ($interpolateProvider) {
        $interpolateProvider.startSymbol('[[').endSymbol(']]');
    });