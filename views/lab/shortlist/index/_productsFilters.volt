<form id="productsFiltersForm" action="" method="">
    <div class="category">
        <h5>{{ "Main product category"|t }}</h5>
        <ul class="ul-main_category_id">
            {% for category in filters.getMainCategories() %}
                <li class="li_radio_main_category_id">
                    <input
                        type="radio"
                        name="filter[main_category_id]"
                        value="{{category.getId()}}"
                        class="radio_main_category_id"
                        data-class="radio_main_category_id"
                        data-name="{{category.getName()}}"
                        id="main_category_{{category.getId()}}"
                        {% if session.get('shortlist-filters')['filter'] is defined
                            and session.get('shortlist-filters')['filter']['main_category_id'] is defined
                            and session.get('shortlist-filters')['filter']['main_category_id'] is category.getId()
                        %}
                            checked="checked"
                            data-value="on"
                        {% else %}
                            data-value="off"
                        {% endif %}
                    />
                    <label for="{{ 'main_category_' ~ category.getId() }}">{{ category.getName() }}</label>
                </li>
            {% endfor %}
        </ul>
    </div>

    <div class="category">
        <h5>{{ "Sub category"|t }}</h5>
        <ul class="ul-sub_category_id">
            {% for category in filters.getSubCategories() %}
                <li class="li_radio_sub_category_id">
                    <input
                        type="radio"
                        name="filter[sub_category_id]"
                        value="{{category.getId()}}"
                        class="radio_sub_category_id"
                        data-class="radio_sub_category_id"
                        data-name="{{category.getName()}}"
                        data-parent="{{ category.Parent.getId() }}"
                        id="sub_category_{{category.getId()}}"
                        {% if session.get('shortlist-filters')['filter'] is defined
                            and session.get('shortlist-filters')['filter']['sub_category_id'] is defined
                            and session.get('shortlist-filters')['filter']['sub_category_id'] is category.getId()
                        %}
                            checked="checked"
                            data-value="on"
                        {% else %}
                            data-value="off"
                        {% endif %}
                    />
                    <label for="{{ 'sub_category_' ~ category.getId() }}">{{ category.getName() }}</label>
                </li>
            {% endfor %}
        </ul>
    </div>

    <div class="category">
        <h5>{{ "Subsub category"|t }}</h5>
        <ul class="ul-sub_sub_category_id">
            {% for category in filters.getSubsubCategories() %}
                <li class="li_check_sub_sub_category_id">
                    <input
                        type="checkbox"
                        name="filter[sub_sub_category_id][]"
                        value="{{category.getId()}}"
                        class="check_sub_sub_category_id"
                        data-name="{{category.getName()}}"
                        data-parent="{{ category.Parent.getId() }}"
                        data-parentparent="{{ category.Parent.Parent.getId() }}"
                        id="sub_sub_category_{{category.getId()}}"
                        {% if session.get('shortlist-filters')['filter'] is defined
                            and session.get('shortlist-filters')['filter']['sub_sub_category_id'] is defined
                            and in_array(category.getId(), session.get('shortlist-filters')['filter']['sub_sub_category_id'])
                        %}
                            checked="checked"
                        {% endif %}
                    />
                    <label for="{{ 'sub_sub_category_' ~ category.getId() }}">{{ category.getName() }}</label>
                </li>
            {% endfor %}
        </ul>
    </div>

    <div class="category">
        <h5>{{ "Suppliers"|t }}</h5>
        <ul class="ul-supplier_id">
            {% for supplier in filters.getSuppliers() %}
                <li class="li_check_supplier_id">
                    <input
                        type="checkbox"
                        name="filter[supplier_id][]"
                        value="{{supplier.id}}"
                        class="check_supplier_id"
                        data-name="{{supplier.name}}"
                        data-logo="{{ supplier.logo }}"
                        id="supplier_id_{{supplier.id}}"
                        {% if session.get('shortlist-filters')['filter'] is defined
                            and session.get('shortlist-filters')['filter']['supplier_id'] is defined
                            and in_array(supplier.id, session.get('shortlist-filters')['filter']['supplier_id'])
                        %}
                            checked="checked"
                        {% endif %}
                    />
                    <label for="{{ 'supplier_id_' ~ supplier.id }}">{{ supplier.name }}</label>
                </li>
            {% endfor %}
        </ul>
    </div>

    <div class="category">
        <h5>{{ "Manufacturer"|t }}</h5>
        <ul class="ul-manufacturer">

        </ul>
    </div>

    <input type="hidden" id="current_page" name="page" value="{% if session.get('shortlist-filters')['page'] %}{{ session.get('shortlist-filters')['page'] }}{% else %}1{% endif %}" />
    <input type="hidden" id="current_limit" name="limit" value="{% if session.get('shortlist-filters')['limit'] %}{{ session.get('shortlist-filters')['limit'] }}{% else %}6{% endif %}" />
    <input type="hidden" id="current_query" name="query" value="{% if session.get('shortlist-filters')['query'] %}{{ session.get('shortlist-filters')['query'] }}{% endif %}" />
    <input type="hidden" id="current_hash" name="hash" value="" />
    <input type="hidden" id="current_list" name="shortlist" value="1" />
</form>
<div class="hidden empty-manufacturer-row">
    <li class="li_check_manufacturer">
        {{ check_field(
            'filter[manufacturer][]',
            'value': '',
            'class': 'check_manufacturer',
            'id': '')
        }}
        <label for=""></label>
    </li>
</div>

<div class="hidden empty-moreless">
    <div class="moreless-elements" style="display: none;">

    </div>
    <div class="moreless-label">
        <div class="more-label">{{ " - view more - " }}</div>
        <div class="less-label">{{ " - view less - " }}</div>
    </div>
</div>