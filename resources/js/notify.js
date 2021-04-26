require('bootstrap-notify');


window.ol_notify_from_partner= function(title, message, icon) {
    $.notify({
        icon: icon,
        title: title,
        message: message,
    },{
        placement: {
            from: "bottom",
            align: "right"
        },
        type: 'ol',
        delay: 3000,
        icon_type: 'image',
        template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0}" role="alert">' +
            '<div class="row col-12">' +
                '<img data-notify="icon" class="img-circle pull-left">' +
                '<div class="d-flex align-items-center">' +
                    '<div data-notify="title">{1}</div>' +
                '</div>' +
            '</div>' +
            '<div class="row col-12">' +
                '<div class="mt-2 ml-2" data-notify="message">{2}</div>' +
            '</div>' + 
        '</div>'
    });
}



window.ol_notify= function(message, type) {
    $.notify({
        message: message, 
    },{
        type: type,
        allow_dismiss: true,
        newest_on_top: true,
        placement: {
            from: "bottom",
            align: "right"
        },
        delay: 10000,
        animate: {
            enter: 'animated fadeInRight',
            exit: 'animated fadeOutRight'
        },
        offset: {
            x: 20,
            y: 20,
        },
    });
}



