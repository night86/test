<div id="{{ id }}" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ title|t }}</h4>
            </div>
            <div class="modal-body">
                <select id="ledger_select" class="select2-input">
                    <option selected="selected"></option>
                    {% for led in ledgers %}
                    <option value="{{ led.id }}">{{ led.code }} - {{ led.description }}</option>
                    {% endfor %}
                </select>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-primary confirm-button">{% if confirmButton is defined %}{{ confirmButton|t }}{% else %}{{ "Confirm"|t }}{% endif %}</button>
                <button type="button" class="btn btn-default cancel-button"
                        data-dismiss="modal">{{ "Cancel"|t }}</button>
            </div>
        </div>
    </div>
</div>