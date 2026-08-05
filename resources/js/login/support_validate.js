$(document).ready(function() {
    jQuery.validator.setDefaults({
        ignore: ':not(select, input:visible, textarea:visible)',
        errorPlacement: function(error, element) {
            if (element.hasClass('bs-select-hidden')) {
                error.insertAfter(element.next('.bootstrap-select'));
            } else if (element.hasClass('datepicker_input_grouped')) {
                error.insertAfter(element.parents('.input-group'));
            } else if (element.hasClass('error_shows') && element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.closest('.input-group').removeClass("col-md-9").wrap("<div class='col-md-9' id='no_padding'></div>"));
            } else if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('.select2-container'));
            } else if (element.hasClass('in-radio-box')) {
                error.insertAfter(element.parents('.in-radio-grp').children('.radio').last());
            } else if (element.hasClass('under-input-group')) {
                error.insertAfter(element.closest('.input-group'));
            } else if (element.hasClass('error_input')) {
                error.insertAfter(element.closest('.input-group').removeClass("col-md-9").wrap( "<div class='col-md-9' id='no_padding'></div>"));
            } else if (element.hasClass('radio-box')) {
                error.insertAfter(element.closest('.form-elements').removeClass("form-elements").wrap( "<div class='form-elements'></div>"));
            } else {
                error.insertAfter(element);
            }
        }
    });
    jQuery.validator.addMethod('charLimit', function(value, element, params) {
        var min = params[0];
        var max = params[1];
        return this.optional(element) || (value.length >= min && value.length <= max);
    }, 'Please enter between {0} and {1} characters.');
    jQuery.validator.addMethod('password', function(v, e) {
        return this.optional(e) || /^[A-Za-z0-9_\[\]@.,/*~!:;|$%^`~<>\\'"{}()#&?-]*$/.test(v);
    }, 'alphabets, numbers and limited characters only allowed');
    jQuery.validator.addMethod('alpha', function(v, e) {
        return this.optional(e) || /^([a-zA-Z]+)$/.test(v);
    }, 'alphabets only allowed');
    jQuery.validator.addMethod('nonZeroInteger', function(v, e) {
        return this.optional(e) || (/^([0-9]+)$/.test(v) && parseInt(v) > 0 && v == parseInt(v));
    }, 'Choose valid option.');
    jQuery.validator.addMethod('real_number', function(v, e, dl) {
        if (typeof dl != "undefined" && dl != true) {
            if (this.optional(e)) {
                return true;
            }
            if ((/^\d+\.?\d*$/.test(v) && parseFloat(v) >= 0)) {
                var s = v.toString();
                var sd = s.split('.');
                if (sd.length > 1) {
                    return (sd[sd.length - 1]).length <= dl;
                }
                return true;
            }
        }
    }, 'Enter valid number.');
    jQuery.validator.addMethod('alpha_str', function(v, e) {
        return this.optional(e) || /^([a-zA-Z ]+)$/.test(v);
    }, "alphabets only allowed");
    jQuery.validator.addMethod('alpha_numeric', function(v, e) {
        return this.optional(e) || /^([a-zA-Z0-9]+)$/.test(v);
    }, "alphabets and numbers only allowed");
    jQuery.validator.addMethod('str_name', function(v, e) {
        return this.optional(e) || /^([a-zA-Z0-9 _-]+)$/.test(v);
    }, 'Please enter a valid value');
    jQuery.validator.addMethod('str_name_format', function(v, e) {
        return this.optional(e) || /^([a-zA-Z0-9& _.,/-]+)$/.test(v);
    }, 'Please enter a valid value');
    jQuery.validator.addMethod('mac_format', function(v, e) {
        return this.optional(e) || /^([a-fA-F0-9]+)$/.test(v);
    }, 'Please enter a valid MAC Address');
    jQuery.validator.addMethod('str_address', function(v, e) {
        return this.optional(e) || /^[ A-Za-z0-9_.,/#-]*$/.test(v);
    }, "alphabets, numbers and limited characters only allowed");
    jQuery.validator.addMethod('length', function(v, e, p) {
        return this.optional(e) || typeof v != 'undefined' && typeof v != null && v.length == p;
    }, "valid number of digits only allowed");
    jQuery.validator.addMethod('month_year', function(v, e) {
        if (this.optional(e)) return true;
        if (/^\d{1,2}\/\d{4}$/.test(v) == false) return false;
        var s = v.split('/');
        if (s[0].length != 2 || !(s[0] >= 1 && s[0] <= 12)) return false;
        if (s[1].length != 4 || !(s[1] >= 1950 && s[1] <= 2100)) return false;
        return true;
    }, "valid mm/yyyy only allowed");
    jQuery.validator.addMethod('date_format', function(v, e) {
        return this.optional(e) || /^\d{4}-\d{1,2}-\d{2}$/.test(v);
    }, "valid YYYY-MM-DD only allowed");
    jQuery.validator.addMethod('alphanum_underscore', function(v, e) {
        return this.optional(e) || /^([a-zA-Z0-9_]+)$/.test(v);
    }, "alphabets,numbers and underscore only allowed");
    jQuery.validator.addMethod('remarks', function(v, e) {
        return this.optional(e) || /^[a-zA-Z0-9\s\r\n -/_%&{}:;",@'.)(:?^]{0,500}$/.test(v);
    }, "alphabets, numbers and limited 500 characters only allowed");
    jQuery.validator.addMethod('acceptable_spcl_chr', function(v, e) {
        return this.optional(e) || /^[a-zA-Z0-9\s\r\n -/_%&{}\[\]:;",@'.)(:?^]{0,2000}$/.test(v);
    }, "alphabets, numbers and limited characters only allowed");
    jQuery.validator.addMethod('clean_text_only', function(v, e) {
        return this.optional(e) || (/^[a-zA-Z0-9\s\r\n\-/_%&{}\[\]:;",@'’.)(:?^]{0,2000}$/.test(v) && !/<[^>]*>/g.test(v));
    }, "Alphabets, numbers and limited characters only. HTML tags are not allowed.");
    jQuery.validator.addMethod('clean_text_only_with_hash', function(v, e) {
        return this.optional(e) || (/^[a-zA-Z0-9\s\r\n\-/_%&{}\[\]:;",@'.)(:?^#]{0,2000}$/.test(v) && !/<[^>]*>/g.test(v));
    }, "Alphabets, numbers and limited characters only. HTML tags are not allowed.");
    jQuery.validator.addMethod('very_long_text', function(v, e) {
        return this.optional(e) || /^[a-zA-Z0-9\s\r\n -/_%&{}:;",@'.)(:?^]{0,10000}$/.test(v);
    }, "alphabets, numbers and limited characters only allowed");
    jQuery.validator.addMethod('long_text', function(v, e) {
        return this.optional(e) || /^[a-zA-Z0-9\s\r\n -/_%&{}:;",@'.)(:?^]{0,2000}$/.test(v);
    }, "alphabets, numbers and limited characters only allowed");
    jQuery.validator.addMethod('username', function(v, e) {
        // return this.optional(e) || /^[a-zA-Z0-9áéèêëíîïóôúûýÁÉÈÊËÍÎÏÓÔÚÛÝàòüçÀÒÜÇäöÄÖâùÿÂÙŸìÌãõÃÕåÅÞß-/_:,@'.)(:]{0,100}$/.test(v);
        return this.optional(e) || /^[a-zA-Z0-9\u00C0-\u00FF\u0100-\u017F\u0180-\u024F-/_:,@'.)(:]{0,100}$/.test(v);
    }, "alphabets, numbers and limited characters only allowed");
    jQuery.validator.addMethod("notEqual", function(value, element, param) {
        return this.optional(element) || value != param;
    }, "Please specify a different (non-default) value");
    jQuery.validator.addMethod('alpha_str_brackets', function(v, e) {
        return this.optional(e) || /^([a-zA-Z() ]+)$/.test(v);
    }, "Alphabets with Special character() only allowed");
    jQuery.validator.addMethod('prospect_no', function(v, e) {
        return this.optional(e) || /^([a-zA-Z0-9-_/]+)$/.test(v);
    }, "Alpha numeric and special characters[-,_,/] only allowed");
    jQuery.validator.addMethod('str_comp_name', function(v, e) {
        return this.optional(e) || /^([a-zA-Z0-9 _&-]+)$/.test(v);
    }, 'Please enter valid name');
    $(document).on('keyup blur', '.change-to-uppercase', function() {
        this.value = this.value.toUpperCase();
    });
    $.validator.addMethod("alphanumeric", function(value, element) {
        return this.optional(element) || /^(?=.*[a-zA-Z])[a-zA-Z0-9\s]*$/.test(value);
    }, "Tab Name must contain only letters and alphanumric.");
    jQuery.validator.addMethod("noSpace", function(value, element) {
        return value == '' || value.trim().length != 0;
    }, "No space allowed");
    $.validator.addMethod("fileNumberPattern", function(value, element) {
        return this.optional(element) || /^[0-9].*/.test(value);
    }, "File number must start with a number");
    $.validator.addMethod("noSpecialStart", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9]/.test(value);
    }, "The Title must not start with a special character.");
    $.validator.addMethod("commaSeparatedTags", function(value, element) {
        if (this.optional(element)) {
            return true;
        }
        var tags = value.split(',');
        var isValid = true;
        var alphaNumericRegex = /^[a-zA-Z0-9]+$/;
        $.each(tags, function(index, tag) {
            if (!alphaNumericRegex.test(tag.trim()) || /\s/.test(tag)) {
                // Check if tag contains any spaces
                isValid = false;
                return false;
            }
        });
        return isValid;
    }, "Tags must be comma-separated alphanumeric values without spaces.");
    jQuery.validator.addMethod("summernote", function() {
        return !($("#content").summernote('isEmpty'));
    }, 'This field is required');

    $.validator.addMethod("strongPassword", function(value, element) {
        return this.optional(element) 
            || /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#^])[A-Za-z\d@$!%*?&#^]{8,255}$/.test(value);
    }, "Password must be at least 8 characters long, include uppercase, lowercase, number, and special character.");

    $.validator.addMethod("notEqualToFields", function(value, element, params) {
        let username = $("[name='" + params.username + "']").val();
        let email = $("[name='" + params.email + "']").val();
        return this.optional(element) || 
            (value.toLowerCase() !== username.toLowerCase() && value.toLowerCase() !== email.toLowerCase());
    }, "Password should not match username or email.");

    jQuery.validator.addMethod("summernote1", function() {
        return !($("#remark").summernote('isEmpty'));
    }, 'This field is required');

    $.validator.addMethod("summernotes", function() {
        return !($("#description").summernote('isEmpty'));
    }, 'This field is required');

    $.validator.addMethod("maxSummernoteChars", function(value, element, params) {
        var content = $(element).val();
        content = $('<div>').html(content).text();
        return content.length <= params;
    }, "The description cannot exceed {0} characters.");

    $.validator.addMethod("decimal", function(value, element) {
        return this.optional(element) || /^\d+(\.\d{1,2})?$/.test(value);
    }, "Please enter a valid  number");
    $.validator.addMethod("notOnlyAt", function(value, element) {
        if ($.trim(value) === "") return true; 
        return !/^@+$/.test(value); 
    }, "The name cannot consist of only '@' symbols.");
    // Custom method for phone number validation with optional country code
    $.validator.addMethod("phoneWithCountryCode", function(value, element) {
        if (value.trim() === '') {
            return true;
        }

        if (value.charAt(0) === '+') {
            return $.isNumeric(value.substr(1));
        } else {
            return $.isNumeric(value);
        }
    }, "Please enter a valid mobile number.");

    jQuery.validator.addMethod("validEmail", function(value, element) {
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return this.optional(element) || emailRegex.test(value);
    }, "Please enter a valid email address.");
});

