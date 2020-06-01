
<nav class="navbar navbar-default navbar-signa-sub">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li {{ controllername is 'projects'
                and actionname is 'view'
                ? 'class="active"' : '' }}>
                    <a href="{{ url('/projects/view/'~projectId) }}">{{ 'Wall'|t }}</a>
                </li>
                <li {{ controllername is 'projects'
                and actionname is 'tasks'
                ? 'class="active"' : '' }}>
                    <a href="{{ url('/projects/tasks/'~projectId) }}">{{ 'Tasks'|t }}</a>
                </li>
                <li {{ controllername is 'projects'
                and actionname is 'note'
                ? 'class="active"' : '' }}>
                    <a href="{{ url('/projects/note/'~projectId) }}">{{ 'Notes'|t }}</a>
                </li>
                <li {{ controllername is 'projects'
                and actionname is 'file'
                ? 'class="active"' : '' }}>
                    <a href="{{ url('/projects/file/'~projectId) }}">{{ 'Files'|t }}</a>
                </li>
            </ul>
        </div>
    </div>
</nav>