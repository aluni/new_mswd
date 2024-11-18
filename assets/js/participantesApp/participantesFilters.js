angular.module('participantesFilters', [])
        .filter('ganadores', function ($filter) {
            return function (participantes, sorteo) {
                /**
                 * Comienzo aleatorio del sorteo, un número entre 1 y 100000 que se
                 * comparará con el número de ticket de los asistentes
                 */
                var comienzo = getRandomInt(0, 99999);
                var count = 0;
                var ganadores = new Array();
                var ids = new Array();
                var participantesValidos = $.grep(participantes, function (participante, key) {
                    return participante.asistido == '1';
                });
                if (!variableVacia(sorteo.hora_inicio) && !variableVacia(sorteo.hora_fin)) {
                    participantesValidos = $.grep(participantesValidos, function (participante, key) {
                        return (participante.hora_entrada > sorteo.hora_inicio) &&
                               (participante.hora_entrada < sorteo.hora_fin);
                    });
                }
                if (sorteo.condicion == 'exclusivo') {
                    participantesValidos = $filter('checkeadosInstitucion')(participantesValidos, sorteo.institucion.id);
                } else if (sorteo.condicion == 'min_checkeos') {
                    participantesValidos = $.grep(participantesValidos, function (participante, key) {
                        return participante.checkeos.length >= 5;
                    });
                }
                var cantidad = Math.min(sorteo.cantidad, participantesValidos.length);
                while (count < cantidad) {
                    var ganadoresSup = $.grep(participantesValidos, function (participante, key) {
                        /**
                         * El participante gana si:
                         * su número de ticket por encima de comienzo aleatorio y 
                         * aún faltan premios por repartir y
                         * no está ya en la lista de ganadores de este sorteo y
                         * en un número aleatorio del 1 al 10 sale un 5
                         */
                        var gana = (participante.sorteos.length === 0) &&
                                (parseInt(participante.numero_entrada) > comienzo) &&
                                (count < sorteo.cantidad) &&
                                (ids.indexOf(participante.id) == -1) &&
                                (getRandomInt(0, 99) == 5);
                        if (gana) {
                            ids.push(participante.id);
                            count++;
                        }
                        return gana;
                    });
                    ganadores = ganadores.concat(ganadoresSup);
                    if (count < sorteo.cantidad) {
                        /**
                         * Si en el el listado de todos los asistentes cuyo número de ticket 
                         * es superior a 'comienzo' no han salido suficientes ganadores, se empieza 
                         * a mirar a aquellos cuyo número de ticket es inferior a dicho 'comienzo'
                         */
                        var ganadoresInf = $.grep(participantesValidos, function (participante, key) {
                            var gana = (participante.sorteos.length === 0) &&
                                    (parseInt(participante.numero_entrada) < comienzo) &&
                                    (count < sorteo.cantidad) &&
                                    (ids.indexOf(participante.id) == -1) &&
                                    (getRandomInt(0, 19) == 5);
                            if (gana) {
                                ids.push(participante.id);
                                count++;
                            }
                            return gana;
                        });
                        ganadores = ganadores.concat(ganadoresInf);
                    }
                }
                return ganadores;
            };
        })
        .filter('premiados', function () {
            return function (participantes, premiado) {
                if (!variableVacia(premiado)) {
                    if (premiado == '0') {
                        return $.grep(participantes, function (participante, key) {
                            return participante.sorteo == '';
                        });
                    } else {
                        return $.grep(participantes, function (participante, key) {
                            return participante.sorteo != '';
                        });
                    }
                }
                return participantes;
            }
        })
        .filter('checkeadosInstitucion', function () {
            return function (participantes, institucionId) {
                var participantesCheckeados = participantes;
                if (!variableVacia(institucionId)) {
                    participantesCheckeados = $.grep(participantes, function (participante, key) {
                        var checkeado = false;
                        if (!variableVacia(participante.checkeos)) {
                            $.each(participante.checkeos, function (key, checkeo) {
                                if (checkeo.institucion.id == institucionId) {
                                    checkeado = true;
                                    return false;
                                }
                            });
                        }
                        return checkeado;
                    });
                }
                return participantesCheckeados;
            };
        });