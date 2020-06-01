<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingUsersLog">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" href="#collapseUsersLog"
                   aria-expanded="true" aria-controls="collapseUsersLog">
                    {% if userslog is not empty %}
                        <span class="badge">{{ userslog|length }}</span>
                    {% endif %}
                    {{ "Users log"|t }}
                </a>
            </h4>
        </div>
        <div id="collapseUsersLog" class="panel-collapse collapse" role="tabpanel"
             aria-labelledby="headingUsersLog">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        {% if userslog is not empty %}
                            <table class="table table-striped table-bordered" cellspacing="0"
                                   width="100%">
                                <thead>
                                    <th class="sortbydate">{{ "Date"|t }}</th>
                                    <th>{{ "Time"|t }}</th>
                                    <th>{{ "User"|t }}</th>
                                    <th>{{ "State"|t }}</th>
                                </thead>
                                <tbody>
                                {% for k,userlog in userslog if k < 5 %}
                                    <tr>
                                        <td>{% if userlog.datetime is defined %}{{ timetostrdt(userlog.datetime) }}{% else %}-{% endif %}</td>
                                        <td>{% if userlog.datetime is defined %}{{ datetimetotime(userlog.datetime) }}{% else %}-{% endif %}</td>
                                        <td>{{ userlog.username }} ({{ userlog.email }})</td>
                                        <td>{{ userlog.state|t }}</td>
                                    </tr>
                                {% endfor %}
                                </tbody>
                            </table>
                            {#<a href="{{ url('lab/sales_order/incoming') }}" class="btn btn-primary pull-right">{{ "Show all"|t }}</a>#}
                        {% endif %}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>