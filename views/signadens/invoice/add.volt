{% extends "layouts/main.volt" %}
{% block title %} {{ "Add new invoice"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Add new invoice"|t }}</h3>

    {{ form('signadens/invoice/add', 'method': 'post') }}

    <fieldset class="form-group">
        <legend>{{ "Basic data"|t }}</legend>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Description"|t }}:</label>
                    {{ text_field('description', 'required': 'required', 'class': 'form-control') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Invoice date"|t }}:</label>
                    {{ text_field('date', 'class': 'form-control datepicker', 'value': date('d-m-Y'), 'required': 'required') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Due date"|t }}:</label>
                    {{ text_field('due_date', 'class': 'form-control datepicker', 'required': 'required') }}
                </div>
            </div>
        </div>

    </fieldset>
    <legend>{{ "Client data"|t }}</legend>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Client name"|t }}:</label>
                    {{ text_field('client_name', 'required': 'required', 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Client address"|t }}:</label>
                    {{ text_field('client_address', 'required': 'required', 'class': 'form-control') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Client name continue"|t }}:</label>
                    {{ text_field('client_name_continue', 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Client zip code"|t }}:</label>
                    {{ text_field('client_zip_code', 'required': 'required', 'class': 'form-control') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Client city"|t }}:</label>
                    {{ text_field('client_city', 'required': 'required', 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Client country"|t }}:</label>
                    {{ text_field('client_country', 'required': 'required', 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <input type="hidden" name="invoice_type" value="signa" />
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