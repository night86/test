{% extends "layouts/main.volt" %}
{% block title %} {{ "View client"|t }} {% endblock %}
{% block content %}
    <h3><a href="/lab/sales_client/"><i class="pe-7s-back"></i></a></h3>
    <fieldset class="form-group">
        <form id="addForm" action="/lab/sales_client/view/{{ organisation.getId() }}" method="post" enctype="multipart/form-data">
            <br />
            {% if _SERVER['HTTP_HOST'] == 'test. ' or _SERVER['HTTP_HOST'] == 'signadens.devv' %}
            <a href="/lab/sales_client/recipelist/{{ organisation.getId() }}" class="btn btn-info" style="float: right; margin: -10px 0 0 0;"><i class="pe-7s-folder"></i> {{ "Available recipes for this dentist"|t }}</a>
            {% endif %}
            <legend>{{ "General contact data"|t }}</legend>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="organisation[name]">{{ "Organisation name"|t }}&nbsp;</label>
                    <input id="organisation_name" name="organisation[name]" type="text" class="form-control" value="{{ organisation.getName() }}" required="required" disabled="disabled" tabindex="1" />
                    <p>&nbsp;</p>
                </div>
                <div class="form-group">
                    <label for="organisation[address]">{{ "Address"|t }}&nbsp;</label>
                    <input id="address" name="organisation[address]" type="text" class="form-control" value="{{ organisation.getAddress() }}" tabindex="4" disabled="disabled" />
                </div>
                <div class="form-group">
                    <label for="organisation[zipcode]">{{ "Zipcode"|t }}&nbsp;</label>
                    <input id="zipcode" name="organisation[zipcode]" type="text" class="form-control" value="{{ organisation.getZipcode() }}" tabindex="7" disabled="disabled" />
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="organisation[kvk_number]">{{ "KvK number"|t }}&nbsp;</label>
                    <input id="kvk_number" name="organisation[kvk_number]" type="text" title="{{ "You can search for the KvK number of the dentist at https://www.kvk.nl/"|t }}" class="form-control" value="{{ organisation.getKvkNumber() }}" required="required" tabindex="2" disabled="disabled" />
                    <p>{{ "You can search for the KvK number of the dentist at"|t }}: <a href="https://www.kvk.nl/" target="_blank">https://www.kvk.nl/</a></p>
                </div>
                <div class="form-group">
                    <label for="organisation[city]">{{ "City"|t }}&nbsp;</label>
                    <input id="city" name="organisation[city]" type="text" class="form-control" value="{{ organisation.getCity() }}" tabindex="5" disabled="disabled" />
                </div>
                <div class="form-group">
                    <label for="organisation[telephone]">{{ "Phone number"|t }}&nbsp;</label>
                    <input id="telephone" name="organisation[telephone]" type="text" class="form-control" value="{{ organisation.getTelephone() }}" tabindex="8" disabled="disabled" />
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="organisation[email]">{{ "General email address"|t }}&nbsp;</label>
                    <input id="general_email" name="organisation[email]" type="text" class="form-control" value="{{ organisation.getEmail() }}" required="required" tabindex="3" disabled="disabled" />
                    <p>&nbsp;</p>
                </div>

                <div class="form-group">
                    <label for="organisation[country]">{{ "Country"|t }}&nbsp;</label>
                    <select id="country" name="organisation[country_id]" class="select2-input form-control" tabindex="6" disabled="disabled">
                        <option></option>
                        {% for country in countryList %}
                            {% if country.id == organisation.getCountryId() %}
                                <option value="{{ country.id }}" selected="selected">{{ country.name }}</option>
                            {% else %}
                                <option value="{{ country.id }}">{{ country.name }}</option>
                            {% endif %}
                        {% endfor %}
                    </select>
                </div>
            </div>
        </div><br /><br /><br />

        <legend>{{ "Invoice information"|t }}</legend>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="invoice_info[address]">{{ "Address"|t }}&nbsp;</label>
                    <input id="invoice_address" name="invoice_info[address]" type="text" class="form-control" value="{% if organisation.InvoiceInfo %}{{ organisation.InvoiceInfo.getAddress() }}{% endif %}" tabindex="10" disabled="disabled" />
                </div>
                <div class="form-group">
                    <label for="invoice_info[country]">{{ "Country"|t }}&nbsp;</label>
                    <select id="invoice_country" name="invoice_info[country_id]" class="select2-input form-control" tabindex="13" disabled="disabled">
                        <option></option>
                        {% for country in countryList %}
                            {% if organisation.InvoiceInfo and country.id == organisation.InvoiceInfo.getCountryId() %}
                                <option value="{{ country.id }}" selected="selected">{{ country.name }}</option>
                            {% else %}
                                <option value="{{ country.id }}">{{ country.name }}</option>
                            {% endif %}
                        {% endfor %}
                    </select>
                </div>
                <div class="form-group">
                    <label for="invoice_info[telephone_admin]">{{ "Telephone number contact person administration"|t }}&nbsp;</label>
                    <input id="invoice_telephone_admin" name="invoice_info[telephone_admin]" type="text" class="form-control" tabindex="15" value="{% if organisation.InvoiceInfo %}{{ organisation.InvoiceInfo.getTelephoneAdmin() }}{% endif %}" disabled="disabled" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="invoice_info[zipcode]">{{ "Zipcode"|t }}&nbsp;</label>
                    <input id="invoice_zipcode" name="invoice_info[zipcode]" type="text" class="form-control" value="{% if organisation.InvoiceInfo %}{{ organisation.InvoiceInfo.getZipcode() }}{% endif %}" tabindex="11" disabled="disabled" />
                </div>
                <div class="form-group">
                    <label for="invoice_info[email]">{{ "Email address administration"|t }}&nbsp;</label>
                    <input id="invoice_email" name="invoice_info[email]" type="text" class="form-control" tabindex="13" value="{% if organisation.InvoiceInfo %}{{ organisation.InvoiceInfo.getEmail() }}{% endif %}" disabled="disabled" />
                </div>
                <div class="form-group">
                    <label for="invoice_info[bank_account]">{{ "Bank account number"|t }}&nbsp;</label>
                    <input id="invoice_bank_account" name="invoice_info[bank_account]" type="text" class="form-control" tabindex="16" value="{% if organisation.InvoiceInfo %}{{ organisation.InvoiceInfo.getBankAccount() }}{% endif %}" disabled="disabled" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="invoice_info[city]">{{ "City"|t }}&nbsp;</label>
                    <input id="invoice_city" name="invoice_info[city]" type="text" class="form-control" value="{% if organisation.InvoiceInfo %}{{ organisation.InvoiceInfo.getCity() }}{% endif %}" tabindex="12" disabled="disabled" />
                </div>
                <div class="form-group">
                    <label for="invoice_info[contact_admin]">{{ "Contact person administration"|t }}&nbsp;</label>
                    <input id="invoice_contact_admin" name="invoice_info[contact_admin]" type="text" class="form-control" tabindex="14" value="{% if organisation.InvoiceInfo %}{{ organisation.InvoiceInfo.getContactAdmin() }}{% endif %}" disabled="disabled" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="invoice_info[salutation]">{{ "Salutation"|t }}&nbsp;</label>
                    <textarea name="invoice_info[salutation]" class="form-control tinymce" style="width: 400px; height: 200px;" disabled="disabled">{% if organisation.InvoiceInfo %}{{ strip_tags(organisation.InvoiceInfo.getSalutation()) }}{% endif %}</textarea>
                </div>
            </div>
            <div class="col-md-4">
            </div>
            <div class="col-md-4">
            </div>
        </div><br /><br />

        <legend>{{ "Information about dentist"|t }}</legend>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="lab_dentist[client_preferences]">{{ "Client preferences"|t }}&nbsp;</label>
                    <textarea name="lab_dentist[client_preferences]" class="form-control tinymce" style="width: 400px; height: 200px;">{{ strip_tags(labDentistData.getClientPreferences()) }}</textarea>
                    <p>&nbsp;</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="lab_dentist[contract]">{{ "Processing contract"|t }}&nbsp;</label>
                    <input id="contract" name="lab_dentist[contract]" type="file" class="form-control" value="{{ labDentistData.getContract() }}" />
                    <p>{{ "You can download the"|t }} <a href="/uploads/contracts/Model_verwerkersovereenkomst.docx" target="_blank">{{ "concept of the contract"|t }}</a> {{ "and upload the signed version." }} {{ "This will also be visible to the dentist"|t }}</p>
                </div>
                <div class="form-group">
                    {% if labDentistData.getContract() is not null %}
                    <a href="/uploads/contracts/{{ labDentistData.getContract() }}" target="_blank">{{ labDentistData.getContract() }}</a>
                    {% endif %}
                </div>
                <div class="form-group">
                    <label for="lab_dentist[payment_arrangement]">{{ "Payment arrangement"|t }}&nbsp;</label>
                    <select id="payment_arrangement" name="lab_dentist[payment_arrangement]" class="form-control">
                        <option selected="selected">{{ "Select payment arrangement"|t }}</option>
                        {% for p in paymentArrangements %}
                            <option value="{{ p.id }}" {% if labDentistData is not null and labDentistData.payment_arrangement_id == p.id %}selected="selected"{% endif %}>{{ p.code }} - {{ p.description }}</option>
                        {% endfor %}
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="lab_dentist[client_number]">{{ "Client number"|t }}&nbsp;</label>
                    <input id="client_number" name="lab_dentist[client_number]" type="text" class="form-control" value="{% if labDentistData.getClientNumber() != 0 %}{{ labDentistData.getClientNumber() }}{% endif %}" tabindex="9" />
                </div>
            </div>
        </div><br /><br />

        <legend>{{ "Contact person(s)"|t }}</legend>
        <p class="pull-right">
            <a id="add-person" data-counter="{% if count(contactPersons) > 0 %}{{ count(contactPersons) }}{% else %}{{ "0" }}{% endif %}" class="btn-primary btn"><i class="pe-7s-plus"></i> {{ "Add new"|t }}</a>
        </p>

        <div class="row">
            <div class="col-md-12">
                <table id="contactPersons" class="basic-datatable table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>{{ "Name"|t }}</th>
                            <th>{{ "Phone"|t }}</th>
                            <th>{{ "Email"|t }}</th>
                            <th>{{ "Function"|t }}</th>
                            <th>{{ "Actions"|t }}</th>
                        </tr>
                    </thead>
                    <tbody class="person-body">
                    {% if count(contactPersons) > 0 %}
                    {% for index, person in contactPersons %}
                        <tr id="row_{{ index+1 }}" class="person-row">
                            <td id="name_{{ index+1 }}">{{ person.getName() }}</td>
                            <td id="phone_{{ index+1 }}">{{ person.getPhone() }}</td>
                            <td id="email_{{ index+1 }}">{{ person.getEmail() }}</td>
                            <td id="funct_{{ index+1 }}">{{ person.getFunction() }}</td>
                            <td width="25%"><a id="edit_{{ index+1 }}" class="btn btn-primary btn-sm edit-person" data-name="{{ person.getName() }}" data-phone="{{ person.getPhone() }}" data-email="{{ person.getEmail() }}" data-function="{{ person.getFunction() }}" data-counter="{{ index+1 }}">
                                    <i class="pe-7s-pen"></i>{{ "Edit"|t}}</a>
                                <a data-id="{{ person.getId() }}" class="btn btn-danger btn-sm remove-person" data-counter="{{ index+1 }}">
                                    <i class="pe-7s-close-circle"></i> {{ "Delete"|t }}</a>
                            </td>
                            <input id="nameval_{{ index+1 }}" type="hidden" value="{{ person.name }}" name="person_old[{{ index+1 }}][name]" />
                            <input id="phoneval_{{ index+1 }}" type="hidden" value="{{ person.phone }}" name="person_old[{{ index+1 }}][phone]" />
                            <input id="emailval_{{ index+1 }}" type="hidden" value="{{ person.email }}" name="person_old[{{ index+1 }}][email]" />
                            <input id="functval_{{ index+1 }}" type="hidden" value="{{ person.function }}" name="person_old[{{ index+1 }}][function]" />
                            <input id="idval_{{ index+1 }}" type="hidden" value="{{ person.id }}" name="person_old[{{ index+1 }}][id]" />
                        </tr>
                    {% endfor %}
                    {% endif %}
                    </tbody>
                </table>
                {% for person in contactPersons %}
                <input id="person_deleted_{{ person.getId() }}" type="hidden" name="person_deleted[{{ person.getId() }}]" />
                {% endfor %}
            </div>
        </div>
        {#
                    <br /><br />

                    <legend>{{ "Client preference per tariff code"|t }}</legend>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="pull-right">
                                <a id="add_tariff" data-counter="{% if count(tariff_preferences) > 1 %}{{ count(tariff_preferences) }}{% else %}1{% endif %}" class="btn-primary btn"><i class="pe-7s-plus"></i> {{ "Add new"|t }}</a>
                            </p>
                        </div>
                    </div>

                    <div id="tariff_template" class="row">

                    {% if lab_dentist.getClientPreferencesTariff() is not null %}

                        {% for index, tp in tariff_preferences %}

                            <div id="rowtariff_{{ index }}" class="form-group">
                                <div class="col-md-3">
                                    <select id="tariff_{{ index }}" class="form-control select2-input" name="client_tariff[{{ index }}][code]">
                                        <option value="0">{{ "Select tariff code"|t }}</option>
                                        {% for tariff in codes %}
                                            <option value="{{ tariff.id }}" {% if tariff.id == tp['code'] %}selected="selected"{% endif %}>{{ tariff.getCode() ~ ' - ' ~ tariff.getDescription() }}</option>
                                        {% endfor %}
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input class="form-control" name="client_tariff[{{ index }}][pref]" placeholder="{{ "Enter the client preference"|t }}" value="{% if tp['pref'] %}{{ tp['pref'] }}{% endif %}" />
                                </div>
                                {% if count(tariff_preferences) > 1 and index > 0 %}
                                    <div class="col-md-1"><a data-id="{{ index }}" class="btn btn-danger btn-sm remove-tariff"><i class="pe-7s-close-circle"></i> Delete</a></div>
                                    <div class="col-md-5"></div>
                                {% else %}
                                    <div class="col-md-6"></div>
                                {% endif %}
                                <div class="col-md-12">&nbsp;</div>
                            </div>

                        {% endfor %}

                    {% else %}

                        <div class="form-group">
                            <div class="col-md-3">
                                <select id="tariff_0" class="form-control select2-input" name="client_tariff[0][code]">
                                    <option value="0">{{ "Select tariff code"|t }}</option>
                                    {% for tariff in codes %}
                                        <option value="{{ tariff.id }}">{{ tariff.getCode() ~ ' - ' ~ tariff.getDescription() }}</option>
                                    {% endfor %}
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input class="form-control" name="client_tariff[0][pref]" placeholder="{{ "Enter the client preference"|t }}" />
                            </div>
                            <div class="col-md-6"></div>
                            <div class="col-md-12">&nbsp;</div>
                        </div>

                    {% endif %}
                    </div>

                    <br class="end-tariff" /><br />

                    <legend>{{ "Client preference per recipe"|t }}</legend>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="pull-right">
                                <a id="add_recipe" data-counter="{% if count(recipe_preferences) > 1 %}{{ count(recipe_preferences) }}{% else %}1{% endif %}" class="btn-primary btn"><i class="pe-7s-plus"></i> {{ "Add new"|t }}</a>
                            </p>
                        </div>
                    </div>

                    <div id="recipe_template" class="row">

                        {% if lab_dentist.getClientPreferencesRecipe() is not null %}

                            {% for index, rp in recipe_preferences %}

                                <div id="rowrecipe_{{ index }}" class="form-group">
                                    <div class="col-md-3">
                                        <select id="recipe_{{ index }}" class="form-control select2-input" name="client_recipe[{{ index }}][code]">
                                            <option value="0">{{ "Choose a recipe"|t }}</option>
                                            {% for rec in recipes %}
                                                <option value="{{ rec.id }}" {% if rec.id == rp['code'] %}selected="selected"{% endif %}>{{ rec.getCode() ~ ' - ' ~ rec.getName() }}</option>
                                            {% endfor %}
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input class="form-control" name="client_recipe[{{ index }}][pref]" placeholder="{{ "Enter the client preference"|t }}" value="{% if rp['pref'] is not null %}{{ rp['pref'] }}{% endif %}" />
                                    </div>
                                    {% if count(recipe_preferences) > 1 and index > 0 %}
                                        <div class="col-md-1"><a data-id="{{ index }}" class="btn btn-danger btn-sm remove-recipe"><i class="pe-7s-close-circle"></i> Delete</a></div>
                                        <div class="col-md-5"></div>
                                    {% else %}
                                        <div class="col-md-6"></div>
                                    {% endif %}
                                    <div class="col-md-12">&nbsp;</div>
                                </div>

                            {% endfor %}

                        {% else %}

                            <div class="form-group">
                                <div class="col-md-3">
                                    <select id="recipe_0" class="form-control select2-input" name="client_recipe[0][code]">
                                        <option value="0">{{ "Choose a recipe"|t }}</option>
                                        {% for rec in recipes %}
                                            <option value="{{ rec.id }}">{{ rec.getCode() ~ ' - ' ~ rec.getName() }}</option>
                                        {% endfor %}
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input class="form-control" name="client_recipe[0][pref]" placeholder="{{ "Enter the client preference"|t }}" />
                                </div>
                                <div class="col-md-6"></div>
                                <div class="col-md-12">&nbsp;</div>
                            </div>

                        {% endif %}
                    </div>#}

        <br /><br />

        <legend>{{ "Locations"|t }}</legend>

        <div class="row">
            <div class="col-md-12">
                <table id="locations" class="basic-datatable table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>{{ "Location name"|t }}</th>
                        <th>{{ "Address"|t }}</th>
                        <th>{{ "Postal code"|t }}</th>
                        <th>{{ "City"|t }}</th>
                        <th>{{ "Country"|t }}</th>
                        <th>{{ "Telephone number"|t }}</th>
                        <th>{{ "Client number"|t }}</th>
                    </tr>
                    </thead>
                    <tbody class="location-body">
                    {% if count(locations) > 0 %}
                        {% for index, loc in locations %}
                            <tr id="rowLoc_{{ index+1 }}" class="location-row">
                                <td id="name_{{ index+1 }}">{{ loc.getName() }}</td>
                                <td id="addr_{{ index+1 }}">{{ loc.getAddress() }}</td>
                                <td id="zipc_{{ index+1 }}">{{ loc.getZipcode() }}</td>
                                <td id="city_{{ index+1 }}">{{ loc.getCity() }}</td>
                                <td id="coun_{{ index+1 }}">{{ loc.Country.getName() }}</td>
                                <td id="tele_{{ index+1 }}">{{ loc.getTelephone() }}</td>
                                {#<td id="clie_{{ index+1 }}">{{ clientNumber[loc.getId()] }}</td>#}
                                <td id="clie_{{ index+1 }}"><input type="text" name="client_number[{{ loc.getId() }}]" value="{{ clientNumber[loc.getId()] }}" /></td>
                                <input id="idval_{{ index+1 }}" type="hidden" value="{{ loc.getId() }}" name="location[{{ index+1 }}][id]" />
                            </tr>
                        {% endfor %}
                    {% endif %}
                    </tbody>
                </table>
            </div>
        </div><br /><br />



        <div class="row">
            <div class="col-md-12">
                <div class="form-group">&nbsp;</div>
                <div class="form-group">
                    <label for="">&nbsp;</label>
                    <button id="confirmForm" type="submit" class="btn btn-primary pull-right"><i class="pe-7s-diskette"></i> {{ "Save"|t }}</button>
                </div>
            </div>
        </div>
    </form>
    </fieldset>

    {{ partial("modals/confirmGeneral", ['id': 'confirm-delete-modal', 'title': "Delete"|t, 'content': "Are you sure you want to delete?"|t, "additionalClass": "confirmDelete"]) }}
    {{ partial("modals/addContactPerson", ['id': 'add-modal', 'title': "Add contact person"|t]) }}
    {{ partial("modals/editContactPerson", ['id': 'edit-modal', 'title': "Edit contact person"|t]) }}

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        $(function(){

            $(window).load(function(){
                $('.dataTables_empty').parent().remove();
                // $('#locations_wrapper').css('background-color', '#CCC');
            });

            $('#add-person').on('click', function(){
                $('#personName').val(null);
                $('#personPhone').val(null);
                $('#personEmail').val(null);
                $('#personFunction').val(null);
                $('#add-modal').modal('show');
            });

            $(document).on('click', '.edit-person', function(){
                $('#personNameEdit').val($(this).attr('data-name'));
                $('#personPhoneEdit').val($(this).attr('data-phone'));
                $('#personEmailEdit').val($(this).attr('data-email'));
                $('#personFunctionEdit').val($(this).attr('data-function'));
                $('#confirmButtonEdit').attr('data-counter', $(this).attr('data-counter'));
                $('#edit-modal').modal('show');

            });

            $(document).on('click', '.edit-new-person', function(){
                $('#personNameEdit').val($(this).attr('data-name'));
                $('#personPhoneEdit').val($(this).attr('data-phone'));
                $('#personEmailEdit').val($(this).attr('data-email'));
                $('#personFunctionEdit').val($(this).attr('data-function'));
                $('#confirmButtonEdit').attr('data-counter', $(this).attr('data-counter'));
                $('#edit-modal').modal('show');

            });

            $(document).on('click', '.remove-person', function(){
                $('#confirm-delete-modal').modal('show');
                $('.confirmDelete').attr({ 'data-id': $(this).attr('data-id'), 'data-counter': $(this).attr('data-counter'), 'data-type': 'old-person'});
            });

            $(document).on('click', '.remove-new-person', function(){
                $('#confirm-delete-modal').modal('show');
                $('.confirmDelete').attr({ 'data-id': $(this).attr('data-id'), 'data-counter': $(this).attr('data-counter'), 'data-type': 'new-person'});
            });

            $('#confirmButtonAdd').on('click', function(){

                var name = $('#personName').val();
                var phone = $('#personPhone').val();
                var email = $('#personEmail').val();
                var funct = $('#personFunction').val();

                if(name === '' || phone === '' || email === '' || funct === ''){
                    toastr.error('{{ 'Fields cannot be empty.'|t }}');
                    return;
                }

                var counter = parseInt($('#add-person').attr('data-counter')) + 1;
                var content = '<tr id="newrow_'+counter+'" class="person-row">' +
                    '<td id="name_'+counter+'">'+name+'</td>' +
                    '<td id="phone_'+counter+'">'+phone+'</td>' +
                    '<td id="email_'+counter+'">'+email+'</td>' +
                    '<td id="funct_'+counter+'">'+funct+'</td>' +
                    '<td><a id="edit_'+counter+'" class="btn btn-primary btn-sm edit-new-person" data-name="'+name+'" data-phone="'+phone+'" data-email="'+email+'" data-function="'+funct+'" data-counter="'+counter+'"><i class="pe-7s-pen"></i>{{ "Edit"|t}}</a>' +
                    '<a class="btn btn-danger btn-sm remove-new-person" data-counter="'+counter+'"><i class="pe-7s-close-circle"></i> {{"Delete"|t}}</a></td>' +
                    '<input id="nameval_'+counter+'" type="hidden" value="'+name+'" name="person_added['+counter+'][name]" />'+
                    '<input id="phoneval_'+counter+'" type="hidden" value="'+phone+'" name="person_added['+counter+'][phone]" />'+
                    '<input id="emailval_'+counter+'" type="hidden" value="'+email+'" name="person_added['+counter+'][email]" />'+
                    '<input id="functval_'+counter+'" type="hidden" value="'+funct+'" name="person_added['+counter+'][function]" /></tr>';
                $('.person-body').append(content);
                $('#add-person').attr('data-counter', counter);
                $('#add-modal').modal('hide');
            });

            $('#confirmButtonEdit').on('click', function(){

                var name = $('#personNameEdit').val();
                var phone = $('#personPhoneEdit').val();
                var email = $('#personEmailEdit').val();
                var funct = $('#personFunctionEdit').val();

                if(name === '' || phone === '' || email === '' || funct === ''){
                    toastr.error('{{ 'Fields cannot be empty.'|t }}');
                    return;
                }

                $('#name_'+$(this).attr('data-counter')).html(name);
                $('#nameval_'+$(this).attr('data-counter')).val(name);
                $('#phone_'+$(this).attr('data-counter')).html(phone);
                $('#phoneval_'+$(this).attr('data-counter')).val(phone);
                $('#email_'+$(this).attr('data-counter')).html(email);
                $('#emailval_'+$(this).attr('data-counter')).val(email);
                $('#funct_'+$(this).attr('data-counter')).html(funct);
                $('#functval_'+$(this).attr('data-counter')).val(funct);
                $('#edit_'+$(this).attr('data-counter')).attr({'data-name': name, 'data-phone': phone, 'data-email': email, 'data-function': funct});
                $('#edit-modal').modal('hide');
            });

            $('.confirmDelete').on('click', function(){

                $('#confirm-delete-modal').modal('hide');

                if($(this).attr('data-type') == 'new-person'){
                    $('#newrow_'+$(this).attr('data-counter')).remove();
                    $('#add-person').attr('data-counter', parseInt($('#add-person').attr('data-counter')) - 1);
                }

                if($(this).attr('data-type') == 'new-location'){
                    $('#newrowLoc_'+$(this).attr('data-counter')).remove();
                    $('#add-location').attr('data-counter', parseInt($('#add-location').attr('data-counter')) - 1);
                }
            });
        });
    </script>
{% endblock %}