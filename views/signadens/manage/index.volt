{% extends "layouts/main.volt" %}
{% block title %} {{ "Framework agreements"|t }} {% endblock %}
{% block content %}

    <h3>
        {{ "Framework agreements"|t }}
        <span class="pull-right"><a href="{{ url("signadens/manage/add") }}" class="btn-primary btn"><i class="pe-7s-plus"></i> {{ "Add new"|t }}</a></span>
    </h3>

    <div class="row">
        <div class="col-md-12">
            <table class="simple-datatable table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <th>{{ "Number"|t }}</th>
                    <th>{{ "Name supplier"|t }}</th>
                    <th class="sortbydate">{{ "Starting date"|t }}</th>
                    <th class="sortbydate">{{ "Due date"|t }}</th>
                    <th>{{ "Status"|t }}</th>
                    <th>{{ "Actions"|t }}</th>
                </thead>
                <tbody>
                {% if agreements is not null %}
                    {% for agreement in agreements %}
                        <tr>
                            <td>{{ agreement.id }}</td>
                            <td>{{ agreement.Organisation.name }}</td>
                            <td>{{ agreement.start_date }}</td>
                            <td>{{ agreement.due_date }}</td>
                            <td>{% if agreement.active != 0 %}{{ 'Active'|t }}{% else %}{{ 'Inactive'|t }}{% endif %}</td>
                            <td>
                                <a href="{{ url('signadens/manage/view/' ~ agreement.id) }}" class="btn btn-primary btn-sm"><i class="pe-7s-note2"></i> {{ 'View'|t }}</a>
                                {% if agreement.startDateDiffWithCurrent() > 0 %}
                                    <a href="{{ url('signadens/manage/edit/' ~ agreement.id) }}" class="btn btn-primary btn-sm"><i class="pe-7s-pen"></i> {{ 'Edit'|t }}</a>
                                {% endif %}
                                <a href="{{ url('signadens/manage/duplicate/' ~ agreement.id) }}" class="btn btn-primary btn-sm"><i class="pe-7s-copy-file"></i> {{ 'Duplicate'|t }}</a>
                                {% if agreement.active %}
                                    <a href="{{ url('signadens/manage/deactivate/' ~ agreement.id) }}" class="btn btn-warning btn-sm"><i class="pe-7s-close-circle"></i> {{'Deactivate'|t}}</a>
                                {% else %}
                                    <a href="{{ url('signadens/manage/activate/' ~ agreement.id) }}" class="btn btn-success btn-sm"><i class="pe-7s-gleam"></i> {{'Activate'|t}}</a>
                                {% endif %}
                            </td>
                        </tr>
                    {% endfor %}
                    {% endif %}
                </tbody>
            </table>
        </div>
    </div>

{% endblock %}
