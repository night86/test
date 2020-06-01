{% extends "layouts/main.volt" %}
{% block title %} {{ "Edit organisation"|t }} {{ organisation.getName() }} {% endblock %}
{% block content %}

    <h3>{{ "Edit organisation"|t }}</h3>

    {{ form('supplier/user/organisation/' ~ organisation.getId(), 'method': 'post', 'enctype': 'multipart/form-data') }}


    <fieldset class="form-group">

        <legend><a href="{{ url("supplier/user/") }}"><i class="pe-7s-back"></i></a> {{ organisation.getName() }}</legend>

        <div class="row">

            <div class="col-md-4">
                <div class="form-group">
                    <supplierel>{{ "Name"|t }}:</supplierel>
                    {{ text_field('organisation[name]', 'disabled': 'disabled', 'class': 'form-control', 'value': organisation.getName() ) }}
                </div>
                <div class="form-group">
                    <supplierel>{{ "Street and number"|t }}:</supplierel>
                    {{ text_field('organisation[address]', 'disabled': 'disabled', 'class': 'form-control', 'value': organisation.getAddress() ) }}
                </div>

            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <supplierel>{{ "Zipcode"|t }}:</supplierel>
                    {{ text_field('organisation[zipcode]', 'disabled': 'disabled', 'class': 'form-control', 'value': organisation.getZipcode() ) }}
                </div>

                <div class="form-group">
                    <supplierel>{{ "City"|t }}:</supplierel>
                    {{ text_field('organisation[city]', 'disabled': 'disabled', 'class': 'form-control', 'value': organisation.getCity() ) }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <supplierel>{{ "Phone"|t }}:</supplierel>
                    {{ text_field('organisation[telephone]', 'disabled': 'disabled', 'class': 'form-control', 'value': organisation.getTelephone() ) }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <supplierel for="name">{{ 'Image'|t }}</supplierel>
                    <input type="file" name="logo" class="form-control" required>
                </div>
                {% if organisation.logo is not null %}
                    <div class="form-group">
                        <a href="{{ url("supplier/user/deleteorganisationimage/")~organisation.getId() }}" class="btn btn-danger btn-block"><i class="pe-7s-diskette"></i> {{ "Delete image"|t }}</a>
                    </div>
                {% endif %}
            </div>
            {% if organisation.logo is not null %}
                <div class="col-md-4">
                    <div class="form-group">
                        <img src="{{ image('organisation', organisation.logo) }}" width="300"/>
                    </div>
                </div>
            {% endif %}
            <div class="col-md-12">
                {#{{ submit_button('Save', 'class': 'btn btn-primary pull-right') }}#}
                <button type="submit" class="btn btn-primary pull-right"><i class="pe-7s-diskette"></i> {{ "Save"|t }}
                </button>
            </div>

        </div>
    </fieldset>

    {{ end_form() }}


{% endblock %}