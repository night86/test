{% extends "layouts/main.volt" %}
{% block title %} {{'Dashboard'|t}} {% endblock %}
{% block content %}

    <h3>{{ "Import log"|t }}</h3>

    <div class="row">
        <div class="col-md-12 col-sm-12">
            <table id="importlog" class="buttons-datatable table table-striped">
                <thead>
                <th class="sortbydate">{{ "Date"|t }}</th>
                <th>{{ "Type"|t }}</th>
                <th>{{ "File name"|t }}</th>
                <th class="sortbydate">{{ "Commencing date"|t }}</th>
                <th>{{ "Action"|t }}</th>
                </thead>
                <tbody>
                {% for import in imports %}
                    <tr>
                        <td>{% if import.datetime is defined %}<div class="hidden">{{ timetostrdt(import.datetime) }}</div>{{ timetostrdt(import.datetime)|dttonl }}{% else %}-{% endif %}
                        {% if import.datetime is defined %}{{ datetimetotime(import.datetime) }}{% else %}-{% endif %}</td>
                        <td>{% if import.file.type === 'create' %} {{ "Product import"|t }} {% else %} {{ "Product update"|t }} {% endif %}</td>
                        <td>{{  import.file.name }}</td>
                        <td><div class="hidden">{{ import.file.effective_from }}</div>{{  import.file.effective_from|dttonl }}</td>
                        <td>{% if import.import_id is defined %}<a href="{{ url('supplier/importlog/view/'~import.import_id) }}" class="btn btn-primary"><i class="pe-7s-search"></i> {{ 'More details'|t }}</a>{% else %}-{% endif %}</td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

{% endblock %}