{% extends "layouts/main.volt" %}
{% block title %} {{'Edit user'|t}} {% endblock %}
{% block content %}

    <h3><a href="{{ url("dentist/group_dentist/") }}"><i class="pe-7s-back"></i></a> {{ user.getFirstname() }} {{ user.getLastname() }}</h3>
    {{ form('dentist/group_dentist/edit/' ~ user.getId(), 'method': 'post') }}

    <fieldset class="form-group">
        <legend>{{ "Identity"|t }}</legend>
        {{ hidden_field('id', 'value': user.getId()) }}
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "First name"|t }}:</label>
                    {{ text_field('firstname', 'required': 'required', 'value': user.getFirstname(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Last name"|t }}:</label>
                    {{ text_field('lastname', 'required': 'required', 'value': user.getLastname(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Email"|t }}:</label>
                    {{ text_field('email', 'required': 'required', 'value': user.getEmail(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{'Organisation'|t}}:</label>
                    {{ text_field('organisation', 'required': 'required', 'value': organisation, 'disabled':'disabled', 'class': 'form-control') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Address"|t }}:</label>
                    {{ text_field('address', 'value': user.getAddress(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Zip code"|t }}:</label>
                    {{ text_field('zip_code', 'value': user.getZipCode(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Phone"|t }}:</label>
                    {{ text_field('telephone', 'value': user.getTelephone(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Active"|t }}:</label>
                    {{ select('active', active, 'required': 'required', 'value': user.getActive(), 'class': 'form-control') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "City"|t }}:</label>
                    {{ text_field('city', 'value': user.getCity(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Country"|t }}:</label>
                    {{ text_field('country', 'value': user.getCountry(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Password"|t }}:</label>
                    {{ password_field('password', 'value': user.getPassword(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label for="">&nbsp;</label>
                    <div class="row">
                        <div class="col-lg-12">
                            <button type="submit" class="btn btn-primary pull-right"><i class="pe-7s-diskette"></i> {{ "Save"|t }}</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>


    </fieldset>
    {{ end_form() }}

{% endblock %}