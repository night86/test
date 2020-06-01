<div id="map_lab_tariff" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ "Map lab tariff code to Signadens tariff code"|t }}</h4>
            </div>
            <div class="modal-body">
                <div id="singleFieldForm">
                    <div class="form-group">
                        <label for="name">{{ "Signadens tariff"|t }}:</label>
                        <p id="signa_tariff"></p>
                    </div>
                    <div class="form-group">
                        <label for="name">{{ "Lab tariff code"|t }}:</label>
                    </div>
                    <div class="form-group">
                        <select id="lab_tariff_id" class="select2-input">
                            <option></option>
                            {% for tariff in signaTariffs %}
                                {% if tariff.organisation_id is currentUser.getOrganisationId() and tariff.signa_tariff_id is null %}
                                    <option value="{{ tariff.id }}">{{ tariff.code }} - {{ tariff.description }}</option>
                                {% endif %}
                            {% endfor %}
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default cancel-button" data-dismiss="modal">{{ "Cancel"|t }}</button>
                <button type="button" class="btn btn-primary confirm-map-tariff" disabled="disabled">{{ "Save"|t }}</button>
            </div>
        </div>
    </div>
</div>