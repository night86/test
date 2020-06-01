{% extends "layouts/main.volt" %}
{% block title %} {{ "Recipes"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Production time"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table id="recipes" class="simple-datatable table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>{{ "Avaliable steps"|t }}</th>
                        <th>{{ "Required amount of work days"|t }}</th>
                    </tr>
                </thead>
                <tbody>
                    {% for status in statuses_av %}
                        <tr>
                            <td>{{ status.getName() }}</td>
                            <td>
                                <input
                                    type="number"
                                    class="form-control numeric status-days"
                                    min="0"
                                    value="{% if isset(statuses_times[status.getId()]) %}{{ statuses_times[status.getId()].getDays() }}{% else %}{% endif %}"
                                    data-statusid="{{ status.getId() }}"
                                    data-statustimeid="{% if isset(statuses_times[status.getId()]) %}{{ statuses_times[status.getId()].getId() }}{% else %}0{% endif %}"
                                />
                            </td>
                        </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        $(document).ready(function(){
            $(document).off('change');
            $(document).on('change', '.status-days', function() {
                var el = $(this);
                el.attr('disabled', 'disabled');
                $.post('/lab/sales_recipe/productiontimeupdate', {
                    days: el.val(),
                    statusId: el.data('statusid'),
                    statusTimeId: el.data('statustimeid')
                }, function (data) {
                    el.data('statustimeid', data.element.id)
                    el.removeAttr('disabled');
                }, 'json');
            });
        });

    </script>
{% endblock %}