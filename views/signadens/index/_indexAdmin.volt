<div class="row">
    <div class="col-md-6 col-sm-12">
        <h5>{{ "Imports to approve"|t }}</h5>
        <table class="simple-datatable table table-striped">
            <thead>
            <th class="sortbydate">{{ "Effective from"|t }}</th>
            <th>{{ "Type"|t }}</th>
            <th>{{ "Supplier"|t }}</th>
            <th>{{ "Action"|t }}</th>
            </thead>
            <tbody>
            {% for import in imports %}
                <tr>
                    <td><div class="hidden">{{ import.effective_from }}</div>{{ import.effective_from|dttonl }}</td>
                    <td>{{ import.type|t }}</td>
                    <td>{{ import.Organisation.name }}</td>
                    <td><a href="{{ url('/signadens/import/approve/') ~ import.id }}" class="btn btn-success btn-sm"><i class="pe-7s-like2"></i> {{ "Approve"|t }}</a></td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    </div>
    <div class="col-md-6 col-sm-12">
        <h5>{{ "All users access log"|t }}</h5>
        <table class="buttons-datatable table table-striped">
            <thead>
            <th>{{ "Date"|t }}</th>
            <th>{{ "Time"|t }}</th>
            <th>{{ "User"|t }}</th>
            <th>{{ "State"|t }}</th>
            </thead>
            <tbody>
            {% for log in logs %}
                <tr>
                    <td>{% if log.datetime is defined %}{{ timetostrdt(log.datetime) }}{% else %}-{% endif %}</td>
                    <td>{% if log.datetime is defined %}{{ datetimetotime(log.datetime) }}{% else %}-{% endif %}</td>
                    <td>{{ log.username }} ({{ log.email }})</td>
                    <td>{{ log.state|t }}</td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    </div>
</div>