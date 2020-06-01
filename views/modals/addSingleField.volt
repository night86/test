<div id="{{ id }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ title|t }}</h4>
            </div>
            <div class="modal-body">
                <p>{{ content|t }} {% if content2 is defined %}<br />{{ content2 }}{% endif %} {% if link is defined %}{{ link }}{% endif %}</p>
                <div class="form-group">
                    <label for="name">{% if label is defined %}{{ label }}{% else %}{{ "Name"|t }}{% endif %}:</label>
                    {{ text_field("newName", "class" : "form-control") }}
                </div>
            </div>
            <div class="modal-footer">
                <button id="confirmButton" type="button"
                        class="btn btn-primary confirm-button {{ additionalClass }}">{% if confirmButton is defined %}{{ confirmButton|t }}{% else %}{{ "Confirm"|t }}{% endif %}</button>
                <button type="button" class="btn btn-default cancel-button"
                        data-dismiss="modal">{{ "Cancel"|t }}</button>
            </div>
        </div>
    </div>
</div>