{% extends "layouts/main.volt" %}
{% block title %} {{ "Notifications"|t }} {% endblock %}
{% block content %}

    <p class="pull-right"><a href="{{ url("notification/print") }}" class="btn-info btn "><i class="pe-7s-print"></i> {{ "Print"|t }}</a></p>
    <h3>{{ "Notifications"|t }}</h3>

    <table id="notifications" class="table table-striped" width="100%">
        <thead>
            <th>{{ "Type"|t }}</th>
            <th>{{ "Subject"|t }}</th>
            <th class="sortbydate">{{ "Received"|t }}</th>
            <th>{{ "Actions"|t }}</th>
        </thead>
    </table>

    {{ partial("modals/empty", ['id': 'inbox-modal' ]) }}
    {{ partial("modals/notificationNonReply", ['id': 'inbox-modal-non-reply' ]) }}

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script type="text/javascript" src="/js/app/notification.js"></script>
    <script>
        var successMessage = '{{ 'Your reply has been send.'|t }}';
        var errorMessage = '{{ 'Your message cannot be created.'|t }}';
        var productRemoval = '{{ 'Product will be removed on the requested date.'|t }}';
        $(function(){
            notification.init("{{ url('notification/read') }}", "{{ url('notification/ajaxlist') }}");
            notification.setNotificationType({{ notificationType }});
            notification.initDataTables();
        });
    </script>

{% endblock %}