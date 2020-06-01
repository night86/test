{% extends "layouts/main.volt" %}
{% block title %} {{ "Edit order"|t }} {% endblock %}

{% block bodyclass %}showproduct{% endblock %}
{% block content %}



    <form id="orderForm" action="{{ url('dentist/order/showproduct/' ~ order.code ~ '/' ~ recipe.code ) }}"
          method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-12">
                <h3><a href="javascript:history.back()"><i class="pe-7s-back"></i></a></h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {% if not recipe.ParentRecipe or recipe.ParentRecipe.image is null %}
                        <div class="product-image" style="background-image: url('http://placehold.it/600x350/ffffff?text=Geen+foto+beschikbaar');"></div>
                        {#<img class="product-image img-responsive"#}
                        {#src="http://placehold.it/600x350/ffffff?text=Geen+foto+beschikbaar" alt="No photo"/>#}
                    {% else %}
                        <div class="product-image" style="background-image: url('{{ url('uploads/images/recipes/'~recipe.ParentRecipe.image) }}');"></div>
                        {#<img class="product-image img-responsive" src="{{ url(product['image']) }}" alt="{{ product['name'] }}"/>#}
                    {% endif %}
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <h3>
                        {% if recipe.ParentRecipe.customName %}
                            {{ recipe.ParentRecipe.customName }}
                        {% else %}
                            {{ recipe.ParentRecipe.name }}
                        {% endif %}
                        {#{{ recipe.custom_name }}#}
                        <span class="pull-right recipeprice">{% if recipe.price_type is 2 %}{{ recipe.price }}{% endif %}</span>
                    </h3>
                    <hr/>
                    <p>{{ recipe.custom_recipe }}</p>
                    <p>{{ recipe.ParentRecipe.description }}</p>
                    <hr/>
                    <strong>{{ 'Product features'|t }}</strong>
                    <hr/>
                    {% if recipe.ParentRecipe.has_schema is 1 %}
                    <p>{{ recipe.ParentRecipe.schema_notice }}</p>
                    {% endif %}
                    <p>
                        {% if recipe.ParentRecipe.has_schema is 1 %}
                        <div id="view_schema" class="row">
                            <div class="col-sm-9">
                                <table id="teeth">
                                    <tr>
                                        <td><span>18</span><input id="T18" name="teeth[18]" type="hidden" value="0"><label class="upper" for="T18"></label></td>
                                        <td><span>17</span><input id="T17" name="teeth[17]" type="hidden" value="0"><label class="upper" for="T17"></label></td>
                                        <td><span>16</span><input id="T16" name="teeth[16]" type="hidden" value="0"><label class="upper" for="T16"></label></td>
                                        <td><span>15</span><input id="T15" name="teeth[15]" type="hidden" value="0"><label class="upper" for="T15"></label></td>
                                        <td><span>14</span><input id="T14" name="teeth[14]" type="hidden" value="0"><label class="upper" for="T14"></label></td>
                                        <td><span>13</span><input id="T13" name="teeth[13]" type="hidden" value="0"><label class="upper" for="T13"></label></td>
                                        <td><span>12</span><input id="T12" name="teeth[12]" type="hidden" value="0"><label class="upper" for="T12"></label></td>
                                        <td><span>11</span><input id="T11" name="teeth[11]" type="hidden" value="0"><label class="upper" for="T11"></label></td>
                                        <td class="divider"><span>21</span><input id="T21" name="teeth[21]" type="hidden" value="0"><label class="upper" for="T21"></label></td>
                                        <td><span>22</span><input id="T22" name="teeth[22]" type="hidden" value="0"><label class="upper" for="T22"></label></td>
                                        <td><span>23</span><input id="T23" name="teeth[23]" type="hidden" value="0"><label class="upper" for="T23"></label></td>
                                        <td><span>24</span><input id="T24" name="teeth[24]" type="hidden" value="0"><label class="upper" for="T24"></label></td>
                                        <td><span>25</span><input id="T25" name="teeth[25]" type="hidden" value="0"><label class="upper" for="T25"></label></td>
                                        <td><span>26</span><input id="T26" name="teeth[26]" type="hidden" value="0"><label class="upper" for="T26"></label></td>
                                        <td><span>27</span><input id="T27" name="teeth[27]" type="hidden" value="0"><label class="upper" for="T27"></label></td>
                                        <td><span>28</span><input id="T28" name="teeth[28]" type="hidden" value="0"><label class="upper" for="T28"></label></td>
                                    </tr>
                                    <tr class="divider">
                                        <td><input id="T48" name="teeth[48]" type="hidden" value="0"><label class="lower" for="T48"></label><span>48</span></td>
                                        <td><input id="T47" name="teeth[47]" type="hidden" value="0"><label class="lower" for="T47"></label><span>47</span></td>
                                        <td><input id="T46" name="teeth[46]" type="hidden" value="0"><label class="lower" for="T46"></label><span>46</span></td>
                                        <td><input id="T45" name="teeth[45]" type="hidden" value="0"><label class="lower" for="T45"></label><span>45</span></td>
                                        <td><input id="T44" name="teeth[44]" type="hidden" value="0"><label class="lower" for="T44"></label><span>44</span></td>
                                        <td><input id="T43" name="teeth[43]" type="hidden" value="0"><label class="lower" for="T43"></label><span>43</span></td>
                                        <td><input id="T42" name="teeth[42]" type="hidden" value="0"><label class="lower" for="T42"></label><span>42</span></td>
                                        <td><input id="T41" name="teeth[41]" type="hidden" value="0"><label class="lower" for="T41"></label><span>41</span></td>
                                        <td class="divider"><input id="T31" name="teeth[31]" type="hidden" value="0"><label class="lower" for="T31"></label><span>31</span></td>
                                        <td><input id="T32" name="teeth[32]" type="hidden" value="0"><label class="lower" for="T32"></label><span>32</span></td>
                                        <td><input id="T33" name="teeth[33]" type="hidden" value="0"><label class="lower" for="T33"></label><span>33</span></td>
                                        <td><input id="T34" name="teeth[34]" type="hidden" value="0"><label class="lower" for="T34"></label><span>34</span></td>
                                        <td><input id="T35" name="teeth[35]" type="hidden" value="0"><label class="lower" for="T35"></label><span>35</span></td>
                                        <td><input id="T36" name="teeth[36]" type="hidden" value="0"><label class="lower" for="T36"></label><span>36</span></td>
                                        <td><input id="T37" name="teeth[37]" type="hidden" value="0"><label class="lower" for="T37"></label><span>37</span></td>
                                        <td><input id="T38" name="teeth[38]" type="hidden" value="0"><label class="lower" for="T38"></label><span>38</span></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-sm-3"></div>
                        </div>
                        {% endif %}
                    </p>

                    {% if count(recipe.ParentRecipe.RecipeActivity) > 0 %}
                    <hr/>
                    <p><strong>{{ "Basic elements"|t }}</strong></p>
                    <table id="activities" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        {% for activity in recipe.ParentRecipe.RecipeActivity %}
                            {% if activity.amount != 1 %}
                                {% set hasActivity += 1 %}
                            {% endif %}
                        {% endfor %}

                        {% if hasActivity > 0 %}
                        <th style="width: 30px;"></th>
                        {% endif %}
                        <th>{{ "Tariff code"|t }}</th>
                        <th>{{ "Description"|t }}</th>
                        {% if recipe.getPriceType() is not 'Fixed' %}
                            <th>{{ "Tariff price"|t }}</th>
                        {% endif %}
                        </thead>
                        <tbody class="activities-body">
                        {% for activity in recipe.ParentRecipe.RecipeActivity %}
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
                                    {% else %}
                                        {{ activity.Tariff.code }}
                                    {% endif %}
                                </td>
                                <td>{{ activity.description }}</td>
                                {% if recipe.getPriceType() is not 'Fixed' %}
                                    <td>
                                        {% if myTariffs[activity.tariffId] is defined %}
                                            <span class="priceval">{{ myTariffs[activity.tariffId]['price'] }}</span>
                                        {% else %}
                                            <span class="priceval">{{ activity.Tariff.price }}</span>
                                        {% endif %}
                                    </td>
                                {% endif %}
                            </tr>
                        {% endfor %}
                        </tbody>
                    </table>
                    {% endif %}
                    {% if availableCustomFields['var'] is 1 %}
                    <hr/>
                    <p><strong>{{ "Variable elements"|t }}</strong></p>
                    {% for customField in recipe.ParentRecipe.RecipeCustomField %}
                        {% if customField.custom_field_type is 'variable' %}
                        <div class="form-group">
                            {#{% if customField.type !== "statement" %}#}
                                {#{% if customField.amount != 1 %}#}
                                    {#<input type="text" value="{{ customField.amount }}" disabled="disabled" style="width: 30px;" />#}
                                {#{% endif %}#}
                            {#<label>{{ customField.name }}</label>#}
                            {#{% else %}#}
                            {#{% endif %}#}
                            {% if customField.amount != 1 %}
                                <input type="text" value="{{ customField.amount }}" disabled="disabled" style="width: 30px;" />
                            {% endif %}
                            <label>{{ customField.name }}</label>

                            {% if customField.type === "text" %}
                                {{ text_field('customField['~customField.id~']', 'class': 'form-control') }}
                                {% if customField.has_lab_check is 1 %}
                                    <input type="checkbox" name="customFieldLab[{{ customField.id }}]" class="field_lab" value="0" />
                                    <label for="add_option">&nbsp;&nbsp;{{ "To be determined by lab"|t }}</label>
                                {% endif %}
                            {% elseif customField.type === "number" %}
                                <input type="number" id="customField[{{ customField.id }}]" name="customField[{{ customField.id }}]" class="form-control numeric" min="0" value="" />
                                {% if customField.has_lab_check is 1 %}
                                    <input type="checkbox" name="customFieldLab[{{ customField.id }}]" class="field_lab" value="0" />
                                    <label for="add_option">&nbsp;&nbsp;{{ "To be determined by lab"|t }}</label>
                                {% endif %}
                            {% elseif customField.type === "select" %}
                                <select class="form-control" name="customField[{{ customField.id }}]">
                                    {% for option in customField.Options %}
                                        <option value="{{ option.value }}">{{ option.option }}</option>
                                    {% endfor %}
                                </select>
                                {% if customField.has_lab_check is 1 %}
                                    <input type="checkbox" name="customFieldLab[{{ customField.id }}]" class="field_lab" value="0" />
                                    <label for="add_option">&nbsp;&nbsp;{{ "To be determined by lab"|t }}</label>
                                {% endif %}
                            {% elseif customField.type === "checkbox" %}
                                <input type="hidden" name="customField[{{ customField.id }}][]" />
                                {% for option in customField.Options %}
                                    <div class="checkbox">
                                        <label><input type="checkbox"
                                                      name="customField[{{ customField.id }}][{{ option.id }}]"
                                                      value="{{ option.value }}">{{ option.option }}</label>
                                    </div>
                                {% endfor %}
                                {% if customField.has_lab_check is 1 %}
                                    <div class="checkbox">
                                        <label for="add_option">
                                            <input type="checkbox" name="customFieldLab[{{ customField.id }}]" class="field_lab" value="0" />
                                            {{ "To be determined by lab"|t }}</label>
                                    </div>
                                {% endif %}
                            {% elseif customField.type === "statement" %}
                                <input type="hidden" id="customField[{{ customField.id }}]" name="customField[{{ customField.id }}]" />
                            {% elseif customField.type === "textarea" %}
                                <textarea name="customField[{{ customField.id }}]" class="form-control tinymce-limited" style="height: 150px;"></textarea>
                            {% endif %}
                        </div>
                        {% endif %}
                    {% endfor %}
                    {% endif %}

                    {% if availableCustomFields['opt'] is 1 %}
                    <hr/>
                    <p><strong>{{ "Optional elements"|t }}</strong></p>
                    {% for customField in recipe.ParentRecipe.RecipeCustomField %}
                        {% if customField.custom_field_type is 'optional' %}
                        <div class="form-group">
                            {#{% if customField.type !== "statement" %}#}
                                {#<label>{{ customField.name }}</label>#}
                            {#{% else %}#}
                            {#{% endif %}#}
                            {% if customField.amount != 1 %}
                                <input type="text" value="{{ customField.amount }}" disabled="disabled" style="width: 30px;" />
                            {% endif %}
                            <label>{{ customField.name }}</label>

                            {% if customField.type === "text" %}
                                {{ text_field('customField['~customField.id~']', 'class': 'form-control') }}
                                {% if customField.has_lab_check is 1 %}
                                    <input type="checkbox" name="customFieldLab[{{ customField.id }}]" class="field_lab" value="0" />
                                    <label for="add_option">&nbsp;&nbsp;{{ "To be determined by lab"|t }}</label>
                                {% endif %}
                            {% elseif customField.type === "number" %}
                                <input type="number" id="customField[{{ customField.id }}]" name="customField[{{ customField.id }}]" class="form-control numeric" min="0" value="" />
                                {% if customField.has_lab_check is 1 %}
                                    <input type="checkbox" name="customFieldLab[{{ customField.id }}]" class="field_lab" value="0" />
                                    <label for="add_option">&nbsp;&nbsp;{{ "To be determined by lab"|t }}</label>
                                {% endif %}
                            {% elseif customField.type === "select" %}
                                <select class="form-control" name="customField[{{ customField.id }}]">
                                    {% for option in customField.Options %}
                                        <option value="{{ option.value }}">{{ option.option }}</option>
                                    {% endfor %}
                                </select>
                                {% if customField.has_lab_check is 1 %}
                                    <input type="checkbox" name="customFieldLab[{{ customField.id }}]" class="field_lab" value="0" />
                                    <label for="add_option">&nbsp;&nbsp;{{ "To be determined by lab"|t }}</label>
                                {% endif %}
                            {% elseif customField.type === "checkbox" %}
                                <input type="hidden" name="customField[{{ customField.id }}][]" />
                                {% for option in customField.Options %}
                                    <div class="checkbox">
                                        <label><input type="checkbox"
                                                      name="customField[{{ customField.id }}][{{ option.id }}]"
                                                      value="{{ option.value }}">{{ option.option }}</label>
                                    </div>
                                {% endfor %}
                                {% if customField.has_lab_check is 1 %}
                                    <div class="checkbox">
                                        <label for="add_option">
                                            <input type="checkbox" name="customFieldLab[{{ customField.id }}]" class="field_lab" value="0" />
                                            {{ "To be determined by lab"|t }}</label>
                                    </div>
                                {% endif %}
                            {% elseif customField.type === "statement" %}
                                <input type="hidden" id="customField[{{ customField.id }}]" name="customField[{{ customField.id }}]" />
                            {% elseif customField.type === "textarea" %}
                                <textarea name="customField[{{ customField.id }}]" class="form-control tinymce-limited" style="height: 150px;"></textarea>
                            {% endif %}
                        </div>
                        {% endif %}
                    {% endfor %}
                    {% endif %}

                    {% if availableCustomFields['add'] is 1 %}
                    <hr/>
                    <p><strong>{{ "Additional information"|t }}</strong></p>
                    {% for customField in recipe.ParentRecipe.RecipeCustomField %}
                        {% if customField.custom_field_type is 'additional' %}
                        <div class="form-group">
                            {#{% if customField.type !== "statement" %}#}
                                {#<label>{{ customField.name }}</label>#}
                            {#{% else %}#}
                            {#{% endif %}#}
                            {% if customField.amount != 1 %}
                                <input type="text" value="{{ customField.amount }}" disabled="disabled" style="width: 30px;" />
                            {% endif %}
                            <label>{{ customField.name }}</label>

                            {% if customField.type === "text" %}
                                {{ text_field('customField['~customField.id~']', 'class': 'form-control') }}
                                {% if customField.has_lab_check is 1 %}
                                <input type="checkbox" name="customFieldLab[{{ customField.id }}]" class="field_lab" value="0" />
                                <label for="add_option">&nbsp;&nbsp;{{ "To be determined by lab"|t }}</label>
                                {% endif %}
                            {% elseif customField.type === "number" %}
                                <input type="number" id="customField[{{ customField.id }}]" name="customField[{{ customField.id }}]" class="form-control numeric" min="0" value="" />
                                {% if customField.has_lab_check is 1 %}
                                <input type="checkbox" name="customFieldLab[{{ customField.id }}]" class="field_lab" value="0" />
                                <label for="add_option">&nbsp;&nbsp;{{ "To be determined by lab"|t }}</label>
                                {% endif %}
                            {% elseif customField.type === "select" %}
                                <select class="form-control" name="customField[{{ customField.id }}]">
                                    {% for option in customField.Options %}
                                        <option value="{{ option.value }}">{{ option.option }}</option>
                                    {% endfor %}
                                </select>
                                {% if customField.has_lab_check is 1 %}
                                <input type="checkbox" name="customFieldLab[{{ customField.id }}]" class="field_lab" value="0" />
                                <label for="add_option">&nbsp;&nbsp;{{ "To be determined by lab"|t }}</label>
                                {% endif %}
                            {% elseif customField.type === "checkbox" %}
                                <input type="hidden" name="customField[{{ customField.id }}][]" />
                                {% for option in customField.Options %}
                                    <div class="checkbox">
                                        <label><input type="checkbox"
                                                      name="customField[{{ customField.id }}][{{ option.id }}]"
                                                      value="{{ option.value }}">{{ option.option }}</label>
                                    </div>
                                {% endfor %}
                                {% if customField.has_lab_check is 1 %}
                                <div class="checkbox">
                                    <label for="add_option">
                                        <input type="checkbox" name="customFieldLab[{{ customField.id }}]" class="field_lab" value="0" />
                                        {{ "To be determined by lab"|t }}</label>
                                </div>
                                {% endif %}
                            {% elseif customField.type === "statement" %}
                            <input type="hidden" id="customField[{{ customField.id }}]" name="customField[{{ customField.id }}]" />
                            {% elseif customField.type === "textarea" %}
                                <textarea name="customField[{{ customField.id }}]" class="form-control tinymce-limited" style="height: 150px;"></textarea>
                            {% endif %}
                        </div>
                        {% endif %}
                    {% endfor %}
                    {% endif %}
                    <hr/>
                </div>
                <div class="form-group">
                    <label>{{ 'Requested delivery date(s)'|t }}</label>
                    <span class="pull-right">
                        <a id="add-date" data-recipe="{{ recipe.id }}" data-counter="0" style="cursor: pointer;"><i class="pe-7s-plus" style="font-size: x-large;"></i></a>
                    </span>
                    <table id="deliveryDates" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>{{ "Phase"|t }}</th>
                            <th>{{ 'Requested delivery date'|t }}</th>
                            <th>{{ 'Prefered part of the day'|t }}</th>
                            <th>{{ "Actions"|t }}</th>
                        </tr>
                        </thead>
                        <tbody class="delivery-body"></tbody>
                    </table>
                </div>
                <div class="form-group">
                    <label>{{ 'Add attachment(s)'|t }}</label>
                    {{ file_field('files[]', 'class': 'form-control', 'multiple': 'multiple') }}
                </div>
                {#<div class="form-group">#}
                    {#{{ text_area('description', 'placeholder': 'Order notes or remarks...'|t, 'class': 'form-control', 'value': order.description) }}#}
                {#</div>#}
            </div>
        </div>

        <legend></legend>
        <div class="row">
            <div class="col-lg-12">
                <button type="submit" class="btn btn-primary pull-right {% if recipe.is_basic == 1 %}submit-allowed{% endif %}" data-type="{{ recipe.getPriceType() }}"><i
                            class="pe-7s-diskette"></i> {{ "Add to order"|t }}</button>
            </div>
        </div>

        <input type="hidden" name="price" id="recipefinalprice" value="{{ recipe.price }}" />

        <input type="hidden" name="{{ tokenKey }}" value="{{ token }}" />
    </form>

    {{ partial("modals/alert", ['id': 'price-info', 'title': 'Attention', 'content': 'This is a target price, the actual price will be calculation on production']) }}
    {{ partial("modals/addSingleFieldDate", ['id': 'add-modal', 'title': "Add delivery date"|t, "type": "new", "statuses_times": statuses_times, "statuses_av": statuses_av]) }}
    {{ partial("modals/editSingleFieldDate", ['id': 'edit-modal', 'title': "Edit delivery date"|t, "type": "edit", "statuses_times": statuses_times, "statuses_av": statuses_av]) }}


{% endblock %}
{% block scripts %}
    {{ super() }}
    <script>
        $(document).ready(function () {

            tinymce.init({
                selector: '.tinymce-limited',
                language_url: '/js/tinymce/langs/nl.js',
                menubar: false,
                statusbar: false,
                branding: false,
                height: 300,
            });

            onSubmitInit();

            $('.upper').on('click', function(){
                var box = $(this).siblings()[1];
                if($(box).val() == 1){
                    $(box).val(0);
                    $(this).removeClass("checked");
                }
                else {
                    $(box).val(1);
                    $(this).addClass("checked");
                }
            });

            $('.lower').on('click', function(){
                var box = $(this).siblings()[0];
                if($(box).val() == 1){
                    $(box).val(0);
                    $(this).removeClass("checked");
                }
                else {
                    $(box).val(1);
                    $(this).addClass("checked");
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

            function onSubmitInit() {
                $('button[type="submit"]').off('click');
                $('button[type="submit"]').on('click', function (e) {
                    e.preventDefault();
                    var priceType = $(this).data('type');
                    if (priceType === 'Composite') {
                        $('#price-info').modal('show');
                        $('#price-info').on('hidden.bs.modal', function () {
                            $('#orderForm').submit();
                        });
                    } else {
                        $('#orderForm').submit();
                    }
                }).removeAttr('disabled');
            }

            recalculatePrice();
            $('input, select').on('change', function() {
                if(!$(this).hasClass('isModal')){
                    recalculatePrice();
                }
            });

            function recalculatePrice() {
                //$('button[type="submit"]').off('click').attr('disabled', 'disabled');
                $.post("{{ url('dentist/order/calculateprice/' ~ recipe.code) }}", {
                    options: $('#orderForm').serialize()
                }, function(data){
                    $('.recipeprice').html(data);
                    $('#recipefinalprice').val(data);
                    onSubmitInit();
                },'json');
            }

            $('.submit-allowed').removeAttr('disabled');

            $('#add-date').on('click', function(){
                $('#deliveryDate').val(null);
                $('#deliveryText').val(null);
                $('#add-modal').modal('show');

            });

            $(document).on('click', '.edit-date', function(){

                var trEl = $(this).closest('tr');

                if (trEl.data('phase')) {
                    $('.phase-select-edit').val(trEl.data('phase')).trigger('change');
                    $('#deliveryTextEdit').val('').closest('.phase-other-area-edit').addClass('hidden');
                } else {
                    $('.phase-select-edit').val(0).trigger('change');
                    $('#deliveryTextEdit').val($(this).data('text')).closest('.phase-other-area-edit').removeClass('hidden');
                }

                $('.radio-part-of-day-edit').each(function(){
                    if ($(this).val() == trEl.find('.part-of-day').val()) {
                        $(this).attr('checked', 'checked');
                        $(this).prop('checked', true);
                    } else {
                        $(this).removeAttr('checked');
                        $(this).prop('checked', false);
                    }
                });

                $('#deliveryDateEdit').val($(this).attr('data-date'));
                $('#confirmButtonEdit').attr('data-counter', $(this).attr('data-counter'));
                $('#edit-modal').modal('show');

            });

            $(document).on('click', '.remove-date', function(){
                $('#row_'+$(this).attr('data-counter')).remove();
                $('#add-date').attr('data-counter', parseInt($(this).attr('data-counter')) - 1);
            });

            $(document).on('click', '#deliveryDateEdit, #deliveryDate', function(){
                $($($('.table-condensed').find('.disabled')).siblings()[5]).addClass('disabled');
            });

            $('#confirmButtonEdit').on('click', function(){

                var phase = $('.phase-select-edit').val();
                var text;
                var days = '';

                if (phase == 0) {
                    text = $('#deliveryTextEdit').val();
                } else {
                    var textRow = $('.phase-select-edit').find('option[value="'+phase+'"]');
                    text = textRow.data('name');
                    days = textRow.data('days');
                }

                var partOfDay = '';

                $('.radio-part-of-day-edit').each(function(){
                    if ($(this).is(':checked')) {
                        partOfDay = $(this).val();
                    }
                });

                $('#row_'+$(this).attr('data-counter')).data('days', days).data('phase',phase);

                $('#date_'+$(this).attr('data-counter')).html($('#deliveryDateEdit').val());
                $('#dateval_'+$(this).attr('data-counter')).val($('#deliveryDateEdit').val());

                $('#text_'+$(this).attr('data-counter')).html(text);
                $('#textval_'+$(this).attr('data-counter')).val(text);

                $('#partOfDay_'+$(this).attr('data-counter')).html(partOfDay);
                $('#partOfDayval_'+$(this).attr('data-counter')).val(partOfDay);

                $('#phaseval_'+$(this).attr('data-counter')).val(phase);
                $('#daysval_'+$(this).attr('data-counter')).val(days);

                $('#edit_'+$(this).attr('data-counter')).attr('data-date', $('#deliveryDateEdit').val());
                $('#edit_'+$(this).attr('data-counter')).attr('data-text', text);
                $('#edit-modal').modal('hide');
            });

            $('#confirmButton').on('click', function(){

                var phase = $('.phase-select').val();
                var text;
                var days = '';

                if (phase == 0) {
                    text = $('#deliveryText').val();
                } else {
                    var textRow = $('.phase-select').find('option[value="'+phase+'"]');
                    text = textRow.data('name');
                    days = textRow.data('days');
                }

                var partOfDay = '';

                $('.radio-part-of-day').each(function(){
                    if ($(this).is(':checked')) {
                        partOfDay = $(this).val();
                    }
                });

                var recipe_id = $('#add-date').attr('data-recipe');
                var date = $('#deliveryDate').val();
                var counter = parseInt($('#add-date').attr('data-counter')) + 1;
                var content = '<tr data-phase="'+phase+'" data-days="'+days+'" id="row_'+counter+'" class="delivery-row">' +
                    '<td id="text_'+counter+'">'+text+'</td>' +
                    '<td id="date_'+counter+'">'+date+'</td>' +
                    '<td id="partOfDay_'+counter+'">'+partOfDay+'</td>' +
                    '<td>' +
                    '<a id="edit_'+counter+'" class="btn btn-primary btn-sm edit-date" data-date="'+date+'" data-text="'+text+'" data-counter="'+counter+'"><i class="pe-7s-pen"></i>{{ "Edit"|t}}</a>'+
                    '<a class="btn btn-danger btn-sm remove-date" data-counter="'+counter+'"><i class="pe-7s-close-circle"></i> {{"Delete"|t}}</a> '+
                    '<input id="dateval_'+counter+'" type="hidden" value="'+date+'" name="delivery['+counter+'][date]" />'+
                    '<input id="textval_'+counter+'" type="hidden" value="'+text+'" name="delivery['+counter+'][text]" />'+
                    '<input id="partOfDayval_'+counter+'" class="part-of-day" type="hidden" value="'+partOfDay+'" name="delivery['+counter+'][part_of_day]" />'+
                    '<input id="daysval_'+counter+'" type="hidden" value="'+days+'" name="delivery['+counter+'][days]" />'+
                    '<input id="phaseval_'+counter+'" type="hidden" value="'+phase+'" name="delivery['+counter+'][phase]" />'+
                    '</td></tr>';
                $('.delivery-body').append(content);
                $('#add-date').attr('data-counter', counter);
                $('#add-modal').modal('hide');
            });

        });
    </script>
{% endblock %}
