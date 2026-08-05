<div id="emailConfigviewMdl" class="modal fade user-mdl-box">
    <div class="modal-dialog">
        <form id="emailConfigMdlviewForm" name="emailConfigMdlviewForm" method="post" action="#" class="form-horizontal" onsubmit="return false;">
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
                            <label for="title" class="control-label col-md-4">Email Service Status</label>
                            <div class="col-md-8">
                                <select name="auto_create_from_email" id="auto_create_from_email"  class="form-control select2" disabled>
                                    <option value="1">Enabled</option>
                                    <option value="0">Disabled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row emailcover">
                        <div class="form-group col-md-12">
                            <label for="email_hos" class="control-label col-md-4">Email Host</label>
                            <div class="col-md-8">
                                <input type="text" name="ebts_host" id="ebts_host" autocomplete="off" class="form-control" placeholder="Enter Email Host" value="" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row emailcover">
                        <div class="form-group col-md-12">
                            <label for="email_por" class="control-label col-md-4">Email Port</label>
                            <div class="col-md-8">
                                <input type="text" name="ebts_port" id="ebts_port" autocomplete="off" class="form-control" placeholder="Enter Email Port" value="" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row emailcover">
                        <div class="form-group col-md-12"> 
                            <label for="email" class="control-label col-md-4">Email Account</label>
                            <div class="col-md-8">
                                <input type="text"  name="ebts_username" id="ebts_username" autocomplete="off" class="form-control" placeholder="Enter Email Account"  value="" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row emailcover">
                        <div class="form-group col-md-12">
                            <label for="encryption" class="control-label col-md-4">Encryption</label>
                            <div class="col-md-8">
                                <select name="ebts_encryption" id="ebts_encryption"  class="form-control select2" disabled>
                                    <option value="ssl">SSL</option>
                                    <option value="tls">TLS</option>
                                    <option value="false">False</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row emailcover">
                        <div class="form-group col-md-12">
                            <label for="validate_cert" class="control-label col-md-4">Validate Certificate</label>
                            <div class="col-md-8">
                                <select name="validate_cert" id="validate_cert"  class="form-control select2" disabled>
                                    <option value="2">False</option>
                                    <option value="1">True</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row emailcover">
                        <div class="form-group col-md-12">
                            <label for="title" class="control-label col-md-4">Restriction For Domains</label>
                            <div class="col-md-8">
                                <select name="ticketing_restricted" id="ticketing_restricted" class="form-control select2" disabled>
                                    <option value="1">Allow Specific Domains</option>
                                    <option value="2">Allow All Domains</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row emailcover domainallowcover">
                        <div class="form-group col-md-12">
                            <label for="title" class="control-label col-md-4">Allow specific domains</label>
                            <div class="col-md-8">
                                <textarea name="ticketing_allowed_domains" id="ticketing_allowed_domains" autocomplete="off"  class="textarea form-control" placeholder="Mention the specific domain name(s) as comma separated" disabled></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row emailcover domainallcover">
                        <div class="form-group col-md-12">
                            <label for="title" class="control-label col-md-4">Restricted domains</label>
                            <div class="col-md-8">
                                <textarea name="ticketing_blocked_domains" id="ticketing_blocked_domains" autocomplete="off"  class="textarea form-control" placeholder="Mention the restricted domain name(s) as comma separated" disabled></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row emailcover ">
                        <div class="form-group col-md-12">
                            <label for="title" class="control-label col-md-4">Blocked Email Accounts</label>
                            <div class="col-md-8">
                                <textarea name="blocked_email_accounts" id="blocked_email_accounts" autocomplete="off"  class="textarea form-control" placeholder="Enter the Blocked Email Accounts" disabled></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row emailcover ">
                        <div class="form-group col-md-12">
                            <label for="title" class="control-label col-md-4">Restricted Words</label>
                            <div class="col-md-8">
                                <textarea name="restricted_words" id="restricted_words" autocomplete="off"  class="textarea form-control" placeholder="Enter Restricted Words as comma separated" disabled></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="display:none"></div>
            </div>
        </form>
    </div>
</div>