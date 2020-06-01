{% extends "layouts/main.volt" %}
{% block title %} {{ "Organisation"|t }} {% endblock %}
{% block content %}

    <h3><a href="{{ url("general/organisation/") }}"><i class="pe-7s-back"></i></a> {{ "Edit organisation"|t }}</h3>

    <fieldset class="form-group">

        <form action="/general/organisationEdit/{{ organisation.getId() }}" method="post" enctype="multipart/form-data">

            <br />
            <legend>{{ organisation.getName() }} - {{ "Organisation data"|t }}</legend>

            <div class="row">

                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ "Name"|t }}:</label>
                        {{ text_field('organisation[name]', 'required': 'required', 'class': 'form-control', 'value': organisation.getName(), 'maxlength': '150' ) }}
                    </div>
                    <div class="form-group">
                        <label>{{ "Street and number"|t }}:</label>
                        {{ text_field('organisation[address]', 'required': 'required', 'class': 'form-control', 'value': organisation.getAddress(), 'maxlength': '100' ) }}
                    </div>
                    {% if organisation.OrganisationType.slug is 'lab' %}
                        <div class="form-group">
                            <label>{{ "ISO2Handle URL"|t }}:</label>
                            {{ text_field('organisation[iso2h_url]', 'class': 'form-control', 'value': organisation.getIso2hUrl() ) }}
                        </div>
                    {% endif %}
                    <div class="form-group">
                        <label>{{ "General emailaddress"|t }}:</label>
                        {{ text_field('organisation[email]', 'required': 'required', 'class': 'form-control', 'value': organisation.getEmail(), 'maxlength': '150' ) }}
                    </div>
                    {% if currentUser.hasRole('ROLE_DENTIST_GENERAL_ORGANISATION_EDIT') %}
                    <div class="form-group">
                        <label>{{ "Lab(s) I am connected to"|t }}:</label>
                        <ul>
                            {% for org in labDentists %}
                                <li>{{ org.Lab.name }}</li>
                            {% endfor %}
                        </ul>
                    </div>
                    {% endif %}
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ "City"|t }}:</label>
                        {{ text_field('organisation[city]', 'required': 'required', 'class': 'form-control', 'value': organisation.getCity(), 'maxlength': '32' ) }}
                    </div>
                    <div class="form-group">
                        <label>{{ "Country"|t }}:</label>
                        <select name="organisation[country_id]" class="form-control" required="required">
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
                    {% if organisation.OrganisationType.slug is 'lab' %}
                        <div class="form-group">
                            <label>{{ "ISO2H username"|t }}:</label>
                            {{ text_field('organisation[iso2h_username]', 'class': 'form-control', 'value': organisation.getIso2hUsername() ) }}
                        </div>
                    {% endif %}
                    {% if currentUser.hasRole('ROLE_LAB_GENERAL_ORGANISATION') %}
                        <div class="form-group">
                            <label>{{ "Invoice initial sequence"|t }}:</label>
                            <input type="number" min="1" max="9999999" class="form-control" name="organisation[invoice_sequence]" value="{{ organisation.getInvoiceSequence() }}" {% if organisation.getInvoiceSequence() is not null %}disabled="disabled" title="{{ "Please contact Signadens admin for changing this value"|t }}"{% endif %} />
                        </div>
                    {% endif %}
                    {% if organisation.logo is not null %}
                        <div class="form-group">
                            <img src="{{ image('organisation', organisation.logo) }}" width="300"/>
                        </div>
                    {% endif %}
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ "Zipcode"|t }}:</label>
                        {{ text_field('organisation[zipcode]', 'required': 'required', 'class': 'form-control', 'value': organisation.getZipcode(), 'maxlength': '32' ) }}
                    </div>
                    <div class="form-group">
                        <label>{{ "Phone"|t }}:</label>
                        {{ text_field('organisation[telephone]', 'required': 'required', 'class': 'form-control', 'value': organisation.getTelephone(), 'maxlength': '32' ) }}
                    </div>

                    {% if organisation.OrganisationType.slug is 'lab' %}
                        <div class="form-group">
                            <label>{{ "ISO2H password"|t }}:</label>
                            {{ password_field('organisation[iso2h_password]', 'class': 'form-control', 'value': organisation.getIso2hPassword() ) }}
                        </div>
                    {% endif %}
                    <div class="form-group">
                        <label for="name">{{ 'Image'|t }}</label>
                        <input type="file" name="logo" class="form-control" {% if organisation.logo is null and organisation.organisation_type_id != 3 %}required="required"{% endif %}>
                    </div>
                    {% if organisation.logo is not null %}
                        <div class="form-group">
                            {% if organisation.organisation_type_id == 1%}
                                <a href="{{ url("supplier/user/deleteorganisationimage/")~organisation.getId() }}" class="btn btn-danger"><i class="pe-7s-trash"></i> {{ "Delete image"|t }}</a>
                            {% elseif organisation.organisation_type_id == 2 %}
                                <a href="{{ url("signadens/organisation/deleteimageedit/")~organisation.getId() }}" class="btn btn-danger"><i class="pe-7s-trash"></i> {{ "Delete image"|t }}</a>
                            {% elseif organisation.organisation_type_id == 3 %}
                                <a href="{{ url("dentist/user/deleteorganisationimage/")~organisation.getId() }}" class="btn btn-danger"><i class="pe-7s-trash"></i> {{ "Delete image"|t }}</a>
                            {% elseif organisation.organisation_type_id == 4 %}
                                <a href="{{ url("lab/user/deleteorganisationimage/")~organisation.getId() }}" class="btn btn-danger"><i class="pe-7s-trash"></i> {{ "Delete image"|t }}</a>
                            {% endif %}
                        </div>
                    {% endif %}
                </div>
            </div>

            {% if currentUser.hasRole('ROLE_LAB_GENERAL_ORGANISATION') %}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{ "Delivery notes"|t }}:</label>
                        <textarea id="helpdesk-text" name="organisation[delivery_notes]" class="form-control tinymce" style="width: 400px; height: 200px;">{{ organisation.getDeliveryNotes() }}</textarea>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="financial_data">{{ "Financial data"|t }}&nbsp;</label>
                        <textarea id="financial_data" name="organisation[financial_data]" class="form-control tinymce" style="width: 400px; height: 200px;">{{ organisation.getFinancialData() }}</textarea>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="invoice_footer">{{ "Invoice footer"|t }}&nbsp;</label>
                        <textarea id="invoice_footer" name="organisation[invoice_footer]" class="form-control tinymce" style="width: 400px; height: 200px;">{{ organisation.getInvoiceFooter() }}</textarea>
                    </div>
                </div>
            </div><hr style="margin-top: 50px; margin-bottom: 50px;" />

        <p class="pull-right"><a id="add_payment" class="btn btn-primary" data-type="new"><i class="pe-7s-plus"></i> {{ "Add new"|t }}</a></p>
        <h3>{{ "Payment arrangements"|t }}</h3>

        <table class="simple-datatable table table-striped">
            <thead>
            <th>{{ "Code"|t }}</th>
            <th>{{ "Description"|t }}</th>
            <th>{{ "Percentage"|t }}</th>
            <th>{{ "Actions"|t }}</th>
            </thead>
            <tbody>
            {% for payment in payments %}
                <tr>
                    <td>{{ payment.getCode() }}</td>
                    <td>{{ payment.getDescription() }}</td>
                    <td>{{ payment.getPercentage() }}</td>
                    <td>
                        <a class="btn btn-primary btn-sm edit-payment" data-type="old" data-id="{{ payment.getId() }}" data-code="{{ payment.getCode() }}" data-desc="{{ payment.getDescription() }}" data-perc="{{ payment.getPercentage() }}"><i class="pe-7s-pen"></i> {{ "Edit"|t }}</a>
                        <a class="btn btn-danger btn-sm delete-payment" data-url="/lab/user/deletepaymentoption/{{ payment.getId() }}"><i class="pe-7s-trash"></i> {{ "Delete"|t }}</a>
                    </td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
            {% endif %}

            {% if currentUser.hasRole('ROLE_DENTIST_GENERAL_ORGANISATION_EDIT') %}

            <legend>{{ "Locations"|t }}</legend>
            <p class="pull-right">
                <a id="add-location" data-counter="{% if count(locations) > 0 %}{{ count(locations) }}{% else %}{{ "0" }}{% endif %}" class="btn-primary btn"><i class="pe-7s-plus"></i> {{ "Add new location"|t }}</a>
            </p>

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
                            <th>{{ "Actions"|t }}</th>
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
                                    <td width="25%">
                                        <a id="editLoc_{{ index+1 }}" class="btn btn-primary btn-sm edit-location"
                                           data-name="{{ loc.getName() }}"
                                           data-addr="{{ loc.getAddress() }}"
                                           data-zipc="{{ loc.getZipcode() }}"
                                           data-city="{{ loc.getCity() }}"
                                           data-coun="{{ loc.getCountryId() }}"
                                           data-tele="{{ loc.getTelephone() }}"
                                           data-counter="{{ index+1 }}">
                                            <i class="pe-7s-pen"></i>{{ "Edit"|t}}</a>
                                        {% if loc.getDeletedAt() is null and loc.getDeletedBy() is null %}
                                            <a data-id="{{ loc.getId() }}" class="btn btn-danger btn-sm disable-location" data-counter="{{ index+1 }}"><i class="pe-7s-close-circle"></i> {{ "Deactivate"|t }}</a>
                                        {% else %}
                                            <a data-id="{{ loc.getId() }}" class="btn btn-success btn-sm enable-location" data-counter="{{ index+1 }}"><i class="pe-7s-play"></i> {{ "Activate"|t }}</a>
                                        {% endif %}
                                    </td>
                                    <input id="nameval_{{ index+1 }}" type="hidden" value="{{ loc.getName() }}" name="location_old[{{ index+1 }}][name]" />
                                    <input id="addrval_{{ index+1 }}" type="hidden" value="{{ loc.getAddress() }}" name="location_old[{{ index+1 }}][address]" />
                                    <input id="zipcval_{{ index+1 }}" type="hidden" value="{{ loc.getZipcode() }}" name="location_old[{{ index+1 }}][zipcode]" />
                                    <input id="cityval_{{ index+1 }}" type="hidden" value="{{ loc.getCity() }}" name="location_old[{{ index+1 }}][city]" />
                                    <input id="counval_{{ index+1 }}" type="hidden" value="{{ loc.getCountryId() }}" name="location_old[{{ index+1 }}][country_id]" />
                                    <input id="televal_{{ index+1 }}" type="hidden" value="{{ loc.getTelephone() }}" name="location_old[{{ index+1 }}][telephone]" />
                                    <input id="idval_{{ index+1 }}" type="hidden" value="{{ loc.getId() }}" name="location_old[{{ index+1 }}][id]" />
                                </tr>
                            {% endfor %}
                        {% endif %}
                        </tbody>
                    </table>
                    {% for loc in locations %}
                        <input id="location_deleted_{{ loc.getId() }}" type="hidden" name="location_deleted[{{ loc.getId() }}]" value="{% if loc.getDeletedAt() is null and loc.getDeletedBy() is null %}0{% else %}1{% endif %}" />
                    {% endfor %}
                </div>
            </div>
            {% endif %}

            <br /><br />
            <legend>{{ organisation.getName() }} - {{ "Invoice information"|t }}</legend>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="invoice_info[address]">{{ "Address"|t }}&nbsp;</label>
                        <input id="invoice_address" name="invoice_info[address]" type="text" class="form-control" value="{% if organisation.InvoiceInfo %}{{ organisation.InvoiceInfo.getAddress() }}{% endif %}" tabindex="10" />
                    </div>
                    <div class="form-group">
                        <label for="invoice_info[country_id]">{{ "Country"|t }}&nbsp;</label>
                        <select id="invoice_country" name="invoice_info[country_id]" class="form-control">
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
                        <input id="invoice_telephone_admin" name="invoice_info[telephone_admin]" type="text" class="form-control" tabindex="15" value="{% if organisation.InvoiceInfo %}{{ organisation.InvoiceInfo.getTelephoneAdmin() }}{% endif %}" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="invoice_info[zipcode]">{{ "Zipcode"|t }}&nbsp;</label>
                        <input id="invoice_zipcode" name="invoice_info[zipcode]" type="text" class="form-control" value="{% if organisation.InvoiceInfo %}{{ organisation.InvoiceInfo.getZipcode() }}{% endif %}" tabindex="11" />
                    </div>
                    <div class="form-group">
                        <label for="invoice_info[email]">{{ "Email address"|t }}&nbsp;</label>
                        <input id="invoice_email" name="invoice_info[email]" type="text" class="form-control" tabindex="13" value="{% if organisation.InvoiceInfo %}{{ organisation.InvoiceInfo.getEmail() }}{% endif %}" />
                    </div>
                    <div class="form-group">
                        <label for="invoice_info[bank_account]">{{ "Bank account number"|t }}&nbsp;</label>
                        <input id="invoice_bank_account" name="invoice_info[bank_account]" type="text" class="form-control" tabindex="16" value="{% if organisation.InvoiceInfo %}{{ organisation.InvoiceInfo.getBankAccount() }}{% endif %}" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="invoice_info[city]">{{ "City"|t }}&nbsp;</label>
                        <input id="invoice_city" name="invoice_info[city]" type="text" class="form-control" value="{% if organisation.InvoiceInfo %}{{ organisation.InvoiceInfo.getCity() }}{% endif %}" tabindex="12" />
                    </div>
                    <div class="form-group">
                        <label for="invoice_info[contact_admin]">{{ "Contact person administration"|t }}&nbsp;</label>
                        <input id="invoice_contact_admin" name="invoice_info[contact_admin]" type="text" class="form-control" tabindex="14" value="{% if organisation.InvoiceInfo %}{{ organisation.InvoiceInfo.getContactAdmin() }}{% endif %}" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="invoice_info[salutation]">{{ "Salutation"|t }}&nbsp;</label>
                        <textarea name="invoice_info[salutation]" class="form-control tinymce" style="width: 400px; height: 200px;">{% if organisation.InvoiceInfo %}{{ organisation.InvoiceInfo.getSalutation() }}{% endif %}</textarea>
                    </div>
                </div>
                <div class="col-md-4">
                </div>
                <div class="col-md-4">
                </div>
            </div>

            {% if currentUser.hasRole('ROLE_DENTIST_GENERAL_ORGANISATION_EDIT') %}
            <br />
            <legend>{{ organisation.getName() }} - {{ "Legal data"|t }}</legend>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{ "Processing contract"|t }}:</label>
                        <ul>
                            {% for org in labDentists %}
                                {% if org.getContract() is not null %}
                                <li><a href="/uploads/contracts/{{ org.getContract() }}" target="_blank">{{ org.getContract() }}</a></li>
                                {% endif %}
                            {% endfor %}
                        </ul>
                        <p>{{ "No processing contract yet? download the"|t }} <a href="/uploads/contracts/Model_verwerkersovereenkomst.docx" target="_blank">{{ "concept of the contract"|t }}</a> {{ "and send it to your connected lab"|t }}</p>
                    </div>
                </div>
            </div>
            <div class="row">&nbsp;</div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{ "Terms of use"|t }}:</label>
                        <p>{{ "I agreed to terms of use of Signadens"|t }}</p>
                    </div>
                </div>
            </div>
            {% endif %}
            <div class="row">
                <div class="col-md-12">
                    <button id="confirm-button" type="submit" class="btn btn-primary pull-right"><i class="pe-7s-diskette"></i> {{ "Save"|t }}</button>
                </div>
            </div>
        </form>
    </fieldset>
    {{ partial("modals/addPaymentOption", ['id': 'add_payment_modal', 'title': "Add payment arrangement"|t, 'content': "Please enter the code, description and percentage of the payment arrangement."|t]) }}
    {{ partial("modals/confirmGeneral", ['id': 'delete_payment_modal', 'title': "Delete"|t, 'content': "Are you sure you want to delete?"|t, 'additionalClass': 'confirm_delete']) }}
    {{ partial("modals/addLocation", ['id': 'add-location-modal', 'title': "Add location"|t, 'type': 'noclient']) }}
    {{ partial("modals/editLocation", ['id': 'edit-location-modal', 'title': "Edit location"|t, 'type': 'noclient']) }}

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

            $('#add_payment').on('click', function(){

                $('#add_payment_modal').modal('show');
                $('#confirm_payment').attr('data-type', $(this).attr('data-type'));
            });

            $('.edit-payment').on('click', function(){

                $('#add_payment_modal').modal('show');
                $('#code').val($(this).attr('data-code'));
                $('#description').val($(this).attr('data-desc'));
                $('#percentage').val($(this).attr('data-perc'));
                $('#confirm_payment').attr({'data-type': $(this).attr('data-type'), 'data-id': $(this).attr('data-id')});
            });

            $('.delete-payment').on('click', function(){

                $('#delete_payment_modal').modal('show');
                $('.confirm_delete').attr('data-url', $(this).attr('data-url'));
            });

            $('.confirm_delete').on('click', function(){

                var url = $(this).attr('data-url');

                setTimeout(function () {

                    toastr.success("{{ 'Payment arrangement deleted'|t }}");

                    setTimeout(function () {
                        location.href = url;
                    }, 1000);
                }, 1000);
            });

            $('#confirm_payment').on('click', function(){

                if($(this).attr('data-type') == 'old') {

                    var id = $(this).attr('data-id');
                }
                else {
                    var id = null;
                }

                if($('#code').val() != '' && $('#code').val() != null){
                    $('#code').css("border-color", "transparent");
                    var code = true;
                }
                else {
                    var code = false
                    $('#code').css("border-color", "red");
                }

                if($('#description').val() != '' && $('#description').val() != null){
                    $('#description').css("border-color", "transparent");
                    var desc = true;
                }
                else {
                    var desc = false
                    $('#description').css("border-color", "red");
                }

                if($('#percentage').val() != '' && $('#percentage').val() != null){
                    $('#percentage').css("border-color", "transparent");
                    var perc = true;
                }
                else {
                    var perc = false;
                    $('#percentage').css("border-color", "red");
                }

                if(code == true && desc == true && perc == true) {

                    // console.log(id);
                    $.ajax({
                        method: 'POST',
                        url: '/lab/user/ajaxpaymentoption/',
                        data: {
                            id: id, code: $('#code').val(), description: $('#description').val(), percentage: $('#percentage').val()
                        },
                        success: function (data) {
                            var obj = $.parseJSON(data);

                            if (obj.status != "error") {

                                setTimeout(function () {

                                    toastr.success(obj.msg);

                                    setTimeout(function () {
                                        location.href = '/general/organisationEdit/{{ currentUser.getOrganisationId() }}';
                                    }, 1000);
                                }, 1000);
                            }
                            else {
                                setTimeout(function () {
                                    toastr.error(obj.msg);
                                }, 1000);
                            }
                        }
                    });
                }
                else {
                    toastr.error("{{ "Please fill in missing fields."|t }}");
                }
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

            $(document).on('click', '.edit-location', function(){
                $('#locationNameEdit').val($(this).attr('data-name'));
                $('#locationAddressEdit').val($(this).attr('data-addr'));
                $('#locationZipcodeEdit').val($(this).attr('data-zipc'));
                $('#locationCityEdit').val($(this).attr('data-city'));
                $('#locationCountryEdit').val($(this).attr('data-coun'));
                $('#locationTelephoneEdit').val($(this).attr('data-tele'));
                $('#confirmEditLocation').attr('data-counter', $(this).attr('data-counter'));
                $('#edit-location-modal').modal('show');

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

            $(document).on('click', '.remove-new-location', function(){
                $('#confirm-delete-modal').modal('show');
                $('.confirmDelete').attr({ 'data-id': $(this).attr('data-id'), 'data-counter': $(this).attr('data-counter'), 'data-type': 'new-location'});
            });

            $('#confirmEditLocation').on('click', function(){

                var name = $('#locationNameEdit').val();
                var addr = $('#locationAddressEdit').val();
                var zipc = $('#locationZipcodeEdit').val();
                var city = $('#locationCityEdit').val();
                var coun = $('#locationCountryEdit').val();
                var ctex = $('#locationCountryEdit option:selected').html();
                var tele = $('#locationTelephoneEdit').val();

                if(name === '' || coun === ''){
                    toastr.error('{{ 'Fields cannot be empty.'|t }}');
                    return;
                }

                $('#name_'+$(this).attr('data-counter')).html(name);
                $('#nameval_'+$(this).attr('data-counter')).val(name);
                $('#addr_'+$(this).attr('data-counter')).html(addr);
                $('#addrval_'+$(this).attr('data-counter')).val(addr);
                $('#zipc_'+$(this).attr('data-counter')).html(zipc);
                $('#zipcval_'+$(this).attr('data-counter')).val(zipc);
                $('#city_'+$(this).attr('data-counter')).html(city);
                $('#cityval_'+$(this).attr('data-counter')).val(city);
                $('#coun_'+$(this).attr('data-counter')).html(ctex);
                $('#counval_'+$(this).attr('data-counter')).val(coun);
                $('#tele_'+$(this).attr('data-counter')).html(tele);
                $('#televal_'+$(this).attr('data-counter')).val(tele);
                $('#editLoc_'+$(this).attr('data-counter')).attr({'data-name': name, 'data-addr': addr, 'data-zipc': zipc, 'data-city': city, 'data-coun': coun, 'data-tele': tele});
                $('#edit-location-modal').modal('hide');
            });

            $('#confirmAddLocation').on('click', function(){

                var name = $('#locationName').val();
                var addr = $('#locationAddress').val();
                var zipc = $('#locationZipcode').val();
                var city = $('#locationCity').val();
                var coun = $('#locationCountry').val();
                var ctex = $('#locationCountry option:selected').html();
                var tele = $('#locationTelephone').val();

                if(name === '' || coun === ''){
                    toastr.error('{{ 'Fields cannot be empty.'|t }}');
                    return;
                }

                var counter = parseInt($('#add-location').attr('data-counter')) + 1;
                var content = '<tr id="newrowLoc_'+counter+'" class="location-row">' +
                    '<td id="name_'+counter+'">'+name+'</td>' +
                    '<td id="addr_'+counter+'">'+addr+'</td>' +
                    '<td id="zipc_'+counter+'">'+zipc+'</td>' +
                    '<td id="city_'+counter+'">'+city+'</td>' +
                    '<td id="coun_'+counter+'">'+ctex+'</td>' +
                    '<td id="tele_'+counter+'">'+tele+'</td>' +
                    '<td><a id="editLoc_'+counter+'" class="btn btn-primary btn-sm edit-location" data-name="'+name+'" data-addr="'+addr+'" data-zipc="'+zipc+'" data-city="'+city+'" data-coun="'+coun+'" data-tele="'+tele+'" data-counter="'+counter+'"><i class="pe-7s-pen"></i>{{ "Edit"|t}}</a>' +
                    '<a class="btn btn-danger btn-sm remove-new-location" data-counter="'+counter+'"><i class="pe-7s-close-circle"></i> {{"Delete"|t}}</a></td>' +
                    '<input id="nameval_'+counter+'" type="hidden" value="'+name+'" name="location_added['+counter+'][name]" />'+
                    '<input id="addrval_'+counter+'" type="hidden" value="'+addr+'" name="location_added['+counter+'][address]" />'+
                    '<input id="zipcval_'+counter+'" type="hidden" value="'+zipc+'" name="location_added['+counter+'][zipcode]" />'+
                    '<input id="cityval_'+counter+'" type="hidden" value="'+city+'" name="location_added['+counter+'][city]" />'+
                    '<input id="counval_'+counter+'" type="hidden" value="'+coun+'" name="location_added['+counter+'][country_id]" />'+
                    '<input id="televal_'+counter+'" type="hidden" value="'+tele+'" name="location_added['+counter+'][telephone]" /></tr>';
                $('.location-body').append(content);
                $('#add-location').attr('data-counter', counter);
                $('#add-location-modal').modal('hide');
            });
        });

        $(window).load(function(){
            $('.dataTables_empty').parent().remove();
        });
    </script>
{% endblock %}