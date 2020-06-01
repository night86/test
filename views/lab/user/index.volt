{% extends "layouts/main.volt" %}
{% block title %} {{ 'Users'|t }} {% endblock %}
{% block content %}

    <p class="pull-right"><a href="{{ url("lab/user/add") }}" class="btn btn-primary">{{ "Add new"|t }} <i
                    class="pe-7s-plus"></i></a></p>



    <h3>{{ "Users"|t }}</h3>

    <table class="simple-datatable table table-striped">
        <thead>
        <th>{{ 'Active'|t }}</th>
        <th>{{ 'Email'|t }}</th>
        <th>{{ 'First name'|t }}</th>
        <th>{{ 'Last name'|t }}</th>
        <th>{{ 'Organisation'|t }}</th>
        <th>{{ 'Department'|t }}</th>
        <th>{{ 'Role'|t }}</th>
        <th>{{ 'Actions'|t }}</th>
        </thead>
        <tbody>
        {% for user in users %}
            <tr>
                <td>{{ active[user.getActive()]|t }}</td>
                <td>{{ user.getEmail() }}</td>
                <td>{{ user.getFirstName() }}</td>
                <td>{{ user.getLastName() }}</td>
                <td>{{ user.organisation.getName() }}</td>
                {% if  user.department is not null %}
                    <td>{{ user.department.getName()}}</td>
                {% else%}
                    <td>-</td>
                {% endif %}
                <td>{{ user.roleTemplate.name }}</td>
                <td>

                    <a href="{{ url("lab/user/edit/" ~ user.getId()) }}" class="btn btn-primary btn-sm"><i
                                class="pe-7s-pen"></i> {{ "Edit"|t }}</a>
                    {% if user.getActive() == 1 %}
                        <a href="{{ url("lab/user/deactivate/" ~ user.getId()) }}" class="btn btn-danger btn-sm"><i
                                    class="pe-7s-close-circle"></i> {{ "Deactivate"|t }}</a>
                    {% else %}
                        <a href="{{ url("lab/user/activate/" ~ user.getId()) }}" class="btn btn-success btn-sm"><i
                                    class="pe-7s-gleam"></i> {{ "Activate"|t }}</a>
                    {% endif %}
                    <a href="{{ url("lab/user/loginasuser/" ~ user.getId()) }}" class="btn btn-warning btn-sm"><i
                                class="pe-7s-glasses"></i> {{ "Login as"|t }}</a>

                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

{% endblock %}