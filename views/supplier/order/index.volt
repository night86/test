{% extends "layouts/main.volt" %}
{% block title %} {{ "Orderlist"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Orderlist"|t }}</h3>

    <table class="simple-datatable table table-striped" data-order="2">
        <thead>
            <th>{{ "Order no."|t }}</th>
            <th>{{ "Client"|t }}</th>
            <th class="sortbydate">{{ "Order date"|t }}</th>
            <th>{{ "Status"|t }}</th>
            <th>{{ "Action"|t }}</th>
        </thead>
        <tbody>
            {% for order in orders %}
                <tr>
                    <td>{{ order.getName() }}</td>
                    <td>{% if order.Organisation %}{{ order.Organisation.getName() }}{% endif %}</td>
                    <td><div class="hidden">{{ order.getCreatedAt() }}</div>{{ order.getCreatedAt()|dttonl }}</td>
                    <td>{{ order.getStatusLabel() }}</td>
                    <td>
                        <a href="{{ url("supplier/order/edit/" ~ order.getId()) }}" class="btn btn-default btn-sm">{{ "View"|t }} <i class="pe-7s-angle-right"></i></a>
                    </td>
                </tr>
            {% endfor %}
        </tbody>
    </table>

{% endblock %}