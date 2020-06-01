{% extends "layouts/main.volt" %}
{% block title %} {{ 'Tasks'|t }} {% endblock %}

{% block content %}
    <h3>
        {{ "Tasks"|t }}
        <span class="pull-right"><a href="{{ url("projects/addtask/")~projectId }}" class="btn-primary btn"><i
                        class="pe-7s-plus"></i> {{ "Add new"|t }}</a></span>
    </h3>

    <div class="row">
        <div class="col-md-12">
            <table class="task-datatable table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                <tr>
                    {#<th>{{ "ID"|t }}</th>#}
                    <th class="select-filter">{{ "Status"|t }}</th>
                    <th>{{ "Task decsription"|t }}</th>
                    <th>{{ "Created by"|t }}</th>
                    <th>{{ "Create date"|t }}</th>
                    <th class="select-filter">{{ "Assignee"|t }}</th>
                    <th class="sortbydate">{{ "Deadline"|t }}</th>
                    <th>{{ "Actions"|t }}</th>
                </tr>
                </thead>
                <tbody>
                {% for task in tasks %}
                    <tr>
                        {#<td>{{ task.id }}</td>#}
                        {% if task.status %}
                            <td data-filter="closed">
                                <i class="fa fa-check-square-o" aria-hidden="true"></i>
                            </td>
                        {% else %}
                            <td data-filter="open">
                                <i class="fa fa-square-o" aria-hidden="true"></i>
                            </td>
                        {% endif %}
                        <td>{{ task.description }}</td>
                        <td>{{ task.Users.email }}</td>
                        <td><div class="hidden">{{ task.created_at }}</div>{{ task.created_at|dttonl }}</td>
                        <td>
                            {% for assigne in task.getAssigneNames() %}
                                {{ assigne['email'] }}
                            {% endfor %}
                        </td>
                        <td>{{ task.deadline }}</td>
                        <td>
                            {% if task.status %}
                                <a href="{{ url("projects/taskstatus/" ~ projectId~"/"~task.id ) }}"
                                   class="btn btn-success btn-sm"><i class="pe-7s-gleam"></i> {{ "Open task"|t }}</a>
                            {% else %}
                                <a href="{{ url("projects/taskstatus/" ~ projectId~"/"~task.id ) }}"
                                   class="btn btn-warning btn-sm"><i class="pe-7s-gleam"></i> {{ "Close task"|t }}</a>
                            {% endif %}
                            <a href="{{ url("projects/viewtask/" ~ projectId~"/"~task.id ) }}"
                               class="btn btn-default btn-sm"><i class="pe-7s-search"></i> {{ "View"|t }}</a>
                            <a href="{{ url("projects/edittask/" ~ projectId~"/"~task.id ) }}"
                               class="btn btn-primary btn-sm"><i class="pe-7s-pen"></i> {{ "Edit"|t }}</a>
                            <a href="{{ url("projects/deletetask/" ~ projectId~"/"~task.id ) }}"
                               class="delete-task btn btn-danger btn-sm"><i class="pe-7s-trash"></i> {{ "Delete"|t }}
                            </a>
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
                <tfoot></tfoot>
            </table>
        </div>
    </div>

    {{ partial("modals/confirmGeneral", ['id': 'confirm-modal', 'title': "Delete"|t, 'content': "Are you sure you want to delete?"|t]) }}

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        $(function () {
            $('.delete-task').on('click', function (e) {
                e.preventDefault();
                $href = $(this).attr('href');
                var confirmModal = $('#confirm-modal');
                confirmModal.modal('show');
                console.log($href);

                $('.confirm-button').on('click', function () {
                    confirmModal.modal('hide');
                    window.location = $href;
                });
            });

        });
        $(window).load(function () {
            tasktable = $('.task-datatable').DataTable({
                columnDefs: [
                    { type: 'date-eu', targets: 'sortbydate' }
                ],
                "pagingType": "simple_numbers",
                "order": [[0, "desc"]],
                "language": {
                    "url": "/js/datatable/dutch.json"
                }
            });

            $(".task-datatable th.select-filter").each(function (i) {
                var name = $(this).text();
                var index = $(this).index();
                if ($(this).text() !== '') {
                    var isStatusColumn = (($(this).text() == 'Status') ? true : false);
                    var select = $('<select class="form-control">' +
                        '<option value=""></option>' +
                        '</select>')
                        .appendTo($('#DataTables_Table_0_length'))
                        .on('change', function () {
                            var val = $(this).val();
                            console.log(val);

                            tasktable.column(index)
                                .search(val ? '^' + $(this).val() + '$' : val, true, false)
                                .draw();
                        });

                    label = select.before('<label>'+name+'</label>');


                    // Get the Status values a specific way since the status is a anchor/image
                    if (isStatusColumn) {
                        var statusItems = [];

                        /* ### IS THERE A BETTER/SIMPLER WAY TO GET A UNIQUE ARRAY OF <TD> data-filter ATTRIBUTES? ### */
                        tasktable.column(index).nodes().to$().each(function (d, j) {
                            var thisStatus = $(j).attr("data-filter");
                            if ($.inArray(thisStatus, statusItems) === -1) statusItems.push(thisStatus);
                        });

                        statusItems.sort();

                        $.each(statusItems, function (i, item) {
                            select.append('<option value="' + item + '">' + item + '</option>');
                        });

                    }
                    // All other non-Status columns (like the example)
                    else {

                        tasktable.column(index).data().unique().sort().each(function (d, j) {

                            var div = document.createElement("div");
                            div.innerHTML = d;
                            var text = div.textContent || div.innerText || "";
                            var dd = $.trim(text);
                            select.append('<option value="' + text + '">' + d + '</option>');
                        });
                    }

                }
            });

        })
    </script>
{% endblock %}