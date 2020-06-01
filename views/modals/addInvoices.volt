<div id="{{ id }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ title|t }}</h4>
            </div>
            <div class="modal-body">
                <form id="singleFieldForm">
                    <div class="form-group">
                        <label>{{ "Start date"|t }}:</label>
                        {{ text_field('start_date', 'class': 'form-control datepicker', 'required': 'required') }}
                    </div>
                    <div class="form-group">
                        <label>{{ "End date"|t }}:</label>
                        {{ text_field('end_date', 'class': 'form-control datepicker', 'required': 'required') }}
                    </div>
                    <div class="form-group">
                        <label>{{ "Clients"|t }}:</label>
                        <select id="client_data" class="form-control select2-input" name="invoice_clients[]" multiple="multiple">
                            <option value="all">{{ "Select all dentists"|t }}</option>
                            {% for d in dentists %}
                                <option value="{{ d['dentist_data']['id'] }}">{{ d['dentist_data']['name'] }}</option>
                            {% endfor %}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ "Invoice date"|t }}:</label>
                        {{ text_field('date', 'class': 'form-control datepicker', 'required': 'required') }}
                        <input type="hidden" name="invoice_type" value="lab" />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="confirmInvoices" type="button"
                        class="btn btn-primary">{{ "Generate invoices"|t }}</button>
                <button type="button" class="btn btn-default cancel-button"
                        data-dismiss="modal">{{ "Cancel"|t }}</button>
            </div>
        </div>
    </div>
</div>