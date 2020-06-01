{% extends "layouts/main.volt" %}
{% block title %} {{ "Edit client"|t }} {% endblock %}
{% block content %}
    <h3><a href="/lab/sales_client/"><i class="pe-7s-back"></i></a></h3>
    <fieldset class="form-group">
        <form id="addForm" action="/lab/sales_client/edit/{{ organisation.getId() }}" method="post" enctype="multipart/form-data">
            <br />
            {% if _SERVER['HTTP_HOST'] == 'test. ' or _SERVER['HTTP_HOST'] == 'signadens.devv' %}
                <a href="/lab/sales_client/recipelist/{{ organisation.getId() }}" class="btn btn-info" style="float: right; margin: -10px 0 0 0;"><i class="pe-7s-folder"></i> {{ "Available recipes for this dentist"|t }}</a>
            {% endif %}
            <legend>{{ "General contact data"|t }}</legend>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="organisation[name]">{{ "Organisation name"|t }}&nbsp;</label>
                        <input id="organisation_name" name="organisation[name]" type="text" class="form-control" value="{{ organisation.getName() }}" {% if isUserActive is true %}readonly="readonly"{% endif %} tabindex="1" maxlength="150" />
                        <p>&nbsp;</p>
                    </div>
                    <div class="form-group">
                        <label for="organisation[address]">{{ "Address"|t }}&nbsp;</label>
                        <input id="address" name="organisation[address]" type="text" class="form-control" value="{{ organisation.getAddress() }}" {% if isUserActive is true %}readonly="readonly"{% endif %} tabindex="4" maxlength="255" />
                    </div>
                    <div class="form-group">
                        <label for="organisation[zipcode]">{{ "Zipcode"|t }}&nbsp;</label>
                        <input id="zipcode" name="organisation[zipcode]" type="text" class="form-control" value="{{ organisation.getZipcode() }}" {% if isUserActive is true %}readonly="readonly"{% endif %} tabindex="7" maxlength="255" />
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="organisation[kvk_number]">{{ "KvK number"|t }}&nbsp;</label>
                        <input id="kvk_number" name="organisation[kvk_number]" type="text" title="{{ "You can search for the KvK number of the dentist at https://www.kvk.nl/"|t }}" class="form-control" value="{{ organisation.getKvkNumber() }}" {% if organisation.getKvkNumber() is not null %}readonly="readonly"{% endif %} required="required" tabindex="2" maxlength="8" />
                        <p>{{ "You can search for the KvK number of the dentist at"|t }}: <a href="https://www.kvk.nl/" target="_blank">https://www.kvk.nl/</a></p>
                    </div>
                    <div class="form-group">
                        <label for="organisation[city]">{{ "City"|t }}&nbsp;</label>
                        <input id="city" name="organisation[city]" type="text" class="form-control" value="{{ organisation.getCity() }}" {% if isUserActive is true %}readonly="readonly"{% endif %} tabindex="5" maxlength="255" />
                    </div>
                    <div class="form-group">
                        <label for="organisation[telephone]">{{ "Phone number"|t }}&nbsp;</label>
                        <input id="telephone" name="organisation[telephone]" type="text" class="form-control" value="{{ organisation.getTelephone() }}" {% if isUserActive is true %}readonly="readonly"{% endif %} tabindex="8" maxlength="255" />
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="organisation[email]">{{ "General email address"|t }}&nbsp;</label>
                        <input id="general_email" name="organisation[email]" type="text" class="form-control" value="{{ organisation.getEmail() }}" {% if isUserActive is true %}readonly="readonly"{% endif %} required="required" tabindex="3" maxlength="150" />
                        <p>&nbsp;</p>
                    </div>

                    <div class="form-group">
                        <label for="organisation[country]">{{ "Country"|t }}&nbsp;</label>
                        <select id="country" name="organisation[country_id]" class="select2-input form-control" tabindex="6" {% if isUserActive is true %}disabled="disabled"{% endif %}>
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
                        <input id="invoice_address" name="invoice_info[address]" type="text" class="form-control" value="{% if organisation.InvoiceInfo %}{{ organisation.InvoiceInfo.getAddress() }}{% endif %}" {% if isUserActive is true %}disabled="disabled"{% endif %} tabindex="10" maxlength="255" />
                    </div>
                    <div class="form-group">
                        <label for="invoice_info[country]">{{ "Country"|t }}&nbsp;</label>
                        <select id="invoice_country" name="invoice_info[country_id]" class="select2-input form-control" tabindex="13" {% if isUserActive is true %}disabled="disabled"{% endif %}>
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
                        <input id="invoice_telephone_admin" name="invoice_info[telephone_admin]" type="text" class="form-control" tabindex="15" value="{% if organisation.InvoiceInfo %}{{ organisation.InvoiceInfo.getTelephoneAdmin() }}{% endif %}" {% if isUserActive is true %}disabled="disabled"{% endif %} maxlength="255" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="invoice_info[zipcode]">{{ "Zipcode"|t }}&nbsp;</label>
                        <input id="invoice_zipcode" name="invoice_info[zipcode]" type="text" class="form-control" value="{% if organisation.InvoiceInfo %}{{ organisation.InvoiceInfo.getZipcode() }}{% endif %}" {% if isUserActive is true %}disabled="disabled"{% endif %} tabindex="11" maxlength="255" />
                    </div>
                    <div class="form-group">
                        <label for="invoice_info[email]">{{ "Email address administration"|t }}&nbsp;</label>
                        <input id="invoice_email" name="invoice_info[email]" type="text" class="form-control" tabindex="13" value="{% if organisation.InvoiceInfo %}{{ organisation.InvoiceInfo.getEmail() }}{% endif %}" {% if isUserActive is true %}disabled="disabled"{% endif %} maxlength="255" />
                    </div>
                    <div class="form-group">
                        <label for="invoice_info[bank_account]">{{ "Bank account number"|t }}&nbsp;</label>
                        <input id="invoice_bank_account" name="invoice_info[bank_account]" type="text" class="form-control" tabindex="16" value="{% if organisation.InvoiceInfo %}{{ organisation.InvoiceInfo.getBankAccount() }}{% endif %}" {% if isUserActive is true %}disabled="disabled"{% endif %} maxlength="255" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="invoice_info[city]">{{ "City"|t }}&nbsp;</label>
                        <input id="invoice_city" name="invoice_info[city]" type="text" class="form-control" value="{% if organisation.InvoiceInfo %}{{ organisation.InvoiceInfo.getCity() }}{% endif %}" {% if isUserActive is true %}disabled="disabled"{% endif %} tabindex="12" maxlength="255" />
                    </div>
                    <div class="form-group">
                        <label for="invoice_info[contact_admin]">{{ "Contact person administration"|t }}&nbsp;</label>
                        <input id="invoice_contact_admin" name="invoice_info[contact_admin]" type="text" class="form-control" tabindex="14" value="{% if organisation.InvoiceInfo %}{{ organisation.InvoiceInfo.getContactAdmin() }}{% endif %}" {% if isUserActive is true %}disabled="disabled"{% endif %} maxlength="255" />
                    </div>
                </div>
            </div><br /><br />

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="invoice_info[salutation]">{{ "Salutation"|t }}&nbsp;</label>
                        <textarea name="invoice_info[salutation]" class="form-control tinymce" style="width: 400px; height: 200px;" {% if isUserActive is true %}disabled="disabled"{% endif %}>{% if organisation.InvoiceInfo %}{{ organisation.InvoiceInfo.getSalutation() }}{% endif %}</textarea>
                    </div>
                </div>
                <div class="col-md-4">
                </div>
                <div class="col-md-4">
                </div>
            </div><br /><br /><br />

            <legend>{{ "Information about dentist"|t }}</legend>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="lab_dentist[client_preferences]">{{ "Client preferences"|t }}&nbsp;</label>
                        <textarea name="lab_dentist[client_preferences]" class="form-control tinymce" style="width: 400px; height: 200px;">{{ lab_dentist.getClientPreferences() }}</textarea>
                        <p>&nbsp;</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="lab_dentist[contract]">{{ "Processing contract"|t }}&nbsp;</label>
                        <input id="contract" name="lab_dentist[contract]" type="file" class="form-control" value="{{ lab_dentist.getContract() }}" />
                        <p>{{ "You can download the"|t }} <a href="/uploads/contracts/Model_verwerkersovereenkomst.docx" target="_blank">{{ "concept of the contract"|t }}</a> {{ "and upload the signed version." }} {{ "This will also be visible to the dentist"|t }}</p>
                    </div>
                    <div class="form-group">
                        <a href="/uploads/contracts/{{ lab_dentist.getContract() }}" target="_blank">{{ lab_dentist.getContract() }}</a>
                        {% if lab_dentist.getContract() is not null %}
                            <a href="/lab/sales_client/deletecontract/{{ lab_dentist.getId() }}" class="btn btn-danger">{{ "Remove"|t }}</a>
                        {% endif %}
                    </div>
                    <div class="form-group">
                        <label for="lab_dentist[payment_arrangement]">{{ "Payment arrangement"|t }}&nbsp;</label>
                        <select id="payment_arrangement" name="lab_dentist[payment_arrangement]" class="form-control">
                            {% for p in paymentArrangements %}
                            <option value="{{ p.id }}"{% if p.id == lab_dentist.payment_arrangement_id %}selected="selected"{% endif %}>{{ p.code }} - {{ p.description }}</option>
                            {% endfor %}
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="lab_dentist[client_number]">{{ "Client number"|t }}&nbsp;</label>
                        <input id="client_number" name="lab_dentist[client_number]" type="text" class="form-control" value="{% if lab_dentist.getClientNumber() != 0 %}{{ lab_dentist.getClientNumber() }}{% endif %}" tabindex="9" maxlength="255" />
                    </div>
                </div>
            </div>

            <br /><br />

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
            </div><br /><br /><br />

            <legend>{{ "Locations"|t }}</legend>
            {% if isUserActive is not true %}
            <p class="pull-right">
                <a id="add-location" data-counter="{% if count(locations) > 0 %}{{ count(locations) }}{% else %}{{ "0" }}{% endif %}" class="btn-primary btn"><i class="pe-7s-plus"></i> {{ "Add new location"|t }}</a>
            </p>
            {% endif %}

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
                            {% if isUserActive is not true %}
                            <th>{{ "Actions"|t }}</th>
                            {% endif %}
                        </tr>
                        </thead>
                        <tbody class="location-body">
                        {% if locations %}
                            {% for index, loc in locations %}
                                <tr id="rowLoc_{{ index+1 }}" class="location-row">
                                    <td id="nameLoc_{{ index+1 }}">{{ loc.getName() }}</td>
                                    <td id="addrLoc_{{ index+1 }}">{{ loc.getAddress() }}</td>
                                    <td id="zipcLoc_{{ index+1 }}">{{ loc.getZipcode() }}</td>
                                    <td id="cityLoc_{{ index+1 }}">{{ loc.getCity() }}</td>
                                    <td id="counLoc_{{ index+1 }}">{{ loc.Country.getName() }}</td>
                                    <td id="teleLoc_{{ index+1 }}">{{ loc.getTelephone() }}</td>
                                    <td id="clieLoc_{{ index+1 }}"><input type="text" name="client_number[{{ loc.getId() }}]" value="{{ clientNumber[loc.getId()] }}" /></td>
                                    {% if isUserActive is not true %}
                                    <td width="25%">
                                            <a id="editLoc_{{ index+1 }}" class="btn btn-primary btn-sm edit-location"
                                               data-name="{{ loc.getName() }}"
                                               data-addr="{{ loc.getAddress() }}"
                                               data-zipc="{{ loc.getZipcode() }}"
                                               data-city="{{ loc.getCity() }}"
                                               data-coun="{{ loc.getCountryId() }}"
                                               data-tele="{{ loc.getTelephone() }}"
                                               data-clie="{{ clientNumber[loc.getId()] }}"
                                               data-counter="{{ index+1 }}">
                                                <i class="pe-7s-pen"></i>{{ "Edit"|t}}</a>
                                            {% if loc.getDeletedAt() is null and loc.getDeletedBy() is null %}
                                            <a data-id="{{ loc.getId() }}" class="btn btn-danger btn-sm disable-location" data-counter="{{ index+1 }}"><i class="pe-7s-close-circle"></i> {{ "Deactivate"|t }}</a>
                                            {% else %}
                                            <a data-id="{{ loc.getId() }}" class="btn btn-success btn-sm enable-location" data-counter="{{ index+1 }}"><i class="pe-7s-play"></i> {{ "Activate"|t }}</a>
                                            {% endif %}
                                    </td>
                                    {% endif %}
                                    <input id="nameLocval_{{ index+1 }}" type="hidden" value="{{ loc.getName() }}" name="location_old[{{ index+1 }}][name]" />
                                    <input id="addrLocval_{{ index+1 }}" type="hidden" value="{{ loc.getAddress() }}" name="location_old[{{ index+1 }}][address]" />
                                    <input id="zipcLocval_{{ index+1 }}" type="hidden" value="{{ loc.getZipcode() }}" name="location_old[{{ index+1 }}][zipcode]" />
                                    <input id="cityLocval_{{ index+1 }}" type="hidden" value="{{ loc.getCity() }}" name="location_old[{{ index+1 }}][city]" />
                                    <input id="counLocval_{{ index+1 }}" type="hidden" value="{{ loc.getCountryId() }}" name="location_old[{{ index+1 }}][country_id]" />
                                    <input id="teleLocval_{{ index+1 }}" type="hidden" value="{{ loc.getTelephone() }}" name="location_old[{{ index+1 }}][telephone]" />
                                    {#<input id="clieLocval_{{ index+1 }}" type="hidden" value="{{ loc.getClientNumber() }}" name="location_old[{{ index+1 }}][client_number]" />#}
                                    <input id="idLocval_{{ index+1 }}" type="hidden" value="{{ loc.getId() }}" name="location_old[{{ index+1 }}][id]" />
                                </tr>
                            {% endfor %}
                        {% endif %}
                        </tbody>
                    </table>
                    {% if locations %}
                        {% for loc in locations %}
                            <input id="location_deleted_{{ loc.getId() }}" type="hidden" name="location_deleted[{{ loc.getId() }}]" value="{% if loc.getDeletedAt() is null and loc.getDeletedBy() is null %}0{% else %}1{% endif %}" />
                        {% endfor %}
                    {% endif %}
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
    {{ partial("modals/addLocation", ['id': 'add-location-modal', 'title': "Add location"|t]) }}
    {{ partial("modals/editLocation", ['id': 'edit-location-modal', 'title': "Edit location"|t]) }}
{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        $(function(){
            tinymce.init({
                selector: '.tinymce',
                language_url: '/js/tinymce/langs/nl.js',
                plugins: "link",
                height: 300,
                selection_toolbar: 'link bold italic | quicklink h2 h3 blockquote',
                menu: {}
            });

            $(window).load(function(){
                $('.dataTables_empty').parent().remove();
            });

            $('#country').select2({
                theme: "bootstrap",
                placeholder: "{{ "Please select a country"|t }}"
            });

            $('#add-person').on('click', function(){
                $('#personName').val(null);
                $('#personPhone').val(null);
                $('#personEmail').val(null);
                $('#personFunction').val(null);
                $('#add-modal').modal('show');
            });

            $('#add-location').on('click', function(){
                $('#locationName').val(null);
                $('#locationAddress').val(null);
                $('#locationZipcode').val(null);
                $('#locationCity').val(null);
                $('#locationCountry').val(null);
                $('#locationTelephone').val(null);
                $('#locationClientNumber').val(null);
                $('#add-location-modal').modal('show');
            });

            $(document).on('click', '.edit-person', function(){
                $('#personNameEdit').val($(this).attr('data-name'));
                $('#personPhoneEdit').val($(this).attr('data-phone'));
                $('#personEmailEdit').val($(this).attr('data-email'));
                $('#personFunctionEdit').val($(this).attr('data-function'));
                $('#confirmButtonEdit').attr('data-counter', $(this).attr('data-counter'));
                $('#edit-modal').modal('show');

            });

            $(document).on('click', '.edit-location', function(){
                $('#locationNameEdit').val($(this).attr('data-name'));
                $('#locationAddressEdit').val($(this).attr('data-addr'));
                $('#locationZipcodeEdit').val($(this).attr('data-zipc'));
                $('#locationCityEdit').val($(this).attr('data-city'));
                $('#locationCountryEdit').val($(this).attr('data-coun'));
                $('#locationTelephoneEdit').val($(this).attr('data-tele'));
                $('#locationClientNumberEdit').val($(this).attr('data-clie'));
                $('#confirmEditLocation').attr('data-counter', $(this).attr('data-counter'));
                $('#edit-location-modal').modal('show');

            });

            $(document).on('click', '.remove-person', function(){
                $('#confirm-delete-modal').modal('show');
                $('.confirmDelete').attr({ 'data-id': $(this).attr('data-id'), 'data-counter': $(this).attr('data-counter'), 'data-type': 'old-person'});
            });

            $(document).on('click', '.disable-location', function(){

                $('#location_deleted_'+$(this).attr('data-id')).val(1);
                $(this).removeClass('disable-location btn-danger');
                $(this).addClass('enable-location btn-success');
                $(this).html('<i class="pe-7s-play"></i> {{ "Activate"|t }}')
            });

            $(document).on('click', '.enable-location', function(){

                $('#location_deleted_'+$(this).attr('data-id')).val(0);
                $(this).removeClass('enable-location btn-success');
                $(this).addClass('disable-location btn-danger');
                $(this).html('<i class="pe-7s-close-circle"></i> {{ "Deactivate"|t }}')
            });

            $(document).on('click', '.remove-new-person', function(){
                $('#confirm-delete-modal').modal('show');
                $('.confirmDelete').attr({ 'data-id': $(this).attr('data-id'), 'data-counter': $(this).attr('data-counter'), 'data-type': 'new-person'});
            });

            $(document).on('click', '.remove-new-location', function(){
                $('#confirm-delete-modal').modal('show');
                $('.confirmDelete').attr({ 'data-id': $(this).attr('data-id'), 'data-counter': $(this).attr('data-counter'), 'data-type': 'new-location'});
            });

            $('.confirmDelete').on('click', function(){
                $('#confirm-delete-modal').modal('hide');

                if($(this).attr('data-type') == 'old-person'){
                    $('#row_'+$(this).attr('data-counter')).remove();
                    $('#person_deleted_'+$(this).attr('data-id')).val(1);
                }

                if($(this).attr('data-type') == 'new-person'){
                    $('#newrow_'+$(this).attr('data-counter')).remove();
                }

                if($(this).attr('data-type') == 'new-location'){
                    $('#newrowLoc_'+$(this).attr('data-counter')).remove();
                }

                if($(this).attr('data-type') == 'old-person' || $(this).attr('data-type') == 'new-person'){
                    $('#add-person').attr('data-counter', parseInt($('#add-person').attr('data-counter')) - 1);
                }
                else {
                    $('#add-location').attr('data-counter', parseInt($('#add-location').attr('data-counter')) - 1);
                }
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

            $('#confirmEditLocation').on('click', function(){

                var name = $('#locationNameEdit').val();
                var addr = $('#locationAddressEdit').val();
                var zipc = $('#locationZipcodeEdit').val();
                var city = $('#locationCityEdit').val();
                var coun = $('#locationCountryEdit').val();
                var ctex = $('#locationCountryEdit option:selected').html();
                var tele = $('#locationTelephoneEdit').val();
                var clie = $('#locationClientNumberEdit').val();

                if(name === '' || coun === ''){
                    toastr.error('{{ 'Fields cannot be empty.'|t }}');
                    return;
                }

                $('#nameLoc_'+$(this).attr('data-counter')).html(name);
                $('#nameLocval_'+$(this).attr('data-counter')).val(name);
                $('#addrLoc_'+$(this).attr('data-counter')).html(addr);
                $('#addrLocval_'+$(this).attr('data-counter')).val(addr);
                $('#zipcLoc_'+$(this).attr('data-counter')).html(zipc);
                $('#zipcLocval_'+$(this).attr('data-counter')).val(zipc);
                $('#cityLoc_'+$(this).attr('data-counter')).html(city);
                $('#cityLocval_'+$(this).attr('data-counter')).val(city);
                $('#counLoc_'+$(this).attr('data-counter')).html(ctex);
                $('#counLocval_'+$(this).attr('data-counter')).val(coun);
                $('#teleLoc_'+$(this).attr('data-counter')).html(tele);
                $('#teleLocval_'+$(this).attr('data-counter')).val(tele);
                $('#clieLoc_'+$(this).attr('data-counter')).html(clie);
                $('#clieLocval_'+$(this).attr('data-counter')).val(clie);
                $('#editLoc_'+$(this).attr('data-counter')).attr({'data-name': name, 'data-addr': addr, 'data-zipc': zipc, 'data-city': city, 'data-coun': coun, 'data-tele': tele, 'data-clie': clie});
                $('#edit-location-modal').modal('hide');
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
                    '<td><a id="edit_'+counter+'" class="btn btn-primary btn-sm edit-person" data-name="'+name+'" data-phone="'+phone+'" data-email="'+email+'" data-function="'+funct+'" data-counter="'+counter+'"><i class="pe-7s-pen"></i>{{ "Edit"|t}}</a>' +
                    '<a class="btn btn-danger btn-sm remove-new-person" data-counter="'+counter+'"><i class="pe-7s-close-circle"></i> {{"Delete"|t}}</a></td>' +
                    '<input id="nameval_'+counter+'" type="hidden" value="'+name+'" name="person_added['+counter+'][name]" />'+
                    '<input id="phoneval_'+counter+'" type="hidden" value="'+phone+'" name="person_added['+counter+'][phone]" />'+
                    '<input id="emailval_'+counter+'" type="hidden" value="'+email+'" name="person_added['+counter+'][email]" />'+
                    '<input id="functval_'+counter+'" type="hidden" value="'+funct+'" name="person_added['+counter+'][function]" /></tr>';
                $('.person-body').append(content);
                $('#add-person').attr('data-counter', counter);
                $('#add-modal').modal('hide');
            });

            $('#confirmAddLocation').on('click', function(){

                var name = $('#locationName').val();
                var addr = $('#locationAddress').val();
                var zipc = $('#locationZipcode').val();
                var city = $('#locationCity').val();
                var coun = $('#locationCountry').val();
                var ctex = $('#locationCountry option:selected').html();
                var tele = $('#locationTelephone').val();
                var clie = $('#locationClientNumber').val();

                if(name === '' || coun === ''){
                    toastr.error('{{ 'Fields cannot be empty.'|t }}');
                    return;
                }

                var counter = parseInt($('#add-location').attr('data-counter')) + 1;
                var content = '<tr id="newrowLoc_'+counter+'" class="location-row">' +
                    '<td id="nameLoc_'+counter+'">'+name+'</td>' +
                    '<td id="addrLoc_'+counter+'">'+addr+'</td>' +
                    '<td id="zipcLoc_'+counter+'">'+zipc+'</td>' +
                    '<td id="cityLoc_'+counter+'">'+city+'</td>' +
                    '<td id="counLoc_'+counter+'">'+ctex+'</td>' +
                    '<td id="teleLoc_'+counter+'">'+tele+'</td>' +
                    '<td id="clieLoc_'+counter+'">'+clie+'</td>' +
                    '<td><a id="editLoc_'+counter+'" class="btn btn-primary btn-sm edit-location" data-name="'+name+'" data-addr="'+addr+'" data-zipc="'+zipc+'" data-city="'+city+'" data-coun="'+coun+'" data-tele="'+tele+'" data-clie="'+clie+'" data-counter="'+counter+'"><i class="pe-7s-pen"></i>{{ "Edit"|t}}</a>' +
                    '<a class="btn btn-danger btn-sm remove-new-location" data-counter="'+counter+'"><i class="pe-7s-close-circle"></i> {{"Delete"|t}}</a></td>' +
                    '<input id="nameLocval_'+counter+'" type="hidden" value="'+name+'" name="location_added['+counter+'][name]" />'+
                    '<input id="addrLocval_'+counter+'" type="hidden" value="'+addr+'" name="location_added['+counter+'][address]" />'+
                    '<input id="zipcLocval_'+counter+'" type="hidden" value="'+zipc+'" name="location_added['+counter+'][zipcode]" />'+
                    '<input id="cityLocval_'+counter+'" type="hidden" value="'+city+'" name="location_added['+counter+'][city]" />'+
                    '<input id="counLocval_'+counter+'" type="hidden" value="'+coun+'" name="location_added['+counter+'][country_id]" />'+
                    '<input id="teleLocval_'+counter+'" type="hidden" value="'+tele+'" name="location_added['+counter+'][telephone]" />'+
                    '<input id="clieLocval_'+counter+'" type="hidden" value="'+clie+'" name="location_added['+counter+'][client_number]" /></tr>';
                $('.location-body').append(content);
                $('#add-location').attr('data-counter', counter);
                $('#add-location-modal').modal('hide');
            });

            $(document).on('click', '#add_tariff', function(){

                var options_html = $('#tariff_0').html();
                var counter = $(this).attr('data-counter');
                var tariff_html = '<div id="rowtariff_'+counter+'" class="form-group">' +
                    '                    <div class="col-md-3">' +
                    '                        <select id="tariff_'+counter+'" class="form-control select2-input" name="client_tariff['+counter+'][code]">' +  options_html +  '</select>' +
                '                    </div>' +
                '                    <div class="col-md-3">' +
                '                        <input class="form-control" name="client_tariff['+counter+'][pref]" placeholder="{{ "Enter the client preference"|t }}" />' +
                '                    </div>' +
                '                    <div class="col-md-1"><a data-id="'+counter+'" class="btn btn-danger btn-sm remove-tariff"><i class="pe-7s-close-circle"></i> Delete</a></div>' +
                '                    <div class="col-md-5"></div>' +
                '                    <div class="col-md-12">&nbsp;</div>' +
                '                </div>';
                $('#tariff_template').append(tariff_html);
                $('#tariff_'+counter).select2({
                    theme: "bootstrap"
                });
                counter++;

                $(this).attr('data-counter', counter);
            });

            $(document).on('click', '.remove-tariff',function(){
                $('#rowtariff_'+$(this).attr('data-id')).remove();
                var counter = $('#add_tariff').attr('data-counter');
                counter--;
                $('#add_tariff').attr('data-counter', counter);
            });

            $(document).on('click', '#add_recipe', function(){

                var options_html = $('#recipe_0').html();
                var counter = $(this).attr('data-counter');
                var recipe_html = '<div id="rowrecipe_'+counter+'" class="form-group">' +
                    '                    <div class="col-md-3">' +
                    '                        <select id="recipe_'+counter+'" class="form-control select2-input" name="client_recipe['+counter+'][code]">' +  options_html +  '</select>' +
                    '                    </div>' +
                    '                    <div class="col-md-3">' +
                    '                        <input class="form-control" name="client_recipe['+counter+'][pref]" placeholder="{{ "Enter the client preference"|t }}" />' +
                    '                    </div>' +
                    '                    <div class="col-md-1"><a data-id="'+counter+'" class="btn btn-danger btn-sm remove-recipe"><i class="pe-7s-close-circle"></i> Delete</a></div>' +
                    '                    <div class="col-md-5"></div>' +
                    '                    <div class="col-md-12">&nbsp;</div>' +
                    '                </div>';
                $('#recipe_template').append(recipe_html);
                $('#recipe_'+counter).select2({
                    theme: "bootstrap"
                });
                counter++;

                $(this).attr('data-counter', counter);
            });

            $(document).on('click', '.remove-recipe',function(){
                $('#rowrecipe_'+$(this).attr('data-id')).remove();
                var counter = $('#add_recipe').attr('data-counter');
                counter--;
                $('#add_recipe').attr('data-counter', counter);
            });
        });
    </script>
{% endblock %}