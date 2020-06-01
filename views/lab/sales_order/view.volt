{% extends "layouts/main.volt" %}
{% block title %} {{ "Order"|t }} {% endblock %}
{% block content %}

    <p style="margin-left: 15px;" class="pull-right"><a href="{{ url("lab/sales_order/print/") ~ order.getCode() }}" class="btn-info btn " target="_blank"><i class="pe-7s-print"></i> {{ "Print"|t }}</a></p>
    <p class="pull-right"><a href="{{ url("lab/sales_order/getpdf/") ~ order.getCode() }}" class="btn-info btn " target="_blank"><i class="pe-7s-download"></i> {{ "Get PDF"|t }}</a></p>

    <h3><a href="{{ url("lab/sales_order/") }}"><i class="pe-7s-back"></i></a> {{ "Order"|t }}: {{ order.code }}</h3>
    <br />
    <form id="orderForm" action="{{ url('lab/sales_order/view/' ~ order.code )}}" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-4">
            <legend>{{ 'Delivery adress'|t }}</legend>

            <div class="form-group">
                <label>{{ 'Dentist organisation name'|t }}</label>
                <div class="col-md-12">
                    {% if order.lab_created is not null and currentUser.Organisation.organisation_type_id is 4 %}
                        <select class="select2-input" name="order_data[dentist]">
                            {% for dentist in lab_dentists %}
                                <option value="{{ dentist.dentist_id }}" {% if order.dentist_id == dentist.dentist_id %}selected="selected"{% endif %}>{{ dentist.Dentist.name }}</option>
                            {% endfor %}
                        </select>
                    {% else %}
                        {{ organisation.getName() }}
                    {% endif %}
                </div>
                <div class="col-md-12">&nbsp;</div>
            </div>
            {% if count(locations) > 1 and order.DentistLocation %}
            <div class="form-group">
                <label>{{ 'Location'|t }}</label>
                <div class="col-md-12">
                    {{ order.DentistLocation.getName() }}
                </div>
                <div class="col-md-12">&nbsp;</div>
            </div>
            {% endif %}
            <div class="form-group">
                <label>{{ 'Client number'|t }}</label>
                <div class="col-md-12">
                    {% if currentLabDentist %}
                    {{ currentLabDentist.getClientNumber() }}
                    {% endif %}
                </div>
                <div class="col-md-12">&nbsp;</div>
            </div>
            <div class="form-group">
                <label>{{ 'Address'|t }}</label>
                <div class="col-md-12">
                    {{ organisation.getAddress() }}
                </div>
                <div class="col-md-12">&nbsp;</div>
            </div>
            <div class="form-group">
                <label>{{ 'Zip code'|t }}</label>
                <div class="col-md-12">
                    {{ organisation.getZipcode() }}
                </div>
                <div class="col-md-12">&nbsp;</div>
            </div>
            <div class="form-group">
                <label>{{ 'City'|t }}</label>
                <div class="col-md-12">
                    {{ organisation.getCity() }}
                </div>
                <div class="col-md-12">&nbsp;</div>
            </div>
        </div>
        <div class="col-md-4">

            <legend>{{'Patient data'|t}}</legend>

            <div class="form-group">
                <label>{{ 'Patient'|t }}</label>
                <div class="col-md-12">
                    {% if order.lab_created is not null and currentUser.Organisation.organisation_type_id is 4 %}
                    <input type="text" name="order_data[patient_initials]" class="form-control" value="{{ order.DentistOrderData.patient_initials }}" required="required" />
                    <input type="text" name="order_data[patient_insertion]" class="form-control" value="{{ order.DentistOrderData.patient_insertion }}" />
                    <input type="text" name="order_data[patient_lastname]" class="form-control" value="{{ order.DentistOrderData.patient_lastname }}" required="required" />
                    {% else %}
                        {{ order.DentistOrderData.patient_initials }} {{ order.DentistOrderData.patient_insertion }} {{ order.DentistOrderData.patient_lastname }}
                    {% endif %}
                </div>
                <div class="col-md-12">&nbsp;</div>
            </div>
            <div class="form-group">
                <label>{{ 'Gender'|t }}</label>
                <div class="col-md-12">
                    {% if order.lab_created is not null and currentUser.Organisation.organisation_type_id is 4 %}
                    <br/><input {% if order.DentistOrderData.patient_gender is 'm' %}checked="checked"{% endif %}
                                type="radio" name="order_data[patient_gender]" value="m"/> {{ "Male"|t }}
                    <br/><input {% if order.DentistOrderData.patient_gender is 'f' %}checked="checked"{% endif %}
                                type="radio" name="order_data[patient_gender]" value="f"/> {{ "Female"|t }}
                    {% else %}
                        {% if order.DentistOrderData.patient_gender is 'm' %}{{ "Male"|t }}{% endif %}
                        {% if order.DentistOrderData.patient_gender is 'f' %}{{ "Female"|t }}{% endif %}
                    {% endif %}
                </div>
                <div class="col-md-12">&nbsp;</div>
            </div>
            <div class="form-group">
                {% if order.lab_created is not null and currentUser.Organisation.organisation_type_id is 4 %}
                    <div class="form-group col-md-4">
                        <label>{{ 'Date of birth'|t }}</label>
                        <select name="order_data[patient_birth][day]" class="form-control">
                            <option value="-" selected="selected">{{ "Day"|t }}</option>
                            {% for i in 1..31 %}
                                <option value="{{ i }}" {% if birthDate['day'] == i %}selected="selected"{% endif %}>{{ i }}</option>
                            {% endfor %}
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>&nbsp;</label>
                        <select name="order_data[patient_birth][month]" class="form-control">
                            <option value="-" selected="selected">{{ "Month"|t }}</option>
                            {% for i in 1..12 %}
                                <option value="{{ i }}" {% if birthDate['month'] == i %}selected="selected"{% endif %}>{{ i }}</option>
                            {% endfor %}
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>&nbsp;</label>
                        <select name="order_data[patient_birth][year]" class="form-control">
                            <option value="-" selected="selected">{{ "Year"|t }}</option>
                            {% for i in 1900..2018 %}
                                <option value="{{ i }}" {% if birthDate['year'] == i %}selected="selected"{% endif %}>{{ i }}</option>
                            {% endfor %}
                        </select>
                    </div>
                {% else %}
                    <label>{{ 'Date of birth'|t }}</label>
                    <div class="col-md-12">{{ order.DentistOrderData.getPatientBirthFormat() }}</div>
                {% endif %}

                <div class="col-md-12">&nbsp;</div>
            </div>
            <div class="form-group">
                <label>{{ 'BSN'|t }}</label>
                <div class="col-md-12">
                    {% if order.lab_created is not null and currentUser.Organisation.organisation_type_id is 4 %}
                        <input type="text" name="order_data[bsn]" class="form-control" value="{{ order.DentistOrderBsn.getBsn() }}" />
                    {% else %}
                        {{ order.DentistOrderBsn.getBsnSecured() }}
                    {% endif %}
                </div>
                <div class="col-md-12">&nbsp;</div>
            </div>
            <div class="form-group">
                <label>{{ 'Patient number'|t }}</label>
                <div class="col-md-12">
                    {% if order.lab_created is not null and currentUser.Organisation.organisation_type_id is 4 %}
                        <input type="text" name="order_data[patient_number]" class="form-control" value="{{ order.DentistOrderData.patient_number }}" />
                    {% else %}
                        {{ order.DentistOrderData.patient_number }}
                    {% endif %}
                </div>
                <div class="col-md-12">&nbsp;</div>
            </div>
        </div>
        <div class="col-md-4">

            <legend>{{'Order info'|t}}</legend>
            {% if order.lab_created is not null and currentUser.Organisation.organisation_type_id is 4 %}
            <div class="form-group">
                <label>{{ 'Add attachment(s)'|t }}</label>
                <div class="col-md-12">
                    {{ file_field('files[]', 'class': 'form-control', 'multiple': 'multiple') }}
                </div>
                <div class="col-md-12">&nbsp;</div>
            </div>
            {% endif %}
            <div class="form-group">
                <label>{{ 'Attachment(s)'|t }}</label>
                <div class="col-md-12">
                    <ul>
                    {% for attachment in attachments %}
                        <li><a href="{{ url('/lab/sales_order/download/'~attachment.id) }}">{{ attachment.file_name }}</a></li>
                    {% endfor %}
                    </ul>
                </div>
            </div>
            <div class="form-group">
                <label>{{ 'Client preferences'|t }}</label>
                <div class="col-md-12">
                    {% if currentLabDentist and currentLabDentist.client_preferences is not null %}
                    <a id="client_preferences" style="cursor: pointer;">{{ "View client preferences"|t }}</a>
                    {% endif %}
                    <br /><br />
                </div>
            </div>
            {% if order.DentistUser %}
            <div class="form-group">
                <label>{{ 'Dentist'|t }}</label>
                <div class="col-md-12">{{ order.DentistUser.firstname }} {{ order.DentistUser.lastname }}</div>
            </div>
            {% endif %}
        </div>
    </div>

    {% for recipeOrder in orderRecipes %}
        {% if recipeOrder.parent_id is not null and recipeOrder.deleted_at is not null and order.status >= 3 %}{% continue %}{% endif %}
        {% if recipeOrder.parent_id is null and recipeOrder.deleted_at is not null %}{% continue %}{% endif %}
        {% if recipeOrder.parent_id is null and order.status == 3 %}
            {{ partial("modals/addSingleFieldSelect", ['id': 'changeRecipeModal_'~ recipeOrder.Recipes.id, 'title': 'Change recipe'|t, 'content': 'If the dentist did not make a final choice for a recipe or if the dentist chose the wrong recipe you can change it to the correct recipe here', 'recipeId': recipeOrder.Recipes.id ]) }}
        {% endif %}
        <div class="row">
            <div class="col-md-12">
                <legend> </legend>
                <table id="recipes" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>{{ "Recipe number"|t }}</th>
                        <th>{{ "Recipe name"|t }}</th>
                        <th>{{ "Status"|t }}</th>
                        <th>{{ "Date and time"|t }}</th>
                        <th>{{ "Status change"|t }}</th>
                        <th>{{ "Changed by"|t }}</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ recipeOrder.Recipes.ParentRecipe.recipe_number }}</td>
                            <td>{{ recipeOrder.Recipes.ParentRecipe.name }} ({{ recipeOrder.Recipes.custom_name }}) {% if recipeOrder.parent_id is null and recipeOrder.deleted_at is not null and order.status >= 3 %}<br /><br /><a class="btn-warning btn">{{ "This recipe was replaced and is no longer active"|t }}</a>{% endif %}</td>
                            <td>
                                <select name="status-{{ recipeOrder.id }}" class="form-control status-select"
                                        data-recipeid="{{ recipeOrder.id }}" data-orgstatus="{{ recipeOrder.status }}">
                                    {% for status in statuses_av %}
                                        <option value="{{ status['id'] }}" {% if status['id'] is recipeOrder.status %}selected="selected"{% endif %}>{{ status['name'] }}</option>
                                    {% endfor %}
                                </select>
                            </td>
                            {% if recipeOrder.status_changed_by is not null %}
                                <td><div class="hidden">{{ recipeOrder.status_changed_at }}</div>{{ recipeOrder.status_changed_at|dttonl }}</td>
                                <td>{% if recipeOrder.StatusPrev %}{{ recipeOrder.StatusPrev.name }} - {% endif %}{{ recipeOrder.StatusCurrent.name }}</td>
                                <td>{{ recipeOrder.StatusUser.getFullName() }}</td>
                            {% else %}
                                <td></td>
                                <td></td>
                                <td></td>
                            {% endif %}
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        {% if recipeOrder.parent_id is null and order.status == 3 %}
        <div class="row">
            <div class="col-md-12">
                <p class="pull-right"><a class="btn-primary btn changeRecipe" data-id="{{ recipeOrder.Recipes.id }}"><i class="pe-7s-repeat"></i> {{ "Change recipe"|t }}</a></p>
            </div>
        </div>
        {% endif %}
        <div class="row">
            <div class="col-md-12">
                <legend> </legend>

                <table id="delivery" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>{{ "Phase"|t }}</th>
                        <th>{{ 'Requested delivery date'|t }}</th>
                        <th>{{ 'Prefered part of the day'|t }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {% for del in recipeOrder.Delivery %}
                        <tr>
                            <td>{{ del.delivery_text }}</td>
                            <td>{{ del.delivery_date }}</td>
                            <td>{{ del.part_of_day }}</td>
                        </tr>
                    {% endfor %}
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row recipeOrderRow" order_recipe="{{ recipeOrder.id }}">

            <div class="col-md-12">
                <legend> </legend>
                <table id="recipes_fields" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>{{ "Amount"|t }}</th>
                            <th>{{ "Field name"|t }}</th>
                            <th>{{ "Options"|t }}</th>
                            <th>{{ "Input dentist"|t }}</th>
                            <th>{{ "Tariff code"|t }}</th>
                            <th>{{ "Price"|t }}</th>
                            <th>{{ "Lot number"|t }}</th>
                            <th>{{ "Batch number"|t }}</th>
                            <th>{{ "Alloy"|t }}</th>
                            <th>{{ "Design number"|t }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    {#basic elements#}
                    {% for recipeAct in recipeOrder.Recipes.ParentRecipe.RecipeActivity %}
                        <tr>
                            {% if recipeAct.amount != 1 %}
                                <td><input type="text" value="{{ recipeAct.amount }}" disabled="disabled" style="width: 30px;" /></td>
                            {% else %}
                                <td></td>
                            {% endif %}
                            <td>{{ recipeAct.description }}</td>
                            <td>-</td>
                            <td>-</td>
                            <td>{{ recipeAct.Tariff.code }}</td>
                            <td class="subtotal">{% if mappedSignaTariffs[recipeAct.getTariffId()] is not null %}{{ number_format(mappedSignaTariffs[recipeAct.getTariffId()].getPrice() * recipeAct.amount, 2) }}{% endif %}</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                    {% endfor %}

                    {#var/opt/add elements#}

                    {% for recipeData in recipeOrder.DentistOrderRecipeData %}
                        {% if in_array(recipeData.field_type, ['select', 'checkbox']) == false %}
                        <tr cft="{{ recipeData.RecipeCustomField.custom_field_type }}" {% if recipeOrder.deleted_at is not null %}class="disable_input"{% endif %}>
                            {# AMOUNT #}
                            <td><input rowdetail="{{ recipeData.id }}" id="amount_{{ recipeData.id }}" type="number" min="1" name="recipe_data[{{ recipeData.order_recipe_id }}][{{ recipeData.id }}][amount]" class="form-control amountvalue" style="width: 70px;" value="{{ recipeData.amount }}" /></td>

                            {# FIELD NAME #}
                            <td>{{ recipeData.field_name }}</td>

                            {# OPTIONS #}
                            {% if recipeData.field_type is 'text' %}
                                <td>
                                    {% if strlen(recipeData.field_value) > 20 %}
                                        <a id="textarealink_{{ recipeData.id }}" data-id="{{ recipeData.id }}" class="textareaInput" style="cursor: pointer;" data-dentist="{{ recipeData.field_dentist_value }}">{{ substr(recipeData.field_value, 0, 29) }}...</a>
                                        <input id="textarea_{{ recipeData.id }}" type="hidden" name="recipe_data[{{ recipeData.order_recipe_id }}][{{ recipeData.id }}][options]" value="{{ recipeData.field_value }}" />
                                    {% else %}
                                        <textarea id="textarea_{{ recipeData.id }}" name="recipe_data[{{ recipeData.order_recipe_id }}][{{ recipeData.id }}][options]">{{ recipeData.field_value }}...</textarea>
                                    {% endif %}
                                    <input type="hidden" name="recipe_data[{{ recipeData.order_recipe_id }}][{{ recipeData.id }}][type]" value="text" />
                                </td>
                            {% elseif recipeData.field_type is 'number' %}
                                <td>

                                    <input type="hidden" name="{{ recipeData.order_recipe_id }}[{{ recipeData.RecipeCustomField.id }}][type]" value="number" />
                                    {% if recipeData.getCustomPriceType() is 1 and recipeData.field_value and recipeData.field_value > 0 %}
                                        <input cft="{{ recipeData.getCustomPriceType() }}" id="number_{{ recipeData.id }}" class="number" type="number" name="recipe_data[{{ recipeData.order_recipe_id }}][{{ recipeData.id }}][options]" data-id="{{ recipeData.id }}" data-price="{% if recipeData.Tariff %}{{ recipeData.Tariff.getPrice() }}{% endif %}" value="{{ recipeData.field_value }}" data-last="{% if recipeData.Tariff %}{{ recipeData.Tariff.getPrice() }}{% endif %}" data-price-type="{{ recipeData.custom_price_type }}" min="0" />
                                    {% elseif recipeData.getCustomPriceType() is 2 and recipeData.field_value and recipeData.field_value > 0 %}
                                        <input cft="{{ recipeData.getCustomPriceType() }}" id="number_{{ recipeData.id }}" class="number" type="number" name="recipe_data[{{ recipeData.order_recipe_id }}][{{ recipeData.id }}][options]" data-id="{{ recipeData.id }}" data-price="{% if recipeData.Tariff %}{{ recipeData.Tariff.getPrice() }}{% endif %}" value="{{ recipeData.field_value }}" data-last="{% if recipeData.Tariff %}{{ number_format(recipeData.Tariff.getPrice() * recipeData.field_value, 2) }}{% endif %}" data-price-type="{{ recipeData.custom_price_type }}" min="0" />
                                    {% else %}
                                        <input cft="{{ recipeData.getCustomPriceType() }}" id="number_{{ recipeData.id }}" class="number" type="number" name="recipe_data[{{ recipeData.order_recipe_id }}][{{ recipeData.id }}][options]" data-id="{{ recipeData.id }}" data-price="{{ recipeData.custom_price }}" value="{{ recipeData.field_value }}" data-last="{{ recipeData.field_value }}" data-price-type="{{ recipeData.custom_price_type }}" min="0" />
                                    {% endif %}

                                    <input type="hidden" name="recipe_data[{{ recipeData.order_recipe_id }}][{{ recipeData.id }}][type]" value="number" />
                                </td>
                            {% elseif recipeData.field_type is 'statement' %}
                                <td>
                                    <input type="hidden" name="recipe_data[{{ recipeData.order_recipe_id }}][{{ recipeData.id }}][type]" value="statement" />
                                </td>
                            {% elseif recipeData.field_type is 'textarea' %}
                                <td>
                                    {% if strlen(recipeData.field_value) > 20 %}
                                        <a id="textarealink_{{ recipeData.id }}" data-id="{{ recipeData.id }}" class="textareaInput" style="cursor: pointer;" data-dentist="{{ htmlspecialchars(recipeData.field_dentist_value) }}">{{ strip_tags(substr(recipeData.field_value, 0, 29)) }}...</a>
                                        <input id="textarea_{{ recipeData.id }}" type="hidden" name="recipe_data[{{ recipeData.order_recipe_id }}][{{ recipeData.id }}][options]" value="{{ htmlspecialchars(recipeData.field_value) }}" />
                                    {% else %}
                                        <textarea id="textarea_{{ recipeData.id }}" name="recipe_data[{{ recipeData.order_recipe_id }}][{{ recipeData.id }}][options]">{{ recipeData.field_value }}...</textarea>
                                    {% endif %}
                                    <input type="hidden" name="recipe_data[{{ recipeData.order_recipe_id }}][{{ recipeData.id }}][type]" value="textarea" />
                                </td>
                            {% endif %}

                            {# INPUT DENTIST #}
                                <td>
                                    {% if strlen(recipeData.field_dentist_value) > 20 %}
                                        <a class="dentistInput" data-id="{{ recipeData.id }}" style="cursor: pointer;" data-dentist="{{ htmlspecialchars(recipeData.field_dentist_value) }}">{{ strip_tags(substr(recipeData.field_dentist_value, 0, 29)) }}...</a>
                                    {% else %}
                                    {{ recipeData.field_dentist_value }}
                                    {% endif %}

                                    {% if recipeData.has_lab_check == 1 %}
                                        <br /><span class="additional_info">[ {{ "Let lab decide"|t }} ]</span>
                                    {% endif %}
                                </td>
                        </tr>
                        {% else %}

                            {% for index,options in recipeData.Options %}
                            <tr cft="{{ recipeData.RecipeCustomField.custom_field_type }}" {% if recipeOrder.deleted_at is not null %}class="disable_input"{% endif %}>
                                {# AMOUNT #}
                                {% if recipeData.field_type is 'checkbox' %}
                                    {% if index is 0 %}
                                        <td><input masterrowdetail="{{ recipeData.id }}" rowdetail="{{ options.id }}" id="amountbox_{{ options.id }}" type="number" min="1" name="recipe_data[{{ recipeData.order_recipe_id }}][{{ recipeData.id }}][amount]" class="form-control amountvalue" style="width: 70px;" value="{{ recipeData.amount }}" /></td>
                                    {% else %}
                                        <td><input masterrowdetail="{{ recipeData.id }}" rowdetail="{{ options.id }}" id="amountbox_{{ options.id }}" type="hidden" value="{{ recipeData.amount }}" /></td>
                                    {% endif %}
                                {% else %}
                                    <td><input rowdetail="{{ recipeData.id }}" id="amount_{{ recipeData.id }}" type="number" min="1" name="recipe_data[{{ recipeData.order_recipe_id }}][{{ recipeData.id }}][amount]" class="form-control amountvalue" style="width: 70px;" value="{{ recipeData.amount }}" /></td>
                                {% endif %}

                                {# FIELD NAME #}
                                <td>{% if index is 0 %}{{ recipeData.field_name }}{% endif %}</td>

                                {% set selectedOption = options %}

                                {# OPTIONS #}
                                <td>
                                {% if recipeData.field_type is 'checkbox' %}
                                    {% if options.Tariff %}
                                        {% if in_array(options.value, json_decode(recipeData.field_value)) %}
                                            {% set selectedOption = options %}
                                            <input class="sag" type="checkbox" name="recipe_data[{{ recipeData.order_recipe_id }}][{{ recipeData.id }}][options][{{ options.id }}]" data-id="{{ options.id }}" data-price="{% if options.Tariff %}{{ options.Tariff.getPrice() }}{% endif %}" data-code="{% if options.Tariff %}{{ options.Tariff.code }}{% endif %}" value="{{ options.value }}" checked="checked" data-amount="{{ recipeData.amount }}" />
                                        {% else %}
                                            <input class="sag" type="checkbox" name="recipe_data[{{ recipeData.order_recipe_id }}][{{ recipeData.id }}][options][{{ options.id }}]" data-id="{{ options.id }}" data-price="{% if options.Tariff %}{{ options.Tariff.getPrice() }}{% endif %}" data-code="{% if options.Tariff %}{{ options.Tariff.code }}{% endif %}" value="{{ options.value }}" data-amount="{{ recipeData.amount }}" />
                                        {% endif %}
                                    {% else %}
                                        {% if in_array(options.value, json_decode(recipeData.field_value)) %}
                                            {% set selectedOption = options %}
                                            <input class="sag" type="checkbox" name="recipe_data[{{ recipeData.order_recipe_id }}][{{ recipeData.id }}][options][{{ options.id }}]" data-id="{{ options.id }}" data-price="0" data-code="{% if options.Tariff %}{{ options.Tariff.code }}{% endif %}" value="{{ options.value }}" checked="checked" data-amount="{{ recipeData.amount }}" />
                                        {% else %}
                                            <input class="sag" type="checkbox" name="recipe_data[{{ recipeData.order_recipe_id }}][{{ recipeData.id }}][options][{{ options.id }}]" data-id="{{ options.id }}" data-price="0" data-code="{% if options.Tariff %}{{ options.Tariff.code }}{% endif %}" value="{{ options.value }}" data-amount="{{ recipeData.amount }}" />
                                        {% endif %}
                                    {% endif %}
                                    <input type="hidden" name="recipe_data[{{ recipeData.order_recipe_id }}][{{ recipeData.id }}][type]" value="checkbox" />
                                    <label>{{ options.option }}</label><br />
                                {% else %}
                                    <select data-select="{{ recipeData.id }}" name="recipe_data[{{ recipeData.order_recipe_id }}][{{ recipeData.id }}][options]" class="select2-input select-option">
                                        {% for options in recipeData.Options %}
                                            {% if options.Tariff %}
                                                {% if options.value == recipeData.field_value %}
                                                    {% set selectedOption = options %}
                                                    <option data-id="{% if options.Tariff %}{{ options.Tariff.id }}{% endif %}" data-code="{% if options.Tariff %}{{ options.Tariff.code }}{% endif %}" data-price="{% if options.Tariff %}{{ options.Tariff.getPrice() }}{% endif %}" value="{{ options.value }}" selected="selected">{{ options.option }}</option>
                                                {% else %}
                                                    <option data-id="{% if options.Tariff %}{{ options.Tariff.id }}{% endif %}" data-code="{% if options.Tariff %}{{ options.Tariff.code }}{% endif %}" data-price="{% if options.Tariff %}{{ options.Tariff.getPrice() }}{% endif %}" value="{{ options.value }}">{{ options.option }}</option>
                                                {% endif %}
                                            {% else %}
                                                {% if options.value == recipeData.field_value %}
                                                    {% set selectedOption = options %}
                                                    <option data-id="{% if options.Tariff %}{{ options.Tariff.id }}{% endif %}" data-code="{% if options.Tariff %}{{ options.Tariff.code }}{% endif %}" data-price="0" value="{{ options.value }}" selected="selected">{{ options.option }}</option>
                                                {% else %}
                                                    <option data-id="{% if options.Tariff %}{{ options.Tariff.id }}{% endif %}" data-code="{% if options.Tariff %}{{ options.Tariff.code }}{% endif %}" data-price="0" value="{{ options.value }}">{{ options.option }}</option>
                                                {% endif %}
                                            {% endif %}
                                        {% endfor %}
                                    </select>
                                    <input type="hidden" name="recipe_data[{{ recipeData.order_recipe_id }}][{{ recipeData.id }}][type]" value="select" />
                                {% endif %}
                                </td>

                                {# INPUT DENTIST #}
                                <td>
                                    {% if recipeData.field_type is 'checkbox' %}
                                        {% if in_array(options.value, json_decode(recipeData.field_dentist_value)) %}
                                            <input type="checkbox" checked="checked" disabled="disabled" />
                                        {% else %}
                                            <input type="checkbox" disabled="disabled" />
                                        {% endif %}
                                    {% elseif recipeData.field_type is 'select' %}
                                        {{ selectedOption.option }}
                                    {% else %}
                                        {{ recipeData.field_dentist_value }}
                                    {% endif %}

                                    {% if recipeData.has_lab_check == 1 %}
                                        <br /><span class="additional_info">[ {{ "Let lab decide"|t }} ]</span>
                                    {% endif %}
                                </td>

                                </tr>
                                {% endfor %}
                            {% endif %}
                        {% endfor %}
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ "Total"|t }}</td>
                            <td id="total_price">{{ number_format(recipeOrder.getPrice(), 2) }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                {% if recipeOrder.Recipes.getPriceType() is 'Fixed' %}
                    <div class="hidden subtotal optional">{{ recipeOrder.Recipes.getPrice() }}</div>
                    <div class="hidden subtotal fixprice"></div>
                {% endif %}
            </div>
        </div>
        <input type="hidden" id="pricetmp_{{ recipeOrder.id }}" name="pricetmp[{{ recipeOrder.id }}]" value="{{ number_format(recipeOrder.getPrice(), 2) }}" />
        {% if recipeOrder.deleted_at is not null %}
            <input type="hidden" name="disabled[{{ recipeOrder.id }}]" value="1" />
        {% endif %}
        <hr />
        {% if recipeOrder.schema_values is not null %}
            <div id="view_schema_{{ recipeOrder.id }}" class="row">
                <div class="col-lg-12">
                    <table id="teeth">
                        <tr>
                            {% for toothbox in schema[recipeOrder.id]['upper_left'] %}{{ toothbox }}{% endfor %}
                            {% for toothbox in schema[recipeOrder.id]['upper_right'] %}{{ toothbox }}{% endfor %}
                        </tr>
                        <tr class="divider">
                            {% for toothbox in schema[recipeOrder.id]['lower_left'] %}{{ toothbox }}{% endfor %}
                            {% for toothbox in schema[recipeOrder.id]['lower_right'] %}{{ toothbox }}{% endfor %}
                        </tr>
                    </table>
                </div>
                <div class="col-lg-12">&nbsp;</div>
                <div class="col-lg-3">
                    <p>{{ "Selection chosen by dentist: "|t }}</p>
                    <p id="default">{% for index, sch in schema[recipeOrder.id]['raw_values'] %}<span>{{ sch }}</span>{% endfor %}</p>
                </div>
                <div class="col-lg-3">
                    <p>{{ "Final selection: "|t }}</p>
                    <p id="final_{{ recipeOrder.id }}" class="final"></p>
                    <p><a data-recipe="{{ recipeOrder.id }}" class="btn btn-primary btn-sm final_schema_edit"><i class="pe-7s-diskette"></i> {{ "Save"|t }}</a></p>
                </div>
            </div>
        {% endif %}
    {% endfor %}
    <div class="row">&nbsp;</div>
    <div class="row">&nbsp;</div>
    {# WIP 546 <div class="row">#}
        {#<div class="col-md-12">#}

            {#<legend>{{'Client preferences for this recipe'|t}}</legend>#}

            {#<table id="recipe_preference" class="table table-striped table-bordered" cellspacing="0" width="100%">#}
                {#<tr>#}
                    {#<th>{{ "Recipe preference"|t }}</th>#}
                {#</tr>#}
                {#{% for rp in json_decode(curr_lab_dentist.getClientPreferencesRecipe()) %}#}
                {#<tr>#}
                    {#<td>{{ rp['pref'] }}</td>#}
                {#</tr>#}
                {#{% endfor %}#}
            {#</table>#}
        {#</div>#}
    {#</div>#}
    {#<div class="row">&nbsp;</div>#}
    {#<div class="row">#}
        {#<div class="col-md-12">#}

            {#<legend>{{'Client preferences per tariff'|t}}</legend>#}

            {#<table id="tariff_preference" class="table table-striped table-bordered" cellspacing="0" width="100%">#}
                {#<tr>#}
                    {#<th>{{ "Tariff code"|t }}</th>#}
                    {#<th>{{ "Preference"|t }}</th>#}
                {#</tr>#}
                {#{% for rt in json_decode(curr_lab_dentist.getClientPreferencesTariff()) %}#}
                    {#<tr>#}
                        {#<td>{{ rt['code'] }}</td>#}
                        {#<td>{{ rt['pref'] }}</td>#}
                    {#</tr>#}
                {#{% endfor %}#}
            {#</table>#}
        {#</div>#}
    {#</div>#}
    <div class="row">
        <div class="col-md-6">

            <legend>{{'Order messages'|t}}</legend>

            <table id="mnessages" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <tbody>
                {% for message in messages %}
                    <tr style="background: transparent;">
                        <th colspan="4" style="border:none;">{{ message.getCreatedAt() }}</th>
                    </tr>
                    <tr>
                        <td width="15%">{{ message.Organisation.getName() }}</td>
                        <td width="15%">{{ message.CreatedBy.getFullname() }}</td>
                        <td>{{ message.getNote() }}</td>
                        <td width="15%">
                            {% if message.DentistOrderNoteFile is not null %}
                                <a href="{{ url('/dentist/order/download/'~message.DentistOrderNoteFile.id) }}" class="btn btn-primary"><i class="pe-7s-download"></i>{{ "Download attachment"|t }}</a>
                            {% endif %}
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>

        </div>
        <div class="col-md-6">


                <legend>{{'New message about order'|t}}</legend>

                <div class="form-group">
                    {{ text_area('new_message', 'placeholder': 'Order notes or remarks...'|t, 'class': 'form-control new-message') }}
                </div>

                <div class="form-group">
                    {{ file_field('files[]', 'class': 'form-control', 'multiple': 'multiple') }}
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary pull-right"><i class="pe-7s-diskette"></i> {{ "Save"|t }}</button>
                    <div class="clearfix"></div>
                </div>

                {% if order.status is 2 %}
                    <div class="form-group">
                        <a href="{{ url("lab/sales_order/toinprogress/" ~ order.code ) }}" class="btn-success btn pull-right"><i class="pe-7s-next-2"></i> {{ "Move to In progress"|t }}</a>
                        <div class="clearfix"></div>
                    </div>
                {% endif %}

                {% if order.status is 3 %}
                    <div class="form-group">
                        <a data-url="{{ url("lab/sales_order/todelivery/" ~ order.code ) }}" class="btn-success btn pull-right generateDelivery"><i class="pe-7s-next-2"></i> {{ "Move to delivery and generate delivery note"|t }}</a>
                        <a href="{{ url("delivery_note/preview/" ~ order.code ) }}" class="btn btn-default pull-right"><i class="pe-7s-id"></i> {{ "View (concept) delivery note"|t }}</a>
                        <div class="clearfix"></div>
                    </div>
                {% endif %}


        </div>
    </div>
    </form>
    <div class="row">
        <div class="col-lg-12">
            <br />
        </div>
    </div>

    {{ partial("modals/confirm", ['id': 'change-status-modal', 'title': 'Change status?'|t, 'content': 'Are you sure you want to change the status?', 'additionalClass': 'change-status', 'primarybutton': 'Yes, I am sure']) }}
    {{ partial("modals/confirm", ['id': 'confirmChangeRecipeModal', 'title': 'Are you sure?'|t, 'content': 'Are you sure you want to change the recipe?', 'additionalClass': 'confirmChange', 'primarybutton': 'Yes, I am sure']) }}
    {% if currentLabDentist %}
    {{ partial("modals/alert", ['id': 'client-preferences-modal', 'title': 'Client preferences'|t, 'content':  currentLabDentist.getClientPreferences() ]) }}
    {% endif %}
    {{ partial("modals/alertDentistInput", ['id': 'textarea-dentist-modal', 'title': 'Dentist input'|t ]) }}
    {{ partial("modals/editTextarea", ['id': 'textarea-edit-modal', 'title': 'Edit textarea'|t ]) }}

{% endblock %}


{% block scripts %}
    {{ super() }}

    <script>
        $(function(){

            tinymce.init({
                selector: '.tinymce',
                language_url: '/js/tinymce/langs/nl.js',
                plugins: "link",
                height: 300,
                branding: false,
                selection_toolbar: 'link bold italic | quicklink h2 h3 blockquote',
                menu: {}
            });

            tinymce.init({
                selector: '.tinymce-readonly',
                language_url: '/js/tinymce/langs/nl.js',
                menubar: false,
                statusbar: false,
                toolbar: false,
                branding: false,
                height: 300,
                readonly: 1
            });

            var old_recipe = 0;

            {% for recipe in schema %}
                {% for teeth in recipe['raw_values'] %}
                $('#T{{ teeth }}_{{ recipe['id'] }}').val(1);
                $('#T{{ teeth }}_{{ recipe['id'] }}').attr('checked', 'checked');

                if($('#T{{ teeth }}_{{ recipe['id'] }}').hasClass('upper')){
                    var tooth_{{ teeth }}_{{ recipe['id'] }} = $('#T{{ teeth }}_{{ recipe['id'] }}').siblings()[1];
                    var span_{{ teeth }}_{{ recipe['id'] }} = $($('#T{{ teeth }}_{{ recipe['id'] }}').siblings()[0]).attr('data-tooth');
                }

                if($('#T{{ teeth }}_{{ recipe['id'] }}').hasClass('lower')){
                    var tooth_{{ teeth }}_{{ recipe['id'] }} = $('#T{{ teeth }}_{{ recipe['id'] }}').siblings()[0];
                    var span_{{ teeth }}_{{ recipe['id'] }} = $($('#T{{ teeth }}_{{ recipe['id'] }}').siblings()[1]).attr('data-tooth');
                }
                $(tooth_{{ teeth }}_{{ recipe['id'] }}).addClass("checked");
                $("#final_{{ recipe['id'] }}").append("<span data-tooth='"+span_{{ teeth }}_{{ recipe['id'] }}+"'>"+span_{{ teeth }}_{{ recipe['id'] }}+"</span>");
                {% endfor %}
            {% endfor %}

            $('.dentistInput').on('click', function(){
                $('#alert-content').val(tinyMCE.get('alert-content').setContent($(this).attr('data-dentist')));
                $('#textarea-dentist-modal').modal("show");
            });

            $('.textareaInput').on('click', function(){
                $('#textareaEdit').val(tinyMCE.get('textareaEdit').setContent($('#textarea_'+$(this).attr('data-id')).val()));
                $('#confirmEditTextarea').attr('data-id', $(this).attr('data-id'));
                $('#textarea-edit-modal').modal("show");
            });

            $('#confirmEditTextarea').on('click', function(){
                $('#textarea_'+$(this).attr('data-id')).val(tinyMCE.get('textareaEdit').getContent());
                $('#textarealink_'+$(this).attr('data-id')).attr('data-dentist', tinyMCE.get('textareaEdit').getContent());
                $('#textarealink_'+$(this).attr('data-id')).empty();
                $('#textarealink_'+$(this).attr('data-id')).html((tinyMCE.get('textareaEdit').getContent({format: 'text'})).substring(0,20) + '...').text();
                $('#textarea-edit-modal').modal("hide");
            });

            $('.disable_input :input').attr('disabled', 'disabled');
            $('.disable_select').attr('disabled', 'disabled');

            $('.generateDelivery').on('click', function(){
                var zar = true;
                $('.required').each(function(){
                   if(!$(this).val()){
                       $(this).focus();
                       zar = false;
                       return false;
                   }
                });

                if(zar == true){
                    window.location.href = $('.generateDelivery').attr('data-url');
                }
                else {
                    setTimeout(function () {
                        toastr.warning("Some fields are missing");
                    }, 500);
                }
            });

            $('.changeRecipe').on('click', function(){
               $('#changeRecipeModal_'+$(this).attr('data-id')).modal('show');
               $('#newRecipe').attr('data-id', $(this).attr('data-id'));
               old_recipe = $(this).attr('data-id');
            });

            $('.confirm-button').on('click', function(){
                $('#changeRecipeModal_'+$(this).attr('data-id')).modal('hide');
                $('#confirmChangeRecipeModal').modal('show');
                old_recipe = $(this).attr('data-id');
            });

            $('.confirmChange').on('click', function(){
                $('#confirmChangeRecipeModal').modal('hide');

                $.ajax({
                    method: "post",
                    url: "/lab/sales_order/edit",
                    data: {
                        oldRecipe: old_recipe,
                        newRecipe: $('#newRecipe_'+old_recipe+' option:selected').val(),
                        orderId: {{ order.id }}
                    },
                    dataType: 'json'
                }).success(function (data) {
                    if(data.status != "error"){
                        setTimeout(function () {
                            toastr.success(data.msg);
                            setTimeout(function () {
                                window.location.reload();
                            }, 1000);
                        }, 1000);
                    }
                    else {
                        setTimeout(function () {
                            toastr.error(data.msg);
                        }, 1000);
                    }
                });
            });

            $('.status-select').on('change', function () {
                var $this = $(this);
                var newValue = $this.val();
                var recipeOrgStatus = $this.data('orgstatus');
                var recipeOrderId = $this.data('recipeid');


                //
                $('#change-status-modal').modal('show').find('.btn-primary').on('click', function () {
                    $.ajax({
                        method: "post",
                        url: "/lab/sales_order/view/" +{{ order.code }},
                        data: {
                            id: recipeOrderId,
                            orgStatus: recipeOrgStatus,
                            newStatus: newValue
                        },
                        dataType: 'json'
                    }).success(function (data) {
                        if(data.status != "error"){
                            setTimeout(function () {
                                toastr.success(data.msg);
                                setTimeout(function () {
                                    window.location.reload();
                                }, 1000);
                            }, 1000);
                        }
                        else {
                            setTimeout(function () {
                                toastr.error(data.msg);
                            }, 1000);
                        }
                    });
                });
            });
        });
    </script>
{% endblock %}