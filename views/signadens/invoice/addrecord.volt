{% extends "layouts/main.volt" %}
{% block title %} {{ "Add new record to the invoice"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Add new record to the invoice"|t }}</h3>

    {{ form('signadens/invoice/addrecord/'~invoiceId, 'method': 'post') }}
        {{ hidden_field('invoice_id', 'value': invoiceId) }}
        <fieldset class="form-group">
            <legend>{{ "Basic data"|t }}</legend>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ "Amount"|t }}:</label>
                        {{ numeric_field('amount', 'required': 'required', 'min': 0, 'class': 'form-control') }}
                    </div>
                    <div class="form-group">
                        <label>{{ "Description"|t }}:</label>
                        {{ text_field('description', 'required': 'required', 'class': 'form-control') }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ "Price"|t }}:</label>
                        {{ numeric_field('price', 'class': 'form-control', 'min': 0, 'step':'any', 'required': 'required') }}
                    </div>
                    <div class="form-group">
                        <label>{{ "Receiver (dentist)"|t }}:</label>
                        {{ text_field('receiver', 'required': 'required', 'class': 'form-control') }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ "Tax"|t }}:</label>
                        {{ numeric_field('tax', 'class': 'form-control', 'min': 0, 'required': 'required') }}
                    </div>
                    <div class="form-group">
                        <label>{{ "Sender (lab)"|t }}:</label>
                        {{ text_field('sender', 'required': 'required', 'class': 'form-control') }}
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