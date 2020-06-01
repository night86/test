{% extends "layouts/main.volt" %}
{% block title %} {{ "Import"|t }} {% endblock %}
{% block content %}

    <div class="col-lg-12">
        <h3>{{ "Import"|t }}
            {#{% if importType is not "create" %}#}
                {#<small>{{ "You'll only import changes to exisiting products."|t }}</small>#}
            {#{% endif %}#}
        </h3>
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
                <table class="overview-datatable table table-striped" cellspacing="0" width="100%">
                    <thead>
                        {% for maps_header in maps_headers %}
                            <th class="{{ maps_header.name }}">{{ maps_header.description|t }}{% if maps_header.req is 1 %}*{% endif %}</th>
                        {% endfor %}
                        <th class="status">{{ "status"|t }}</th>
                    </thead>
                    <tbody>
                        {% for row in rows %}
                            <tr>
                                {% for index, maps_header in maps_headers %}
                                    <td>{% if row[maps_header.name] is defined %}{{ row[maps_header.name]|truncate }}{% endif %}</td>
                                {% endfor %}
                                {% if row['status']|length %}
                                    <td>{% if isset(row['customErrorLabel']) and row['customErrorLabel'] %}{{ row['customErrorLabel']|t }}{% else %}{{ "ERRORS"|t }}{% endif %} <span data-container="body" class="import-error" data-placement="left" data-trigger="hover"
                                                    title="{{ "Encountered errors"|t }}:"
                                                    data-content="{% for status in row['status_array'] %}{{ status|t }}</br>{% endfor %}"><i class="pe-7s-help1"></i></span></td>
                                {% else %}
                                    <td>{{ "OK"|t }}</td>
                                {% endif %}
                            </tr>
                        {% endfor %}
                    </tbody>
                </table>
                <div class="col-lg-12" style="margin-bottom: 20px;">
                    <a href="{{ url('supplier/import/map') }}" class="btn btn-primary pull-left"><i class="pe-7s-angle-left"></i> {{ "Previous step"|t }}</a>
                    <a href="{{ url('supplier/import/confirm') }}" class="btn btn-primary pull-right">{{ "Next step"|t }} <i class="pe-7s-angle-right"></i></a>
                </div>
            </div>
        </div>
    </div>

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script type="text/javascript">
        $(window).load(function () {
            $('.import-error').popover({'html': true});
            setTimeout(function(){
                var table = $('.overview-datatable').DataTable( {
                    pagingType: "simple_numbers",
                    order: [[19, "desc"]],
                    dom: 'C<"clear">lfrtip',
                    scrollX: true,
                    columnDefs: [
                        { targets: ['name', 'price', 'price_currency', 'code', 'manufacturer', 'status'], visible: true },
                        { targets: '_all', visible: false }
                    ],
                    colVis: {
                        align: "right",
                        buttonText: "{{ "Select columns"|t }}",
                        showAll: "{{ "Show all"|t }}",
                        exclude: [ ],
                        restore: "{{ "Show default"|t }}"
                    },
                    "language": {
                        "url": "/js/datatable/dutch.json"
                    }
                } );

                $('.dataTables_wrapper ').addClass('colVis-wrapper');
            }, 100);
        });


    </script>

{% endblock %}