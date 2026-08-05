<div id="accemailConfigviewMdl" class="modal fade user-mdl-box">
    <div class="modal-dialog">
        <form id="accemailConfigMdlviewForm" name="accemailConfigMdlviewForm" method="post" action="#" class="form-horizontal" onsubmit="return false;">
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
                            <label for="email" class="control-label col-md-4">Email Host</label>
                            <div class="col-md-8">
                                <input type="text" name="email" id="email" autocomplete="off" class="form-control" placeholder="Enter Email" value="" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="start_date" class="control-label col-md-4">From Date</label>
                            <div class="col-md-8">
                                <input type="text" name="start_date" id="start_date" autocomplete="off" class="form-control" placeholder="Enter From Date" value="" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12"> 
                            <label for="end_date" class="control-label col-md-4">To Date</label>
                            <div class="col-md-8">
                                <input type="text"  name="end_date" id="end_date" autocomplete="off" class="form-control" placeholder="Enter End Date"  value="" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="tkt_ac_account_ids" class="control-label col-md-4">Accounts</label>
                            <div class="col-md-8">
                                <select id="tkt_ac_account_ids"  name="tkt_ac_account_ids[]" multiple disabled>
                                   
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="title" class="control-label col-md-4 mandatory">Status</label>
                            <div class="col-md-8">
                                <select name="status" id="status" class="form-control select2" disabled>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="display:none"></div>
            </div>
        </form>
    </div>
</div>