<div id="{{ id }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ title|t }}</h4>
            </div>
            <div class="modal-body">
                <form id="singleFieldForm">
                    <div class="form-group">
                        <label for="name">{{ "Content"|t }}:</label>
                        <textarea id="textareaEdit" class="tinymce" style="display: block; min-width: 500px; max-width: 500px; min-height: 200px;"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="confirmEditTextarea" type="button"
                        class="btn btn-primary">{% if confirmButton is defined %}{{ confirmButton|t }}{% else %}{{ "Confirm"|t }}{% endif %}</button>
                <button type="button" class="btn btn-default cancel-button"
                        data-dismiss="modal">{{ "Cancel"|t }}</button>
            </div>
        </div>
    </div>
</div>