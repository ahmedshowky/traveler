jQuery(function($) {
    var parent = $('.form-add-booking-partner');
    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    if ($('input#check_in', parent).length && $('input#check_out', parent).length) {
        check_in = $('input#check_in', parent).datepicker({
            language: st_params.locale || '',
            format: $(this).data('date-format'),
            startDate: "today",
            todayBtn: "linked",
            todayHighlight: !0,
            weekStart: 1,
            autoclose: !0,
            weekStart: 1,
            format: $('[data-date-format]').data('date-format')
        });
        check_out = $('input#check_out', parent).datepicker({
            language: st_params.locale || '',
            format: $(this).data('date-format'),
            startDate: "today",
            todayBtn: "linked",
            todayHighlight: !0,
            autoclose: !0,
            format: $('[data-date-format]').data('date-format'),
            weekStart: 1
        });
        check_in.on('changeDate', function(e) {
            var new_date = e.date;
            new_date.setDate(new_date.getDate() + 1);
            $('input#check_out', parent).datepicker('setDates', new_date);
            $('input#check_out', parent).datepicker('setStartDate', new_date)
        })
    }
    if ($('input#check_in_car', parent).length && $('input#check_out_car', parent).length) {
        var check_in_car = $('input#check_in_car', parent).datepicker({
            language: st_params.locale || '',
            format: $(this).data('date-format'),
            startDate: "today",
            todayBtn: "linked",
            todayHighlight: !0,
            autoclose: !0,
            format: $('[data-date-format]').data('date-format'),
            weekStart: 1
        });
        var check_out_car = $('input#check_out_car', parent).datepicker({
            language: st_params.locale || '',
            format: $(this).data('date-format'),
            startDate: "today",
            todayBtn: "linked",
            todayHighlight: !0,
            autoclose: !0,
            format: $('[data-date-format]').data('date-format')
        });
        check_in_car.on('changeDate', function(e) {
            var new_date = e.date;
            new_date.setDate(new_date.getDate());
            $('input#check_out_car', parent).datepicker('setDates', new_date);
            $('input#check_out_car', parent).datepicker('setStartDate', new_date)
        })
    }
    $('.st_post_select_ajax', parent).each(function() {
        var me = $(this);
        $(this).select2({
            placeholder: me.data('placeholder'),
            minimumInputLength: 2,
            allowClear: !0,
            ajax: {
                url: st_params.ajax_url,
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page) {
                    return {
                        q: term,
                        action: 'st_post_select_ajax',
                        post_type: me.data('post-type'),
                        user_id: me.data('user-id')
                    }
                },
                results: function(data, page) {
                    return {
                        results: data.items
                    }
                },
                cache: !0
            },
            initSelection: function(element, callback) {
                var id = $(element).val();
                if (id !== "") {
                    var data = {
                        id: id,
                        name: $(element).data('pl-name'),
                        description: $(element).data('pl-desc')
                    };
                    callback(data)
                }
            },
            formatResult: function(state) {
                if (!state.id) return state.name;
                return state.name + '<p><em>' + state.description + '</em></p>'
            },
            formatSelection: function(state) {
                if (!state.id) return state.name;
                return state.name + '<p><em>' + state.description + '</em></p>'
            },
            escapeMarkup: function(m) {
                return m
            }
        })
    });
    $('input#hotel_id', parent).on('change', function(event) {
        var hotel_id = $(this).val();
        var user_id = $(this).data('user-id');
        var data = {
            action: 'st_partnerGetListRoom',
            hotel_id: hotel_id,
            user_id: user_id
        };
        $('#overlay', parent).addClass('active');
        $.post(st_params.ajax_url, data, function(respon, textStatus, xhr) {
            $('#overlay', parent).removeClass('active');
            if (typeof respon == 'object') {
                $('input#room_id', parent).select2({
                    data: respon
                })
            }
        }, 'json')
    });
    $('input#room_id', parent).on('change', function(event) {
        $('input#item_price', parent).val('');
        $('#extra-price-wrapper', parent).html('');
        var room_id = $(this).val();
        if (typeof room_id != 'undefined' && parseInt(room_id) > 0) {
            $('#overlay', parent).addClass('active');
            data = {
                action: 'st_getRoomHotelInfo',
                room_id: room_id
            };
            $.post(st_params.ajax_url, data, function(respon, textStatus, xhr) {
                $('#overlay', parent).removeClass('active');
                if (typeof respon == 'object') {
                    $('input#item_price', parent).val(respon.price);
                    $('input.room_id', parent).val(room_id);
                    $('span#label_room_price', parent).html(respon.price);
                    $('#extra-price-wrapper', parent).html(respon.extras);
                    $(parent).iCheck({
                        checkboxClass: 'i-check',
                        radioClass: 'i-radio'
                    });
                    $('#adult-wrapper', parent).html(respon.adult_html);
                    $('#child-wrapper', parent).html(respon.child_html);
                    $('#room-wrapper', parent).html(respon.room_html);
                    $('#required_name_html', parent).html(respon.required_name_html);
                    $('.st_check_book_none', parent).show();
                    $("#form-add-booking-partner").trigger("triggerGuestPartner");
                }
            }, 'json');
            
        }
    });
    $('input#rental_id', parent).on('change', function(event) {
        $('input#item_price', parent).val('');
        $('#extra-price-wrapper', parent).html('');
        var rental_id = $(this).val();
        if (typeof rental_id != 'undefined' && parseInt(rental_id) > 0) {
            $('#overlay', parent).addClass('active');
            data = {
                action: 'st_getRentalInfo',
                rental_id: rental_id
            };
            $.post(st_params.ajax_url, data, function(respon, textStatus, xhr) {
                $('#overlay', parent).removeClass('active');
                if (typeof respon == 'object') {
                    $('input#item_price', parent).val(respon.price);
                    $('#extra-price-wrapper', parent).html(respon.extras);
                    $(parent).iCheck({
                        checkboxClass: 'i-check',
                        radioClass: 'i-radio'
                    });
                    $('#adult-wrapper', parent).html(respon.adult_html);
                    $('#child-wrapper', parent).html(respon.child_html);
                    $('.st_check_book_none', parent).show();
                    $('#required_name_html', parent).html(respon.required_name_html);
                    $("#form-add-booking-partner").trigger("triggerGuestPartner");
                }
            }, 'json')
        }
    });
    $('input#tour_id', parent).on('change', function(event) {
        var tour_id = $(this).val();
        if (typeof tour_id != 'undefined' && parseInt(tour_id) > 0) {
            $('#overlay', parent).addClass('active');
            data = {
                action: 'st_getInfoTour',
                tour_id: tour_id
            };
            $.post(st_params.ajax_url, data, function(respon, textStatus, xhr) {
                $('#overlay', parent).removeClass('active');
                if (typeof respon == 'object') {
                    $('#type-tour-wrapper', parent).html(respon.type_tour);
                    $('span#label_type_tour', parent).html(respon.label_type_tour);
                    $('input#max_people', parent).val(respon.max_people);
                    $('span#label_max_people', parent).html(respon.max_people);
                    $('input#adult_price', parent).val(respon.adult_price);
                    $('span#label_adult_price', parent).html(respon.adult_price);
                    $('input#child_price', parent).val(respon.child_price);
                    $('span#label_child_price', parent).html(respon.child_price);
                    $('input#infant_price', parent).val(respon.infant_price);
                    $('span#label_infant_price', parent).html(respon.infant_price);
                    $('#adult-wrapper', parent).html(respon.adult_html);
                    $('#child-wrapper', parent).html(respon.child_html);
                    $('#infant-wrapper', parent).html(respon.infant_html);
                    $('#required_name_html', parent).html(respon.required_name_html);
                    $('#extra-price-wrapper', parent).html(respon.extras);
                    $('.st_check_book_none', parent).show();
                    if ($(".check-in-out-input").length > 0) {
                        $(".check-in-out-input").data('tour-id',tour_id);
                    }
                    if ($('#starttime_hidden_load_form').length > 0) {
                        $('#starttime_hidden_load_form').attr('data-tourid',tour_id);
                    } else {
                    }
                }
            }, 'json')
        }
    });
    $('input#activity_id', parent).on('change', function(event) {
        var activity_id = $(this).val();
        if (typeof activity_id != 'undefined' && parseInt(activity_id) > 0) {
            $('#overlay', parent).addClass('active');
            data = {
                action: 'st_getInfoActivity',
                activity_id: activity_id
            };
            $.post(st_params.ajax_url, data, function(respon, textStatus, xhr) {
                $('#overlay', parent).removeClass('active');
                if (typeof respon == 'object') {
                    var check_in = $('input#check_in', parent);
                    var check_out = $('input#check_out', parent);
                    if (respon.activity_text == 'daily_activity') {
                        $('input#check_out_activity', parent).attr('data-duration', respon.duration);
                        check_in.on('changeDate', function(e) {
                            var new_date = e.date;
                            new_date.setDate(new_date.getDate() + parseInt(respon.duration));
                            check_out.datepicker('setDates', new_date)
                        });
                        check_out.on('show', function(e) {
                            check_out.datepicker('hide')
                        })
                    } else {
                        var date_in = new Date(respon.check_in);
                        var date_out = new Date(respon.check_out);
                        check_in.datepicker('setDates', date_in);
                        check_out.datepicker('setDates', date_out);
                        check_in.on('show', function(e) {
                            check_in.datepicker('hide')
                        });
                        check_out.on('show', function(e) {
                            check_out.datepicker('hide')
                        })
                    }
                    $('#type-activity-wrapper', parent).html(respon.type_activity);
                    $('#label_type_activity', parent).html(respon.label_type_activity);
                    $('input#max_people', parent).val(respon.max_people);
                    $('input#adult_price', parent).val(respon.adult_price);
                    $('input#child_price', parent).val(respon.child_price);
                    $('input#infant_price', parent).val(respon.infant_price);
                    $('#adult-wrapper', parent).html(respon.adult_html);
                    $('#child-wrapper', parent).html(respon.child_html);
                    $('#infant-wrapper', parent).html(respon.infant_html);
                    $('#extra-price-wrapper', parent).html(respon.extras);
                    $('span#label_max_people', parent).html(respon.max_people);
                    $('span#label_adult_price', parent).html(respon.adult_price);
                    $('span#label_child_price', parent).html(respon.child_price);
                    $('span#label_infant_price', parent).html(respon.infant_price);
                    $('#required_name_html', parent).html(respon.required_name_html);
                    if ($(".check-in-out-input").length > 0) {
                        $(".check-in-out-input").data('tour-id',activity_id);
                    }

                    if ($('#starttime_hidden_load_form').length > 0) {
                        $('#starttime_hidden_load_form', parent).data('tourid',activity_id);
                    } else {
                    }

                    $('.st_check_book_none', parent).show();
                }
            }, 'json')
        }
    });
    var list_selected_equipment = [];
    $('input#car_id.st_post_select_ajax', parent).on('change', function(event) {
        var car_id = $(this).val();
        if (typeof car_id != 'undefined' && parseInt(car_id) > 0) {
            $('#overlay', parent).addClass('active');
            data = {
                action: 'st_getInfoCarPartner',
                car_id: car_id
            };
            $.post(st_params.ajax_url, data, function(respon, textStatus, xhr) {
                $('#overlay', parent).removeClass('active');
                if (typeof respon == 'object') {
                    $('input#item_price', parent).val(respon.price);
                    $('span#label_price_car', parent).html(respon.price);
                    $('#equipments-price-wrapper', parent).html(respon.item_equipment);
                    $(parent).iCheck({
                        checkboxClass: 'i-check',
                        radioClass: 'i-radio'
                    });
                    $('input.list_equipment', parent).on('ifChanged', function(event) {
                        if ($(this).prop('checked') == !0) {
                            list_selected_equipment.push({
                                title: $(this).attr('data-title'),
                                price: str2num($(this).attr('data-price')),
                                price_unit: $(this).data('price-unit'),
                                price_max: $(this).data('price-max')
                            })
                        }
                        $('input#selected_equipments', parent).val(JSON.stringify(list_selected_equipment))
                    });
                    $('.st_check_book_none', parent).show();
                }
            }, 'json')
        }
    });
    $('select#car_id.st_post_select_carstransfer_ajax', parent).on('change', function(event) {
        var car_id = $(this).val();
        var car_post_type = $(this).data('post-type');
        if (typeof car_id != 'undefined' && parseInt(car_id) > 0) {
            $('#overlay', parent).addClass('active');
            data = {
                action: 'st_getInfoCarTranferPartner',
                car_id: car_id,
                car_post_type : car_post_type,
            };
            $.post(st_params.ajax_url, data, function(respon, textStatus, xhr) {
                $('#overlay', parent).removeClass('active');
                if (typeof respon == 'object') {
                    $('input#item_price', parent).val(respon.price);
                    $('#equipments-price-wrapper', parent).html(respon.item_equipment);
                    $(parent).iCheck({
                        checkboxClass: 'i-check',
                        radioClass: 'i-radio'
                    });
                    $('input.list_equipment', parent).on('ifChanged', function(event) {
                        if ($(this).prop('checked') == !0) {
                            list_selected_equipment.push({
                                title: $(this).attr('data-title'),
                                price: str2num($(this).attr('data-price')),
                                price_unit: $(this).data('price-unit'),
                                price_max: $(this).data('price-max')
                            })
                        }
                        $('input#selected_equipments', parent).val(JSON.stringify(list_selected_equipment))
                    });
                    $('.st_check_book_none', parent).show();
                }
            }, 'json')
        }
    });
    function str2num(val) {
        val = '0' + val;
        val = parseFloat(val);
        return val
    }
    var flag_cart = 1;
    var flag = !1;
    var form_validate = !0;
    $('#partner-booking-button', parent).on('click', function(event) {
        event.preventDefault();
        $('input.required,select.required,textarea.required', parent).removeClass('error');
        $('input.required,select.required,textarea.required', parent).each(function() {
            if (!$(this).val()) {
                $(this).addClass('error');
                form_validate = !1
            }
        });
        if (typeof form_validate == 'undefined' || form_validate == !1) {
            $('.form_alert', parent).addClass('alert-danger').removeClass('hidden');
            $('.form_alert', parent).html(st_checkout_text.validate_form);
            return !1
        }
        var data = parent.serializeArray();
        $('.form_alert', parent).removeClass('alert-danger').addClass('hidden').html('');
        if (typeof data == 'object') {
            if (flag) return !1;
            flag = !0;
            $('#overlay', parent).addClass('active');
            $.ajax({
                url: st_params.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json'
            }).done(function(respon) {
                if (respon.status == false) {
                    if (respon.message) {
                        $('.form_alert', parent).addClass('alert-danger').removeClass('hidden');
                        $('.form_alert', parent).html(respon.message);
                        $('#overlay', parent).removeClass('active');
                    }
                }
                if (respon.status == 'partner') {
                    if (flag_cart <= 2) {
                        flag = !1;
                        $('#partner-booking-button').trigger('click');
                        flag_cart++;
                        $('#overlay', parent).addClass('active');
                    }
                    if (flag_cart > 2) {
                        flag_cart = 1;
                        $('#overlay', parent).removeClass('active');
                    }
                }
                if (respon.redirect) {
                    window.location.href = respon.redirect
                }
            }).fail(function(error) {
                console.error("error", error)
            }).always(function(dataOrjqXHR, textStatus, jqXHRorErrorThrown) {

                /*$('#overlay',parent).removeClass('active')*/ ;
                flag = !1
            })
        }
    });
    class TourCalendar {
        constructor(container) {
            var self = this;
            this.container = container;
            this.calendar = null;
            this.form_container = null;
            this.init = function () {
                self.container = container;
                self.calendar = $('.calendar-content', self.container);
                self.form_container = $('.calendar-form', self.container);
                self.initCalendar();
            };
            this.initCalendar = function () {
                self.calendar.fullCalendar({
                    firstDay: 1,
                    lang: st_params.locale,
                    timezone: st_timezone.timezone_string,
                    customButtons: {
                        reloadButton: {
                            text: st_params.text_refresh,
                            click: function () {
                                self.calendar.fullCalendar('refetchEvents');
                            }
                        }
                    },
                    header: {
                        left: 'today,reloadButton',
                        center: 'title',
                        right: 'prev, next'
                    },
                    selectable: !0,
                    select: function (start, end, jsEvent, view) {
                        var start_date = new Date(start._d).toString("MM");
                        var end_date = new Date(end._d).toString("MM");
                        var today = new Date().toString("MM");
                        if (start_date < today || end_date < today) {
                            self.calendar.fullCalendar('unselect');
                        }
                    },
                    events: function (start, end, timezone, callback) {
                        var events = [];
                        $.ajax({
                            url: st_params.ajax_url,
                            dataType: 'json',
                            type: 'post',
                            data: {
                                action: 'st_get_availability_tour_frontend',
                                tour_id: $('input#tour_id').val(),
                                start: start.unix(),
                                end: end.unix()
                            },
                            success: function (doc) {
                                if (typeof doc === 'object') {
                                    if (typeof doc.events === 'object') {
                                        events = doc.events;
                                    }
                                } else {
                                    console.log('Can not get data');
                                }
                                callback(events);
                            },
                            error: function (e) {
                                alert('Can not get the availability slot. Lost connect with your sever');
                            }
                        });
                    },
                    eventClick: function (event, element, view) { },
                    eventMouseover: function (event, jsEvent, view) {
                        $('.event-number-' + event.start.unix()).addClass('hover');
                    },
                    eventMouseout: function (event, jsEvent, view) {
                        $('.event-number-' + event.start.unix()).removeClass('hover');
                    },
                    eventRender: function (event, element, view) {
                        var html = event.day;
                        var html_class = "none";
                        if (typeof event.date_end != 'undefined') {
                            html += ' - ' + event.date_end;
                            html_class = "group";
                        }
                        var today_y_m_d = new Date().getFullYear() + "-" + (new Date().getMonth() + 1) + "-" + new Date().getDate();
                        if (event.status == 'available') {
                            var title = "";
                            if (event.adult_price != 0) {
                                title += st_checkout_text.adult_price + ': ' + event.adult_price + " <br/>";
                            }
                            if (event.child_price != 0) {
                                title += st_checkout_text.child_price + ': ' + event.child_price + " <br/>";
                            }
                            if (event.infant_price != 0) {
                                title += st_checkout_text.infant_price + ': ' + event.infant_price;
                            }
                            html = "<button data-placement='top' title='" + title + "' data-toggle='tooltip' class='" + html_class + " btn btn-available'>" + html;
                        } else {
                            html = "<button disabled data-placement='top' title='Disabled' data-toggle='tooltip' class='" + html_class + " btn btn-disabled'>" + html;
                        }
                        if (today_y_m_d === event.date) {
                            html += "<span class='triangle'></span>";
                        }
                        html += "</button>";
                        element.addClass('event-' + event.id);
                        element.addClass('event-number-' + event.start.unix());
                        $('.fc-content', element).html(html);
                        element.on('click', function () {
                            date = $.fullCalendar.moment(event.start._i).format(st_params.date_format.toUpperCase());
                            $('input#check_in_tour').val(date);
                            if (typeof event.end != 'undefined' && event.end && typeof event.end._i != 'undefined') {
                                date = new Date(event.end._i);
                                date.setDate(date.getDate() - 1);
                                date = $.fullCalendar.moment(date).format(st_params.date_format.toUpperCase());
                                $('input#check_out_tour').val(date).parents('.form-group').show();
                            } else {
                                date = $.fullCalendar.moment(event.start._i).format(st_params.date_format.toUpperCase());
                                $('input#check_out_tour').val(date).parents('.form-group').hide();
                            }
                            $('input#adult_price').val(event.adult_price);
                            $('input#child_price').val(event.child_price);
                            $('input#infant_price').val(event.infant_price);
                        });
                    },
                    loading: function (isLoading, view) {
                        if (isLoading) {
                            $('.calendar-wrapper-inner .overlay-form').fadeIn();
                        } else {
                            $('.calendar-wrapper-inner .overlay-form').fadeOut();
                        }
                    },
                });
            };
        }
    }
    $('input#check_out_tour').parents('.form-group').hide();

    class ActivityCalendar {
        constructor(container) {
            var self = this;
            this.container = container;
            this.calendar = null;
            this.form_container = null;
            this.init = function () {
                self.container = container;
                self.calendar = $('.calendar-content', self.container);
                self.form_container = $('.calendar-form', self.container);
                self.initCalendar();
            };
            this.initCalendar = function () {
                self.calendar.fullCalendar({
                    firstDay: 1,
                    lang: st_params.locale,
                    timezone: st_timezone.timezone_string,
                    customButtons: {
                        reloadButton: {
                            text: st_params.text_refresh,
                            click: function () {
                                self.calendar.fullCalendar('refetchEvents');
                            }
                        }
                    },
                    header: {
                        left: 'prev',
                        center: 'title',
                        right: 'next'
                    },
                    contentHeight: 360,
                    select: function (start, end, jsEvent, view) {
                        var start_date = new Date(start._d).toString("MM");
                        var end_date = new Date(end._d).toString("MM");
                        var today = new Date().toString("MM");
                        if (start_date < today || end_date < today) {
                            self.calendar.fullCalendar('unselect');
                        }
                    },
                    events: function (start, end, timezone, callback) {
                        $.ajax({
                            url: st_params.ajax_url,
                            dataType: 'json',
                            type: 'post',
                            data: {
                                action: 'st_get_availability_activity_frontend',
                                activity_id: $('input#activity_id').val(),
                                start: start.unix(),
                                end: end.unix()
                            },
                            success: function (doc) {
                                if (typeof doc == 'object') {
                                    callback(doc);
                                }
                            },
                            error: function (e) {
                                alert('Can not get the availability slot. Lost connect with your sever');
                            }
                        });
                    },
                    eventClick: function (event, element, view) { },
                    eventMouseover: function (event, jsEvent, view) { },
                    eventMouseout: function (event, jsEvent, view) { },
                    eventRender: function (event, element, view) {
                        var html = event.day;
                        var html_class = "none";
                        if (typeof event.date_end != 'undefined') {
                            html += ' - ' + event.date_end;
                            html_class = "group";
                        }
                        var today_y_m_d = new Date().getFullYear() + "-" + (new Date().getMonth() + 1) + "-" + new Date().getDate();
                        if (event.status == 'available') {
                            var title = "";
                            if (event.adult_price != 0) {
                                title += st_checkout_text.adult_price + ': ' + event.adult_price + " <br/>";
                            }
                            if (event.child_price != 0) {
                                title += st_checkout_text.child_price + ': ' + event.child_price + " <br/>";
                            }
                            if (event.infant_price != 0) {
                                title += st_checkout_text.infant_price + ': ' + event.infant_price;
                            }
                            html = "<button data-placement='top' title  = '" + title + "' data-toggle='tooltip' class='" + html_class + " btn btn-available'>" + html;
                        } else {
                            html = "<button disabled data-placement='top' title  = 'Disabled' data-toggle='tooltip' class='" + html_class + " btn btn-disabled'>" + html;
                        }
                        if (today_y_m_d === event.date) {
                            html += "<span class='triangle'></span>";
                        }
                        html += "</button>";
                        element.addClass('event-' + event.id);
                        element.addClass('event-number-' + event.start.unix());
                        $('.fc-content', element).html(html);
                        element.on('click', function () {
                            date = $.fullCalendar.moment(event.start._i).format(st_params.date_format.toUpperCase());
                            $('input#check_in_activity').val(date);
                            if (typeof event.end != 'undefined' && event.end && typeof event.end._i != 'undefined') {
                                date = new Date(event.end._i);
                                date.setDate(date.getDate() - 1);
                                date = $.fullCalendar.moment(date).format(st_params.date_format.toUpperCase());
                                $('input#check_out_activity').val(date).parents('.form-group').show();
                            } else {
                                date = $.fullCalendar.moment(event.start._i).format(st_params.date_format.toUpperCase());
                                $('input#check_out_activity').val(date).parents('.form-group').hide();
                            }
                            $('input#adult_price').val(event.adult_price);
                            $('input#child_price').val(event.child_price);
                            $('input#infant_price').val(event.infant_price);
                        });
                    },
                    eventAfterRender: function (event, element, view) {
                        $('[data-toggle="tooltip"]').tooltip({
                            html: !0
                        });
                    },
                    loading: function (isLoading, view) {
                        if (isLoading) {
                            $('.calendar-wrapper-inner .overlay-form').fadeIn();
                        } else {
                            $('.calendar-wrapper-inner .overlay-form').fadeOut();
                        }
                    },
                });
            };
        }
    }
    /*Car Tranfer*/
    // $('.st_post_select_carstransfer_ajax', parent).each(function() {
    //     var me = $(this);
    //     $(this).select2({
    //         placeholder: me.data('placeholder'),
    //         minimumInputLength: 2,
    //         allowClear: !0,
    //         ajax: {
    //             url: st_params.ajax_url,
    //             dataType: 'json',
    //             quietMillis: 250,
    //             data: function(term, page) {
    //                 return {
    //                     q: term,
    //                     action: 'st_post_select_carstransfer_ajax',
    //                     post_type: me.data('post-type'),
    //                     user_id: me.data('user-id')
    //                 }
    //             },
    //             results: function(data, page) {
    //                 return {
    //                     results: data.items
    //                 }
    //             },
    //             cache: !0
    //         },
    //         initSelection: function(element, callback) {
    //             var id = $(element).val();
    //             if (id !== "") {
    //                 var data = {
    //                     id: id,
    //                     name: $(element).data('pl-name'),
    //                     description: $(element).data('pl-desc')
    //                 };
    //                 callback(data)
    //             }
    //         },
    //         formatResult: function(state) {
    //             if (!state.id) return state.name;
    //             return state.name + '<p><em>' + state.description + '</em></p>'
    //         },
    //         formatSelection: function(state) {
    //             if (!state.id) return state.name;
    //             return state.name + '<p><em>' + state.description + '</em></p>'
    //         },
    //         escapeMarkup: function(m) {
    //             return m
    //         }
    //     })
    // });
    /*transfer_to Car transfer*/
    $('#transfer_from.st_transfer_from_select_ajax', parent).each(function() {
        var me = $(this);
        $(this).select2({
            placeholder: me.data('placeholder'),
            minimumInputLength: 2,
            allowClear: !0,
            ajax: {
                url: st_params.ajax_url,
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page) {
                    return {
                        q: term,
                        action: 'st_transfer_from_select_ajax',
                        post_type: me.data('post-type'),
                        user_id: me.data('user-id')
                    }
                },
                results: function(data, page) {
                    return {
                        results: data.items
                    }
                },
                cache: !0
            },
            initSelection: function(element, callback) {
                var id = $(element).val();
                if (id !== "") {
                    var data = {
                        id: id,
                        name: $(element).data('pl-name'),
                        description: $(element).data('pl-desc')
                    };
                    callback(data);
                }
            },
            formatResult: function(state) {
                if (!state.id) return state.name;
                return state.name + '<p><em>' + state.description + '</em></p>'
            },
            formatSelection: function(state) {
                if (!state.id) return state.name;
                return state.name + '<p><em>' + state.description + '</em></p>'
            },
            escapeMarkup: function(m) {
                return m
            }
        })
    });
    $('#transfer_to.st_transfer_to_select_ajax', parent).each(function() {
        var me = $(this);
        $(this).select2({
            placeholder: me.data('placeholder'),
            minimumInputLength: 2,
            allowClear: !0,
            ajax: {
                url: st_params.ajax_url,
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page) {
                    return {
                        q: term,
                        action: 'st_transfer_to_select_ajax',
                        post_type: me.data('post-type'),
                        user_id: me.data('user-id')
                    }
                },
                results: function(data, page) {
                    return {
                        results: data.items
                    }
                },
                cache: !0
            },
            initSelection: function(element, callback) {
                var id = $(element).val();
                if (id !== "") {
                    var data = {
                        id: id,
                        name: $(element).data('pl-name'),
                        description: $(element).data('pl-desc')
                    };
                    callback(data);
                }
            },
            formatResult: function(state) {
                if (!state.id) return state.name;
                return state.name + '<p><em>' + state.description + '</em></p>'
            },
            formatSelection: function(state) {
                if (!state.id) return state.name;
                return state.name + '<p><em>' + state.description + '</em></p>'
            },
            escapeMarkup: function(m) {
                return m
            }
        });
    });
    //  $('.st_post_select_carstransfer_ajax', parent).each(function() {
    //     var me = $(this);
    //     $(this).select2({
    //         placeholder: me.data('placeholder'),
    //         minimumInputLength: 2,
    //         allowClear: !0,
    //         ajax: {
    //             url: st_params.ajax_url,
    //             dataType: 'json',
    //             quietMillis: 250,
    //             data: function(term, page) {
    //                 return {
    //                     q: term,
    //                     action: 'st_post_select_carstransfer_ajax',
    //                     post_type: me.data('post-type'),
    //                     user_id: me.data('user-id'),
    //                     transfer_from: $('#transfer_from.st_transfer_from_select_ajax').val(),
    //                     transfer_to: $('#transfer_to.st_transfer_to_select_ajax').val(),
    //                 }
    //             },
    //             results: function(data, page) {
    //                 return {
    //                     results: data.items
    //                 }
    //             },
    //             cache: !0
    //         },
    //         initSelection: function(element, callback) {
    //             var id = $(element).val();
    //             if (id !== "") {
    //                 var data = {
    //                     id: id,
    //                     name: $(element).data('pl-name'),
    //                     description: $(element).data('pl-desc')
    //                 };
    //                 callback(data)
    //             }
    //         },
    //         formatResult: function(state) {
    //             if (!state.id) return state.name;
    //             return state.name + '<p><em>' + state.description + '</em></p>'
    //         },
    //         formatSelection: function(state) {
    //             if (!state.id) return state.name;
    //             return state.name + '<p><em>' + state.description + '</em></p>'
    //         },
    //         escapeMarkup: function(m) {
    //             return m
    //         }
    //     })
    // });
    $('#transfer_from.st_transfer_from_select_ajax', parent).on('change', function(event) {
        var transfer_from_val = $('#transfer_from.st_transfer_from_select_ajax').val();
        var transfer_to_val = $('#transfer_to.st_transfer_to_select_ajax').val();
        if( (typeof transfer_from_val !== 'undefined')  &&  (transfer_from_val.length > 0) && (typeof transfer_to_val !== 'undefined')  &&  (transfer_to_val.length > 0)) {
            $('.st_check_book_none').show();
            $("#car_id.st_post_select_carstransfer_ajax").select2();
            $.ajax({
                url: st_params.ajax_url,
                type: "GET",
                data: {
                    'action': 'st_post_select_carstransfer_ajax',
                    'transfer_from': transfer_from_val,
                    'transfer_to': transfer_to_val,
                },
                dataType: "json",
                beforeSend: function () {
                   $('#overlay', parent).addClass('active');
                },
                error : function(jqXHR, textStatus, errorThrown) {
                      $("#aLoad").remove();
                      console.log("ERRO" +jqXHR + "is" + errorThrown );
                    },
                success : function(res){
                    $('#overlay', parent).removeClass('active');
                },
                complete: function (xhr, status) {
                   //$data = $(xhr.responseJSON.html);
                    $('#car_id.st_post_select_carstransfer_ajax').html(xhr.responseJSON.html);
                    // if($data.length){
                    //     element.attr('data-paged', xhr.responseJSON.paged);
                    //     element.attr('data-index', xhr.responseJSON.index);
                    //     loadmore.hide();
                    //     buttonloadmore.show();
                    // } else {
                    //     loadmore.hide();
                    //     offloadmore.remove();
                    // }
                }
            });
        }
    });
    $('#transfer_to.st_transfer_to_select_ajax', parent).on('change', function(event) {
        var transfer_from_val = $('#transfer_from.st_transfer_from_select_ajax').val();
        var transfer_to_val = $('#transfer_to.st_transfer_to_select_ajax').val();
        if( (typeof transfer_from_val !== 'undefined')  &&  (transfer_from_val.length > 0) && (typeof transfer_to_val !== 'undefined')  &&  (transfer_to_val.length > 0)) {
            $('.st_check_book_none').show();
            $("#car_id.st_post_select_carstransfer_ajax").select2();
            $.ajax({
                url: st_params.ajax_url,
                type: "GET",
                data: {
                    'action': 'st_post_select_carstransfer_ajax',
                    'transfer_from': transfer_from_val,
                    'transfer_to': transfer_to_val,
                },
                dataType: "json",
                beforeSend: function () {
                   $('#overlay', parent).addClass('active');
                },
                error : function(jqXHR, textStatus, errorThrown) {
                      $("#aLoad").remove();
                      console.log("ERRO" +jqXHR + "is" + errorThrown );
                    },
                success : function(res){
                    $('#overlay', parent).removeClass('active');
                },
                complete: function (xhr, status) {
                    $('#car_id.st_post_select_carstransfer_ajax').html(xhr.responseJSON.html);
                }
            });
        }
    });
    $('input#check_out_activity').parents('.form-group').hide();

})
