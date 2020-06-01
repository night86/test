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
                            {{ form('auth/login', 'method': 'post') }}

                            <div class="form-group">
                                <label for="email">{{ "Username/Email"|t }}</label>
                                {{ text_field("email", "class" : "form-control") }}
                            </div>
                            <div class="form-group">
                                <label for="password">{{ "Password"|t }}</label>
                                {{ password_field('password', 'class': 'form-control') }}
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="checkbox">
                                        <label><input type="checkbox" id="remember" name="remember"
                                                      value="1"> {{ "Remember me"|t }}</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    {#{{ submit_button('Login', 'class': 'btn btn-primary pull-right btn-lg') }}#}
                                    <button type="submit" class="btn btn-primary pull-right btn-lg">{{ "Login"|t }} <i
                                                class="pe-7s-door-lock"></i></button>
                                </div>
                            </div>

                            </form>

                            <div class="text-right">
                                <a href="{{ url("auth/forgetpassword") }}">{{ "Forget password"|t }}</a></br>
                                <a href="{{ url("support") }}">{{ "Support"|t }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

{% endblock %}