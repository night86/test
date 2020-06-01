{% extends "layouts/main.volt" %}
{% block title %} {{ 'Project'|t }}: {{ project.getName() }} {{ 'Wall'|t }} {% endblock %}
{% block bodyclass %}project-wall-body{% endblock %}
{% block content %}

    <h3>{{ 'Wall'|t }} {{ project.getName() }}  </h3>

    <div id="project-wall">
        <ul id="messages">

        </ul>
        <form id="chat-form">
            <input id="message" autocomplete="off" placeholder="{{ 'You can start typing message here'|t }}"/>
            <button><i class="pe-7s-paper-plane"></i></button>
        </form>
        <div id="typing">
            <div class="names">

            </div>
            <span>{{ 'typing' | t }}</span>
        </div>
    </div>

{% endblock %}


{% block scripts %}
    {{ super() }}
    <script>
        var allUsers = {{ allUsers }};
        var userid = '{{ currentUser.getId() }}';
        var fullname = '{{ currentUser.getFullname() }}';
        var email = '{{ currentUser.getEmail() }}';
        var project_id = '{{ project.getId() }}';
        {#var socket = io('{{ serverName }}:3000/wall?projectid='+project_id);#}
        var socket = io('{{ serverName }}:{{ port }}/wall');
        socket.on('connect', function () {
            // Connected, let's sign-up for to receive messages for this room
            socket.emit('room', project_id);
            messageBoxHeight();

        });
        $('form').submit(function () {
            var $val = $('#message').val();
            socket.emit('chat message', {
                'message': $val,
                'name': fullname,
                'email': email,
                'user_id': userid,
                'project_id': project_id,
//                'date': moment().format("YYYY-MM-DD HH:mm:ss")
            });
            $('#message').val('');
            return false;
        });
        socket.on('chat message', function (data) {
            createMessage(data)
        });
        socket.on('old message', function (data) {
            createMessage(data)
        });

        socket.on('typing', function (user) {
            if (user != userid) {
                var id = parseInt(user);
                isTyping.init(allUsers[id].name);
            }
        })

        $('#message').each(function() {
            var elem = $(this);

            // Save current value of element
            elem.data('oldVal', elem.val());

            // Look for changes in the value
            elem.bind("propertychange change click keyup input paste", function(event){
                // If value has changed...
                if (elem.data('oldVal') != elem.val()) {
                    // Updated stored value
                    elem.data('oldVal', elem.val());

                    socket.emit('typing', userid);
                }
            });
        });

        function createMessage(data) {

            sdate = moment(data.date).format("DD-MM-YYYY HH:mm:ss");
            if (data.user_id == userid) {
                sclass = 'own';
                sname = 'You';
            } else {
                sclass = 'nown'
                sname = data.name;
            }
            $('#messages')
                    .append(
                            $('<li class="' + sclass + '">')
                                    .append($('<div class="wrap">')
                                            .append($('<div class="data-name">')
                                                    .append($('<span class="name">').text(sname))
                                                    .append($('<span class="date">').text(sdate))
                                            )
                                            .append($('<span class="message">').text(data.message))
                                    )
                    ).scrollTop($('#messages').prop("scrollHeight"));
        }

        function messageBoxHeight() {
            setTimeout(function(){
                var messages = $('#messages');
                var wh = $(window).height();
                var hh = $('#page-content-wrapper').find('header').height();
                var titleh = $('#page-content-wrapper').find('.block-content > h3').outerHeight();
                var chatForm = $('#chat-form').outerHeight();

                var eq = (wh - (hh + titleh + chatForm + 42));

                messages.height(eq);
            }, 0);
        }


        var isTyping = {
            users: [],
            timer: '',
            init: function(userName){
                var exist = $.inArray(userName, this.users);

                if (exist != -1){
                    this.users[exist] = userName
                } else {
                    this.users.push(userName);
                }
                this.showTyping();
                window.clearTimeout(this.timer);
                this.timer = window.setTimeout(function(){
                    isTyping.hideBox()
                    var place = $.inArray(userName, isTyping.users);
//                    delete isTyping.users[place]
                    isTyping.users.splice(place, 1);
                },2000);
            },
            showTyping: function () {
                var typing = $('#typing');

                var visible = typing.is(':visible');

                if (visible == false){
                    this.displayBox(typing);
                }
                this.fillBox(typing);
            },
            displayBox: function(el){
//                var typing = $('#typing');
//                var namesCnt = typing.find('.names');
//                namesCnt.append(userName);
                el.fadeIn();
            },
            hideBox: function(){
                var el = $('#typing');
                el.fadeOut();
            },
            fillBox: function (el) {
                el.find('.names').html('');
                $.each(this.users, function(index, value) {
                    el.find('.names').append('<span class="name">' + value + ' </span>');
                });
            }
        }
    </script>
{% endblock %}