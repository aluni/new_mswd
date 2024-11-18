angular.module('institucionesApp', [
    'configuracion',
    'institucionesControllers'
]).factory('Instituciones', function ($resource) {
    return $resource(institucionesREST + '/:institucionId', {entradasId: '@id'}, {update: {method: 'PUT'}});
});