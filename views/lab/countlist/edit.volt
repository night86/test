{% extends "layouts/main.volt" %}
{% block title %} {{ "Countlist"|t }} {% endblock %}
{% block content %}

    <h3>
        <a href="{{ url("lab/countlist/") }}"><i class="pe-7s-back"></i></a>
        {{ "Countlist"|t }}
        {% if countlistStatus != 3 %}
            <span id="addRow" class="btn btn-primary pull-right"><i class="pe-7s-plus"></i> {{ "Add product"|t }}</span>
        {% endif %}
    </h3>

    <div class="row">
        <div class="col-md-12">
            <table id="countlist" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <th>{{ "Product name"|t }}</th>
                    <th>{{ "Product material"|t }}</th>
                    <th>{{ "Supplier"|t }}</th>
                    <th>{{ "Product code"|t }}</th>
                    <th>{{ "Product price"|t }}</th>
                    <th>{{ "Amount"|t }}</th>
                </thead>
            </table>
            {% if countlistStatus != 3 %}
                <a href="{{ url('/lab/countlist/save/'~countlistId) }}" id="completeCount" class="btn btn-primary pull-right"><i class="pe-7s-angle-down-circle"></i> {{ "Save and complete count"|t }}</a>
                <a href="{{ url('/lab/countlist/save/'~countlistId) }}" id="saveCount" class="btn btn-primary pull-right"><i class="pe-7s-angle-down"></i> {{ "Save count"|t }}</a>
            {% endif %}
        </div>
    </div>

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script type="text/javascript" src="/js/app/countlist.js"></script>
    <script>
        $(function(){
            countlist.init("{{ url('/lab/countlist/ajaxcountlistview/'~countlistId) }}");
            countlist.initDataTablesView();
        });
    </script>
{% endblock %}