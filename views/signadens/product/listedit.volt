{% extends "layouts/main.volt" %}
{% block title %} {{ "Product edit"|t }} {% endblock %}
{% block styles %}
    {{ super() }}
    <style>
        .row {
            margin-bottom: 20px;
        }
    </style>
{% endblock %}

{% block content %}

    <h3><a href="{{ url("signadens/product/list") }}"><i class="pe-7s-back"></i></a> {{ "Product edit"|t }}</h3>

    {{ form('signadens/product/listedit/'~product.id, 'method': 'post', 'enctype' : 'multipart/form-data') }}
    <fieldset class="form-group">
        <legend>{{ "Product ID"|t }}: {{ product.signa_id }}</legend>
        <div class="row">
            <div class="col-md-4">
                <label for="">{{ "Product name"|t }}</label>
                {{ text_field('name', 'required': 'required', 'value': product.name, 'class': 'form-control') }}
            </div>
            <div class="col-md-4">
                <label for="">{{ "Manufacturer"|t }}</label>
                {{ text_field('manufacturer', 'value': product.Manufacturer.name, 'class': 'form-control', 'readonly': 'readonly') }}
            </div>
            <div class="col-md-4">
                <label for="">{{ "Price"|t }}</label>
                {{ numeric_field('price', 'required': 'required', 'value': product.price, 'class': 'form-control', 'step': 'any', 'min': 0) }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label for="">{{ "Currency"|t }}</label>
                {{ text_field('currency', 'required': 'required', 'value': product.currency, 'class': 'form-control') }}
            </div>

            <div class="col-md-4">
                <label for="">{{ "Material"|t }}</label>
                {{ text_field('material', 'value': product.material, 'class': 'form-control') }}
            </div>
            <div class="col-md-4">
                <label for="">{{ "Delivery time"|t }}</label>
                {{ numeric_field('delivery_time', 'value': product.delivery_time, 'class': 'form-control') }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label for="">{{ "Code"|t }}</label>
                {{ text_field('code', 'required': 'required', 'value': product.code, 'class': 'form-control') }}
            </div>
            <div class="col-md-4">
                <label for="">{{ "Barcode supplier"|t }}</label>
                {{ text_field('barcode_supplier', 'value': product.barcode_supplier, 'class': 'form-control') }}
            </div>
            <div class="col-md-4">
                <label for="">{{ "Tax percentage"|t }}</label>
                {{ numeric_field('tax_percentage','value': product.tax_percentage, 'class': 'form-control') }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label for="">{{ "External link productsheet"|t }}</label>
                {{ text_field('external_link_productsheet', 'value': product.external_link_productsheet, 'class': 'form-control') }}
            </div>
            <div class="col-md-4">
                <label for="">{{ "External link"|t }}</label>
                {{ text_field('external_link', 'value': product.external_link, 'class': 'form-control') }}
            </div>
            <div class="col-md-4">
                <label for="">{{ "URL to product image"|t }}</label>
                {% set imageurl = '' %}
                {% if product.images|length > 0 and product.images[0]['url'] %}
                    {% set imageurl = product.images[0]['url'] %}
                {% endif %}
                {{ text_field('external_product_image', 'value': imageurl, 'class': 'form-control') }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ 'Add attachment(s)'|t }}</label>
                    {% if attachment is defined and attachment is not false %}
                        <div><b>Attachment:</b> {{ attachment.name }}</div>
                    {% endif %}
                    {{ file_field('files[]', 'class': 'form-control') }} {#'multiple': 'multiple'#}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ "Description"|t }}</label>
                    {{ text_area('description', 'class': 'form-control', 'value': product.description) }}
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ "Product group"|t }}</label>
                    {{ text_field('product_group', 'class': 'form-control', 'value': product.product_group) }}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ "External link product sheet Signadens"|t }}</label>
                    {{ text_field('signa_external_link', 'class': 'form-control', 'value': product.signa_external_link) }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ "Product sheet Signadens"|t }}</label>
                    {{ file_field('signa[]', 'class': 'form-control') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ "Product description Signadens"|t }}</label>
                    {{ text_field('signa_description', 'class': 'form-control', 'value': product.signa_description) }}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ "Main product category"|t }}</label>
                    <select id="main_category_id" name="main_category_id" class="form-control">
                        <option value=""> - </option>
                        {% for category in productCategories %}
                            {% if category.parent_id is null %}
                                <option {% if product.main_category_id is category.id %}selected="selected"{% endif %} value="{{ category.id }}">{{ category.name }}</option>
                            {% endif %}
                        {% endfor %}
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ "Sub category"|t }}</label>
                    <select id="sub_category_id" name="sub_category_id" class="form-control">
                        <option value=""> - </option>
                        {% for category in productCategories %}
                            {% if category.Parent and not category.Parent.Parent %}
                                <option {% if product.sub_category_id is category.id %}selected="selected"{% endif %} data-parent="{{ category.parent_id }}" class="hidden catoption" value="{{ category.id }}">{{ category.name }}</option>
                            {% endif %}
                        {% endfor %}
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ "Subsub category"|t }}</label>
                    <select id="sub_sub_category_id" name="sub_sub_category_id" class="form-control">
                        <option value=""> - </option>
                        {% for category in productCategories %}
                            {% if category.Parent and category.Parent.Parent %}
                                <option {% if product.sub_sub_category_id is category.id %}selected="selected"{% endif %} data-parent="{{ category.parent_id }}" class="hidden catoption" value="{{ category.id }}">{{ category.name }}</option>
                            {% endif %}
                        {% endfor %}
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <p>{{ "Based on the connections between Signadens ledger codes and product categories this product is connected to the following ledger codes:"|t }}</p>
                <p>{{ "Ledger code purchase"|t }}: {{ ledgerPurchase.code }} - {{ ledgerPurchase.description }}</p>
                <p>{{ "Ledger code sales"|t }}: {{ ledgerSales.code }} - {{ ledgerSales.description }}</p>
            </div>
            <div class="col-lg-12">&nbsp;</div>
            <div class="col-lg-12">
                <p>{{ "You can manually change this in the fields below. Be aware that a manual connection overrules the above connection."|t }}</p>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="ledger_purchase">{{ "Ledger code purchase"|t }}</label>
                    <select id="ledger_purchase" name="ledger_purchase_id" class="form-control select2-input">
                        <option></option>
                        {% for code in ledgerCodes %}
                            <option {% if code.id == product.ledger_purchase_id %}selected="selected"{% endif %} value="{{ code.id }}">{{ code.code }} - {{ code.description }}</option>
                        {% endfor %}
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="ledger_sales">{{ "Ledger code sales"|t }}</label>
                    <select id="ledger_sales" name="ledger_sales_id" class="form-control select2-input">
                        <option></option>
                        {% for code in ledgerCodes %}
                            <option {% if code.id == product.ledger_sales_id %}selected="selected"{% endif %} value="{{ code.id }}">{{ code.code }} - {{ code.description }}</option>
                        {% endfor %}
                    </select>
                </div>
            </div>
            <div class="col-md-4">&nbsp;</div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary pull-right"><i class="pe-7s-diskette"></i> {{ "Save"|t }}</button>
            </div>
        </div>
    </fieldset>
    {{ end_form() }}

{% endblock %}

{% block scripts %}
    {{ super() }}

    <script>
        function rebuildCategories()
        {
            var mainCatVal = $('#main_category_id').val();

            $('#sub_category_id .catoption').each(function(){
                if ($(this).data('parent') == mainCatVal) {
                    $(this).removeClass('hidden');
                } else if (!$(this).hasClass('hidden')) {
                    $(this).addClass('hidden');
                    $(this).removeAttr('selected');
                }
            });

            var subCatVal = $('#sub_category_id').val();

            $('#sub_sub_category_id .catoption').each(function(){
                if ($(this).data('parent') == subCatVal) {
                    $(this).removeClass('hidden');
                } else if (!$(this).hasClass('hidden')) {
                    $(this).addClass('hidden');
                    $(this).removeAttr('selected');
                }
            });

        }

        $(document).ready(function () {
            rebuildCategories();
            $('#main_category_id, #sub_category_id, #sub_sub_category_id').on('change', function() {
                rebuildCategories();
            });
        });
    </script>

{% endblock %}