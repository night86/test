{% extends "layouts/main.volt" %}
{% block title %} {{ "Edit ledger code"|t }} {% endblock %}
{% block content %}

    <h3><a href="{{ url("signadens/ledger/") }}"><i class="pe-7s-back"></i></a>{{ "Edit ledger code"|t }}</h3>

    {{ form('signadens/ledger/edit/' ~ code.getId(), 'method': 'post') }}

    <fieldset class="form-group">
        <legend>{{ "Basic data"|t }}</legend>
        {{ hidden_field('id', 'value': code.getId()) }}
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Code"|t }}:</label>
                    {{ numeric_field('code', 'required': 'required', 'value': code.getCode(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Balance type"|t }}:</label>
                    {{ text_field('balance_type', 'required': 'required', 'value': code.getBalanceType(), 'class': 'form-control') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Description"|t }}:</label>
                    {{ text_field('description', 'value': code.getDescription(), 'class': 'form-control', 'required': 'required') }}
                </div>
                <div class="form-group">
                    <label>{{ "Balance side"|t }}:</label>
                    {{ text_field('balance_side', 'required': 'required', 'value': code.getBalanceSide(), 'class': 'form-control') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Type"|t }}:</label>
                    {{ text_field('type', 'value': code.getGroupType(), 'class': 'form-control', 'required': 'required') }}
                </div>
                <div class="form-group">
                    <label>{{ "Product"|t }}:</label>
                    {{ select('product_id', products, 'required': 'required', 'value': code.getProductId(), 'class': 'form-control') }}
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