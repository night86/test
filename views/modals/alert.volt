<div id="{{ id }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ title|t }}</h4>
            </div>
            <div class="modal-body">
                <p>{{content|t }}{% if existingUser is defined %} <span id="kvk_alert"></span>.{% endif %}</p>
                {% if content2 is defined %}
                <p>{{content2|t }}</p>
                {% endif %}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{% if closeButton is defined %}{{ closeButton }}{% else %}{{ "ok"|t }}{% endif %}</button>
            </div>
        </div>
    </div>
</div>