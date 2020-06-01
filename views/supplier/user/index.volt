{% extends "layouts/main.volt" %}
{% block title %} {{'Users'|t}} {% endblock %}
{% block content %}

    <p><a href="{{ url("supplier/user/add") }}" class="btn-primary btn "><i class="pe-7s-plus"></i> {{ "Add new"|t }}</a></p>
    <table class="simple-datatable table table-striped">
        <thead>
            <th>{{'Active'|t}}</th>
            <th>{{'Email'|t}}</th>
            <th>{{'First name'|t}}</th>
            <th>{{'Last name'|t}}</th>
            <th>{{'Organisation'|t}}</th>
            <th>{{'Role'|t}}</th>
            <th>{{'Actions'|t}}</th>
        </thead>
        <tbody>
            {% for user in users %}
                <tr>
                    <td>{{ active[user.getActive()]|t }}</td>
                    <td>{{ user.getEmail() }}</td>
                    <td>{{ user.getFirstName() }}</td>
                    <td>{{ user.getLastName() }}</td>
                    <td>{{ user.organisation.getName() }}</td>
                    <td>{{ user.roleTemplate.name }}</td>
                    <td>
                        <a href="{{ url("supplier/user/edit/" ~ user.getId()) }}" class="btn btn-primary btn-sm"><i class="pe-7s-pen"></i> {{ "Edit"|t }}</a>
                        {% if user.getActive() == 1 %}
                            <a href="{{ url("supplier/user/deactivate/" ~ user.getId()) }}" class="btn btn-danger btn-sm"><i class="pe-7s-close-circle"></i> {{ "Deactivate"|t }}</a>
                        {% else %}
                            <a href="{{ url("supplier/user/activate/" ~ user.getId()) }}" class="btn btn-success btn-sm"><i class="pe-7s-gleam"></i> {{ "Activate"|t }}</a>
                        {% endif %}
                        <a href="{{ url("supplier/user/loginasuser/" ~ user.getId()) }}" class="btn btn-warning btn-sm"><i class="pe-7s-glasses"></i> {{ "Login as"|t }}</a>
                    </td>
                </tr>
            {% endfor %}
        </tbody>
    </table>

{% endblock %}