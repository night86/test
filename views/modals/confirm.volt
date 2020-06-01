<div id="{{ id }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ title|t }}</h4>
            </div>
            <div class="modal-body">
                {# use skip translation content for html contents #}
                {% if skiptranslation is defined %}
                    {{ content }}
                {% else %}
                    {{ content|t }}
                {% endif %}

                {% if existingUser is defined and existingUser is true %}
                    <p>&nbsp;</p>
                    <p id="den_name"></p>
                    <p id="den_street"></p>
                    <p id="den_postal"></p>
                    <p id="den_city"></p>
                {% endif %}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary {{ additionalClass }}" >
                    {% if primarybutton is defined %}
                        {{ primarybutton|t }}
                    {% else %}
                        {{ "Save"|t }}
                    {% endif %}
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ "Cancel"|t }}</button>
            </div>
        </div>
    </div>
</div>