{{-- @page-meta
{
    "page_no": "CF05-26",
    "file": "customform1.blade.php",
    "versions": [
        {
        "version": "1.0",
        "writer": "Priya Maru",
        "from": "2026-05",
        "reviewer": null,
        "description": "custom Form Modal"
        }
    ]
}
--}}
<div class="panel-body pad-no" id="custom_request">
    <div class="container-fluid">
        <!-- Progress -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="progress-bar1">
                    <div class="step-marker active" data-step="1">
                        Employee Type
                    </div>
                    <div class="step-marker" data-step="2">
                        Employee Info
                    </div>
                    <div class="step-marker" data-step="3">
                        Add Items
                    </div>

                </div>

            </div>
        </div>

        <!-- STEP 1 -->
        <fieldset class="step active">
            <div class="amg-card">
                <div class="amg-card-body p-4">
                    <div class="row justify-content-center">
                        <div class="col-lg-10 text-center">
                            <input type="hidden" name="custom_form_id" id="custom_form_id" value="">
                            <div class="d-flex flex-wrap justify-content-center gap-4">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input me-2" name="employee_type"
                                        value="Self" checked>
                                    Self
                                </label>

                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input me-2" name="employee_type"
                                        value="LTTS Employee">
                                    LTTS Employee
                                </label>

                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input me-2" name="employee_type"
                                        value="Non LTTS Employee">
                                    Non LTTS Employee
                                </label>
                            </div>
                            <div class="mt-4 text-end">
                                <button class="amg-btn amg-btn-primary next" type="button">
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>

        <!-- STEP 2 -->
        <fieldset class="step">
            <div class="amg-card psno-container">
                <div class="amg-card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label mandatory">
                                EMP/PS No
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-buildings"></i>
                                </span>
                                <select id="emp_no" name="user_id" class="form-control" required>
                                </select>
                                <input type="hidden" id="emp_number" name="emp_no">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Department
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-diagram-3"></i>
                                </span>
                                <select id="department" class="form-control plgn-select2">
                                </select>
                                <input type="hidden" id="department_name" name="department">
                                <input type="hidden" id="department_id" name="department_id">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label mandatory">
                                Name
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" id="emp_name" name="emp_name" class="form-control"
                                    placeholder="Enter Name">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Email ID
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" id="email_id" name="email_id" class="form-control"
                                    placeholder="Enter Email ID">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Contact
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-telephone"></i>
                                </span>
                                <input type="text" id="contact" name="contact" class="form-control"
                                    placeholder="Enter Contact">
                            </div>

                        </div>

                        <div class="col-md-6 psno-container">
                            <label class="form-label">
                                Location
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-geo-alt"></i>
                                </span>
                                <select id="location" class="form-control plgn-select2">
                                </select>
                                <input type="hidden" name="location">
                                <input type="hidden" name="location_id">
                            </div>
                        </div>
                    </div>

                    <!-- Manager Info -->
                    <div class="row g-4 mt-1 hide" id="manager_data">
                        <div class="col-md-6">
                            <label class="form-label">
                                IS Name
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-person-badge"></i>
                                </span>
                                <input type="text" id="manager_name" name="manager_name" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                IS Email
                            </label>

                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope-paper"></i>
                                </span>
                                <input type="text" id="manager_email" name="manager_email" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                IS PS No.
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-credit-card"></i>
                                </span>
                                <input type="text" id="manager_ps_no" name="manager_ps_no" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Cost Center -->
                    <div class="row g-4 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">
                                Cost Center
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-calculator"></i>
                                </span>
                                <input type="text" id="cost_center" name="cost_center" class="form-control"
                                    placeholder="Enter Cost Center">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label mandatory">
                                WBS
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-journal-text"></i>
                                </span>
                                <input type="text" id="wbs_center" name="wbs_center" class="form-control"
                                    placeholder="Enter WBS">
                            </div>
                            <span id="wbs_error" class="text-danger small mt-1 d-block">
                            </span>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                WBS Note
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-pencil-square"></i>
                                </span>
                                <input type="text" id="wbs_note" name="wbs_note" class="form-control"
                                    placeholder="Enter WBS Note">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button class="amg-btn amg-btn-outline prev" type="button">
                            Back
                        </button>
                        <button class="amg-btn amg-btn-primary next" type="button">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </fieldset>

        <!-- STEP 3 -->
        <fieldset class="step">
            <div class="amg-card">
                <div class="amg-card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label mandatory">
                                Delivery Location
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </span>
                                <select class="form-control b_location" id="b_location" name="b_location">
                                </select>
                                <input type="hidden" name="delivery_location">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Internal Location
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-pin-map"></i>
                                </span>
                                <select class="form-control" id="internal_location" name="internal_location_id">
                                </select>
                                <input type="hidden" name="internal_location">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Remark
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-chat-left-text"></i>
                                </span>
                                <input type="text" class="form-control" id="remark_item" name="remark_item"
                                    placeholder="Remark">
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive mt-4">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th width="28%">Category</th>
                                    <th width="30%">Item</th>
                                    <th width="12%">Quantity</th>
                                    <th width="25%">Remarks</th>
                                    <th width="1%">Add</th>
                                </tr>
                            </thead>

                            <tbody id="repeater-container">
                                <tr class="entry">
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-grid"></i>
                                            </span>
                                            <select class="form-control category_id" id="category_id_0"
                                                name="category_id[]">
                                            </select>
                                            <input type="hidden" class="selected-category-name" id="category_0"
                                                name="category[]">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-box-seam"></i>
                                            </span>
                                            <select class="form-control item" name="item_id[]" id="item_0">
                                            </select>
                                            <input type="hidden" class="selected-item-img" id="item_img_0"
                                                name="item_img[]">
                                            <input type="hidden" class="selected-item-name" id="item_name_0"
                                                name="item[]">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-123"></i>
                                            </span>
                                            <input type="number" class="form-control quantity" name="quantity[]"
                                                min="1" id="quantity_0" placeholder="Qty">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-pencil"></i>
                                            </span>
                                            <input type="text" class="form-control remarks" name="remarks[]"
                                                id="remarks_0" placeholder="Remarks">

                                            <input type="hidden" class="selected-type-name" id="item_type_0"
                                                name="item_type[]">
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <button type="button" class="amg-btn amg-btn-primary addNewRow">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" id="btnClear" class="amg-btn amg-btn-outline">
                            Cancel
                        </button>
                        <button type="button" class="amg-btn amg-btn-outline prev">
                            Back
                        </button>
                        <button type="button" id="saveData" class="amg-btn amg-btn-primary">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
