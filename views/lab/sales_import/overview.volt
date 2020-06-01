{% extends "layouts/main.volt" %}
{% block title %} {{ "Import"|t }} {% endblock %}
{% block content %}

    <div class="col-lg-12">
        <h3>{{ "Import"|t }}</h3>
    </div>

    <div class="import-steps">
        <div class="row">
            <div class="col-md-12">
                <ul class="step-list">
                    <li {% if router.getActionName() === 'index' %} class="active" {% endif %}>
                        1. {{ "Select import type and file"|t }}</li>
                    <li {% if router.getActionName() === 'map' %} class="active" {% endif %}>
                        2. {{ "Map columns"|t }}</li>
                    <li {% if router.getActionName() === 'overview' %} class="active" {% endif %}>
                        3. {{ "Import overview"|t }}</li>
                    <li {% if router.getActionName() === 'confirm' %} class="active" {% endif %}>
                        4. {{ "Affected rows"|t }}</li>
                    <li {% if router.getActionName() === 'complete' %} class="active" {% endif %}>
                        5. {{ "Ready for approval"|t }}</li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="simple-datatable-import table table-striped">
                    <thead>
                    {% for maps_header in maps_headers %}
                        <th>{{ maps_header.description|t }}</th>
                    {% endfor %}
                    <th>{{ "status"|t }}</th>
                    </thead>
                    <tbody>
                    {% for row in rows %}
                        <tr>
                            {% for index, maps_header in maps_headers %}
                                <td>{{ row[maps_header.name]|truncate }}</td>
                            {% endfor %}
                            {% if row['status']|length %}
                                <td>{{ "ERROR"|t }} <span data-container="body" class="import-error" data-placement="left" data-trigger="hover"
                                                title="{{ "Encountered errors"|t }}:"
                                                data-content="{{ row['status']|t }}"><i class="pe-7s-help1"></i></span></td>
                            {% else %}
                                <td>{{ "OK"|t }}</td>
                            {% endif %}
                        </tr>
                    {% endfor %}
                    </tbody>
                </table>
                <div class="col-lg-12" style="margin-bottom: 20px;">
                    <a href="{{ url('lab/sales_import/map') }}" class="btn btn-primary pull-left"><i class="pe-7s-angle-left"></i> {{ "Previous step"|t }}</a>
                    <a href="{{ url('lab/sales_import/confirm') }}" class="btn btn-primary pull-right">{{ "Next step"|t }} <i class="pe-7s-angle-right"></i></a>
                </div>
            </div>
        </div>
    </div>

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script type="text/javascript">
        $(document).ready(function () {
            $('.import-error').popover({'html': true});
        });
    </script>

{% endblock %}