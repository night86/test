{% extends "layouts/main.volt" %}
{% block title %} {{ "Edit user"|t }} {% endblock %}
{% block content %}
    <h3><a href="{{ url("dentist/user/") }}"><i class="pe-7s-back"></i></a><span> {{ user.getFirstname() }} {{ user.getLastname() }}</span></h3>
    {{ form('dentist/user/edit/' ~ user.getId(), 'method': 'post') }}

    <fieldset class="form-group">
        <legend>{{ "Identity"|t }}</legend>
        {{ hidden_field('id', 'value': user.getId()) }}
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ "First name"|t }}:</label>
                    {{ text_field('firstname', 'required': 'required', 'value': user.getFirstname(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Last name"|t }}:</label>
                    {{ text_field('lastname', 'required': 'required', 'value': user.getLastname(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Role"|t }}:</label>
                    {{ select('role_template_id', roles, 'using': ['id', 'name'], 'required': 'required', 'value': user.getRoleTemplateId(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{'Organisation'|t}}:</label>
                    {{ text_field('organisation', 'required': 'required', 'value': organisation.name, 'disabled':'disabled', 'class': 'form-control') }}
                </div>
                {% if count(locations) > 1 %}
                <div class="form-group">
                    <label>{{'Location(s)'|t}}:</label><br />
                    {% for loc in locations %}
                    <input id="location_{{ loc.id }}" type="hidden" name="location[{{ loc.id }}]" value="{% if in_array(loc.id, user.getLocations()) %}1{% else %}0{% endif %}" />
                    <input id="box_{{ loc.id }}" data-id="{{ loc.id }}" data-name="{{ loc.name }}" class="location-box" type="checkbox" {% if in_array(loc.id, user.getLocations()) %}checked="checked"{% endif %} />&nbsp;{{ loc.name }}<br />
                    {% endfor %}
                </div>
                <div class="form-group">
                    <label>{{'Main location'|t}}:</label><br />
                    <select id="main_location" name="main_location_id" class="form-control">
                        {% for loc in locations %}
                            {% if in_array(loc.id, user.getLocations()) %}
                            <option value="{{ loc.id }}" {% if loc.id == user.main_location_id %}selected="selected"{% endif %}>{{ loc.name }}</option>
                            {% endif %}
                        {% endfor %}
                    </select>
                </div>
                {% endif %}
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ "Phone"|t }}:</label>
                    {{ text_field('telephone', 'value': user.getTelephone(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Email"|t }}:</label>
                    {{ text_field('email', 'required': 'required', 'value': user.getEmail(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Password"|t }}:</label>
                    {{ password_field('password', 'value': '', 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Active"|t }}:</label>
                    {{ select('active', active, 'required': 'required', 'value': user.getActive(), 'class': 'form-control') }}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group pull-right">
                    <label>&nbsp;</label>
                    {{ submit_button('Save'|t, 'class': 'btn btn-primary pull-right') }}
                </div>
            </div>
        </div>


    </fieldset>
    {{ end_form() }}

    <form>
        <fieldset class="form-group">
            <legend>{{ "Access log"|t }}</legend>
            <table class="simple-datatable table table-striped">
                <thead>
                <th class="sortbydate">{{ "Date"|t }}</th>
                <th>{{ "Time"|t }}</th>
                <th>{{ "User"|t }}</th>
                <th>{{ "Action"|t }}</th>
                </thead>
                <tbody>
                <tr>
                    <td>test</td>
                    <td>test</td>
                    <td>test</td>
                    <td>test</td>
                </tr>
                </tbody>
            </table>
        </fieldset>
    </form>

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        $(function () {
            $('.location-box').on('change', function(){
               if($(this).is(':checked')){
                   $('#main_location').append('<option value="'+$(this).attr('data-id')+'">'+$(this).attr('data-name')+'</option>')
                   $('#location_'+$(this).attr('data-id')).val(1);
               }
               else {
                   $('#main_location option[value='+$(this).attr('data-id')+']').remove();
                   $('#location_'+$(this).attr('data-id')).val(0);
               }
            });
        });
    </script>
{% endblock %}