{% extends "layouts/main.volt" %}
{% block title %} {{ "Orderlist"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Orders"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table id="history" class="buttons-datatable table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <th>{{ "Order no."|t }}</th>
                    {#<th>{{ "Total"|t }}</th>#}
                    <th>{{ "Order by"|t }}</th>
                    <th class="sortbydate">{{ "Order date"|t }}</th>
                    <th>{{ "Status"|t }}</th>
                    <th>{{ "Actions"|t }}</th>
                </thead>
                <tbody>
                {% for order in orders %}
                    <tr>
                        <td>{{ order.name }}</td>
                        {#<td>{{ order.getTotal() }}</td>#}
                        <td>{{ order.CreatedBy.firstname }} {{ order.CreatedBy.lastname }}</td>
                        <td><div class="hidden">{{ order.order_at }}</div>{{ order.order_at|dttonl }}</td>
                        <td>{{ order.getStatusLabel()|t }}</td>
                        <td>
                            <a href="{{ url('supplier/order/historyDetails/' ~ order.id) }}" class="btn btn-default btn-sm"><i class="pe-7s-look"></i> {{ 'More details'|t }}</a>
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

{% endblock %}