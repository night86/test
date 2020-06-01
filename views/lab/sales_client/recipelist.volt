{% extends "layouts/main.volt" %}
{% block title %} {{ "Recipe list"|t }} {% endblock %}
{% block content %}

    <h3><a href="/lab/sales_client/edit/{{ dentist.getId() }}"><i class="pe-7s-back"></i></a></h3>
    <p class="pull-left">
        {#<a href="{{ url("lab/sales_client/pending") }}" id="pending-button" class="btn-primary btn"><i class="pe-7s-pen"></i> {{ "Pending invitations"|t }}</a>
        <a href="{{ url("lab/sales_client/add") }}" class="btn-primary btn"><i class="pe-7s-plus"></i> {{ "Add dentist"|t }}</a>#}
    </p>
    <h3>{{ "Activated recipes of"|t }} {{ dentist.getName() }}</h3>
    <form id="recipelistForm" action="/lab/sales_client/recipelist/{{ dentist.getId() }}" method="post">
    <div class="row">
        <div class="col-md-12">
            {% for inCat, cat in categoryList %}
                {% if cat['show'] is 1 %}
                <div class="panel-group" id="category_{{ inCat }}" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingNewOrder_{{ inCat }}">
                            <h4 class="panel-title">
                                <input id="catBox_{{ inCat }}" data-id="{{ inCat }}" class="catBox" type="checkbox" />
                                <a id="catToggle_{{ inCat }}" role="button" data-toggle="collapse" href="#categoryCollapse_{{ inCat }}" aria-expanded="true">{{ cat['name'] }}</a>
                            </h4>
                        </div>
                        <div id="categoryCollapse_{{ inCat }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingNewOrder_{{ inCat }}">
                            <div class="panel-body">
                                {% for inSub, sub in cat['sub_categories'] %}
                                    {% if sub['show'] is 1 %}
                                    <div class="panel-group" id="sub_{{ inSub }}" role="tablist" aria-multiselectable="true">
                                        <div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="headingNewOrder_{{ inSub }}">
                                                <h4 class="panel-title">
                                                    <input id="subBox_{{ inSub }}" class="subBox subBox_{{ inCat }}" data-id="{{ inSub }}" data-cat="{{ inCat }}" type="checkbox" />
                                                    <a id="subToggle_{{ inSub }}" role="button" data-toggle="collapse" href="#subCollapse_{{ inSub }}" aria-expanded="true" aria-controls="collapseNewProduct_{{ inSub }}">{{ sub['name'] }}</a>
                                                </h4>
                                            </div>
                                            <div id="subCollapse_{{ inSub }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingNewOrder_{{ inSub }}">
                                                <div class="panel-body">
                                                    {% for inSubSub, subsub in sub['sub_sub_categories'] %}
                                                        {% if subsub['show'] is 1 %}
                                                        <div class="panel-group" id="subsub_{{ inSubSub }}" role="tablist" aria-multiselectable="true">
                                                            <div class="panel panel-default">
                                                                <div class="panel-heading" role="tab" id="headingNewOrder_{{ inSubSub }}">
                                                                    <h4 class="panel-title">
                                                                        <input id="subsubBox_{{ inSubSub }}" class="subsubBox subsubBox_{{ inSub }}" data-id="{{ inSubSub }}" data-cat="{{ inCat }}" data-sub="{{ inSub }}" type="checkbox" />
                                                                        <a id="subsubToggle_{{ inSubSub }}" role="button" data-toggle="collapse" href="#subsubCollapse_{{ inSubSub }}" aria-expanded="true" aria-controls="collapseNewProduct_{{ inSubSub }}">{{ subsub['name'] }}</a>
                                                                    </h4>
                                                                </div>
                                                                <div id="subsubCollapse_{{ inSubSub }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingNewOrder_{{ inSubSub }}">
                                                                    <div class="panel-body">
                                                                        {% for inRec, rec in subsub['recipes'] %}
                                                                            <input id="recBox_{{ inRec }}" class="recBox recBox_{{ inSubSub }}" data-id="{{ inRec }}" data-cat="{{ inCat }}" data-sub="{{ inSub }}" data-subsub="{{ inSubSub }}" name="recipe[{{ rec['id'] }}]" {% if selectedRecipes is not null and in_array(rec['id'], selectedRecipes) %}value="1" checked="checked"{% else %}value="0"{% endif %} type="checkbox" />
                                                                            <a role="button" data-toggle="collapse" href="" aria-expanded="true" aria-controls="collapseNewProduct_{{ inRec }}">{{ rec['name'] }}</a><br />
                                                                        {% endfor %}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {% endif %}
                                                    {% endfor %}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {% endif %}
                                {% endfor %}
                            </div>
                        </div>
                    </div>
                </div>
                {% endif %}
            {% endfor %}
        </div>
    </div>
    {#<pre>{{ print_r(categoryList) }}</pre>#}
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">&nbsp;</div>
            <div class="form-group">
                <label for="">&nbsp;</label>
                <button id="confirmForm" type="submit" class="btn btn-primary pull-right"><i class="pe-7s-diskette"></i> {{ "Save"|t }}</button>
            </div>
        </div>
    </div>
        {{ partial("modals/confirmGeneral", ['id': 'reset-options', 'title': "Confirmation"|t, 'content': "You are resetting all options below. Are you sure?"|t, 'confirmButton': 'Yes'|t, 'cancelButton': 'Cancel'|t, 'additionalClass': 'confirmReset']) }}
        {{ partial("modals/confirmGeneral", ['id': 'all-options', 'title': "Confirmation"|t, 'content': "Are you sure you want to select all recipes within this category?"|t, 'confirmButton': 'Yes'|t, 'cancelButton': 'Cancel'|t, 'additionalClass': 'confirmAll']) }}
    </form>

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        $(function(){

            {% if selectedRecipes is not null %}
                {% for rec in selectedRecipes %}

                var subsub_{{ rec }} = $('#recBox_{{ rec }}').attr('data-subsub');
                var sub_{{ rec }} = $('#recBox_{{ rec }}').attr('data-sub');
                var cat_{{ rec }} = $('#recBox_{{ rec }}').attr('data-cat');

                $('#catToggle_'+cat_{{ rec }}).trigger('click');
                $('#subToggle_'+sub_{{ rec }}).trigger('click');
                $('#subsubToggle_'+subsub_{{ rec }}).trigger('click');

                $('#catBox_'+cat_{{ rec }}).prop('checked', true);
                $('#catBox_'+cat_{{ rec }}).attr('checked', 'checked');
                $('#subBox_'+sub_{{ rec }}).prop('checked', true);
                $('#subBox_'+sub_{{ rec }}).attr('checked', 'checked');
                $('#subsubBox_'+subsub_{{ rec }}).prop('checked', true);
                $('#subsubBox_'+subsub_{{ rec }}).attr('checked', 'checked');

                {% endfor %}
            {% endif %}

            // SELECT ALL SUBCATEGORIES
            $('.catBox').on('focus', function(){

                if(!$(this).is(":checked")){

                    $('#all-options').modal('show');
                    $('.confirmAll').attr({"data-id": $(this).attr('data-id'), "data-type": "cat"});
                }
                else {
                    $('#reset-options').modal('show');
                    $('.confirmReset').attr({"data-id": $(this).attr('data-id'),  "data-type": "cat"});
                }
            });

            // SELECT ALL SUBSUBCATEGORIES
            $('.subBox').on('focus', function(){

                if(!$(this).is(":checked")){

                    $('#all-options').modal('show');
                    $('.confirmAll').attr({"data-id": $(this).attr('data-id'), "data-cat": $(this).attr('data-cat'), "data-type": "sub"});
                }
                else {
                    $('#reset-options').modal('show');
                    $('.confirmReset').attr({"data-id": $(this).attr('data-id'), "data-cat": $(this).attr('data-cat'), "data-type": "sub"});
                }
            });

            // SELECT ALL RECIPES
            $('.subsubBox').on('focus', function(){

                if(!$(this).is(":checked")){

                    $('#all-options').modal('show');
                    $('.confirmAll').attr({"data-id": $(this).attr('data-id'), "data-cat": $(this).attr('data-cat'), "data-sub": $(this).attr('data-sub'), "data-type": "subsub"});
                }
                else {
                    $('#reset-options').modal('show');
                    $('.confirmReset').attr({"data-id": $(this).attr('data-id'), "data-cat": $(this).attr('data-cat'), "data-sub": $(this).attr('data-sub'), "data-type": "subsub"});
                }
            });

            // CHECK FOR NO RECIPES CHECKED THEN UNCHECK SUBSUBCATEGORY
            $('.recBox').on('click', function(){

                var checkedRecipes = false;

                if($(this).prop('checked') == true){
                    $(this).attr('checked', 'checked');
                    $(this).prop('checked', true);
                    $(this).val(1);
                }
                else {
                    $(this).removeAttr('checked');
                    $(this).prop('checked', false);
                    $(this).val(0);
                }

                $('.recBox_'+$(this).attr('data-subsub')).each(function(){

                    // IF AT LEAST ONE RECIPE IS CHECKED IS ENOUGH TO LEAVE THE SUBSUB CHECKED
                    if($(this).prop('checked') == true){
                        checkedRecipes = true;
                    }
                });

                // IF NO CHECKED RECIPES AND PARENT SUBSUB IS CHECKED THEN UNCHECK IT
                if(checkedRecipes == false){
                    $('#subsubBox_'+$(this).attr('data-subsub')).prop('checked', false);
                    $('#subsubBox_'+$(this).attr('data-subsub')).removeAttr('checked');

                    var checkedSubSub = false;

                    $('.subsubBox_'+$(this).attr('data-sub')).each(function(){

                        // IF AT LEAST ONE SUBSUB IS CHECKED IS ENOUGH TO LEAVE THE SUB CHECKED
                        if($(this).prop('checked') == true){
                            checkedSubSub = true;
                        }
                    });

                    if(checkedSubSub == false){
                        $('#subBox_'+$(this).attr('data-sub')).prop('checked', false);
                        $('#subBox_'+$(this).attr('data-sub')).removeAttr('checked');

                        var checkedSub = false;

                        $('.subBox_'+$(this).attr('data-cat')).each(function(){

                            // IF AT LEAST ONE SUB IS CHECKED IS ENOUGH TO LEAVE THE CAT CHECKED
                            if($(this).prop('checked') == true){
                                checkedSub = true;
                            }
                        });

                        if(checkedSub == false){
                            $('#catBox_'+$(this).attr('data-cat')).prop('checked', false);
                            $('#catBox_'+$(this).attr('data-cat')).removeAttr('checked');
                        }
                        else {
                            $('#catBox_'+$(this).attr('data-cat')).prop('checked', true);
                            $('#catBox_'+$(this).attr('data-cat')).attr('checked', 'checked');
                        }
                    }
                    else {
                        $('#subBox_'+$(this).attr('data-sub')).prop('checked', true);
                        $('#subBox_'+$(this).attr('data-sub')).attr('checked', 'checked');
                    }
                }
                else {
                    $('#subsubBox_'+$(this).attr('data-subsub')).prop('checked', true);
                    $('#subsubBox_'+$(this).attr('data-subsub')).attr('checked', 'checked');
                    $('#subBox_'+$(this).attr('data-sub')).prop('checked', true);
                    $('#subBox_'+$(this).attr('data-sub')).attr('checked', 'checked');
                    $('#catBox_'+$(this).attr('data-cat')).prop('checked', true);
                    $('#catBox_'+$(this).attr('data-cat')).attr('checked', 'checked');
                }
            });

            // MODAL SELECTING ALL RECIPES FOR CHOSEN CATEGORY
            $('.confirmAll').on('click', function(){

                // If subsub-category
                if($(this).attr('data-type') === "subsub"){

                    // Enable subsub box and all recipes inside
                    $('#subsubBox_'+$(this).attr('data-id')).prop("checked", true);
                    $('#subsubBox_'+$(this).attr('data-id')).attr("checked", "checked");
                    $('.recBox_'+$(this).attr('data-id')).prop("checked", true);
                    $('.recBox_'+$(this).attr('data-id')).attr("checked", "checked");

                    // Enable sub and category
                    $('#subBox_'+$(this).attr('data-sub')).prop('checked', true);
                    $('#subBox_'+$(this).attr('data-sub')).attr('checked', 'checked');
                    $('#catBox_'+$(this).attr('data-cat')).prop('checked', true);
                    $('#catBox_'+$(this).attr('data-cat')).attr('checked', 'checked');
                }

                // If sub-category
                if($(this).attr('data-type') === "sub"){

                    // Enable subsub boxes and recipes inside
                    $('.subsubBox_'+$(this).attr('data-id')).each(function(){

                        // Enable subsub box
                        $('#subsubBox_'+$(this).attr('data-id')).prop("checked", true);
                        $('#subsubBox_'+$(this).attr('data-id')).attr("checked", "checked");

                        // Enable recipes
                        $('.recBox_'+$(this).attr('data-id')).prop("checked", true);
                        $('.recBox_'+$(this).attr('data-id')).attr("checked", "checked");
                    });

                    // Enable sub and category
                    $('#subBox_'+$(this).attr('data-id')).prop("checked", true);
                    $('#subBox_'+$(this).attr('data-id')).attr("checked", "checked");
                    $('#catBox_'+$(this).attr('data-cat')).prop('checked', true);
                    $('#catBox_'+$(this).attr('data-cat')).attr('checked', 'checked');
                }

                // If category
                if($(this).attr('data-type') === "cat"){

                    // Enable sub boxes
                    $('.subBox_'+$(this).attr('data-id')).each(function(){

                        // Enable subsub boxes and recipes inside
                        $('.subsubBox_'+$(this).attr('data-id')).each(function(){

                            // Enable subsub box
                            $('#subsubBox_'+$(this).attr('data-id')).prop("checked", true);
                            $('#subsubBox_'+$(this).attr('data-id')).attr("checked", "checked");

                            // Enable recipes
                            $('.recBox_'+$(this).attr('data-id')).prop("checked", true);
                            $('.recBox_'+$(this).attr('data-id')).attr("checked", "checked");
                        });

                        // Enable sub boxes
                        $('#subBox_'+$(this).attr('data-id')).prop("checked", true);
                        $('#subBox_'+$(this).attr('data-id')).attr("checked", "checked");
                    });

                    // Enable category
                    $('#catBox_'+$(this).attr('data-id')).prop('checked', true);
                    $('#catBox_'+$(this).attr('data-id')).attr('checked', 'checked');
                }
                $('#all-options').modal('hide');
            });

            // MODAL DELETING ALL RECIPES FOR CHOSEN CATEGORY
            $('.confirmReset').on('click', function(){

                // If subsub-category
                if($(this).attr('data-type') === "subsub"){

                    // Disable subsub box and all recipes inside
                    $('#subsubBox_'+$(this).attr('data-id')).prop("checked", false);
                    $('#subsubBox_'+$(this).attr('data-id')).removeAttr("checked");
                    $('.recBox_'+$(this).attr('data-id')).prop("checked", false);
                    $('.recBox_'+$(this).attr('data-id')).removeAttr("checked");

                    // Search for subsubs remaining
                    var hasSubSubs = false;

                    $('.subsubBox_'+$(this).attr('data-sub')).each(function(){

                        // If at least one subsub is checked is enough to leave the sub checked
                        if($(this).prop('checked') == true){
                            hasSubSubs = true;
                        }
                    });

                    // If no subsubs remaining then disable sub
                    if(hasSubSubs === false){
                        $('#subBox_'+$(this).attr('data-sub')).prop('checked', false);
                        $('#subBox_'+$(this).attr('data-sub')).removeAttr('checked');
                    }

                    // Search for subs remaining
                    var hasSubs = false;

                    $('.subBox_'+$(this).attr('data-cat')).each(function(){

                        // If one sub is checked, leave the cat checked
                        if($(this).prop('checked') == true){
                            hasSubs = true;
                        }
                    });

                    // If no subs remaining then disable cat
                    if(hasSubs === false){
                        $('#catBox_'+$(this).attr('data-cat')).prop('checked', false);
                        $('#catBox_'+$(this).attr('data-cat')).removeAttr('checked');
                    }
                }

                // If sub-category
                if($(this).attr('data-type') === "sub"){

                    // Disable sub box
                    $('#subBox_'+$(this).attr('data-id')).prop("checked", false);
                    $('#subBox_'+$(this).attr('data-id')).removeAttr("checked");

                    // Disable subsub box and all recipes inside
                    $('.subsubBox_'+$(this).attr('data-id')).each(function(){

                        $('#subsubBox_'+$(this).attr('data-id')).prop("checked", false);
                        $('#subsubBox_'+$(this).attr('data-id')).removeAttr("checked");
                        $('.recBox_'+$(this).attr('data-id')).prop("checked", false);
                        $('.recBox_'+$(this).attr('data-id')).removeAttr("checked");
                    });

                    // Search for subs remaining
                    var hasSubs = false;

                    $('.subBox_'+$(this).attr('data-cat')).each(function(){

                        // If one sub is checked, leave the cat checked
                        if($(this).prop('checked') == true){
                            hasSubs = true;
                        }
                    });

                    // If no subs remaining then disable cat
                    if(hasSubs === false){
                        $('#catBox_'+$(this).attr('data-cat')).prop('checked', false);
                        $('#catBox_'+$(this).attr('data-cat')).removeAttr('checked');
                    }
                }

                // If category
                if($(this).attr('data-type') === "cat"){

                    // Disable cat box
                    $('#catBox_'+$(this).attr('data-id')).prop("checked", false);
                    $('#catBox_'+$(this).attr('data-id')).removeAttr("checked");

                    // Disable subs box and all subsubs and recipes inside
                    $('.subBox_'+$(this).attr('data-id')).each(function(){

                        $('.subsubBox_'+$(this).attr('data-id')).each(function(){

                            $('#subsubBox_'+$(this).attr('data-id')).prop("checked", false);
                            $('#subsubBox_'+$(this).attr('data-id')).removeAttr("checked");
                            $('.recBox_'+$(this).attr('data-id')).prop("checked", false);
                            $('.recBox_'+$(this).attr('data-id')).removeAttr("checked");
                        });
                        $('#subBox_'+$(this).attr('data-id')).prop("checked", false);
                        $('#subBox_'+$(this).attr('data-id')).removeAttr("checked");
                    });
                }
                $('#reset-options').modal('hide');
            });
        });
    </script>
{% endblock %}