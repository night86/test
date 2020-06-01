{% extends "layouts/main.volt" %}
{% block title %} {{ "Recipe details"|t }} {% endblock %}

{% block bodyclass %}showproduct{% endblock %}
{% block content %}

        <div class="row">
            <div class="col-md-12">
                <h3><a href="javascript:history.back()"><i class="pe-7s-back"></i></a></h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {% if not recipe.Recipes.ParentRecipe or recipe.Recipes.ParentRecipe.image is null %}
                        <div class="product-image"
                             style="background-image: url('http://placehold.it/600x350/ffffff?text=Geen+foto+beschikbaar');"></div>
                        {#<img class="product-image img-responsive"#}
                        {#src="http://placehold.it/600x350/ffffff?text=Geen+foto+beschikbaar" alt="No photo"/>#}
                    {% else %}
                        <div class="product-image"
                             style="background-image: url('{{ url('uploads/images/recipes/'~recipe.Recipes.ParentRecipe.image) }}');"></div>
                        {#<img class="product-image img-responsive" src="{{ url(product['image']) }}" alt="{{ product['name'] }}"/>#}
                    {% endif %}
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <h3>
                        {% if recipe.Recipes.ParentRecipe.customName %}
                            {{ recipe.Recipes.ParentRecipe.customName }}
                        {% else %}
                            {{ recipe.Recipes.ParentRecipe.name }}
                        {% endif %}
                        {#{{ recipe.custom_name }} <span class="pull-right recipeprice">{{ recipe.Recipes.price }}</span>#}
                    </h3>
                    <hr/>
                    <p>
                        {{ recipe.Recipes.custom_recipe }}
                    </p>
                    <p>
                        {{ recipe.Recipes.ParentRecipe.description }}
                    </p>
                    <hr/>
                    <strong>{{ 'Product features'|t }}</strong>
                    <hr/>
                    <p>
                        {% if recipe.Recipes.ParentRecipe.has_schema is 1 %}
                        <div id="view_schema" class="row">
                            <div class="col-sm-9">
                                <table id="teeth">
                                    <tr>
                                        <td><span>18</span><input id="T18" name="teeth[18]" type="hidden" value="{% if in_array(18,schema_default) %}1{% else %}0{% endif %}"><label class="upper {% if in_array(18,schema_default) %}checked{% endif %}" for="T18"></label></td>
                                        <td><span>17</span><input id="T17" name="teeth[17]" type="hidden" value="{% if in_array(17,schema_default) %}1{% else %}0{% endif %}"><label class="upper {% if in_array(17,schema_default) %}checked{% endif %}" for="T17"></label></td>
                                        <td><span>16</span><input id="T16" name="teeth[16]" type="hidden" value="{% if in_array(16,schema_default) %}1{% else %}0{% endif %}"><label class="upper {% if in_array(16,schema_default) %}checked{% endif %}" for="T16"></label></td>
                                        <td><span>15</span><input id="T15" name="teeth[15]" type="hidden" value="{% if in_array(15,schema_default) %}1{% else %}0{% endif %}"><label class="upper {% if in_array(15,schema_default) %}checked{% endif %}" for="T15"></label></td>
                                        <td><span>14</span><input id="T14" name="teeth[14]" type="hidden" value="{% if in_array(14,schema_default) %}1{% else %}0{% endif %}"><label class="upper {% if in_array(14,schema_default) %}checked{% endif %}" for="T14"></label></td>
                                        <td><span>13</span><input id="T13" name="teeth[13]" type="hidden" value="{% if in_array(13,schema_default) %}1{% else %}0{% endif %}"><label class="upper {% if in_array(13,schema_default) %}checked{% endif %}" for="T13"></label></td>
                                        <td><span>12</span><input id="T12" name="teeth[12]" type="hidden" value="{% if in_array(12,schema_default) %}1{% else %}0{% endif %}"><label class="upper {% if in_array(12,schema_default) %}checked{% endif %}" for="T12"></label></td>
                                        <td><span>11</span><input id="T11" name="teeth[11]" type="hidden" value="{% if in_array(11,schema_default) %}1{% else %}0{% endif %}"><label class="upper {% if in_array(11,schema_default) %}checked{% endif %}" for="T11"></label></td>
                                        <td class="divider"><span>21</span><input id="T21" name="teeth[21]" type="hidden" value="{% if in_array(21,schema_default) %}1{% else %}0{% endif %}"><label class="upper {% if in_array(21,schema_default) %}checked{% endif %}" for="T21"></label></td>
                                        <td><span>22</span><input id="T22" name="teeth[22]" type="hidden" value="{% if in_array(22,schema_default) %}1{% else %}0{% endif %}"><label class="upper {% if in_array(22,schema_default) %}checked{% endif %}" for="T22"></label></td>
                                        <td><span>23</span><input id="T23" name="teeth[23]" type="hidden" value="{% if in_array(23,schema_default) %}1{% else %}0{% endif %}"><label class="upper {% if in_array(23,schema_default) %}checked{% endif %}" for="T23"></label></td>
                                        <td><span>24</span><input id="T24" name="teeth[24]" type="hidden" value="{% if in_array(24,schema_default) %}1{% else %}0{% endif %}"><label class="upper {% if in_array(24,schema_default) %}checked{% endif %}" for="T24"></label></td>
                                        <td><span>25</span><input id="T25" name="teeth[25]" type="hidden" value="{% if in_array(25,schema_default) %}1{% else %}0{% endif %}"><label class="upper {% if in_array(25,schema_default) %}checked{% endif %}" for="T25"></label></td>
                                        <td><span>26</span><input id="T26" name="teeth[26]" type="hidden" value="{% if in_array(26,schema_default) %}1{% else %}0{% endif %}"><label class="upper {% if in_array(26,schema_default) %}checked{% endif %}" for="T26"></label></td>
                                        <td><span>27</span><input id="T27" name="teeth[27]" type="hidden" value="{% if in_array(27,schema_default) %}1{% else %}0{% endif %}"><label class="upper {% if in_array(27,schema_default) %}checked{% endif %}" for="T27"></label></td>
                                        <td><span>28</span><input id="T28" name="teeth[28]" type="hidden" value="{% if in_array(28,schema_default) %}1{% else %}0{% endif %}"><label class="upper {% if in_array(28,schema_default) %}checked{% endif %}" for="T28"></label></td>
                                    </tr>
                                    <tr class="divider">
                                        <td><input id="T48" name="teeth[48]" type="hidden" value="{% if in_array(48,schema_default) %}1{% else %}0{% endif %}"><label class="lower {% if in_array(48,schema_default) %}checked{% endif %}" for="T48"></label><span>48</span></td>
                                        <td><input id="T47" name="teeth[47]" type="hidden" value="{% if in_array(47,schema_default) %}1{% else %}0{% endif %}"><label class="lower {% if in_array(47,schema_default) %}checked{% endif %}" for="T47"></label><span>47</span></td>
                                        <td><input id="T46" name="teeth[46]" type="hidden" value="{% if in_array(46,schema_default) %}1{% else %}0{% endif %}"><label class="lower {% if in_array(46,schema_default) %}checked{% endif %}" for="T46"></label><span>46</span></td>
                                        <td><input id="T45" name="teeth[45]" type="hidden" value="{% if in_array(45,schema_default) %}1{% else %}0{% endif %}"><label class="lower {% if in_array(45,schema_default) %}checked{% endif %}" for="T45"></label><span>45</span></td>
                                        <td><input id="T44" name="teeth[44]" type="hidden" value="{% if in_array(44,schema_default) %}1{% else %}0{% endif %}"><label class="lower {% if in_array(44,schema_default) %}checked{% endif %}" for="T44"></label><span>44</span></td>
                                        <td><input id="T43" name="teeth[43]" type="hidden" value="{% if in_array(43,schema_default) %}1{% else %}0{% endif %}"><label class="lower {% if in_array(43,schema_default) %}checked{% endif %}" for="T43"></label><span>43</span></td>
                                        <td><input id="T42" name="teeth[42]" type="hidden" value="{% if in_array(42,schema_default) %}1{% else %}0{% endif %}"><label class="lower {% if in_array(42,schema_default) %}checked{% endif %}" for="T42"></label><span>42</span></td>
                                        <td><input id="T41" name="teeth[41]" type="hidden" value="{% if in_array(41,schema_default) %}1{% else %}0{% endif %}"><label class="lower {% if in_array(41,schema_default) %}checked{% endif %}" for="T41"></label><span>41</span></td>
                                        <td class="divider"><input id="T31" name="teeth[31]" type="hidden" value="{% if in_array(31,schema_default) %}1{% else %}0{% endif %}"><label class="lower {% if in_array(31,schema_default) %}checked{% endif %}" for="T31"></label><span>31</span></td>
                                        <td><input id="T32" name="teeth[32]" type="hidden" value="{% if in_array(32,schema_default) %}1{% else %}0{% endif %}"><label class="lower {% if in_array(32,schema_default) %}checked{% endif %}" for="T32"></label><span>32</span></td>
                                        <td><input id="T33" name="teeth[33]" type="hidden" value="{% if in_array(33,schema_default) %}1{% else %}0{% endif %}"><label class="lower {% if in_array(33,schema_default) %}checked{% endif %}" for="T33"></label><span>33</span></td>
                                        <td><input id="T34" name="teeth[34]" type="hidden" value="{% if in_array(34,schema_default) %}1{% else %}0{% endif %}"><label class="lower {% if in_array(34,schema_default) %}checked{% endif %}" for="T34"></label><span>34</span></td>
                                        <td><input id="T35" name="teeth[35]" type="hidden" value="{% if in_array(35,schema_default) %}1{% else %}0{% endif %}"><label class="lower {% if in_array(35,schema_default) %}checked{% endif %}" for="T35"></label><span>35</span></td>
                                        <td><input id="T36" name="teeth[36]" type="hidden" value="{% if in_array(36,schema_default) %}1{% else %}0{% endif %}"><label class="lower {% if in_array(36,schema_default) %}checked{% endif %}" for="T36"></label><span>36</span></td>
                                        <td><input id="T37" name="teeth[37]" type="hidden" value="{% if in_array(37,schema_default) %}1{% else %}0{% endif %}"><label class="lower {% if in_array(37,schema_default) %}checked{% endif %}" for="T37"></label><span>37</span></td>
                                        <td><input id="T38" name="teeth[38]" type="hidden" value="{% if in_array(38,schema_default) %}1{% else %}0{% endif %}"><label class="lower {% if in_array(38,schema_default) %}checked{% endif %}" for="T38"></label><span>38</span></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-sm-3"></div>
                        </div>
                        {% endif %}
                    </p>

                    {% if count(recipe.Recipes.ParentRecipe.RecipeActivity) > 0 %}
                    <hr/>
                    <p><strong>{{ "Basic elements"|t }}</strong></p>
                    <table id="activities" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        {% for activity in recipe.Recipes.ParentRecipe.RecipeActivity %}
                            {% if activity.amount != 1 %}
                                {% set hasActivity += 1 %}
                            {% endif %}
                        {% endfor %}

                        {% if hasActivity > 0 %}
                            <th style="width: 30px;"></th>
                        {% endif %}
                        <th>{{ "Tariff code"|t }}</th>
                        <th>{{ "Description"|t }}</th>
                        <th>{{ "Tariff price"|t }}</th>
                        </thead>
                        <tbody class="activities-body">
                        {% for activity in recipe.Recipes.ParentRecipe.RecipeActivity %}
                            <tr class="activity-row">
                                {% if hasActivity > 0 %}
                                    {% if activity.amount != 1 %}
                                        <td><input type="text" value="{{ activity.amount }}" disabled="disabled" style="width: 30px;" /></td>
                                    {% else %}
                                        <td></td>
                                    {% endif %}
                                {% endif %}
                                <td>
                                    {% if myTariffs[activity.tariffId] is defined %}
                                        {{ myTariffs[activity.tariffId]['code'] }}
                                    {% endif %}
                                </td>
                                <td>{{ activity.description }}</td>
                                <td>
                                    {% if myTariffs[activity.tariffId] is defined %}
                                        <span class="priceval">{{ myTariffs[activity.tariffId]['price'] }}</span>
                                    {% endif %}
                                </td>
                            </tr>
                        {% endfor %}
                        </tbody>
                    </table>
                    {% endif %}
                    {% if availableCustomFields['var'] is 1 %}
                    <hr/>
                    <p><strong>{{ "Variable elements"|t }}</strong></p>
                    {% for customField in recipe.DentistOrderRecipeData %}
                        {% if customField.RecipeCustomField.custom_field_type is 'variable' %}
                        <div class="form-group">
                            {% if customField.amount != 1 %}
                                <input type="text" value="{{ customField.amount }}" disabled="disabled" style="width: 30px;" />
                            {% endif %}
                            <label>{{ customField.field_name }}</label>
                            {% if customField.field_type === "text" %}
                                {{ text_field('customField['~customField.id~']', 'class': 'form-control', 'value': customField.field_value, 'disabled':'disabled') }}
                                {% elseif customField.field_type === "number" %}
                                {{ numeric_field('customField['~customField.id~']', 'class': 'form-control', 'value': customField.field_value, 'disabled':'disabled') }}
                            {% elseif customField.field_type === "select" %}
                                <select class="form-control" name="customField[{{ customField.id }}]" disabled="disabled">
                                    {% for option in customField.Options %}
                                        <option value="{{ option.value }}" {% if option.value == customField.field_value %}selected="selected"{% endif %}>{{ option.option }}</option>
                                    {% endfor %}
                                </select>
                            {% elseif customField.field_type === "checkbox" %}

                                {% for option in customField.RecipeCustomField.Options %}

                                    <div class="checkbox">
                                        <label><input type="checkbox" disabled="disabled"
                                                      name="customField[{{ customField.id }}][{{ option.id }}]"
                                                      value="{{ option.value }}" {% if isset(json_decode(customField.field_value)[option.id]) and option.value is json_decode(customField.field_value)[option.id] %}checked="checked"{% else %}{% endif %} />{{ option.option }}</label>
                                    </div>
                                {% endfor %}
                            {% elseif customField.field_type === "textarea" %}
                                <textarea name="customField[{{ customField.id }}]" class="form-control tinymce-limited" style="height: 150px;" disabled="disabled">{{ customField.field_value }}</textarea>
                            {% endif %}
                        </div>
                        {% endif %}
                    {% endfor %}
                    {% endif %}

                    {% if availableCustomFields['opt'] is 1 %}
                    <hr/>
                    <p><strong>{{ "Optional elements"|t }}</strong></p>
                    {% for customField in recipe.DentistOrderRecipeData %}
                        {% if customField.RecipeCustomField.custom_field_type is 'optional' %}
                            <div class="form-group">
                                {% if customField.amount != 1 %}
                                    <input type="text" value="{{ customField.amount }}" disabled="disabled" style="width: 30px;" />
                                {% endif %}
                                <label>{{ customField.field_name }}</label>
                                {% if customField.field_type === "text" %}
                                    {{ text_field('customField['~customField.id~']', 'class': 'form-control', 'value': customField.field_value, 'disabled':'disabled') }}
                                {% elseif customField.field_type === "number" %}
                                    {{ numeric_field('customField['~customField.id~']', 'class': 'form-control', 'value': customField.field_value, 'disabled':'disabled') }}
                                {% elseif customField.field_type === "select" %}
                                    <select class="form-control" name="customField[{{ customField.id }}]" disabled="disabled">
                                        {% for option in customField.Options %}
                                            <option value="{{ option.value }}" {% if option.value == customField.field_value %}selected="selected"{% endif %}>{{ option.option }}</option>
                                        {% endfor %}
                                    </select>
                                {% elseif customField.field_type === "checkbox" %}
                                    {% for option in customField.RecipeCustomField.Options %}
                                        <div class="checkbox">
                                            <label><input type="checkbox" disabled="disabled"
                                                          name="customField[{{ customField.id }}][{{ option.id }}]"
                                                          value="{{ option.value }}" {% if isset(json_decode(customField.field_value)[option.id]) and option.value is json_decode(customField.field_value)[option.id] %}checked="checked"{% else %}{% endif %} />{{ option.option }}</label>
                                        </div>
                                    {% endfor %}
                                {% elseif customField.field_type === "textarea" %}
                                    <textarea name="customField[{{ customField.id }}]" class="form-control tinymce-limited" style="height: 150px;" disabled="disabled">{{ customField.field_value }}</textarea>
                                {% endif %}
                            </div>
                        {% endif %}
                    {% endfor %}
                    {% endif %}

                    {% if availableCustomFields['add'] is 1 %}
                    <hr/>
                    <p><strong>{{ "Additional information"|t }}</strong></p>
                    {% for customField in recipe.DentistOrderRecipeData %}
                        {% if customField.RecipeCustomField.custom_field_type is 'additional' %}
                            <div class="form-group">
                                {% if customField.amount != 1 %}
                                    <input type="text" value="{{ customField.amount }}" disabled="disabled" style="width: 30px;" />
                                {% endif %}
                                <label>{{ customField.field_name }}</label>
                                {% if customField.field_type === "text" %}
                                    {{ text_field('customField['~customField.id~']', 'class': 'form-control', 'value': customField.field_value, 'disabled':'disabled') }}
                                {% elseif customField.field_type === "number" %}
                                    {{ numeric_field('customField['~customField.id~']', 'class': 'form-control', 'value': customField.field_value, 'disabled':'disabled') }}
                                {% elseif customField.field_type === "select" %}
                                    <select class="form-control" name="customField[{{ customField.id }}]" disabled="disabled">
                                        {% for option in customField.Options %}
                                            <option value="{{ option.value }}" {% if option.value == customField.field_value %}selected="selected"{% endif %}>{{ option.option }}</option>
                                        {% endfor %}
                                    </select>
                                {% elseif customField.field_type === "checkbox" %}
                                    {% for option in customField.RecipeCustomField.Options %}
                                        <div class="checkbox">
                                            <label><input type="checkbox" disabled="disabled"
                                                          name="customField[{{ customField.id }}][{{ option.id }}]"
                                                          value="{{ option.value }}" {% if isset(json_decode(customField.field_value)[option.id]) and option.value is json_decode(customField.field_value)[option.id] %}checked="checked"{% else %}{% endif %} />{{ option.option }}</label>
                                        </div>
                                    {% endfor %}
                                {% elseif customField.field_type === "textarea" %}
                                    <textarea name="customField[{{ customField.id }}]" class="form-control tinymce-limited" style="height: 150px;" disabled="disabled">{{ customField.field_value }}</textarea>
                                {% endif %}
                            </div>
                        {% endif %}
                    {% endfor %}
                    {% endif %}
                    <hr/>
                </div>
                <div class="form-group">
                    <label>{{ 'Requested delivery date(s)'|t }}</label>
                    {#<span class="pull-right">#}
                        {#<a id="add-date" data-recipe="{{ recipe.id }}" data-counter="{% if count(recipe.Delivery) > 0 %}{{ count(recipe.Delivery) }}{% else %}{{ "0" }}{% endif %}" style="cursor: pointer;"><i class="pe-7s-plus" style="font-size: x-large;"></i></a>#}
                    {#</span>#}
                    <table id="deliveryDates" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>{{ "Phase"|t }}</th>
                            <th>{{ 'Requested delivery date'|t }}</th>
                            <th>{{ 'Prefered part of the day'|t }}</th>
                            {#<th>{{ "Actions"|t }}</th>#}
                        </tr>
                        </thead>
                        <tbody class="delivery-body">
                            {% for index, delivery in recipe.Delivery %}
                                <tr data-phase="{{ delivery.recipe_status_id }}" data-days="{{ delivery.days }}" id="row_{{ index+1 }}" class="delivery-row">
                                    <td id="text_{{ index+1 }}">{{ delivery.delivery_text }}</td>
                                    <td id="date_{{ index+1 }}">{{ delivery.delivery_date }}</td>
                                    <td id="partOfDay_{{ index+1 }}">{{ delivery.part_of_day }}</td>
                                    {#<td>#}
                                        {#<a id="edit_{{ index+1 }}" class="btn btn-primary btn-sm edit-date" data-date="{{ delivery.delivery_date }}" data-text="{{ delivery.delivery_text }}" data-counter="{{ index+1 }}">#}
                                            {#<i class="pe-7s-pen"></i>{{ "Edit"|t}}</a>#}
                                        {#<a class="btn btn-danger btn-sm remove-date" data-counter="{{ index+1 }}">#}
                                            {#<i class="pe-7s-close-circle"></i> {{"Delete"|t}}</a>#}

                                        {#<input id="dateval_{{ index+1 }}" type="hidden" value="{{ delivery.delivery_date }}" name="delivery_old[{{ index+1 }}][date]" />#}
                                        {#<input id="textval_{{ index+1 }}" type="hidden" value="{{ delivery.delivery_text }}" name="delivery_old[{{ index+1 }}][text]" />#}
                                        {#<input id="daysval_{{ index+1 }}" type="hidden" value="{{ delivery.days }}" name="delivery_old[{{ index+1 }}][days]" />#}
                                        {#<input id="phaseval_{{ index+1 }}" type="hidden" value="{{ delivery.recipe_status_id }}" name="delivery_old[{{ index+1 }}][phase]" />#}
                                        {#<input id="partOfDayval_{{ index+1 }}" class="part-of-day" type="hidden" value="{{ delivery.part_of_day }}" name="delivery_old[{{ index+1 }}][part_of_day]" />#}
                                        {#<input id="idval_{{ index+1 }}" type="hidden" value="{{ delivery.id }}" name="delivery_old[{{ index+1 }}][id]" />#}
                                    {#</td>#}
                                </tr>
                            {% endfor %}
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    <label>{{ 'Attachment(s)'|t }}</label>
                    {#{{ file_field('files[]', 'class': 'form-control', 'multiple': 'multiple') }}#}
                    <table id="fileList" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>{{ 'Filename'|t }}</th>
                            {#<th>{{ "Actions"|t }}</th>#}
                        </tr>
                        </thead>
                        <tbody class="filelist-body">
                        {% for file in recipe.Files %}
                            <tr id="rowFile_{{ file.id }}">
                               <td>{{ file.file_name }}</td>
                               {#<td><a class="btn btn-danger btn-sm remove-file" data-id="{{ file.id }}">#}
                                       {#<i class="pe-7s-close-circle"></i> {{"Delete"|t}}</a>#}
                               {#</td>#}
                            </tr>
                            <input id="deletedFile_{{ file.id }}" type="hidden" name="deletedFile[{{ file.id }}]" value="0" />
                        {% endfor %}
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    {{ text_area('description', 'placeholder': 'Order notes or remarks...'|t, 'class': 'form-control', 'value': recipe.DentistOrder.description, 'disabled':'disabled') }}
                </div>
            </div>
        </div>

        {#<legend></legend>#}
        {#<div class="row">#}
            {#<div class="col-lg-12">#}
                {#<button type="submit" class="btn btn-primary pull-right" data-type="{{ recipe.Recipes.getPriceType() }}"><i#}
                            {#class="pe-7s-diskette"></i> {{ "Save changes"|t }}</button>#}
            {#</div>#}
        {#</div>#}

        <input type="hidden" name="price" id="recipefinalprice" value="{{ recipe.Recipes.price }}" />

    {#{{ partial("modals/alert", ['id': 'price-info', 'title': 'Attention', 'content': 'This is a target price, the actual price will be calculation on production']) }}#}
    {#{{ partial("modals/addSingleFieldDate", ['id': 'add-modal', 'title': "Add delivery date"|t, "type": "new", "statuses_times": statuses_times, "statuses_av": statuses_av]) }}#}
    {#{{ partial("modals/editSingleFieldDate", ['id': 'edit-modal', 'title': "Edit delivery date"|t, "type": "edit", "statuses_times": statuses_times, "statuses_av": statuses_av]) }}#}

{% endblock %}
{% block scripts %}
    {{ super() }}
    <script>
        $(function () {

            tinymce.init({
                selector: '.tinymce-limited',
                language_url: '/js/tinymce/langs/nl.js',
                menubar: false,
                statusbar: false,
                toolbar: false,
                branding: false,
                height: 300,
                readonly: 1
            });

        });
    </script>
{% endblock %}
