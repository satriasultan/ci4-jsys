
function formatRepo(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository__title'>" + repo.nik + "</div>";
    if (repo.nmlengkap) {
        markup += "<div class='select2-result-repository__description'>" + repo.nmlengkap +" <i class='fa fa-circle-o'></i> "+ repo.nmdept +"</div>";
    }
    return markup;
}

function formatRepoSelection(repo) {
    return repo.nmlengkap || repo.text;
}

var defaultInitial = '';
$(".select2_kary").select2({
    placeholder: "Ketik Nik Atau Nama Karyawan",
    allowClear: true,
    ajax: {
        url: HOST_URL + 'trans/ess_calendar/list_karyawan' + '?var=' + '',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                _search_: params.term, // search term
                _page_: params.page,
                _draw_: true,
                _start_: 1,
                _perpage_: 2,
                _paramglobal_: defaultInitial,
            };
        },
        processResults: function(data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    // minimumInputLength: 1,
    templateResult: formatRepo, // omitted for brevity, see the source of this page
    templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
});


$('#formAbsenCalendar').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'fa fa-close',
        validating: 'fa fa-refresh'
    },
    fields: {
        nik: {
            validators: {
                notEmpty: {
                    message: 'The field can not be empty'
                }
            }
        },
    },
    excluded: [':disabled']
});

var eventsx = '';
function lihatNik() {
//urldecode()
    //encodeURIComponent('-INFto2000,2001to5000')
    Swal.fire({
        title: 'Peringatan..!!!',
        text: 'Anda akan, Melihat data presensi nik ',
        backdrop: true,
        allowOutsideClick: false,
        showConfirmButton: true,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Ok`,
        icon: 'question',
        //denyButtonText: `Don't save`,
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            var validator = $('#formAbsenCalendar').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                $.ajax({
                    url: HOST_URL + 'trans/ess_calendar/api_postdata' + '?var=' + $('[name="nik"]').val(),
                    type: "GET",
                    dataType: 'json',
                    success: function (data) {
                        eventsx = data;

                        if (result.isConfirmed) {
                            window.location.replace(HOST_URL + 'trans/ess_calendar' + '/?var=' + $('[name="nik"]').val())
                        } else if (result.isDenied) {
                            return false;
                        }
                    }
                });
            }
        } else if (result.isDenied) {
            return false;
        }
    })

}



