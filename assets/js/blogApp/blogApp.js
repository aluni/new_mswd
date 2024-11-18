angular.module('aluniBlogApp', [
    'configuracion',
    'blogFilters',
    'blogControllers'
]).factory('Entradas', function ($resource) {
    return $resource(entradasREST + '/:entradasId', {entradasId: '@id'}, {update: {method: 'PUT'}});
});