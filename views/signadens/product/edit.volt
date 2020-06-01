{% extends "layouts/main.volt" %}
{% block title %} {{ "Recipes"|t }} {% endblock %}
{% block content %}

    <h3><a href="{{ url("signadens/product/") }}"><i class="pe-7s-back"></i></a> {{ "Edit recipe"|t }}
        : {{ recipe.name }}</h3>

    <form id="recipeForm" action="{{ url('signadens/product/edit/' ~ recipe.code ) }}" method="post" enctype="multipart/form-data">
        <fieldset class="form-group">

            <legend></legend>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ 'Recipe number'|t }}</label>
                        <input id="recipe_number" name="recipe_number" type="number" class="form-control" required="required" min="1" value="{{ recipe.recipe_number }}" />
                    </div>
                    <div class="form-group">
                        <label>{{ 'Name'|t }}</label>
                        {{ text_field('name', 'required': 'required', 'class': 'form-control', 'value': recipe.name) }}
                    </div>
                    <div class="form-group">
                        <label>{{ 'Product description'|t }}</label>
                        {{ text_area('description', 'class': 'form-control', 'value': recipe.description) }}
                    </div>
                    <div class="form-group">
                        <label>{{ 'Recipe image'|t }}</label>
                        {{ file_field('image', 'class': 'form-control') }}
                    </div>
                    {% if recipe.image is not null %}
                        <div class="form-group">
                            <a href="{{ url("signadens/product/deleteimageedit/")~recipe.code }}"
                               class="btn btn-danger"><i class="pe-7s-trash"></i> {{ "Delete image"|t }}</a>
                        </div>
                    {% endif %}
                    {% for setting in recipeSettings %}
                        {% if selectedSettings|isArray and in_array(setting.id, selectedSettings) %}
                            {% for sel in previousSettings %}
                                {% if sel.setting_id == setting.id %}
                                    <div class="form-group">
                                        <label>{{ setting.name }}</label>
                                        <select name="recipe_settings_old[{{ sel.id }}]" class="form-control">
                                            {% if sel.option_id is null %}
                                                <option selected="selected" value="-">-</option>
                                            {% else %}
                                                <option value="-">-</option>
                                            {% endif %}
                                            {% for option in setting.Options %}
                                                {% if sel.option_id == option.id %}
                                                    <option value="{{ option.id }}" selected="selected">{{ option.name }}</option>
                                                {% else %}
                                                    <option value="{{ option.id }}">{{ option.name }}</option>
                                                {% endif %}
                                            {% endfor %}
                                        </select>
                                    </div>
                                {% endif %}
                            {% endfor %}
                        {% else %}
                            <div class="form-group">
                                <label>{{ setting.name }}</label>
                                <select name="recipe_settings_new[{{ setting.id }}]" class="form-control">
                                    <option selected="selected" value="">-</option>
                                    {% for option in setting.Options %}
                                        <option value="{{ option.id }}">{{ option.name }}</option>
                                    {% endfor %}
                                </select>
                            </div>
                        {% endif %}
                    {% endfor %}
                    {% if groupDiscount|length > 0 %}
                        <label>{{ 'Discount dentist groups'|t }}</label>
                        <table id="groups-discount" class="table table-striped table-bordered" cellspacing="0"
                               width="100%">
                            <thead>
                            <th>{{ "Dentist group"|t }}</th>
                            <th>{{ "Discount type"|t }}</th>
                            <th>{{ "Value"|t }}</th>
                            <th>{{ "Action"|t }}</th>
                            </thead>
                            <tbody>
                            {% for discount in groupDiscount %}
                                <tr>
                                    <td>{{ discount.Organisation.getName() }}</td>
                                    <td>{{ discount.getAddedTypeLabels()|t }}</td>
                                    <td>{{ discount.getValue() }}</td>
                                    <td><a class="btn btn-danger"
                                           href="{{ url("/signadens/product/deletegroup/" ~ discount.getId()) }}"><i
                                                    class="pe-7s-trash"></i></a></td>
                                </tr>
                            {% endfor %}
                            </tbody>
                        </table>
                    {% endif %}
                    <button type="button" data-toggle="modal" class="btn btn-primary"
                            data-target="#manage-dentistgroups">{{ "Add dentist group"|t }} <i class="pe-7s-plus"></i>
                    </button>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ 'Categories'|t }}</label>
                        <ul>
                            {% for categoryRow in recipe.getCategriesSringArray() %}
                                <li>{{ categoryRow }}</li>
                            {% endfor %}
                        </ul>
                    </div>
                    {% if recipe.image !== null %}
                        <div class="form-group">
                            <img src="{{ image_url ~ recipe.image }}" width="300" class="img-responsive">
                        </div>
                    {% endif %}
                </div>
                <div class="col-md-6 sortable-group">

                    {#{{ dump(statuses.toArray()) }}#}
                    <div class="row">
                        <div class="col-sm-6">
                            <label>{{ 'Statuses selected'|t }}</label>
                            {{ hidden_field('statuses_selected', 'id': 'selectedValues') }}
                            <ul id="statuses-selected" class="statuses-sort">
                                {% if statuses_se %}
                                    {% for status in statuses_se %}
                                        {% if status %}
                                        <li data-id="{{ status['id'] }}">
                                            <span class="glyphicon glyphicon-move" aria-hidden="true"></span>
                                            <span class="status-name">{{ status['name'] }}</span>
                                            <div class="status-actions">
                                                <i class="status-edit pe-7s-pen"></i>
                                                <i class="status-delete pe-7s-trash"></i>
                                            </div>
                                        </li>
                                        {% endif %}
                                    {% endfor %}
                                {% endif %}
                            </ul>
                        </div>
                        <div class="col-sm-6">
                            <label>{{ 'Statuses available'|t }}</label>
                            {{ hidden_field('available_values', 'id': 'availableValues') }}
                            <ul id="statuses-available" class="statuses-sort">
                                {% for status in statuses_av %}
                                    {% if not in_array(status.getId(), statuses_se_id) %}
                                        <li data-id="{{ status.id }}">
                                            <span class="glyphicon glyphicon-move" aria-hidden="true"></span>
                                            <span class="status-name">{{ status.name }}</span>
                                            <div class="status-actions">
                                                <i class="status-edit pe-7s-pen"></i>
                                                <i class="status-delete pe-7s-trash"></i>
                                            </div>
                                        </li>
                                    {% endif %}
                                {% endfor %}
                            </ul>

                            <button id="add-new-status" class="btn btn-primary pull-right btn-block" type="button">{{ "Add new status"|t }}</button>

                        </div>
                    </div>
                </div>
            </div>


            <div class="clearfix"></div>

            <div class="row">&nbsp;</div>

            <div class="row">
                <div class="col-md-6">&nbsp;</div>
                <div class="col-md-3">&nbsp;</div>
                <div class="col-md-3">
                    <p>
                        <input type="checkbox" name="add_schema" id="add_schema" class="basic-switcher" value="{{ recipe.has_schema }}" {% if recipe.has_schema == 1 %}checked="checked"{% endif %} />
                        <label for="add_schema">&nbsp;&nbsp;{{ "Add schematic teeth view"|t }}</label>
                    </p>
                    <p>
                        <input type="checkbox" name="is_basic" id="is_basic" class="basic-switcher" value="{{ recipe.is_basic }}" {% if recipe.is_basic == 1 %}checked="checked"{% endif %} />
                        <label for="is_basic">&nbsp;&nbsp;{{ "This is a basic recipe"|t }}</label>
                    </p>
                </div>
            </div>


            <div id="row_notice" class="row" {% if recipe.has_schema == 0 %}style="display: none;"{% endif %}>
                <div class="col-md-6">&nbsp;</div>
                <div class="col-md-3">&nbsp;</div>
                <div class="col-md-3">
                    <textarea id="schema_notice" name="schema_notice" style="width: 300px;">{{ recipe.schema_notice }}</textarea>
                </div>
            </div>


            <div class="row">&nbsp;</div>

            <div class="row" style="margin-top: 30px;">
                <div class="col-lg-12">
                    <legend>
                        {{ 'Basic elements'|t }}
                        <span class="pull-right"><a href="javascript:;" class="add-activity add-new-row"><i
                                        class="pe-7s-plus"></i></a></span>
                    </legend>
                </div>

                <div class="col-md-12">

                    <table id="activities" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <th>{{ "Amount"|t }}</th>
                        <th width="30%">{{ "Tariff code"|t }}</th>
                        <th width="45%">{{ "Description"|t }}</th>
                        <th width="15%">{{ "Actions"|t }}</th>
                        </thead>
                        <tbody class="activities-body">
                        {% for index, activity in recipe.RecipeActivity %}
                            <tr class="activity-row">
                                <td><input type="number" min="0" name="activity[amount][{{ index }}]" class="form-control" value="{{ activity.amount }}" /></td>
                                <td>
                                    <select name="activity[tariff][{{ index }}]" class="form-control select2-input">
                                        {% for tariff in tariffs %}
                                            <option {% if activity.tariffId is tariff.id %}selected="selected"{% endif %}
                                                    value="{{ tariff.id }}">{{ tariff.code }}{% if tariff.description %} - {{ tariff.description }}{% endif %}</option>
                                        {% endfor %}
                                    </select>
                                </td>
                                <td>{{ text_area('activity[description]['~index~']', 'class': 'form-control', 'value': activity.description) }}</td>
                                <td><a href="javascript:;" class="btn btn-danger btn-sm activity-remove-row"><i
                                                class="pe-7s-close-circle"></i></a></td>
                            </tr>
                        {% endfor %}
                        </tbody>
                    </table>
                </div>

                <div class="col-lg-12">
                    <legend>
                        {{ 'Variable elements'|t }}
                        <span class="pull-right"><a href="javascript:;" class="add-new-row add-customfield"
                                                    data-counter="{{ variableCounter }}" data-type="variable"><i
                                        class="pe-7s-plus"></i></a></span>
                    </legend>
                </div>
                <div class="col-md-12">
                    <table id="customfields" class="table table-striped table-bordered" cellspacing="0"
                           width="100%">
                        <thead>
                            <th>{{ "Amount"|t }}</th>
                            <th>{{ "Field name"|t }}</th>
                            <th width="30%">{{ "Field type"|t }}</th>
                            <th width="35%">{{ "Options"|t }}</th>
                            <th width="15%">{{ "Actions"|t }}</th>
                        </thead>
                        <tbody id="variable" class="customfields-body">
                        {% for index, customFieldRecipe in recipe.RecipeCustomField %}
                            {% if customFieldRecipe.custom_field_type is 'variable' %}
                            <tr class="customfield-row">
                                <td><input type="number" min="0" name="variable[amount][{{ index }}]" class="form-control" value="{{ customFieldRecipe.amount }}" /></td>
                                <td>
                                    {{ text_field('variable[name]['~index~']', 'class': 'form-control', 'value': customFieldRecipe.name) }}
                                </td>
                                <td>
                                    <select name="variable[type][{{ index }}]" class="form-control" disabled>
                                        {% for fieldtype, fieldtypename in recipe.getCustomFieldTypes() %}
                                            <option {% if customFieldRecipe.type is fieldtype %}selected="selected"{% endif %}
                                                    value="{{ fieldtype }}">{{ fieldtypename|t }}</option>
                                        {% endfor %}
                                    </select>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <div class="clearfix"></div>
                                    </div>

                                    {% if customFieldRecipe.type is 'statement' %}
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <select name="params[{{ index }}][statement]"
                                                        class="form-control select2-input customtariff">
                                                    <option value="0">{{ "Custom price"|t }}</option>
                                                    {% for tariff in tariffs %}
                                                        <option {% if tariff.id is customFieldRecipe.getCustomPriceTariffId() %}selected="selected"{% endif %}
                                                                value="{{ tariff.id }}">{{ tariff.code }}{% if tariff.description %} - {{ tariff.description }}{% endif %}</option>
                                                    {% endfor %}
                                                </select>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    {% endif %}

                                    {% if customFieldRecipe.type is 'textarea' %}
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <textarea name="params[{{ index }}][textarea]" draggable="false"></textarea>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    {% endif %}

                                    {% if customFieldRecipe.type is 'number' %}
                                        <div class="form-group">
                                            <div class="col-md-3">
                                                <select name="params[{{ index }}][numberprice]"
                                                        class="form-control select2-input customtariff">
                                                    <option value="0">-</option>
                                                    {% for tariff in tariffs %}
                                                        <option {% if tariff.id is customFieldRecipe.getCustomPriceTariffId() %}selected="selected"{% endif %}
                                                                value="{{ tariff.id }}">{{ tariff.code }}{% if tariff.description %} - {{ tariff.description }}{% endif %}</option>
                                                    {% endfor %}
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <div><label><input
                                                                {% if customFieldRecipe.getCustomPriceType() is 1 %}checked="checked"{% endif %}
                                                                type="radio"
                                                                name="params[{{ index }}][numberpricechoose]"
                                                                value="1"/> {{ "Single additional price"|t }}
                                                    </label></div>
                                                <div><label><input
                                                                {% if customFieldRecipe.getCustomPriceType() is 2 %}checked="checked"{% endif %}
                                                                type="radio"
                                                                name="params[{{ index }}][numberpricechoose]"
                                                                value="2"/> {{ "Additional price per item"|t }}
                                                    </label></div>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    {% endif %}

                                    {% for k,option in customFieldRecipe.Options %}
                                        {#{{ text_field('customfield[options]['~index~'][]', 'class': 'form-control', 'value': option.getOption()) }}#}

                                        <div class="form-group">
                                            <div class="col-md-5">
                                                {{ text_field('variable[options][' ~ index ~ '][' ~ k ~ ']', 'class': 'form-control', 'value': option.getOption()) }}
                                            </div>

                                            <div class="col-md-5">
                                                <select name="field_option[{{ index }}][{{ k }}][selecttariff]"
                                                        class="form-control select2-input customtariff">
                                                    <option value="0">{{ "Custom price"|t }}</option>
                                                    {% for tariff in tariffs %}
                                                        <option {% if tariff.id is option.getTariffId() %}selected="selected"{% endif %}
                                                                value="{{ tariff.id }}">{{ tariff.code }}{% if tariff.description %} - {{ tariff.description }}{% endif %}</option>
                                                    {% endfor %}
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number"
                                                       name="field_option[{{ index }}][{{ k }}][numberselectprice]"
                                                       value="{{ option.getCustomPriceTariffId() }}" class="form-control numeric" />
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>

                                    {% endfor %}
                                    {% if customFieldRecipe.type is not 'statement' and customFieldRecipe.type is not 'textarea' %}
                                        <br />
                                        <input type="checkbox" name="params[{{ index }}][field_lab]" class="field_lab" value="{{ customFieldRecipe.has_lab_check }}" {% if customFieldRecipe.has_lab_check is 1 %}checked="checked"{% endif %} />
                                        <label for="add_option">&nbsp;&nbsp;{{ "To be determined by lab"|t }}</label>
                                    {% endif %}
                                </td>
                                <td><a href="javascript:;" class="btn btn-danger btn-sm customfield-remove-row"><i
                                                class="pe-7s-close-circle"></i></a></td>
                            </tr>
                            {% endif %}
                        {% endfor %}
                        </tbody>
                    </table>
                </div>

                <div class="col-lg-12">
                    <legend>
                        {{ 'Optional elements'|t }}
                        <span class="pull-right"><a href="javascript:;" class="add-new-row add-customfield"
                                                    data-counter="{{ optionalCounter }}" data-type="optional"><i
                                        class="pe-7s-plus"></i></a></span>
                    </legend>
                </div>
                <div class="col-md-12">
                    <table id="customfields" class="table table-striped table-bordered" cellspacing="0"
                           width="100%">
                        <thead>
                            <th>{{ "Amount"|t }}</th>
                            <th>{{ "Field name"|t }}</th>
                            <th width="30%">{{ "Field type"|t }}</th>
                            <th width="35%">{{ "Options"|t }}</th>
                            <th width="15%">{{ "Actions"|t }}</th>
                        </thead>
                        <tbody id="optional" class="customfields-body">
                        {% for index, customFieldRecipe in recipe.RecipeCustomField %}
                            {% if customFieldRecipe.custom_field_type is 'optional' %}
                                <tr class="customfield-row">
                                    <td><input type="number" min="0" name="optional[amount][{{ index }}]" class="form-control" value="{{ customFieldRecipe.amount }}" /></td>
                                    <td>
                                        {{ text_field('optional[name]['~index~']', 'class': 'form-control', 'value': customFieldRecipe.name) }}
                                    </td>
                                    <td>
                                        <select name="optional[type][{{ index }}]" class="form-control" disabled>
                                            {% for fieldtype, fieldtypename in recipe.getCustomFieldTypes() %}
                                                <option {% if customFieldRecipe.type is fieldtype %}selected="selected"{% endif %}
                                                        value="{{ fieldtype }}">{{ fieldtypename|t }}</option>
                                            {% endfor %}
                                        </select>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="clearfix"></div>
                                        </div>

                                        {% if customFieldRecipe.type is 'statement' %}
                                            <div class="form-group">
                                                <div class="col-md-6">
                                                    <select name="params[{{ index }}][statement]"
                                                            class="form-control select2-input customtariff">
                                                        <option value="0">{{ "Custom price"|t }}</option>
                                                        {% for tariff in tariffs %}
                                                            <option {% if tariff.id is customFieldRecipe.getCustomPriceTariffId() %}selected="selected"{% endif %}
                                                                    value="{{ tariff.id }}">{{ tariff.code }}{% if tariff.description %} - {{ tariff.description }}{% endif %}</option>
                                                        {% endfor %}
                                                    </select>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        {% endif %}

                                        {% if customFieldRecipe.type is 'textarea' %}
                                            <div class="form-group">
                                                <div class="col-md-6">
                                                    <textarea name="params[{{ index }}][textarea]" draggable="false"></textarea>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        {% endif %}

                                        {% if customFieldRecipe.type is 'number' %}
                                            <div class="form-group">
                                                <div class="col-md-3">
                                                    <select name="params[{{ index }}][numberprice]"
                                                            class="form-control select2-input customtariff">
                                                        <option value="0">-</option>
                                                        {% for tariff in tariffs %}
                                                            <option {% if tariff.id is customFieldRecipe.getCustomPriceTariffId() %}selected="selected"{% endif %}
                                                                    value="{{ tariff.id }}">{{ tariff.code }}{% if tariff.description %} - {{ tariff.description }}{% endif %}</option>
                                                        {% endfor %}
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <div><label><input
                                                                    {% if customFieldRecipe.getCustomPriceType() is 1 %}checked="checked"{% endif %}
                                                                    type="radio"
                                                                    name="params[{{ index }}][numberpricechoose]"
                                                                    value="1"/> {{ "Single additional price"|t }}
                                                        </label></div>
                                                    <div><label><input
                                                                    {% if customFieldRecipe.getCustomPriceType() is 2 %}checked="checked"{% endif %}
                                                                    type="radio"
                                                                    name="params[{{ index }}][numberpricechoose]"
                                                                    value="2"/> {{ "Additional price per item"|t }}
                                                        </label></div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        {% endif %}

                                        {% for k,option in customFieldRecipe.Options %}
                                            <div class="form-group">
                                                <div class="col-md-5">
                                                    {{ text_field('optional[options][' ~ index ~ '][' ~ k ~ ']', 'class': 'form-control', 'value': option.getOption()) }}
                                                </div>

                                                <div class="col-md-5">
                                                    <select name="field_option[{{ index }}][{{ k }}][selecttariff]"
                                                            class="form-control select2-input customtariff">
                                                        <option value="0">{{ "Custom price"|t }}</option>
                                                        {% for tariff in tariffs %}
                                                            <option {% if tariff.id is option.getTariffId() %}selected="selected"{% endif %}
                                                                    value="{{ tariff.id }}">{{ tariff.code }}{% if tariff.description %} - {{ tariff.description }}{% endif %}</option>
                                                        {% endfor %}
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number"
                                                           name="field_option[{{ index }}][{{ k }}][numberselectprice]"
                                                           value="{{ option.getCustomPriceTariffId() }}" class="form-control numeric" />
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>

                                        {% endfor %}
                                        {% if customFieldRecipe.type is not 'statement' %}
                                            <br />
                                            <input type="checkbox" name="params[{{ index }}][field_lab]" class="field_lab" value="{{ customFieldRecipe.has_lab_check }}" {% if customFieldRecipe.has_lab_check is 1 %}checked="checked"{% endif %} />
                                            <label for="add_option">&nbsp;&nbsp;{{ "To be determined by lab"|t }}</label>
                                        {% endif %}
                                    </td>
                                    <td><a href="javascript:;" class="btn btn-danger btn-sm customfield-remove-row"><i
                                                    class="pe-7s-close-circle"></i></a></td>
                                </tr>
                            {% endif %}
                        {% endfor %}
                        </tbody>
                    </table>
                </div>

                <div class="col-lg-12">
                    <legend>
                        {{ 'Additional information'|t }}
                        <span class="pull-right"><a href="javascript:;" class="add-new-row add-customfield"
                                                    data-counter="{{ additionalCounter }}" data-type="additional"><i
                                        class="pe-7s-plus"></i></a></span>
                    </legend>
                </div>
                <div class="col-md-12">
                    <table id="customfields" class="table table-striped table-bordered" cellspacing="0"
                           width="100%">
                        <thead>
                            <th>{{ "Amount"|t }}</th>
                            <th>{{ "Field name"|t }}</th>
                            <th width="30%">{{ "Field type"|t }}</th>
                            <th width="35%">{{ "Options"|t }}</th>
                            <th width="15%">{{ "Actions"|t }}</th>
                        </thead>
                        <tbody id="additional" class="customfields-body">
                        {% for index, customFieldRecipe in recipe.RecipeCustomField %}
                            {% if customFieldRecipe.custom_field_type is 'additional' %}
                                <tr class="customfield-row">
                                    <td><input type="number" min="0" name="additional[amount][{{ index }}]" class="form-control" value="{{ customFieldRecipe.amount }}" /></td>
                                    <td>
                                        {{ text_field('additional[name]['~index~']', 'class': 'form-control', 'value': customFieldRecipe.name) }}
                                    </td>
                                    <td>
                                        <select name="additional[type][{{ index }}]" class="form-control" disabled>
                                            {% for fieldtype, fieldtypename in recipe.getCustomFieldTypes() %}
                                                <option {% if customFieldRecipe.type is fieldtype %}selected="selected"{% endif %}
                                                        value="{{ fieldtype }}">{{ fieldtypename|t }}</option>
                                            {% endfor %}
                                        </select>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="clearfix"></div>
                                        </div>

                                        {% if customFieldRecipe.type is 'statement' %}
                                            <div class="form-group">
                                                <div class="col-md-6">
                                                    <select name="params[{{ index }}][statement]"
                                                            class="form-control select2-input customtariff">
                                                        <option value="0">{{ "Custom price"|t }}</option>
                                                        {% for tariff in tariffs %}
                                                            <option {% if tariff.id is customFieldRecipe.getCustomPriceTariffId() %}selected="selected"{% endif %}
                                                                    value="{{ tariff.id }}">{{ tariff.code }}{% if tariff.description %} - {{ tariff.description }}{% endif %}</option>
                                                        {% endfor %}
                                                    </select>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        {% endif %}

                                        {% if customFieldRecipe.type is 'textarea' %}
                                            <div class="form-group">
                                                <div class="col-md-6">
                                                    <textarea name="params[{{ index }}][textarea]" draggable="false"></textarea>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        {% endif %}

                                        {% if customFieldRecipe.type is 'number' %}
                                            <div class="form-group">
                                                <div class="col-md-3">
                                                    <select name="params[{{ index }}][numberprice]"
                                                            class="form-control select2-input customtariff">
                                                        <option value="0">-</option>
                                                        {% for tariff in tariffs %}
                                                            <option {% if tariff.id is customFieldRecipe.getCustomPriceTariffId() %}selected="selected"{% endif %}
                                                                    value="{{ tariff.id }}">{{ tariff.code }}{% if tariff.description %} - {{ tariff.description }}{% endif %}</option>
                                                        {% endfor %}
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <div><label><input
                                                                    {% if customFieldRecipe.getCustomPriceType() is 1 %}checked="checked"{% endif %}
                                                                    type="radio"
                                                                    name="params[{{ index }}][numberpricechoose]"
                                                                    value="1"/> {{ "Single additional price"|t }}
                                                        </label></div>
                                                    <div><label><input
                                                                    {% if customFieldRecipe.getCustomPriceType() is 2 %}checked="checked"{% endif %}
                                                                    type="radio"
                                                                    name="params[{{ index }}][numberpricechoose]"
                                                                    value="2"/> {{ "Additional price per item"|t }}
                                                        </label></div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        {% endif %}

                                        {% for k,option in customFieldRecipe.Options %}
                                            <div class="form-group">
                                                <div class="col-md-5">
                                                    {{ text_field('additional[options][' ~ index ~ '][' ~ k ~ ']', 'class': 'form-control', 'value': option.getOption()) }}
                                                </div>

                                                <div class="col-md-5">
                                                    <select name="field_option[{{ index }}][{{ k }}][selecttariff]"
                                                            class="form-control select2-input customtariff">
                                                        <option value="0">{{ "Custom price"|t }}</option>
                                                        {% for tariff in tariffs %}
                                                            <option {% if tariff.id is option.getTariffId() %}selected="selected"{% endif %}
                                                                    value="{{ tariff.id }}">{{ tariff.code }}{% if tariff.description %} - {{ tariff.description }}{% endif %}</option>
                                                        {% endfor %}
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number"
                                                           name="field_option[{{ index }}][{{ k }}][numberselectprice]"
                                                           value="{{ option.getCustomPriceTariffId() }}" class="form-control numeric" />
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>

                                        {% endfor %}
                                        {% if customFieldRecipe.type is not 'statement' %}
                                        <br />
                                        <input type="checkbox" name="params[{{ index }}][field_lab]" class="field_lab" value="{{ customFieldRecipe.has_lab_check }}" {% if customFieldRecipe.has_lab_check is 1 %}checked="checked"{% endif %} />
                                        <label for="add_option">&nbsp;&nbsp;{{ "To be determined by lab"|t }}</label>
                                        {% endif %}
                                    </td>
                                    <td><a href="javascript:;" class="btn btn-danger btn-sm customfield-remove-row"><i
                                                    class="pe-7s-close-circle"></i></a></td>
                                </tr>
                            {% endif %}
                        {% endfor %}
                        </tbody>
                    </table>
                </div>

                <legend></legend>
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
                <td>
                    <select name="activity[tariff][]" class="form-control select2-ajax">
                        {% for tariff in tariffs %}
                            <option value="{{ tariff.id }}">{{ tariff.code }}{% if tariff.description %} - {{ tariff.description }}{% endif %}</option>
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

    {{ partial("modals/newCustomField") }}
    {{ partial("modals/manageDentistGroups", ['id': 'manage-dentistgroups', 'title': 'New discount'|t, 'content': manageUsersContent, 'additionalClass': 'save-groups' , 'productId': code]) }}
    {{ partial("modals/confirmGeneral", ['id': 'confirm-modal', 'title': "Delete"|t, 'content': "Are you sure you want to delete?"|t]) }}
    {{ partial("modals/confirm", ['id': 'delete-status',
    'title': 'Delete status?'|t,
    'content': 'Do you want delete status ?',
    'additionalClass': 'delete-data',
    'primarybutton': 'Delete'
    ]) }}

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        $(function () {
            $('#groups-discount').find('a').on('click', function (e) {
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

            {#{% if recipe.has_schema is 1 %}#}
                {#$('#add_schema').trigger('click');#}
            {#{% endif %}#}

            var add_schema = $('#add_schema').val();
            $('#add_schema').on('switchChange.bootstrapSwitch', function (event, state) {

                if (add_schema == 1) {
                    add_schema = 0;
                    $('#add_schema').val(0);
                    $('#add_schema').removeAttr("checked");
                    $('#add_schema').prop("checked", false);
                    $('#schema_notice').val();
                    $('#row_notice').hide();
                }
                else {
                    add_schema = 1;
                    $('#add_schema').val(1);
                    $('#add_schema').attr("checked", "checked");
                    $('#add_schema').prop("checked", true);
                    $('#row_notice').show();
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

            $('.field_lab').on("change", function(){
                if($(this).val() == 1){
                    $(this).val(0);
                    $(this).removeAttr("checked");
                }
                else {
                    $(this).val(1);
                    $(this).attr("checked", "checked");
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

            $('.field_lab').on("change", function(){
                if($(this).val() == 1){
                    $(this).val(0);
                    $(this).removeAttr("checked");
                }
                else {
                    $(this).val(1);
                    $(this).attr("checked", "checked");
                }
            });
        });
    </script>
{% endblock %}