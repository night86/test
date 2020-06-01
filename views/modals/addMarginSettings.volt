<div id="{{ id }}" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ title|t }}</h4>
            </div>
            <div class="modal-body">
                <form id="singleFieldForm">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="margin_type">{{ "Margin type"|t }}:</label>
                            <select id="margin_type" name="margin_type" class="form-control">
                                <option value="" disabled="disabled" selected="selected">{{ "Select margin type"|t }}</option>
                                <option value="1">{{ 'Fixed price'|t }}</option>
                                <option value="2">{{ 'Fixed margin in euro on top of purchase price'|t }}</option>
                                <option value="3">{{ 'As percentages of the purchase price'|t }}</option>
                                <option value="4">{{ 'As percentages of the sales price'|t }}</option>
                            </select>
                        </div>
                        <div class="col-md-12">&nbsp;</div>
                    </div>
                    <div class="row">
                        <div class="col-md-9">
                            <label for="margin_value">{{ "Margin value"|t }}:</label>
                            <input id="margin_value" name="margin_value" type="number" class="form-control" min="0" step="0.01" />
                        </div>
                        <div id="margin_symbol" class="col-md-3" style="margin-top: 32px; margin-left: -15px; display: none;"></div>
                        <div class="col-md-12">&nbsp;</div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="rounding_type">{{ "Rounding type"|t }}:</label>
                            <select id="rounding_type" name="rounding_type" class="form-control">
                                <option value="" disabled="disabled" selected="selected">{{ "Select rounding type"|t }}</option>
                                <option value="1">{{ 'Decimal (2)'|t }}</option>
                                <option value="2">{{ 'Integer'|t }}</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="confirmButton" type="button"
                        class="btn btn-primary confirm-button">{% if confirmButton is defined %}{{ confirmButton|t }}{% else %}{{ "Confirm"|t }}{% endif %}</button>
                <button type="button" class="btn btn-default cancel-button"
                        data-dismiss="modal">{{ "Cancel"|t }}</button>
            </div>
        </div>
    </div>
</div>