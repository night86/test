{% extends "layouts/main.volt" %}
{% block title %} {{ 'Projects'|t }} {% endblock %}
{% block content %}

    <p class="pull-right">
        <button class="btn btn-primary" data-toggle="modal" data-target="#add-project">{{ "Add new"|t }} <i
                    class="pe-7s-plus"></i></button>
    </p>
    <h3>{{ "Projects"|t }}</h3>

    <table class="simple-datatable table table-striped">
        <thead>
            <th>{{ 'Name'|t }}</th>
            <th class="sortbydate">{{ 'Project created'|t }}</th>
            <th class="sortbydate">{{ 'Last edit in project'|t }}</th>
            <th>{{ 'Users'|t }}</th>
            <th>{{ 'Actions'|t }}</th>
        </thead>
        <tbody>
        {% for project in projects %}
            <tr>
                <td>
                    {% if currentUser.getId() is project.getCreatedBy() %}
                        <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                        <strong>{{ project.getName() }}</strong>
                    {% else %}
                        {{ project.getName() }}
                    {% endif %}

                </td>
                <td>{{ project.getCreatedAt() }}</td>
                <td>{{ project.getEditedAt() }}</td>
                <td>{{ project.getCountedUsers() }}</td>
                <td>
                    <a href="{{ url("projects/view/" ~ project.getId()) }}" class="btn btn-success btn-sm"><i
                                class="pe-7s-look"></i> {{ "View"|t }}</a>
                    {% if currentUser.getId() is project.getCreatedBy() %}
                        <button class="editProjectBtn btn btn-primary btn-sm" data-toggle="modal"
                                data-target="#edit-project" data-projectid="{{ project.getId() }}"
                                data-projectname="{{ project.getName() }}"><i class="pe-7s-pen"></i> {{ "Edit"|t }}
                        </button>
                        <button class="manageUsersBtn btn btn-warning btn-sm" data-toggle="modal"
                                data-target="#manage-users" data-projectid="{{ project.getId() }}"><i
                                    class="pe-7s-users"></i> {{ "Manage users"|t }}</button>
                        <a href="{{ url("projects/delete/" ~ project.getId()) }}" class="btn btn-danger btn-sm"><i
                                    class="pe-7s-trash"></i> {{ "Delete"|t }}</a>
                    {% else %}
                        <a href="{{ url("projects/leave/" ~ project.getId()) }}" class="btn btn-danger btn-sm"><i class="fa fa-sign-out" aria-hidden="true"></i> {{ "Leave project"|t }}</a>
                    {% endif %}
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>
    <div class="col-xs-12">
        <i class="fa fa-user-circle-o" aria-hidden="true"></i> - {{ "User project"|t }}
    </div>



    {{ partial("modals/addProject", ['id': 'add-project', 'title': 'Add project'|t, 'content': addModalContent, 'additionalClass': 'save-project']) }}
    {{ partial("modals/addProject", ['id': 'edit-project', 'title': 'Edit project'|t, 'content': editModalContent, 'additionalClass': 'save-project']) }}
    {{ partial("modals/manageUsersForProject", ['id': 'manage-users', 'title': 'Manage users'|t, 'content': manageUsersContent, 'additionalClass': 'save-users']) }}

{% endblock %}


{% block scripts %}
    {{ super() }}
    <script>
        $(function () {
            $('.editProjectBtn').click(function () {
                $('#edit-project').find('#projectId').val($(this).data('projectid'));
                $('#edit-project').find('#projectName').val($(this).data('projectname'));
            });
            $('.manageUsersBtn').click(function () {
                $('#manage-users').find('#manageUsersProjectId').val($(this).data('projectid'));
            });
            $(".save-project").click(function (e) {
                e.preventDefault();
                $(this).closest('.modal-content').find('form').submit();
            });
        });
    </script>
{% endblock %}