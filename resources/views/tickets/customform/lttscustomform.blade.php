{{-- @page-meta
{
    "page_no": "LCF05-26",
    "file": "lttscustomform.blade.php",
    "versions": [
        {
        "version": "1.0",
        "writer": "Priya Maru",
        "from": "2026-05",
        "reviewer": null,
        "description": "ltts Custom Form Model"
        }
    ]
}
--}}
<div class="checkbox-group customFormModal">
    <div class="active_ticket hide">
        <div class="row">
            <label class="col-md-4">
                <input type="checkbox" name="exception_access[]" id="github" value="github"><span class="checkmark"></span> GitHub Access
            </label>
            <div class="" id="active_ticket_options">
                <label class="col-md-4">
                    <input type="checkbox" name="exception_access[]" id="code-push-pull" value="code[Push+Pull]"><span class="checkmark"></span> Code [Push+Pull]
                </label>
                <label class="col-md-4">
                    <input type="checkbox" name="exception_access[]" id="code-paste-upload" value="code[Paste+Upload]">
                    <span class="checkmark"></span> Code [Paste+Upload]
                </label>
            </div>
        </div>  
        <div class="row m2">
            <div class="active_ticket_id text-left"></div>
        </div>
        <input type="hidden" name="cr_custom_form" id="cr_custom_form">
    </div>
    <div class="no_active_ticket hide">
        <div class="row">
            <label class="col-md-6">
                <input type="radio" name="exception_access" id="github" value="github"> <span class="radiomark"></span> GitHub Access
            </label>
        </div>
        <div class="" id="not_active_ticket_options">
            <div class="row">
                <label class="col-md-6">
                    <input type="radio" name="exception_access" id="github_gitbash" value="github,code[Push+Pull]"> <span class="radiomark"></span> GitHub Access + Code [Push+Pull]
                </label>
            </div>
            <div class="row">
                <label class="col-md-12">
                    <input type="radio" name="exception_access" id="github_dlp" value="github,code[Paste+Upload]"> <span class="radiomark"></span> GitHub Access + Code [Paste+Upload]
                </label>
            </div>
            <div class="row">
                <label class="col-md-12">
                    <input type="radio" name="exception_access" id="github_git_bash_dlp" value="github,code[Push+Pull],code[Paste+Upload]"> <span class="radiomark"></span> GitHub Access + Code [Push+Pull] + Code [Paste+Upload]
                </label>
            </div>
        </div>
      <input type="hidden" name="cr_custom_form" id="cr_custom_form">
      <div class="row m2">
        <div class="no_active_ticket_id col-md-12 text-left"> No Active GitHub ticket for this user</div>
      </div>
      <div class="error danger" id="exception_error"></div>
  </div>
  <input type="hidden" name="github_access_url" id="github_access_url">
</div>
<div class="hide m2" id="external_public_category"><b>Note:- For External Public GitHub Upload/Copy Paste access cannot be granted.</b></div>
<div class="modal-footer" id="custom_form_footer">
    <div class="row">
        <div class="col-md-12 saveDataWrap">
            <button type="button" id="nextData" class="btn btn-theme-red">{{ trans("button.next") }}</button>
            <button type="button" id="btnClear" class="btn btn-theme-black" data-dismiss="modal">{{ trans("button.cancel") }}</button>
        </div>
    </div>
</div>
<input type="hidden" name="form_data" id="form_data">
<div id="dynamic_form_for_cr" class="hide">
    <textarea name="field_values" id="field_value" class="form-control"></textarea>
</div>

<style>
    input[type="radio"] {
        transform: scale(1.5); /* Increase size */
        margin-right: 4px;
    }
    .button{
        margin-right:0px;
    }
    .have_active_access{
      color:blue;
    }
/* 
    .checkbox-group {
      display: flex;
      flex-direction: column;
      gap: 15px;
      max-width: 300px;
      margin: auto;
      background:#fff;
    }

    label {
      position: relative;
      padding-left: 35px;
      cursor: pointer;
      user-select: none;
    }

    input[type="checkbox"] {
      position: absolute;
      opacity: 0;
      cursor: pointer;
    } */

    /* Custom checkbox style */
    /* .checkmark {
      position: absolute;
      left: 0;
      top: 2px;
      height: 20px;
      width: 20px;
      background-color: #ccc;
      border-radius: 5px;
      transition: background 0.3s ease;
    }

    label:hover input ~ .checkmark {
      background-color: #bbb;
    }

    label input:checked ~ .checkmark {
      background-color: #D80505;
    }

    .checkmark:after {
      content: "";
      position: absolute;
      display: none;
    }

    label input:checked ~ .checkmark:after {
      display: block;
    }

    label .checkmark:after {
      left: 7px;
      top: 3px;
      width: 5px;
      height: 10px;
      border: solid white;
      border-width: 0 3px 3px 0;
      transform: rotate(45deg);
    } */
    .form-control {
      display: block;
      width: 100%;
      margin-top: 5px;
      padding: 8px;
    }
    .form-group {
      margin-bottom: 16px;
    }
    .required-star {
      color: red;
      margin-left: 4px;
    }
    #mdl_popup_loader {
      display: none;
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: 999;
      background: rgba(0,0,0,0.3) url('{{ asset("imgs/loader.gif") }}') center no-repeat;
      transition: all 0.3s ease-in-out;
    }
    #mdl_popup_loader.active {
        display: block;
    }
    .error {
      color:red;
    }
    #external_public_category {
      margin-left: -8px;
    }
   .no_active_ticket_id {
     margin-left: -15px;
   }
  </style>
