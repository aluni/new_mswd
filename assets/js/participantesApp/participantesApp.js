angular.module('participantesApp', [
    'configuracion',
    'participantesFilters',
    'participantesControllers'
]).factory('Participantes', function ($resource) {
    return $resource(participantesREST + '/:id', null, {update: {method: 'PUT'}});
}).factory('Sorteos', function ($resource) {
    return $resource(sorteosREST + '/:id', null, {update: {method: 'PUT'}});
});