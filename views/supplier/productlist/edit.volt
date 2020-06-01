{% extends "layouts/main.volt" %}
{% block title %} {{ "Edit product"|t }} {{ product.name }} {% endblock %}
{% block styles %}
    {{ super() }}
    <style>
        .row {
            margin-bottom: 20px;
        }
    </style>
{% endblock %}
{% block scripts %}
<script>
        $(function(){
            $('#date').datepicker({
                startDate: 0
            });
        });
</script>
{% endblock %}
{% block content %}

    <h3><a href="{{ url("supplier/productlist/") }}"><i class="pe-7s-back"></i></a> {{ "Product edit"|t }}</h3>

    {{ form('supplier/productlist/edit/'~product.id, 'method': 'post', 'enctype' : 'multipart/form-data', 'disabled': 'disabled') }}
    <fieldset class="form-group">
        <legend>{{ "Product ID"|t }}: {{ product.signa_id }}</legend>
        <div class="row">
            <div class="col-md-4">
                <label for="">{{ "Product name"|t }}</label>
                {{ text_field('name', 'required': 'required', 'value': product.name, 'class': 'form-control', 'disabled': 'disabled') }}
            </div>
            <div class="col-md-4">
                <label for="">{{ "Manufacturer"|t }}</label>
                {{ text_field('manufacturer', 'value': product.manufacturer, 'class': 'form-control', 'disabled': 'disabled') }}
            </div>
            <div class="col-md-4">
                <label for="">{{ "Price"|t }}</label>
                {{ numeric_field('price', 'required': 'required', 'value': product.price, 'class': 'form-control', 'step': 'any', 'min': 0, 'disabled': 'disabled') }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label for="">{{ "Currency"|t }}</label>
                {{ text_field('currency', 'required': 'required', 'value': product.currency, 'class': 'form-control', 'disabled': 'disabled') }}
            </div>

            <div class="col-md-4">
                <label for="">{{ "Material"|t }}</label>
                {{ text_field('material', 'value': product.material, 'class': 'form-control', 'disabled': 'disabled') }}
            </div>
            <div class="col-md-4">
                <label for="">{{ "Delivery time"|t }}</label>
                {{ numeric_field('delivery_time', 'value': product.delivery_time, 'class': 'form-control', 'disabled': 'disabled') }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label for="">{{ "Code"|t }}</label>
                {{ text_field('code', 'required': 'required', 'value': product.code, 'class': 'form-control', 'disabled': 'disabled') }}
            </div>
            <div class="col-md-4">
                <label for="">{{ "Barcode supplier"|t }}</label>
                {{ text_field('barcode_supplier', 'value': product.barcode_supplier, 'class': 'form-control', 'disabled': 'disabled') }}
            </div>
            <div class="col-md-4">
                <label for="">{{ "Tax percentage"|t }}</label>
                {{ numeric_field('tax_percentage','value': product.tax_percentage, 'class': 'form-control', 'disabled': 'disabled') }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
            <label for="">{{ "External link productsheet"|t }}</label>
            {{ text_field('external_link_productsheet', 'value': product.external_link_productsheet, 'class': 'form-control') }}
            </div>
            {#<div class="col-md-4">#}
                {#<label for="">{{ "External link"|t }}</label>#}
                {#{{ text_field('external_link', 'value': product.external_link, 'class': 'form-control', 'disabled': 'disabled') }}#}
            {#</div>#}
            <div class="col-md-4">
                <label for="">{{ "URL to product image"|t }}</label>
                {% set imageurl = '' %}
                {% if product.images|length > 0 and product.images[0]['url'] %}
                    {% set imageurl = product.images[0]['url'] %}
                {% endif %}
                {{ text_field('external_product_image', 'value': imageurl, 'class': 'form-control', 'disabled': 'disabled') }}
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="name">{{ 'Image'|t }}</label>
                    <input type="file" name="logo" class="form-control">
                </div>
            </div>
            <div class="col-md-1">
                {% if product.images is not null %}

                    <a href="{{ url("supplier/productlist/deleteimage/")~product.getId() }}" style="margin-top: 25px;" class="btn btn-danger delete-image"><i
                                class="pe-7s-trash"></i> {{ "Delete image"|t }}</a>
                {% endif %}
            </div>
        </div>
        <div class="row">
            {#<div class="col-md-4">#}
            {#<div class="form-group">#}
            {#<label>{{ 'Add attachment(s)'|t }}</label>#}
            {#{% if attachment is defined and attachment is not false %}#}
            {#<div><b>Attachment:</b> {{ attachment.name }}</div>#}
            {#{% endif %}#}
            {#{{ file_field('files[]', 'class': 'form-control') }} #}{##}{#'multiple': 'multiple'#}
            {#</div>#}
            {#</div>#}
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ "Description"|t }}</label>
                    {{ text_area('description', 'class': 'form-control', 'value': product.description, 'disabled': 'disabled') }}
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ "Product group"|t }}</label>
                    {{ text_field('product_group', 'class': 'form-control', 'value': product.product_group, 'disabled': 'disabled') }}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="name">{{ 'Upload product sheet'|t }}</label>
                    <input type="file" name="sheet" class="form-control">
                </div>
                {% if product.internal_link_productsheet is not null %}
                <div class="form-group">
                    <a href="{{ product.internal_link_productsheet }}">{{ product.internal_productsheet }}</a>
                </div>
                {% endif %}
            </div>
            <div class="col-md-1">
                {% if product.external_link_productsheet is not null %}

                    <a href="{{ url("supplier/productlist/deletesheet/")~product.getId() }}" style="margin-top: 25px;" class="btn btn-danger delete-image"><i
                                class="pe-7s-trash"></i> {{ "Delete sheet"|t }}</a>
                {% endif %}
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ "Special order"|t }}</label>
                    {% if product.getSpecialOrder() == '1' %}
                        {{ check_field('special_order_check', 'value': 1, 'checked': 'checked', 'class': 'basic-switcher') }}
                    {% else %}
                        {{ check_field('special_order_check', 'value': 0, 'class': 'basic-switcher') }}
                    {% endif %}
                    {{ hidden_field('special_order', 'class': 'form-control', 'value': product.special_order) }}
                </div>
            </div>
            {#<div class="col-md-4">#}
            {#<div class="form-group">#}
            {#<label for="">{{ "External link product sheet Signadens"|t }}</label>#}
            {#{{ text_field('signa_external_link', 'class': 'form-control', 'value': product.signa_external_link) }}#}
            {#</div>#}
            {#</div>#}
            {#<div class="col-md-4">#}
            {#<div class="form-group">#}
            {#<label for="">{{ "Product sheet Signadens"|t }}</label>#}
            {#{{ file_field('signa[]', 'class': 'form-control') }}#}
            {#</div>#}
            {#</div>#}
            {#<div class="col-md-4">#}
            {#<div class="form-group">#}
            {#<label for="">{{ "Product description Signadens"|t }}</label>#}
            {#{{ text_field('signa_description', 'class': 'form-control', 'value': product.signa_description) }}#}
            {#</div>#}
            {#</div>#}
        </div>
        <div class="row">
            <div class="col-lg-11">
                <button {% if product.removal_request is not null %}disabled="disabled" title="{{ "Removal request already sent"|t }}" {% endif %} type="button" class="btn btn-danger pull-right product-remove" data-url="/supplier/productlist/removeproduct/{{ product.getId() }}">
                    <i class="pe-7s-trash"></i> {{ "Request product removal"|t }}
                </button>
            </div>
            <div class="col-lg-1">
                <button type="submit" class="btn btn-primary pull-right">
                    <i class="pe-7s-diskette"></i> {{ "Save"|t }}
                </button>
            </div>
        </div>

    </fieldset>

    {{ partial("modals/confirmGeneral", ['id': 'confirm-modal', 'title': "Delete"|t, 'content': "Are you sure you want to delete?"|t]) }}

    {{ end_form() }}

    {{ partial("modals/confirmRemoval", ['id': 'confirm-remove-modal', 'title': "Request product removal"|t, 'content': "From what moment do you want this product to be removed and why are you requesting this?"|t]) }}

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        $(function(){
            var special_order = $('#special_order').val();

            $('#special_order_check').on('switchChange.bootstrapSwitch', function (event, state) {
                if(special_order == 1){
                    special_order = 0;
                    $('#special_order').val(0);
                    $('#special_order_check').val(0);
                }
                else {
                    special_order = 1;
                    $('#special_order').val(1);
                    $('#special_order_check').val(1);
                }
            });


            $('.delete-image').on('click', function(e){
                e.preventDefault();
                $href = $(this).attr('href');
                var confirmModal = $('#confirm-modal');
                confirmModal.modal('show');

                $('.confirm-button').on('click', function(){
                    confirmModal.modal('hide');
                    window.location = $href;
                });
            });
            $('.product-remove').on('click', function(e){
                e.preventDefault();
                $href = $(this).attr('data-url');
                var confirmRemoveModal = $('#confirm-remove-modal');
                confirmRemoveModal.modal('show');

                $('.confirm-button').on('click', function(){
                    confirmRemoveModal.modal('hide');
                    if($('#date').val()){
                        $.ajax({
                            method: 'POST',
                            url: $href,
                            data: { id: {{ product.getId() }}, date: $('#date').val(), msg: $('#msg').val() },
                            success: function(data){
                                var obj = $.parseJSON(data);

                                if (obj.status === 'ok') {
                                    setTimeout(function () {
                                        toastr.success(obj.msg)
                                    }, 1000);
                                }
                                else {
                                    setTimeout(function () {
                                        toastr.error(obj.msg)
                                    }, 1000);
                                }
                            },
                        });
                    }
                    else {
                        setTimeout(function () {
                            toastr.error('{{"Please fill out the required fields."|t}}')
                        }, 1000);
                    }
                });
            });
        });
    </script>
{% endblock %}