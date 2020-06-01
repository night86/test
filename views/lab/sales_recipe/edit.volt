{% extends "layouts/main.volt" %}
{% block title %} {{ "Recipes"|t }} {% endblock %}
{% block content %}

    <h3><a href="{{ url("lab/sales_recipe/") }}"><i class="pe-7s-back"></i></a> {{ "Edit recipe"|t }}
        : {{ recipe.custom_code }} - {{ recipe.custom_name }}</h3>

    <div class="col-md-12">
        <br/><br/>
    </div>
    <div class="clearfix"></div>

    <form id="recipeForm" action="{{ url('lab/sales_recipe/edit/' ~ recipe.code ) }}" method="post"
          enctype="multipart/form-data">
        <fieldset class="form-group">

            <legend>{{ 'Recipe defaults'|t }}</legend>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ 'Recipe number'|t }}</label>
                        <div class="col-md-12">
                            {{ recipe.ParentRecipe.recipe_number }}
                            <br/><br/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>{{ 'Signadens product name'|t }}</label>
                        <div class="col-md-12">
                            {{ recipe.ParentRecipe.name }}
                            <br/><br/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>{{ 'Product description'|t }}</label>
                        <div class="col-md-12">
                            {{ recipe.ParentRecipe.description|nl2br }}
                            <br/><br/>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ 'Categories'|t }}</label>
                        <ul>
                            {% for categoryRow in recipe.getCategriesSringArray() %}
                                <li>{{ categoryRow }}</li>
                            {% endfor %}
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    {% if recipe.getStatuses() is not empty %}
                        <label>{{ 'Statuses'|t }}</label>
                        <ol>
                            {% set statuses = unserialize(recipe.getStatuses()) %}
                            {% for status in statuses %}
                                <li>{{ status['name'] }}</li>
                            {% endfor %}
                        </ol>
                    {% endif %}
                </div>
            </div>
            <br/>

            <legend>{{ 'Recipe settings'|t }}</legend>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ 'Price type'|t }}</label>
                        <div class="col-md-12">
                            <label>
                                <input class="radioprice price-composite"
                                       {% if recipe.price_type == 'Composite' %}checked="checked"{% endif %}
                                       type="radio" value="1" name="price_type"/>
                                {{ 'Composite price (calculate price based on tariff codes)'|t }}
                                <strong class="price-calculated"></strong>
                                <input type="hidden" id="price_composite" name="price_composite"/>
                            </label>
                            <label>
                                <input class="radioprice price-fixed"
                                       {% if recipe.price_type == 'Fixed' %}checked="checked"{% endif %} type="radio"
                                       value="2" name="price_type"/>
                                {{ 'Fixed price (all in one price, additional supplied product excluded)'|t }}
                                <br/>{{ "Price"|t }}:
                            </label>
                            <input id="price" type="text" name="price" class="form-control" value="{{ recipe.price }}"/>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ 'Delivery time'|t }}</label>
                        {{ numeric_field('delivery_time', 'class': 'form-control', 'value': recipe.delivery_time) }}
                        {{ 'workdays'|t }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ 'Custom recipe'|t }}</label>
                        {{ text_area('custom_recipe', 'class': 'form-control', 'value': recipe.custom_recipe) }}
                    </div>
                </div>
            </div>
            <legend>{{ 'Custom fields'|t }}</legend>
            <div class="row">
                {#<div class="col-md-6">#}
                {#<div class="form-group">#}
                {#<label>{{ 'Recipe image'|t }}</label>#}
                {#{{ file_field('image', 'class': 'form-control') }}#}
                {#</div>#}
                {#</div>#}
                {% if recipe.image !== null %}
                    <div class="col-md-6">
                        <div class="form-group">
                            <img src="{{ image_url ~ recipe.image }}" width="300">
                        </div>
                    </div>
                {% endif %}
            </div>
            <br/><br/>

            <legend>
                {{ 'Basic elements'|t }}
            </legend>
            <div class="row">
                <div class="col-md-12">

                    <table id="activities" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <th>{{ "Amount"|t }}</th>
                        <th width="30%">{{ "Signadens tariff code"|t }}</th>
                        <th>{{ "Signadens description"|t }}</th>
                        <th>{{ "Actions"|t }}</th>
                        {#<th>{{ "My tariff code"|t }}</th>
                        <th>{{ "My description"|t }}</th>
                        <th>{{ "My tariff price"|t }}</th>#}
                        </thead>
                        <tbody class="activities-body">
                        {% for activity in recipe.ParentRecipe.RecipeActivity %}
                            <tr class="activity-row">
                                <td>{{ activity.amount }}</td>
                                <td>
                                    {% for tariff in tariffs %}
                                        {% if activity.tariffId is tariff.id %}
                                            {{ tariff.code }}
                                        {% endif %}
                                    {% endfor %}
                                </td>
                                <td>{{ activity.description|nl2br }}</td>
                                {#<td>
                                    {% if myTariffs[activity.tariffId] is defined %}
                                        {{ myTariffs[activity.tariffId].code }}
                                    {% endif %}
                                </td>
                                <td>
                                    {% if myTariffs[activity.tariffId] is defined %}
                                        {{ myTariffs[activity.tariffId].description }}
                                    {% endif %}
                                </td>
                                <td>
                                    {% if myTariffs[activity.tariffId] is defined %}
                                        <span class="priceval">{{ myTariffs[activity.tariffId].price }}</span>
                                    {% endif %}
                                </td>#}
                                <td></td>
                            </tr>
                        {% endfor %}
                        </tbody>
                    </table>
                </div>
            </div>
            <legend>
                {{ 'Variable elements'|t }}
            </legend>
            <div class="row">
                <div class="col-md-12">
                    <table id="customfields" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <th>{{ "Amount"|t }}</th>
                        <th>{{ "Field name"|t }}</th>
                        <th width="30%">{{ "Field type"|t }}</th>
                        <th width="30%">{{ "Options"|t }}</th>
                        <th width="15%">{{ "Actions"|t }}</th>
                        </thead>
                        <tbody class="customfields-body">
                        {% for index, customFieldRecipe in customFields %}
                            {% if customFieldRecipe.custom_field_type is 'variable' %}
                            <tr class="customfield-row">
                                <td>{{ customFieldRecipe.amount }}</td>
                                <td>
                                    {{ customFieldRecipe.name }}
                                    {#{{ text_field('customfield[name]['~index~']', 'class': 'form-control', 'value': customFieldRecipe.name) }}#}
                                </td>
                                <td>
                                    {% for fieldtype, fieldtypename in recipe.getCustomFieldTypes() %}
                                        {% if customFieldRecipe.type is fieldtype %}
                                            {{ fieldtypename|t }}
                                        {% endif %}
                                    {% endfor %}
                                    {#<select name="customfield[type][{{ index }}]" class="form-control" disabled>#}
                                    {#{% for fieldtype, fieldtypename in recipe.getCutomFiledTypes() %}#}
                                    {#<option {% if customFieldRecipe.type is fieldtype %}selected="selected"{% endif %}#}
                                    {#value="{{ fieldtype }}">{{ fieldtypename|t }}</option>#}
                                    {#{% endfor %}#}
                                    {#</select>#}
                                </td>
                                <td>
                                    {% if customFieldRecipe.type is 'number' and customFieldRecipe.getCustomPriceTariffId() and customFieldRecipe.getCustomPriceTariffId() is not 0 %}
                                        {% if customFieldRecipe.getCustomPriceType() is 1 %}
                                            {{ "Single additional price"|t }}: €{{ customFieldRecipe.getCustomPriceTariffId() }}
                                        {% elseif customFieldRecipe.getCustomPriceType() is 2 %}
                                            {{ "Additional price per item"|t }}: €{{ customFieldRecipe.getCustomPriceTariffId() }}
                                        {% endif %}
                                    {% endif %}

                                    {% for option in customFieldRecipe.Options %}
                                        <div>
                                            {{ option.getOption() }}
                                            {% if (option.getCustomPriceTariffId() is not null or option.getCustomPriceTariffId() is not 0) and option.getTariffId() is 0 %}
                                                <b>{{ ' | ' }}</b> <i>{{ "Custom price"|t }}:
                                                €{{ option.getCustomPriceTariffId() }}</i>
                                            {% elseif option.getTariffId() is not 0 and option.Tariff %}
                                                <b>{{ ' | ' }}</b>
                                                <i>{{ option.Tariff.code }} {% if option.Tariff.description %} - {{ option.Tariff.description }}{% endif %}</i>
                                            {% endif %}
                                        </div>
                                        {#{{ text_field('customfield[options]['~index~'][]', 'class': 'form-control', 'value': option.getOption()) }}#}
                                    {% endfor %}
                                </td>
                                <td></td>
                            </tr>
                            {% endif %}
                        {% endfor %}
                        </tbody>
                    </table>
                </div>
            </div>

            <legend>
                {{ 'Optional elements'|t }}
            </legend>
            <div class="row">
                <div class="col-md-12">
                    <table id="customfields" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <th>{{ "Amount"|t }}</th>
                        <th>{{ "Field name"|t }}</th>
                        <th width="30%">{{ "Field type"|t }}</th>
                        <th width="30%">{{ "Options"|t }}</th>
                        <th width="15%">{{ "Actions"|t }}</th>
                        </thead>
                        <tbody class="customfields-body">
                        {% for index, customFieldRecipe in customFields %}
                            {% if customFieldRecipe.custom_field_type is 'optional' %}
                            <tr class="customfield-row">
                                <td>{{ customFieldRecipe.amount }}</td>
                                <td>
                                    {{ customFieldRecipe.name }}
                                    {#{{ text_field('customfield[name]['~index~']', 'class': 'form-control', 'value': customFieldRecipe.name) }}#}
                                </td>
                                <td>
                                    {% for fieldtype, fieldtypename in recipe.getCustomFieldTypes() %}
                                        {% if customFieldRecipe.type is fieldtype %}
                                            {{ fieldtypename|t }}
                                        {% endif %}
                                    {% endfor %}
                                    {#<select name="customfield[type][{{ index }}]" class="form-control" disabled>#}
                                    {#{% for fieldtype, fieldtypename in recipe.getCutomFiledTypes() %}#}
                                    {#<option {% if customFieldRecipe.type is fieldtype %}selected="selected"{% endif %}#}
                                    {#value="{{ fieldtype }}">{{ fieldtypename|t }}</option>#}
                                    {#{% endfor %}#}
                                    {#</select>#}
                                </td>
                                <td>
                                    {% if customFieldRecipe.type is 'number' and customFieldRecipe.getCustomPriceTariffId() and customFieldRecipe.getCustomPriceTariffId() is not 0 %}
                                        {% if customFieldRecipe.getCustomPriceType() is 1 %}
                                            {{ "Single additional price"|t }}: €{{ customFieldRecipe.getCustomPriceTariffId() }}
                                        {% elseif customFieldRecipe.getCustomPriceType() is 2 %}
                                            {{ "Additional price per item"|t }}: €{{ customFieldRecipe.getCustomPriceTariffId() }}
                                        {% endif %}
                                    {% endif %}

                                    {% for option in customFieldRecipe.Options %}
                                        <div>
                                            {{ option.getOption() }}
                                            {% if (option.getCustomPriceTariffId() is not null or option.getCustomPriceTariffId() is not 0) and option.getTariffId() is 0 %}
                                                <b>{{ ' | ' }}</b> <i>{{ "Custom price"|t }}:
                                                €{{ option.getCustomPriceTariffId() }}</i>
                                            {% elseif option.getTariffId() is not 0 and option.Tariff %}
                                                <b>{{ ' | ' }}</b>
                                                <i>{{ option.Tariff.code }} {% if option.Tariff.description %} - {{ option.Tariff.description }}{% endif %}</i>
                                            {% endif %}
                                        </div>
                                        {#{{ text_field('customfield[options]['~index~'][]', 'class': 'form-control', 'value': option.getOption()) }}#}
                                    {% endfor %}
                                </td>
                                <td></td>
                            </tr>
                            {% endif %}
                        {% endfor %}
                        </tbody>
                    </table>
                </div>
            </div>

            <legend>
                {{ 'Additional information'|t }}
            </legend>
            <div class="row">
                <div class="col-md-12">
                    <table id="customfields" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <th>{{ "Amount"|t }}</th>
                        <th>{{ "Field name"|t }}</th>
                        <th width="30%">{{ "Field type"|t }}</th>
                        <th width="30%">{{ "Options"|t }}</th>
                        <th width="15%">{{ "Actions"|t }}</th>
                        </thead>
                        <tbody class="customfields-body">
                        {% for index, customFieldRecipe in customFields %}
                            {% if customFieldRecipe.custom_field_type is 'additional' %}
                            <tr class="customfield-row">
                                <td>{{ customFieldRecipe.amount }}</td>
                                <td>
                                    {{ customFieldRecipe.name }}
                                    {#{{ text_field('customfield[name]['~index~']', 'class': 'form-control', 'value': customFieldRecipe.name) }}#}
                                </td>
                                <td>
                                    {% for fieldtype, fieldtypename in recipe.getCustomFieldTypes() %}
                                        {% if customFieldRecipe.type is fieldtype %}
                                            {{ fieldtypename|t }}
                                        {% endif %}
                                    {% endfor %}
                                    {#<select name="customfield[type][{{ index }}]" class="form-control" disabled>#}
                                    {#{% for fieldtype, fieldtypename in recipe.getCutomFiledTypes() %}#}
                                    {#<option {% if customFieldRecipe.type is fieldtype %}selected="selected"{% endif %}#}
                                    {#value="{{ fieldtype }}">{{ fieldtypename|t }}</option>#}
                                    {#{% endfor %}#}
                                    {#</select>#}
                                </td>
                                <td>
                                    {% if customFieldRecipe.type is 'number' and customFieldRecipe.getCustomPriceTariffId() and customFieldRecipe.getCustomPriceTariffId() is not 0 %}
                                        {% if customFieldRecipe.getCustomPriceType() is 1 %}
                                            {{ "Single additional price"|t }}: €{{ customFieldRecipe.getCustomPriceTariffId() }}
                                        {% elseif customFieldRecipe.getCustomPriceType() is 2 %}
                                            {{ "Additional price per item"|t }}: €{{ customFieldRecipe.getCustomPriceTariffId() }}
                                        {% endif %}
                                    {% endif %}

                                    {% for option in customFieldRecipe.Options %}
                                        <div>
                                            {{ option.getOption() }}
                                            {% if (option.getCustomPriceTariffId() is not null or option.getCustomPriceTariffId() is not 0) and option.getTariffId() is 0 %}
                                                <b>{{ ' | ' }}</b> <i>{{ "Custom price"|t }}:
                                                €{{ option.getCustomPriceTariffId() }}</i>
                                            {% elseif option.getTariffId() is not 0 and option.Tariff %}
                                                <b>{{ ' | ' }}</b>
                                                <i>{{ option.Tariff.code }} {% if option.Tariff.description %} - {{ option.Tariff.description }}{% endif %}</i>
                                            {% endif %}
                                        </div>
                                        {#{{ text_field('customfield[options]['~index~'][]', 'class': 'form-control', 'value': option.getOption()) }}#}
                                    {% endfor %}
                                </td>
                                <td></td>
                            </tr>
                            {% endif %}
                        {% endfor %}
                        </tbody>
                    </table>
                </div>
            </div>

            <legend></legend>
            <div class="row">
                <div class="col-lg-6">
                    {% if recipe.getActive() %}
                        <a class="btn btn-warning deactivateaction"
                           href="{{ url('lab/sales_recipe/deactivate/'~recipe.getId()) }}"><i
                                    class="pe-7s-gleam"></i> {{ "Save and deactivate"|t }}</a>
                    {% else %}
                        <a class="btn btn-success activateaction"
                           href="{{ url('lab/sales_recipe/activate/'~recipe.getId()) }}"><i
                                    class="pe-7s-gleam"></i> {{ "Save and activate"|t }}</a>
                    {% endif %}
                </div>
                <div class="col-lg-6">
                    <button type="submit" class="btn btn-primary pull-right"><i
                                class="pe-7s-diskette"></i> {{ "Save"|t }}</button>
                </div>
            </div>

        </fieldset>
    </form>

{% endblock %}

{% block scripts %}
    {{ super() }}

    <script>

    </script>

{% endblock %}