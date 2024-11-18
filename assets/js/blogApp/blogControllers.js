angular.module('blogControllers', []).controller('Blog', function($scope, $filter, Entradas) {
    $scope.filtro = new Object();
    $scope.filtroaux = new Object();
    $scope.filtroaux.labels = new Array();
    $scope.labels = new Array();
    $scope.conjuntoLabels = new Array();
    $scope.fechaActual = new Date();
    Entradas.query(
            function(response) {
                $scope.entradas = response;
                paginacion($scope, $scope.entradas, 20, '');
                $scope.sacarLabels();
                $('#mensajeEspera').hide();
                $('#listadoAngular').show();
            },
            function(response) {
                manejoErrores(response, $scope);
            });
            
    $scope.anadirEtiqueta = function(etiqueta) {
        var index = $scope.filtroaux.labels.indexOf(etiqueta);
        if (index === -1) {
            $scope.filtroaux.labels.push(etiqueta);
        }
    };
    
    $scope.quitarEtiqueta = function(etiqueta) {
        var index = $scope.filtroaux.labels.indexOf(etiqueta);
        if (index > -1) {
            $scope.filtroaux.labels.splice(index, 1);
        }
    };
    
    $scope.filtrado = function(listado) {
        var listado_aux = $filter('filter')(listado, $scope.filtro);
        listado_aux = $filter('tieneEtiquetas')(listado_aux, $scope.filtroaux.labels);
        return listado_aux;
    };
    
    $scope.fechaAnoPasado = function(mes){
         return new Date($scope.fechaActual.getFullYear() - 1, $scope.fechaActual.getMonth() + mes);
    };
    
    $scope.fechaAnoActual = function(mes){
         return new Date($scope.fechaActual.getFullYear(), mes - 1);
    };

    $scope.sacarLabels = function() {
        $.each($scope.entradas, function(key, entrada) {
            if (!variableVacia(entrada.labels)) {
                $.each(entrada.labels, function(key, label) {
                    var index = $scope.conjuntoLabels.indexOf(label);
                    if (index === -1) {
                        var labelAux = new Object();
                        labelAux.nombre = label;
                        labelAux.contador = 1;
                        $scope.labels.push(labelAux);
                        $scope.conjuntoLabels.push(label);
                    } else {
                        $scope.labels[index].contador++;
                    }
                });
            }
        });
    };
});