<div id="{{ id }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ "Are you sure you want to remove this setting?"|t }}</h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default cancel-button" data-dismiss="modal">{{ "Cancel"|t }}</button>
                <button type="button" id="confirm_remove" class="btn btn-primary {{ additionalClass }}">{{ "Yes"|t }}</button>
            </div>
        </div>
    </div>
</div>