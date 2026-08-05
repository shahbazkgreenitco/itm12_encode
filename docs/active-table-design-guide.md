# Active Table Design Guide

This guide documents the current user list table design so another developer can build the same table UI in another page or modal.

## Source Of Truth

The current implementation is split across these files:

- `resources/views/users/index.blade.php`
- `resources/css/custom.css`
- `public/js/users/newlist.js`

## Goal

Reuse the same table design by keeping the same:

- wrapper structure
- toolbar structure
- DataTable shell
- column class names
- cell HTML patterns
- CSS token scope

When building a new table, change only:

- table `id`
- column labels
- row data
- URLs / ajax source
- action handlers

## Important Scope Note

The current CSS is scoped to:

```html
#main-user-list-wrapper
```

That means the same design only applies automatically inside that wrapper.

If another developer wants the same table in another page or modal, use one of these options:

1. Reuse `id="main-user-list-wrapper"` only if there will be a single instance on the page.
2. Preferred: convert the CSS selector scope from `#main-user-list-wrapper` to a reusable class such as `.user-list-theme`.

For shared reuse, the second option is cleaner.

## Main Design Tokens

These variables control the table look in `resources/css/custom.css`:

```css
#main-user-list-wrapper {
    --user-list-surface: #ffffff;
    --user-list-panel: #ffffff;
    --user-list-header-bg: #eff2fa;
    --user-list-border: #e3e9f3;
    --user-list-row-border: #edf1f7;
    --user-list-text: #44506a;
    --user-list-heading: #131927;
    --user-list-muted: #7b879c;
    --user-list-link: #131927;
    --user-list-avatar-bg: #eef2f7;
    --user-list-avatar-text: #41516b;
    --user-list-role-bg: #eef2fa;
    --user-list-role-text: #52627d;
    --user-list-role-admin-bg: #e9f0ff;
    --user-list-role-admin-text: #3057d5;
    --user-list-role-user-bg: #e8f8ee;
    --user-list-role-user-text: #16794c;
    --user-list-role-super-admin-bg: #f2e5ff;
    --user-list-role-super-admin-text: #6a26db;
    --user-list-status-active: #186b43;
    --user-list-status-active-ring: rgba(24, 107, 67, 0.18);
    --user-list-status-inactive: #f12f35;
    --user-list-status-inactive-ring: rgba(241, 47, 53, 0.18);
    --user-list-action-bg: #ffffff;
    --user-list-action-border: #dbe2ef;
    --user-list-action-hover: #eff2fa;
    --user-list-action-text: #52627d;
    --user-list-delete-border: #ffd7d9;
    --user-list-delete-hover: #fff1f1;
    --user-list-control-bg: #ffffff;
    --user-list-control-border: #dbe2ef;
    --user-list-control-text: #52627d;
    --user-list-control-muted: #7b879c;
    --user-list-control-shadow: 0 8px 20px rgba(19, 25, 39, 0.08);
    --user-list-view-active-bg: #ffffff;
    --user-list-view-active-text: #131927;
}
```

There is also a dark theme override in `resources/css/custom.css`.

## Toolbar Structure

Use this pattern for the toolbar above the table:

```html
<div class="users-list-toolbar d-flex flex-wrap align-items-center justify-content-between gap-3">
    <div class="users-list-toolbar-left d-flex flex-wrap align-items-center gap-3">
        <label class="user-list-length-control mb-0" for="user-list-page-length">
            <span class="user-list-length-text">Show</span>
            <select id="user-list-page-length" class="user-list-length-select js-user-page-length" aria-label="Rows per page">
                <option value="10" selected>(10)</option>
                <option value="15">(15)</option>
                <option value="25">(25)</option>
                <option value="50">(50)</option>
            </select>
        </label>

        <div class="user-list-view-switch" role="tablist" aria-label="User list view switch">
            <button type="button" class="user-list-view-btn is-active js-user-view-toggle" data-view="list" aria-pressed="true">
                List View
            </button>
            <button type="button" class="user-list-view-btn js-user-view-toggle" data-view="card" aria-pressed="false">
                Card View
            </button>
        </div>
    </div>
</div>
```

## Table Shell

Use this structure for the main table:

