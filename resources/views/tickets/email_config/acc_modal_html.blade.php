<div id="accemailConfigMdl" class="modal fade user-mdl-box">
    <div class="modal-dialog">
        <form id="accemailConfigMdlForm" name="accemailConfigMdlForm" method="post" action="#" class="form-horizontal" onsubmit="return false;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 id="title" class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="email" class="control-label col-md-4 mandatory">Email</label>
                            <div class="col-md-8">
                                <input type="text" name="email" id="email" autocomplete="off" class="form-control" placeholder="Enter Email" value=""/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12 mandatory">
                            <label for="start_date" class="control-label col-md-4">Start Date</label>
                            <div class="col-md-8">
                                <input type="text" name="start_date" id="start_date" autocomplete="off" class="form-control" placeholder="Enter Start Date" value=""/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12"> 
                            <label for="end_date" class="control-label col-md-4 mandatory">End Date</label>
                            <div class="col-md-8">
                                <input type="text"  name="end_date" id="end_date" autocomplete="off" class="form-control" placeholder="Enter End Date"  value=""/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="tkt_ac_account_ids" class="control-label col-md-4 mandatory">Accounts</label>
                            <div class="col-md-8">
                                <select id="tkt_ac_account_ids"  name="tkt_ac_account_ids[]" multiple>
                                   
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="title" class="control-label col-md-4 mandatory">Status</label>
                            <div class="col-md-8">
                                <select name="status" id="status" class="form-control select2">
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <i class="fa fa-spinner fa-spin" id="img" style="display:none" /></i>
                    <button type="button" id="btnSubmit" class="btn btn-theme-red">{{ trans("button.save") }}</button>
                    <button type="button" id="btnClear" class="btn btn-theme-black" data-dismiss="modal">{{ trans("button.close") }}</button>
                </div>
            </div>
        </form>
    </div>
</div>