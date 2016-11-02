// When a user click on a friend, the chat history appears
$('.friend').on('click', function () {
    var that = $(this);

    $.ajax({
        url: 'api/v1/getUserMessages/' + $(this).data('friend-id'),
        dataType: 'json',
        type: 'get',
        success: function (data) {
            var chat_history = $("<div class='span12' style='max-height: 250px; overflow-y: scroll;'></div>");
            $.each(data, function (i, message) {
                var sender = message['sender']['id'] === that.data('friend-id') ? message['sender']['username'] : 'You';
                var message_time = getDateFromTimestamp(new Date(message['sendDate']['timestamp'] * 1000));
                var message_div = $('<div>')
                        .append('<div class="span8" >' + sender + ': ' + message['message'] + '</div>')
                        .append('<div class="span4" >' + message_time + '</div>');
                chat_history.append(message_div);
            });

            $('.chat-history').html(chat_history).show();
            $('.chat-history').find('div').scrollTop($('.chat-history').find('div')[0].scrollHeight);

            $('.message-textarea, .message-send').show();
            $('.receiver').val(that.data('friend-id'));
        }
    });
})

// When a user sends a message to a friend
$('.message-send').on('click', function () {
    var data = [];
    data.push({name: 'message', value: $('.message-textarea').val()});
    data.push({name: 'receiver_id', value: $('.receiver').val()});
    $.ajax({
        url: 'api/v1/postMessage/',
        type: 'post',
        data: data,
        success: function () {
            var message_time = getDateFromTimestamp(new Date());
            var message_div = $('<div>')
                    .append('<div class="span8" >You: ' + $('.message-textarea').val() + '</div>')
                    .append('<div class="span4" >' + message_time + '</div>');
            $('.chat-history').children('div').append(message_div);

            $('.chat-history').find('div').scrollTop($('.chat-history').find('div')[0].scrollHeight);

            $('.message-textarea').val('');
        }
    });
})

// Put the date in the format that is readable for the users
function getDateFromTimestamp(message_time) {
    var message_time_formatted = message_time.getFullYear() + '-' + message_time.getMonth() + '-' + message_time.getDay() + ' ';
    message_time_formatted += message_time.getHours() < 12 ? message_time.getHours() : (message_time.getHours() - 12);
    message_time_formatted += ': ' + message_time.getMinutes();
    message_time_formatted += message_time.getHours() < 12 ? ' am' : ' pm';
    return message_time_formatted;
}

// Check for notifications every 5 secs
(function checkNotifications() {
    var receiver_id = $('.chat-history:visible').length > 0 ? $('.receiver').val() : -1;
    $.ajax({
        url: 'api/v1/checkNotifications/' + receiver_id,
        success: function (data) {
            if (data == 1) {
                $('.notification').html("<div class='alert-info alert-block'>New message</div>");
            }
            else {
                $('.notification').html("");
            }

        },
        complete: function () {
            // Schedule the next request when the current one's complete
            setTimeout(function () {
                checkNotifications()
            }, 5000);
        }
    });
})();