```html
<div class="js-user-list-view-panel">
    <div class="table-responsive">
        <table id="default_order" class="table display">
            <thead>
                <tr>
                    <th><input class="form-check-input mt-3" type="checkbox" id="select-all"></th>
                    <th><h4>User Info</h4></th>
                    <th><h4>Company</h4></th>
                    <th><h4>Job Type</h4></th>
                    <th><h4>Contact Details</h4></th>
                    <th><h4>Manager</h4></th>
                    <th><h4>Location</h4></th>
                    <th><h4>Status</h4></th>
                    <th><h4>Updated On</h4></th>
                    <th><h4>Actions</h4></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
```

## DataTable Setup Pattern

The current table is a server-side DataTable with:

- `processing: true`
- `serverSide: true`
- `scrollX: true`
- `autoWidth: false`
- `lengthChange: false`
- `fixedColumns.leftColumns: 1`
- `fixedColumns.rightColumns: 1`

## Column Class Mapping

Keep these column class names to preserve the same spacing and width behavior:

| Column | Class |
| --- | --- |
| checkbox | `users-col-checkbox` |
| user info | `users-col-user-info` |
| company | `users-col-company` |
| job type | `users-col-job-type` |
| contact | `users-col-contact` |
| manager | `users-col-manager` |
| location | `users-col-location` |
| status | `users-col-status` |
| updated on | `users-col-updated-on` |
| actions | `users-col-actions` |

## Reusable Cell Patterns

If a developer is not using the same JS renderers, they should still output the same inner HTML structure.

### Checkbox Cell

```html
<label class="user-list-checkbox-wrap">
    <input type="checkbox" class="row-checkbox form-check-input" value="15">
</label>
```

### User Info Cell

```html
<div class="user-list-person">
    <img src="/path/avatar.jpg" alt="John Doe" class="user-list-avatar">
    <div class="user-list-copy">
        <a class="user-list-link" href="/user/info/15" target="_blank" rel="noopener noreferrer">John Doe</a>
        <span class="user-list-meta">john.doe</span>
    </div>
</div>
```

If there is no profile image:

```html
<span class="user-list-avatar user-list-avatar-fallback" aria-hidden="true">JD</span>
```

### Text-Only Cell

Use this for simple one-line columns like company or location:

```html
<div class="user-list-text-block user-list-text-block--strong">OpenAI</div>
```

or:

```html
<div class="user-list-text-block">Seoul</div>
```

### Stack Cell

Use this when the cell has two visual lines:

```html
<div class="user-list-stack">
    <span class="user-list-text-block user-list-text-block--strong">Company Staff</span>
    <span class="user-list-role-pill is-admin">Administrator</span>
</div>
```

### Contact Cell

```html
<div class="user-list-stack">
    <span class="user-list-text-block">john@example.com</span>
    <span class="user-list-meta">+82 10 1234 5678</span>
</div>
```

### Manager Cell

```html
<div class="user-list-person user-list-person--compact">
    <span class="user-list-avatar user-list-avatar--sm user-list-avatar-fallback" aria-hidden="true">AM</span>
    <div class="user-list-copy">
        <span class="user-list-text-block user-list-text-block--strong">Alex Manager</span>
    </div>
</div>
```

### Role Pill

Use one of these tone classes:

- `is-super-admin`
- `is-admin`
- `is-user`
- default: no extra tone class

Example:

```html
<span class="user-list-role-pill is-super-admin">Super Admin</span>
```

### Status Cell

Recommended reusable HTML:

```html
<span class="user-list-status is-active">
    <span class="user-list-status-dot"></span>
    <span class="user-list-status-label">Active</span>
</span>
```

Inactive version:

```html
<span class="user-list-status is-inactive">
    <span class="user-list-status-dot"></span>
    <span class="user-list-status-label">Inactive</span>
</span>
```

### Updated On Cell

```html
<div class="user-list-stack">
    <span class="user-list-text-block user-list-text-block--strong">2026-03-26</span>
    <span class="user-list-meta">10:45 AM</span>
</div>
```

### Empty Value

```html
<span class="user-list-empty">-</span>
```

### Actions Cell

Quick action buttons:

