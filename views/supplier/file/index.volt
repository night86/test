{% extends "layouts/main.volt" %}
{% block title %} {{ "Files"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Files"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table id="history" class="table table-striped table-bordered" cellspacing="0" width="100%">
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
                                <a href="{{ url('supplier/file/download/' ~ file.getId()) }}" class="btn btn-success btn-sm delete"><i class="pe-7s-cloud-download"></i> {{'Download'|t}}</a>
                            </td>
                        </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

{% endblock %}