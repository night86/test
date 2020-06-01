<div id="{{ id }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ title|t }} <span id="modal_org_title"></span></h4>
            </div>
            <div class="modal-body">
                <p>{{ "Do you want to send"|t }} <span id="modal_org_name"></span> {{ "an invitation to join the Signadens platform?"|t }}</p>
                <p>{{ "The invitation will be sent to"|t }} <span id="modal_org_email"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary {{ additionalClass }}" >{{ "Yes, send invitation"|t }}</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ "Cancel"|t }}</button>
            </div>
        </div>
    </div>
</div>