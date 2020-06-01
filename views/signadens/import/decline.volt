{% extends "layouts/main.volt" %}
{% block title %} Signadens {% endblock %}
{% block content %}

    <h3>{{ "Import - decline"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            {{ form('signadens/import/decline/'~id, 'method': 'post') }}
                <h5>{{ "Import by"|t }} {{ userFullName }} {{ "on"|t }} {{ effectiveFrom }}</h5>
                <table class="table table-striped">
                    <thead>
                        <th>{{ "Product Code"|t }}</th>
                        <th>{{ "Product Name"|t }}</th>
                        {% if type == 'update' %}
                            <th>{{ "New Product"|t }}</th>
                            <th>{{ "Old Product"|t }}</th>
                        {% endif %}
                        <th>{{ "Remark about denial"|t }}</th>
                    </thead>
                    <tbody>
                        {% for product in products %}
                            <tr>
                                <td>{{ product.code }}</td>
                                <td>{{ product.name }}</td>
                                {% if type == 'update' %}
                                    {% set row = product.compareOldNew() %}
                                    <td>
                                        <span class="product-list-value {% if row['new']['name']['change'] %}active{% endif %}"><strong>{{ "Name"|t }}:</strong> {{ row['new']['name']['value'] }}</span>
                                        <span class="product-list-value {% if row['new']['description']['change'] %}active{% endif %}"><strong>{{ "Description"|t }}:</strong> {{ row['new']['description']['value']|truncate }}</span>
                                        <span class="product-list-value {% if row['new']['material']['change'] %}active{% endif %}"><strong>{{ "Material"|t }}:</strong> {{ row['new']['material']['value'] }}</span>
                                        <span class="product-list-value {% if row['new']['price']['change'] %}active{% endif %}"><strong>{{ "Price"|t }}:</strong> {{ row['new']['price']['value'] }}</span>
                                    </td>
                                    <td>
                                        <span class="product-list-value {% if row['old']['name']['change'] %}old-active{% endif %}"><strong>{{ "Name"|t }}:</strong> {{ row['old']['name']['value'] }}</span>
                                        <span class="product-list-value {% if row['old']['description']['change'] %}old-active{% endif %}"><strong>{{ "Description"|t }}:</strong> {{ row['old']['description']['value']|truncate }}</span>
                                        <span class="product-list-value {% if row['old']['material']['change'] %}old-active{% endif %}"><strong>{{ "Material"|t }}:</strong> {{ row['old']['material']['value'] }}</span>
                                        <span class="product-list-value {% if row['old']['price']['change'] %}old-active{% endif %}"><strong>{{ "Price"|t }}:</strong> {{ row['old']['price']['value'] }}</span>
                                    </td>
                                {% endif %}
                                <td>{{ text_area('messages['~product.id~']', 'class': 'form-control', 'rows': 2, 'style':'width:100%;') }}</td>
                            </tr>
                        {% endfor %}
                    </tbody>
                </table>
            <div class="row">
                <div class="col-md-12" style="margin-top: 15px;">
                    <label>{{ "General message"|t }}</label>
                    {{ text_area('message', 'class': 'form-control', 'rows': 5) }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary pull-right" style="margin-top: 15px;">{{ "Send Denial"|t }} <i class="pe-7s-angle-right"></i></button>
                </div>
            </div>

            {{ end_form() }}
        </div>
    </div>

{% endblock %}