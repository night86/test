<div id="add-new-status-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    {% if statusId is defined %}
                        {{ "Edit new status"|t }}
                    {% else %}
                        {{ "Add new status"|t }}
                    {% endif %}
                </h4>
            </div>
            <div class="modal-body">
                <form>
                    <label for="statusName">{{ "Type name for new status"|t }}</label>
                    <input type="hidden" id="statusId" name="statusId"
                           value="{% if statusId is defined %}{{ statusId }}{% endif %}">
                    <input type="text" id="statusName" name="statusName" class="form-control"
                           value="{% if statusName is defined %}{{ statusName }}{% endif %}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">{{ "Save"|t }}</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ "Close"|t }}</button>
            </div>
        </div>
    </div>
</div>