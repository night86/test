<div class="container">
    {% for index, notification in notofications %}
        <div class="row padding-15" style="page-break-inside:avoid !important;">
            <p><span style="font-size:20px;font-weight:600;">{{ notification.getSubject() }}</span> <span style="font-size:10px;">{{ "from"|t }} {{ notification.Created.getFullname() }} {{ notification.getCreatedAt()|dttonl }}</span></p>
            <h5>{{ "type"|t }}: {{ notification.getTypeLabel() }}</h5>
            <p>{{ notification.getDescription() }}</p>
        </div>
        {% if index + 1 < notofications|length %}
            <hr/>
        {% endif %}
    {% endfor %}
</div>