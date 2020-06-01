{% extends "layouts/main.volt" %}
{% block title %} {{ "Incoming orders"|t }} {% endblock %}
{% block content %}

    <p class="pull-right"><a href="/dentist/order/create" class="btn btn-primary">{{ "Add order"|t }} <i class="pe-7s-plus"></i></a></p>

    <h3>{{ "Incoming orders"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table id="orders" class="buttons-datatable-incoming table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                <th>{{ "Order no."|t }}</th>
                <th>{{ "Status"|t }}</th>
                <th>{{ "Dentist(client)"|t }}</th>
                <th>{{ "Location"|t }}</th>
                {#<th>{{ "Delivery date"|t }}</th>#}
                <th>{{ "Amount"|t }}</th>
                <th>{{ "Patient"|t }}</th>
                <th>{{ "Date of birth"|t }}</th>
                <th class="sortbydate">{{ "Order date"|t }}</th>
                <th>{{ "Actions"|t }}</th>
                </thead>
                <tbody>
                {% for order in orders %}
                    <tr>
                        <td>{{ order.code }}</td>
                        <td>{{ order.getStatusLabel()|t }}</td>
                        <td>{{ order.Dentist.name }}</td>
                        <td>{% if count(locations) > 1 and order.DentistLocation %}{{ order.DentistLocation.getName() }}{% else %}{{ "Main location"|t }}{% endif %}</td>
                        {#<td>{{ order.delivery_at }}</td>#}
                        <td>{{ order.getTotal() }}</td>
                        <td>{% if order.DentistOrderData %}{{ order.DentistOrderData.getPatientInitials() }} {{ order.DentistOrderData.getPatientInsertion() }} {{ order.DentistOrderData.getPatientLastname() }}{% endif %}</td>
                        <td>{% if order.DentistOrderData and order.DentistOrderData.getPatientBirth() is not null %}{{ date("d-m-Y", strtotime(order.DentistOrderData.getPatientBirth())) }}{% endif %}</td>
                        <td><div class="hidden">{{ order.order_at }}</div>{{ order.order_at|dttonl }}</td>
                        <td>
                            <a href="{{ url('lab/sales_order/view/' ~ order.code) }}" class="btn btn-default btn-sm"><i class="pe-7s-look"></i> {{ 'Show'|t }}</a>
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

{% endblock %}