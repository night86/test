{% extends "layouts/main.volt" %}
{% block title %} Dashboard {% endblock %}
{% block content %}

    <h3>{{ "Import log details"|t }}</h3>

    <div class="row">
        <div class="col-md-12 col-sm-12">
            <table id="viewlog" class="buttons-datatable table table-striped">
                <thead>
                <th>{{ "Name"|t }}</th>
                <th>{{ "Code"|t }}</th>
                <th>{{ "Price"|t }}</th>
                {#<th>{{ "Package unit"|t }}</th>#}
                <th>{{ "Tax percentage"|t}}
                <th>{{ "Delivery time"|t }}</th>
                {#<th>{{ "Key words"|t }}</th>#}
                <th>{{ "Status"|t }}</th>
                </thead>
                <tbody>
                {% for product in products %}
                    <tr>
                        <td>{{  product.getName() }}</td>
                        <td>{{  product.getCode() }}</td>
                        <td>{{  product.getPrice() }}</td>

                        {#<td>{{  product.getPackageUnit() }}</td>#}
                        <td>{{  product.getTaxPercentage() }}</td>
                        <td>{{  product.getDeliveryTime() }}</td>
                        {#<td>{{  product.getKeyWords() }}</td>#}
                        <td>
                            {% if product.getActive() %}<strong>{{ 'Active'|t }}</strong> {% else %} <strong>{{ 'Inactive'|t }}</strong> {% endif %}
                            {% if product.getDeleted() %}<strong>{{ 'Deleted'|t }}</strong> {% endif %}
                            {% if product.getApproved() %}<strong>{{ 'Approved'|t }}</strong> {% endif %}
                            {% if product.getDeclined() %}<strong>{{ 'Declined'|t }}</strong> {% endif %}
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

{% endblock %}