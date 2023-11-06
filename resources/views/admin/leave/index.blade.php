@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div id="calendar">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->

    <!-- Modal -->
    <div class="modal fade" id="leaveModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Leave Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" style="display:none"></div>
                    <form class="leave" action="{{ route('leave.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="id" name="id" >

                        <div class="form-group">
                            <label>All Day</label>
                            <input type="checkbox" id="allDay" name="allDay" value="1">
                        </div>
                        <br>
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="text" name="start" id="start" class="form-control"/>
                        </div>
                       <br>
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="text" name="end" id="end" class="form-control"/>
                        </div>
                        <br>
                        <div class="form-group">
                            <label>Leave Type</label>
                            <select name="type" id="type" class="form-control"/>
                             <option value ="sick">Sick Leave</option>
                             <option value ="personal">Personal Leave</option>
                             <option value ="vacation">Vacation Leave</option>
                           </select>
                        </div>
                        <br>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="textarea form-control" id="description" cols="40" rows="5"></textarea>
                        </div>
                    </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger mr-auto" id="deleteBtn" onclick ='deleteEvent()'>Delete</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick ='submitLeaveRequest()'>Save changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
{{--boostrap--}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
{{--datetime picker    --}}
            <script type="module" src=" https://cdn.jsdelivr.net/npm/jquery-datetime-picker@2.5.11/build/jquery.datetimepicker.full.min.js "></script>
{{--fullcalendar  --}}
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js'></script>

        {{---moment--}}
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

            {{--       load calendar--}}
<script>
    var calendar = null;
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            initialDate: new Date(),
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
         // to fetch data in calendar
            events: {
                url: '{{ url('refetch-events') }}',
                method: 'GET',
                failure: function() {
                    alert('Failed to fetch events. Please try again later.');
                }
            },
            //to fetch description in calender
            eventContent: function (arg) {
                return {
                    html: `<b>${arg.event.extendedProps.type}</b><p>${arg.event.extendedProps.description}</p>`,
                };
            },
         // to open the modal
            dateClick: function (info) {
                      console.log(info)
                  let startDate, endDate ,allDay;

                  allDay = $('#allDay').prop('checked');
                if (allDay){
                    startDate = moment(info.date).format("YYYY-MM-DD");
                    endDate = moment(info.date).format("YYYY-MM-DD");
                    initializeStartDateEndDateFormat('Y-m-d', true);
                }else{
                    initializeStartDateEndDateFormat('Y-m-d H:i', false);
                    startDate = moment(info.date).format("YYYY-MM-DD HH:mm:ss");
                    endDate = moment(info.date).add(30,'minutes').format("YYYY-MM-DD HH:mm:ss");
                }
                // Set the id to null or an empty string to indicate a new record
                $('#id').val(null); // or $('#id').val(''); for an empty string
                $('#start').val(startDate);
                  $('#end').val(endDate);
                  modalReset();
                  $('#leaveModal').modal("show");

            },

            // to edit the data
            eventClick: function (info) {
                console.log(info);
                modalReset();
                const event = info.event;

                // Set the is_all_day checkbox
                $('#allDay').prop('checked', event.allDay);

                if(event.allDay) {
                    // Convert start and end dates to a user-friendly format
                    const startFormatted = moment(event.start).format('YYYY-MM-DD');
                    const endFormatted = moment(event.end).format('YYYY-MM-DD');
                    const selectedType = event.extendedProps.type;
                    // Populate modal fields
                    $('#id').val(event.id);
                    $('#start').val(startFormatted);
                    $('#end').val(endFormatted);
                    $('#type').val(selectedType);
                    $('#description').val(event.extendedProps.description);
                }else{
                    const startFormatted = moment(event.start).format('YYYY-MM-DD HH:mm:ss');
                    const endFormatted = moment(event.end).format('YYYY-MM-DD HH:mm:ss');
                    const selectedType = event.extendedProps.type;
                    // Populate modal fields
                    $('#id').val(event.id);
                    $('#start').val(startFormatted);
                    $('#end').val(endFormatted);
                    $('#type').val(selectedType);
                    $('#description').val(event.extendedProps.description);
                }

                // Determine the date format based on whether the event is all day
                if (event.allDay) {
                    initializeStartDateEndDateFormat('Y-m-d', true);
                } else {
                    initializeStartDateEndDateFormat('Y-m-d H:i', false);
                }
                const leaveType = $('#type').val();

                $('#leaveModal').modal('show');
                $('#deleteBtn').show();
            }
        });
        calendar.render();

        // check the whole day

        $('#allDay').change(function() {
            let is_all_day = $(this).prop('checked');
            if(is_all_day){
                let start = $('#start').val().slice(0,10);
                $('#start').val(start);
                let end = $('#end').val().slice(0,10);
                $('#end').val(end);
                initializeStartDateEndDateFormat('Y-m-d', is_all_day);
            }else{
                let start = $('#start').val().slice(0,10);
                $('#start').val(start + " 00:00 ");
                let end = $('#end').val().slice(0,10);
                $('#end').val(end + " 00:30 ");
                initializeStartDateEndDateFormat('Y-m-d H:i', is_all_day);
            }
        });
    })

    // initialize the date format
    function initializeStartDateEndDateFormat(format, allDay){
        let timePicker = !allDay;
        $('#start').datetimepicker({
             format: format,
              timepicker: timePicker
        });
        $('#end').datetimepicker({
            format: format,
            timepicker: timePicker
        });
    }
// reset modal
    function modalReset() {
        // Clear form fields or perform any other necessary actions
        // $('#start').val('');
        // $('#end').val('');
        $('#type').prop('selectedIndex', 0); // Reset the select to the first option
        $('#description').val('');
        $('#deleteBtn').hide();

    }

// save leave request data
    function submitLeaveRequest() {
        let id = $("#id").val();

        let url = '{{ route('leave.store') }}';
        let postData = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            allDay: $('#allDay').prop('checked') ? 1 : 0,
            start: $('#start').val(),
            end: $('#end').val(),
            type: $('#type').val(),
            description: $('#description').val(),
        };
        if(id){
            url = '{{ url('/leave') }}/' + id;
            postData._method ="PUT";
        }else{

        }

        $.ajax({
            type: 'POST',
            url: url,
            dataType: 'json',
            data: postData,
            success: function (res) {
                if (res.success) {
                    calendar.refetchEvents();

                    // Manually trigger the modal close event
                    $('#leaveModal').modal('hide');
                } else {
                    alert("Something is Wrong!!");
                }
            }
        });
    }


    function deleteEvent() {
        if (window.confirm("Are you sure you want to delete this?")) {
            // Reload the current page to reflect the changes
            location.reload();

            let id = $("#id").val();
            let url = '{{ url('/leave') }}/' + id;

            $.ajax({
                type: 'POST', // Use POST to simulate DELETE with _method
                data: {
                    _method: 'DELETE',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        calendar.refetchEvents();

                        // Close the modal after successful deletion
                        $('#leaveModal').modal('hide');

                    } else {
                        alert("Something is Wrong!!");
                    }
                }
            });
        }
    }



</script>
@endsection


