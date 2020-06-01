<div id="{{ id }}" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ title|t }}</h4>
            </div>
            <div class="modal-body">
                <p>{{ content|t }}</p>
                <div class="form-group">
                    <label for="code">{{ "Code"|t }}:</label>
                    <input id="code" type="text" class="form-control" name="code" />
                </div>
                <div class="form-group">
                    <label for="percentage">{{ "Percentage"|t }}:</label>
                    <input id="percentage" type="number" min="0" class="form-control" name="percentage" step="0.1" />
                </div>
                <div class="form-group">
                    <label for="description">{{ "Description"|t }}:</label>
                    <textarea id="description" class="form-control" name="description" style="resize: none;"></textarea>
                    <p>{{ "Available tokens"|t }}:</p>
                    <p>[discount_percentage]</p>
                    <p>[discount_amount]</p>
                    <p>[price_minus_discount]</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default cancel-button" data-dismiss="modal">{{ "Cancel"|t }}</button>
                <button id="confirm_payment" type="button" class="btn btn-primary confirm-button"><i class="pe-7s-diskette"></i> {{ "Save"|t }}</button>
            </div>
        </div>
    </div>
</div>