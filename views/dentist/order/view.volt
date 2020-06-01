{% extends "layouts/main.volt" %}
{% block title %} {{ "Order"|t }} {% endblock %}
{% block content %}

    <h3><a href="javascript:history.back()"><i class="pe-7s-back"></i></a> {{ "Order"|t }}: {{ order.code }}
    </h3>
    <br/>

    <div class="row">
        <div class="col-md-6">

            <legend>{{ 'Patient data'|t }}</legend>

            <div class="form-group">
                <label>{{ 'Patient'|t }}</label>
                <div class="col-md-12">
                    {{ order.DentistOrderData.getPatientInitials() }} {{ order.DentistOrderData.getPatientInsertion() }} {{ order.DentistOrderData.getPatientLastname() }}
                    <br/><br/>
                </div>
            </div>
            <div class="form-group">
                <label>{{ 'Gender'|t }}</label>
                <div class="col-md-12">
                    {% if order.DentistOrderData.patient_gender is 'm' %}{{ "Male"|t }}{% endif %}
                    {% if order.DentistOrderData.patient_gender is 'f' %}{{ "Female"|t }}{% endif %}
                    <br/><br/>
                </div>
            </div>
            <div class="form-group">
                <label>{{ 'Date of birth'|t }}</label>
                <div class="col-md-12">
                    {{ order.DentistOrderData.getPatientBirthFormat() }}
                    <br/><br/>
                </div>
            </div>
            <div class="form-group">
                <label>{{ 'BSN'|t }}</label>
                <div class="col-md-12">
                    {{ order.DentistOrderBsn.getBsnSecured() }}
                    <br/><br/>
                </div>
            </div>
        </div>
        <div class="col-md-6">

            <legend>{{ 'Order info'|t }}</legend>

            {#<div class="form-group">#}
                {#<label>{{ 'Delivery date'|t }}</label>#}
                {#<div class="col-md-12">#}
                    {#{% if order.delivery_at %}#}
                        {#{{ order.delivery_at }}#}
                    {#{% else %}#}
                        {#--#}
                    {#{% endif %}#}
                    {#<br/><br/>#}
                {#</div>#}
            {#</div>#}
            <div class="form-group">
                <label>{{ 'Attachment(s)'|t }}</label>
                <div class="col-md-12">
                    <ul>
                        {% for attachment in attachments %}
                            <li>
                                <a href="{{ url('/dentist/order/download/'~attachment.id) }}">{{ attachment.file_name }}</a>
                            </li>
                        {% endfor %}
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <legend></legend>
            <table id="recipes" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>{{ "Recipe number"|t }}</th>
                        <th>{{ "Recipe name"|t }}</th>
                        <th>{{ "Price"|t }}</th>
                        <th>{{ "Status"|t }}</th>
                        <th>{{ "Date and time"|t }}</th>
                        <th>{{ "Status change"|t }}</th>
                        <th>{{ "Changed by"|t }}</th>
                    </tr>
                </thead>
                <tbody>
                {% for recipeOrder in orderRecipes %}
                    <tr>
                        <td>{{ recipeOrder.Recipes.ParentRecipe.recipe_number }}</td>
                        <td>{# recipeOrder.Recipes.custom_name #}{{ recipeOrder.Recipes.ParentRecipe.name }}</td>
                        <td width="15%">
                            {% if recipeOrder.Recipes.DGD|length > 0 %}
                                {% for dgd in recipeOrder.Recipes.DGD %}
                                    {{ dgd.getDiscountPrice() }}
                                {% endfor %}
                            {% else %}
                                {{ recipeOrder.price }}
                            {% endif %}
                        </td>
                        <td>
                            <select name="status-{{ recipeOrder.id }}" class="form-control status-select"
                                    data-recipeid="{{ recipeOrder.id }}" data-orgstatus="{{ recipeOrder.status }}">
                                {% for status in statuses %}
                                    <option value="{{ status['id'] }}" {% if status['id'] is recipeOrder.status %}selected="selected"{% endif %}>{{ status['name'] }}</option>
                                {% endfor %}
                            </select>
                        </td>
                        {% if recipeOrder.status_changed_by is not null %}
                            <td><div class="hidden">{{ recipeOrder.status_changed_at }}</div>{{ recipeOrder.status_changed_at|dttonl }}</td>
                            <td>{{ recipeOrder.StatusPrev.name }} - {{ recipeOrder.StatusCurrent.name }}</td>
                            <td>{{ recipeOrder.StatusUser.getFullName() }}</td>
                        {% else %}
                            <td></td>
                            <td></td>
                            <td></td>
                        {% endif %}
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">

            <legend>{{ 'Order messages'|t }}</legend>

            <table id="mnessages" class="table table-striped table-bordered" cellspacing="0" width="100%"
                   style="border: none;">
                <tbody>
                {% for message in messages %}
                    <tr style="background: transparent;">
                        <th colspan="4" style="border:none;">{{ message.getCreatedAt() }}</th>
                    </tr>
                    <tr>
                        <td width="15%">{{ message.Organisation.getName() }}</td>
                        <td width="15%">{{ message.CreatedBy.getFullname() }}</td>
                        <td>{{ message.getNote() }}</td>
                        <td width="15%">
                            {% if message.DentistOrderNoteFile is not null %}
                                <a href="{{ url('/dentist/order/download/'~message.DentistOrderNoteFile.id) }}"
                                   class="btn btn-primary"><i class="pe-7s-download"></i>{{ "Download attachment"|t }}
                                </a>
                            {% endif %}
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>


        </div>
        <div class="col-md-6">

            <form id="orderForm" action="{{ url('dentist/order/view/' ~ order.code ) }}" method="post"
                  enctype="multipart/form-data">
                <legend>{{ 'New message about order'|t }}</legend>

                <div class="form-group">
                    {{ text_area('new_message', 'placeholder': 'Order notes or remarks...'|t, 'class': 'form-control new-message') }}
                </div>

                <div class="form-group">
                    {{ file_field('files[]', 'class': 'form-control', 'multiple': 'multiple') }}
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary pull-right"><i class="pe-7s-back"></i> {{ "Reply"|t }}
                    </button>
                </div>
            </form>

        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <br/>
        </div>
    </div>


    {{ partial("modals/confirm", ['id': 'change-status-modal', 'title': 'Change status?'|t, 'content': 'Are you sure you want to change the status?', 'additionalClass': 'change-status', 'primarybutton': 'Yes, I am sure']) }}

{% endblock %}

{% block scripts %}
    {{ super() }}

    <script>
        $('.status-select').on('change', function () {
            var $this = $(this);
            var newValue = $this.val();
            var recipeOrgStatus = $this.data('orgstatus');
            var recipeOrderId = $this.data('recipeid');


            //
            $('#change-status-modal').modal('show').find('.btn-primary').on('click', function () {
                $.ajax({
                    method: "post",
                    url: "/dentist/order/view/" +{{ order.code }},
                    data: {
                        id: recipeOrderId,
                        orgStatus: recipeOrgStatus,
                        newStatus: newValue
                    },
                    dataType: 'json'
                }).done(function (data) {
                    if (data != 'error') {
                        window.location.reload();
//                        $('#change-status-modal').modal('hide');
                    } else {
                        window.location.reload();
                    }
                });
            });
        })
    </script>
{% endblock %}