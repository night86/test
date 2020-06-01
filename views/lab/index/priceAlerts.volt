{% extends "layouts/main.volt" %}
{% block title %} {{ "Lab"|t }} {% endblock %}
{% block content %}

<h3>{{ "Price alerts"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table class="table simple-datatable table-striped table-bordered" cellspacing="0"
                   width="100%">
                <thead>
                <tr>
                    <th>{{ "Date"|t }}</th>
                    <th>{{ "Product code"|t }}</th>
                    <th>{{ "Product name"|t }}</th>
                    <th>{{ "Old price"|t }}</th>
                    <th>{{ "New price"|t }}</th>
                </tr>
                </thead>
                <tbody>
                {% for product in products %}
                    <tr>
                        <td>{{ timetostrdt(product.start_date) }}</td>
                        <td>{{ product.product_code }}</td>
                        <td>{{ product.product_name }}</td>
                        <td>{{ product.old_price }}</td>
                        <td>{{ product.new_price }}</td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

{% endblock %}