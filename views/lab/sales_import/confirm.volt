{% extends "layouts/main.volt" %}
{% block title %} {{ "Import"|t }} {% endblock %}
{% block content %}

    <div class="col-lg-12">
        <h3>{{ "Import"|t }}</h3>
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
                {{ form('lab/sales_import/confirm', 'method': 'post') }}
                <table class="simple-datatable-import table table-striped">
                    <thead>
                    <th>{{ "Code"|t }}</th>
                    <th>{{ "Description"|t }}</th>
                    {% if type == 'tariff' %}
                        <th>{{ "Price"|t }}</th>
                    {% elseif type == 'ledger' %}
                        <th>{{ "Group type"|t }}</th>
                        <th>{{ "Balance type"|t }}</th>
                        <th>{{ "Balance side"|t }}</th>
                    {% endif %}
                    <th>{{ "Exclude from import"|t }}</th>
                    </thead>
                    <tbody>
                    {% for index, row in rows %}
                        <tr>
                            <td>{{ row['code'] }}</td>
                            <td>{{ row['description'] }}</td>
                            {% if type == 'tariff' %}
                                <td>{{ row['price'] }}</td>
                            {% elseif type == 'ledger' %}
                                <td>{{ row['group_type'] }}</td>
                                <td>{{ row['balance_type'] }}</td>
                                <td>{{ row['balance_side'] }}</td>
                            {% endif %}
                            <td><input type="checkbox" name="excludeProducts[]" value="{{ index }}" {% if row['status'] == "ERROR" %}checked="checked"{% endif %} /></td>
                        </tr>
                    {% endfor %}
                    </tbody>
                </table>
                <div class="col-lg-12" style="margin-bottom: 20px;">
                    <a href="{{ url('lab/sales_import/overview') }}" class="btn btn-primary pull-left"><i
                                class="pe-7s-angle-left"></i> {{ "Previous step"|t }}</a>
                    {#{{ submit_button('Import >', 'class': 'btn btn-primary pull-right') }}#}
                    <button type="submit" class="btn btn-primary pull-right">{{ "Import"|t }} <i class="pe-7s-upload"></i>
                    </button>
                    {{ end_form() }}</div>
            </div>
        </div>
    </div>
{% endblock %}