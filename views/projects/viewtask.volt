{% extends "layouts/main.volt" %}
{% block title %} {{ 'Tasks'|t }} {% endblock %}

{% block content %}
    <h3>
        {{ "Task"|t }} {{ task.id }}
    </h3>

    <div class="row">
        <div class="col-md-5">
            <label>{{ "Description"|t }}</label>
            <div class="row">
                <div class="col-md-12">
                    {{ task.description }}
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <label>{{ "Deadline"|t }}</label>
            <div class="row">
                <div class="col-md-12">
                    {{ task.deadline }}
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <label>{{ "Assignee"|t }}</label>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>{{ "Email" }}</th>
                    <th>{{ "Name" }}</th>
                </tr>
                </thead>
                <tbody>
                {% for assignee in task.getAssigneNames() %}
                    <tr>
                        <td>{{ assignee['email'] }}</td>
                        <td>{{ assignee['firstname'] }} {{ assignee['lastname'] }}</td>
                    </tr>
                {% endfor %}

                </tbody>
            </table>

        </div>
    </div>

{% endblock %}

{% block scripts %}
    {{ super() }}
{% endblock %}