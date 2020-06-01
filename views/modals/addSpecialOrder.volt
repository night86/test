<div id="{{ id }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ title|t }}</h4>
            </div>
            <div class="modal-body">
                {{ content|t }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary {{ additionalClass }}" >{{ "Save"|t }}</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ "Cancel"|t }}</button>
            </div>
        </div>
    </div>
</div>