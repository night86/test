{% extends "layouts/main.volt" %}
{% block title %} {{ "Recipes"|t }} {% endblock %}
{% block content %}

    <h3><a href="{{ url("signadens/product/") }}"><i class="pe-7s-back"></i></a> {{ "New recipe"|t }}</h3>

    <form id="recipeForm" action="{{ url('signadens/product/add') }}" method="post" enctype="multipart/form-data">
        <fieldset class="form-group">

            <legend></legend>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ 'Recipe number'|t }}</label>
                        <input id="recipe_number" name="recipe_number" type="number" class="form-control" required="required" min="1" />
                    </div>

                    <div class="form-group">
                        <label>{{ 'Name'|t }}</label>
                        {{ text_field('name', 'required': 'required', 'class': 'form-control') }}
                    </div>
                    <div class="form-group">
                        <label>{{ 'Product description'|t }}</label>
                        {{ text_area('description', 'class': 'form-control') }}
                    </div>
                    <div class="form-group">
                        <label>{{ 'Recipe image'|t }}</label>
                        {{ file_field('image', 'class': 'form-control') }}
                    </div>
                    {% for setting in recipeSettings %}
                        <div class="form-group">
                            <label>{{ setting.name }}</label>
                            <select name="recipe_settings[{{ setting.id }}]" class="form-control">
                                <option selected="selected" value="">-</option>
                                {% for option in setting.Options %}
                                    <option value="{{ option.id }}">{{ option.name }}</option>
                                {% endfor %}
                            </select>
                        </div>
                    {% endfor %}
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ 'Categories'|t }}</label>

                    </div>
                </div>
                <div class="col-md-6 sortable-group">

                    {#{{ dump(statuses.toArray()) }}#}
                    <div class="row">
                        <div class="col-sm-6">
                            <label>{{ 'Statuses selected'|t }}</label>
                            {{ hidden_field('statuses_selected', 'id': 'selectedValues') }}
                            <ul id="statuses-selected" class="statuses-sort">

                            </ul>
                        </div>
                        <div class="col-sm-6">
                            <label>{{ 'Statuses available'|t }}</label>
                            {{ hidden_field('available_values', 'id': 'availableValues') }}
                            <ul id="statuses-available" class="statuses-sort">
                                {% for status in statuses_av %}
                                    <li data-id="{{ status.id }}"><span class="glyphicon glyphicon-move"
                                                                        aria-hidden="true"></span> <span
                                                class="status-name">{{ status.name }}</span>
                                        <div class="status-actions"><i class="status-edit pe-7s-pen"></i><i
                                                    class="status-delete pe-7s-trash"></i></div>
                                    </li>
                                {% endfor %}
                            </ul>

                            <button id="add-new-status"
                                    class="btn btn-primary pull-right btn-block" type="button">{{ "Add new status"|t }}</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">&nbsp;</div>

            <div class="row">
                <div class="col-md-6">&nbsp;</div>
                <div class="col-md-3">&nbsp;</div>
                <div class="col-md-3">
                    <p>
                        <input type="checkbox" name="add_schema" id="add_schema" class="basic-switcher" value="0" />
                        <label for="add_schema">&nbsp;&nbsp;{{ "Add schematic teeth view"|t }}</label>
                    </p>
                    <p>
                        <input type="checkbox" name="is_basic" id="is_basic" class="basic-switcher" data-on-text="{{ "Yes"|t }}" data-off-text="{{ "No"|t }}" value="0" />
                        <label for="is_basic">&nbsp;&nbsp;{{ "This is a basic recipe"|t }}</label>
                    </p>
                </div>
            </div>

            <div class="row">&nbsp;</div>

            <legend>
                {{ 'Basic elements'|t }}
                <span class="pull-right"><a href="javascript:;" class="add-activity add-new-row"><i
                                class="pe-7s-plus"></i></a></span>
            </legend>
            <div class="row">
                <div class="col-md-12">

                    <table id="activities" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <th>{{ "Amount"|t }}</th>
                        <th width="30%">{{ "Tariff code"|t }}</th>
                        <th width="45%">{{ "Description"|t }}</th>
                        <th width="15%">{{ "Actions"|t }}</th>
                        </thead>
                        <tbody class="activities-body">
                        </tbody>
                    </table>
                </div>
            </div>

            <legend>
                {{ 'Variable elements'|t }}
                <span class="pull-right"><a href="javascript:;" class="add-new-row add-customfield"
                                            data-counter="{{ customfieldCounter }}" data-type="variable"><i
                                class="pe-7s-plus"></i></a></span>
            </legend>
            <div class="row">
                <div class="col-md-12">
                    <table id="customfields" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <th>{{ "Amount"|t }}</th>
                        <th>{{ "Field name"|t }}</th>
                        <th width="30%">{{ "Field type"|t }}</th>
                        <th width="35%">{{ "Options"|t }}</th>
                        <th width="15%">{{ "Actions"|t }}</th>
                        </thead>
                        <tbody id="variable" class="customfields-body">
                        </tbody>
                    </table>
                </div>
            </div>

            <legend>
                {{ 'Optional elements'|t }}
                <span class="pull-right"><a href="javascript:;" class="add-new-row add-customfield"
                                            data-counter="{{ customfieldCounter }}" data-type="optional"><i
                                class="pe-7s-plus"></i></a></span>
            </legend>
            <div class="row">
                <div class="col-md-12">
                    <table id="optionalfields" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <th>{{ "Amount"|t }}</th>
                        <th>{{ "Field name"|t }}</th>
                        <th width="30%">{{ "Field type"|t }}</th>
                        <th width="35%">{{ "Options"|t }}</th>
                        <th width="15%">{{ "Actions"|t }}</th>
                        </thead>
                        <tbody id="optional" class="customfields-body">
                        </tbody>
                    </table>
                </div>
            </div>

            <legend>
                {{ 'Additional information'|t }}
                <span class="pull-right"><a href="javascript:;" class="add-new-row add-customfield"
                                            data-counter="{{ customfieldCounter }}" data-type="additional"><i
                                class="pe-7s-plus"></i></a></span>
            </legend>
            <div class="row">
                <div class="col-md-12">
                    <table id="additionalfields" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <th>{{ "Amount"|t }}</th>
                        <th>{{ "Field name"|t }}</th>
                        <th width="30%">{{ "Field type"|t }}</th>
                        <th width="35%">{{ "Options"|t }}</th>
                        <th width="15%">{{ "Actions"|t }}</th>
                        </thead>
                        <tbody id="additional" class="customfields-body">
                        </tbody>
                    </table>
                </div>
            </div>

            <legend></legend>
            <div class="row">
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary pull-right"><i
                                class="pe-7s-diskette"></i> {{ "Save"|t }}</button>
                </div>
            </div>

        </fieldset>
    </form>

    <div class="activity-pattern hidden">
        <table>
            <tr class="activity-row">
                <td><input type="number" min="0" name="activity[amount][]" class="form-control" value="" /></td>
                <td>
                    <select name="activity[tariff][]" class="form-control select2-ajax">
                        {% for tariff in tariffs %}
                            {% if tariff.active %}
                                <option value="{{ tariff.id }}">{{ tariff.code }}{% if tariff.description %} - {{ tariff.description }}{% endif %}</option>
                            {% endif %}
                        {% endfor %}
                    </select>
                </td>
                <td>{{ text_area('activity[description][]', 'class': 'form-control') }}</td>
                <td><a href="javascript:;" class="btn btn-danger btn-sm activity-remove-row"><i
                                class="pe-7s-close-circle"></i></a></td>
            </tr>
        </table>
    </div>

    <div class="product-pattern hidden">
        <table>
            <tr class="product-row">
                <td>
                    {#<select name="product[product][]" class="form-control select2-ajax">#}
                        {#{% for product in products %}#}
                            {#<option value="{{ product['id'] }}">{{ product['code'] }}{% if product['name'] %} - {{ product['name'] }}{% endif %}</option>#}
                        {#{% endfor %}#}
                    {#</select>#}
                </td>
                <td>{{ text_area('product[description][]', 'class': 'form-control') }}</td>
                <td>{{ numeric_field('product[amount][]', 'class': 'form-control') }}</td>
                <td><a href="javascript:;" class="btn btn-danger btn-sm product-remove-row"><i
                                class="pe-7s-close-circle"></i></a></td>
            </tr>
        </table>
    </div>

    <div class="customfield-pattern hidden">
        <table>
            <tr class="customfield-row">
                <td>
                    {{ text_field('customfield[name][]', 'class': 'form-control') }}
                </td>
                <td>
                    <select name="customfield[type][]" class="form-control">
                        {% for fieldtype, fieldtypename in recipe.getCustomFieldTypes() %}
                            <option value="{{ fieldtype }}">{{ fieldtypename|t }}</option>
                        {% endfor %}
                    </select>
                </td>
                <td><a href="javascript:;" class="btn btn-danger btn-sm customfield-remove-row"><i
                                class="pe-7s-close-circle"></i></a></td>
            </tr>
        </table>
    </div>

    {# section with modals #}

    {{ partial("modals/newCustomField") }}

    {{ partial("modals/confirm", ['id': 'delete-status',
    'title': 'Delete status?'|t,
    'content': 'Do you want delete status ?',
    'additionalClass': 'delete-data',
    'primarybutton': 'Delete'
    ]) }}

    {#   End section with modals    #}


{% endblock %}

{% block scripts %}
    {{ super() }}
<script>
    $(function() {

        var add_schema = $('#add_schema').val();
        $('#add_schema').on('switchChange.bootstrapSwitch', function (event, state) {

            if (add_schema == 1) {
                add_schema = 0;
                $('#add_schema').val(0);
                $('#add_schema').removeAttr("checked");
            }
            else {
                add_schema = 1;
                $('#add_schema').val(1);
                $('#add_schema').attr("checked", "checked");
            }
        });

        var is_basic = $('#is_basic').val();
        $('#is_basic').on('change', function (e) {

            if (is_basic == 1) {
                is_basic = 0;
                $('#is_basic').val(0);
                $('#is_basic').removeAttr("checked");
            }
            else {
                is_basic = 1;
                $('#is_basic').val(1);
                $('#is_basic').attr("checked", "checked");
            }
        });

        $('.add_option').on('switchChange.bootstrapSwitch', function (event, state) {

            var add_option = $(this).val();
            if (add_option == 1) {
                add_option = 0;
                $(this).val(0);
            }
            else {
                add_option = 1;
                $(this).val(1);
            }
        });

        $('.add_option').on('switchChange.bootstrapSwitch', function (event, state) {

            var add_option = $(this).val();
            if (add_option == 1) {
                add_option = 0;
                $(this).val(0);
            }
            else {
                add_option = 1;
                $(this).val(1);
            }
        });

        var is_basic = $('#is_basic').val();
        $('#is_basic').on('switchChange.bootstrapSwitch', function (event, state) {

            if (is_basic == 1) {
                is_basic = 0;
                $('#is_basic').val(0);
            }
            else {
                is_basic = 1;
                $('#is_basic').val(1);
            }
        });
    });
</script>
{% endblock %}