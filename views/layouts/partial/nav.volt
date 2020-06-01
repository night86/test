{% if isHeader is defined and isHeader is 'loginout' %}
    <nav class="navbar navbar-default navbar-signa">

        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">{{ 'Toggle navigation'|t }}</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse {{ organisationSlug() }}" id="bs-example-navbar-collapse-1">
            <div class="container-fluid">
                {% if currentUser != null %}
                    <a href="{{ url('auth/logout') }}" class="btn btn-primary navbar-btn pull-right"><i class="pe-7s-lock"></i> {{ 'Logout'|t }}</a>

                    {% if session.get('auth_back') is not null %}
                        <a href="{{ url('signadens/user/backtoadmin') }}" class="btn btn-default navbar-btn pull-right"><i class="pe-7s-id"></i> {{ 'Back to admin'|t }}</a>
                    {% endif %}
                {% else %}
                    <a href="{{ url("auth/login") }}" class="btn btn-primary navbar-btn pull-right">{{ 'Login'|t }}</a>
                {% endif %}
            </div><!-- /.navbar-collapse -->
        </div>
    </nav>
{% elseif isHeader is not defined or isHeader is not false %}
    <nav class="navbar navbar-default navbar-signa">

        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">{{ 'Toggle navigation'|t }}</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse {{ organisationSlug() }}" id="bs-example-navbar-collapse-1">

            <!-- arrow to toggle sidebar -->
            <a href="#" id="sidebar-toggle"><i class="pe-7s-left-arrow"></i></a>

            <div class="container-fluid">
            {% if currentUser != null %}

                {{ partial(organisationSlug() ~ "/partial/nav") }}

                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ greetingPartOfDay()|t }} {{ currentUser.getFullName() }} <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ url("general/account") }}">{{ "Account information"|t }}</a></li>
                            <li><a href="{{ url("general/organisation") }}">{{ "Organisation"|t }}</a></li>
                            <li role="separator" class="divider"></li>
                        </ul>
                    </li>
                    <li {{ controllername is 'notification' ? 'class="active"' : '' }}>
                        <a href="{{ url("notification/index") }}">
                            {% if this.notifications.countUnreaded() > 0 %}
                                <strong>{{ 'Inbox'|t }} <span class="badge">{{ this.notifications.countUnreaded() }}</span></strong>
                            {% else %}
                                {{ 'Inbox'|t }}
                            {% endif %}
                        </a>
                    </li>
                    {% if currentUser.hasRole('ROLE_SIGNADENS_HELPDESK') is true %}
                    <li class="dropdown {{ controllername is 'helpdesk' ? 'class="active"' : '' }}">
                        <a href="{{ url("signadens/helpdesk/view") }}" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Helpdesk <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ url("signadens/helpdesk/view") }}">{{ "View helpdesk"|t }}</a></li>
                            <li><a href="{{ url("signadens/instructions/edit") }}">{{ "Edit instructions"|t }}</a></li>
                            <li><a href="{{ url(organisationSlug()~"/index/start") }}">{{ "View instructions"|t }}</a></li>
                            <li role="separator" class="divider"></li>
                        </ul>
                    </li>
                    {% else %}
                    <li class="dropdown {{ controllername is 'helpdesk' ? 'class="active"' : '' }}">
                        <a href="{{ url("signadens/helpdesk/view") }}" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Helpdesk <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ url("signadens/helpdesk/view") }}">{{ "View helpdesk"|t }}</a></li>
                            <li><a href="{{ url(organisationSlug()~"/index/start") }}">{{ "Instructions"|t }}</a></li>
                            <li role="separator" class="divider"></li>
                        </ul>
                    </li>

                    {% endif %}
                    <a href="{{ url('auth/logout') }}" class="btn btn-primary navbar-btn pull-right"><i class="pe-7s-lock"></i> {{ 'Logout'|t }}</a>

                    {% if session.get('auth_back') is not null or session.get('auth_back_masterkey') is not null %}
                        <a href="{{ url('signadens/user/backtoadmin') }}" class="btn btn-default navbar-btn pull-right"><i class="pe-7s-id"></i> {{ 'Back to admin'|t }}</a>
                    {% endif %}
                </ul>
            {% else %}
                <a href="{{ url("auth/login") }}" class="btn btn-primary navbar-btn pull-right">{{ 'Login'|t }}</a>
            {% endif %}
            </div><!-- /.navbar-collapse -->
        </div>
    </nav>
{% endif %}