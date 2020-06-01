{% extends "layouts/main.volt" %}
{% block title %} {{ "Lab"|t }} {% endblock %}
{% block content %}
    <h3>{{ "Dashboard"|t }}</h3><br />
    
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div style="display: block;" class="loader">
                    <div class="loader-inner box1"></div>
                    <div class="loader-inner box2"></div>
                    <div class="loader-inner box3"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row dashboardArea">

    </div>
    {#{% if 'beheer' in currentUser.roleTemplate.name|lower %}
    {{ partial("lab/index/_indexAdmin") }}
    {% else %}
    {{ partial("lab/index/_indexEmployee") }}
    {% endif %}#}

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        $(document).ready(function(){
            dashboard.init();
        });
    </script>
{% endblock %}