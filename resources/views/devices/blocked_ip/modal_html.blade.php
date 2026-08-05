{{--
/**
------------------------------------------------------------
File: modal_html.blade.php
Module: Device/blockedIP
DEV/26/04
------------------------------------------------------------
Version: 1.0.0
Author: Safdar Ali
Page ID: #DEV-002
Created On: 2026-04-23
Reviewed By: -
------------------------------------------------------------
Change Log:
[1.0.0] - Initial version
[1.0.1] - UI fixes and buttons
------------------------------------------------------------
*/
--}}
<div id="block-mdl" class="amg-modal modal fade user-mdl-box" data-bs-backdrop="static">
    <div class="modal-dialog  modal-dialog-centered">
        <form id="block-mdl-frm" method="post">
            @csrf
            <div class="modal-content rounded-5">
                <!-- HEADER -->
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4">
                        {{ trans("block_ip.block_ip_fields.block_ip") }}
                    </h3>
                    <button type="button" class="modal-close px-4" data-bs-dismiss="modal">✕</button>
                </div>
                <!-- BODY -->
                <div class="modal-body">
                    <div class="container-fluid py-3">
                        <div class="row g-4 px-4">
                            <!-- TYPE (IP / MAC) -->
                            <div class="col-md-12">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label b1-text required">
                                        {{ trans("block_ip.block_ip_fields.block") }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="d-flex gap-4">
                                        <div>
                                            <input type="radio" id="ip_check" name="type" value="1">
                                            <label for="ip_check">IP</label>
                                        </div>
                                        <div>
                                            <input type="radio" id="mac_check" name="type" value="2">
                                            <label for="mac_check">MAC</label>
                                        </div>
                                    </div>
                                    <div class="amg-form-error-wrap  d-block mt-1"></div>

                                </div>
                            </div>

                            <!-- IP FIELD -->
                            <div id="ip_div" class="col-md-12">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label b1-text required">
                                        {{ trans("block_ip.block_ip_fields.ip") }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            🌐
                                        </span>
                                        <input type="text" name="ip" id="ip" class="form-control"
                                            placeholder="{{ trans('block_ip.block_ip_fields.ip') }}">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                            <!-- MAC FIELD -->
                            <div id="mac_div" class="col-md-12 ">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label b1-text required">
                                        {{ trans("block_ip.block_ip_fields.mac") }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            🖧
                                        </span>
                                        <input type="text" name="mac" id="mac" class="form-control"
                                            placeholder="{{ trans('block_ip.block_ip_fields.mac') }}">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="modal-footer px-4 pb-4">
                        <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary">
                            {{ trans("block_ip.block_ip_fields.save") }}
                        </button>
                        <button type="button" class="amg-btn amg-btn-outline-secondary" data-bs-dismiss="modal">
                            {{ trans("block_ip.block_ip_fields.close") }}
                        </button>
                    </div>
                </div>
        </form>
    </div>
</div>

<style>
    #block-mdl .amg-form-error-wrap {
        margin-left: 0 !important;
    }
</style>