/* format emails */
var TktEmail = function() {
    var t = this;
    t.formatEmails = function(val) {
        var given_value = val;
        console.log(given_value);
        try {
            if(typeof val != "string") {
                return val;
            }

            val = val.replace(/(\r\n|\n|\r)/gm, ",");

            if( val.indexOf('(') > -1 && val.indexOf(')') > -1) {
                val = val.replace(/\(/g, '<');
                val = val.replace(/\)/g, '>');
            }
            
            var result = [];
            if( val.indexOf(";") > -1 ) {
                val = val.replace(/;/g, ",");
            }

            if( val.indexOf(",") > -1 ) {
                var splitted = val.split(",");
                if( Array.isArray(splitted) == true && splitted.length > 0 ) {
                    splitted.forEach(function(v) {
                        var s = v.trim();
                        if( v == "" ) {
                            return;
                        }
                        var g = s.match("<(.*)>");
                        if(Array.isArray(g) && g.length > 1) {
                            result.push((g[1]).trim());
                        }
                        else if(s.indexOf('@') > -1 && s.indexOf('.') > -1) {
                            result.push(s);
                        }
                    });
                }
            }
            else if( val.indexOf("<") > -1 && val.indexOf(">") > -1) {
                var s = val.trim();
                var g = s.match("<(.*)>");
                if(Array.isArray(g) && g.length > 1) {
                    result.push((g[1]).trim());
                }
            }
            else if( val.indexOf("@") > -1 && val.indexOf(".") > -1 ) {
                result.push(val);
            }
            else {
                return val;
            }
    
            return result.join(", ");    
        }
        catch(e) {
            console.log(e);
            return given_value;
        }
    }
    
    t.formatEmailsAdapter = function() {
        if($(this).val() != "") {
            var temp = t.formatEmails($(this).val());
            $(this).val(temp);
        }
    };
};

