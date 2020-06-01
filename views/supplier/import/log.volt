{% extends "layouts/main.volt" %}
{% block title %} {{ "Import"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Import list"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table class="simple-datatable table table-striped">
                <thead>
                    <th class="sortbydate">{{ "Date"|t }}</th>
                    <th>{{ "Type"|t }}</th>
                    <th>{{ "Status"|t }}</th>
                </thead>
                <tbody>
                {% for import in imports %}
                    <tr>
                        <td><div class="hidden">{{ import.getDateTimeArr()['date'] }}</div>{{ import.getDateTimeArr()['date']|dttonl }}</td>
                        <td>{% if import.type === 'create' %} {{ "Product import"|t }} {% else %} {{ "Product update"|t }} {% endif %}</td>
                        <td>{% if import.closed %} {{ "Approved"|t }} {% else %} {{ "In queue"|t }} {% endif %}</td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

{% endblock %}