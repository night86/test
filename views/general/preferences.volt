{% extends "layouts/main.volt" %}
{% block title %} {{ "Preferences"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Preferences"|t }}</h3>

    <div class="row">
        <div class="col-md-12 col-sm-12">
            <ul class="nav nav-tabs basic-tabs" data-tabs="tabs">
                <li class="active"><a href="#organisation" aria-controls="organisation" data-toggle="tab">{{ "Organisation"|t }}</a></li>
                <li><a href="#file-share" aria-controls="file-share" data-toggle="tab">{{ "Files share"|t }}</a></li>
            </ul>

            <div class="tab-content">
                <div class="padding-15 tab-pane fade in active" id="organisation">
                    <p>Whoops, looks like this content is empty.</p>
                </div>
                <div class="padding-15 tab-pane fade" id="file-share">
                    {{ form('general/preferences/', 'method': 'post') }}

                    <fieldset class="form-group">
                        <legend>{{ "Allow user files share"|t }}</legend>
                        <div class="row">
                            {% for userFile in userFiles %}
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>
                                            {% if userFile.getAllow() == 'Yes' %}
                                                {{ check_field('userFile['~userFile.getId()~']', 'value': 1, 'checked': 'checked', 'class': 'basic-switcher') }}
                                            {% else %}
                                                {{ check_field('userFile['~userFile.getId()~']', 'value': 1, 'class': 'basic-switcher') }}
                                            {% endif %}
                                            {{ userFile.FromUser.getFullname() }}
                                        </label>
                                    </div>
                                </div>
                            {% endfor %}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    {{ submit_button('Save', 'class': 'btn btn-primary pull-right') }}
                                </div>
                            </div>
                        </div>


                    </fieldset>
                    {{ end_form() }}
                </div>
            </div>

        </div>
    </div>

{% endblock %}