function filterCount(params, dateRange, multiple) {
    let filterCount = 0;
    $.each(params , function(i, v) {
        // console.log(i);
        if(v != "null") {
            filterCount ++;
        }
    });
    // console.log(dateRange);
    // let filterCount = Object.keys(params).length;
    // console.log(filterCount);
    if(multiple) filterCount--;
    // console.log(filterCount);
    if(dateRange == "null" || dateRange > 0 ) filterCount--;
    if(filterCount > 0) {
        $('#filter_count').css('display', 'flex');
        $('#filter_count').text(filterCount);
        $('.btn-boardFilter #filter-unact').addClass('hidden');
        $('.btn-boardFilter #filter-act').removeClass('hidden');
    } else {
        $('#filter_count').css('display', 'none');
        $('.btn-boardFilter #filter-unact').removeClass('hidden');
        $('.btn-boardFilter #filter-act').addClass('hidden');
    }
    $('#advance-filters').collapse('hide');

}

function resetFilterCount() {
    $('#filter_count').css('display', 'none');
    $('#advance-filters').collapse('hide');
}

function filterCountWithout(params, dateRange, multiple) {
    let filterCount = 0;
    $.each(params , function(i, v) {
        if(v != "null") {
            filterCount ++;
        }
    });
    if(multiple) filterCount--;
   
    if(dateRange == "null" || dateRange > 0 ) filterCount--;
    if(filterCount > 0) {
        $('#filter_count').css('display', 'flex');
        $('#filter_count').text(filterCount);
    } else {
        $('#filter_count').css('display', 'none');
    }
    $('#advance-filters').collapse('show');
}