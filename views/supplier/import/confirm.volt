{% extends "layouts/main.volt" %}
{% block title %} {{ "Import"|t }} {% endblock %}
{% block content %}

    <div class="col-lg-12">
        <h3>{{ "Import"|t }}
            {#{% if importType is not "create" %}#}
                {#<small>{{ "You'll only import changes to exisiting products."|t }}</small>#}
            {#{% endif %}#}
        </h3>
    </div>

    <div class="import-steps">
        <div class="row">
            <div class="col-md-12">
                <ul class="step-list">
                    <li {% if router.getActionName() === 'index' %} class="active" {% endif %}>
                        1. {{ "Select import type and file"|t }}</li>
                    <li {% if router.getActionName() === 'map' %} class="active" {% endif %}>
                        2. {{ "Map columns"|t }}</li>
                    <li {% if router.getActionName() === 'overview' %} class="active" {% endif %}>
                        3. {{ "Import overview"|t }}</li>
                    <li {% if router.getActionName() === 'confirm' %} class="active" {% endif %}>
                        4. {{ "Affected rows"|t }}</li>
                    <li {% if router.getActionName() === 'complete' %} class="active" {% endif %}>
                        5. {{ "Ready for approval"|t }}</li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                {{ form('supplier/import/confirm', 'method': 'post') }}
                <table id="confirmTable" class="table table-striped">
                    <thead>
                    <th>{{ "Product Code"|t }}</th>
                    <th>{{ "Product Name"|t }}</th>
                    {% if type == 'update' %}
                        <th>{{ "New product data"|t }}</th>
                        <th>{{ "Old product data"|t }}</th>
                    {% endif %}
                    <th>{{ "Exclude from import"|t }}</th>
                    </thead>
                    <tbody>
                    {% for index, row in rows %}
                        <tr>
                            <td>{{ row['code'] }}</td>
                            <td>{{ row['name'] }}</td>
                            {% if type == 'update' %}
                                <td>
                                    {#<?php \dump($row); ?>#}
                                    {% if row['newValues'] is defined %}

                                        {#<span class="product-list-value {% if row['newValues']['name']['change'] %}active{% endif %}"><strong>{{ "Name"|t }}#}
                                                {#:</strong> {{ row['newValues']['name']['value'] }}</span>#}

                                        {% for fname, fvalue in row['newValues'] %}
                                            {% if fvalue|isArray and fvalue['change'] is defined and fvalue['change'] is true %}
                                                <span class="product-list-value"><strong>{{ fname|t }}
                                                        :</strong> {{ fvalue['value'] }}</span>
                                            {% endif %}
                                        {% endfor %}

                                        {#<span class="product-list-value {% if row['newValues']['description']['change'] %}active{% endif %}"><strong>{{ "Description"|t }}#}
                                                {#:</strong> {{ row['newValues']['description']['value']|truncate }}</span>#}
                                        {#<span class="product-list-value {% if row['newValues']['material']['change'] %}active{% endif %}"><strong>{{ "Material"|t }}#}
                                                {#:</strong> {{ row['newValues']['material']['value'] }}</span>#}
                                        {#<span class="product-list-value {% if row['newValues']['price']['change'] %}active{% endif %}"><strong>{{ "Price"|t }}#}
                                                {#:</strong> {{ row['newValues']['price']['value'] }}</span>#}
                                        {#<span class="product-list-value {% if row['newValues']['price_currency']['change'] %}active{% endif %}"><strong>{{ "Currency"|t }}#}
                                                {#:</strong> {{ row['newValues']['price_currency']['value'] }}</span>#}
                                        {#<span class="product-list-value {% if row['newValues']['image_url']['change'] %}active{% endif %}"><strong>{{ "Images urls"|t }}#}
                                                {#:</strong> {{ row['newValues']['image_url']['value'] }}</span>#}
                                    {% else %}
                                        <p>{{ "Create new product"|t }}</p>
                                    {% endif %}
                                </td>
                                <td>
                                    {% if row['oldValues'] is defined %}

                                        {#<span class="product-list-value {% if row['oldValues']['name']['change'] %}old-active{% endif %}"><strong>{{ "Name"|t }}#}
                                                {#:</strong> {{ row['oldValues']['name']['value'] }}</span>#}

                                        {% for fname, fvalue in row['newValues'] %}
                                            {% if fvalue|isArray and fvalue['change'] is defined and fvalue['change'] is true %}
                                                {% set fvalue = row['oldValues'][fname] %}
                                                <span class="product-list-value"><strong>{{ fname|t }}
                                                        :</strong> {{ fvalue['value'] }}</span>
                                            {% endif %}
                                        {% endfor %}

                                        {#<span class="product-list-value {% if row['oldValues']['name']['change'] %}old-active{% endif %}"><strong>{{ "Name"|t }}#}
                                                {#:</strong> {{ row['oldValues']['name']['value'] }}</span>#}
                                        {#<span class="product-list-value {% if row['oldValues']['description']['change'] %}old-active{% endif %}"><strong>{{ "Description"|t }}#}
                                                {#:</strong> {{ row['oldValues']['description']['value']|truncate }}</span>#}
                                        {#<span class="product-list-value {% if row['oldValues']['material']['change'] %}old-active{% endif %}"><strong>{{ "Material"|t }}#}
                                                {#:</strong> {{ row['oldValues']['material']['value'] }}</span>#}
                                        {#<span class="product-list-value {% if row['oldValues']['price']['change'] %}old-active{% endif %}"><strong>{{ "Price"|t }}#}
                                                {#:</strong> {{ row['oldValues']['price']['value'] }}</span>#}
                                        {#<span class="product-list-value {% if row['oldValues']['price_currency']['change'] %}old-active{% endif %}"><strong>{{ "Currency"|t }}#}
                                                {#:</strong> {{ row['oldValues']['price_currency']['value'] }}</span>#}
                                        {#<span class="product-list-value {% if row['oldValues']['image_url']['change'] %}old-active{% endif %}"><strong>{{ "Images urls"|t }}#}
                                                {#:</strong> {{ row['oldValues']['image_url']['value'] }}</span>#}
                                    {% else %}
                                        <p>{{ "Product does not exist"|t }}</p>
                                    {% endif %}
                                </td>
                            {% endif %}
                            <td>
                                {% if row['status']|length %}
                                    {% if isset(row['customErrorLabel']) and row['customErrorLabel'] %}
                                        {% if row['customErrorLabel'] is 'Blocked' %}
                                            {{ hidden_field('excludeProducts[]', 'value': index) }}
                                            {{ check_field('', 'value': index, 'checked': 'checked', 'disabled': 'disabled') }}
                                        {% else %}
                                            {{ check_field('excludeProducts[]', 'value': index, 'checked': 'checked') }}
                                        {% endif %}
                                        {{ row['customErrorLabel']|t }}
                                    {% else %}

                                        {{ check_field('excludeProducts[]', 'value': index, 'checked': 'checked') }}
                                        {{ "ERRORS"|t }}
                                    {% endif %}
                                    <span data-container="body" class="import-error" data-placement="left" data-trigger="hover"
                                       title="{{ "Encountered errors"|t }}:"
                                       data-content="{% for status in row['status_array'] %}{{ status|t }}</br>{% endfor %}">
                                        <i class="pe-7s-help1"></i>
                                    </span>
                                {% else %}
                                    {{ check_field('excludeProducts[]', 'value': index) }}
                                {% endif %}
                            </td>
                        </tr>
                    {% endfor %}
                    </tbody>
                </table>
                <div class="col-lg-12" style="margin-bottom: 20px;">
                    <a href="{{ url('supplier/import/overview') }}" class="btn btn-primary pull-left"><i
                                class="pe-7s-angle-left"></i> {{ "Previous step"|t }}</a>
                    {#{{ submit_button('Import >', 'class': 'btn btn-primary pull-right') }}#}
                    <button type="submit" class="btn btn-primary pull-right">{{ "Import"|t }} <i class="pe-7s-upload"></i>
                    </button>
                    {{ end_form() }}</div>
            </div>
        </div>
    </div>
{% endblock %}

{% block scripts %}
    {{ super() }}
    <script type="text/javascript">
        $(document).ready(function () {
            $('.import-error').popover({'html': true});

            var table = $('#confirmTable').DataTable( {
                pagingType: "simple_numbers",
                order: [[4, "desc"]],
                "language": {
                    "url": "/js/datatable/dutch.json"
                }
            } );
        });
    </script>

{% endblock %}