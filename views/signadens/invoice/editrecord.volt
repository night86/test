{% extends "layouts/main.volt" %}
{% block title %} {{ "Edit record from the invoice"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Edit record from the invoice"|t }}</h3>

    {{ form('signadens/invoice/editrecord/'~record.getInvoiceId()~'?recordId='~record.getId(), 'method': 'post') }}
        <fieldset class="form-group">
            <legend>{{ "Basic data"|t }}</legend>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ "Amount" }}:</label>
                        {{ numeric_field('amount', 'required': 'required', 'value': record.getAmount(), 'min': 0, 'class': 'form-control') }}
                    </div>
                    <div class="form-group">
                        <label>{{ "Description" }}:</label>
                        {{ text_field('description', 'required': 'required', 'value': record.getDescription(), 'class': 'form-control') }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ "Price" }}:</label>
                        {{ numeric_field('price', 'class': 'form-control', 'value': record.getPriceWithoutTax(), 'min': 0, 'step':'any', 'required': 'required') }}
                    </div>
                    <div class="form-group">
                        <label>{{ "Receiver (dentist)" }}:</label>
                        {{ text_field('receiver', 'required': 'required', 'value': record.getReceiver(), 'class': 'form-control') }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ "Tax" }}:</label>
                        {{ numeric_field('tax', 'class': 'form-control', 'value': record.getTax(), 'min': 0, 'required': 'required') }}
                    </div>
                    <div class="form-group">
                        <label>{{ "Sender (lab)" }}:</label>
                        {{ text_field('sender', 'required': 'required', 'value': record.getSender(), 'class': 'form-control') }}
                    </div>
                    <div class="form-group">
                        <label for="">&nbsp;</label>
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-primary pull-right"><i class="pe-7s-diskette"></i> {{ "Save"|t }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    {{ end_form() }}

{% endblock %}