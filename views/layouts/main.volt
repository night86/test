<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    {#<link rel="icon" href="/img/favicon.ico">#}
    <link rel="shortcut icon" type="image/png" href="/img/favicon.png"/>
    <title>
        {% if getServerName() == 'test. ' %}(Test){% elseif getServerName() != 'acc. ' and getServerName() != 'mijn. ' and getServerName() != 'test. ' %}(Dev){% endif %}
        Signadens - {% block title %}{% endblock %}
    </title>

    {% block styles %}

        {% if checkDev() %}
            <link rel="stylesheet/less" type="text/css" href="/less/main.less"/>
        {% else %}
            {{ assets.outputCss() }}
        {% endif %}

    {% endblock %}


    {% if checkDev() %}
        {{ assets.outputJs('dev') }}
    {% endif %}
    {{ assets.outputJs('header') }}

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="{% block bodyclass %}{% endblock %}">

{% set actionname = router.getActionName() %}
{% set controllername = router.getControllerName() %}

{% if isHeader is defined and isHeader is 'loginout' %}
    <div id="wrapper" class="nav-hidden">
{% elseif isHeader is not defined or isHeader is not false %}
    <div id="wrapper">
{% else %}
    <div id="wrapper" class="nav-hidden">
{% endif %}
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <li class="sidebar-brand">
                    <a href="/{{ organisationSlug }}/index/">
                        <img src="/img/signadens_logo_small_inv.png" class="img-responsive" alt="Signadens Logo">
                    </a>
                </li>
                {% block sidebarcontent %}
                {% endblock %}
            </ul>
        </div>
        <div id="page-content-wrapper">
            <div class="container-fluid">





                {% if isIframe is not defined or not isIframe %}

                    <div class="row">

                        <header>
                            {{ partial("layouts/partial/nav") }}

                            {% if currentUser is defined and currentUser.hasRole('ROLE_LAB_USER_MASTERKEY') is not true %}
                                {% if
                                (
                                organisationSlug() is not 'default'
                                and router.getControllerName()
                                and not in_array(router.getControllerName(), ['auth','notification', 'projects'])
                                )
                                and disableSubnav is not defined
                                and router.getControllerName() is not 'helpdesk'
                                %}
                                    {{ partial(organisationSlug() ~ "/" ~ router.getControllerName() ~ "/_subnav") }}
                                {% elseif
                                in_array(router.getControllerName(), ['notification', 'projects'])
                                and disableSubnav is not defined %}
                                    {{ partial(router.getControllerName() ~ "/_subnav") }}
                                {% endif %}
                            {% endif %}
                        </header>

                        {# all of flashes are disabled in controllers and changed on session -> message #}
                        {{ this.flashSession.output() }}

                        <div class="block-content">

                {% else %}

                    <div class="row">

                        <header>
                            {{ partial("layouts/partial/nav") }}

                            {% if currentUser is defined and currentUser.hasRole('ROLE_LAB_USER_MASTERKEY') is not true %}
                                {% if
                                    (
                                    organisationSlug() is not 'default'
                                    and router.getControllerName()
                                    and not in_array(router.getControllerName(), ['auth','notification', 'projects'])
                                    )
                                    and disableSubnav is not defined
                                    and router.getControllerName() is not 'helpdesk'
                                %}
                                    {{ partial(organisationSlug() ~ "/" ~ router.getControllerName() ~ "/_subnav") }}
                                {% elseif
                                    in_array(router.getControllerName(), ['notification', 'projects'])
                                    and disableSubnav is not defined %}
                                    {{ partial(router.getControllerName() ~ "/_subnav") }}
                                {% endif %}
                            {% endif %}
                        </header>

                    </div>

                    {{ this.flashSession.output() }}

                {% endif %}

                        {% block content %}{% endblock %}

                {% if isIframe is not defined or not isIframe %}

                        </div>
                    </div>

                {% endif %}
            </div>
        </div>
    </div>


    {#<footer>#}
    {#{{ partial("layouts/partial/footer") }}#}
    {#</footer>#}


    {# prefere to add scripts in controller #}
    {% block scripts %}
        {{ assets.outputJs('footerNotCompile') }}
        {{ assets.get('footer')|addVersion }}
        {{ assets.get('additional')|addVersion }}

        {% if messages.has() %}
            {% set message = messages.output() %}
            <script type="text/javascript">
                setTimeout(function () {
                    {% if isset(message['type']) %}
                        toastr.{{ message['type'] }}("{{ message['content']|t }}");
                    {% else %}
                        {% for messageEl in message %}
                            toastr.{{ messageEl['type'] }}("{{ messageEl['content']|t }}");
                        {% endfor %}
                    {% endif %}
                }, 2000);
            </script>
        {% endif %}
    {% endblock %}
    {#<script type="text/javascript" src="https://gate51.atlassian.net/s/d41d8cd98f00b204e9800998ecf8427e-T/3wwcvm/100014/c/1000.0.10/_/download/batch/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector.js?locale=en-US&collectorId=507d145c"></script>#}
        {#<div style="background: white; padding: 20px; z-index: 99999999; position: absolute;"><?php echo '<br />END. Total execution time in seconds: ' . (microtime(true) - START_TIME); ?></div>#}
</body>
</html>
{% if checkDev() %}
    <?php
    {#echo $toolbar->render();#}
    ?>
{% endif %}