$(document).ready(function() {
    /* initialize the external events
     -----------------------------------------------------------------*/
    function init_events(ele) {
        ele.each(function () {

            // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
            // it doesn't need to have a start or end
            var eventObject = {
                title: $.trim($(this).text()) // use the element's text as the event title
            }

            // store the Event Object in the DOM element so we can get to it later
            $(this).data('eventObject', eventObject)

            // make the event draggable using jQuery UI
            $(this).draggable({
                zIndex        : 1070,
                revert        : false, // will cause the event to go back to its
                revertDuration: 0  //  original position after the drag
            })

        })
    }

    init_events($('#external-events div.external-event'))

    /* initialize the calendar
     -----------------------------------------------------------------*/
    //Date for the calendar events (dummy data)
    var date = new Date();
    var d    = date.getDate(),
        m    = date.getMonth(),
        y    = date.getFullYear();

    // console.log(new Date(y, m, d) + 'Kuntul');

    $.ajax({
        url: HOST_URL + 'trans/ess_calendar/api_postdata' + '?var=' + $('[name="niksession"]').val(),
        async: false,
        type: "POST",
        global: false,
        dataType: 'json',
        success: function (data) {
            eventsx = data;

        }
    });
    $('#calendar').fullCalendar({
        header    : {
            left  : 'prev,next today',
            center: 'title',
            right : 'month',
            //right : 'month,agendaWeek,agendaDay'
        },
        buttonText: {
            today: 'today',
            month: 'month',
            // week : 'week',
            // day  : 'day'
        },
        events    : eventsx,
        //Random default events
        //colour
        //backgroundColor: '#f56954', //red
        //backgroundColor: '#0073b7', //Blue
        //backgroundColor: '#f39c12', //yellow
        //backgroundColor: '#00c0ef', //Info (aqua)
        //backgroundColor: '#00a65a', //Success (green)
        //backgroundColor: '#00a65a', //Success (green)
        /* events    : [
            {
                title          : '07:30',
                start          : '2021-09-01',
                end            : '',
                backgroundColor: '#00a65a', //Success (green)
                borderColor    : '#00a65a' //Success (green)
            },
            {
                title          : '17:10',
                start          : '2021-09-01',
                backgroundColor: '#0073b7', //Blue
                borderColor    : '#0073b7' //Blue
            },
            {
                title          : 'Long Event',
                start          : new Date(y, m, d - 5),
                end            : new Date(y, m, d - 2),
                backgroundColor: '#f39c12', //yellow
                borderColor    : '#f39c12' //yellow
            },
            {
                title          : 'Meeting',
                start          : new Date(y, m, d, 10, 30),
                allDay         : false,
                backgroundColor: '#0073b7', //Blue
                borderColor    : '#0073b7' //Blue
            },
            {
                title          : 'Lunch',
                start          : new Date(y, m, d, 12, 0),
                end            : new Date(y, m, d, 14, 0),
                allDay         : false,
                backgroundColor: '#00c0ef', //Info (aqua)
                borderColor    : '#00c0ef' //Info (aqua)
            },
            {
                title          : 'Birthday Party',
                start          : new Date(y, m, d + 1, 22, 0),
                end            : new Date(y, m, d + 1, 22, 30),
                allDay         : false,
                backgroundColor: '#00a65a', //Success (green)
                borderColor    : '#00a65a' //Success (green)
            },
            {
                title          : 'Click for Google',
                start          : new Date(y, m, 28),
                end            : new Date(y, m, 29),
                url            : 'http://google.com/',
                backgroundColor: '#00a65a', //Success (green)
                borderColor    : '#3c8dbc' //Primary (light-blue)
            }
        ], */
        eventClick: function(event) {
            if (event.url) {
                return false;
            }
        },
        editable  : false,
        clickable : false,
        droppable : false, // this allows things to be dropped onto the calendar !!!
        drop      : function (date, allDay) { // this function is called when something is dropped

            // retrieve the dropped element's stored Event Object
            var originalEventObject = $(this).data('eventObject')

            // we need to copy it, so that multiple events don't have a reference to the same object
            var copiedEventObject = $.extend({}, originalEventObject)

            // assign it the date that was reported
            copiedEventObject.start           = date
            copiedEventObject.allDay          = allDay
            copiedEventObject.backgroundColor = $(this).css('background-color')
            copiedEventObject.borderColor     = $(this).css('border-color')

            // render the event on the calendar
            // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
            $('#calendar').fullCalendar('renderEvent', copiedEventObject, true)

            // is the "remove after drop" checkbox checked?
            if ($('#drop-remove').is(':checked')) {
                // if so, remove the element from the "Draggable Events" list
                $(this).remove()
            }

        }
    })

    /* ADDING EVENTS */
    var currColor = '#3c8dbc' //Red by default
    //Color chooser button
    var colorChooser = $('#color-chooser-btn')
    $('#color-chooser > li > a').click(function (e) {
        e.preventDefault()
        //Save color
        currColor = $(this).css('color')
        //Add color effect to button
        $('#add-new-event').css({ 'background-color': currColor, 'border-color': currColor })
    })
    $('#add-new-event').click(function (e) {
        e.preventDefault()
        //Get value and make sure it is not null
        var val = $('#new-event').val()
        if (val.length == 0) {
            return
        }

        //Create events
        var event = $('<div />')
        event.css({
            'background-color': currColor,
            'border-color'    : currColor,
            'color'           : '#fff'
        }).addClass('external-event')
        event.html(val)
        $('#external-events').prepend(event)

        //Add draggable funtionality
        init_events(event)

        //Remove event from text input
        $('#new-event').val('')
    })

});