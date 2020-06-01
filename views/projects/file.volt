{% extends "layouts/main.volt" %}
{% block title %} {{'Project'|t}}: {{ project.getName() }} {{ 'Files'|t }} {% endblock %}

{% block content %}

    <p class="pull-right"><a href="{{ url("projects/uploadfile") }}" id="showUploader" class="btn-primary btn "><i class="pe-7s-cloud-upload"></i> {{ "Upload file"|t }}</a></p>

    <div class="row">
        <div class="col-md-12">
            <table class="table simple-datatable table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                <th>{{ "Name"|t }}</th>
                <th>{{ "Owner"|t }}</th>
                <th>{{ "Upload date"|t }}</th>
                <th>{{ "File size"|t }}</th>
                <th>{{ "Actions"|t }}</th>
                </thead>
                <tbody>
                {% for file in files %}
                    <tr>
                        <td>{{ file.getNameOriginal() }}</td>
                        <td>{{ file.CreatedBy.getFullname() }}</td>
                        <td><div class="hidden">{{ file.getCreatedAt() }}</div>{{ file.getCreatedAt()|dttonl }}</td>
                        <td>{{ file.getSize()|t }}</td>
                        <td>
                            <a href="{{ url('projects/downloadfile/'~ projectId ~ '/' ~ file.getId()) }}" class="btn btn-success btn-sm delete"><i class="pe-7s-cloud-download"></i> {{'Download'|t}}</a>
                            {% if file.getCreatedBy() == currentUser.getId() %}
                                <a href="{{ url('projects/deletefile/'~ projectId ~ '/' ~ file.getId()) }}" class="btn btn-danger btn-sm delete"><i class="pe-7s-trash"></i> {{'Delete'|t}}</a>
                            {% endif %}
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

    {{ partial("modals/alert", ['id': 'upload-modal', 'title': 'Upload file'|t, 'content': uploadContent]) }}

{% endblock %}


{% block scripts %}
    {{ super() }}
    <script>
        $(function(){

        });
    </script>
{% endblock %}