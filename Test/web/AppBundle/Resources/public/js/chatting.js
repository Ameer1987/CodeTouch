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
                var message_time = new Date(message['sendDate']['timestamp'] * 1000);
                var message_time_formatted = message_time.getHours() < 12 ? message_time.getHours() : (message_time.getHours() - 12);
                message_time_formatted += ': ' + message_time.getMinutes();
                message_time_formatted += message_time.getHours() < 12 ? ' am' : ' pm';
                var message_div = $('<div>')
                        .append('<div class="span10" >' + sender + ': ' + message['message'] + '</div>')
                        .append('<div class="span2" >' + message_time_formatted + '</div>');
                chat_history.append(message_div);
            });
            $('.chat-history').html(chat_history);

            $('.message-textarea, .message-send').show();
            $('.receiver').val(that.data('friend-id'));
        }
    });
})

$('.message-send').on('click', function () {
    var data = [];
    data.push({name: 'message', value: $('.message-textarea').val()});
    data.push({name: 'receiver_id', value: $('.receiver').val()});
    $.ajax({
        url: 'api/v1/postMessage/',
        dataType: 'json',
        type: 'post',
        data: data,
        success: function () {
            var message_time = new Date();
            var message_time_formatted = message_time.getHours() < 12 ? message_time.getHours() : (message_time.getHours() - 12);
            message_time_formatted += ': ' + message_time.getMinutes();
            message_time_formatted += message_time.getHours() < 12 ? ' am' : ' pm';
            var message_div = $('<div>')
                    .append('<div class="span10" >You: ' + $('.message-textarea').val() + '</div>')
                    .append('<div class="span2" >' + message_time_formatted + '</div>');
            $('.chat-history').append(message_div);
            
            $('.message-textarea').val('');
        }
    });
})