{% extends "layouts/main.volt" %}
{% block title %} {{ "Add ledger code"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Add ledger code"|t }}</h3>

    {{ form('lab/sales_ledger/add', 'method': 'post') }}

    <fieldset class="form-group">
        <legend>{{ "Ledger code"|t }}</legend>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Code"|t }}:</label>
                    {{ numeric_field('code', 'required': 'required', 'class': 'form-control') }}
                </div>
                {#<div class="form-group">
                    <label>{{ "Balance type"|t }}:</label>
                    {{ text_field('balance_type', 'required': 'required', 'class': 'form-control') }}
                </div>
                #}
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Description"|t }}:</label>
                    {{ text_field('description', 'class': 'form-control', 'required': 'required') }}
                </div>
                {#<div class="form-group">
                    <label>{{ "Balance side"|t }}:</label>
                    {{ text_field('balance_side', 'required': 'required', 'class': 'form-control') }}
                </div>
                #}
            </div>
            <div class="col-md-4">
                {#<div class="form-group">
                    <label>{{ "Group type"|t }}:</label>
                    {{ text_field('group_type', 'class': 'form-control', 'required': 'required') }}
                </div>
                <div class="form-group">
                    <label>{{ "Product"|t }}:</label>
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