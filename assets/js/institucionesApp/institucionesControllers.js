angular.module('institucionesControllers', []).controller('Instituciones', function ($scope, Instituciones) {
    $scope.instituciones = Instituciones.query(
            function () {
                $('#mensajeEspera').hide();
                $('.seccionAngular').show();
            });
});