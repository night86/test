<tr class="customfield-row">
    <td><input type="number" min="0" name="{{ elementType }}[amount][{{ fieldCnt }}]" class="form-control" value="" /></td>
    <td>
        {{ text_field(elementType ~ '[name][' ~ fieldCnt ~ ']', 'class': 'form-control', 'value': fieldName) }}
    </td>
    <td>
        <select name="{{ elementType }}[type][{{ fieldCnt }}]" class="form-control" disabled>
            {% for fieldtypecurr, fieldtypename in customFieldTypes %}
                <option {% if fieldType is fieldtypecurr %}selected="selected"{% endif %}
                        value="{{ fieldtypecurr }}">{{ fieldtypename }}</option>
            {% endfor %}
        </select>
    </td>
    <td>


            <div class="form-group">
                <div class="clearfix"></div>
            </div>
            <div class="form-group">

                {% if fieldNumberParams and fieldType is 'number' %}
                    <select name="params[{{ fieldCnt }}][numberprice]" class="form-control select2-input customtariff">
                        <option value="0">-</option>
                        {% for tariff in tariffs %}
                            <option {% if tariff.id is fieldNumberParams['numberprice'] %}selected="selected"{% endif %} value="{{ tariff.id }}">{{ tariff.code }}{% if tariff.description %} - {{ tariff.description }}{% endif %}</option>
                        {% endfor %}
                    </select>
                    <br />
                    <div><label><input {% if fieldNumberParams['numberpricechoose'] is 1 %}checked="checked"{% endif %} type="radio" name="params[{{ fieldCnt }}][numberpricechoose]" value="1" /> {{ singlePriceLabel }}</label></div>
                    <div><label><input {% if fieldNumberParams['numberpricechoose'] is 2 %}checked="checked"{% endif %} type="radio" name="params[{{ fieldCnt }}][numberpricechoose]" value="2" /> {{ itemPriceLabel }}</label></div>
                {% endif %}

                {% if fieldNumberParams and fieldType is 'statement' %}
                    <select name="params[{{ fieldCnt }}][statement]" class="form-control select2-input customtariff">
                        <option value="0">{{ customPriceLabel }}</option>
                        {% for tariff in tariffs %}
                            <option {% if tariff.id is fieldNumberParams['statement'] %}selected="selected"{% endif %} value="{{ fieldNumberParams['statement'] }}">{{ tariff.code }}{% if tariff.description %} - {{ tariff.description }}{% endif %}</option>
                        {% endfor %}
                    </select>
                {% endif %}

                {% if fieldNumberParams and fieldType is 'textarea' %}
                    <textarea name="params[{{ fieldCnt }}][textarea]" class="form-control"></textarea>
                {% endif %}

                <div class="clearfix"></div>
            </div>

        {% for optionFieldCnt,options in fieldOptions %}
            <div class="form-group">
                <div class="clearfix"></div>
            </div>

            {% for k,option in options %}

                <div class="form-group">
                    <div class="col-md-5">
                        {{ text_field(elementType ~ '[options][' ~ optionFieldCnt ~ '][' ~ k ~ ']', 'class': 'form-control', 'value': option['name']) }}
                    </div>

                    <div class="col-md-5">
                        <select name="field_option[{{ optionFieldCnt }}][{{ k }}][selecttariff]" class="form-control select2-input customtariff">
                            <option value="0">{{ customPriceLabel }}</option>
                            {% for tariff in tariffs %}
                                <option {% if tariff.id is option['selecttariff'] %}selected="selected"{% endif %} value="{{ tariff.id }}">{{ tariff.code }}{% if tariff.description %} - {{ tariff.description }}{% endif %}</option>
                            {% endfor %}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="field_option[{{ optionFieldCnt }}][{{ k }}][numberselectprice]" value="{{ option['numberselectprice'] }}" class="form-control numeric" />
                    </div>
                    <div class="clearfix"></div>
                </div>

            {% endfor %}
        {% endfor %}
        {% if fieldNumberParams and fieldType is not 'statement' and fieldType is not 'textarea' %}
        <br />
        <input type="checkbox" name="params[{{ fieldCnt }}]field_lab" class="field_lab" value="{{ fieldLab }}" />
        <label for="add_option">&nbsp;&nbsp;{{ label_lab }}</label>
        {% endif %}
    </td>
    <td><a href="javascript:;" class="btn btn-danger btn-sm customfield-remove-row"><i
                    class="pe-7s-close-circle"></i></a></td>
</tr>