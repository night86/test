{% extends "layouts/main.volt" %}
{% block title %} {{ "User departments"|t }} {% endblock %}
{% block content %}

    <p class="pull-right"><a href="{{ url("signadens/manage/adddepartment") }}" class="btn btn-primary"><i class="pe-7s-plus"></i> {{ "Add new"|t }}</a></p>
    <h3>{{ "User departments"|t }}</h3>

    <table class="simple-datatable table table-striped">
        <thead>
        <th>{{ "Name"|t }}</th>
        <th>{{ "Actions"|t }}</th>
        </thead>
        <tbody>
        {% for department in departments %}
            <tr>
                <td>{{ department.getName() }}</td>
                <td>
                    <a href="{{ url("signadens/manage/editdepartment/" ~ department.getId()) }}" class="btn btn-primary btn-sm"><i class="pe-7s-pen"></i> {{ "Edit"|t }}</a>
                    <a href="{{ url("signadens/manage/deletedepartment/" ~ department.getId()) }}" class="btn btn-danger btn-sm"><i class="pe-7s-trash"></i> {{ "Delete"|t }}</a>
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

{% endblock %}