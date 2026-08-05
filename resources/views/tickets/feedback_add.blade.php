@extends('layouts.layout0')
@section('title', 'Confirmation Page')

@section('content')
    @if(session()->has('status_success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session()->get('status_success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif(isset($info))
        <div class="text-center py-5">
            <h1 class="display-4 text-muted">{{ $info }}</h1>
        </div>
    @else
        <form id="frm-feedback" name="frm_feedback" method="post" action="{{ route('ticket.addFeedback') }}"
            class="form-horizontal">
            @csrf
            <input type="hidden" name="id" id="id" class="hidden" value="{{ base64_encode("###" . $ticket->id) }}" />

            <div id="question">
                {{-- Heading --}}
                <div class="text-center mb-4">
                    <p style="margin:0 auto; max-width: 290px; font-size: 20px; font-weight: 500; color: #2d3748;">
                        {{ trans("content.service_ticket_fields.improve") }}
                    </p>
                </div>

                {{-- Opinion Text --}}
                <p class="text-center mb-3" style="font-weight: 600; color: #4a5568;">
                    {{ trans("content.service_ticket_fields.opinion") }}
                </p>

                {{-- Feedback Emojis --}}
                <div class="text-center mb-4">
                    <div class="row justify-content-center g-2">
                        <div class="col-2 col-sm-2">
                            <div class="option1">
                                <label for="feedback1" class="d-block cursor-pointer text-center">
                                    <img src="{{ asset("images/emo/1.gif") }}" class="img-circle"
                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%; transition: all 0.3s ease;" />
                                    <br />
                                    <input type="radio" name="feedback" id="feedback1" value="1" required class="mt-2"
                                        style="accent-color: #dc3545;" />
                                </label>
                            </div>
                        </div>
                        <div class="col-2 col-sm-2">
                            <div class="option1">
                                <label for="feedback2" class="d-block cursor-pointer text-center">
                                    <img src="{{ asset("images/emo/2.gif") }}" class="img-circle"
                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%; transition: all 0.3s ease;" />
                                    <br />
                                    <input type="radio" name="feedback" id="feedback2" value="2" class="mt-2"
                                        style="accent-color: #dc3545;" />
                                </label>
                            </div>
                        </div>
                        <div class="col-2 col-sm-2">
                            <div class="option1">
                                <label for="feedback3" class="d-block cursor-pointer text-center">
                                    <img src="{{ asset("images/emo/3.gif") }}" class="img-circle"
                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%; transition: all 0.3s ease;" />
                                    <br />
                                    <input type="radio" name="feedback" id="feedback3" value="3" class="mt-2"
                                        style="accent-color: #dc3545;" />
                                </label>
                            </div>
                        </div>
                        <div class="col-2 col-sm-2">
                            <div class="option1">
                                <label for="feedback4" class="d-block cursor-pointer text-center">
                                    <img src="{{ asset("images/emo/4.gif") }}" class="img-circle"
                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%; transition: all 0.3s ease;" />
                                    <br />
                                    <input type="radio" name="feedback" id="feedback4" value="4" class="mt-2"
                                        style="accent-color: #dc3545;" />
                                </label>
                            </div>
                        </div>
                        <div class="col-2 col-sm-2">
                            <div class="option1">
                                <label for="feedback5" class="d-block cursor-pointer text-center">
                                    <img src="{{ asset("images/emo/5.gif") }}" class="img-circle"
                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%; transition: all 0.3s ease;" />
                                    <br />
                                    <input type="radio" name="feedback" id="feedback5" value="5" checked class="mt-2"
                                        style="accent-color: #dc3545;" />
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Remarks --}}
                <div class="form-group mb-3">
                    <label class="form-label" style="font-weight: 500; color: #4a5568;">
                        {{ trans("content.service_ticket_fields.leave") }}
                        <span id="remarks_required" class="text-danger d-none">*</span>
                    </label>
                    <textarea name="remarks" id="remarks" class="form-control" maxlength="1000" placeholder="Enter your feedback max 2000 character" rows="6" required style="border-radius: 8px; border: 1px solid #e2e8f0; resize: vertical; padding: 12px; min-height: 200px; height: 200px; width: 100%; font-size: 14px; line-height: 1.6;"></textarea>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" id="btnClear" class="amg-btn amg-btn-secondary">
                    {{ trans("button.close") }}
                </button>
                <button type="submit" id="btnSubmit" class="amg-btn amg-btn-primary">
                    {{ trans("button.send") }}
                </button>
            </div>
        </form>
    @endif

    {{-- Custom Styles --}}
    <link href="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.css') !!}" rel="stylesheet" />
    <style>
        /* Feedback Form Styles */
        .option1 label {
            cursor: pointer;
            display: block;
            padding: 5px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .option1 label:hover {
            background: #f0f0f0;
            transform: scale(1.05);
        }

        .option1 input[type="radio"] {
            cursor: pointer;
        }

        .option1 input[type="radio"]:checked+.img-circle,
        .option1 label:has(input[type="radio"]:checked) .img-circle {
            border-color: #dc3545;
            box-shadow: 0 0 10px rgba(220, 53, 69, 0.3);
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .alert {
            border-radius: 8px;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        /* Dark mode styles for layout0 content */
        html[data-bs-theme="dark"] .layout0-card {
            background: #2d2d44;
            color: #e2e8f0;
        }

        html[data-bs-theme="dark"] .layout0-card .form-control {
            background: #3d3d5c;
            border-color: #4a4a6a;
            color: #e2e8f0;
        }

        html[data-bs-theme="dark"] .layout0-card .form-control::placeholder {
            color: #a0a0c0;
        }

        html[data-bs-theme="dark"] .layout0-card .btn-secondary {
            background: #4a4a6a;
            border-color: #5a5a7a;
        }

        html[data-bs-theme="dark"] .option1 label:hover {
            background: #3d3d5c;
        }

        html[data-bs-theme="dark"] .layout0-card p {
            color: #e2e8f0 !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .img-circle {
                width: 40px !important;
                height: 40px !important;
            }
        }

        @media (max-width: 576px) {
            .img-circle {
                width: 32px !important;
                height: 32px !important;
            }

            .col-2 {
                padding: 0 4px;
            }

            .layout0-card {
                padding: 15px;
            }
        }
    </style>
@endsection

@push('scripts')
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.js') !!}"></script>
    <script>
        $(document).ready(function () {
            // Clear button functionality
            var feedbackMaxRating = @json($feedbackMaxRating ?? 0);

            var summernoteConfig = {
                inheritPlaceholder: true,
                placeholder: 'Enter your feedback',
                toolbar: [
                    ['color', ['color']],
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol']]
                ],
                minHeight: 200,
                focus: true
            };
            $('textarea[name="remarks"]').summernote(summernoteConfig);

            $('#btnClear').on('click', function () {
                $('#frm-feedback')[0].reset();
                $('#remarks').summernote('code', '');
                $('#feedback5').prop('checked', true).trigger('change');
                var validator = $('#frm-feedback').validate();
                validator.resetForm();
                $('#frm-feedback').find('.is-invalid, .error').removeClass('is-invalid error');
                $('#frm-feedback').find('label.error').remove();
                $('#remarks').next('.note-editor').removeClass('is-invalid');
                $('#remarks_required').toggleClass('d-none',5 > (feedbackMaxRating || 0));
            });

           $.validator.addMethod("summernoteRequired", function (value, element) {
                var selectedRating = parseInt($('input[name="feedback"]:checked').val() || 0);
                var maxFeedbackRating = parseInt(feedbackMaxRating || 0);
                if (selectedRating > maxFeedbackRating) {
                    return true;
                }
                return !$('#remarks').summernote('isEmpty');
            }, "Remarks is required.");

            $.validator.addMethod("summernoteMaxLength", function (value, element, max) {
                var html = $(element).summernote('code');
                return html.length <= max;
            }, $.validator.format("Maximum {0} characters allowed."));

            // Validation
            $('#frm-feedback').validate({
                ignore: [],
                rules: {
                    feedback: {
                        required: true
                    },
                    remarks: {
                        summernoteRequired: true,
                        summernoteMaxLength: 2000
                    }
                },
                messages: {
                    feedback: {
                        required: '{{ trans("content.service_ticket_fields.select_feedback") }}'
                    },
                    remarks: {
                        summernoteRequired: '{{ trans("content.service_ticket_fields.enter_remarks") }}',
                        summernoteMaxLength: 'Maximum 2000 characters allowed.'
                    }
                },
                errorPlacement: function (error, element) {
                    if (element.attr('id') === 'remarks') {
                        error.insertAfter(element.next('.note-editor'));
                    } else {
                        error.insertAfter(element);
                    }
                }
            });

            $('#frm-feedback').on('submit', function (e) {
                if (!$('#frm-feedback').valid()) {
                    return false;
                }
                return true;
            });

            // Radio button click effect
            $('#remarks').on('summernote.change', function () {
                $(this).valid();
            });

        // Update required mark on rating change
            $('input[name="feedback"]').on('change', function () {
                $('.option1 .img-circle').css('border-color', 'transparent');
                $(this).closest('label').find('.img-circle').css('border-color', '#dc3545');
                var selectedRating = parseInt($(this).val());
                var maxFeedbackRating = feedbackMaxRating || 0;
                $('#remarks_required').toggleClass(
                    'd-none',
                    selectedRating > maxFeedbackRating
                );
                $('#remarks').valid();
            });
        });
    </script>
@endpush