{% extends "layouts/main.volt" %}
{% block title %} {{'Project'|t}}: {{ project.getName() }} {{ 'Notes'|t }} {% endblock %}

{% block content %}

    <p class="pull-right">
        <a href="{{ url("projects/addnote/")~projectId }}" class="btn-primary btn"><i
                    class="pe-7s-plus"></i> {{ "New note"|t }}</a>
    </p>

    <div class="row">
        <div class="col-md-12">
            <table class="table simple-datatable table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                <th>{{ "Subject"|t }}</th>
                <th>{{ "File link"|t }}</th>
                <th>{{ "Owner"|t }}</th>
                <th>{{ "Created date"|t }}</th>
                <th>{{ "Actions"|t }}</th>
                </thead>
                <tbody>
                {% for file in files %}
                    <tr>
                        <td><b>{{ file.title }}</b><br />{{ file.content }}</td>
                        <td>
                            {% for oneFile in file.Files %}
                                <a href="{{ url('projects/downloadnotefile/'~ projectId ~ '/' ~ oneFile.getId()) }}" class="btn btn-default btn-sm"><i class="pe-7s-cloud-download"></i> {{ oneFile.getNameOriginal() }}</a>
                            {% endfor %}

                        </td>
                        <td>{{ file.CreatedBy.getFullname() }}</td>
                        <td><div class="hidden">{{ file.getCreatedAt() }}</div>{{ file.getCreatedAt()|dttonl }}</td>
                        <td>

                            {% if file.getCreatedBy() == currentUser.getId() %}
                                <a href="{{ url('projects/editnote/'~ projectId ~ '/' ~ file.getId()) }}" class="btn btn-primary btn-sm"><i class="pe-7s-pen"></i> {{'Edit'|t}}</a>
                                <a href="{{ url('projects/removenote/'~ projectId ~ '/' ~ file.getId()) }}" class="btn btn-danger btn-sm delete"><i class="pe-7s-trash"></i> {{'Delete'|t}}</a>
                            {% endif %}
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

    {{ partial("modals/alert", ['id': 'upload-modal', 'title': 'New note'|t, 'content': uploadContent]) }}

{% endblock %}


{% block scripts %}
    {{ super() }}
    <script>
        $(function(){

        });
    </script>
{% endblock %}