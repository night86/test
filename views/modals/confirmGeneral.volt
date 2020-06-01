<div id="{{ id }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ title|t }}</h4>
            </div>
            <div class="modal-body">
                {% if content is defined %}
                {{ content|t }}
                {% endif %}
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-primary confirm-button {{ additionalClass }}">{% if confirmButton is defined %}{{ confirmButton|t }}{% else %}{{ "Confirm"|t }}{% endif %}</button>
                <button type="button" class="btn btn-default cancel-button"
                        data-dismiss="modal">{% if cancelButton is defined %}{{ cancelButton|t }}{% else %}{{ "No"|t }}{% endif %}</button>
            </div>
        </div>
    </div>
</div>