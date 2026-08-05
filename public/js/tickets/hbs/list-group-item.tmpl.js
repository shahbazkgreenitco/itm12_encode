(function() {
    var template = Handlebars.template,
        templates = Handlebars.templates = Handlebars.templates || {};
    templates['list-group-item.hbs'] = template({
        "1": function(container, depth0, helpers, partials, data) {
            return " merge_sec ";
        },
        "3": function(container, depth0, helpers, partials, data) {
            return "                <div class=\"js-act-staring\">\r\n                    <i class=\"fa fa-star\"></i>\r\n                </div>\r\n";
        },
        "5": function(container, depth0, helpers, partials, data) {
            var helper;

            return "                <span class=\"label label-spam\">" +
                container.escapeExpression(((helper = (helper = helpers.Spam_t || (depth0 != null ? depth0.Spam_t : depth0)) != null ? helper : helpers.helperMissing), (typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}), { "name": "Spam_t", "hash": {}, "data": data }) : helper))) +
                "</span>\r\n";
        },
        "7": function(container, depth0, helpers, partials, data) {
            var helper;

            return "                <span class=\"label-merged-primary\">" +
                container.escapeExpression(((helper = (helper = helpers.Merge_Primary_t || (depth0 != null ? depth0.Merge_Primary_t : depth0)) != null ? helper : helpers.helperMissing), (typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}), { "name": "Merge_Primary_t", "hash": {}, "data": data }) : helper))) +
                "</span>\r\n";
        },
        "9": function(container, depth0, helpers, partials, data) {
            var stack1, helper, alias1 = container.escapeExpression;

            return "                <span class=\"label-merged\">Merged - <a href=\"" +
                alias1(((helper = (helper = helpers.merge_primary_link || (depth0 != null ? depth0.merge_primary_link : depth0)) != null ? helper : helpers.helperMissing), (typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}), { "name": "merge_primary_link", "hash": {}, "data": data }) : helper))) +
                "\">#" +
                alias1(container.lambda(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.merge_primary : stack1), depth0)) +
                "</a></span>\r\n";
        },
        "11": function(container, depth0, helpers, partials, data) {
            var stack1, alias1 = container.lambda,
                alias2 = container.escapeExpression;

            return "                    <span class=\"f-B\">\r\n                        <span class=\"priority priority priority" +
                alias2(alias1(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.priority : stack1), depth0)) +
                "\"></span>\r\n                        " +
                alias2(alias1(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.priority : stack1), depth0)) +
                "\r\n                    </span>\r\n";
        },
        "13": function(container, depth0, helpers, partials, data) {
            return "                            <i class=\"fa fa-envelope\" aria-hidden=\"true\"></i>\r\n";
        },
        "15": function(container, depth0, helpers, partials, data) {
            var stack1;

            return ((stack1 = helpers["if"].call(depth0 != null ? depth0 : (container.nullContext || {}), (depth0 != null ? depth0.assigned_to_avail : depth0), { "name": "if", "hash": {}, "fn": container.program(16, data, 0), "inverse": container.noop, "data": data })) != null ? stack1 : "");
        },
        "16": function(container, depth0, helpers, partials, data) {
            var stack1, helper, alias1 = depth0 != null ? depth0 : (container.nullContext || {}),
                alias2 = helpers.helperMissing,
                alias3 = "function",
                alias4 = container.escapeExpression,
                alias5 = container.lambda;

            return "                <p class=\"lgi-lbl-k\">" +
                alias4(((helper = (helper = helpers.Assgined_To_t || (depth0 != null ? depth0.Assgined_To_t : depth0)) != null ? helper : alias2), (typeof helper === alias3 ? helper.call(alias1, { "name": "Assgined_To_t", "hash": {}, "data": data }) : helper))) +
                ":</p>\r\n                <div class=\"ubi\" title=\"Assigned To\">   \r\n                    <img class=\"img-md img-circle\" src=\"" +
                alias4(((helper = (helper = helpers.assigner_img || (depth0 != null ? depth0.assigner_img : depth0)) != null ? helper : alias2), (typeof helper === alias3 ? helper.call(alias1, { "name": "assigner_img", "hash": {}, "data": data }) : helper))) +
                "\" alt=\"Profile Image\" />\r\n                    <div class=\"ubi-content\">\r\n                        <p class=\"f-A\">" +
                alias4(alias5(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.assigned_to_name : stack1), depth0)) +
                "</p>\r\n                        <p class=\"f-C\">" +
                alias4(alias5(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.assigned_to_loc : stack1), depth0)) +
                "</p>\r\n                    </div>\r\n                </div>\r\n";
        },
        "18": function(container, depth0, helpers, partials, data) {
            var stack1, helper, alias1 = container.escapeExpression;

            return "                <a href=\"#\" class=\"add_to_merge\" data-id=\"" +
                alias1(container.lambda(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.id : stack1), depth0)) +
                "\"><i class=\"fa fa-code-fork\"></i> " +
                alias1(((helper = (helper = helpers.Add_to_Merge_t || (depth0 != null ? depth0.Add_to_Merge_t : depth0)) != null ? helper : helpers.helperMissing), (typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}), { "name": "Add_to_Merge_t", "hash": {}, "data": data }) : helper))) +
                "</a>\r\n";
        },
        "19": function(container, depth0, helpers, partials, data) {
            return "                            <i class=\"fa fa-calendar\" aria-hidden=\"true\"></i>\r\n";
        },
        "compiler": [7, ">= 4.0.0"],
        "main": function(container, depth0, helpers, partials, data) {
            var stack1, helper, alias1 = container.lambda,
                alias2 = container.escapeExpression,
                alias3 = depth0 != null ? depth0 : (container.nullContext || {}),
                alias4 = helpers.helperMissing,
                alias5 = "function";

            return "\r\n<div class=\"list-group-item color-code-" +
                alias2(alias1(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.color_code : stack1), depth0)) +
                "\">\r\n    <div class=\"row mar-btm-6\">\r\n        <div class=\"col-md-5\">\r\n            <span class=\"tkt_sub " +
                ((stack1 = helpers["if"].call(alias3, (depth0 != null ? depth0.is_merged_sec : depth0), { "name": "if", "hash": {}, "fn": container.program(1, data, 0), "inverse": container.noop, "data": data })) != null ? stack1 : "") +
                "\">\r\n" +
                ((stack1 = helpers["if"].call(alias3, ((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.is_starred : stack1), { "name": "if", "hash": {}, "fn": container.program(3, data, 0), "inverse": container.noop, "data": data })) != null ? stack1 : "") +
                "                <div class=\"" +
                alias2(((helper = (helper = helpers.feedback || (depth0 != null ? depth0.feedback : depth0)) != null ? helper : alias4), (typeof helper === alias5 ? helper.call(alias3, { "name": "feedback", "hash": {}, "data": data }) : helper))) +
                "\"></div>\r\n" +
                ((stack1 = helpers["if"].call(alias3, ((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.spam : stack1), { "name": "if", "hash": {}, "fn": container.program(5, data, 0), "inverse": container.noop, "data": data })) != null ? stack1 : "") +
                ((stack1 = helpers["if"].call(alias3, ((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.is_merge_primary : stack1), { "name": "if", "hash": {}, "fn": container.program(7, data, 0), "inverse": container.noop, "data": data })) != null ? stack1 : "") +
                ((stack1 = helpers["if"].call(alias3, (depth0 != null ? depth0.is_merged_sec : depth0), { "name": "if", "hash": {}, "fn": container.program(9, data, 0), "inverse": container.noop, "data": data })) != null ? stack1 : "") +
                "                <a href=\"" +
                alias2(((helper = (helper = helpers.view_link || (depth0 != null ? depth0.view_link : depth0)) != null ? helper : alias4), (typeof helper === alias5 ? helper.call(alias3, { "name": "view_link", "hash": {}, "data": data }) : helper))) +
                "\" target=\"_blank\">" +
                alias2((helpers.textMask || (depth0 && depth0.textMask) || alias4).call(alias3, 40, ((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.subject : stack1), { "name": "textMask", "hash": {}, "data": data })) +
                "</a>\r\n            </span>\r\n        </div>\r\n        <div class=\"col-md-7 pad-no\">\r\n            <div class=\"row\">\r\n                <div class=\"col-md-2\">\r\n                    <span class=\"tkt_id\">#" +
                alias2(alias1(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.id : stack1), depth0)) +
                "</span>\r\n                </div>\r\n                <div class=\"col-md-3\">\r\n                    <span class=\"f-B\">\r\n                        <i class=\"lgi-ico lgi-ico-status\"></i>" +
                alias2(alias1(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.status : stack1), depth0)) +
                "\r\n                    </span>\r\n                </div>\r\n                <div class=\"col-md-2\">\r\n" +
                ((stack1 = helpers.unless.call(alias3, ((stack1 = (depth0 != null ? depth0.hide_fields : depth0)) != null ? stack1.priority : stack1), { "name": "unless", "hash": {}, "fn": container.program(11, data, 0), "inverse": container.noop, "data": data })) != null ? stack1 : "") +
                "                </div>\r\n                <div class=\"col-md-5\">\r\n                    <div class=\"f-C\">" +
                ((stack1 = ((helper = (helper = helpers.status_div1 || (depth0 != null ? depth0.status_div1 : depth0)) != null ? helper : alias4), (typeof helper === alias5 ? helper.call(alias3, { "name": "status_div1", "hash": {}, "data": data }) : helper))) != null ? stack1 : "") +
                "</div>\r\n                </div>\r\n            </div>\r\n        </div>\r\n    </div>\r\n    <div class=\"row options-cvrable\">\r\n        <div class=\"col-md-3\">\r\n            <p class=\"lgi-lbl-k\">" +
                alias2(((helper = (helper = helpers.Creator_Info_t || (depth0 != null ? depth0.Creator_Info_t : depth0)) != null ? helper : alias4), (typeof helper === alias5 ? helper.call(alias3, { "name": "Creator_Info_t", "hash": {}, "data": data }) : helper))) +
                ":</p>\r\n            <div class=\"ubi\" title=\"Creator\">   \r\n                <img class=\"img-md img-circle\" src=\"" +
                alias2(((helper = (helper = helpers.profile_img || (depth0 != null ? depth0.profile_img : depth0)) != null ? helper : alias4), (typeof helper === alias5 ? helper.call(alias3, { "name": "profile_img", "hash": {}, "data": data }) : helper))) +
                "\" alt=\"Profile Image\" />\r\n                <div class=\"ubi-content\">\r\n                    <p class=\"f-A\">" +
                alias2(alias1(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.creator_name : stack1), depth0)) +
                "</p>\r\n                    <p class=\"f-C\">\r\n" +
                ((stack1 = helpers["if"].call(alias3, (depth0 != null ? depth0.via_email : depth0), { "name": "if", "hash": {}, "fn": container.program(13, data, 0), "inverse": container.noop, "data": data })) != null ? stack1 : "") +
                "                        " +
                ((stack1 = helpers["if"].call(alias3, (depth0 != null ? depth0.via_schedular : depth0), { "name": "if", "hash": {}, "fn": container.program(19, data, 0), "inverse": container.noop, "data": data })) != null ? stack1 : "") +
                "                        " +
                alias2((helpers.concatWithSeparator || (depth0 && depth0.concatWithSeparator) || alias4).call(alias3, "|", (depth0 != null ? depth0.creator_info : depth0), { "name": "concatWithSeparator", "hash": {}, "data": data })) +
                "\r\n                    </p>\r\n                </div>\r\n            </div>\r\n        </div>\r\n        <div class=\"col-md-4\">\r\n            <p class=\"lgi-lbl-k\">" +
                alias2(((helper = (helper = helpers.Department_Info_t || (depth0 != null ? depth0.Department_Info_t : depth0)) != null ? helper : alias4), (typeof helper === alias5 ? helper.call(alias3, { "name": "Department_Info_t", "hash": {}, "data": data }) : helper))) +
                ":</p>\r\n            <p class=\"f-A\" title=\"Department\"><i class=\"lgi-ico lgi-ico-department\"></i>" +
                alias2(alias1(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.dep_name : stack1), depth0)) +
                "</p>\r\n            <p class=\"f-C\" title=\"Problem & Sub Categories\"><i class=\"lgi-ico lgi-ico-problem\"></i>" +
                alias2((helpers.concatWithSeparator || (depth0 && depth0.concatWithSeparator) || alias4).call(alias3, "|", (depth0 != null ? depth0.category_info : depth0), { "name": "concatWithSeparator", "hash": {}, "data": data })) +
                "</p>\r\n        </div>\r\n        <div class=\"col-md-2\">\r\n            <p class=\"lgi-lbl-k\">" +
                alias2(((helper = (helper = helpers.CreatedUpdated_At_t || (depth0 != null ? depth0.CreatedUpdated_At_t : depth0)) != null ? helper : alias4), (typeof helper === alias5 ? helper.call(alias3, { "name": "CreatedUpdated_At_t", "hash": {}, "data": data }) : helper))) +
                ":</p>\r\n            <p class=\"f-C\" title=\"Ticket Created At\"><i class=\"lgi-ico lgi-ico-created\"></i>" +
                alias2(alias1(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.created_at_format : stack1), depth0)) +
                "</p>\r\n            <p class=\"f-C\" title=\"Ticket Updated At\"><i class=\"lgi-ico lgi-ico-updated\"></i>" +
                alias2(alias1(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.updated_at_format : stack1), depth0)) +
                "</p>\r\n        </div>\r\n        <div class=\"col-md-3\">\r\n" +
                ((stack1 = helpers.unless.call(alias3, ((stack1 = (depth0 != null ? depth0.hide_fields : depth0)) != null ? stack1.assigned_to : stack1), { "name": "unless", "hash": {}, "fn": container.program(15, data, 0), "inverse": container.noop, "data": data })) != null ? stack1 : "") +
                "        </div>\r\n        \r\n        <div class=\"act_options1\">\r\n            <div class=\"act_options1_list\">\r\n                <a href=\"" +
                alias2(((helper = (helper = helpers.view_link || (depth0 != null ? depth0.view_link : depth0)) != null ? helper : alias4), (typeof helper === alias5 ? helper.call(alias3, { "name": "view_link", "hash": {}, "data": data }) : helper))) +
                "\" target=\"_blank\"><i class=\"fa fa-eye\"></i> " +
                alias2(((helper = (helper = helpers.View_t || (depth0 != null ? depth0.View_t : depth0)) != null ? helper : alias4), (typeof helper === alias5 ? helper.call(alias3, { "name": "View_t", "hash": {}, "data": data }) : helper))) +
                "</a>\r\n" +
                ((stack1 = helpers["if"].call(alias3, (depth0 != null ? depth0.can_merge : depth0), { "name": "if", "hash": {}, "fn": container.program(18, data, 0), "inverse": container.noop, "data": data })) != null ? stack1 : "") +
                "            </div>\r\n        </div>\r\n    </div>\r\n</div>";
        },
        "useData": true
    });
    templates['list-group-item.hbs'] = template({
        "1": function(container, depth0, helpers, partials, data) {
            return " merge_sec ";
        },
        "3": function(container, depth0, helpers, partials, data) {
            return "                <div class=\"js-act-staring\">\r\n                    <i class=\"fa fa-star\"></i>\r\n                </div>\r\n";
        },
        "5": function(container, depth0, helpers, partials, data) {
            var helper;

            return "                <span class=\"label label-spam\">" +
                container.escapeExpression(((helper = (helper = helpers.Spam_t || (depth0 != null ? depth0.Spam_t : depth0)) != null ? helper : helpers.helperMissing), (typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}), { "name": "Spam_t", "hash": {}, "data": data }) : helper))) +
                "</span>\r\n";
        },
        "7": function(container, depth0, helpers, partials, data) {
            var helper;

            return "                <span class=\"label-merged-primary\">" +
                container.escapeExpression(((helper = (helper = helpers.Merge_Primary_t || (depth0 != null ? depth0.Merge_Primary_t : depth0)) != null ? helper : helpers.helperMissing), (typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}), { "name": "Merge_Primary_t", "hash": {}, "data": data }) : helper))) +
                "</span>\r\n";
        },
        "9": function(container, depth0, helpers, partials, data) {
            var stack1, helper, alias1 = container.escapeExpression;

            return "                <span class=\"label-merged\">Merged - <a href=\"" +
                alias1(((helper = (helper = helpers.merge_primary_link || (depth0 != null ? depth0.merge_primary_link : depth0)) != null ? helper : helpers.helperMissing), (typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}), { "name": "merge_primary_link", "hash": {}, "data": data }) : helper))) +
                "\">#" +
                alias1(container.lambda(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.merge_primary : stack1), depth0)) +
                "</a></span>\r\n";
        },
        "11": function(container, depth0, helpers, partials, data) {
            var stack1, alias1 = container.lambda,
                alias2 = container.escapeExpression;

            return "                    <span class=\"f-B\">\r\n                        <span class=\"priority priority priority" +
                alias2(alias1(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.priority : stack1), depth0)) +
                "\"></span>\r\n                        " +
                alias2(alias1(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.priority : stack1), depth0)) +
                "\r\n                    </span>\r\n";
        },
        "13": function(container, depth0, helpers, partials, data) {
            return "                            <i class=\"fa fa-envelope\" aria-hidden=\"true\"></i>\r\n";
        },
        "15": function(container, depth0, helpers, partials, data) {
            var stack1;

            return ((stack1 = helpers["if"].call(depth0 != null ? depth0 : (container.nullContext || {}), (depth0 != null ? depth0.assigned_to_avail : depth0), { "name": "if", "hash": {}, "fn": container.program(16, data, 0), "inverse": container.noop, "data": data })) != null ? stack1 : "");
        },
        "16": function(container, depth0, helpers, partials, data) {
            var stack1, helper, alias1 = depth0 != null ? depth0 : (container.nullContext || {}),
                alias2 = helpers.helperMissing,
                alias3 = "function",
                alias4 = container.escapeExpression,
                alias5 = container.lambda;

            return "                <p class=\"lgi-lbl-k\">" +
                alias4(((helper = (helper = helpers.Assgined_To_t || (depth0 != null ? depth0.Assgined_To_t : depth0)) != null ? helper : alias2), (typeof helper === alias3 ? helper.call(alias1, { "name": "Assgined_To_t", "hash": {}, "data": data }) : helper))) +
                ":</p>\r\n                <div class=\"ubi\" title=\"Assigned To\">   \r\n                    <img class=\"img-md img-circle\" src=\"" +
                alias4(((helper = (helper = helpers.assigner_img || (depth0 != null ? depth0.assigner_img : depth0)) != null ? helper : alias2), (typeof helper === alias3 ? helper.call(alias1, { "name": "assigner_img", "hash": {}, "data": data }) : helper))) +
                "\" alt=\"Profile Image\" />\r\n                    <div class=\"ubi-content\">\r\n                        <p class=\"f-A\">" +
                alias4(alias5(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.assigned_to_name : stack1), depth0)) +
                "</p>\r\n                        <p class=\"f-C\">" +
                alias4(alias5(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.assigned_to_loc : stack1), depth0)) +
                "</p>\r\n                    </div>\r\n                </div>\r\n";
        },
        "18": function(container, depth0, helpers, partials, data) {
            var stack1, helper, alias1 = container.escapeExpression;

            return "                <a href=\"#\" class=\"add_to_merge\" data-id=\"" +
                alias1(container.lambda(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.id : stack1), depth0)) +
                "\"><i class=\"fa fa-code-fork\"></i> " +
                alias1(((helper = (helper = helpers.Add_to_Merge_t || (depth0 != null ? depth0.Add_to_Merge_t : depth0)) != null ? helper : helpers.helperMissing), (typeof helper === "function" ? helper.call(depth0 != null ? depth0 : (container.nullContext || {}), { "name": "Add_to_Merge_t", "hash": {}, "data": data }) : helper))) +
                "</a>\r\n";
        },
        "19": function(container, depth0, helpers, partials, data) {
            return "                            <i class=\"fa fa-calendar\" aria-hidden=\"true\"></i>\r\n";
        },
        "compiler": [7, ">= 4.0.0"],
        "main": function(container, depth0, helpers, partials, data) {
            var stack1, helper, alias1 = container.lambda,
                alias2 = container.escapeExpression,
                alias3 = depth0 != null ? depth0 : (container.nullContext || {}),
                alias4 = helpers.helperMissing,
                alias5 = "function";

            return "\r\n<div class=\"list-group-item color-code-" +
                alias2(alias1(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.color_code : stack1), depth0)) +
                "\">\r\n    <div class=\"row mar-btm-6\">\r\n        <div class=\"col-md-5\">\r\n            <span class=\"tkt_sub " +
                ((stack1 = helpers["if"].call(alias3, (depth0 != null ? depth0.is_merged_sec : depth0), { "name": "if", "hash": {}, "fn": container.program(1, data, 0), "inverse": container.noop, "data": data })) != null ? stack1 : "") +
                "\">\r\n" +
                ((stack1 = helpers["if"].call(alias3, ((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.is_starred : stack1), { "name": "if", "hash": {}, "fn": container.program(3, data, 0), "inverse": container.noop, "data": data })) != null ? stack1 : "") +
                "                <div class=\"" +
                alias2(((helper = (helper = helpers.feedback || (depth0 != null ? depth0.feedback : depth0)) != null ? helper : alias4), (typeof helper === alias5 ? helper.call(alias3, { "name": "feedback", "hash": {}, "data": data }) : helper))) +
                "\"></div>\r\n" +
                ((stack1 = helpers["if"].call(alias3, ((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.spam : stack1), { "name": "if", "hash": {}, "fn": container.program(5, data, 0), "inverse": container.noop, "data": data })) != null ? stack1 : "") +
                ((stack1 = helpers["if"].call(alias3, ((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.is_merge_primary : stack1), { "name": "if", "hash": {}, "fn": container.program(7, data, 0), "inverse": container.noop, "data": data })) != null ? stack1 : "") +
                ((stack1 = helpers["if"].call(alias3, (depth0 != null ? depth0.is_merged_sec : depth0), { "name": "if", "hash": {}, "fn": container.program(9, data, 0), "inverse": container.noop, "data": data })) != null ? stack1 : "") +
                "                <a href=\"" +
                alias2(((helper = (helper = helpers.view_link || (depth0 != null ? depth0.view_link : depth0)) != null ? helper : alias4), (typeof helper === alias5 ? helper.call(alias3, { "name": "view_link", "hash": {}, "data": data }) : helper))) +
                "\" target=\"_blank\">" +
                alias2((helpers.textMask || (depth0 && depth0.textMask) || alias4).call(alias3, 40, ((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.subject : stack1), { "name": "textMask", "hash": {}, "data": data })) +
                "</a>\r\n            </span>\r\n        </div>\r\n        <div class=\"col-md-7 pad-no\">\r\n            <div class=\"row\">\r\n                <div class=\"col-md-2\">\r\n                    <span class=\"tkt_id\">#" +
                alias2(alias1(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.id : stack1), depth0)) +
                "</span>\r\n                </div>\r\n                <div class=\"col-md-3\">\r\n                    <span class=\"f-B\">\r\n                        <i class=\"lgi-ico lgi-ico-status\"></i>" +
                alias2(alias1(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.status : stack1), depth0)) +
                "\r\n                    </span>\r\n                </div>\r\n                <div class=\"col-md-2\">\r\n" +
                ((stack1 = helpers.unless.call(alias3, ((stack1 = (depth0 != null ? depth0.hide_fields : depth0)) != null ? stack1.priority : stack1), { "name": "unless", "hash": {}, "fn": container.program(11, data, 0), "inverse": container.noop, "data": data })) != null ? stack1 : "") +
                "                </div>\r\n                <div class=\"col-md-5\">\r\n                    <div class=\"f-C\">" +
                ((stack1 = ((helper = (helper = helpers.status_div1 || (depth0 != null ? depth0.status_div1 : depth0)) != null ? helper : alias4), (typeof helper === alias5 ? helper.call(alias3, { "name": "status_div1", "hash": {}, "data": data }) : helper))) != null ? stack1 : "") +
                "</div>\r\n                </div>\r\n            </div>\r\n        </div>\r\n    </div>\r\n    <div class=\"row options-cvrable\">\r\n        <div class=\"col-md-3\">\r\n            <p class=\"lgi-lbl-k\">" +
                alias2(((helper = (helper = helpers.Creator_Info_t || (depth0 != null ? depth0.Creator_Info_t : depth0)) != null ? helper : alias4), (typeof helper === alias5 ? helper.call(alias3, { "name": "Creator_Info_t", "hash": {}, "data": data }) : helper))) +
                ":</p>\r\n            <div class=\"ubi\" title=\"Creator\">   \r\n                <img class=\"img-md img-circle\" src=\"" +
                alias2(((helper = (helper = helpers.profile_img || (depth0 != null ? depth0.profile_img : depth0)) != null ? helper : alias4), (typeof helper === alias5 ? helper.call(alias3, { "name": "profile_img", "hash": {}, "data": data }) : helper))) +
                "\" alt=\"Profile Image\" />\r\n                <div class=\"ubi-content\">\r\n                    <p class=\"f-A\">" +
                alias2(alias1(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.creator_name : stack1), depth0)) +
                "</p>\r\n                    <p class=\"f-C\">\r\n" +
                ((stack1 = helpers["if"].call(alias3, (depth0 != null ? depth0.via_email : depth0), { "name": "if", "hash": {}, "fn": container.program(13, data, 0), "inverse": container.noop, "data": data })) != null ? stack1 : "") +
                "                        " +
                ((stack1 = helpers["if"].call(alias3, (depth0 != null ? depth0.via_schedular : depth0), { "name": "if", "hash": {}, "fn": container.program(19, data, 0), "inverse": container.noop, "data": data })) != null ? stack1 : "") +
                "                        " +
                alias2((helpers.concatWithSeparator || (depth0 && depth0.concatWithSeparator) || alias4).call(alias3, "|", (depth0 != null ? depth0.creator_info : depth0), { "name": "concatWithSeparator", "hash": {}, "data": data })) +
                "\r\n                    </p>\r\n                </div>\r\n            </div>\r\n        </div>\r\n        <div class=\"col-md-4\">\r\n            <p class=\"lgi-lbl-k\">" +
                alias2(((helper = (helper = helpers.Department_Info_t || (depth0 != null ? depth0.Department_Info_t : depth0)) != null ? helper : alias4), (typeof helper === alias5 ? helper.call(alias3, { "name": "Department_Info_t", "hash": {}, "data": data }) : helper))) +
                ":</p>\r\n            <p class=\"f-A\" title=\"Department\"><i class=\"lgi-ico lgi-ico-department\"></i>" +
                alias2(alias1(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.dep_name : stack1), depth0)) +
                "</p>\r\n            <p class=\"f-C\" title=\"Problem & Sub Categories\"><i class=\"lgi-ico lgi-ico-problem\"></i>" +
                alias2((helpers.concatWithSeparator || (depth0 && depth0.concatWithSeparator) || alias4).call(alias3, "|", (depth0 != null ? depth0.category_info : depth0), { "name": "concatWithSeparator", "hash": {}, "data": data })) +
                "</p>\r\n        </div>\r\n        <div class=\"col-md-2\">\r\n            <p class=\"lgi-lbl-k\">" +
                alias2(((helper = (helper = helpers.CreatedUpdated_At_t || (depth0 != null ? depth0.CreatedUpdated_At_t : depth0)) != null ? helper : alias4), (typeof helper === alias5 ? helper.call(alias3, { "name": "CreatedUpdated_At_t", "hash": {}, "data": data }) : helper))) +
                ":</p>\r\n            <p class=\"f-C\" title=\"Ticket Created At\"><i class=\"lgi-ico lgi-ico-created\"></i>" +
                alias2(alias1(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.created_at_format : stack1), depth0)) +
                "</p>\r\n            <p class=\"f-C\" title=\"Ticket Updated At\"><i class=\"lgi-ico lgi-ico-updated\"></i>" +
                alias2(alias1(((stack1 = (depth0 != null ? depth0.d : depth0)) != null ? stack1.updated_at_format : stack1), depth0)) +
                "</p>\r\n        </div>\r\n        <div class=\"col-md-3\">\r\n" +
                ((stack1 = helpers.unless.call(alias3, ((stack1 = (depth0 != null ? depth0.hide_fields : depth0)) != null ? stack1.assigned_to : stack1), { "name": "unless", "hash": {}, "fn": container.program(15, data, 0), "inverse": container.noop, "data": data })) != null ? stack1 : "") +
                "        </div>\r\n        \r\n        <div class=\"act_options1\">\r\n            <div class=\"act_options1_list\">\r\n                <a href=\"" +
                alias2(((helper = (helper = helpers.view_link || (depth0 != null ? depth0.view_link : depth0)) != null ? helper : alias4), (typeof helper === alias5 ? helper.call(alias3, { "name": "view_link", "hash": {}, "data": data }) : helper))) +
                "\" target=\"_blank\"><i class=\"fa fa-eye\"></i> " +
                alias2(((helper = (helper = helpers.View_t || (depth0 != null ? depth0.View_t : depth0)) != null ? helper : alias4), (typeof helper === alias5 ? helper.call(alias3, { "name": "View_t", "hash": {}, "data": data }) : helper))) +
                "</a>\r\n" +
                ((stack1 = helpers["if"].call(alias3, (depth0 != null ? depth0.can_merge : depth0), { "name": "if", "hash": {}, "fn": container.program(18, data, 0), "inverse": container.noop, "data": data })) != null ? stack1 : "") +
                "            </div>\r\n        </div>\r\n    </div>\r\n</div>";
        },
        "useData": true
    });
})();
