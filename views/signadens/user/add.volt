{% extends "layouts/main.volt" %}
{% block title %} {{'Add user'|t}} {% endblock %}
{% block content %}

    <h3>{{ "Add new user"|t }}</h3>
    {{ form('signadens/user/add', 'method': 'post') }}

    <fieldset class="form-group">
        <legend>{{ "Identity"|t }}</legend>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{'First name'|t}}:</label>
                    {{ text_field('firstname', 'required': 'required', 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{'Last name'|t}}:</label>
                    {{ text_field('lastname', 'required': 'required', 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{'Role'|t}}:</label>
                    <select id="role_template_id" name="role_template_id" class="form-control" required="required">
                        <option orgType="none" selected="selected"></option>
                        {% for role in roles %}
                            <option orgType="{{ role.organisationType.getSlug() }}"  value="{{ role.id }}">{{ role.name }}</option>
                        {% endfor %}
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{'Phone'|t}}:</label>
                    {{ text_field('telephone', 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{'Email'|t}}:</label>
                    {{ text_field('email', 'required': 'required', 'class': 'form-control') }}
                </div>


                {#<div class="form-group">#}
                    {#<label>{{'Password'|t}}:</label>#}
                    {#{{ password_field('password', 'required': 'required', 'class': 'form-control') }}#}
                {#</div>#}

            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{'Organisation'|t}}:</label>
                    <select id="organisation_id" name="organisation_id" class="form-control" required="required">
                        <option orgType="none" selected="selected"></option>
                        {% for organisation in organisations %}
                            <option orgType="{{ organisation.OrganisationType.getSlug() }}"  value="{{ organisation.id }}">{{ organisation.name }}</option>
                        {% endfor %}
                    </select>
                </div>
                <div class="form-group">
                    <label>{{'Active'|t}}:</label>
                    {{ select('active', active, 'required': 'required', 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label for="">&nbsp;</label>
                    <div class="row">
                        <div class="col-lg-12">
                            {{ text_field('hidden', 'class': 'form-control', 'style': 'visibility: hidden;') }}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="">&nbsp;</label>
                    <div class="row">
                        <div class="col-lg-12">
                            <button type="submit" class="btn btn-primary pull-right"><i class="pe-7s-add-user"></i> {{ "Add"|t }}</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </fieldset>
    {{ end_form() }}

{% endblock %}