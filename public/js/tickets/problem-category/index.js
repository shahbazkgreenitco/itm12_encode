var ProblemCategory = function(config) {
    var t = this;
    t.config = config;
    t.config.search = t.config.search || "";
    t.config.other_filters = t.config.other_filters || {};
    t.config.export_filters = t.config.export_filters || "";
    let companyId = t.config.company_user_detail ? t.config.company_user_detail.dashboard_company_id : null;
    t.page = $("#main-user-list-wrapper");
    t.table = t.page.find("#mytable");
    t.searchInput = t.page.find(".plain-search");
    t.pageLength = t.page.find(".userModulePageLenth");
    t.filterBadge = t.page.find(".filter-count-badge");
    t.mdl = t.page.find("#TicketProblemTypeModal");
    t.mdl.title = t.mdl.find(".modal-title");

    t.mdl.frm = t.mdl.find("#frm_problem_type");
    t.mdl.frmEl = {};
    t.mdl.frmEl.id = t.mdl.frm.find("#id");
    t.mdl.frmEl.parentId = t.mdl.frm.find("#parent_id");
    t.mdl.frmEl.departmentId = t.mdl.frm.find("#department_id");
    t.mdl.frmEl.serviceTypeId = t.mdl.frm.find("#service_type_id");
    t.mdl.frmEl.ticketAttender = t.mdl.frm.find("#ticket_attender");
    t.mdl.frmEl.autoAllocationGroup = t.mdl.frm.find("#auto_allocation_group");
    t.mdl.frmEl.custom_fieldset = t.mdl.frm.find("#custom_fieldset");
    t.mdl.frmEl.number_of_days = t.mdl.frm.find("#number_of_days");
    t.mdl.frmEl.privilege_access = t.mdl.frm.find("#privilege_access");
    t.mdl.frmEl.auto_resolve_ticket = t.mdl.frm.find("#auto_resolve_ticket");
    t.mdl.frmEl.no_of_approval_days = t.mdl.frm.find("#no_of_approval_days");
    t.mdl.frmEl.priorityId = t.mdl.frm.find("#priority_id");
    t.mdl.frmEl.tat = t.mdl.frm.find("#tat");
    t.mdl.frmEl.workaround_sla = t.mdl.frm.find("#workaround_sla");
    t.mdl.frmEl.response_sla = t.mdl.frm.find("#response_sla");
    t.mdl.frmEl.close_ticket_after_days = t.mdl.frm.find("#close_ticket_after_days");
    t.mdl.frmEl.reopen_ticket_until_days = t.mdl.frm.find("#reopen_ticket_until_days");
    t.mdl.frmEl.name = t.mdl.frm.find("#name");
    t.mdl.frmEl.category_tag = t.mdl.frm.find("#category_tag");
    t.mdl.frmEl.approval_required = t.mdl.frm.find('#approval_required');
    t.mdl.frmEl.pab_id = t.mdl.frm.find('#pab_id');
    t.mdl.frmEl.remarks = t.mdl.frm.find("#remarks");
    t.mdl.frmEl.is_form_required = t.mdl.frm.find('#is_form_required');
    t.mdl.frmEl.form_id = t.mdl.frm.find("#form_id");
    t.mdl.frmEl.status = t.mdl.frm.find("#status");
    t.mdl.frmEl.company_id = t.mdl.frm.find('#company_id');
    t.mdl.frmEl.role_id = t.mdl.frm.find('#role_id');
    t.mdl.btn = {};
    t.mdl.btn.submit = t.mdl.find("#btnSubmit");
    t.mdl.btn.clear = t.mdl.find("#btnClose");
    t.mdl.btn.clr = t.mdl.find("#btnClr");

    t.mdl.remarks = t.page.find("#remarksModal");
    t.mdl.remarks.body = t.mdl.remarks.find(".modal-body");

    t.httpCall = true;
    t.httpPostPath = "";
    t.searchbox = t.table.find(".searchbox");
    // t.searchTimer = null;
    t.actionMenuHideTimer = null;
    t.btn = {};
    t.btn.reload = t.page.find(".btn-reload-list");
    t.btn.export = t.page.find(".btn-export-problem-category");
    t.btn.import = t.page.find(".btn-import-problem-category");
    t.btn.add = t.page.find(".btn-add-type");

    t.icons = {
        ellipsis: function () {
            return '<svg width="5" height="14" viewBox="0 0 5 20" fill="none" ><path d="M5 10C5 10.5128 4.85338 11.014 4.57867 11.4404C4.30397 11.8667 3.91352 12.199 3.45671 12.3952C2.99989 12.5915 2.49723 12.6428 2.01227 12.5428C1.52732 12.4427 1.08187 12.1958 0.732234 11.8332C0.382603 11.4707 0.144501 11.0087 0.0480379 10.5058C-0.0484251 10.0029 0.00108321 9.48159 0.190302 9.00786C0.379521 8.53412 0.699952 8.12922 1.11108 7.84434C1.5222 7.55946 2.00555 7.40741 2.5 7.40741C3.16304 7.40741 3.79893 7.68056 4.26777 8.16676C4.73661 8.65297 5 9.3124 5 10ZM2.5 5.18519C2.99445 5.18519 3.4778 5.03313 3.88893 4.74826C4.30005 4.46338 4.62048 4.05847 4.8097 3.58474C4.99892 3.111 5.04843 2.58972 4.95196 2.0868C4.8555 1.58389 4.6174 1.12193 4.26777 0.759354C3.91814 0.396773 3.47268 0.149853 2.98773 0.0498171C2.50277 -0.0502186 2.00011 0.00112333 1.54329 0.19735C1.08648 0.393578 0.69603 0.725877 0.421327 1.15223C0.146624 1.57858 1.34665e-06 2.07983 1.34665e-06 2.59259C1.34665e-06 3.28019 0.263393 3.93963 0.732234 4.42583C1.20107 4.91204 1.83696 5.18519 2.5 5.18519ZM2.5 14.8148C2.00555 14.8148 1.5222 14.9669 1.11108 15.2517C0.699952 15.5366 0.379521 15.9415 0.190302 16.4153C0.00108321 16.889 -0.0484251 17.4103 0.0480379 17.9132C0.144501 18.4161 0.382603 18.8781 0.732234 19.2406C1.08187 19.6032 1.52732 19.8501 2.01227 19.9502C2.49723 20.0502 2.99989 19.9989 3.45671 19.8026C3.91352 19.6064 4.30397 19.2741 4.57867 18.8478C4.85338 18.4214 5 17.9202 5 17.4074C5 16.7198 4.73661 16.0604 4.26777 15.5742C3.79893 15.088 3.16304 14.8148 2.5 14.8148Z" fill="currentColor"/></svg>';
        },
        edit: function () {
            return '<svg viewBox="0 0 16 16" fill="none" ><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>';
        },
        delete: function () {
            return '<svg viewBox="0 0 15 17" fill="none" ><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/></svg>';
        },
        history: function () {
            return `<svg width="16" height="15" class="opacity-60" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8.75 3.75002V7.14612L11.5719 8.83909C11.714 8.92445 11.8164 9.06279 11.8566 9.22366C11.8967 9.38453 11.8713 9.55476 11.7859 9.6969C11.7006 9.83904 11.5622 9.94144 11.4014 9.98159C11.2405 10.0217 11.0703 9.99633 10.9281 9.91096L7.80312 8.03596C7.71063 7.98039 7.63411 7.90183 7.58099 7.80791C7.52787 7.71399 7.49997 7.60792 7.5 7.50002V3.75002C7.5 3.58426 7.56585 3.42529 7.68306 3.30808C7.80027 3.19087 7.95924 3.12502 8.125 3.12502C8.29076 3.12502 8.44973 3.19087 8.56694 3.30808C8.68415 3.42529 8.75 3.58426 8.75 3.75002ZM8.125 2.31338e-05C7.13906 -0.00243276 6.16242 0.190675 5.25161 0.568169C4.34079 0.945664 3.51389 1.50005 2.81875 2.19924C2.25078 2.77424 1.74609 3.32737 1.25 3.90627V2.50002C1.25 2.33426 1.18415 2.17529 1.06694 2.05808C0.949731 1.94087 0.79076 1.87502 0.625 1.87502C0.45924 1.87502 0.300268 1.94087 0.183058 2.05808C0.065848 2.17529 0 2.33426 0 2.50002L0 5.62502C0 5.79078 0.065848 5.94975 0.183058 6.06697C0.300268 6.18418 0.45924 6.25002 0.625 6.25002H3.75C3.91576 6.25002 4.07473 6.18418 4.19194 6.06697C4.30915 5.94975 4.375 5.79078 4.375 5.62502C4.375 5.45926 4.30915 5.30029 4.19194 5.18308C4.07473 5.06587 3.91576 5.00002 3.75 5.00002H1.95312C2.51172 4.34221 3.06797 3.72268 3.70234 3.08049C4.57098 2.21186 5.67633 1.61847 6.88029 1.37446C8.08424 1.13045 9.33341 1.24665 10.4717 1.70853C11.61 2.17041 12.5869 2.95749 13.2805 3.97144C13.974 4.98538 14.3533 6.18121 14.3711 7.40952C14.3889 8.63782 14.0443 9.84413 13.3804 10.8777C12.7165 11.9113 11.7627 12.7263 10.6382 13.2209C9.51379 13.7155 8.2685 13.8678 7.05799 13.6587C5.84749 13.4496 4.72543 12.8885 3.83203 12.0453C3.77232 11.9889 3.70208 11.9448 3.62532 11.9155C3.54856 11.8862 3.46679 11.8724 3.38467 11.8747C3.30254 11.877 3.22168 11.8955 3.1467 11.929C3.07172 11.9626 3.00408 12.0106 2.94766 12.0703C2.89123 12.13 2.84712 12.2003 2.81783 12.277C2.78855 12.3538 2.77467 12.4356 2.777 12.5177C2.77932 12.5998 2.79779 12.6807 2.83136 12.7557C2.86493 12.8306 2.91295 12.8983 2.97266 12.9547C3.86285 13.7948 4.94512 14.4042 6.125 14.7298C7.30489 15.0554 8.54653 15.0873 9.74157 14.8226C10.9366 14.558 12.0487 14.005 12.9809 13.2117C13.913 12.4184 14.6368 11.4091 15.0892 10.2718C15.5415 9.13442 15.7086 7.90366 15.5759 6.68689C15.4432 5.47011 15.0147 4.30431 14.3279 3.29122C13.641 2.27813 12.7166 1.44854 11.6354 0.874854C10.5542 0.301167 9.34899 0.000819796 8.125 2.31338e-05Z" fill="currentColor"></path>
                    </svg>`;
        },
        taskview: function () {
            return '<svg viewBox="0 0 19 13" fill="none" ><path d="M18.6961 5.99688C18.6687 5.93516 18.007 4.46719 16.5359 2.99609C14.5758 1.03594 12.1 0 9.37499 0C6.64999 0 4.17421 1.03594 2.21405 2.99609C0.742961 4.46719 0.0781175 5.9375 0.0538988 5.99688C0.0183622 6.07681 0 6.16331 0 6.25078C0 6.33826 0.0183622 6.42476 0.0538988 6.50469C0.0812425 6.56641 0.742961 8.03359 2.21405 9.50469C4.17421 11.4641 6.64999 12.5 9.37499 12.5C12.1 12.5 14.5758 11.4641 16.5359 9.50469C18.007 8.03359 18.6687 6.56641 18.6961 6.50469C18.7316 6.42476 18.75 6.33826 18.75 6.25078C18.75 6.16331 18.7316 6.07681 18.6961 5.99688ZM9.37499 11.25C6.9703 11.25 4.86952 10.3758 3.13046 8.65234C2.4169 7.94273 1.80983 7.13356 1.32812 6.25C1.8097 5.36636 2.41679 4.55717 3.13046 3.84766C4.86952 2.12422 6.9703 1.25 9.37499 1.25C11.7797 1.25 13.8805 2.12422 15.6195 3.84766C16.3345 4.557 16.9429 5.36619 17.4258 6.25C16.8625 7.30156 14.4086 11.25 9.37499 11.25ZM9.37499 2.5C8.63331 2.5 7.90829 2.71993 7.2916 3.13199C6.67492 3.54404 6.19427 4.12971 5.91044 4.81494C5.62662 5.50016 5.55235 6.25416 5.69705 6.98159C5.84174 7.70902 6.19889 8.3772 6.72334 8.90165C7.24779 9.4261 7.91597 9.78325 8.6434 9.92795C9.37083 10.0726 10.1248 9.99838 10.8101 9.71455C11.4953 9.43072 12.0809 8.95007 12.493 8.33339C12.9051 7.7167 13.125 6.99168 13.125 6.25C13.124 5.25576 12.7285 4.30253 12.0255 3.59949C11.3225 2.89645 10.3692 2.50103 9.37499 2.5ZM9.37499 8.75C8.88054 8.75 8.39719 8.60338 7.98607 8.32867C7.57494 8.05397 7.25451 7.66352 7.06529 7.20671C6.87607 6.74989 6.82657 6.24723 6.92303 5.76227C7.01949 5.27732 7.25759 4.83186 7.60722 4.48223C7.95686 4.1326 8.40231 3.8945 8.88727 3.79804C9.37222 3.70157 9.87488 3.75108 10.3317 3.9403C10.7885 4.12952 11.179 4.44995 11.4537 4.86107C11.7284 5.2722 11.875 5.75555 11.875 6.25C11.875 6.91304 11.6116 7.54893 11.1428 8.01777C10.6739 8.48661 10.038 8.75 9.37499 8.75Z" fill="currentColor"/></svg>';
        },
        escalation: function () {
            return '<svg viewBox="0 0 18 17" fill="none" ><path d="M15.0445 1.83367C14.0518 0.839533 12.753 0.20888 11.3579 0.0434748C9.9627 -0.12193 8.55249 0.187562 7.3548 0.922008C6.15712 1.65645 5.24182 2.77301 4.75661 4.09151C4.2714 5.41 4.24458 6.85353 4.68048 8.18914L0.366413 12.5032C0.249834 12.6189 0.157408 12.7565 0.0945058 12.9082C0.0316039 13.0599 -0.000518312 13.2226 6.32415e-06 13.3868V15.6282C6.32415e-06 15.9597 0.131702 16.2777 0.366123 16.5121C0.600543 16.7465 0.918486 16.8782 1.25001 16.8782H3.75001C3.91577 16.8782 4.07474 16.8124 4.19195 16.6951C4.30916 16.5779 4.37501 16.419 4.37501 16.2532V15.0032H5.62501C5.79077 15.0032 5.94974 14.9374 6.06695 14.8201C6.18416 14.7029 6.25001 14.544 6.25001 14.3782V13.1282H7.50001C7.58211 13.1283 7.66342 13.1122 7.73929 13.0808C7.81516 13.0494 7.88411 13.0034 7.94219 12.9454L8.68907 12.1977C9.31422 12.4011 9.96762 12.5042 10.625 12.5032H10.6328C11.8683 12.5017 13.0757 12.1341 14.1023 11.4467C15.129 10.7594 15.9289 9.78314 16.401 8.64139C16.8731 7.49965 16.9962 6.24359 16.7548 5.03191C16.5133 3.82022 15.9182 2.70727 15.0445 1.83367ZM15.625 6.41726C15.5399 9.08054 13.3008 11.2501 10.6336 11.2532H10.625C9.99232 11.2543 9.36524 11.1347 8.77735 10.9009C8.66235 10.8509 8.535 10.8368 8.41184 10.8602C8.28868 10.8836 8.1754 10.9435 8.08673 11.0321L7.24141 11.8782H5.62501C5.45925 11.8782 5.30028 11.944 5.18307 12.0613C5.06586 12.1785 5.00001 12.3374 5.00001 12.5032V13.7532H3.75001C3.58425 13.7532 3.42528 13.819 3.30806 13.9363C3.19085 14.0535 3.12501 14.2124 3.12501 14.3782V15.6282H1.25001V13.3868L5.8461 8.79148C5.93472 8.7028 5.99462 8.58953 6.01803 8.46637C6.04143 8.34321 6.02726 8.21586 5.97735 8.10085C5.74278 7.511 5.62318 6.88173 5.62501 6.24695C5.62501 3.57976 7.79766 1.3407 10.4609 1.25554C11.1451 1.23271 11.8266 1.35059 12.4633 1.60188C13.1 1.85317 13.6783 2.23252 14.1624 2.71642C14.6466 3.20033 15.0262 3.77848 15.2778 4.41507C15.5293 5.05166 15.6475 5.73313 15.625 6.41726ZM13.125 4.6907C13.125 4.87612 13.07 5.05737 12.967 5.21154C12.864 5.36572 12.7176 5.48588 12.5463 5.55683C12.375 5.62779 12.1865 5.64636 12.0046 5.61018C11.8228 5.57401 11.6557 5.48472 11.5246 5.35361C11.3935 5.2225 11.3042 5.05545 11.268 4.8736C11.2318 4.69174 11.2504 4.50324 11.3214 4.33193C11.3923 4.16063 11.5125 4.01421 11.6667 3.91119C11.8208 3.80818 12.0021 3.7532 12.1875 3.7532C12.4361 3.7532 12.6746 3.85197 12.8504 4.02779C13.0262 4.2036 13.125 4.44206 13.125 4.6907Z" fill="currentColor"/></svg>';
        },
        active: function(){
        return `<svg  width="18" height="18" viewBox="0 0 16 16" fill="none"><rect width="16" height="16" rx="8" fill="#186B43"/><circle cx="8" cy="8" r="3" fill="white"/></svg>`
        },
        inactive: function(){
            return `<svg  width="18" height="18" viewBox="0 0 16 16" fill="none"><rect width="16" height="16" rx="8" fill="#F12F35"/><circle cx="8" cy="8" r="3" fill="white"/></svg>`
        }    
    };

    const requiredCheck = function(element) {
        return (t.mdl.frmEl.approval_required.val() == '1');
    };

    t.filters = {
        wrapper: t.page.find("#advanceFilterModal"),
        trigger: t.page.find(".btn-open-filter"),
        btnFilter: t.page.find("#advanceFilterModal .btn-filter"),
        btnfilterclr: t.page.find("#advanceFilterModal .btn-clear-filter"),
        data: {
            problem_categories: {}
        }
    };
    t.filters.priority = t.filters.wrapper.find("#filter_by_priority"),
    t.filters.department = t.filters.wrapper.find("#filter_by_department"),
    t.filters.allocation_group = t.filters.wrapper.find("#filter_allocation_group"),
    t.filters.authority_approval = t.filters.wrapper.find("#authority_approval"),
    t.filters.pab = t.filters.wrapper.find("#filter_by_pab"),
    t.filters.form_name = t.filters.wrapper.find("#filter_by_form"),
    t.filters.based_on_category = t.filters.wrapper.find("#filter_based_on_category"),
    t.filters.filter_by_escalation = t.filters.wrapper.find("#filter_by_escalation"),
    t.filters.filter_by_escalation_for = t.filters.wrapper.find("#filter_by_escalation_for"),
    t.filters.filter_by_escalation_to = t.filters.wrapper.find("#filter_by_escalation_to"),
    t.filters.filter_by_status = t.filters.wrapper.find("#filter_by_status"),
    t.filters.filter_by_role = t.filters.wrapper.find("#filter_by_role"),

    t.filters.fun = {
        reload_allocation_group: function() {
            t.filters.allocation_group.empty();
            t.filters.allocation_group.select2({
                ajax: {
                    url: t.config.url.getAllocationGroups,
                    dataType: "json"
                },
                width: "100%",
                dropdownParent: t.filters.wrapper,
                allowClear: true,
                minimumResultsForSearch: Infinity,
                placeholder: t.config.translations.filter_by_allocation_group
            });
        },
        reload_priority: function() {
            t.filters.priority.empty();
            $.each(t.config.priorities, function(i, k) {
                t.filters.priority.append(new Option(k.name, k.id, false, false));
            });
            t.filters.priority.trigger("change");
        },
        reload_department: function() {
            t.filters.department.empty();
            t.filters.department.select2({
                ajax: {
                    url: t.config.url.departments_with_company,
                    dataType: "json",
                    data: function (p) {
                        return {
                            search: p.term,
                             page: p.page || 1,
                            company_id: companyId
                        };
                    },
                },
                width: "100%",
                dropdownParent: t.filters.wrapper,
                allowClear: true,
                minimumResultsForSearch: Infinity,
                placeholder: config.translations.filter_by_department
            });
        },
        reload_pab: function() {
            t.filters.pab.empty();
            t.filters.pab.select2({
                ajax: {
                    url: t.config.url.getPabApproved,
                    dataType: "json"
                },
                width: "100%",
                dropdownParent: t.filters.wrapper,
                allowClear: true,
                minimumResultsForSearch: Infinity,
                placeholder: config.translations.filter_by_authority_board
            });
        },
        reload_formname: function() {
            t.filters.form_name.empty();
            t.filters.form_name.select2({
                ajax: {
                    url: t.config.url.getformname,
                    dataType: "json"
                },
                width: "100%",
                dropdownParent: t.filters.wrapper,
                allowClear: true,
                minimumResultsForSearch: Infinity,
                placeholder: config.translations.filter_by_formname
            });
        }
    };

    $.validator.addMethod("greaterThan", function(value, element, params) {
        var otherValue = $(params).val();
        if (value === "" || otherValue === "") {
            return true;
        }
        return parseFloat(value) > parseFloat(otherValue);
    }, "Please enter a value greater than {0}");

    $.validator.addMethod("greaterThanOrEqual", function(value, element, params) {
        var otherValue = $(params).val();
        if (value === "" || otherValue === "") {
            return true;
        }
        return parseFloat(value) >= parseFloat(otherValue);
    }, "Please enter a value greater than or equal to {0}");

    $.validator.addMethod("lessThan", function(value, element, params) {
        var otherValue = $(params).val();
        if (value === "" || otherValue === "") {
            return true;
        }
        return parseFloat(value) < parseFloat(otherValue);
    }, "Please enter a value less than {0}");

    $.validator.addMethod("lessThanOrEqual", function(value, element, param) {
        var parts = param.split(":");
        var selector = parts[0];
        var defaultMax = parts[1] ? parseInt(parts[1]) : 3;
        
        var compareValue = $(selector).val();
        
        if (compareValue && compareValue !== '') {
            return parseInt(value) <= parseInt(compareValue);
        } else {
            return parseInt(value) <= defaultMax;
        }
    }, function(param, element) {
        var parts = param.split(":");
        var selector = parts[0];
        var defaultMax = parts[1] ? parseInt(parts[1]) : 3;
        
        var compareValue = $(selector).val();
        
        if (compareValue && compareValue !== '') {
            return "Value must be equal to or less than " + compareValue;
        } else {
            return "Value must be equal to or less than " + defaultMax;
        }
    });
        $.validator.addMethod("reopenLimit", function (value, element, selector) {
        if (!value) {
            return true;
        }

        var closeVal = $(selector).val();

        if (!closeVal) {
            return parseInt(value) <= 3;
        }
        return parseInt(value) <= parseInt(closeVal);

    }, function (params, element) {

        var closeVal = $(params).val();
        if (!closeVal) {
            return "Value must be less than or equal to 3";
        }
        return "Value must be less than or equal to Close Ticket After Days";
    });

    t.updateValidationState = function (element, hasError) {
        var group = element.closest(".input-group");
        var isSelect2 = element.hasClass("select2-hidden-accessible");
        if (group.length) {
            group.toggleClass("amg-form-invalid", !!hasError);
        }
        if (isSelect2) {
            element.next(".select2-container")
                .find(".select2-selection")
                .toggleClass("amg-form-select-error", !!hasError);
        }
    };

    t.prepareModalFieldLayout = function() {
        if (!t.mdl || !t.mdl.frm || !t.mdl.frm.length) return;

        t.mdl.frm.find(".input-group").each(function() {
            $(this).closest(".amg-form-field").addClass("amg-form-field-row");
        });
    };

    t.getModalErrorWrap = function(element) {
        var row = element.closest(".amg-form-field-row");
        var wrap;

        if (!row.length) {
            return $();
        }

        wrap = row.children(".amg-form-error-wrap");
        if (!wrap.length) {
            wrap = $('<div class="amg-form-error-wrap"></div>');
            row.append(wrap);
        }

        return wrap;
    };

    t.frmValidator = t.mdl.frm.validate({
        onsubmit: false,
        rules: {
            service_type_id: {
                required: true,
                digits: true,
                str_name: true
            },
            parent_id: {
                str_name: true
            },
            department_id: {
                required: true,
                digits: true,
                str_name: true
            },
            name: {
                required: true,
                str_name: false,
                clean_text_only: true,
            },
            priority_id:{
                required:true
            },
            number_of_days:{
                digits: true,
                min: 1,
                max: 90,
                remarks: true,   
            },
            tat: {
                digits: true,
                min: 0,
                max: 1000,
                remarks: true,
                greaterThan: "#response_sla",
                greaterThan: "#workaround_sla"
            },
            close_ticket_after_days: {
                digits: true,
                min: 1,
                max: 10,
                remarks: true,
            },
            reopen_ticket_until_days: {
                digits: true,
                min: 1,
                max: 10,
                remarks: true,
                reopenLimit: "#close_ticket_after_days"
            },
            response_sla: {
                remarks: true,
                min: 0,
                max: 1000,
                lessThan: "#tat",
                lessThan: "#workaround_sla"
            },
            workaround_sla: {
                remarks: true,
                min: 0,
                max: 1000,
                lessThan: "#tat",
                greaterThan: "#response_sla"
            },
            'pab_id[]': {
                required: function(element) {
                    return t.mdl.frmEl.approval_required.val() == 1
                },
            },
            form_id: {
                required: function(element) {
                    return parseInt(t.mdl.frmEl.approval_required.val(), 10) === 1 &&
                        (parseInt(t.mdl.frmEl.is_form_required.val(), 10) === 1 ||
                         parseInt(t.mdl.frmEl.is_form_required.val(), 10) === 2);
                },
            },
            company_id: {required: true,},
           
        },
        messages: {
            tat: {
                greaterThan: "TAT must be greater than both Response SLA and Workaround SLA."
            },
            response_sla: {
                lessThan: "Response SLA must be less than TAT.",
                greaterThan: "Response SLA must be less than Workaround SLA."
            },
            workaround_sla: {
                lessThan: "Workaround SLA must be less than TAT.",
                greaterThan: "Workaround SLA must be greaterThan Response SLA."
            },
            company_id: {
                required: "Please select a company"
            },
            department_id:{
                require:"Please select Department"
            },
            name:{
                required:"Please Enter Problem Category Name"
            }
          
        },

        errorPlacement: function (error, element) {
                var errorWrap = t.getModalErrorWrap(element);

                if (errorWrap.length) {
                    error.appendTo(errorWrap);
                } else if (element.closest(".input-group").length) {
                    error.insertAfter(element.closest(".input-group"));
                } else {
                    error.insertAfter(element);
                }
                t.updateValidationState(element, true);
            },
            highlight: function (element) {
                t.updateValidationState($(element), true);
            },
            unhighlight: function (element) {
                t.updateValidationState($(element), false);
            }
    });

    t.escapeHtml = function(value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    };

    t.safeText = function(value, fallback) {
        var text = $.trim(String(value == null ? "" : value));
        return text === "" || text === "null" || text === "undefined" ? (fallback || "") : text;
    };

    t.hasPermission = function(permission) {
        return $.inArray(permission, t.config.permissions) !== -1;
    };

    t.renderProblemCategoryCell = function(record, type) {
        var name = t.safeText(record.pt_name, "-");
        var blocks = [];

        if (type !== "display") {
            return [
                name,
                t.safeText(record.auto_allocation_group, ""),
                t.safeText(record.pab, ""),
                t.safeText(record.form_name, "")
            ].join(" ");
        }

        blocks.push('<div class="d-flex flex-column gap-1">');
        blocks.push('<span class="b5-text fw-medium" data-bs-toggle="tooltip" title="' + t.escapeHtml(name) + '">' + t.escapeHtml(name) + '</span>');

        if (record.parent_id) {
            blocks.push('<span class="b7-text role-badge role-badge-super-admin">' + t.escapeHtml(config.translations.SUB_CATEGORY || "Sub Category") + '</span>');
        }
        if (record.esc_tot) {
            blocks.push('<span class="b5-text pcescalation">' + t.escapeHtml(record.esc_tot) + ' Escalations</span>');
        }
        if (record.approval_required === "Required") {
            blocks.push('<span class="b5-text">Approval: ' + t.escapeHtml(record.approval_required) + '</span>');
            if (t.safeText(record.pab, "")) {
                blocks.push('<span class="b5-text">Authority Board: ' + t.escapeHtml(record.pab) + '</span>');
            }
        }
        if (record.form_id != null && t.safeText(record.form_name, "")) {
            blocks.push('<span class="b5-text">' + t.escapeHtml((record.is_form_required == 2 ? "Custom Form Name: " : "Form Name: ") + record.form_name) + '</span>');
        }
        if (t.safeText(record.auto_allocation_group, "")) {
            blocks.push('<span class="b5-text">Allocation Group: ' + t.escapeHtml(record.auto_allocation_group) + '</span>');
        }

        blocks.push('</div>');
        return blocks.join("");
    };

   t.renderStatusCell = function(value, type) {
        if (type !== "display") {
            return value == 1 ? "Enable" : "Disable";
        }

        return value == 1
            ? `<span class=" b5-text d-flex justify-content-center align-items-center">${t.icons.active()} <span class="fw-normal b5-text ms-1">Enable</span> </span>`
            : `<span class="b5-text d-flex justify-content-center align-items-center">${t.icons.inactive()} <span class="fw-normal b5-text ms-1">  Disable</span> </span>`;
    };

    t.renderRemarksCell = function(value, type) {
        var plainText;

        if (!value) {
            return "";
        }

        plainText = $("<div>").html(value).text();
        if (type !== "display") {
            return '<div class="b5-text">'+ plainText +'</div>';
        }

        if (plainText.length <= 60) {
            return value;
        }

        return '<div class="b5-text">' + plainText.substring(0, 50) + '... <a href="#" class="read-more" data-full-text="' + t.escapeHtml(value) + '">Read More</a></div>';
    };

    t.getIcon = function(key) {
        return (t.icons && t.icons[key]) ? t.icons[key]() : "";
    };

    t.quickActionButtonHtml = function(label, cls, id, iconKey, extraClass) {
        return [
            '<button type="button" class="user-list-action-btn ', t.escapeHtml(extraClass || ""), ' ', cls, '" data-id="', t.escapeHtml(id), '" title="', t.escapeHtml(label), '" aria-label="', t.escapeHtml(label), '"  data-bs-toggle="tooltip">',
            t.getIcon(iconKey),
            '</button>'
        ].join("");
    };

    t.actionItemHtml = function(label, cls, id, iconKey, href, target) {
        var attrs = href
            ? ' href="' + t.escapeHtml(href) + '"'
            : ' href="#" data-id="' + t.escapeHtml(id) + '"';

        if (target) {
            attrs += ' target="' + t.escapeHtml(target) + '"';
        }

        return [
            '<li>',
            '<a class="dropdown-item ', cls, '"', attrs, '>',
            t.getIcon(iconKey),
            '<span>', t.escapeHtml(label), '</span>',
            '</a>',
            '</li>'
        ].join("");
    };

    t.renderActionsCell = function(record, type) {
        var quickActions = [];
        var menuActions = [];
        var dropdownId;

        if (type !== "display") {
            return "";
        }

        if (t.hasPermission("ProblemCategoriesEdit")) {
            quickActions.push(t.quickActionButtonHtml(config.translations.Edit_Category || "Edit Category", "dtActEdit", record.id, "edit"));
        }
        if (t.hasPermission("ProblemCategoriesDelete")) {
            quickActions.push(t.quickActionButtonHtml(config.translations.Delete_Category || "Delete Category", "dtActDel", record.id, "delete", "is-delete"));
        }
        if (t.hasPermission("ProblemCategoriesEsclationAdd")) {
            menuActions.push(t.actionItemHtml(config.translations.Esclation || "Escalation", "dtActEscl", record.id, "escalation"));
        }
        if (t.hasPermission("ProblemCategoriesHistory")) {
            menuActions.push(t.actionItemHtml(config.translations.history || 'History' , "", record.id, "history", config.url.history + "/" + record.id, "_blank"));
        }
        if (t.config.taskModule === 1 && t.hasPermission("TaskRead")) {
            menuActions.push(t.actionItemHtml( config.translations.define_tasks||"Define Tasks", "", record.id, "taskview", config.url.getTaskList + "/" + record.id, "_blank"));
        }

        if (!quickActions.length && !menuActions.length) {
            return '<span class="user-list-empty">-</span>';
        }

        dropdownId = "user-table-action-dropdown-" + record.id;

        return [
            '<div class="user-list-actions">',
            quickActions.join(""),
            menuActions.length ? [
                '<div class="">',
                '<button type="button" class="action-link user-list-menu-toggle" id="', dropdownId, '" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true" aria-label="More actions">',
                t.getIcon("ellipsis"),
                '</button>',
                '<ul class="dropdown-menu action-dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="', dropdownId, '">',
                menuActions.join(""),
                '</ul>',
                '</div>'
            ].join("") : "",
            '</div>'
        ].join("");
    };

    t.showActionMenu = function(dropdown) {
        var actionCell;
        var actionRow;

        dropdown = $(dropdown);
        if (!dropdown.length) return;

        actionCell = dropdown.closest("td, th");
        actionRow = dropdown.closest("tr");

        t.clearActionMenuHideTimer();
        t.hideActionMenus(dropdown);
        actionCell.addClass("is-action-menu-open");
        actionRow.addClass("is-action-menu-open");
        dropdown.addClass("is-hover-open");
        dropdown.find(".user-list-menu-toggle").addClass("show").attr("aria-expanded", "true");
        dropdown.find(".action-dropdown-menu").addClass("show");
    };

    t.hideActionMenu = function(dropdown) {
        var actionCell;
        var actionRow;

        dropdown = $(dropdown);
        if (!dropdown.length) return;

        actionCell = dropdown.closest("td, th");
        actionRow = dropdown.closest("tr");
        actionCell.removeClass("is-action-menu-open");
        actionRow.removeClass("is-action-menu-open");
        dropdown.removeClass("is-hover-open");
        dropdown.find(".user-list-menu-toggle").removeClass("show").attr("aria-expanded", "false");
        dropdown.find(".action-dropdown-menu").removeClass("show");
    };

    t.scheduleActionMenuHide = function(dropdown) {
        dropdown = $(dropdown);
        if (!dropdown.length) return;

        t.clearActionMenuHideTimer();
        t.actionMenuHideTimer = window.setTimeout(function() {
            t.hideActionMenu(dropdown);
        }, 180);
    };

    t.clearActionMenuHideTimer = function() {
        if (!t.actionMenuHideTimer) return;

        window.clearTimeout(t.actionMenuHideTimer);
        t.actionMenuHideTimer = null;
    };

    t.hideActionMenus = function(exceptDropdown) {
        var exceptElement = exceptDropdown && $(exceptDropdown).length ? $(exceptDropdown).get(0) : null;

        t.page.find(".user-list-actions .dropdown.is-hover-open").each(function() {
            if (exceptElement && this === exceptElement) return;
            t.hideActionMenu($(this));
        });
    };

    t.scheduleTableLayoutSync = function() {
        window.setTimeout(function() {
            if (!t.dTbl) return;

            try {
                t.dTbl.columns.adjust();

                if (typeof t.dTbl.fixedColumns === "function") {
                    var fixedColumnsApi = t.dTbl.fixedColumns();

                    if (fixedColumnsApi && typeof fixedColumnsApi.relayout === "function") {
                        fixedColumnsApi.relayout();
                    } else if (fixedColumnsApi && typeof fixedColumnsApi.update === "function") {
                        fixedColumnsApi.update();
                    }
                }
            } catch (err) {
                console.warn("Problem category table relayout skipped:", err);
            }
        }, 0);
    };

    t.upsertSelect2Options = function(element, values, opts) {
        var select = $(element);
        var items = $.isArray(values) ? values : (values ? [values] : []);
        var selectedValues = [];

        opts = opts || {};

        if (!select.length) return;

        if (opts.clear !== false) {
            select.empty();
        }

        $.each(items, function(_, item) {
            var id;
            var text;
            var option;

            if (item == null) return;

            if (typeof item === "object") {
                id = item.id;
                text = item.text != null ? item.text : (item.name != null ? item.name : item.label);
            } else {
                id = item;
                text = item;
            }

            if (id == null || text == null) return;

            option = select.find("option[value='" + String(id).replace(/'/g, "\\'") + "']");
            if (!option.length) {
                select.append(new Option(text, id, false, false));
            } else {
                option.text(text);
            }
            selectedValues.push(String(id));
        });

        if (select.prop("multiple")) {
            select.val(selectedValues).trigger("change");
        } else {
            select.val(selectedValues.length ? selectedValues[0] : "").trigger("change");
        }
    };

    let columns = [
        {
            data: 'a',
            className: "amg-table-col-264",
            render: function(data, type, row) {
                return t.renderProblemCategoryCell(row.a || {}, type);
            }

        },
        {
            data: 'a.company',
            render: function (data) {
                return `<div class="b5-text text-truncate">${data || ''}</div>`;
            }
        },
        {   data: 'a.department',
            render: function (data) {
                return `<div class="b5-text text-truncate">${data || ''}</div>`;
            }
        },
        {
            data: 'a.pc_name',
            render: function (data) {
                return `<div class="b5-text text-truncate">${data || ''}</div>`;
            }
        },
        {
            data: 'a.category_tag',
            render: function (data) {
                return `<div class="b5-text text-truncate">${data || ''}</div>`;
            }
        },
        {
            data: 'a.ticket_attender',
            render: function (data) {
                return `<div class="b5-text text-truncate">${data || ''}</div>`;
            }
        },
        {
            data: 'a.priority',
            render: function (data) {
                return `<div class="b5-text text-truncate">${data || ''}</div>`;
            }
        },
        {
            data: 'a.tat',
            render: function (data) {
                return `<div class="b5-text text-truncate">${data || ''}</div>`;
            }
        }
    ];

    if (t.config.taskModule === 1 && jQuery.inArray("TaskRead", t.config.permissions) !== -1) {
        columns.push({
            data: 'a.task_count',
            render: function (d, type, row) {
                if (d >= 0) {
                    return "<a target='_blank' style='cursor:pointer' class='view-task' id='" + row.a.id + "' data-id='" + row.a.id + "'>" + d + "</a>";
                }
                return '';
            }
        });
    }
    columns = columns.concat([
        {
            data: 'a.status',
            render: function (data, type) {
                return `
                    <span class="user-list-status">
                        ${t.renderStatusCell(data, type)}
                    </span>
                `;
            }
        },
        { data: 'a.response_sla',
            render: function (data) {
                return `<div class="b5-text text-truncate">${data || ''}</div>`;
            }
         },
        { data: 'a.workaround_sla',
            render: function (data) {
                return `<div class="b5-text text-truncate">${data || ''}</div>`;
            }
         },
        { data: 'a.close_ticket_after_days',
            render: function (data) {
                return `<div class="b5-text text-truncate">${data || ''}</div>`;
            }
         },
        { data: 'a.reopen_ticket_until_days',
            render: function (data) {
                return `<div class="b5-text text-truncate">${data || ''}</div>`;
            }
         },
        {
            data: 'a.remarks',
            orderable:false,
            className: "amg-table-col-264",
            render: function (data, type) {
                return t.renderRemarksCell(data, type);
            }
        },
        { className:"amg-table-col-176", data: 'a.last_updated_at',
            render: function (data) {
                return `<div class="b5-text text-truncate">${data || ''}</div>`;
            }
         },
        {
            data: 'a',
            orderable: false,
            searchable: false,
            width: "148px",
            className: "amg-col-actions",
            render: function(data, type, row) {
                return t.renderActionsCell(row.a || {}, type);
            }
        }
    ]);
    let lastUpdatedIndex = (t.config.taskModule === 1) && (jQuery.inArray("TaskRead", t.config.permissions) !== -1) ? 15 : 14;
    t.dTbl = t.table.DataTable({
        language: {
            info: `${t.config.datatable_translations.showing} _START_ ${t.config.datatable_translations.to} _END_ ${t.config.datatable_translations.of} _TOTAL_ ${t.config.datatable_translations.records}`,
            infoEmpty: `${t.config.datatable_translations.showing} 0 ${t.config.datatable_translations.to} 0 ${t.config.datatable_translations.of} 0 ${t.config.datatable_translations.records}`,
            emptyTable: t.config.datatable_translations.empty_result,
            zeroRecords: t.config.datatable_translations.empty_result,
            infoFiltered: `(${t.config.datatable_translations.filtered} ${t.config.datatable_translations.from} _MAX_ ${t.config.datatable_translations.total_entries})`,
            paginate: {
                previous: t.config.datatable_translations.prev,
                next: t.config.datatable_translations.next
            }
        },
        autoWidth: false,
        order: [[lastUpdatedIndex, 'desc']],
        processing: true,
        serverSide: true,
        scrollX: true,
        pageLength: parseInt(t.pageLength.val(), 10) || 10,
        lengthChange: false,
        searching: false,

        ajax: { 
            url: t.config.url.types, 
            type: "post",
            data: function(d) {
                d._token = t.config.token;
                d.search = t.config.search;
                d.filters = t.config.other_filters;
                d.main_filter = t.config.main_filter;
                d.company_id = companyId;
            },

            complete: function() {
                // Hide DataTables processing loader
                t.table
                    .closest('.dataTables_wrapper')
                    .find('.dataTables_processing')
                    .hide();
            },

            error: function() {
                // Hide loader even if AJAX fails
                t.table
                    .closest('.dataTables_wrapper')
                    .find('.dataTables_processing')
                    .hide();
            }
        },

        fixedColumns: { 
            leftColumns: 1, 
            rightColumns: 1 
        },

        dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',


        columns: columns,

        fnInitComplete: function () {
            if (t.pageLength.length) {
                t.pageLength.val(String(parseInt(t.pageLength.val(), 10) || 10));
            }

            t.scheduleTableLayoutSync();

            // Ensure processing loader is hidden after initial load
            t.table
                .closest('.dataTables_wrapper')
                .find('.dataTables_processing')
                .hide();
        },

        drawCallback: function() {
            initTooltips();
            t.scheduleTableLayoutSync();
            t.table
                .closest('.dataTables_wrapper')
                .find('.dataTables_processing')
                .hide();
        }
    });

    function initTooltips(){
        $('[data-bs-toggle="tooltip"]').tooltip();
    }

    function truncateHtml(html, maxLength) {
        var div = document.createElement("div");
        div.innerHTML = html;
        var text = div.textContent || div.innerText || "";
        if (text.length <= maxLength) {
            return html;
        }
        return text.substring(0, maxLength);
    }

    function escapeHtml(text) {
        return text.replace(/[&<>"'`=\/]/g, function (s) {
            return entityMap[s];
        });
    }

    var entityMap = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;",
        "/": "&#x2F;",
        "`": "&#x60;",
        "=": "&#x3D;"
    };

    t.hasFilterValue = function(value) {
        if ($.isArray(value)) {
            return value.filter(function(item) {
                return t.hasFilterValue(item);
            }).length > 0;
        }

        if (value === null || value === undefined) {
            return false;
        }

        value = String(value).trim();
        return value !== "" && value !== "null" && value !== "undefined";
    };

    t.getFilterValueCount = function(value) {
        if ($.isArray(value)) {
            return value.filter(function(item) {
                return t.hasFilterValue(item);
            }).length;
        }

        return t.hasFilterValue(value) ? 1 : 0;
    };

    t.getFilterLabels = function(element) {
        var labels = [];

        if (!element || !element.length) {
            return labels;
        }

        if (element.hasClass("select2-hidden-accessible")) {
            labels = $.map(element.select2("data") || [], function(item) {
                return item && item.text ? $.trim(item.text) : "";
            });
        } else {
            labels = $.map(element.find("option:selected"), function(option) {
                return $.trim($(option).text());
            });
        }

        return labels.filter(function(label) {
            return label !== "" && label.toLowerCase() !== "no filter";
        });
    };

    t.collectFilter = function(filters, summaries, key, label, element) {
        var value = element && element.length ? element.val() : null;
        var count = t.getFilterValueCount(value);
        var labels;

        if (!count) {
            return 0;
        }

        filters[key] = value;
        labels = t.getFilterLabels(element);
        summaries.push(label + ": " + (labels.length ? labels.join(", ") : ($.isArray(value) ? value.join(", ") : value)));
        return count;
    };

    t.updateFilterBadge = function(count, summaries) {
        var title = "Filter";

        if (t.filterBadge.length) {
            if (count > 0) {
                t.filterBadge.removeClass("d-none").text(count);
            } else {
                t.filterBadge.addClass("d-none").text("0");
            }
        }

        if (t.filters.trigger.length) {
            t.filters.trigger
                .attr("title", title)
                .attr("aria-label", title)
                .attr("data-bs-original-title", title);
        }
    };

    t.getValidatedSearchValue = function(showAlert) {
        var value;

        if (typeof t.searchInput.validate_str_param === "function") {
            value = t.searchInput.validate_str_param();
            if (value === false) {
                if (showAlert) {
                    alert("Please enter a valid value for search");
                }
                return false;
            }
            return value;
        }

        return $.trim(t.searchInput.val() || "");
    };

    t.cache_filter_values = function() {
        var v = t.getValidatedSearchValue(false);
        var summaries = [];
        var filterCount = 0;
        var filters = {};
        var allowedClients = ["rolepermission", "ril", "grdemo"];

        if (v === false) {
            return false;
        }

        t.config.search = v;
        filterCount += t.collectFilter(filters, summaries, "priority", config.translations.Filter_By_Priority || "Priority", t.filters.priority);
        filterCount += t.collectFilter(filters, summaries, "department", config.translations.filter_by_department || "Department", t.filters.department);
        filterCount += t.collectFilter(filters, summaries, "allocation_group", config.translations.filter_by_allocation_group || "Allocation Group", t.filters.allocation_group);
        filterCount += t.collectFilter(filters, summaries, "authority_approval", config.translations.filter_by_authority_approval || "Authority Approval", t.filters.authority_approval);
        filterCount += t.collectFilter(filters, summaries, "based_on_category", config.translations.filter_by_category || "Category Type", t.filters.based_on_category);
        filterCount += t.collectFilter(filters, summaries, "pab", config.translations.filter_by_authority_board || "Authority Board", t.filters.pab);
        filterCount += t.collectFilter(filters, summaries, "form_name", config.translations.filter_by_formname || "Form", t.filters.form_name);
        filterCount += t.collectFilter(filters, summaries, "filter_by_escalation", config.translations.filter_by_escalation || "Escalation", t.filters.filter_by_escalation);
        filterCount += t.collectFilter(filters, summaries, "filter_by_escalation_for", config.translations.filter_by_escalation_for || "Escalation For", t.filters.filter_by_escalation_for);
        filterCount += t.collectFilter(filters, summaries, "filter_by_escalation_to", config.translations.filter_by_escalation_to || "Escalation To", t.filters.filter_by_escalation_to);
        filterCount += t.collectFilter(filters, summaries, "filter_by_status", config.translations.filter_by_status || "Status", t.filters.filter_by_status);

        if (allowedClients.includes(t.config.client)) {
            filterCount += t.collectFilter(filters, summaries, "filter_by_role", config.translations.filter_by_roles || "Role", t.filters.filter_by_role);
        }

        t.config.other_filters = filters;
        t.config.export_filters = btoa(JSON.stringify({
            search: t.config.search,
            other_filters: t.config.other_filters
        }));
        t.updateFilterBadge(filterCount, summaries);
        return true;
    };

    t.openFilterModal = function(e) {
        if (e) {
            e.preventDefault();
        }

        t.toggleModal(t.filters.wrapper, true);
    };

    t.applyFilters = function(e) {
        if (e) {
            e.preventDefault();
        }

        if (t.cache_filter_values() === false) {
            return false;
        }
        t.reload();
        t.toggleModal(t.filters.wrapper, false);
    };

    t.search = function (e) {
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || $(this).is("span")) {
            var v = t.getValidatedSearchValue(true);
            if (v === false) {
                t.config.search = "";
                return false;
            }
            t.config.search = v;
            t.reload();
        } else if (target.tagName == "BUTTON") {
            if (t.cache_filter_values() === false) {
                return false;
            }
            t.reload();
        }
    };

    t.reload = function () {
        t.dTbl.ajax.reload();
    };

    t.tableSearch = function(e) {
        e.preventDefault();
        if (t.cache_filter_values() === false) {
            return false;
        }
        t.dTbl.ajax.reload();
    };

    // t.handleSearchInput = function() {
    //     window.clearTimeout(t.searchTimer);
    //     t.searchTimer = window.setTimeout(function() {
    //         if (t.cache_filter_values() === false) {
    //             return;
    //         }
    //         t.dTbl.ajax.reload();
    //     }, 250);
    // };

    t.toggleModal = function(target, shouldShow) {
        var modal = target && target.jquery ? target : $(target);

        if (!modal.length) {
            return;
        }

        if (window.bootstrap && typeof window.bootstrap.Modal === "function") {
            window.bootstrap.Modal.getOrCreateInstance(modal[0])[shouldShow ? "show" : "hide"]();
        } else if (typeof modal.modal === "function") {
            modal.modal(shouldShow ? "show" : "hide");
        }
    };

    t.import = function(e) {
        e.preventDefault();
        window.location = t.config.url.import_url;
    }

    t.export = function(e) {
        e.preventDefault();
        if (t.cache_filter_values() === false) {
            return false;
        }
        window.location = t.config.url.download_url + "?q=" + t.config.export_filters + '&company_id=' +companyId;
    };

    t.exportPDF = function(e) {
        e.preventDefault();
        if (t.cache_filter_values() === false) {
            return false;
        }
        window.location = config.url.download_pdf + "?q=" + t.config.export_filters;
    };
    
    t.resetFrm = function() {
        t.mdl.frm.trigger("reset");
        t.frmValidator.resetForm();
        t.mdl.frm.find(".amg-form-error-wrap").empty();
        t.mdl.frm.find("label.error").remove();
        t.mdl.frm.find(".error").removeClass("error");
        t.mdl.frm.find(".amg-form-invalid").removeClass("amg-form-invalid");
        t.mdl.frm.find(".amg-form-select-error").removeClass("amg-form-select-error");
        t.mdl.frmEl.id.val("");
        t.mdl.priorityId();
        t.mdl.form_id();
        t.mdl.frmEl.approval_required.val(0).trigger("change");
        t.mdl.frmEl.pab_id.val("").trigger("change");
        t.mdl.frmEl.is_form_required.val(0).trigger("change");
        t.mdl.frmEl.custom_fieldset.val(0).trigger("change");
        t.mdl.frmEl.priorityId.empty();
        $.each(t.config.priorities, function(i,v) {
            t.mdl.frmEl.priorityId.append(new Option(v.name, v.id));
        });
        t.mdl.frmEl.priorityId.trigger("change");
        t.mdl.frmEl.auto_resolve_ticket.prop('checked',false);
        t.mdl.frmEl.departmentId.empty().trigger("change");
        t.mdl.frmEl.parentId.empty().trigger("change");
        t.mdl.frmEl.ticketAttender.empty().trigger("change");
        t.mdl.frmEl.autoAllocationGroup.empty().trigger("change");
        t.mdl.frmEl.custom_fieldset.empty().trigger("change");
        t.mdl.frmEl.role_id.empty().trigger("change");
        t.mdl.frmEl.pab_id.val(null).trigger("change");
        t.mdl.frmEl.company_id.val('').trigger("change");
        t.mdl.frmEl.remarks.summernote('reset');
    };

    t.handleSubmit = function(e) {
        e.preventDefault();

        if( t.frmValidator.form() == false ) {
            return false;
        }

        if(t.httpCall != true) {
            return false;
        }
        t.httpCall = false;
        var formData = new FormData(t.mdl.frm[0]);
        var http = $.ajax({
            url: t.httpPostPath, 
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function(data) {
            if(typeof data == "object") {
                if(data.status == "success") {
                    t.reload();
                    sweetAlert('center', 'success', data);
                    // vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>'});
                    t.toggleModal(t.mdl, false);
                }
                else {
                    sweetAlert('center', 'error', data);
                    // vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>'});
                }
            }
        });
        http.fail(function() {
            alert("Something went wrong. Please check given details are correct");
        });
        http.always(function() {
            t.httpCall = true;
        });
    };

    t.addType = function(e) {
        e.preventDefault();
        t.resetFrm();
        t.httpPostPath = t.config.url.add;
        t.mdl.title.html(config.translations.New_Problem_Category);
        t.mdl.btn.submit.text(config.translations.Create);
        t.mdl.frmEl.no_of_approval_days.addClass('hide');
        t.mdl.frmEl.status.val("1").trigger("change");
        t.toggleModal(t.mdl, true);
        if (Array.isArray(config.company_defulte) && config.company_defulte.length === 1 && config.company_defulte[0].id) {
            let option = new Option(config.company_defulte[0].text, config.company_defulte[0].id, true, true);
            t.mdl.frmEl.company_id.append(option).trigger('change');
        }
    };

    t.editType = function(e) {
        e.preventDefault();
        t.resetFrm();
        var typeId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.edit + "/" + typeId;
        var http = $.get(t.config.url.get + "/" + typeId);
        http.done(function(data) {
            if(typeof data == "object") {
                if(data.status == "success") {
                    t.mdl.title.html(config.translations.Edit_Problem_Category);
                    t.mdl.btn.submit.text(config.translations.Save_Changes);
                    t.toggleListen();
                    t.mdl.frmEl.id.val(data.type.data.id);
                    t.mdl.frmEl.name.val(data.type.data.name);
                    t.mdl.frmEl.category_tag.val(data.type.data.category_tag);
                    t.mdl.frmEl.number_of_days.val(data.type.data.number_of_days);
                    if(data.type.data.privilege_access == 1){
                        t.mdl.frmEl.privilege_access.prop('checked', true);
                        t.mdl.frmEl.no_of_approval_days.removeClass('hide');
                    } else {
                        t.mdl.frmEl.no_of_approval_days.addClass('hide');
                    }
                    if(data.type.data.auto_resolve_ticket == 1){
                        t.mdl.frmEl.auto_resolve_ticket.prop('checked', true);
                    }

                    t.mdl.frmEl.tat.val(data.type.data.tat);
                    t.mdl.frmEl.response_sla.val(data.type.data.response_sla);
                    t.mdl.frmEl.close_ticket_after_days.val(data.type.data.close_ticket_after_days);
                    t.mdl.frmEl.reopen_ticket_until_days.val(data.type.data.reopen_ticket_until_days);
                    t.mdl.frmEl.workaround_sla.val(data.type.data.workaround_sla);
                    if(typeof data.type.data.priority_id != "undefined") {
                        t.mdl.frmEl.priorityId.val(data.type.data.priority_id);
                        //console.log(data.type.data.priority_id);
                    }
                    t.mdl.frmEl.priorityId.trigger("change");
                    t.mdl.frmEl.status.val(data.type.data.status).trigger("change");

                    t.upsertSelect2Options(t.mdl.frmEl.departmentId, data.type.dropdown.department);
                    t.upsertSelect2Options(t.mdl.frmEl.ticketAttender, data.type.dropdown.ticket_attender);
                    t.upsertSelect2Options(t.mdl.frmEl.autoAllocationGroup, data.type.dropdown.auto_allocation_group);
                    if(typeof data.type.dropdown.custom_fieldset != "undefined" && data.type.data.custom_fieldset != null) {
                        t.upsertSelect2Options(t.mdl.frmEl.custom_fieldset, $.map(data.type.dropdown.custom_fieldset, function(v) {
                            return { id: v.id, text: v.name };
                        }));
                        t.mdl.frmEl.custom_fieldset.val((data.type.data.custom_fieldset).split(",")).trigger('change');
                    }
            
                    if(typeof data.type.dropdown.parents != "undefined") {
                        t.fillOptsParentDropDown(data.type.dropdown.parents, data.type.data.parent_id);    
                    }
                    t.mdl.frmEl.approval_required.val(data.type.data.approval_required).trigger("change");

                    t.mdl.frmEl.is_form_required.val(data.type.data.is_form_required).trigger("change");
                    if(typeof data.type.data.form_id != "undefined") {
                        t.mdl.frmEl.form_id.val(data.type.data.form_id).trigger("change");
                        //console.log(data.type.data.form_id);
                    }

                    // Keep the full Blade-provided PAB option list intact and only mark the edit values as selected.
                    t.upsertSelect2Options(t.mdl.frmEl.pab_id, data.type.dropdown.pab_id, { clear: false });

                    if (typeof data.type.dropdown.company != "" && typeof data.type.dropdown.company != null) {
                        t.mdl.frmEl.company_id.val(data.type.dropdown.company).empty();
                        t.mdl.frmEl.company_id.append(new Option(data.type.dropdown.company.text, data.type.dropdown.company.id, true,true));
                    }
                    const allowedClients = ["rolepermission","ril","grdemo"];
                    if(allowedClients.includes(t.config.client) && data?.type?.dropdown?.roles?.length) {
                        t.upsertSelect2Options(t.mdl.frmEl.role_id, data.type.dropdown.roles);
                    }

                    t.mdl.frmEl.remarks.summernote('code', data.type.data.remarks);
                    t.approvalChanged();

                    t.toggleListen(true);
                    t.toggleModal(t.mdl, true);
                }
                else {
                    sweetAlert('center', 'error', data);
                    // vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>'});
                }
            }
        });
        http.fail(function() {
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
            // alert("Something went wrong. Please check given details are correct");
        });
        http.always(function() {
            t.httpCall = true;
        });
    };

    /* to fill the parent id drop down */
    t.fillOptsParentDropDown = function(options, id) {
        try {
            t.mdl.frmEl.parentId.empty();
            t.mdl.frmEl.parentId.append(new Option(config.translations.No_ParentCategory, ''));

            if(Array.isArray(options) == true && options.length > 0) {
                $.each(options, function(i, v) {
                    var option = typeof id != "undefined" && id == v.id ? new Option(v.name, v.id, true, true) : new Option(v.name, v.id);
                    t.mdl.frmEl.parentId.append(option);
                });

                t.mdl.frmEl.parentId.trigger("change");
            }
            else {
                t.mdl.frmEl.parentId.trigger("change");
            }
        }
        catch(e) {
            console.log(e);
        }
    }

    t.deleteType = function(e) {
        e.preventDefault();
        var typeId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.delete + "/" + typeId;
        // vex.dialog.confirm({
        //     message: config.translations.are_you_delete_problemcategory,
        //     callback: function (value) {
        //         if(value == true) {
        //             var http = $.get(t.httpPostPath);
        //             http.done(function(data) {
        //                 if(typeof data == "object") {
        //                     if(data.status == "success") {
        //                         vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>'});
        //                         t.dTbl.ajax.reload();
        //                     }
        //                     else {
        //                         vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '</p></div>'});
        //                     }
        //                 }
        //             });
        //             http.fail(function() {
        //                 alert("Something went wrong. Please check given details are correct");
        //             });
        //             http.always(function() {
        //                 t.httpCall = true;
        //             });
        //         }
        //     }
        // });
        var data = {
            'msg': config.translations.something_went_wrong,
        };
        sweetAlerts(config.translations.are_you_delete_problemcategory, 'warning', t.httpPostPath, t.dTbl, data);
    };
    
    t.onDepartmentChange = function(e) {
        e.preventDefault();
        t.mdl.frmEl.ticketAttender.empty().trigger("change");
        t.mdl.frmEl.autoAllocationGroup.empty().trigger("change");
        t.mdl.frmEl.parentId.empty();
        t.mdl.frmEl.parentId.append(new Option(config.translations.No_ParentCategory, ''));
        t.mdl.frmEl.parentId.trigger("change");
        
        var d = t.mdl.frmEl.departmentId.val();
        var c = t.mdl.frmEl.company_id.val();
        if(d > 0) {
            $.get(t.config.url.problem_categories_by_dept + '/' + d + '/' +c, function(result) {
                if(typeof result == "object" && result.status == "success" && result.data.length > 0) {
                    $.each(result.data, function(i, v) {
                        t.mdl.frmEl.parentId.append(new Option(v.name, v.id));
                    });
                    t.mdl.frmEl.parentId.trigger("change");
                }
            });
        }
    };
    
    t.toggleListen = function(s) {
        t.mdl.frmEl.departmentId.off("change", $.proxy(t.onDepartmentChange));
        if(typeof s != "undefined" && s == true) {
            t.mdl.frmEl.departmentId.on("change", $.proxy(t.onDepartmentChange));
        }
    };

    t.syncConditionalFields = function() {
        var isPrivilegeEnabled = t.mdl.frmEl.privilege_access.length && t.mdl.frmEl.privilege_access.is(":checked");
        var isApprovalRequired = parseInt(t.mdl.frmEl.approval_required.val(), 10) === 1;
        var formRequirementValue = parseInt(t.mdl.frmEl.is_form_required.val(), 10);
        var isFormSelectionVisible = formRequirementValue === 1 || formRequirementValue === 2;

        if (t.mdl.frmEl.no_of_approval_days.length) {
            t.mdl.frmEl.no_of_approval_days.toggleClass("hide", !isPrivilegeEnabled);
            if (!isPrivilegeEnabled) {
                t.mdl.frmEl.number_of_days.val("");
            }
        }

        t.mdl.frmEl.pab_id.closest(".parentcover").toggleClass("hide", !isApprovalRequired);
        t.mdl.frmEl.is_form_required.closest(".parentcover").toggleClass("hide", !isApprovalRequired);

        if (!isApprovalRequired) {
            t.mdl.frmEl.pab_id.val("").trigger("change");
            t.mdl.frmEl.form_id.val("").trigger("change");
            if (String(t.mdl.frmEl.is_form_required.val()) !== "0") {
                t.mdl.frmEl.is_form_required.val("0");
                t.mdl.form_id();
            }
        }

        t.mdl.frmEl.form_id.closest(".parentcover").toggleClass("hide", !(isApprovalRequired && isFormSelectionVisible));

        if (!(isApprovalRequired && isFormSelectionVisible)) {
            t.mdl.frmEl.form_id.val("").trigger("change");
        }
    };

    t.approvalChanged = function() {
        t.syncConditionalFields();
    };

    var select2Opts = {width:"100%"};
    t.mdl.priorityId = function () {
        t.mdl.frmEl.priorityId.empty().append(new Option(config.translations.Select_Priority_Type, "", true, true));
        $.each(t.config.priorities, function (i, v) {
            t.mdl.frmEl.priorityId.append(new Option(v.name,v.id));
        });
        t.mdl.frmEl.priorityId.trigger("change");
    };
    t.mdl.frmEl.status.select2($.extend({}, select2Opts, {dropdownParent: $('#TicketProblemTypeModal')}));
    t.mdl.frmEl.priorityId.select2($.extend({}, select2Opts, {dropdownParent: $('#TicketProblemTypeModal'),data: t.config.priorities}));
    t.mdl.frmEl.departmentId.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.departmentId.parent(),
        ajax: {
            url: t.config.url.departments_with_company,
            dataType: "json",
            data: function (p) {
                let companyId = t.mdl.frmEl.company_id.val();
                if (!companyId) {
                    return false;
                }
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: companyId
                };
            },
            delay: 300
        },
        allowClear:true,
        //minimumInputLength: 1,
        placeholder: config.translations.select_the_department
    }));
    t.mdl.frmEl.parentId.select2($.extend({}, select2Opts, {dropdownParent: t.mdl.frmEl.parentId.parent(),placeholder: config.translations.Select_Parent_Category, allowClear: true}));
    t.mdl.frmEl.approval_required.select2($.extend({}, select2Opts,{dropdownParent: $('#TicketProblemTypeModal')}));
    var message = t.config.client == 'ltts' ? "You can select maximum four" : "You can select maximum two";
    // t.mdl.frmEl.pab_id.select2($.extend({}, select2Opts));
    t.mdl.frmEl.pab_id.select2({
        width: "100%",
        dropdownParent: t.mdl.frmEl.pab_id.parent(),
        placeholder: "Authority Board (SRAT)",
        allowClear: false,
        multiple: true,
        closeOnSelect : true,
        maximumSelectionLength: t.config.client == 'ltts' ? 4 : 2,
        language: {
            maximumSelected: function () {
                return message;
            }
        }
    }).on('change', function () {
        let isUpdating = false;
        t.mdl.frmEl.pab_id.on('change', function () {

            if (isUpdating) return;
            var $select = $(this);
            var $selected = $select.find(':selected');
            var pab = $selected.data('pab');
            var id = $selected.val();
            $select.find('option').prop('disabled', false);

            if (pab == 8) {

                isUpdating = true;
                $select.val([id]);
                $select.find('option').each(function () {
                    if ($(this).data('pab') != 8) {
                        $(this).prop('disabled', true);
                    }
                });
                $select.trigger('change.select2');
                isUpdating = false;
            }
        });
    });
    t.mdl.frmEl.pab_id.on('select2:select', function(e){
        var elm = e.params.data.element;
        $elm = jQuery(elm);
        $t = jQuery(this);
        $t.append($elm);
        $t.trigger('change.select2');
    });

    t.mdl.form_id = function () {
        t.mdl.frmEl.form_id.empty().append(new Option("Form Name", "", true, true));
        if (parseInt(t.mdl.frmEl.is_form_required.val(), 10) === 1) {
            $.each(t.config.form, function (i, v) {
                t.mdl.frmEl.form_id.append(new Option(v.form_name, v.id));
            });
        } else if (parseInt(t.mdl.frmEl.is_form_required.val(), 10) === 2) {
            $.each(t.config.custom_form, function (i, v) {
                t.mdl.frmEl.form_id.append(new Option(v.name, v.id));
            });
        }
    
        t.mdl.frmEl.form_id.trigger("change");
    };
    t.mdl.frmEl.is_form_required.on('change', function () { t.mdl.form_id(); });
    t.mdl.frmEl.form_id.select2($.extend({}, select2Opts, {dropdownParent: t.mdl.frmEl.form_id.parent(),data: t.config.form}));
    t.mdl.frmEl.is_form_required.select2($.extend({}, select2Opts,{dropdownParent: t.mdl.frmEl.is_form_required.parent()}));
    // t.mdl.frmEl.serviceTypeId.select2(select2Opts);
    //t.mdl.frmEl.priorityId.select2($.extend({}, select2Opts, {placeholder: "Select Priority"}));
    t.mdl.frmEl.ticketAttender.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.ticketAttender.parent(),
        ajax: {
            url: t.config.url.getTicketAttendersByDepartment,
            dataType: "json",
            data: function (p) {
                let companyId = t.mdl.frmEl.company_id.val();
                if (!companyId) {
                    return false;
                }
                return {
                    search: p.term,
                    page: p.page || 1,
                    department_id: t.mdl.frmEl.departmentId.val(),
                    company_id: companyId
                };
            },
            delay: 200
        },
        //minimumInputLength: 1,
        allowClear:true,
        placeholder: config.translations.Select_the_User,
        templateResult: function(data) {
            if (!data) return $("<div>No data</div>");
            var imgPaddingLeft = "24px";
            return t.config.userDropdownFormat(data, imgPaddingLeft);
        }   
     }));

    t.mdl.frmEl.autoAllocationGroup.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.autoAllocationGroup.parent(),
        ajax: {
            url: t.config.url.getAllocationGroups,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    department_id: t.mdl.frmEl.departmentId.val()
                };
            },
            delay: 200
        },
        allowClear:true,
        placeholder: 'Select auto allocation group'
    }));
    t.filters.filter_by_role.select2($.extend({}, select2Opts, {
        dropdownParent: t.filters.filter_by_role.parent(),
        ajax: {
            url: t.config.url.getRoles,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                };
            },
            delay: 200
        },
        placeholder: t.config.translations.filter_by_roles
    }));
    t.mdl.frmEl.role_id.select2($.extend({}, select2Opts, {
        dropdownParent: $('#TicketProblemTypeModal'),
        ajax: {
            url: t.config.url.getRoles,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                };
            },
            delay: 200
        },
        allowClear:true,
        placeholder: t.config.translations.select_role
    }));

    t.mdl.frmEl.custom_fieldset.select2($.extend({}, select2Opts, {
        dropdownParent:t.mdl.frmEl.custom_fieldset.parent(),
        ajax: {
            url: t.config.url.fetchFieldSet+"/2",
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 200
        },
        allowClear: true,
        placeholder: t.config.translations.select_fieldset,
    }));


    t.mdl.btn.clr.click(function() {
        t.mdl.frm.find(".amg-form-error-wrap").empty();
        t.mdl.frm.find("label.error").remove();
        t.mdl.frm.find(".error").removeClass("error");
        t.mdl.frm.find(".amg-form-invalid").removeClass("amg-form-invalid");
        t.mdl.frm.find(".amg-form-select-error").removeClass("amg-form-select-error");
        t.mdl.frmEl.name.val(null).trigger("change");
        t.mdl.frmEl.category_tag.val(null);
        t.mdl.frmEl.departmentId.val(0).trigger("change");
        t.mdl.frmEl.ticketAttender.val(0).trigger("change");
        t.mdl.frmEl.autoAllocationGroup.val(0).trigger("change");
        t.mdl.frmEl.priorityId.val(null).trigger("change");
        t.mdl.frmEl.form_id.val(null).trigger("change");
        t.mdl.frmEl.parentId.val(null).trigger("change");
        t.mdl.frmEl.tat.val(null).trigger("change");
        t.mdl.frmEl.close_ticket_after_days.val(null).trigger("change");
        t.mdl.frmEl.reopen_ticket_until_days.val(null).trigger("change");
        t.mdl.frmEl.response_sla.val(null).trigger("change");
        t.mdl.frmEl.workaround_sla.val(null).trigger("change");
        t.mdl.frmEl.remarks.summernote('reset');
        t.mdl.frmEl.is_form_required.val(0).trigger("change");
        t.mdl.frmEl.pab_id.val(null).trigger("change");
        t.mdl.frmEl.privilege_access.prop('checked', false).trigger("change");
        t.mdl.frmEl.number_of_days.val(null).trigger("change");
        t.mdl.frmEl.role_id.val(null).trigger("change");
        t.syncConditionalFields();
    });

    t.mdl.frmEl.remarks.summernote({
        inheritPlaceholder: true,
        placeholder: config.translations.comment_summer,
        toolbar: summernote_toolbar,
        icons: summernote_icons,
        styleTags: styleTags,
        minHeight: 120,
        focus: true,
        disableDragAndDrop: true,
        callbacks: {
            onInit: function () {
                $(this)
                    .next('.note-editor')
                    .addClass('amg-summernote-editor');

                if (typeof userOnInit === 'function') {
                    userOnInit.apply(this, arguments);
                }
                $('.note-style .dropdown-toggle').html(textResizeIcon);
            }
        }
    });
    t.prepareModalFieldLayout();

    t.formChanged = function() {
        t.mdl.form_id();
        t.syncConditionalFields();
    };

    t.resetFilterFields = function() {
        t.filters.priority.val(null).trigger("change");
        t.filters.department.val(null).trigger("change");
        t.filters.allocation_group.val(null).trigger("change");
        t.filters.authority_approval.val("").trigger("change");
        t.filters.pab.val(null).trigger("change");
        t.filters.form_name.val(null).trigger("change");
        t.filters.based_on_category.val("null").trigger("change");
        t.filters.filter_by_escalation.val("").trigger("change");
        t.filters.filter_by_escalation_for.val("").trigger("change");
        t.filters.filter_by_escalation_to.val(null).trigger("change");
        t.filters.filter_by_status.val("").trigger("change");
        t.filters.filter_by_role.val(null).trigger("change");
    };

    t.btnClrFilter = function(e) {
        if (e) {
            e.preventDefault();
        }

        t.resetFilterFields();
        t.cache_filter_values();
        $('#advanceFilterModal').modal('hide');
        t.reload();
    };

    t.handleEscalationForChange = function() {
        t.filters.filter_by_escalation_to.val(null).trigger("change");
    };

    if(t.mdl.frmEl.privilege_access.length) {
        t.mdl.frmEl.privilege_access.on("change", function() {
            t.syncConditionalFields();
        });
    }
    var select2Opts = { width: "100%" };
    var filterSelect2Opts = $.extend({}, select2Opts, { dropdownParent: t.filters.wrapper });
    t.filters.priority.select2($.extend({}, filterSelect2Opts, { placeholder: config.translations.Filter_By_Priority }));
    t.filters.authority_approval.select2($.extend({}, filterSelect2Opts, { placeholder: config.translations.filter_by_authority_approval }));
    t.filters.based_on_category.select2($.extend({}, filterSelect2Opts, { placeholder: config.translations.filter_by_category }));
    t.filters.filter_by_escalation.select2($.extend({}, filterSelect2Opts, { placeholder: config.translations.filter_by_escalation || "Filter by Escalation" }));
    t.filters.filter_by_escalation_for.select2($.extend({}, filterSelect2Opts, { placeholder: config.translations.filter_by_escalation_for }));
    t.filters.filter_by_escalation_to.select2($.extend({}, filterSelect2Opts, {
        ajax: {
            url: t.config.url.getescalate_to,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    id: t.filters.filter_by_escalation_for.val(),
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: config.translations.filter_by_escalation_to,
        templateResult: function(data) {
            if (!data) return $("<div>No data</div>");
            var imgPaddingLeft = "0px";
            return t.config.userDropdownFormat(data, imgPaddingLeft);
        }
    }));
     t.viewTask = function(e) {
        var ID = $(this).attr("data-id");
        window.open(config.url.getTaskList + '/' + ID, '_blank');
    }

    t.mdl.frmEl.company_id.select2($.extend({}, select2Opts, {
        dropdownParent:t.mdl.frmEl.company_id.parent(),
        ajax: {
            url: config.url.get_company_by_user_access,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 300
        },
        placeholder: "Select Company"
    })).on("change", function (e) {
        t.mdl.frmEl.departmentId.empty().trigger("change");
    });

    t.filters.filter_by_status.select2($.extend({}, filterSelect2Opts, { placeholder: config.translations.filter_by_status }));
    t.filters.fun.reload_priority();
    t.filters.fun.reload_department();
    t.filters.fun.reload_allocation_group();
    t.filters.fun.reload_pab();
    t.filters.fun.reload_formname();
    t.filters.btnFilter.on("click", $.proxy(t.applyFilters, t));
    t.filters.btnfilterclr.on("click", $.proxy(t.btnClrFilter, t));
    t.mdl.btn.submit.on("click", $.proxy(t.handleSubmit));
    t.mdl.frm.on("submit", $.proxy(t.handleSubmit));
    t.dTbl.ajax.reload();
    t.filters.trigger.on("click", $.proxy(t.openFilterModal, t));
    t.btn.reload.on("click", $.proxy(t.reload));
    t.btn.export.on("click", $.proxy(t.export));
    t.btn.add.on("click", $.proxy(t.addType));
    t.btn.import.on("click", $.proxy(t.import));
    t.page.on("click", ".dtActEdit", $.proxy(t.editType));
    t.page.on("click", ".dtActDel", $.proxy(t.deleteType));
    var menuCloseTimer;

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#detached-action-menu').length && !$(e.target).closest('.user-list-menu-toggle').length) {
            $('#detached-action-menu').remove();
            $('.user-list-menu-toggle').data('menu-open', false);
        }
    });

    $(document).on('mouseenter', '.user-list-menu-toggle, #detached-action-menu', function () {
        clearTimeout(menuCloseTimer);
    });

    $(document).on('mouseleave', '.user-list-menu-toggle, #detached-action-menu', function () {
        menuCloseTimer = setTimeout(function () {
            var btnHovered = $('.user-list-menu-toggle:hover').length;
            var menuHovered = $('#detached-action-menu:hover').length;
            if (!btnHovered && !menuHovered) {
                $('#detached-action-menu').remove();
                $('.user-list-menu-toggle').data('menu-open', false);
            }
        }, 150);
    });

    t.page.on('mouseenter', '.user-list-menu-toggle', function (e) {
        if ($('#detached-action-menu').length) {
            clearTimeout(menuCloseTimer);
            return;
        }

        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        var $btn = $(this);
        $('#detached-action-menu').remove();
        $('.user-list-menu-toggle').data('menu-open', false);

        var $menu = $btn.siblings('.dropdown-menu').clone(true);
        $menu.attr('id', 'detached-action-menu').addClass('show');
        $('body').append($menu);

        var btnRect = $btn[0].getBoundingClientRect();
        $menu.css({
            position: 'fixed',
            top: btnRect.bottom + 'px',
            left: btnRect.left + 'px',
            zIndex: 99999,
            display: 'block'
        });

        setTimeout(function () {
            var menuHeight = $menu.outerHeight();
            var menuWidth = $menu.outerWidth();
            var windowHeight = $(window).height();
            var windowWidth = $(window).width();

            if (btnRect.bottom + menuHeight > windowHeight) {
                $menu.css('top', (btnRect.top - menuHeight) + 'px');
            }
            if (btnRect.left + menuWidth > windowWidth) {
                $menu.css('left', (windowWidth - menuWidth - 10) + 'px');
            }
            if (parseFloat($menu.css('left')) < 0) {
                $menu.css('left', '10px');
            }
        }, 0);

        $btn.data('menu-open', true);
    });

    $(document).on('click', '#detached-action-menu .dropdown-item', function (e) {
        var $item = $(this);
        var id = $item.data('id') || $item.attr('id');
        var href = $item.attr('href');
        var target = $item.attr('target') || '_self';
        var isRealLink = href && href !== '#';

        e.preventDefault();
        e.stopPropagation();

        $('#detached-action-menu').remove();
        $('.user-list-menu-toggle').data('menu-open', false);

        if (isRealLink) {
            
            window.open(href, target, 'noopener');
            return;
        }

        var actionClass = null;
        ['cloneBtn'].forEach(function (cls) {
            if ($item.hasClass(cls)) actionClass = cls;
        });

        if (actionClass && id) {
            t.content.find('.' + actionClass + '[id="' + id + '"]').first().trigger('click');
        }
    });

    $(document).on('scroll', function () {
        $('#detached-action-menu').remove();
        $('.user-list-menu-toggle').data('menu-open', false);
    });
    $(window).off("resize.problemCategoryTable").on("resize.problemCategoryTable", function() {
        t.scheduleTableLayoutSync();
    });
    t.page.on("click", ".read-more", function(e) {
        var fullText;

        e.preventDefault();
        fullText = $(this).data("full-text");

        if (window.Swal && typeof Swal.fire === "function") {
            Swal.fire({
                title: config.translations.comment_summer || "Notes",
                html: fullText,
                width: 700
            });
        } else {
            alert($("<div>").html(fullText).text());
        }
    });
    // t.searchInput.on("input", $.proxy(t.handleSearchInput));//no need to search records on keyup.
    t.searchInput.on("keydown", function(e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            t.cache_filter_values();
            t.reload();
        }
    });
    t.pageLength.on("change", function() {
        if (t.dTbl) {
            t.dTbl.page.len(parseInt($(this).val(), 10) || 10).draw(false);
        }
    });
    t.mdl.frmEl.approval_required.on("change", $.proxy(t.approvalChanged)).trigger("change");
    t.mdl.frmEl.departmentId.on("change", $.proxy(t.onDepartmentChange));
    t.mdl.frmEl.is_form_required.on("change", $.proxy(t.formChanged)).trigger("change");
    t.syncConditionalFields();
    t.filters.filter_by_escalation_for.on("change", $.proxy(t.handleEscalationForChange, t));
    t.page.on('click','.view-task', $.proxy(t.viewTask));
    /*$("input[name='pab_id']").on('change', function() {
        $(this).valid();
    });*/
};

