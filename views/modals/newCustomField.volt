<div id="customFieldModal" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ "Create new custom field"|t }}</h4>
            </div>
            <div class="modal-body">
                <form id="newCustomFieldForm">
                    <p>{{ "Enter the label, select type and options"|t }}</p>

                    <div class="form-group">
                        <label for="email">{{ "Field label"|t }}:</label>
                        {{ text_field("field_label", "class" : "form-control", "required":"required") }}
                    </div>

                    <div class="form-group">
                        <label for="email">{{ "Field type"|t }}:</label>
                        <select name="field_type" id="customFieldFieldType" class="form-control">
                            {% for fieldtype, fieldtypename in recipe.getCustomFieldTypes() %}
                                <option value="{{ fieldtype }}">{{ fieldtypename|t }}</option>
                            {% endfor %}
                        </select>
                    </div>

                    <div id="lab_choice" class="form-group">
                        <input type="checkbox" name="field_lab" id="field_lab_modal" value="0" />
                        <label for="add_option">&nbsp;&nbsp;{{ "To be determined by lab"|t }}</label>
                    </div>

                    <div class="form-group setnumberprice">
                        <div class="flexiblefields">
                            <div class="col-md-6">
                                <div><label>{{ radio_field('params[numberpricechoose]', 'checked': 'checked', 'value': '1') }} {{ "Single additional price"|t }}</label></div>
                                <div><label>{{ radio_field('params[numberpricechoose]', 'value': '2') }} {{ "Additional price per item"|t }}</label></div>
                            </div>
                            <div class="col-md-6">
                                {# {{ numeric_field("params[numberprice]", "class" : "form-control") }} #}
                                <select id="numberPrice" name="params[numberprice]" class="form-control select2-input">
                                    <option value="0">{{ "Additional price"|t }}</option>
                                    {% for tariff in tariffs %}
                                        <option value="{{ tariff.id }}">{{ tariff.code }}{% if tariff.description %} - {{ tariff.description }}{% endif %}</option>
                                    {% endfor %}
                                </select>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="form-group statement">
                        <div class="flexiblefields">
                            <div class="col-md-12">
                                <select name="params[statement]" class="form-control select2-input customtariff">
                                    <option value="0">{{ "Custom price"|t }}</option>
                                    {% for tariff in tariffs %}
                                        <option value="{{ tariff.id }}">{{ tariff.code }}{% if tariff.description %} - {{ tariff.description }}{% endif %}</option>
                                    {% endfor %}
                                </select>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="form-group">
                        <div class="options-content" id="optionsContentInputs">
                            <label>{{ "Field options"|t }}:</label>
                        </div>

                        <div class="clearfix"></div>
                        <hr />
                        <a href="#" class="btn btn-primary options-content" id="addNewOption"><i class="pe-7s-plus"></i>{{ "Add option"|t }}</a>
                    </div>

                </form>

                <div class="hidden newoptiontemplate">

                    <div class="row-field-option">
                        <hr />
                        <div class="form-group">
                            <div class="col-md-10">
                                <input type="text" class="form-control input-field-option" name="field_option[counterCustomFiled][counterCustomOption][name]">
                            </div>
                            <div class="col-md-2">
                                <a href="javascript:;" class="btn btn-danger btn-sm field-option-remove-row"><i class="pe-7s-close-circle"></i></a>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                        <div class="form-group customparameters">
                            <div class="col-md-7">
                                <select id="cusOpt_counterCustomOption" name="field_option[counterCustomFiled][counterCustomOption][selecttariff]" class="form-control select2-input customtariff">
                                    <option value="0">{{ "Custom price"|t }}</option>
                                    {% for tariff in tariffs %}
                                        <option value="{{ tariff.id }}">{{ tariff.code }}{% if tariff.description %} - {{ tariff.description }}{% endif %}</option>
                                    {% endfor %}
                                </select>
                            </div>
                            <div class="col-md-3 customprice">
                                {{ numeric_field("field_option[counterCustomFiled][counterCustomOption][numberselectprice]", "class" : "form-control") }}
                            </div>
                        </div>

                        <div class="clearfix"></div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary add-custom-fields" data-type="{{ data_type }}">{{ "Add"|t }}</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ "Cancel"|t }}</button>
            </div>
        </div>
    </div>
</div>