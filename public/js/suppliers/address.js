$(document).ready(function(){
    var ModelAdd = function (config) {
    var t = this;
    t.config = config;

    t.content = $('section.content');
    
    t.mdl = t.content.find('#modelmdl');
    
    t.frm = t.mdl.find('#cab');
 
    t.frmEl = {};
    t.frmEl.address = t.frm.find('#address');
    t.frmEl.address2 = t.frm.find('#address2');
    t.frmEl.city = t.frm.find('#city_id');
    t.frmEl.state = t.frm.find('#state_id');
    t.frmEl.country = t.frm.find('#country_id');
    t.frmEl.phone = t.frm.find('#phone');
    t.frmEl.fax = t.frm.find('#fax');
    t.frmEl.zip = t.frm.find('#zip');

    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit');

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
        onsubmit: false,
        rules: {
            address: {
                required: true,
                str_name: true
            },
            address2: {
                str_name: true
            },
            city_id: {
                required: true,
                str_name: true
            },
            state_id: {
                required: true,
                str_name: true
            },
            country_id: {
                required: true,
                str_name: true
            },
            phone: {
                number: true
            },
            fax: {
                str_name: true
            },
            zip: {
                number: true
            }
        }
    });

    t.btn.submit.on('click',  $.proxy(t.handlesubmit));
   
};
new ModelAdd(config); 
});

