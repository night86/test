<div id="{{ id }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ title|t }}</h4>
            </div>
            <div class="modal-body">
                <p>{{ content|t }}</p>
                <form id="removalProductForm">
                    <div class="form-group">
                        <label>{{ "Starting date"|t }}:</label>
                        {{ text_field('date', 'class': 'form-control datepicker', 'data-date-start-date':'0d', 'required': 'required') }}
                    </div>
                    <div class="form-group">
                        <label for="msg">{{ "Message"|t }}:</label>
                        {{ text_field("msg", "class" : "form-control") }}
                    </div>
                </form>
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
