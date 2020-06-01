{% extends "layouts/main.volt" %}
{% block title %} {{ "Clients"|t }} {% endblock %}
{% block content %}

    <p class="pull-right">
        <a data-href="{{ url("lab/sales_client/add") }}" class="btn-primary btn add-client"><i class="pe-7s-plus"></i> {{ "Add dentist"|t }}</a>
    </p>
    <h3>{{ "Clients"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table id="clients" class="simple-datatable-client table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <th>{{ "Status"|t }}</th>
                    <th>{{ "Name"|t }}</th>
                    <th>{{ "Address"|t }}</th>
                    <th>{{ "Zip code"|t }}</th>
                    <th>{{ "City"|t }}</th>
                    <th>{{ "Country"|t }}</th>
                    <th>{{ "Phone"|t }}</th>
                    <th>{{ "Actions"|t }}</th>
                </thead>
                <tbody>
                    {% for client in clients %}
                        {% if client.Dentist is not null %}
                        <tr>
                            <td>{% if client.getStatus() == 'concept' %}{{ "Concept"|t }}{% elseif client.getStatus() == 'pending' %}{{ "Pending"|t }}{% else %}{{ "Active"|t }}{% endif %}</td>
                            <td>{{ client.Dentist.getName() }}</td>
                            <td>{{ client.Dentist.getAddress() }}</td>
                            <td>{{ client.Dentist.getZipCode() }}</td>
                            <td>{{ client.Dentist.getCity() }}</td>
                            <td>{% if client.Dentist.Country %}{{ client.Dentist.Country.getName() }}{% endif %}</td>
                            <td>{{ client.Dentist.getTelephone() }}</td>
                            <td>
                                {% if client.getStatus() == 'concept' or client.getStatus() == 'pending' %}
                                <a href="/lab/sales_client/edit/{{ client.Dentist.getId() }}" class="btn btn-primary btn-sm"><i class="pe-7s-pen"></i> {{ "Edit"|t }}</a>
                                {% else %}
                                    <a href="/lab/sales_client/view/{{ client.Dentist.getId() }}" class="btn btn-default btn-sm"><i class="pe-7s-look"></i> {{ "View"|t }}</a>
                                {% endif %}

                                {% if client.getStatus() == 'concept' %}
                                    <a class="btn btn-info btn-sm invite_button" data-id="{{ client.Dentist.getId() }}" data-name="{{ client.Dentist.getName() }}" data-email="{{ client.Dentist.getEmail() }}"><i class="pe-7s-mail"></i> {{ "Send invite"|t }}</a>
                                {% endif %}

                                {% if client.getStatus() == 'pending' %}
                                    <a class="btn btn-warning btn-sm invite_button" data-id="{{ client.Dentist.getId() }}" data-name="{{ client.Dentist.getName() }}" data-email="{{ client.Dentist.getEmail() }}"><i class="pe-7s-mail"></i> {{ "Resend invite"|t }}</a>
                                {% endif %}
                            </td>
                        </tr>
                        {% endif %}
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </div>


    {{ partial("modals/newInvitation", ['id': 'add_client', 'title': 'Invite dentist'|t, 'additionalClass': 'send-invite']) }}
    {{ partial("modals/addSingleField", [
        'id': 'new_dentist',
        'title': 'KvK number of the new dentist'|t,
        'additionalClass': 'new-kvk-dentist',
        'content': "Please add the KvK number of the dentist you want to add to your list of clients."|t,
        'content2': "If you don't know the KvK number you can go to"|t,
        'link': "<a href='http://www.kvk.nl/zoeken' target='_blank'>www.kvk.nl/zoeken</a>",
        'label': "KvK number"|t
    ]) }}
    {{ partial("modals/alert", ['id': 'sended-message', 'title': 'Success'|t, 'content': 'Message has been sended to the denstist.'|t]) }}
    {{ partial("modals/alert", ['id': 'not-sended-message', 'title': 'Warning'|t, 'content': 'This dentist was already invited.'|t]) }}
    {{ partial("modals/alert", ['id': 'kvk-within-lab', 'title': 'Existing KvK'|t, 'content': 'You already used this KvK number for'|t, 'content2': 'Please add a new KvK number.'|t, 'closeButton': 'Close'|t, 'existingUser': true]) }}
    {{ partial("modals/confirm", [
        'id': 'kvk-other-lab',
        'title': 'Existing user'|t,
        'content': 'This dentist organisation is already a Signadens user. Is it correct you want to add this dentist organisation to your client list:'|t,
        'primarybutton': 'Yes',
        'additionalClass': 'confirm-existing-kvk',
        'closeButton': 'Close'|t,
        'existingUser': true
    ]) }}

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        $(function(){

            $('.invite_button').on('click', function() {
                $('#add_client').modal('show');
                $('#modal_org_title').html($(this).attr('data-name'));
                $('#modal_org_name').html($(this).attr('data-name'));
                $('#modal_org_email').html($(this).attr('data-email'));

                $('.send-invite').attr({'data-id': $(this).attr('data-id'), 'data-name': $(this).attr('data-name'), 'data-email': $(this).attr('data-email')});
            });

            $('.send-invite').on('click', function(){

                $.ajax({
                    url: '/lab/sales_client/',
                    type: 'post',
                    data: {
                        'email': $(this).attr('data-email'),
                        'organisation_name': $(this).attr('data-name'),
                        'organisation_id': $(this).attr('data-id')
                    },
                    success: function (data) {
                        var obj = $.parseJSON(data);
                        if (obj.status == true) {
                            $('#add_client').modal('hide');
                            // $('#sended-message').modal('show');
                            setTimeout(function () {
                                toastr.success('{{"Invite sent"|t}}');
                                $('#add_client').modal('hide');
                                setTimeout(function () {
                                    window.location.reload();
                                }, 1000);
                            }, 1000);
                        } else {
                            setTimeout(function () {
                                toastr.error('{{"Error when sending invite"|t}}')
                            }, 1000);
                        }
                    }
                });
            });

            $('.add-client').on('click', function(){
               $('#new_dentist').modal('show');
            });

            $('.new-kvk-dentist').on('click', function(){

                if($.isNumeric($('#newName').val())){

                    var newName = $('#newName').val();
                    $.ajax({
                        method: 'POST',
                        url: '/lab/sales_client/validatekvk',
                        data: {
                            kvk_number: newName,
                            lab_id: {{ currentUser.getOrganisationId() }}
                        },
                        success: function(data){
                            var obj = $.parseJSON(data);

                            if(obj.isKvkUsed == true){

                                if(obj.isKvkUsedWithinLab == true){
                                    $('#kvk_alert').html(obj.dentistData['name']);
                                    $('#kvk-within-lab').modal('show');
                                }
                                else {
                                    $('#den_name').html(obj.dentistData['name']);
                                    $('#den_street').html(obj.dentistData['street']);
                                    $('#den_postal').html(obj.dentistData['postal']);
                                    $('#den_city').html(obj.dentistData['city']);
                                    $('#kvk-other-lab').modal('show');
                                }
                            }
                            else {
                                setTimeout(function () {
                                    location.href = '/lab/sales_client/add/'+newName;
                                }, 1000);
                            }
                        }
                    });
                    $('#new_dentist').modal('hide');
                }
                else {
                    toastr.error('{{ "Kvk value must be a number"|t }}');
                }

            });

            $('.confirm-existing-kvk').on('click', function(){
                $('#kvk-other-lab').modal('hide');
                setTimeout(function () {
                    location.href = '/lab/sales_client/add/'+$('#newName').val();
                }, 1000);
            });
        });
    </script>
{% endblock %}