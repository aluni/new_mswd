angular.module('blogFilters', [])
        .filter('htmlToPlaintext', function() {
            return function(text) {
                return String(text).replace(/<[^>]+>/gm, '');
            };
        })
        .filter('tieneEtiquetas', function() {
            return function(entradas, labels) {
                if (variableVacia(labels) || variableVacia(entradas)) {
                    return entradas;
                }
                var arrayFiltrado = new Array();
                $.each(entradas, function(key, entrada) {
                    if (!variableVacia(entrada.labels)) {
                        var contiene = true;
                        $.each(labels, function(key, label) {
                            var index = entrada.labels.indexOf(label);
                            if (index === -1) {
                                contiene = false;
                                return false;
                            }
                        });
                    }
                    if (contiene) {
                        arrayFiltrado.push(entrada);
                    }
                });
                return arrayFiltrado;
            };
        });