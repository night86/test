{% extends "layouts/main.volt" %}
{% block title %} Signadens {% endblock %}
{% block content %}

    <h3>{{ "Import list"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table class="simple-datatable table table-striped">
                <thead>
                <th>{{ "ID"|t }}</th>
                <th>{{ "Type"|t }}</th>
                <th class="sortbydate">{{ "Effective from"|t }}</th>
                <th>{{ "Supplier"|t }}</th>
                <th>{{ "Added by"|t }}</th>
                <th>{{ "Action"|t }}</th>
                </thead>
                <tbody>
                {% for import in imports %}
                    <tr>
                        <td>{{ import.id }}</td>
                        <td>{{ import.type|t }}</td>
                        <td><div class="hidden">{{ import.effective_from }}</div>{{ import.effective_from|dttonl }}</td>
                        <td>{{ import.Organisation.name }}</td>
                        <td>
                            {% if import.Created %}
                                {{ import.Created.getFullName() }}
                            {% else %}
                                User not found
                            {% endif %}
                        </td>
                        <td>
                            <a href="{{ url('/signadens/import/approve/') ~ import.id }}" class="btn btn-success btn-sm"><i class="pe-7s-like2"></i> {{ "Approve"|t }}</a>
                            {#<a href="" class="btn btn-default btn-sm"><i class="pe-7s-display2"></i> {{ "Show products without category"|t }}</a>#}
                            {#<a href="" class="btn btn-default btn-sm"><i class="pe-7s-edit"></i> {{ "Show missing tariff code ranges"|t }}</a>#}
                            {#<a href="" class="btn btn-info btn-sm"><i class="pe-7s-play"></i> {{ "Run script again"|t }}</a>#}
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

{% endblock %}