<div id="{{ id }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ title|t }}</h4>
            </div>
            <div class="modal-body">
                <p>{{ content|t }}</p>
                <form id="singleFieldForm">
                    <div class="form-group">
                        <label for="name">{{ "Name"|t }}:</label>
                        {{ text_field("editName", "class" : "form-control") }}
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="confirmEditButton" type="button"
                        class="btn btn-primary confirm-edit-button">{% if confirmButton is defined %}{{ confirmButton|t }}{% else %}{{ "Confirm"|t }}{% endif %}</button>
                <button type="button" class="btn btn-default cancel-button"
                        data-dismiss="modal">{{ "Cancel"|t }}</button>
            </div>
        </div>
    </div>
</div>