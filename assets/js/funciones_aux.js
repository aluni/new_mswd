function paginacion($scope, listado, tamanoPag, ordenar) {
    $scope.descendente = false;
    $scope.tamanoPag = tamanoPag;
    $scope.pagActual = 1;
    $scope.ordenar = ordenar;
    $scope.numPags = function () {
        if (!variableVacia(listado)) {
            $scope.filtradoLong = $scope.filtrado(listado).length;
            var numPags = Math.ceil($scope.filtradoLong / $scope.tamanoPag);
            if (numPags === 0)
                numPags = 1;
        } else {
            $scope.filtradoLong = 0;
            var numPags = 1;
        }
        if ($scope.pagActual > numPags) {
            $scope.pagActual = numPags;
        }
        return numPags;
    };
}

function variableVacia(variable) {
    return(typeof (variable) === 'undefined' || variable === null || variable === '' || variable.length === 0);
}

angular.module('configuracion', ['ngRoute', 'ngSanitize', 'ngResource', 'ui.bootstrap'])
        .config(function ($interpolateProvider) {
            $interpolateProvider.startSymbol('[[').endSymbol(']]');
        });

$('select').change(function () {
    if (variableVacia($(this).val())) {
        $(this).css('color', '#999');
    } else {
        $(this).css('color', 'black');
    }
});

function actualizarSelects() {
    $('select').each(function () {
        if (variableVacia($(this).val())) {
            $(this).css('color', '#999');
        } else {
            $(this).css('color', 'black');
        }
    });

    $('option').each(function () {
        if (variableVacia($(this).val())) {
            $(this).css('color', '#999');
        } else {
            $(this).css('color', 'black');
        }
    });
}

var mq = window.matchMedia("(max-width: 768px)");
if (mq.matches) {
    $('#collapse_consulta').collapse('hide');
    $('#collapse_filtros').collapse('hide');
    $('#collapse_etiquetas').collapse('hide');
    $('#collapse_archivo').collapse('hide');
}

function getRandomInt(min, max) {
    // Create byte array and fill with 1 random number
    const randomBuffer = new Uint32Array(1);
    window.crypto.getRandomValues(randomBuffer);
    let randomNumber = randomBuffer[0] / (0xffffffff + 1);
    return Math.floor(randomNumber * (max - min + 1)) + min;
}

actualizarSelects();