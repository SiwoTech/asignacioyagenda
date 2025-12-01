/**
 * Módulo de Asignación de Agenda para Operadores
 * 
 * Este script maneja la lógica del modal para asignar agenda a operadores.
 * Incluye validación frontend, llamadas AJAX y actualización de UI.
 */

(function($) {
    'use strict';
    
    // Variables globales del módulo
    var currentOperatorId = null;
    var currentOperatorName = null;
    var isProcessing = false;
    
    /**
     * Inicializa el modal HTML en el DOM
     */
    function initModal() {
        // Verificar si el modal ya existe
        if ($('#modalAsignarAgenda').length > 0) {
            return;
        }
        
        var modalHtml = `
            <div class="modal fade" id="modalAsignarAgenda" tabindex="-1" aria-labelledby="modalAsignarAgendaLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="modalAsignarAgendaLabel">Asignar Agenda al Operador</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div id="agendaAlertContainer"></div>
                            
                            <div class="mb-3">
                                <span class="operator-info-badge" id="operatorInfoBadge"></span>
                            </div>
                            
                            <form id="formAsignarAgenda">
                                <input type="hidden" id="agenda_operator_id" name="operator_id">
                                <input type="hidden" id="agenda_operator_name" name="operator_name">
                                
                                <div class="mb-3">
                                    <label class="form-label" for="agenda_date">Fecha *</label>
                                    <input type="date" class="form-control" id="agenda_date" name="date" required>
                                </div>
                                
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="agenda_start_time">Hora inicio *</label>
                                            <input type="time" class="form-control" id="agenda_start_time" name="start_time" required>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="agenda_end_time">Hora fin</label>
                                            <input type="time" class="form-control" id="agenda_end_time" name="end_time">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label" for="agenda_subject">Asunto / Nota *</label>
                                    <input type="text" class="form-control" id="agenda_subject" name="subject" 
                                           placeholder="Descripción del servicio o tarea" required maxlength="255">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label optional-label" for="agenda_location">Ubicación</label>
                                    <input type="text" class="form-control" id="agenda_location" name="location" 
                                           placeholder="Hotel o lugar de trabajo" maxlength="255">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label optional-label" for="agenda_notes">Notas adicionales</label>
                                    <textarea class="form-control" id="agenda_notes" name="notes" rows="2" 
                                              placeholder="Observaciones o comentarios"></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="agenda_centro">Centro de trabajo</label>
                                            <select class="form-control" id="agenda_centro" name="centro">
                                                <option value="Cancun">Cancún</option>
                                                <option value="Playa">Playa del Carmen</option>
                                                <option value="Xcaret">Xcaret</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="agenda_color">Color</label>
                                            <select class="form-control" id="agenda_color" name="color">
                                                <option value="#E55B26" style="color:#E55B26;">Naranja (CWO)</option>
                                                <option value="#FFD700" style="color:#FFD700;">Amarillo</option>
                                                <option value="#0071c5" style="color:#0071c5;">Azul Turquesa</option>
                                                <option value="#FF4500" style="color:#FF4500;">Naranja Rojo</option>
                                                <option value="#228B22" style="color:#228B22;">Verde</option>
                                                <option value="#8B0000" style="color:#8B0000;">Rojo</option>
                                                <option value="#A020F0" style="color:#A020F0;">Púrpura</option>
                                                <option value="#1C1C1C" style="color:#1C1C1C;">Negro</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2 mt-3">
                                    <button type="submit" class="btn btn-cwo" id="btnGuardarAgenda">
                                        <span class="btn-text">Guardar Agenda y Asignar</span>
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(modalHtml);
        
        // Vincular eventos
        bindEvents();
    }
    
    /**
     * Vincula los eventos del formulario
     */
    function bindEvents() {
        // Evento de envío del formulario
        $('#formAsignarAgenda').on('submit', function(e) {
            e.preventDefault();
            submitAgenda();
        });
        
        // Limpiar alertas al cerrar el modal
        $('#modalAsignarAgenda').on('hidden.bs.modal', function() {
            clearAlerts();
            clearValidation();
            isProcessing = false;
            updateButtonState(false);
        });
        
        // Establecer fecha mínima como hoy
        var today = new Date().toISOString().split('T')[0];
        $('#agenda_date').attr('min', today);
    }
    
    /**
     * Abre el modal con la información del operador
     * @param {number} operatorId - ID del operador
     * @param {string} operatorName - Nombre del operador
     * @param {string} operatorCentro - Centro de trabajo del operador (opcional)
     */
    window.openAssignModal = function(operatorId, operatorName, operatorCentro) {
        // Inicializar modal si no existe
        initModal();
        
        // Guardar datos del operador
        currentOperatorId = operatorId;
        currentOperatorName = operatorName;
        
        // Limpiar formulario
        $('#formAsignarAgenda')[0].reset();
        clearAlerts();
        clearValidation();
        
        // Establecer valores del operador
        $('#agenda_operator_id').val(operatorId);
        $('#agenda_operator_name').val(operatorName);
        $('#operatorInfoBadge').text('Operador: ' + operatorName);
        
        // Establecer centro de trabajo si se proporciona
        if (operatorCentro && operatorCentro.trim() !== '') {
            $('#agenda_centro').val(operatorCentro);
        }
        
        // Establecer fecha actual como predeterminada
        var today = new Date().toISOString().split('T')[0];
        $('#agenda_date').val(today);
        
        // Mostrar el modal
        var modal = new bootstrap.Modal(document.getElementById('modalAsignarAgenda'));
        modal.show();
    };
    
    /**
     * Valida los campos del formulario
     * @returns {boolean} - true si es válido, false si no
     */
    function validateForm() {
        var isValid = true;
        clearValidation();
        
        // Validar fecha
        var date = $('#agenda_date').val();
        if (!date) {
            showFieldError('agenda_date', 'La fecha es requerida');
            isValid = false;
        }
        
        // Validar hora de inicio
        var startTime = $('#agenda_start_time').val();
        if (!startTime) {
            showFieldError('agenda_start_time', 'La hora de inicio es requerida');
            isValid = false;
        }
        
        // Validar asunto
        var subject = $('#agenda_subject').val().trim();
        if (!subject) {
            showFieldError('agenda_subject', 'El asunto es requerido');
            isValid = false;
        }
        
        // Validar que hora fin sea mayor que hora inicio (si se proporciona)
        var endTime = $('#agenda_end_time').val();
        if (endTime && startTime && endTime < startTime) {
            showFieldError('agenda_end_time', 'La hora de fin debe ser posterior a la hora de inicio');
            isValid = false;
        }
        
        return isValid;
    }
    
    /**
     * Muestra un error en un campo específico
     * @param {string} fieldId - ID del campo
     * @param {string} message - Mensaje de error
     */
    function showFieldError(fieldId, message) {
        var field = $('#' + fieldId);
        field.addClass('is-invalid');
        field.after('<div class="invalid-feedback">' + message + '</div>');
    }
    
    /**
     * Limpia los errores de validación
     */
    function clearValidation() {
        $('#formAsignarAgenda .is-invalid').removeClass('is-invalid');
        $('#formAsignarAgenda .invalid-feedback').remove();
    }
    
    /**
     * Envía los datos de la agenda al servidor
     */
    function submitAgenda() {
        if (isProcessing) {
            return;
        }
        
        if (!validateForm()) {
            return;
        }
        
        isProcessing = true;
        updateButtonState(true);
        clearAlerts();
        
        var formData = {
            operator_id: $('#agenda_operator_id').val(),
            operator_name: $('#agenda_operator_name').val(),
            date: $('#agenda_date').val(),
            start_time: $('#agenda_start_time').val(),
            end_time: $('#agenda_end_time').val() || $('#agenda_start_time').val(),
            subject: $('#agenda_subject').val().trim(),
            notes: $('#agenda_notes').val().trim(),
            location: $('#agenda_location').val().trim(),
            centro: $('#agenda_centro').val(),
            color: $('#agenda_color').val()
        };
        
        $.ajax({
            type: 'POST',
            url: 'api/create_agenda.php',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                isProcessing = false;
                updateButtonState(false);
                
                if (response.success) {
                    showAlert('success', '¡Agenda creada exitosamente para ' + currentOperatorName + '!');
                    
                    // Cerrar modal después de 1.5 segundos
                    setTimeout(function() {
                        var modal = bootstrap.Modal.getInstance(document.getElementById('modalAsignarAgenda'));
                        if (modal) {
                            modal.hide();
                        }
                        
                        // Recargar la tabla de operadores
                        if (typeof cargarTabla === 'function') {
                            cargarTabla();
                        } else {
                            $('#tabla').load('componentes/tabla.php');
                        }
                    }, 1500);
                } else {
                    showAlert('danger', response.error || 'Error al crear la agenda.');
                }
            },
            error: function(xhr, status, error) {
                isProcessing = false;
                updateButtonState(false);
                
                var errorMessage = 'Error al procesar la solicitud.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.status === 400) {
                    errorMessage = 'Datos inválidos. Verifique los campos.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Error del servidor. Intente nuevamente.';
                }
                
                showAlert('danger', errorMessage);
            }
        });
    }
    
    /**
     * Actualiza el estado del botón de envío
     * @param {boolean} loading - true si está cargando
     */
    function updateButtonState(loading) {
        var btn = $('#btnGuardarAgenda');
        var btnText = btn.find('.btn-text');
        var spinner = btn.find('.spinner-border');
        
        if (loading) {
            btn.prop('disabled', true);
            btnText.text('Guardando...');
            spinner.removeClass('d-none');
        } else {
            btn.prop('disabled', false);
            btnText.text('Guardar Agenda y Asignar');
            spinner.addClass('d-none');
        }
    }
    
    /**
     * Muestra una alerta en el contenedor de alertas
     * @param {string} type - Tipo de alerta (success, danger, warning, info)
     * @param {string} message - Mensaje a mostrar
     */
    function showAlert(type, message) {
        var alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        `;
        
        $('#agendaAlertContainer').html(alertHtml);
    }
    
    /**
     * Limpia las alertas del contenedor
     */
    function clearAlerts() {
        $('#agendaAlertContainer').empty();
    }
    
    // Inicializar cuando el documento esté listo
    $(document).ready(function() {
        initModal();
    });
    
})(jQuery);
