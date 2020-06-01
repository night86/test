<div id="{{ id }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ title|t }}</h4>
            </div>
            <div class="modal-body">
                <p>{{ content|t }}</p>
                <div id="singleFieldForm">
                    <div class="form-group">
                        <label for="name">{{ "Available recipes"|t }}:</label>
                    </div>
                    <div class="form-group">
                        <select id="newRecipe_{{ recipeId }}" class="select2-input">
                            {% for recipe in availableRecipes %}
                                <option value="{{ recipe.id }}">{{ recipe.name }}</option>
                            {% endfor %}
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-primary confirm-button" data-id="{{ recipeId }}">{% if confirmButton is defined %}{{ confirmButton|t }}{% else %}{{ "Confirm"|t }}{% endif %}</button>
                <button type="button" class="btn btn-default cancel-button"
                        data-dismiss="modal">{{ "Cancel"|t }}</button>
            </div>
        </div>
    </div>
</div>