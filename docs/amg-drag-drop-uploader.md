# AMG Drag Drop Uploader Guide

This uploader is implemented in `public/js/drag-drap.js`. Use it when a module needs drag-drop file upload with preview, validation, auto upload, and remove support.

## 1. Include The Script

Add the uploader script on the page where the uploader HTML exists.

```blade
@push('scripts')
<script src="{{ CommonHelper::asset('js/drag-drap.js') }}"></script>
<script src="{{ CommonHelper::asset('js/your-module/your-module.js') }}"></script>
@endpush
```

The script auto-initializes all elements with `data-amg-uploader` on page load.

## 2. Add The HTML

Copy this structure into your modal or page.

```blade
<div id="attachment-dropper-cover"
    class="amg-uploader"
    data-amg-uploader
    data-multiple="true"
    data-auto-upload="true"
    data-max-files="10"
    data-max-size="10"
    data-accept=".jpg,.jpeg,.png,.gif,.xls,.xlsx,.doc,.docx,.ppt,.pdf,.txt,.msg,.zip,.psd,.csv"
    data-upload-url="{{ url('your/upload/route') }}"
    data-remove-url="{{ url('your/remove/route') }}"
    data-token="{{ csrf_token() }}"
    data-record-input="#your-form #id"
    data-upload-field="attachment">

    <input type="file" id="attachment_input" class="amg-uploader__input" multiple hidden>

    <div id="attachment-dropper" class="amg-uploader__dropzone">
        <div class="amg-uploader__message">
            <span class="amg-uploader__icon-wrap">...</span>
            <span>
                Upload note or
                <span class="amg-uploader__hint">Drop files here</span>
            </span>
        </div>

        <button type="button" class="amg-btn amg-btn-outline amg-btn-sm amg-uploader__trigger">
            Add Attachment
        </button>
    </div>

    <div class="amg-uploader__preview"></div>
    <div class="amg-uploader__error"></div>
</div>
```

## 3. Required Attributes

| Attribute | Purpose |
| --- | --- |
| `data-amg-uploader` | Marks the wrapper as an uploader. Required. |
| `data-upload-url` | POST route used for uploading files. Required for auto upload. |
| `data-remove-url` | POST route used when removing uploaded files. |
| `data-token` | CSRF token. You can also use a meta csrf token. |
| `data-record-input` | Selector for the hidden record ID input. Example: `#your-form #id`. |
| `data-upload-field` | File field name sent to the backend. Default is `attachment`. |

## 4. Optional Attributes

| Attribute | Default | Purpose |
| --- | --- | --- |
| `data-multiple` | `true` | Allow multiple files. Use `false` for one file only. |
| `data-auto-upload` | `true` if upload URL exists | Upload immediately after file selection/drop. |
| `data-max-files` | `0` | Maximum number of files. `0` means unlimited. |
| `data-max-size` | `0` | Max file size in MB. `0` means unlimited. |
| `data-accept` | input `accept` value | Allowed extensions/MIME types. |
| `data-input` | `.amg-uploader__input` | Custom input selector. |
| `data-dropzone` | `.amg-uploader__dropzone` | Custom dropzone selector. |
| `data-trigger` | `.amg-uploader__trigger` | Custom button selector. |
| `data-preview` | `.amg-uploader__preview` | Custom preview container selector. |
| `data-error` | `.amg-uploader__error` | Custom error container selector. |
| `data-token-input` | none | Selector for CSRF token input. |
| `data-record-id` | none | Static record ID if not using `data-record-input`. |

## 5. Initialize In Dynamic Modals

If the uploader HTML is already on the page, no manual initialization is needed.

If the modal/content is injected dynamically, initialize it after the HTML is added:

```js
if (window.AMGDragDrop) {
    window.AMGDragDrop.initAll(document.getElementById("yourModal"));
}
```

For the schedule module, this is done like:

```js
if (window.AMGDragDrop) {
    window.AMGDragDrop.initAll(t.mdl[0]);
}

t.uploader = t.mdl.find("#attachment-dropper-cover")[0].AMGDragDropUploader;
```

## 6. Important Record ID Step

With `data-auto-upload="true"`, the uploader sends files immediately. The backend needs a record ID, so create/init the parent record before opening the modal.

Example flow:

1. User clicks Add.
2. Call an init/save endpoint.
3. Put returned ID into the hidden input.
4. Open the modal.
5. Uploads can now use that ID.

```js
$.get(config.url.initRecord).done(function (res) {
    if (res.status === "success") {
        $("#your-form #id").val(res.id);
        $("#yourModal").modal("show");
    }
});
```

If the ID is missing, the uploader shows: `Save record before upload.`

## 7. Backend Upload Response

The upload endpoint must accept:

| Request field | Meaning |
| --- | --- |
| `_token` | CSRF token |
| `id` | Parent record ID |
| `attachment` | Uploaded file, or your `data-upload-field` value |

Return JSON like:

```json
{
  "status": "success",
  "msg": "",
  "data": {
    "id": 123,
    "filename": "uploaded-file-name.pdf"
  }
}
```

The uploader reads `data.filename` and shows it in the preview.

## 8. Backend Remove Response

The remove endpoint receives:

| Request field | Meaning |
| --- | --- |
| `_token` | CSRF token |
| `id` | Parent record ID |
| `name` | Uploaded filename shown in preview |

Return JSON like:

```json
{
  "status": "success",
  "msg": "Attachment removed"
}
```

## 9. Show Existing Attachments In Edit Mode

When editing an existing record, clear the uploader and add existing files after loading record data.

```js
var uploader = document.querySelector("#attachment-dropper-cover").AMGDragDropUploader;

if (uploader) {
    uploader.clear();

    (record.attachments || []).forEach(function (file) {
        uploader.addExisting({
            original_file_name: file.original_file_name
        });
    });
}
```

Supported existing file keys are `original_file_name`, `name`, or `filename`.

## 10. Useful JavaScript Methods

```js
var uploader = document.querySelector("#attachment-dropper-cover").AMGDragDropUploader;
```

| Method | Use |
| --- | --- |
| `uploader.clear()` | Remove all preview items and selected files. |
| `uploader.addExisting(fileData)` | Add an already-uploaded file to preview. |
| `window.AMGDragDrop.initAll(scope)` | Initialize all uploaders inside a DOM scope. |

## 11. Non Auto Upload Mode

Use this when you want files submitted with the main form instead of uploaded instantly.

```html
data-auto-upload="false"
```

In this mode, selected files stay in the file input and can be submitted with `new FormData(formElement)`.

## 12. Checklist For A New Module

1. Include `js/drag-drap.js`.
2. Add uploader HTML with `data-amg-uploader`.
3. Add upload and remove routes.
4. In the upload controller, validate `id` and the file field.
5. Return `{ status: "success", data: { filename: "..." } }`.
6. In the remove controller, accept `id` and `name`.
7. If auto upload is enabled, set the hidden record ID before opening the modal.
8. If the uploader is inside dynamic HTML, call `window.AMGDragDrop.initAll(scope)`.
9. For edit mode, call `uploader.clear()` then `uploader.addExisting(...)`.
10. Test select file, drag-drop, max size, invalid extension, remove, and edit existing attachments.
