{% extends "layouts/main.volt" %}
{% block title %} {{ "Lab"|t }} {% endblock %}
{% block content %}

<h3>{{ "New product"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table class="table simple-datatable table-striped table-bordered" cellspacing="0"
                   width="100%">
                <thead>
                <tr>
                    <th>{{ "Product Code"|t }}</th>
                    <th>{{ "Product Name"|t }}</th>
                    <th>{{ "Price"|t }}</th>
                    <th>{{ "Name supplier"|t }}</th>
                </tr>
                </thead>
                <tbody>
                {% for product in mproducts %}
                    <tr>
                        {% if product.details|isArray %}
                            <td>{{ product.details['code'] }}</td>
                            <td>{{ product.details['name'] }}</td>
                            <td>{{ product.details['price'] }} {{ product.details['currency'] }}</td>
                        {% else %}
                            <td>{{ product.details.code }}</td>
                            <td>{{ product.details.name }}</td>
                            <td>{{ product.details.price }} {{ product.details.currency }}</td>
                        {% endif %}
                        <td>{{ product.supplierName }}</td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

{% endblock %}