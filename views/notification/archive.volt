{% extends "layouts/main.volt" %}
{% block title %} {{ "Notifications"|t }} {% endblock %}
{% block content %}

    <p class="pull-right"><a href="{{ url("notification/print/archive") }}" class="btn-info btn "><i class="pe-7s-print"></i> {{ "Print"|t }}</a></p>
    <h3>{{ "Notifications"|t }}</h3>

    <table id="notifications" class="table table-striped">
        <thead>
        <th>{{ "Type"|t }}</th>
        <th>{{ "Subject"|t }}</th>
        <th class="sortbydate">{{ "Received"|t }}</th>
        <th>{{ "Actions"|t }}</th>
        </thead>
        <tbody>
        {% for notification in notifications %}
            <tr>
                {% if notification.getReadAt() is not null %}
                    <td>{{ notification.getTypeLabel() }}</td>
                    <td>{{ notification.getSubject() }}</td>
                    <td><div class="hidden">{{ notification.getCreatedAt() }}</div>{{ notification.getCreatedAt()|dttonl }}</td>
                {% else %}
                    <td><strong>{{ notification.getTypeLabel() }}</strong></td>
                    <td><strong>{{ notification.getSubject() }}</strong></td>
                    <td><div class="hidden">{{ notification.getCreatedAt() }}</div><strong>{{ notification.getCreatedAt()|dttonl }}</strong></td>
                {% endif %}
                <td>
                    <a class="showModal btn btn-primary btn-sm" data-id="{{ notification.getId() }}" data-description="{{ notification.getDescription() }}" data-subject="{{ notification.getSubject() }}"><i class="pe-7s-search"></i> {{ "Read"|t }}</a>
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    {{ partial("modals/empty", ['id': 'inbox-modal' ]) }}

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script type="text/javascript" src="/js/app/notification.js"></script>
    <script>
        $(function(){
            notification.init("{{ url('notification/read') }}");
            notification.initDataTables();
        });
    </script>

{% endblock %}