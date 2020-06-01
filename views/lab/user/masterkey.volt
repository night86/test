{% extends "layouts/main.volt" %}
{% block title %} {{ 'Users'|t }} {% endblock %}
{% block bodyclass %}login-page{% endblock %}
{% block content %}
    <div id="login-cnt" class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="row">
                    <div class="col-md-12 text-center logo-cnt">
                        <img src="/img/signadens_logo_small_inv.png" alt="Signadens Logo">
                    </div>
                </div>
            </div>
            <div id="masterkey-cnt" class="row">
                <div class="col-sm-8 col-sm-offset-2">
                    <legend>{{ "Click on your name to proceed"|t }}</legend>
                    <ul id="masterkey-list">
                        {% for user in users %}
                            {% if 'beheer' not in user.roleTemplate.name and 'login' not in user.roleTemplate.name %}
                                <li>
                                    <a href="{{ url("lab/user/loginbymasterkey/" ~ user.getId()) }}">
                                        <i class="pe-7s-user"></i> {{ user.getFirstName() }} {{ user.getLastName() }}
                                    </a>
                                </li>
                            {% endif %}
                        {% endfor %}
                    </ul>
                </div>
            </div>
        </div>
    </div>

{% endblock %}