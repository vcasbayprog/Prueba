@extends('adminlte::page')

@section('title', 'Inicio')

@section('content_header')
    <h1></h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div id="calendar" class="calendar"></div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/js-year-calendar@latest/dist/js-year-calendar.min.css" />
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/tippy.js@4.3.5/themes/light.css" />

    <style>
        .sidebar-dark-orange {
            background-color: darkblue !important;
        }
    </style>
@stop

@section('js')
    <script src="https://unpkg.com/js-year-calendar@latest/dist/js-year-calendar.min.js"></script>
    <script src="https://unpkg.com/popper.js@1/dist/umd/popper.min.js"></script>
    <script src="https://unpkg.com/tippy.js@4"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js"></script>

    <script>
        // Español
        moment.locale('es');

        let tooltip = null;

        const calendar = new Calendar('#calendar', {
            dataSource: function({ year }) {
                return fetch(`/api/holidays?year=${year}`)
                    .then(response => response.json())
                    .then(holidays => {
                        return holidays.map(holiday => ({
                            startDate: new Date(holiday.date),
                            endDate: new Date(holiday.date),
                            name: holiday.name,
                            color: holiday.color,
                        }));
                    })
                    .catch(err => {
                        console.error('Error fetching holidays:', err);
                        return [];
                    });
            },
            mouseOnDay: function(e) {
                if (e.events.length > 0) {
                    var content = '';

                    for (var i in e.events) {
                        var formattedDate = moment(e.events[i].startDate).format('D [de] MMMM [del] YYYY');
                        content += '<div class="event-tooltip-content">' +
                            '<div class="event-name" style="color:' + e.events[i].color + '">' + e.events[i].name + '</div>' +
                            '<div class="event-date">' + formattedDate + '</div>' +
                            '</div>';
                    }

                    if (tooltip !== null) {
                        tooltip.destroy();
                        tooltip = null;
                    }

                    tooltip = tippy(e.element, {
                        placement: 'right',
                        content: content,
                        theme: 'light',
                        animateFill: false,
                        animation: 'shift-away',
                        arrow: true
                    });
                    tooltip.show();
                }
            },
            mouseOutDay: function() {
                if (tooltip !== null) {
                    tooltip.destroy();
                    tooltip = null;
                }
            },
            clickDay: function(e) {
                if (e.events.length > 0) {
                    var content = '';

                    for (var i in e.events) {
                        var formattedDate = moment(e.events[i].startDate).format('D [de] MMMM [del] YYYY');
                        content += '<div class="event-tooltip-content">' +
                            '<div class="event-name" style="color:' + e.events[i].color + '">' + e.events[i].name + '</div>' +
                            '<div class="event-date">' + formattedDate + '</div>' +
                            '</div>';
                    }

                    if (tooltip !== null) {
                        tooltip.destroy();
                        tooltip = null;
                    }

                    tooltip = tippy(e.element, {
                        placement: 'right',
                        content: content,
                        theme: 'light',
                        animateFill: false,
                        animation: 'shift-away',
                        arrow: true
                    });
                    tooltip.show();
                }
            }
        });
    </script>
@stop

