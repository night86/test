<div id="{{ id }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ title|t }}</h4>
            </div>
            <div class="modal-body">
                <form id="singleFieldForm">
                    <div class="form-group">
                        <label for="name">{{ "Phase"|t }}:</label>
                        <select class="form-control isModal phase-select" required="required">
                            {% for status in json_decode(recipeBase.statuses) %}
                                <option data-name="{{ status['name'] }}" data-days="{% if isset(statuses_times[status['id']]) %}{{ statuses_times[status['id']].getDays() }}{% endif %}" value="{{ status['id'] }}">{{ status['name'] }}{% if isset(statuses_times[status['id']]) %} - Minimale productietijd: {{ statuses_times[status['id']].getDays() }} werkdag(en){% endif %}</option>
                            {% endfor %}
                            <option selected="selected" value="0">{{ "Other"|t }}</option>
                        </select>
                    </div>
                    <div class="form-group phase-other-area">
                        <label for="name">{{ "Other"|t }}:</label>
                        {{ text_field("deliveryText", "class" : "form-control isModal") }}
                    </div>
                    <div class="form-group">
                        <label for="name">{{ "Requested delivery date"|t }}:</label>
                        {{ text_field("deliveryDate", "class" : "form-control isModal", "required" : "required") }}
                    </div>
                    {#<div class="form-group">#}
                        {#<label for="name">{{ "Description of delivery"|t }}:</label>#}
                        {#{{ text_field("deliveryText", "class" : "form-control isModal") }}#}
                    {#</div>#}
                    <div class="form-group">
                        <label for="name">{{ "Prefered part of the day (no guarantee)"|t }}:</label>
                        <div>
                            <input id="part-of-day-morning" name="part-of-day" type="radio" class="radio-part-of-day" value="{{ "morning"|t }}" />
                            <label for="part-of-day-morning"> {{ "morning"|t }}</label>
                        </div>
                        <div>
                            <input id="part-of-day-afternoon" name="part-of-day" type="radio" class="radio-part-of-day" value="{{ "afternoon"|t }}" />
                            <label for="part-of-day-afternoon"> {{ "afternoon"|t }}</label>
                        </div>
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
<script>
    $(document).ready(function(){
        $('#deliveryDate').datepicker({
            startDate: new Date(),
            format: 'dd-mm-yyyy',
            "autoclose": true,
            language: 'nl',
            daysOfWeekDisabled: [0,6]
        });
        $(document).off('change', '.phase-select');
        $(document).on('change', '.phase-select', function() {
            if ($(this).val() == 0) {
                $('.phase-other-area').removeClass('hidden');
            } else {
                $('.phase-other-area').addClass('hidden');
            }
        });
    });
</script>