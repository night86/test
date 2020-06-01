{% extends "layouts/main.volt" %}
{% block title %} {{ "Countlist"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Countlist"|t }} <a href="add" class="btn btn-primary pull-right"><i class="pe-7s-plus"></i> {{ "New countlist"|t }}</a></h3>

    <div class="row">
        <div class="col-md-12">
            <table id="countlist" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <th>{{ "Count date"|t }}</th>
                    <th>{{ "Count completedate"|t }}</th>
                    <th>{{ "Counted by user"|t }}</th>
                    <th>{{ "Counted value"|t }}</th>
                    <th>{{ "Actions"|t }}</th>
                </thead>
            </table>
        </div>
    </div>

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        $(function(){
            countlist.init("{{ url('/lab/countlist/ajaxcountlist') }}");
            countlist.initDataTables();
        });
    </script>
{% endblock %}