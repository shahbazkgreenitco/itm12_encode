<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/dist/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/theme/app.init.js') }}"></script>
<script src="{{ asset('assets/js/theme/theme.js') }}"></script>
<script src="{{ asset('assets/js/theme/app.min.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

<script src="{{ asset('js/common.js') }}"></script>
<script src="{{ asset('js/support_validate.js') }}"></script>
<script src="{{ asset('js/profile/index.js') }}"></script>

{{-- Companywise Script --}}
<script>
    const isLoggedIn = @json(auth()->check());
    $(document).ready(function () {
        const DEFAULT_COMPANY_ID = @json($dashboardCompanyId);
        const COMPANY_KEY = 'Default_Company';
        const $companySelect = $("#config-company");
        var select2Opts = { width: "100%" };
        let isInitializing = true;
        if (isLoggedIn) {
            $.ajax({
                type: 'GET',
                url: "{{ url('getCompanyByUserAccess') }}",
                success: function (response) {

                    if (!response.results || !response.results.length) {
                        return;
                    }

                    $companySelect.empty();

                    let selectedCompany = null;
                    let isAllSelected = (DEFAULT_COMPANY_ID == 0 || DEFAULT_COMPANY_ID == null);

                    let allOption = new Option("All", 0, isAllSelected, isAllSelected);
                    $companySelect.append(allOption);

                    if (isAllSelected) {
                        selectedCompany = { id: 0, text: "All" };
                    }

                    response.results.forEach(function (company) {

                        let isSelected = false;

                        if (DEFAULT_COMPANY_ID > 0 && company.id == DEFAULT_COMPANY_ID) {
                            isSelected = true;
                            selectedCompany = company;
                        }

                        let option = new Option(company.text, company.id, isSelected, isSelected);
                    $companySelect.append(option);
                    });

                $companySelect.trigger('change');
                    isInitializing = false;


                        localStorage.setItem(COMPANY_KEY, JSON.stringify({
                            id: selectedCompany.id,
                            text: selectedCompany.text,
                        }));
                }
            });
        }

        $companySelect.on('change', function () {
            if (isInitializing) return; 
            let data = $(this).select2('data');
            if (!data.length || !data[0].id) {
                localStorage.removeItem(COMPANY_KEY);
                return;
            }

            let companyData = {
                id: data[0].id,
                text: data[0].text
            };

            let previous = localStorage.getItem(COMPANY_KEY);
            let previousId = previous ? JSON.parse(previous).id : null;
            console.log(String(previousId),String(companyData.id));
            if (String(previousId) === String(companyData.id)) {
                return;
            }

            localStorage.setItem(COMPANY_KEY, JSON.stringify(companyData));
            $.ajax({
                url: "{{ route('user.store.default.company') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    company_id: companyData.id,
                    time: Date.now() 
                },
                success: function (res) {
                    console.log('Default company saved');
                    location.reload();
                },
                error: function (xhr) {
                    console.error('Failed to save company', xhr);
                }
            });
        });
        
        window.addEventListener('storage', function (event) {
            if (event.key !== COMPANY_KEY) return;
            if (!event.newValue) return;
            location.reload();
        });

        $(document).on("click", '#calendar', function () {
            $('#calendarMdl').modal("show");
        });

        $('#calendarMdl').on('shown.bs.modal', function () {
            if ($('#eventCalendar').data('fullCalendar')) {
                $('#eventCalendar').fullCalendar('destroy');
            }

        $('#eventCalendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            // Load events dynamically for the current view
            events: function(start, end, timezone, callback) {
                $.ajax({
                    url: '{{url("block-calendar/get-events")}}',
                    type: 'GET',
                    data: {
                        start: start.format('YYYY-MM-DD'), // Start date of the current view
                        end: end.format('YYYY-MM-DD')     // End date of the current view
                    },
                    dataType: 'json',
                    success: function(data) {
                        var events = data.map(function(event) {
                            return {
                                id: event.id, // Optional if you need the event ID
                                title: event.subject,
                                start: event.start_date_time,
                                end: event.end_date_time,
                                description: event.description,
                                avatar: event.avatar,
                                fullname: event.fullname,
                                avatar_type:event.avatar_type,
                                technician_role:event.technician_role,
                                attendees:event.attendees,
                            };
                        });
                        callback(events); // Pass the events to FullCalendar
                    },
                    error: function() {
                        alert("Error fetching events");
                    }
                });
            },
            editable: false,
            droppable: false,
            eventLimit: true,
            eventLimitClick: 'popover',
            eventRender: function(event, element) {
                var eventContent = `
                    <div style="display: flex;">
                        ${event.avatar_type === 'initials' ? 
                            `<div style="width: 20px; height: 20px; border-radius: 50%; background-color: #ccc; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold; margin-right: 5px;">${event.avatar}</div>` :
                            `<img src="${event.avatar}" alt="User Avatar" style="width: 20px; height: 20px; border-radius: 50%; margin-right: 5px;">`}
                        <span style="align-items: center;">
                            <span style="font-size: 8px">${event.fullname}</span>
                            <br/>
                            <span style="font-size: 8px">${event.title}</span>
                        </span>
                    </div>`;
                element.find('.fc-content').html(eventContent);
                element.on('click', function() {
                    var attendeesHtml = `
                    <div class="col-md-12">
                        <strong>Attendees</strong>
                        <div class="meeting-attendees-container">
                            ${event.attendees && event.attendees.length > 0 ? 
                                event.attendees.map(attendee => `
                                    <div style="display: flex; align-items: center; margin-bottom: 5px;margin-right:10px">
                                        ${attendee.avatar_type === 'initials' ? 
                                            `<div class="calendar-user-initials">${attendee.avatar || 'NA'}</div>` :
                                            `<img src="${attendee.avatar || 'default-avatar.png'}" alt="${attendee.name}" class="calendar-user-avatar" />`
                                        }
                                        <div class="organizer-data" style="margin-left: 10px;">
                                            <strong data-toggle="tooltip" title="${attendee.name}">
                                                ${attendee.name.length > 20 ? attendee.name.slice(0, 20) + '...' : attendee.name}
                                            </strong><br/>
                                            <span>${attendee.roles || 'No role specified'}</span>
                                        </div>
                                    </div>
                                `).join('') 
                            : '<div>No attendees</div>'}</div></div>`;

                    var modalContent = `
                        <h4><strong>${event.title}</strong></h4>
                        <span class="time-range"><span class="time-icon"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 0C8.68678 0 7.38642 0.258658 6.17317 0.761205C4.95991 1.26375 3.85752 2.00035 2.92893 2.92893C1.05357 4.8043 0 7.34784 0 10C0 12.6522 1.05357 15.1957 2.92893 17.0711C3.85752 17.9997 4.95991 18.7362 6.17317 19.2388C7.38642 19.7413 8.68678 20 10 20C12.6522 20 15.1957 18.9464 17.0711 17.0711C18.9464 15.1957 20 12.6522 20 10C20 8.68678 19.7413 7.38642 19.2388 6.17317C18.7362 4.95991 17.9997 3.85752 17.0711 2.92893C16.1425 2.00035 15.0401 1.26375 13.8268 0.761205C12.6136 0.258658 11.3132 0 10 0ZM14.2 14.2L9 11V5H10.5V10.2L15 12.9L14.2 14.2Z" fill="#188544"></path></svg></span> ${moment(event.start).format('DD MMM YYYY hh:mm A')} - ${moment(event.end).format('DD MMM YYYY hh:mm A')}</span></div>
                        <br/>
                        <strong>Organizer </strong><br>
                        <div style="display: flex; align-items: center;">
                            ${event.avatar_type === 'initials' ? 
                                `<div class="calendar-user-initials">${event.avatar}</div>` :
                                `<img src="${event.avatar}" class="calendar-user-avatar" />`}
                            <div class="organizer-data"><strong>${event.fullname}</strong><br><span>${event.technician_role}</span></div>
                        </div>
                        <hr/>
                        ${attendeesHtml}
                        <div><strong><i class="fa fa-list"></i></strong> ${event.description || 'No description available'}</div>
                    `;

                $('#eventDetails').html(modalContent);
                $('#eventModal').modal('show');
            });
            }
        });
    });
   });
