<div id="{{ id }}" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ title|t }}</h4>
            </div>
            <div class="modal-body">
                <form id="singleFieldForm">
                    <div class="form-group">
                        <label for="manufacturer">{{ "Manufacturer"|t }}:</label>
                        <span id="man_error" style="display: none; width: 60%; float: right; position: absolute; margin: -13px 115px; color: red;">{{ "This combination of manufacturer and product category already exists and therefore cannot be saved."|t }}</span>
                        <select id="manufacturer_id" name="manufacturer_id" class="select2-input">
                            <option></option>
                            {% for m in manufacturers %}
                                <option value="{{ m.id }}">{{ m.name }}</option>
                            {% endfor %}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="product_category">{{ "Product category"|t }}:</label>
                        <select id="product_category" name="product_category" class="select2-input">
                            <option></option>
                            {% for pc in productCategories %}
                                <option value="{{ pc['id'] }}">{% if pc['cat_parent_name'] %}{{ pc['cat_parent_name'] }} - {% endif %}{% if pc['sub_parent_name'] %}{{ pc['sub_parent_name'] }} - {% endif %}{{ pc['name'] }}</option>
                            {% endfor %}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="range_from">{{ "From"|t }}:</label>
                        <input id="range_from" name="range_from" type="number" class="form-control" min="0" />
                    </div>
                    <div class="form-group">
                        <label for="range_to">{{ "To"|t }}:</label>
                        <input id="range_to" name="range_to" type="number" class="form-control" min="0" />
                        <span id="ran_error" style="display: none; color: red;">{{ "Part of this range is already in use and therefore can not be saved."|t }}</span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="confirmButton" type="button"
                        class="btn btn-primary confirm-button">{% if confirmButton is defined %}{{ confirmButton|t }}{% else %}{{ "Confirm"|t }}{% endif %}</button>
                <button type="button" class="btn btn-default cancel-button"
                        data-dismiss="modal">{{ "Cancel"|t }}</button>
            </div>
        </div>
    </div>
</div>