var EsclationClass = function(config) {
    var t = this;
    t.config = config;
    t.page = $(".pbmg-list-wrapper");
    t.table = t.page.find("#esclations");
    t.tbody = t.table.find("tbody");
    t.mdl = t.page.find("#EsclationModal");
    t.mdl.title = t.mdl.find(".modal-title");
    t.mdl.pbm_cat_name = t.mdl.find("#pbm_cat_name");
    t.mdl.history_link = t.mdl.find("#history_link");

    t.mdl.frm = t.mdl.find("#frm_esclation");
    t.mdl.frmEl = {};
    t.mdl.frmEl.esclate_to = t.mdl.frm.find('#esclate_to');
    t.mdl.frmEl.esclate_need_at = t.mdl.frm.find('#esclate_need_at');
    t.mdl.frmEl.sla_count = t.mdl.frm.find('#sla_count');
    t.mdl.frmEl.escalate_group = t.mdl.frm.find('#esclate_group');
    t.mdl.frmEl.escalate_to_group = t.mdl.frm.find('.escalate_to_group');
    t.mdl.frmEl.escalate_to_user = t.mdl.frm.find('.escalate_to_user');
    t.mdl.frmEl.groups = t.mdl.frm.find('#groups');
    t.mdl.frmEl.users = t.mdl.frm.find('#users');
    t.mdl.frmEl.technician_mark_cc = t.mdl.frm.find('#technician_mark_cc');

    t.mdl.btn = {};
    t.mdl.btn.btnSubmit = t.mdl.find('#btnEsclationSubmit');
    t.mdl.btn.btnClear = t.mdl.find('#btnEsclationClear');

    t.prob_cat_id = null;
    t.escl_id = null;
    t.data = {};
    t.httpCall = true;

    t.getEscalateForValue = function() {
        return t.mdl.frm.find("input[name='escalate_for']:checked").val() || "users";
    };

    t.isEscalatingToGroup = function() {
        return t.getEscalateForValue() === "groups";
    };

    t.destroySelect2 = function(element) {
        if (element && element.length && element.hasClass("select2-hidden-accessible")) {
            element.select2("destroy");
        }
    };

    t.updateValidationState = function (element, hasError) {
        var group = element.closest(".input-group");
        var isSelect2 = element.hasClass("select2-hidden-accessible");
        if (group.length) {
            group.toggleClass("amg-form-invalid", !!hasError);
        }
        if (isSelect2) {
            element.next(".select2-container")
                .find(".select2-selection")
                .toggleClass("amg-form-select-error", !!hasError);
        }
    };

    t.prepareModalFieldLayout = function() {
        if (!t.mdl || !t.mdl.frm || !t.mdl.frm.length) return;

        t.mdl.frm.find(".input-group").each(function() {
            $(this).closest(".amg-form-field").addClass("amg-form-field-row");
        });
    };

    t.getModalErrorWrap = function(element) {
        var row = element.closest(".amg-form-field-row");
        var wrap;

        if (!row.length) {
            return $();
        }

        wrap = row.children(".amg-form-error-wrap");
        if (!wrap.length) {
            wrap = $('<div class="amg-form-error-wrap"></div>');
            row.append(wrap);
        }

        return wrap;
    };

    t.clearFieldValidation = function(element) {
        if (!element || !element.length) {
            return;
        }

        t.updateValidationState(element, false);
        t.getModalErrorWrap(element).empty();
    };

    t.frmValidator = t.mdl.frm.validate({
        onsubmit: false,
        ignore: ":hidden:not(.select2-hidden-accessible)",
        rules: {
            esclate_group: {
                required: {
                    depends: function() {
                        return t.isEscalatingToGroup();
                    }
                }
            },
            esclate_to: {
                required: {
                    depends: function() {
                        return !t.isEscalatingToGroup();
                    }
                }
            },
            esclate_need_at: {
                required: true,
                digits: true,
                min: 1,
                max: 1000
            },
            sla_count: {
                required: true
            },
        },
        messages: {
            esclate_group: {
                required: "Escalate To Group field is required"
            },
            esclate_to: {
                required: "Escalate To User field is required"
            },
            esclate_need_at: {
                required: "Escalate Trigger Time field is required",
                digits: "Escalate Trigger Time must be a number",
                min: "Escalate Trigger Time must be at least 1 hour",
                max: "Escalate Trigger Time cannot exceed 1000 hours"
            },
            sla_count: {
                required: "SLA Count field is required"
            }
        },
        errorPlacement: function (error, element) {
            var errorWrap = t.getModalErrorWrap(element);

            if (errorWrap.length) {
                error.appendTo(errorWrap);
            } else if (element.closest(".input-group").length) {
                error.insertAfter(element.closest(".input-group"));
            } else {
                error.insertAfter(element);
            }
            t.updateValidationState(element, true);
        },
        highlight: function (element) {
            t.updateValidationState($(element), true);
        },
        unhighlight: function (element) {
            t.updateValidationState($(element), false);
        }
    });

    t.prepareModalFieldLayout();

    t.showTable = function() {
        t.tbody.empty();
        try {
            if( typeof t.data.esclations != undefined && t.data.esclations instanceof Array && t.data.esclations.length > 0 ) {
                $.each(t.data.esclations, function(i,r) {
                    var sla_count =  (r.sla_count == 1) ? 'Yes' : 'No';
                    var technician_mark_cc = (r.technician_mark_cc == 1) ? 'Yes' : 'No';
                    var user_or_group = r.user_fname != "" ? r.user_fname : r.escalation_group;
                    var email = r.email != null ? r.email : "";
                    var displayName = user_or_group;
                    if (user_or_group && user_or_group.length > 10) {
                        displayName = user_or_group.substring(0, 10) + "...";
                    }
                    var displayEmail = email;
                    if (email && email.length > 10) {
                        displayEmail = email.substring(0, 10) + "...";
                    }
                    var row = "<tr>";
                    // row += "<td>" + r.esclate_stage_no + "</td>";
                    row += "<td><div " + (displayName.includes('...') ? "data-bs-toggle='tooltip' title='" + t.escapeHtml(user_or_group) + "'" : "") + ">" + displayName + "</div><div " + (displayEmail.includes('...') ? "data-bs-toggle='tooltip' title='" + t.escapeHtml(email) + "'" : "") + ">" + displayEmail + "</div>";
                   if (jQuery.inArray("ProblemCategoriesEsclationEdit", t.config.permissions) !== -1) {
                        row += `
                            <a href="javascript:void(0)" data-id="${r.id}" class="escl-edit btn-link mar-rgt mt-2" title="Edit" data-bs-toggle='tooltip'>
                               <i class="bi bi-pencil-square"></i>
                            </a>
                        `;
                    }

                    if (jQuery.inArray("ProblemCategoriesEsclationDelete", t.config.permissions) !== -1) {
                        row += `
                            <a href="javascript:void(0)" data-id="${r.id}" class="escl-del btn-link ms-2 mt-2" title="Delete" data-bs-toggle='tooltip'>
                               <i class="bi bi-trash3"></i>
                            </a>
                        `;
                    }
                    row += "<td>" + r.esclate_need_at + "</td>";
                    row += "<td>" + sla_count + "</td>";
                    row += "<td>" + technician_mark_cc + "</td>";
                    row += "</tr>";
                    t.tbody.append(row);
                    t.initTooltips();
                });
            }
            else {
                t.tbody.append("<tr><td colspan='4' class='text-center'>No Esclation configured</td></tr>");
            }
        }
        catch(e) {
            console.log(e);
        }
    };

    t.initTooltips = function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    }

    t.loadForEdit = function(e) {
        try {
            t.clearFrm();
            t.escl_id = parseInt($(this).attr('data-id'));
            if(t.escl_id < 0) {
                throw Error("Unable to load the esclation to edit.");
            }

            var esclation = null;
            $.each(t.data.esclations, function(i,r) {
                // console.log(i, " - ", r);
                if( r.id == t.escl_id ) {
                    esclation = r;
                    return false;
                }
            });

            if(esclation.user_fname == "") {
                t.mdl.frmEl.escalate_to_user.addClass("hide");
                t.mdl.frmEl.escalate_to_group.removeClass("hide");
                t.mdl.frmEl.groups.prop("checked",true);
                t.mdl.frmEl.escalate_group.empty();
                t.mdl.frmEl.escalate_group.append(new Option(esclation.escalation_group, esclation.escalation_group_id, true, true));
            } else {
                t.mdl.frmEl.escalate_to_user.removeClass("hide");
                t.mdl.frmEl.escalate_to_group.addClass("hide");
                t.mdl.frmEl.users.prop("checked",true);
                t.mdl.frmEl.esclate_to.empty();
                t.mdl.frmEl.esclate_to.append(new Option(esclation.user_fname + (esclation.email ? ' - ' + esclation.email : ''),esclation.esclate_to, true, true));
            }

            // t.mdl.frmEl.esclate_to.append(new Option(esclation.user_fname + ' - ' + esclation.email, esclation.esclate_to, true, true));
            t.mdl.frmEl.esclate_need_at.val(esclation.esclate_need_at);
            t.mdl.frmEl.sla_count.val(esclation.sla_count).trigger('change');
            t.mdl.frmEl.technician_mark_cc.val(esclation.technician_mark_cc).trigger('change');
        }
        catch(e) {
            alert(e);
        }
    };

    t.loadEsclation = function() {
        t.clearFrm();
        try {
            t.prob_cat_id = parseInt($(this).attr('data-id'));
            if(t.prob_cat_id < 0) {
                throw Error("Unable to load the esclations of choosen problem category.");
            }

            t.destroySelect2(t.mdl.frmEl.esclate_to);
            t.destroySelect2(t.mdl.frmEl.escalate_group);
            t.destroySelect2(t.mdl.frmEl.sla_count);

            var select2Opts = { width:"100%"};
            t.mdl.frmEl.esclate_to.select2($.extend({}, select2Opts, {
                dropdownParent: t.mdl.frmEl.esclate_to.parent(),
                ajax: {
                    url: t.config.url.get_esclatable_users + '/' + t.prob_cat_id,
                    dataType: "json",
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                        };
                    },
                    delay: 300
                },
                allowClear: true,
                placeholder: config.translations.Select_the_User,
                templateResult: function(data) {
                    if (!data) return $("<div>No data</div>");
                    var imgPaddingLeft = "24px";
                    return t.config.userDropdownFormat(data, imgPaddingLeft);
                }
            }));
            t.mdl.frmEl.escalate_group.select2($.extend({}, select2Opts, {
                dropdownParent: t.mdl.frmEl.escalate_group.parent(),
                ajax: {
                    url: t.config.url.get_esclatable_group + '/'+ t.prob_cat_id,
                    dataType: "json",
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                        };
                    },
                    delay: 200
                },
                allowClear: true,
                placeholder: "Select group",
            }));
            t.mdl.frmEl.sla_count.select2($.extend({}, select2Opts, {dropdownParent: t.mdl.frmEl.sla_count.parent(),allowClear: true, placeholder: config.translations.sla_count}));

            $.get(t.config.url.get_esclations_info + '/' + t.prob_cat_id).then(function(resp) {
                console.log(resp);
                if(resp.status == "success") {
                    t.data.pbm_cat = resp.pbm_cat;
                    t.data.esclations = resp.esclations;
                    t.mdl.pbm_cat_name.text(t.data.pbm_cat.name);
                    t.mdl.history_link.attr('href', t.config.url.base_url +'/tickets/problem-categories/esclation-history/' + t.data.pbm_cat.id);
                    t.showTable();
                    t.mdl.frmEl.escalate_to_user.removeClass("hide");
                    t.mdl.frmEl.escalate_to_group.addClass("hide");
                    t.mdl.modal("show");
                }
            })
            .fail(function(e) {
                throw Error("Please refresh page and try again" + e);
            });
        }
        catch(e) {
            alert(e);
        }
    };

    t.clearFrm = function() {
        t.mdl.frm[0].reset();
        t.frmValidator.resetForm();
        t.mdl.frmEl.users.prop("checked", true);
        t.mdl.frmEl.esclate_to.empty().val("").trigger('change');
        t.mdl.frmEl.escalate_group.empty().val("").trigger("change");
        t.mdl.frmEl.sla_count.val("").trigger('change');
        t.mdl.frmEl.technician_mark_cc.val("").trigger('change');
        t.mdl.frmEl.esclate_to.val("").trigger('change');
        t.mdl.frmEl.esclate_need_at.val("");
        t.escl_id = null;
        t.mdl.frmEl.escalate_to_user.removeClass("hide");
        t.mdl.frmEl.escalate_to_group.addClass("hide");
        t.clearFieldValidation(t.mdl.frmEl.esclate_to);
        t.clearFieldValidation(t.mdl.frmEl.escalate_group);
        t.clearFieldValidation(t.mdl.frmEl.esclate_need_at);
        t.clearFieldValidation(t.mdl.frmEl.sla_count);
        t.clearFieldValidation(t.mdl.frmEl.technician_mark_cc);
    };

    t.deleteEsclation = function(e) {//sweetalert
        e.preventDefault();
        try{
            t.escl_id = parseInt($(this).attr('data-id'));

            if(t.escl_id == "" || t.escl_id < 1) {
                throw Error("Unable to delete the esclation. Please refresh page and try again");
            }

            Swal.fire({
                title: config.translations.are_you_delete_problemcategory,
                icon: 'warning',
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.get(t.config.url.del_esclation + '/' + t.escl_id).then(function(resp) {
                        t.escl_id = null;
                        if(resp.status == "success") {
                            if( typeof resp.esclations != undefined && resp.esclations instanceof Array ) {
                                t.data.esclations = resp.esclations;
                            }
                        }

                        t.showTable();
                        if( resp.msg != "" ) {
                            sweetAlert('center', 'success', resp);
                        }
                    })
                    .fail(function(e) {
                        throw Error("Please refresh page and try again" + e);
                    });
                }
            });
        }
        catch(e) {
            alert(e);
        }
    }

    t.handleSubmit = function(e) {
        if (e && typeof e.preventDefault === "function") {
            e.preventDefault();
        }

        if( t.frmValidator.form() == false ) {
            return false;
        }

        if(t.httpCall != true) {
            return false;
        }
        t.httpCall = false;

        var path = t.escl_id != null ? t.config.url.update_esclation + '/' + t.escl_id : t.config.url.add_esclation + '/' + t.prob_cat_id;

        var formData = new FormData(t.mdl.frm[0]);
        formData.append("problem_category_id", t.prob_cat_id);
        formData.append('_token', config.token);

        var http = $.ajax({
            url: path,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData,
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.clearFrm();
                    if( typeof data.esclations != undefined && data.esclations instanceof Array ) {
                        t.data.esclations = data.esclations;
                    }
                    sweetAlert('center', 'success', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
                } else {
                    sweetAlert('center', 'error', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                }

                t.showTable();
            }
        });
        http.fail(function () {
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
            // alert("Something went wrong. Please check given details are correct");
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    t.escalateFor = function(e) {
        var val = $(this).val();
        if(val == "groups") {
            t.mdl.frmEl.escalate_to_user.addClass("hide");
            t.mdl.frmEl.escalate_to_group.removeClass("hide");
            t.mdl.frmEl.groups.prop("checked",true);
            t.mdl.frmEl.esclate_to.val("").trigger("change");
            t.clearFieldValidation(t.mdl.frmEl.esclate_to);
            t.mdl.frmEl.escalate_group.valid();
        } else {
            t.mdl.frmEl.users.prop("checked", true);
            t.mdl.frmEl.escalate_to_user.removeClass("hide");
            t.mdl.frmEl.escalate_to_group.addClass("hide");
            t.mdl.frmEl.escalate_group.val("").trigger("change");
            t.clearFieldValidation(t.mdl.frmEl.escalate_group);
            t.mdl.frmEl.esclate_to.valid();
        }
    };

    t.mdl.frmEl.technician_mark_cc.select2($.extend({}, {
        dropdownParent: t.mdl.frmEl.technician_mark_cc.parent(),
        width:"100%",
        placeholder:"Select"
    }));

    t.mdl.frmEl.esclate_to.on("change", function() {
        $(this).valid();
    });
    t.mdl.frmEl.escalate_group.on("change", function() {
        $(this).valid();
    });
    t.mdl.frmEl.sla_count.on("change", function() {
        $(this).valid();
    });
    t.mdl.frmEl.esclate_need_at.on("input blur", function() {
        $(this).valid();
    });

    t.clearEscalationFrm =  function(e){
        console.log('clear form called')
        t.clearFrm();
        t.mdl.modal('hide');
    }

    t.escapeHtml = function (value) {
        return String(value === null || value === undefined ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    };

    $(document).on('click',".dtActEscl", $.proxy(t.loadEsclation));
    t.tbody.on("click", ".escl-edit", $.proxy(t.loadForEdit));
    t.tbody.on("click", ".escl-del", $.proxy(t.deleteEsclation));
    t.mdl.frm.on("submit", $.proxy(t.handleSubmit));
    t.mdl.btn.btnClear.on("click", $.proxy(t.clearEscalationFrm));
    t.page.on("click",'.escalate_for', $.proxy(t.escalateFor));
};

var MyApp = function(config) {
    var t = this;
    t.config = config;
    t.config.myApp = this;
    t.page = $("#page-content");
    t.table = t.page.find("#mytable");
    new ProblemCategory(t.config);
    new EsclationClass(t.config);

    t.config.userDropdownFormat = function (s, imgPaddingLeft) {
        var self = t;
        if (s && typeof s.loading !== "undefined" && s.loading) {return $("<div>" + s.text + "</div>");}
        var name  = self.safeDisplayValue(s.text, "-");
        var email = self.safeDisplayValue(s.email, "");
        var empNo = self.safeDisplayValue(s.employee_num, "");
        var imageUrl = self.safeDisplayValue(s.img_path, "");
        var avatarHtml = self.getAvatarHtml(name, imageUrl, "user-list-avatar");
        var html = [
            '<div class="d-flex align-items-start gap-3 w-100">',
                avatarHtml,
                '<div class="d-flex flex-column w-100 gap-1 min-w-0">',
                    '<div class="d-flex align-items-center gap-2">',
                        '<span class="b2-text">' + self.escapeHtml(name) + '</span>',
                        s.status == 1 ? '<span class="active-user"></span>' : '<span class="inactive-user"></span>',
                    '</div>',
                    email ? '<span class="b1-text opacity-60"><i class="bi bi-envelope me-1"></i>' + self.escapeHtml(email) + '</span>' : "",
                    empNo ? '<span class="b1-text opacity-60"><i class="bi bi-credit-card me-1"></i>' + self.escapeHtml(empNo) + '</span>' : "",
                '</div>',
            '</div>'
        ].join("");

        return $(html);
    };
};
MyApp.prototype.initTooltips = function () {
    $('[data-bs-toggle="tooltip"]').tooltip();
}

MyApp.prototype.isFilledValue = function (value) {
    if (value === null || value === undefined) return false;
    var text = String(value).trim();
    return text !== "" && text !== "0" && text.toLowerCase() !== "null" && text.toLowerCase() !== "undefined";
};

MyApp.prototype.safeDisplayValue = function (value, fallback) {
    return this.isFilledValue(value) ? String(value).trim() : (fallback !== undefined ? fallback : "-");
};

MyApp.prototype.getAvatarHtml = function (name, imageUrl, className) {
    if (imageUrl) {
        return '<img src="' + this.escapeHtml(imageUrl) + '" class="' + className + '" />';
    }

    var initials = "";
    if (name) {
        var parts = name.trim().split(" ");
        var first = parts[0] ? parts[0][0] : "";
        var last  = parts.length > 1 ? parts[parts.length - 1][0] : "";
        initials = (first + last).toUpperCase();
    }

    return '<div class="' + className + ' d-flex align-items-center justify-content-center avatar-fallback">'
        + initials +
        '</div>';
};
MyApp.prototype.escapeHtml = function (value) {
    return String(value === null || value === undefined ? "" : value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
};
