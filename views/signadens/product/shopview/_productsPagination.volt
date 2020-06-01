
<ul class="pagination pull-right" style="margin-bottom: 15px;">
    {% if total_pages > 2 AND current_page > 2 %}
        <li>
            <a class="change-page" data-page="1" href="javascript:;">1</a>
        </li>
    {% endif %}

    {% if total_pages > 1 AND before_page < current_page AND before_page > 0 %}
        <li>
            <a class="change-page" data-page="{{ before_page }}" href="javascript:;">{{ before_page }}</a>
        </li>
    {% endif %}

    {% if total_pages > 0 %}
        <li class="active"><a href="javascript:;" data-page="{{ current_page }}">{{ current_page }}</a></li>
    {% endif %}

    {% if total_pages > 1 AND after_page > current_page AND after_page <= total_pages %}
        <li>
            <a class="change-page" data-page="{{ after_page }}" href="javascript:;">{{ after_page }}</a>
        </li>
    {% endif %}

    {#{% if page.last > page.next AND page.total_pages > 3 %}#}
    {% if (total_pages - current_page) > 1 %}
        <li>
            <a class="change-page" data-page="{{ total_pages }}" href="javascript:;">{{ total_pages }}</a>
        </li>
    {% endif %}
</ul>