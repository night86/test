{% extends "layouts/main.volt" %}
{% block title %} {{'Users'|t}} {% endblock %}
{% block content %}

    <h3>{{ "Users"|t }}</h3>

    <div class="row">
        <div class="col-md-12 padding-15">
            <select name="organisation" id="organisation-list" class="form-control" style="width: 200px; display: inline-block">
                <option value="0">{{ "Select organisation"|t }}</option>
                {% for organisation in organisations %}
                    <option value="{{ organisation.getName() }}">{{ organisation.getName() }}</option>
                {% endfor %}
            </select>
            <select name="role" id="role-list" class="form-control" style="width: 200px; display: inline-block">
                <option value="0">{{ "Select role"|t }}</option>
                {% for role in roles %}
                    <option value="{{ role.getName() }}">{{ role.getName() }}</option>
                {% endfor %}
            </select>
            <span class="pull-right"><a href="{{ url("signadens/user/add") }}" class="btn-primary btn"><i class="pe-7s-add-user"></i> {{ "Add new"|t }}</a></span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table id="users" class="table table-striped" style="width: 100%">
                <thead>
                    <th>{{'Email'|t}}</th>
                    <th>{{'Active'|t}}</th>
                    <th>{{'First name'|t}}</th>
                    <th>{{'Last name'|t}}</th>
                    <th>{{'Organisation'|t}}</th>
                    <th>{{'Role'|t}}</th>
                    <th>{{'Actions'|t}}</th>
                </thead>
            </table>
        </div>
    </div>

{% endblock %}
{% block scripts %}
    {{ super() }}
    <script>
        $(function(){
            user.init("{{ url('/signadens/user/ajaxlist') }}");
            user.initDataTables();
        });
    </script>
{% endblock %}