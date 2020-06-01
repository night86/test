{% extends "layouts/main.volt" %}
{% block title %} {{ "Roles"|t }} {% endblock %}
{% block content %}

    <p class="pull-right"><a href="{{ url("signadens/role/add") }}" class="btn-primary btn "><i class="pe-7s-plus"></i> {{ "Add new"|t }}</a></p>
    <h3>{{ "Roles"|t }}</h3>

    <table class="simple-datatable table table-striped">
        <thead>
            <th>{{ "Active"|t }}</th>
            <th>{{ "Name"|t }}</th>
            <th>{{ "Description"|t }}</th>
            <th>{{ "Users with Role"|t }}</th>
            <th>{{ "Admin"|t }}</th>
            <th>{{ "Actions"|t }}</th>
        </thead>
        <tbody>
            {% for role in roles %}
                <tr>
                    <td>{{ active[role.getActive()] }}</td>
                    <td>{{ role.getName() }}</td>
                    <td>{{ role.getDescription() }}</td>
                    <td><a href="{{ url("signadens/user/?role=" ~ role.getId()) }}">{{ role.countUsers() }}</a></td>
                    <td>{{ active[role.getIsAdmin()] }}</td>
                    <td>
                        <a href="{{ url("signadens/role/edit/" ~ role.getId()) }}" class="btn btn-primary btn-sm"><i class="pe-7s-pen"></i> {{ "Edit"|t }}</a>
                        {% if role.getActive() == 1 %}
                            <a href="{{ url("signadens/role/deactivate/" ~ role.getId()) }}" class="btn btn-danger btn-sm"><i class="pe-7s-close-circle"></i> {{ "Deactivate"|t }}</a>
                        {% else %}
                            <a href="{{ url("signadens/role/activate/" ~ role.getId()) }}" class="btn btn-success btn-sm"><i class="pe-7s-gleam"></i> {{ "Activate"|t }}</a>
                        {% endif %}
                    </td>
                </tr>
            {% endfor %}
        </tbody>
    </table>

{% endblock %}