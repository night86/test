<div id="{{ id }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ title|t }}</h4>
            </div>
            <div class="modal-body">
                <form id="singleFieldForm">
                    <div class="form-group">
                        <label for="name">{{ "Name"|t }}:</label>
                        {{ text_field("personNameEdit", "class" : "form-control isModal", "required" : "required") }}
                    </div>
                    <div class="form-group">
                        <label for="name">{{ "Phone"|t }}:</label>
                        {{ text_field("personPhoneEdit", "class" : "form-control isModal", "required" : "required") }}
                    </div>
                    <div class="form-group">
                        <label for="name">{{ "Email"|t }}:</label>
                        {{ text_field("personEmailEdit", "class" : "form-control isModal", "required" : "required") }}
                    </div>
                    <div class="form-group">
                        <label for="name">{{ "Function"|t }}:</label>
                        {{ text_field("personFunctionEdit", "class" : "form-control isModal", "required" : "required") }}
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="confirmButtonEdit" type="button"
                        class="btn btn-primary">{% if confirmButton is defined %}{{ confirmButton|t }}{% else %}{{ "Confirm"|t }}{% endif %}</button>
                <button type="button" class="btn btn-default cancel-button"
                        data-dismiss="modal">{{ "Cancel"|t }}</button>
            </div>
        </div>
    </div>
</div>