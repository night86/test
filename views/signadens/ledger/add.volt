{% extends "layouts/main.volt" %}
{% block title %} {{ "Add new ledger code"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Add new ledger code"|t }}</h3>

    {{ form('signadens/ledger/add', 'method': 'post') }}

    <fieldset class="form-group">
        <legend>{{ "Basic data"|t }}</legend>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{"Code"|t}}:</label>
                    {{ numeric_field('code', 'required': 'required', 'class': 'form-control') }}
                </div>
                {#<div class="form-group">
                    <label>Balance type:</label>
                    {{ text_field('balance_type', 'required': 'required', 'class': 'form-control') }}
                </div>
                #}
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{"Description"|t}}:</label>
                    {{ text_field('description', 'class': 'form-control', 'required': 'required') }}
                </div>
                {#<div class="form-group">
                    <label>Balance side:</label>
                    {{ text_field('balance_side', 'required': 'required', 'class': 'form-control') }}
                </div>
                #}
            </div>
            <div class="col-md-4">
                {#<div class="form-group">
                    <label>Type:</label>
                    {{ text_field('type', 'class': 'form-control', 'required': 'required') }}
                </div>
                <div class="form-group">
                    <label>Product:</label>
                    {{ select('product_id', products, 'required': 'required', 'class': 'form-control') }}
                </div>
                #}
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