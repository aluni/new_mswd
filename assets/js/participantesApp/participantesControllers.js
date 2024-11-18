angular.module('participantesControllers', [])
        .controller('Participantes', function ($scope, $filter, $http, Participantes, Sorteos) {
            $scope.filtro = new Object();
            $scope.filtroext = new Object();
            $scope.ganadores = new Array();
            $scope.filtrado = function (listado) {
                var listado_aux = $filter('filter')(listado, $scope.filtro);
                listado_aux = $filter('premiados')(listado_aux, $scope.filtroext.premiado);
                listado_aux = $filter('checkeadosInstitucion')(listado_aux, $scope.filtroext.institucionId);
                return listado_aux;
            };
            $scope.participantes = Participantes.query(
                    function () {
                        paginacion($scope, $scope.participantes, 10, 'numero_entrada');
                        $('#mensajeEspera').hide();
                        $('.seccionAngular').show();
                    });

            $scope.marcarAsistencia = function (participante) {
                participante.asistido = true;
                var d = new Date();
                participante.hora_entrada = (d.toTimeString()).substr(0, 8);
                Participantes.update({id: participante.id}, participante, function (response) {
                    $('#mensajeExito').html('¡Asistencia marcada!');
                    $('#mensajeExito').fadeIn(500).delay(1500).fadeOut(500);
                    $scope.filtro.numero_entrada = '';
                });
            };

            $scope.guardarGanador = function (participante) {
                Participantes.update(participante, function () {
                    $('#mensajeExito').html('¡Ganador guardardo correctamente!');
                    $('#mensajeExito').fadeIn(500).delay(1500).fadeOut(500);
                });
            };

            $scope.desmarcarAsistencia = function (participante) {
                participante.asistido = false;
                Participantes.update({id: participante.id}, participante, function () {
                    $('#mensajeExito').html('¡Asistencia desmarcada!');
                    $('#mensajeExito').fadeIn(500).delay(1500).fadeOut(500);
                });
            };

            $('input').keypress(function (e) {
                var code = e.keyCode || e.which;
                if ((code === 13) && ($scope.filtradoLong === 1)) {
                    var participante = $scope.filtrado($scope.participantes)[0];
                    $scope.marcarAsistencia(participante);
                }
            });

            $scope.sorteoVacio = function (sorteo) {
                return variableVacia(sorteo) || variableVacia(sorteo.nombre) || variableVacia(sorteo.cantidad) ||
                        variableVacia(sorteo.condicion) || variableVacia(sorteo.institucion);
            };

            $scope.refrescarParticipantes = function () {
                $scope.participantes = Participantes.query(
                        function () {
                            paginacion($scope, $scope.participantes, 10, 'numero_entrada');
                        });
            };

            $scope.guardarGanadores = function (ganadores, sorteo) {
                sorteo.participantes = ganadores;
                Sorteos.save(sorteo, function () {
                    $scope.refrescarParticipantes();
                    $('#ganadores').modal('hide');
                    $('#mensajeExito').html('¡Ganadores guardados correctamente!');
                    $('#mensajeExito').fadeIn(500).delay(2000).fadeOut(500);
                    $scope.ganadores = [];
                });
            };

            $scope.anadirGanador = function (ganadores, cantidad) {
                for (let i = 1; i <= cantidad; i++) {
                    ganadores.push('');
                }
            };

            $scope.lanzarSorteo = function (sorteo) {
                $scope.ganadores = $filter('ganadores')($scope.participantes, sorteo);
                $('#ganadores').modal('show');
//                $scope.participantes = Participantes.query(
//                        function () {
//                            $scope.ganadores = $filter('ganadores')($scope.participantes, sorteo);
//                            $('#ganadores').modal('show');
//                        });
            };
            $scope.checkearParticipante = function (email) {
                $http.put(checkearParticipanteURL + '/' + email)
                        .then(function (response) {
                            $('#mensajeExito').html(response.data);
                            $('#mensajeExito').fadeIn(500).delay(2000).fadeOut(500);
                            $scope.refrescarParticipantes();
                        }, function (response) {
                            $('#mensajeError').html(response.data);
                            $('#mensajeError').fadeIn(500).delay(2000).fadeOut(500);
                        });
            };

        });