</div>
<style type="text/css">
    #custom_request span.small-text {
        font-size: 13px;
        font-weight: normal;
        font-style: italic;
    }

    #custom_request .go-list {
        margin: 10px;
    }

    #custom_request button {
        float: right;
        margin-right: 10px;
    }

    #custom_request span.with-black {
        color: #000;
    }

    @media (min-width: 992px) {
        #custom_request .col-md-2 {
            text-align: right;
            width: 11.666667%;
        }
    }

    #custom_request .step {
        display: none;
    }

    #custom_request .step.active {
        display: block;
    }

    #custom_request .progress-bar1 {
        display: flex;
        justify-content: space-between;
        width: 100%;
        position: relative;
        margin-bottom: 20px;
    }

    #custom_request .step-marker {
        position: relative;
        z-index: 2;
        width: 33.33%;
        text-align: center;
        font-weight: bolder;
        color: white;
    }

    #custom_request .step-marker::before {
        content: attr(data-step);
        display: block;
        margin: 0 auto 10px auto;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #f1bf02;
        line-height: 24px;
        color: white;
    }

    #custom_request .step-marker.active::before {
        background: #66cd33;
    }

    #custom_request .step-marker:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 28%;
        right: -50%;
        width: 100%;
        height: 4px;
        background: #ddd;
        z-index: -1;
        transform: translateY(-50%);
    }

    #custom_request .input-group-addon {
        border: 1px solid #555555;
        border-radius: 5px 0px 0px 5px;
        padding: 6px;
        font-size: 14px;
        font-weight: 400;
        line-height: 1;
        color: #fff;
        text-align: center;
        background-color: #555555;
    }

    #requested_form .input-group-addon {
        min-width: 30px;
    }

    #custom_request .item_font_size {
        font-size: 13px;
    }

    #custom_request .radio-inline {
        display: inline-block;
        margin-right: 50px;
        color: white;
    }

    #custom_request .col-sm-1 {
        width: 9.333333%;
    }

    #custom_request .col-md-1 {
        width: 11.333333%;
    }

    #custom_request .btn-margin {
        margin-top: 25px;
    }

    #custom_request .custom-form-margin-lf {
        margin-left: 16px;
    }

    .one_line_dis {
        font-size: 13px;
        font-weight: bold;
        font-family: Arial, sans-serif;
        color: #ffe200;
        text-align: left;
        width: 100%;
    }

    #custom_request .control-label {
        color: white;
    }

    #custom_request .panel-footer {
        background: linear-gradient(to right, #0b174e, #109fca);
        color: #758697;
        border-color: rgba(0, 0, 0, 0.07);
        position: relative;
    }

    #custom_request .error-message {
        color: #eb2521;
        font-weight: bold;
    }

    #available-quantity {
        font-weight: bold;
    }

    @media (min-width: 992px) {
        #custom_request .col-md-2 {
            text-align: right;
            width: 11.666667%;
        }
    }

    label.mandatory::after {
        content: "*";
        position: unset;
        top: -2px;
        font-size: 17px;
        color: #e00;
        font-weight: bold;
    }
</style>
<script>
    window.resetStep();
</script>
