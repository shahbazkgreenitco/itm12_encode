$(document).ready(function(){
    var statusAdd = function (config) {
  
    var t = this;
    t.config = config;

    t.content = $('section.content');
    
    t.mdl = t.content.find('#label');
    
    t.frm = t.mdl.find('#pan');
 
    t.frmEl = {};
    t.frmEl.item_value = t.frm.find('#item_value');

    t.btn = {};
    t.btn.submit = t.frm.find('.btnSubmit');
    
    t.httpCall = false;
   // t.httpPostPath = t.config.url.add
    t.resetFrm = {};

    t.handlesubmit = function (e) {
      
        if (t.frmValidator.form() == false) {
            e.preventDefault();
            return false;
        }
        return true;
       
    };
   
    t.frmValidator = t.frm.validate({
        rules: {
            item_value: {
                required: true,
                str_name: true
            }
        }
    });

    t.btn.submit.on('click',  $.proxy(t.handlesubmit));
   
};
new statusAdd(config); 
});

