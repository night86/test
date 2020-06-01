{% extends "layouts/main.volt" %}
{% block title %} Signadens {% endblock %}
{% block content %}

    <h3>{{ "Import - categorize"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            {{ form('signadens/import/categorize/'~id, 'method': 'post') }}
                <h5>{{ "Import by"|t }} {{ userFullName }} {{ "on"|t }} {{ effectiveFrom }}</h5>
                <table class="table table-striped">
                    <thead>
                        <th>{{ check_field('selectedAll', 'class': 'select-all-products') }}</th>
                        <th>{{ "Product Code"|t }}</th>
                        <th>{{ "Product Name"|t }}</th>
                        {% if type == 'update' %}
                            <th>{{ "New Product"|t }}</th>
                            <th>{{ "Old Product"|t }}</th>
                        {% else %}
                            <th>{{ "Product price"|t }}</th>
                            <th>{{ "Main product category"|t }}</th>
                            <th>{{ "Sub category"|t }}</th>
                            <th>{{ "Subsub category"|t }}</th>
                            {#<th>{{ "Package unit"|t }}</th>#}
                            <th>{{ "Delivery time"|t }}</th>
                            <th>{{ "Material"|t }}</th>
                            <th>{{ "Manufacturer"|t }}</th>
                            {#<th>{{ "Barcode supplier"|t }}</th>#}
                            {#<th>{{ "Key word for product searching"|t }}</th>#}
                        {% endif %}
                    </thead>
                    <tbody>
                        {% for product in products %}
                            <tr>
                                <td>{{ check_field('selectedProducts[]', 'value': product.id, 'class': 'group-checkbox') }}</td>
                                <td>{{ product.code }}</td>
                                <td>{{ product.name }}</td>
                                {% if type == 'update' %}
                                    <td>
                                        {% if row['newValues'] is defined %}
                                            <span class="product-list-value {% if row['newValues']['name']['change'] %}active{% endif %}"><strong>{{ "Name"|t }}:</strong> {{ row['newValues']['name']['value'] }}</span>
                                            <span class="product-list-value {% if row['newValues']['description']['change'] %}active{% endif %}"><strong>{{ "Description"|t }}:</strong> {{ row['newValues']['description']['value']|truncate }}</span>
                                            <span class="product-list-value {% if row['newValues']['material']['change'] %}active{% endif %}"><strong>{{ "Material"|t }}:</strong> {{ row['newValues']['material']['value'] }}</span>
                                            <span class="product-list-value {% if row['newValues']['price']['change'] %}active{% endif %}"><strong>{{ "Price"|t }}:</strong> {{ row['newValues']['price']['value'] }}</span>
                                            <span class="product-list-value {% if row['newValues']['price_currency']['change'] %}active{% endif %}"><strong>{{ "Currency"|t }}:</strong> {{ row['newValues']['price_currency']['value'] }}</span>
                                            <span class="product-list-value {% if row['newValues']['image_url']['change'] %}active{% endif %}"><strong>{{ "Images urls"|t }}:</strong> {{ row['newValues']['image_url']['value'] }}</span>
                                        {%  else %}
                                            <p>{{ "Create new product"|t }}</p>
                                        {%  endif %}
                                    </td>
                                    <td>
                                        {% if row['oldValues'] is defined %}
                                            <span class="product-list-value {% if row['oldValues']['name']['change'] %}old-active{% endif %}"><strong>{{ "Name"|t }}:</strong> {{ row['oldValues']['name']['value'] }}</span>
                                            <span class="product-list-value {% if row['oldValues']['description']['change'] %}old-active{% endif %}"><strong>{{ "Description"|t }}:</strong> {{ row['oldValues']['description']['value']|truncate }}</span>
                                            <span class="product-list-value {% if row['oldValues']['material']['change'] %}old-active{% endif %}"><strong>{{ "Material"|t }}:</strong> {{ row['oldValues']['material']['value'] }}</span>
                                            <span class="product-list-value {% if row['oldValues']['price']['change'] %}old-active{% endif %}"><strong>{{ "Price"|t }}:</strong> {{ row['oldValues']['price']['value'] }}</span>
                                            <span class="product-list-value {% if row['oldValues']['price_currency']['change'] %}old-active{% endif %}"><strong>{{ "Currency"|t }}:</strong> {{ row['oldValues']['price_currency']['value'] }}</span>
                                            <span class="product-list-value {% if row['oldValues']['image_url']['change'] %}old-active{% endif %}"><strong>{{ "Images urls"|t }}:</strong> {{ row['oldValues']['image_url']['value'] }}</span>
                                        {%  else %}
                                            <p>{{ "Product does not exist"|t }}</p>
                                        {%  endif %}
                                    </td>
                                {% else %}
                                    <td>{{ product.price }}</td>
                                    <td>
                                        {% if product.MainCategory %}
                                            {{ product.MainCategory.name }}
                                        {% endif %}
                                    </td>
                                    <td>
                                        {% if product.SubCategory %}
                                            {{ product.SubCategory.name }}
                                        {% endif %}
                                    </td>
                                    <td>
                                        {% if product.SubSubCategory %}
                                            {{ product.SubSubCategory.name }}
                                        {% endif %}
                                    </td>
                                    {#<td>{% if product.package_unit is defined %}{{ product.package_unit }}{% endif %}</td>#}
                                    <td>{{ product.delivery_time }}</td>
                                    <td>{{ product.material }}</td>
                                    <td>{{ product.manufacturer }}</td>

                                    {#<td>{{ product.barcode_supplier }}</td>#}
                                    {#<td>{{ product.key_words }}</td>#}
                                {% endif %}
                            </tr>
                        {% endfor %}
                    </tbody>
                </table>
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <button type="submit" class="btn btn-primary pull-right" style="margin: 15px;"><i class="pe-7s-check"></i> {{ "Set category for selected"|t }} <i class="pe-7s-angle-right"></i></button>
                        <span class="pull-right" style="margin: 15px;">{{ "Category"|t }}: {{ select('categoryId', categories, 'using': ['id', 'name'], 'required': 'required', 'class': 'form-control', 'style': 'width:150px;display:inline-block;') }}</span>
                    </div>
                </div>
            </div>

                {#{{ submit_button('Set category for selected >', 'class': 'btn btn-primary pull-right') }}#}
            {{ end_form() }}
        </div>
    </div>

{% endblock %}