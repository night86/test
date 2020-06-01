{% extends "layouts/main.volt" %}
{% block title %} {{'Login'|t}} {% endblock %}
{% block bodyclass %}login-page{% endblock %}
{% block content %}
    {#{% for log in thisUserLogs %}#}
    {#{{ dump(log) }}#}
    {#{% endfor %}#}
    <div id="login-cnt" class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="row">
                    <div class="col-md-12 text-center logo-cnt">
                        <img src="/img/signadens_logo_small_inv.png" alt="Signadens Logo">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-8 col-sm-offset-2">
                        <div class="row login-box">
                            <h1>{{ "Login"|t }}</h1>
                            {{ form('auth/forgetpassword', 'method': 'post') }}

                            <div class="form-group">
                                <label for="email">{{ "Request new password"|t }}</label>
                                {{ text_field("email", "class" : "form-control", "placeholder": "Your email address"|t, "required":"required") }}
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-md-offset-6">
                                    <button type="submit" class="btn btn-primary pull-right btn-lg">{{ "Send instructions"|t }} <i
                                                class="pe-7s-mail"></i></button>
                                </div>
                            </div>

                            </form>

                            <div class="text-right">
                                <a href="{{ url("auth/login") }}">{{ "Login"|t }}</a></br>
                                <a href="{{ url("support") }}">{{ "Support"|t }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

{% endblock %}