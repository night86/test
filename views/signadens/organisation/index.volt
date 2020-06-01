{% extends "layouts/main.volt" %}
{% block title %} {{ "Organisations"|t }} {% endblock %}
{% block content %}

    <p class="pull-right"><a href="{{ url("signadens/organisation/add") }}" class="btn-primary btn "><i class="pe-7s-plus"></i> {{ "Add new"|t }}</a></p>
    <h3>{{ "Organisations"|t }}</h3>

    <table class="simple-datatable table table-striped">
        <thead>
            <th>{{ "Active"|t }}</th>
            <th>{{ "Type"|t }}</th>
            <th>{{ "Name"|t }}</th>
            <th>{{ "Address"|t }}</th>
            <th>{{ "Zip Code"|t }}</th>
            <th>{{ "City"|t }}</th>
            <th>{{ "Country"|t }}</th>
            <th>{{ "Phone"|t }}</th>
            <th>{{ "Users"|t }}</th>
            <th>{{ "Actions"|t }}</th>
        </thead>
        <tbody>
            {% for organisation in organisations %}
                <tr>
                    <td>{{ active[organisation.getActive()]|t }}</td>
                    <td>{% if organisation.OrganisationType %}{{ organisation.OrganisationType.getName()|t }}{% else %}-{% endif %}</td>
                    <td>{{ organisation.getName() }}</td>
                    <td>{{ organisation.getAddress() }}</td>
                    <td>{{ organisation.getZipcode() }}</td>
                    <td>{{ organisation.getCity() }}</td>
                    <td>{% if organisation.country %}{{ organisation.country.getCode() }}{% else %}-{% endif %}</td>
                    <td>{{ organisation.getTelephone() }}</td>
                    <td>{{ organisation.countUsers() }}</td>
                    <td>
                        <a href="{{ url("signadens/organisation/edit/" ~ organisation.getId()) }}" class="btn btn-primary btn-sm"><i class="pe-7s-pen"></i> {{ "Edit"|t }}</a>
                        {% if organisation.getActive() == 1 %}
                            <a href="{{ url("signadens/organisation/deactivate/" ~ organisation.getId()) }}" class="btn btn-danger btn-sm"><i class="pe-7s-close-circle"></i> {{ "Deactivate"|t }}</a>
                        {% else %}
                            <a href="{{ url("signadens/organisation/activate/" ~ organisation.getId()) }}" class="btn btn-success btn-sm"><i class="pe-7s-gleam"></i> {{ "Activate"|t }}</a>
                        {% endif %}
                    </td>
                </tr>
            {% endfor %}
        </tbody>
    </table>

{% endblock %}