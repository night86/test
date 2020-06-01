<nav class="navbar navbar-default navbar-signa-sub">
    <div class="container-fluid">

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

            <ul class="nav navbar-nav">
                <li {{ controllername is 'notification'
                    and actionname is 'index'
                    ? 'class="active"' : '' }}>
                    <a href="{{ url('notification/index') }}">{{ 'Inbox'|t }}</a>
                </li>
                <li {{ controllername is 'notification'
                    and actionname is 'archive'
                    ? 'class="active"' : '' }}>
                    <a href="{{ url('notification/archive') }}">{{ 'Archive'|t }}</a>
                </li>
            </ul>

        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>