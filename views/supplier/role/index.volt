{% extends "layouts/main.volt" %}
{% block title %} {{'Roles'|t}} {% endblock %}
{% block content %}

    <p><a href="{{ url("supplier/role/add") }}" class="btn-primary btn ">{{ "Add new"|t }} <i class="pe-7s-plus"></i></a></p>
    <table class="simple-datatable table table-striped">
        <thead>
            <th>{{'Active'|t}}</th>
            <th>{{'Name'|t}}</th>
            <th>{{'Description'|t}}</th>
            <th>{{'Users with Role'|t}}</th>
            <th>{{'Actions'|t}}</th>
        </thead>
        <tbody>
            {% for role in roles %}
                <tr>
                    <td>{{ active[role.getActive()] }}</td>
                    <td>{{ role.getName() }}</td>
                    <td>{{ role.getDescription() }}</td>
                    <td><a href="{{ url("supplier/user/?role=" ~ role.getId()) }}">{{ role.countUsers() }}</a></td>
                    <td>
                        <a href="{{ url("supplier/role/edit/" ~ role.getId()) }}" class="btn btn-primary btn-sm"><i class="pe-7s-pen"></i> {{ "Edit"|t }}</a>
                        {% if role.getActive() == 1 %}
                            <a href="{{ url("supplier/role/deactivate/" ~ role.getId()) }}" class="btn btn-danger btn-sm"><i class="pe-7s-close-circle"></i> {{ "Deactivate"|t }}</a>
                        {% else %}
                            <a href="{{ url("supplier/role/activate/" ~ role.getId()) }}" class="btn btn-success btn-sm"><i class="pe-7s-gleam"></i> {{ "Activate"|t }}</a>
                        {% endif %}
                    </td>
                </tr>
            {% endfor %}
        </tbody>
    </table>

{% endblock %}