{% extends "layouts/main.volt" %}
{% block title %} {{ "Order history"|t }} {% endblock %}
{% block content %}

    <h3>{{ "All orders"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table id="orders" class="buttons-datatable table table-striped table-bordered" cellspacing="0" width="100%">
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
                        <td>{{ order.CreatedBy.firstname }} {{ order.CreatedBy.lastname }}</td>
                        <td>{% if count(locations) > 1 and order.DentistLocation %}{{ order.DentistLocation.getName() }}{% else %}{{ "Main location"|t }}{% endif %}</td>
                        {#<td>{{ date("Y-m-d", strtotime(order.delivery_at)) }}</td>#}
                        <td>{{ order.getTotal() }}</td>
                        <td>{% if order.DentistOrderData %}{{ order.DentistOrderData.getPatientInitials() }} {{ order.DentistOrderData.getPatientInsertion() }} {{ order.DentistOrderData.getPatientLastname() }}{% endif %}</td>
                        <td>{% if order.DentistOrderData and order.DentistOrderData.getPatientBirth() is not null %}{{ date("d-m-Y", strtotime(order.DentistOrderData.getPatientBirth())) }}{% endif %}</td>
                        <td><div class="hidden">{{ order.order_at }}</div>{{ order.order_at|dttonl }}</td>
                        <td>
                            <a href="{{ url('lab/sales_order/view/' ~ order.code) }}" class="btn btn-default btn-sm"><i class="pe-7s-look"></i> {{ 'Show'|t }}</a>
                            <a href="{{ url("delivery_note/view/" ~ order.code ) }}" class="btn btn-default btn-sm"><i class="pe-7s-look"></i> {{ "View delivery note"|t }}</a>
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

{% endblock %}