```html
<div class="user-list-actions">
    <button type="button" class="user-list-action-btn dtActEdit" data-id="15" title="Edit User" aria-label="Edit User">
        <!-- svg -->
    </button>

    <button type="button" class="user-list-action-btn is-delete dtActDel" data-id="15" title="Delete User" aria-label="Delete User">
        <!-- svg -->
    </button>

    <div class="dropdown">
        <button type="button" class="action-link user-list-menu-toggle" id="user-table-action-dropdown-15" data-bs-toggle="dropdown" aria-expanded="false" aria-label="More actions">
            <!-- svg -->
        </button>
        <ul class="dropdown-menu action-dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="user-table-action-dropdown-15">
            <li><a class="dropdown-item dtActClone" href="#" data-id="15"><span class="b3-text">Clone User</span></a></li>
            <li><a class="dropdown-item dtActView" href="#" data-id="15"><span class="b3-text">View Details</span></a></li>
        </ul>
    </div>
</div>
```

## Main CSS Classes

These classes are the core of the same table design:

| Class | Purpose |
| --- | --- |
| `users-list-toolbar` | toolbar wrapper above table |
| `users-list-toolbar-left` | left-side toolbar group |
| `user-list-length-control` | page-length control shell |
| `user-list-length-text` | �Show� label |
| `user-list-length-select` | page-length select |
| `user-list-view-switch` | list/card toggle shell |
| `user-list-view-btn` | toggle button |
| `user-list-checkbox-wrap` | checkbox alignment wrapper |
| `user-list-person` | avatar + text layout |
| `user-list-person--compact` | smaller person layout |
| `user-list-copy` | text column next to avatar |
| `user-list-stack` | vertical 2-line or multi-line stack |
| `user-list-avatar` | main avatar |
| `user-list-avatar--sm` | small avatar |
| `user-list-avatar-fallback` | initials avatar |
| `user-list-link` | main clickable text |
| `user-list-text-block` | standard body text |
| `user-list-text-block--strong` | stronger text line |
| `user-list-meta` | secondary muted text |
| `user-list-role-pill` | compact role badge |
| `user-list-status` | status wrapper |
| `user-list-status-dot` | status colored dot |
| `user-list-actions` | action button group |
| `user-list-action-btn` | quick action button |
| `user-list-menu-toggle` | dropdown toggle button |
| `user-list-empty` | empty-state text |

## Minimum Width Rules

The current design uses fixed minimum widths by column class:

```css
.users-col-checkbox { min-width: 52px; }
.users-col-user-info { min-width: 220px; }
.users-col-company { min-width: 168px; }
.users-col-job-type { min-width: 162px; }
.users-col-contact { min-width: 208px; }
.users-col-manager { min-width: 166px; }
.users-col-location { min-width: 128px; }
.users-col-status { min-width: 112px; }
.users-col-updated-on { min-width: 152px; }
.users-col-actions { min-width: 132px; }
```

Keep these if the table should feel visually identical.

## Responsive Behavior

The current responsive behavior in `resources/css/custom.css` does this:

- below `991.98px`: toolbar stretches, avatars shrink slightly, text gets a bit smaller
- below `575.98px`: the toolbar left group becomes vertical and the controls become full width

If another developer wants the same mobile behavior, those media-query rules must move together with the table styles.

## Optional Card View Placeholder

The page currently uses this placeholder for future card view:

```html
<div class="user-card-view-placeholder js-user-card-view-panel d-none">
    <div class="user-card-view-placeholder-box">
        <h5 class="mb-2">Card View</h5>
        <p class="mb-0">Card view layout will be added in the next phase.</p>
    </div>
</div>
```

## Recommended Reuse Strategy

If this table design will be reused in multiple screens:

1. Move the `#main-user-list-wrapper` CSS scope to a reusable class like `.user-list-theme`.
2. Wrap each reused table in `.user-list-theme`.
3. Reuse the same `users-col-*` and `user-list-*` classes.
4. Reuse the same DataTable render HTML patterns.

That avoids duplicate IDs and keeps the design consistent.

## Developer Checklist

- Add the wrapper scope used by the CSS
- Reuse the toolbar classes
- Keep the `table display` DataTable shell
- Keep the `users-col-*` column classes
- Output the same `user-list-*` inner HTML structure
- Keep the role pill tone classes
- Keep the action button and dropdown structure
- Keep the responsive CSS blocks
- Avoid inline CSS when moving this into a reusable table component
