<div class="modal fade" id="modal-template" tabindex="-1" role="dialog" aria-labelledby="modal-template-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><span id="modal-template-title"></span></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" title="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="mTIdLocation"><b>Ubicación:</b></label>
                            <select name="mTIdLocation" id="mTIdLocation" class="form-control" disabled>
                                <option value="1">Tlaquiltenango</option>
                                <option value="2">Zacatepec</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="mBEstatus"><b>* Estatus del Paquete:</b></label>
                            <select name="mBEstatus" id="mBEstatus" class="form-control">
                                <option value="99">Selecciona</option>
                                <option value="1">Nuevo</option>
                                <option value="2">Mensaje Enviado (Recordatorio)</option>
                                <option value="5">Confirmados (Recordatorio)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                            <div class="form-group">
                                <label for="mbIdCatParcel"><b>Paquetería:</b></label>
                                <select name="mbIdCatParcel" id="mbIdCatParcel" class="form-control">
                                    <option value="99">TODAS</option>
                                    <option value="1">J&T</option>
                                    <option value="2">IMILE</option>
                                    <option value="3">CNMEX</option>
                                </select>
                            </div>
                        </div>
                </div>
                <div class="row">
                    <div class="col-md-9">
                       <div class="row">
    <div class="col-md-12">
        <h5><b>Llenar Campos de la Plantilla:</b></h5>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="field2">Ubicacion ({{2}}):</label>
            <input type="text" class="form-control" id="field2" placeholder="Ej: Tlaquiltenango">
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="field3">Hora Hoy ({{3}}):</label>
            <input type="time" class="form-control" id="field3">
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="field4">Hora Mañana ({{4}}):</label>
            <input type="time" class="form-control" id="field4">
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="field5">Fecha Devolución ({{5}}):</label>
            <input type="date" class="form-control" id="field5">
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="field6">Hora Devolución ({{6}}):</label>
            <input type="time" class="form-control" id="field6">
        </div>
    </div>

</div>

                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="mBListTelefonos"><b>* Telefonos (Excel):</b></label>
                            <textarea class="form-control" id="mBListTelefonos" name="mBListTelefonos" rows="6"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mt-3">
        <h5><b>Vista Previa:</b></h5>
            <div id="preview-template" style="white-space: pre-line; border:1px solid #ccc; padding:10px; border-radius:5px;">
                Hola usuario_db,
                Tu pedido ya está listo para recoger en {{2}}.
                Puedes recogerlo hoy hasta las {{3}} o mañana antes de las {{4}}.  
                De lo contrario, será devuelto el {{5}} a las {{6}}.
                Si no podrás recogerlo hoy, confirma tu pedido enviando tu identificación oficial con fotografía.
                Te compartimos los datos de tu pedido: folios_db.
                ¡Nos vemos pronto!
            </div>
        </div>
            <div class="modal-footer">
                <button id="btn-send-template" type="button" class="btn btn-success" title="Guardar">Enviar</button>
                <button type="button" class="btn btn-danger" title="Cerrar" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>