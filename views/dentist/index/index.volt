{% extends "layouts/main.volt" %}
{% block title %} {{'Dentist'|t}} {% endblock %}
{% block content %}

    <div class="row">
        <div class="col-md-8">
            <span class="pull-right" style="margin-top: 15px;"><a href="{{ url("dentist/order/create") }}" class="btn-primary btn"><i class="pe-7s-plus"></i> {{ "New order"|t }}</a></span>
            <h3>&nbsp;</h3>
            <div id="calendar" style="display: block"></div>
        </div>
        <div class="col-md-4">
            {% if labLogo is defined %}
                {% for logo in labLogo %}
                    <img class="img-responsive pull-right" src="{{ image('organisation', logo) }}" alt="Logo" style="max-width: 300px; max-height: 150px; margin: 10px 0 0 0;"/>
                {% endfor %}
            {% endif %}
            <div id="sideEventsList" style="clear: both;">
                <h3 class="side-title"></h3>
                <div class="side-content"></div>
            </div>
        </div>
    </div>
    <style>
        .fc-day {
            cursor: pointer !important;
        }
        .fc-day:hover, .event-list-element:hover{
            background-color: #EEE;
        }
        .fc-today {
            font-weight: bold;
        }

    </style>
{% endblock %}
{% block scripts %}
    {{ super() }}
    <script>
        $(function(){
            calendar.init("{{ url('dentist/index/ajaxcalendar') }}");
        });

        $(document).on('click', '.event-list-element', function(){
            console.log($(this));
            var zar = $($(this).children()[3]).children()[0];
            location.href = zar.href;
        });

        var boxDay = null;

        $(document).on('click', '.fc-day', function(){

            if(boxDay != null){
                if(boxDay.hasClass('fc-today')){
                    boxDay.css('background-color', '#fcf8e3');
                }
                else {
                    boxDay.css('background-color', '#FFF');
                }
            }
            $(this).css('background-color', '#EEE');
            boxDay = $(this);
        });
    </script>
{% endblock %}