{% extends "layouts/main.volt" %}
{% block title %} {{ 'Tasks'|t }} {% endblock %}

{% block content %}

    <div class="row">
        <div class="col-lg-12">
            <fieldset class="form-group">
                {{ form('projects/addtask/' ~ projectId, 'method': 'post') }}
                <div class="row">
                    <div class="col-md-12">
                        <legend>{{ "New task"|t }}</legend>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label for="status">{{ "Status"|t }}</label>
                                {#{{ check_field('status', 'value':1) }}#}
                                {{ select('status', status, 'required': 'required', 'class': 'form-control') }}
                                {#<select name="status" id="status" class="form-control">#}
                                    {#<option value="0" selected>{{ "Open"|t }}</option>#}
                                    {#<option value="1">{{ "Close"|t }}</option>#}
                                {#</select>#}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="description">{{ "Description"|t }}</label>
                                {{ text_area('description', 'class': 'form-control', 'required': 'required') }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="deadline">{{ "Deadline"|t }}</label>
                                {{ text_field('deadline', 'class': 'form-control datepicker-deadline', 'required': 'required') }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-gorup">
                                <label for="users">{{ "Assignee"|t }}</label>
                                <select class="select2-users" name="users[]" required="required">
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary pull-right"><i
                                class="pe-7s-diskette"></i> {{ "Save"|t }}</button>
                </div>
                {{ end_form() }}
            </fieldset>
        </div>
    </div>

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        var deadlineTime = moment().add(1, 'days').format("DD-MM-YYYY");

        $('.datepicker-deadline').datepicker({
            format: 'dd-mm-yyyy',
            "autoclose": true,
            startDate: deadlineTime,
            language: 'nl'
        });

        $(".select2-users").select2({
            placeholder: '{{ "Select users"|t }}:',
            theme: "bootstrap",
            multiple: false,
            tokenSeparators: [',', ' '],
            //dropdownParent: $(this).parent(),
//            tags: true,
            language: "nl",
            allowClear: true,
            ajax: {
                url: "/projects/addtask/"+{{ projectId }},
                dataType: 'json',
                type: "GET",
                delay: 250,
                data: function (params) {
                    var queryParameters = {
//                        projectId: $('#manageUsersProjectId').val(),
                        q: params.term,
                        page: params.page
                    }
                    return queryParameters;
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            console.log(item);
                            return {
                                text: item.email,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });
    </script>
{% endblock %}