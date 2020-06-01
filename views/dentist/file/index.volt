{% extends "layouts/main.volt" %}
{% block title %} {{ "Files"|t }} {% endblock %}
{% block content %}

    <p class="pull-right"><a href="{{ url("dentist/file/upload") }}" id="showUploader" class="btn-primary btn "><i class="pe-7s-cloud-upload"></i> {{ "Upload file"|t }}</a></p>
    <h3>{{ "Files"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                <th>{{ "Name"|t }}</th>
                <th>{{ "Owner"|t }}</th>
                <th>{{ "Upload date"|t }}</th>
                <th>{{ "File size"|t }}</th>
                <th>{{ "Shared with"|t }}</th>
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
                                {% if file.getCreatedBy() == currentUser.getId() %}
                                    {% if file.hasShared() %}
                                        <a href="{{ url('dentist/file/editshare/' ~ file.getId()) }}" class="btn btn-primary btn-sm edit-share" data-url="{{ url('dentist/file/share/' ~ file.getId()) }}"><i class="pe-7s-refresh-cloud"></i> {{'Edit share'|t}}</a>
                                    {% else %}
                                        <a href="{{ url('dentist/file/share/' ~ file.getId()) }}" class="btn btn-primary btn-sm new-share"><i class="pe-7s-cloud"></i> {{'Share'|t}}</a>
                                    {% endif %}
                                {% else %}
                                    -
                                {% endif %}
                            </td>
                            <td>
                                <a href="{{ url('dentist/file/download/' ~ file.getId()) }}" class="btn btn-success btn-sm delete"><i class="pe-7s-cloud-download"></i> {{'Download'|t}}</a>
                                {% if file.getCreatedBy() == currentUser.getId() %}
                                    <a href="{{ url('dentist/file/delete/' ~ file.getId()) }}" class="btn btn-danger btn-sm delete"><i class="pe-7s-trash"></i> {{'Delete'|t}}</a>
                                {% endif %}
                            </td>
                        </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

    {{ partial("modals/alert", ['id': 'upload-modal', 'title': 'Upload file'|t, 'content': uploadContent]) }}
    {{ partial("modals/confirmGeneral", ['id': 'share-modal', 'confirmButton': 'Share'|t, 'title': 'Share file'|t, 'content': shareContent]) }}
    {{ partial("modals/confirmGeneral", ['id': 'editshare-modal', 'confirmButton': 'Share'|t, 'title': 'Share file'|t, 'content': '']) }}

{% endblock %}