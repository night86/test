<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    {% if currentUser.hasRole('ROLE_SUPPLIER_DASHBOARD_IMPORT_PRODUCTS') %}
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingImportLog">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" href="#collapseImportLog"
                       aria-expanded="true" aria-controls="collapseImportLog">
                        {% if importsCount > 0 %}
                            <span class="badge">{{ importsCount }}</span>
                        {% endif %}
                        {{ "Log product import / update"|t }}
                    </a>
                </h4>
            </div>
            <div id="collapseImportLog" class="panel-collapse collapse" role="tabpanel"
                 aria-labelledby="headingImportLog">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            {% if imports is not empty %}
                                <table class="table table-striped table-bordered" cellspacing="0"
                                       width="100%">
                                    <thead>
                                        <th class="sortbydate">{{ "Date"|t }}</th>
                                        <th>{{ "Type"|t }}</th>
                                        <th>{{ "Status"|t }}</th>
                                    </thead>
                                    <tbody>
                                    {% for k,import in imports if k < 5 %}
                                        <tr>
                                            <td><div class="hidden">{{ import.getDateTimeArr()['date'] }}</div>{{ import.getDateTimeArr()['date']|dttonl }}</td>
                                            <td>{% if import.type === 'create' %} {{ "Product import"|t }} {% else %} {{ "Product update"|t }} {% endif %}</td>
                                            <td>{% if import.closed %} {{ "Approved"|t }} {% else %} {{ "In queue"|t }} {% endif %}</td>
                                        </tr>
                                    {% endfor %}
                                    </tbody>
                                </table>
                                <a href="{{ url('supplier/import/log') }}" class="btn btn-primary pull-right">{{ "Show all"|t }}</a>
                            {% endif %}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {% endif %}

    {% if currentUser.hasRole('ROLE_SUPPLIER_DASHBOARD_IMPORT_NOTIFICATIONS') %}
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingImportNotifyLog">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" href="#collapseImportNotifyLog"
                       aria-expanded="true" aria-controls="collapseImportNotifyLog">
                        {% if notifications is not empty and notifications|length > 0 %}
                            <span class="badge">{{ notifications|length }}</span>
                        {% endif %}
                        {{ "Notification about import / update"|t }}
                    </a>
                </h4>
            </div>
            <div id="collapseImportNotifyLog" class="panel-collapse collapse" role="tabpanel"
                 aria-labelledby="headingImportNotifyLog">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            {% if notifications is not empty %}
                                <table class="table table-striped table-bordered" cellspacing="0"
                                       width="100%">
                                    <thead>
                                    <th class="sortbydate">{{ "Receiving date"|t }}</th>
                                    <th>{{ "Notification content"|t }}</th>
                                    </thead>
                                    <tbody>
                                    {% for k,notification in notifications if k < 5 %}
                                        <tr>
                                            <td><div class="hidden">{{ notification.getCreatedAt() }}</div>{{ notification.getCreatedAt()|dttonl }}</td>
                                            <td>{{ notification.getDescription()|decodeString }}</td>
                                        </tr>
                                    {% endfor %}
                                    </tbody>
                                </table>
                                <a href="{{ url('notification/index/?type=2') }}" class="btn btn-primary pull-right">{{ "Show all"|t }}</a>
                            {% endif %}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {% endif %}

</div>