var comprobarOtro = function () {
    if ($('#comoConoce').val() === 'Others') {
        $('#comoConoceOtro').show();
    } else {
        $('#comoConoceOtro').hide();
    }
};

var comoConoce = function () {
    if ($('#comoConoce').val() === 'Others') {
        return $('#comoConoceOtro').val();
    } else {
        return $('#comoConoce').val();
    }
};
$('#formularioInscripcion').submit(function (event) {
    event.preventDefault();
    var data = new Object;
    $('#guardarInscripcion').prop("disabled", true);
    $('#guardarInscripcion').html('<i class="fa fa-circle-o-notch fa-spin"></i> <b>Guardando... <span class="inverso">| Saving...</span></b>');
    data.nombre = $('#nombre').val();
    data.apellidos = $('#apellidos').val();
    data.sexo = $('#sexo').val();
    data.email = $('#email').val();
    data.universidad = $('#universidad').val();
    data.comoConoce = comoConoce();
    data.nacionalidad = $('#nacionalidad').val();
//    data.anoNacimiento = $('#anoNacimiento').val();
    data.observaciones = $('#observaciones').val();
    data.asistido = (typeof asistido === 'undefined')?'0':'1';
    data.participaSorteos = ($('#aceptarSorteos').is(':checked'))?'1':'0';
    $.post(urlGuardarInscripcion, data)
            .done(function (response) {
                $('#mensajeExito').html(response);
                $('#mensajeExito').fadeIn(500);
                $('#guardarInscripcion').prop("disabled", false);
                $('#guardarInscripcion').html('<b>Guardar inscripción <span class="inverso">| Save form</span></b>');
            })
            .fail(function (response) {
                $('#mensajeError').html("El email introduccido ya está en uso, busca en tu correo el mail con las instrucciones para descargar tu ticket de entrada");
                $('#mensajeError').fadeIn(500).delay(10000).fadeOut(500);
                $('#guardarInscripcion').prop("disabled", false);
                $('#guardarInscripcion').html('<b>Guardar inscripción <span class="inverso">| Save form</span></b>');
            });
});