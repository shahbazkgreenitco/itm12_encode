/* ── Tab switching ─────────────────────────────────── */
function tcfSwitchTab(id, btn) {
    document.querySelectorAll('.tcf-tab').forEach(function(t)   { t.classList.remove('active'); });
    document.querySelectorAll('.tcf-panel').forEach(function(p) { p.classList.remove('active'); });
    btn.classList.add('active');
    var panel = document.getElementById('tab-' + id);
    if (panel) panel.classList.add('active');
}

/* ── Accordion toggle (smooth slide) ──────────────── */
function tcfToggleAccordion(head) {
    head.classList.toggle('open');
    var body = head.nextElementSibling;
    if (!body) return;
    if (head.classList.contains('open')) {
        body.style.display   = '';
        body.style.overflow  = 'hidden';
        body.style.maxHeight = '0';
        body.style.transition= 'max-height .25s ease';
        requestAnimationFrame(function() {
            body.style.maxHeight = body.scrollHeight + 'px';
            body.addEventListener('transitionend', function h() {
                body.style.maxHeight = 'none';
                body.style.overflow  = '';
                body.style.transition= '';
                body.removeEventListener('transitionend', h);
            });
        });
    } else {
        body.style.overflow  = 'hidden';
        body.style.maxHeight = body.scrollHeight + 'px';
        body.style.transition= 'max-height .25s ease';
        requestAnimationFrame(function() {
            body.style.maxHeight = '0';
            body.addEventListener('transitionend', function h() {
                body.style.display   = 'none';
                body.style.transition= '';
                body.removeEventListener('transitionend', h);
            });
        });
    }
}

/* ════════════════════════════════════════════════════
   SELECT ALL — Departments Table
   ════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function() {

    var masterCb  = document.getElementById('deptSelectAll');
    var masterWrap = document.getElementById('deptSelectAllWrap');

    function getRowChecks() {
        return document.querySelectorAll('#deptTableBody .dept-row-check');
    }

    function syncMaster() {
        var rows    = getRowChecks();
        var checked = Array.from(rows).filter(function(c) { return c.checked; });
        if (!masterCb) return;
        masterCb.checked       = checked.length === rows.length && rows.length > 0;
        masterCb.indeterminate = checked.length > 0 && checked.length < rows.length;
        if (masterWrap) {
            masterWrap.classList.toggle('all-selected', masterCb.checked);
        }
    }

    /* Master → all rows */
    if (masterCb) {
        masterCb.addEventListener('change', function() {
            var rows = getRowChecks();
            rows.forEach(function(cb) {
                cb.checked = masterCb.checked;
                var tr = cb.closest('.dept-row');
                if (tr) tr.classList.toggle('row-selected', masterCb.checked);
            });
            if (masterWrap) {
                masterWrap.classList.toggle('all-selected', masterCb.checked);
            }
        });
    }

    /* Row → sync master */
    document.querySelectorAll('#deptTableBody .dept-row-check').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var tr = cb.closest('.dept-row');
            if (tr) tr.classList.toggle('row-selected', cb.checked);
            syncMaster();
        });
    });

    /* Toggle action text update */
    document.querySelectorAll('#deptTableBody .dept-row-toggle input').forEach(function(tog) {
        tog.addEventListener('change', function() {
            var label = this.closest('td').querySelector('.dept-toggle-label');
            if (label) label.textContent = this.checked ? 'Disable' : 'Enable';
        });
    });

    /* ════════════════════════════════════════════════════
       SELECT ALL — Report Fields
       ════════════════════════════════════════════════════ */
    var reportMaster = document.getElementById('selectAllFields');
    if (reportMaster) {
        reportMaster.addEventListener('change', function() {
            document.querySelectorAll('.field-check input[type="checkbox"]').forEach(function(cb) {
                cb.checked = reportMaster.checked;
            });
        });
        document.querySelectorAll('.field-check input[type="checkbox"]').forEach(function(cb) {
            cb.addEventListener('change', function() {
                var all     = document.querySelectorAll('.field-check input[type="checkbox"]');
                var chk     = document.querySelectorAll('.field-check input[type="checkbox"]:checked');
                reportMaster.indeterminate = chk.length > 0 && chk.length < all.length;
                reportMaster.checked       = chk.length === all.length;
            });
        });
    }

    /* ════════════════════════════════════════════════════
       SELECT ALL — Permissions (Handler panel)
       ════════════════════════════════════════════════════ */
    var permMaster = document.querySelector('.tcf-user-detail .tcf-check-item input[type="checkbox"]');
    if (permMaster) {
        permMaster.addEventListener('change', function() {
            document.querySelectorAll('.tcf-perm-item input[type="checkbox"]').forEach(function(cb) {
                cb.checked = permMaster.checked;
            });
        });
        document.querySelectorAll('.tcf-perm-item input[type="checkbox"]').forEach(function(cb) {
            cb.addEventListener('change', function() {
                var all = document.querySelectorAll('.tcf-perm-item input[type="checkbox"]');
                var chk = document.querySelectorAll('.tcf-perm-item input[type="checkbox"]:checked');
                permMaster.indeterminate = chk.length > 0 && chk.length < all.length;
                permMaster.checked       = chk.length === all.length;
            });
        });
    }

    /* ── Department search ────────────────────────── */
    window.deptSearch = function(q) {
        q = q.toLowerCase();
        document.querySelectorAll('#deptTableBody .dept-row').forEach(function(tr) {
            var name = tr.querySelector('td:nth-child(2)')?.textContent?.toLowerCase() || '';
            tr.style.display = (!q || name.includes(q)) ? '' : 'none';
        });
    };

    /* ── User selection ───────────────────────────── */
    window.tcfSelectUser = function(el, name) {
        document.querySelectorAll('.tcf-user-item').forEach(function(i) { i.classList.remove('active'); });
        el.classList.add('active');
        var su = document.querySelector('.tcf-selected-user');
        if (su) su.textContent = 'Selected User: ' + name.replace(/\s+/g, '.');
    };
});