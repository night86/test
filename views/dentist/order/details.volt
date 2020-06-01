{% extends "layouts/main.volt" %}
{% block title %} {{ "Order details"|t }} {% endblock %}
{% block content %}

    <h3>
        <a href="javascript:history.back()"><i class="pe-7s-back"></i></a>
        {{ "Order details"|t }}: {{ order.code }}
    </h3>

    {#<div class="row">#}
        {#<div class="col-md-12">&nbsp;</div>#}
        {#<div class="col-md-12">&nbsp;</div>#}
        {#{% if currentUser.Organisation.getOrganisationTypeId() == 4 %}#}
        {#<div class="col-md-2">#}
            {#<label>{{ 'Dentist profile'|t }}</label>#}
            {#<select id="lab_dentist" name="dentist_id" class="select2-input">#}
                {#<option></option>#}
                {#{% for dentist in labDentists %}#}
                    {#<option value="{{ dentist.dentist_id }}" {% if order.dentist_id == dentist.dentist_id %}selected="selected"{% endif %}>{{ dentist.Dentist.name }}</option>#}
                {#{% endfor %}#}
            {#</select>#}
        {#</div>#}
        {#<div class="col-md-1">#}
            {#{% if currentUser.Organisation.getOrganisationTypeId() == 4 %}#}
                {#<button id="save_dentist" type="button" class="btn btn-primary" style="margin-top: 23px;" data-order="{{ order.id }}"><i class="pe-7s-diskette"></i> {{ "Save"|t }}</button>#}
            {#{% endif %}#}
        {#</div>#}
        {#<div class="col-md-1">&nbsp;</div>#}
        {#<div class="col-md-9">#}
            {#{% if blockForm is false %}#}
            {#<p class="pull-right"><a id="add_recipe" data-url="{{ url("dentist/order/add/" ~ order.code ) }}" class="btn-primary btn "><i class="pe-7s-plus"></i> {{ "Add product"|t }}</a></p>#}
            {#{% endif %}#}
        {#</div>#}
        {#{% else %}#}
        {#<div class="col-md-12">#}
            {#<p class="pull-right"><a id="add_recipe" data-url="{{ url("dentist/order/add/" ~ order.code ) }}" class="btn-primary btn {% if dentistLabs is not null and count(orderRecipes) is 0 and count(dentistLabs) > 1 %}multiLabs{% endif %}"><i class="pe-7s-plus"></i> {{ "Add product"|t }}</a></p>#}
        {#</div>#}
        {#{% endif %}#}
    {#</div>#}

    {#<form id="orderForm" action="{{ url('dentist/order/edit/' ~ order.code ) }}" method="post" enctype="multipart/form-data">#}
        <input id="reload_after" type="hidden" name="reload_after" value="0" />
        <fieldset class="form-group">
            {% if blockForm is false %}
            <div class="row">
                <div class="col-md-12">
                    <table id="recipes" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>{{ "Recipe number"|t }}</th>
                                <th>{{ "Recipe name"|t }}</th>
                                {#<th>{{ "Delivery time"|t }}</th>#}
                                {% if currentUser.Organisation.getOrganisationTypeId() != 4 %}
                                <th>{{ "Lab"|t }}</th>
                                {% endif %}
                                {#<th>{{ "Price"|t }}</th>#}
                                <th>{{ "Actions"|t }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        {% for recipeOrder in order.DentistOrderRecipe %}
                            {% if recipeOrder.deleted_at is not null or recipeOrder.deleted_by is not null %}
                            {% continue %}
                            {% else %}
                            <tr>
                                <td>{{ recipeOrder.Recipes.ParentRecipe.recipe_number }}</td>
                                <td>{# recipeOrder.Recipes.custom_name #}{{ recipeOrder.Recipes.ParentRecipe.name }}</td>
                                {#<td>{% if recipeOrder.Recipes.delivery_time is null %}0{% else %}{{ recipeOrder.Recipes.delivery_time }}{% endif %}</td>#}
                                {% if currentUser.Organisation.getOrganisationTypeId() != 4 %}
                                <td>{{ recipeOrder.Recipes.Lab.name }}</td>
                                {% endif %}
                                {#<td width="15%">
                                    {% if recipeOrder.Recipes.DGD|length > 0 %}
                                        {% for dgd in recipeOrder.Recipes.DGD %}
                                            {{ dgd.getDiscountPrice() }}
                                        {% endfor %}
                                    {% else %}
                                        {{ recipeOrder.price }}
                                    {% endif %}
                                </td>#}
                                <td>
                                    <a href="{{ url("dentist/order/recipedetails/" ~ recipeOrder.id) }}" class="btn-primary btn "><i class="pe-7s-note2"></i> {{ "Recipe details"|t }}</a>
                                </td>
                            </tr>
                            {% endif %}
                        {% endfor %}
                        </tbody>
                    </table>
                </div>
            </div>

            <legend></legend>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group col-md-6" style="padding-left: 0;">
                        <label>{{ 'Patient initials'|t }}</label>
                        <input id="patient_initials" name="data[patient_initials]" class="form-control" value="{{ order.DentistOrderData.patient_initials }}" required="required" readonly="readonly" />
                    </div>
                    <div class="form-group col-md-6" style="padding-right: 0;">
                        <label>{{ 'Patient insertion'|t }}</label>
                        <input id="patient_insertion" name="data[patient_insertion]" class="form-control" value="{{ order.DentistOrderData.patient_insertion }}" readonly="readonly" />
                    </div>
                    <div class="form-group">
                        <label>{{ 'Patient last name'|t }}</label>
                        <input id="patient_lastname" name="data[patient_lastname]" class="form-control" value="{{ order.DentistOrderData.patient_lastname }}" required="required" readonly="readonly" />
                    </div>
                    <div class="form-group">
                        <label>{{ 'BSN'|t }}</label>
                        <input id="bsn" name="bsn" class="form-control" type="text" value="{% if order.DentistOrderBsn and order.DentistOrderBsn.getBsn() is not null %}{{ order.DentistOrderBsn.getBsn() }}{% endif %}" readonly="readonly" />
                        <p>BSN alleen invullen indien voor uw administratie strikt noodzakelijk</p>
                    </div>
                    <div class="form-group">
                        <label>{{ 'Patient number'|t }}</label>
                        <input id="patient_number" name="data[patient_number]" class="form-control" type="text" value="{{ order.DentistOrderData.patient_number }}" readonly="readonly" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group col-md-4">
                        <label>{{ 'Date of birth'|t }}</label>
                        <select id="patient_birth_day" name="data[patient_birth][day]" class="form-control" required="required" disabled="disabled">
                            <option value="" disabled="disabled" selected="selected">{{ "Day"|t }}</option>
                            {% for i in 1..31 %}
                                <option value="{{ i }}" {% if birthDate['day'] == i %}selected="selected"{% endif %}>{{ i }}</option>
                            {% endfor %}
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>&nbsp;</label>
                        <select id="patient_birth_month" name="data[patient_birth][month]" class="form-control" required="required" disabled="disabled">
                            <option value="" disabled="disabled" selected="selected">{{ "Month"|t }}</option>
                            {% for i in 1..12 %}
                                <option value="{{ i }}" {% if birthDate['month'] == i %}selected="selected"{% endif %}>{{ i }}</option>
                            {% endfor %}
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>&nbsp;</label>
                        <select id="patient_birth_year" name="data[patient_birth][year]" class="form-control" required="required" disabled="disabled">
                            <option value="" disabled="disabled" selected="selected">{{ "Year"|t }}</option>
                            {% for i in 1900..2018 %}
                                <option value="{{ i }}" {% if birthDate['year'] == i %}selected="selected"{% endif %}>{{ i }}</option>
                            {% endfor %}
                        </select>
                    </div>
                    <div class="form-group col-md-12">
                        <label>{{ 'Gender'|t }}</label>
                        <br/><input {% if order.DentistOrderData.patient_gender is 'm' %}checked="checked"{% endif %}
                                    type="radio" name="data[patient_gender]" value="m" disabled="disabled" /> {{ "Male"|t }}
                        <br/><input {% if order.DentistOrderData.patient_gender is 'f' %}checked="checked"{% endif %}
                                    type="radio" name="data[patient_gender]" value="f" disabled="disabled" /> {{ "Female"|t }}
                    </div>
                </div>
                <div class="col-md-4">
                    {% if subDentists is not null %}
                        <div class="form-group">
                            <label>{{ 'Dentist'|t }}</label>
                            <select id="sub_dentist" name="dentist_user_id" class="form-control" disabled="disabled">
                                <option></option>
                                {% for dentist in subDentists %}
                                    {% if dentist.active == 1 %}
                                        <option value="{{ dentist.id }}" {% if dentist.id == currentUser.getId() or dentist.id == order.getDentistUserId() %}selected="selected"{% endif %}>{{ dentist.firstname }} {{ dentist.lastname }}</option>
                                    {% endif %}
                                {% endfor %}
                            </select>
                        </div>
                    {% endif %}
                    {#HIDDEN ON LIVE#}
                    {#{% if count(locations) > 1 %}#}
                    {#<div class="form-group">#}
                        {#<label>{{ 'Location'|t }}</label>#}
                        {#<select id="locations" name="location_id" class="form-control" required="required" disabled="disabled">#}
                            {#<option></option>#}
                            {#{% for loc in locations %}#}
                                {#<option value="{{ loc.getId() }}" {% if (order.getLocationId() is not null and order.getLocationId() == loc.getId()) or (order.getLocationId() is null and lastLocation is not false and lastLocation.getLocationId() == loc.getId()) %}selected="selected"{% endif %}>{{ loc.getName() }}</option>#}
                            {#{% endfor %}#}
                        {#</select>#}
                    {#</div>#}
                    {#{% endif %}#}
                    {#HIDDEN ON LIVE#}
                    <div class="form-group">
                        <label>{{ 'Attachment(s)'|t }}</label>
                        {% for file in files %}
                        <div class="form-control" id="attachment-{{file['id']}}">
                            {{file['file_name']}}
                            {#<a id="file-{{file['id']}}" class="file-remove-button btn btn-danger btn-xs pull-right"><i class="pe-7s-trash"></i></a>#}
                        </div>

                        {% endfor %}

                        {#{{ file_field('files[]', 'class': 'form-control', 'multiple': 'multiple') }}#}
                    </div>
                    <div class="form-group">
                        <textarea id="description" name="description" placeholder="{{ 'Order notes or remarks...'|t }}" class="form-control" readonly="readonly"></textarea>
                    </div>
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
                                <th colspan="4" style="border:none;">{{ date("d-m-Y H:i:s", strtotime(message.getCreatedAt())) }}</th>
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
                <div class="col-md-6"></div>
            </div>

            <legend></legend>
            {#<div class="row">#}
                {#<div class="col-lg-12">#}
                    {#{% if order.DentistOrderRecipe|length > 0 %}#}
                        {#{% if order.status == 2 or order.status == 3 %}#}
                            {#<button type="submit" name="complete" class="btn btn-success pull-right"><i class="pe-7s-next-2 complete-order"></i> {{ "Send updated order to lab"|t }}</button>#}
                        {#{% else %}#}
                            {#<button type="submit" name="complete" class="btn btn-success pull-right"><i class="pe-7s-play complete-order"></i> {{ "Complete order"|t }}</button>#}
                        {#{% endif %}#}
                    {#{% endif %}#}
                    {#<button id="save_patient" type="submit" class="btn btn-primary pull-right"><i class="pe-7s-diskette"></i> {{ "Save"|t }}</button>#}
                {#</div>#}
            {#</div>#}
        {% endif %}
        </fieldset>
        {#<div class="modal fade" id="chooseLabModal" tabindex="-1" role="dialog" aria-labelledby="chooseLabModal">#}
            {#<div class="modal-dialog" role="document">#}
                {#<div class="modal-content">#}
                    {#<div class="modal-header">#}
                        {#<button type="button" class="close" data-dismiss="modal" aria-label="Close">#}
                            {#<span aria-hidden="true">&times;</span></button>#}
                        {#<h4 class="modal-title" id="chooseLabModalLabel">{{ "Your dentist organisation is connected to multiple labs. At which lab do you want to order?"|t }}</h4>#}
                    {#</div>#}
                    {#<div class="modal-body">#}
                        {#{% for lab in dentistLabs %}#}
                            {#<input id="radio_{{ lab.lab_id }}" type="radio" name="lab_choice" class="labOption" data-id="{{ lab.lab_id }}" value="{{ lab.lab_id }}" /><img class="labOption" data-id="{{ lab.lab_id }}" src="/uploads/images/organisation/{{ lab.Lab.logo }}" alt="{{ lab.Lab.name }}" width="200" style="margin-left: 20px; cursor: pointer;" /><br /><br /><br />#}
                        {#{% endfor %}#}
                    {#</div>#}
                    {#<div class="modal-footer">#}
                        {#<button type="button" class="btn btn-default" data-dismiss="modal">{{'Close'|t}}</button>#}
                        {#<button id="confirmLab" type="button" class="btn btn-primary">{{'Save'|t}}</button>#}
                    {#</div>#}
                {#</div>#}
            {#</div>#}
        {#</div>#}
    {#</form>#}
    {#{{ partial("modals/confirm", ['id': 'confirmDelete', 'title': 'Delete recipe from order'|t, 'content': 'Are you sure you want to delete this recipe?', 'additionalClass': 'confirm-delete']) }}#}


{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        {#$(document).ready(function () {#}
            {#function addBusinessDays(date, daysToAdd) {#}
                {#var cnt = 0;#}
                {#var tmpDate = moment(date);#}
                {#while (cnt < daysToAdd) {#}
                    {#tmpDate = tmpDate.add('days', 1);#}
                    {#if (tmpDate.weekday() != moment().day("Sunday").weekday() && tmpDate.weekday() != moment().day("Saturday").weekday()) {#}
                        {#cnt = cnt + 1;#}
                    {#}#}
                {#}#}

                {#return tmpDate;#}
            {#}#}

            {#$('.labOption').on('click', function(){#}
                {#$('#radio_'+$(this).data('id')).prop('checked', true);#}
                {#$('#confirmLab').attr('data-id', $(this).attr('data-id'));#}
            {#});#}

            {#$('#add_recipe').on('click', function(){#}

                {#if($(this).hasClass('multiLabs')){#}
                    {#$("#chooseLabModal").modal('show');#}
                {#}#}
                {#else {#}
                    {#$('#reload_after').val(1);#}
                    {#$('#save_patient').trigger('click');#}
                {#}#}
            {#});#}

            {#$('#confirmLab').on('click', function(){#}
                {#// alert($(this).attr('data-id'));#}
                {#$("#chooseLabModal").modal('hide');#}
                {#$('#reload_after').val(1);#}
                {#$('#save_patient').trigger('click');#}
            {#});#}

            {#$('#lab_dentist').select2({#}
                {#theme: 'bootstrap',#}
                {#placeholder: "{{ 'Select dentist'|t }}"#}
            {#});#}

            {#var deliveryTime = addBusinessDays(moment(),{{ deliveryTime }}).format("DD-MM-YYYY");#}

            {#$('.datepicker-delivery-date').datepicker({#}
                {#format: 'dd-mm-yyyy',#}
                {#"autoclose": true,#}
                {#startDate: deliveryTime,#}
                {#language: 'nl'#}
            {#});#}

            {#$('#save_dentist').on('click', function(){#}
                {#$.ajax({#}
                    {#method: "post",#}
                    {#url: "/dentist/order/ajaxeditdentist",#}
                    {#data: {#}
                        {#order_id: $(this).attr('data-order'),#}
                        {#dentist_id: $('#lab_dentist').val()#}
                    {#},#}
                    {#dataType: 'json'#}
                {#}).success(function (data) {#}
                    {#if(data.status != "error"){#}
                        {#setTimeout(function () {#}
                            {#toastr.success(data.msg);#}
                            {#setTimeout(function () {#}
                                {#window.location.reload();#}
                            {#}, 1000);#}
                        {#}, 1000);#}
                    {#}#}
                    {#else {#}
                        {#setTimeout(function () {#}
                            {#toastr.error(data.msg);#}
                        {#}, 1000);#}
                    {#}#}
                {#});#}
            {#});#}

            {#$('#discuss_delivery').on('change', function(){#}
                {#if ($(this).is(':checked')) {#}
                    {#$(this).attr("checked", "checked");#}
                    {#$(this).val(1);#}
                {#}#}
                {#else {#}
                    {#$(this).val(0);#}
                    {#$(this).removeAttr("checked");#}
                {#}#}
            {#});#}

            {#var recipeId = 0;#}
            {#var orderId = 0;#}

            {#$('.deleteRecipe').on('click', function(){#}
                {#recipeId = $(this).attr('data-recipe');#}
                {#orderId = $(this).attr('data-order');#}
                {#$('#confirmDelete').modal('show');#}
            {#});#}

            {#$('.confirm-delete').on('click', function(){#}
                {#$('#confirmDelete').modal('hide');#}
                {#$.ajax({#}
                    {#method: "post",#}
                    {#url: "/dentist/order/deleterecipe",#}
                    {#data: {#}
                        {#recipeId: recipeId,#}
                        {#orderId: orderId#}
                    {#},#}
                    {#dataType: 'json'#}
                {#}).success(function (data) {#}
                    {#if(data.status != "error"){#}
                        {#setTimeout(function () {#}
                            {#toastr.success(data.msg);#}
                            {#setTimeout(function () {#}
                                {#window.location.reload();#}
                            {#}, 1000);#}
                        {#}, 1000);#}
                    {#}#}
                    {#else {#}
                        {#setTimeout(function () {#}
                            {#toastr.error(data.msg);#}
                        {#}, 1000);#}
                    {#}#}
                {#});#}
            {#});#}
        {#});#}
    </script>
{% endblock %}