</script>


<script type="text/javascript">
    /* ── Password toggle ── */
    function togglePwd() {
        const inp = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (inp.type === 'password') {
            inp.type = 'text';
            icon.innerHTML = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>`;
        } else {
            inp.type = 'password';
            icon.innerHTML = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        let storedCompany = localStorage.getItem('Default_Company');
        let wrapper = document.querySelector('.client-brand');
        if (storedCompany) {
                try {
                    let companyObj = JSON.parse(storedCompany);
                    let companyId = companyObj.id;
                    if (!companyId || companyId == 0) {
                        return;
                    }
                    fetch("{{ url('company/logo') }}/" + companyId)
                    .then(res => res.json())
                    .then(data => {

                        const logoEl = document.getElementById('loginCompanyLogo');

                        if (data.status === 'success' && data.logo) {

                            logoEl.src = data.logo;

                            logoEl.onerror = function () {
                                this.closest('.client-brand').style.display = 'none';
                            };

                            wrapper.style.display = 'block';
                        }
                    })
                    .catch(console.error);

            } catch (e) {
                console.error('Invalid Default_Company in localStorage');
            }
        } else {
        }
    });

    $(document).ready(function() {

        var config = new Object;
        config.url = new Object;
        config.token = "{{ csrf_token() }}";
        config.url.validateUsername = "{{ url('admin/validate-username') }}";
        config.impersonate = @json(config('app.impersonate'));
        config.client = @json(config('app.client'));
        new LoginAdd(config);
    });
</script>

