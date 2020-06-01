<div id="cartModal{{ idVariant }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ title|t }}</h4>
            </div>

            <div class="modal-body">
                {% if idVariant  is '-already' %}
                    <p>{{ 'This product was already added to the orderlist by' |t }}
                        <span id="product-user-name{{ idVariant }}"></span> || <span
                                id="product-user-departament{{ idVariant }}"></span>
                        {{ 'Do you still want to add this to the order list for a specific project?'|t }}</p>
                    <div class="form-group">
                        <label>{{ "Project No."|t }}</label>

                        <input type="text" name="project-no{{ idVariant }}" class="form-control"
                               id="project-no{{ idVariant }}">
                        <label>{{ "This product is for general stock."|t }}</label>
                        <input type="checkbox" id="modal-unblock-checkbox{{ idVariant }}">
                        <div id="project-must{{ idVariant }}"
                             style="color:red;display:none;">{{ "Please provide project number"|t }} </div>
                    </div>

                {% else %}

                    {{ 'Is this a general stock or for a specific project? If you enter a project number it will be saved to this product for tho order'|t }}
                    <div class="form-group">
                        <label>{{ "Project No."|t }}</label>
                        <input type="text" name="project-no{{ idVariant }}" class="form-control"
                               id="project-no{{ idVariant }}">
                    </div>
                {% endif %}
            </div>
            <div class="modal-footer">
                {% if idVariant  is '' %}
                    {% set buttontext = "Add to cart"|t %}
                {% else %}
                    {% set buttontext = "Yes"|t %}
                {% endif %}
                <button type="button" class="btn btn-primary" data-dismiss="modal"
                        id="add-project-no{{ idVariant }}">{{ buttontext }}</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ "Cancel"|t }}</button>
            </div>
        </div>
    </div>
</div>