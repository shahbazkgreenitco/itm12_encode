(function (window, document, $) {
    "use strict";
    var styleId = "amg-drag-drop-style";
    var guardBound = false;
    var instances = [];

    function toBool(value, fallback) {
        if (value === undefined || value === null || value === "") return !!fallback;
        return value === true || value === "true" || value === "1" || value === 1;
    }

    function toNumber(value, fallback) {
        var parsed = parseFloat(value);
        return isNaN(parsed) ? fallback : parsed;
    }

    function hasFileDrag(event) {
        var types = event.dataTransfer && event.dataTransfer.types;
        if (!types) return false;
        return Array.prototype.indexOf.call(types, "Files") !== -1;
    }

    function injectStyles() {
        if (document.getElementById(styleId)) return;

        var style = document.createElement("style");
        style.id = styleId;
        style.textContent = [
            ".amg-uploader{width:100%;}",
            "[data-bs-theme='dark'] .amg-uploader__dropzone{background:#21252900;border-color:#495057;}",
            "[data-bs-theme='dark'] .amg-uploader__item{background:#2b3035;border-color:#495057;}",
            "[data-bs-theme='dark'] .amg-uploader__thumb{background:#343a40;color:#f8f9fa;}",
            "[data-bs-theme='dark'] .amg-uploader__name{color:#f8f9fa;}",
            "[data-bs-theme='dark'] .amg-uploader__status{color:#adb5bd;}",
            "[data-bs-theme='dark'] .amg-uploader__remove{background:#343a40;color:#f8f9fa;box-shadow:inset 0 0 0 1px #495057;}",
            ".amg-uploader__dropzone{min-height:82px;border:1.5px dashed #d9dee7;border-radius:8px;background:#fff;display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 16px;transition:border-color .18s ease,background-color .18s ease,box-shadow .18s ease;}",
            ".amg-uploader__dropzone.is-dragover{border-color:var(--btn-primary-bg,#f12f35);background:rgba(241,47,53,.04);box-shadow:0 0 0 .2rem rgba(241,47,53,.08);}",
            ".amg-uploader__message{display:flex;align-items:center;gap:10px;min-width:0;color:#344054;font-size:13px;font-weight:500;}",
            ".amg-uploader__icon{width:34px;height:34px;min-width:34px;border-radius:8px;background:#fff5f5;color:var(--btn-primary-bg,#f12f35);display:inline-flex;align-items:center;justify-content:center;}",
            ".amg-uploader__icon svg{width:18px;height:18px;}",
            ".amg-uploader__hint{display:block;color:#667085;font-size:12px;font-weight:400;margin-top:2px;}",
            ".amg-uploader__icon-wrap{width:34px;height:34px;min-width:34px;border-radius:8px;background:#fff5f5;color:var(--btn-primary-bg,#f12f35);display:inline-flex;align-items:center;justify-content:center;}",
            ".amg-uploader__icon-wrap svg{width:20px;height:20px;}",
            ".amg-uploader__preview{display:grid;gap:8px;margin-top:10px;}",
            ".amg-uploader__item{display:flex;align-items:center;gap:10px;border:1px solid #edf0f5;background:#f9fafb;border-radius:8px;padding:8px 10px;}",
            ".amg-uploader__thumb{width:42px;height:42px;min-width:42px;border-radius:8px;background:#eef2f7;color:#475467;display:flex;align-items:center;justify-content:center;overflow:hidden;font-size:10px;font-weight:700;text-transform:uppercase;}",
            ".amg-uploader__thumb img{display:block;width:100%;height:100%;object-fit:cover;}",
            ".amg-uploader__meta{flex:1 1 auto;min-width:0;}",
            ".amg-uploader__name{color:#1f2937;font-size:13px;font-weight:600;line-height:1.25;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}",
            ".amg-uploader__status{color:#667085;font-size:12px;line-height:1.35;margin-top:2px;}",
            ".amg-uploader__progress{height:4px;background:#e5e7eb;border-radius:999px;overflow:hidden;margin-top:6px;}",
            ".amg-uploader__progress-bar{height:100%;width:0%;background:var(--btn-primary-bg,#f12f35);transition:width .16s ease;}",
            ".amg-uploader__item.is-complete .amg-uploader__progress-bar{background:#198754;}",
            ".amg-uploader__item.is-error .amg-uploader__progress-bar{background:var(--btn-primary-bg,#f12f35);}",
            ".amg-uploader__remove{width:30px;height:30px;min-width:30px;border:0;border-radius:8px;background:#fff;color:#667085;display:inline-flex;align-items:center;justify-content:center;font-size:18px;line-height:1;box-shadow:inset 0 0 0 1px #d9dee7;}",
            ".amg-uploader__remove:hover{color:var(--btn-primary-bg,#f12f35);box-shadow:inset 0 0 0 1px var(--btn-primary-bg,#f12f35);}",
            ".amg-uploader__error{margin-top:8px;color:#c53030;font-size:12px;font-weight:500;}",
            "@media (max-width:575.98px){.amg-uploader__dropzone{align-items:stretch;flex-direction:column}.amg-uploader__dropzone .amg-btn{width:100%;}}"
        ].join("");
        document.head.appendChild(style);
    }

    function bindGlobalGuard() {
        if (guardBound) return;
        guardBound = true;

        document.addEventListener("dragover", function (event) {
            if (hasFileDrag(event)) {
                event.preventDefault();
            }
        });

        document.addEventListener("drop", function (event) {
            if (hasFileDrag(event)) {
                event.preventDefault();
            }
        });
    }

    function bytesToLabel(bytes) {
        if (!bytes && bytes !== 0) return "";
        if (bytes < 1024) return bytes + " B";
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + " KB";
        return (bytes / (1024 * 1024)).toFixed(1) + " MB";
    }

    function fileKey(file) {
        return [file.name, file.size, file.lastModified].join("|");
    }

    function getExtension(name) {
        var parts = String(name || "").split(".");
        return parts.length > 1 ? "." + parts.pop().toLowerCase() : "";
    }

    function matchesAccept(file, accept) {
        var rules;
        var ext;

        if (!accept) return true;
        rules = accept.split(",").map(function (rule) {
            return ($ && $.trim ? $.trim(rule) : rule.trim()).toLowerCase();
        }).filter(Boolean);
        if (!rules.length) return true;

        ext = getExtension(file.name);
        return rules.some(function (rule) {
            if (rule.charAt(0) === ".") return ext === rule;
            if (rule.slice(-2) === "/*") return file.type.indexOf(rule.slice(0, -1)) === 0;
            return file.type.toLowerCase() === rule;
        });
    }

    function createUploadIcon() {
        var span = document.createElement("span");
        span.className = "amg-uploader__icon";
        span.innerHTML = '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 15V4m0 0 4 4m-4-4-4 4M5 15v3a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        return span;
    }

    function readJson(responseText) {
        try {
            return JSON.parse(responseText);
        } catch (err) {
            return null;
        }
    }

    function safeDecode(value) {
        try {
            return decodeURIComponent(value);
        } catch (err) {
            return value;
        }
    }

    function AMGDragDropUploader(element, options) {
        this.root = element;
        this.options = options || {};
        this.files = [];
        this.items = [];
        this.dragDepth = 0;

        this.input = this.findElement(this.options.input || this.root.getAttribute("data-input") || ".amg-uploader__input");
        this.dropzone = this.findElement(this.options.dropzone || this.root.getAttribute("data-dropzone") || ".amg-uploader__dropzone");
        this.trigger = this.findElement(this.options.trigger || this.root.getAttribute("data-trigger") || ".amg-uploader__trigger");
        this.preview = this.findElement(this.options.preview || this.root.getAttribute("data-preview") || ".amg-uploader__preview");
        this.errorBox = this.findElement(this.options.errorBox || this.root.getAttribute("data-error") || ".amg-uploader__error");

        this.multiple = toBool(this.options.multiple !== undefined ? this.options.multiple : this.root.getAttribute("data-multiple"), true);
        this.autoUpload = toBool(this.options.autoUpload !== undefined ? this.options.autoUpload : this.root.getAttribute("data-auto-upload"), !!this.getUploadUrl());
        this.maxFiles = toNumber(this.options.maxFiles || this.root.getAttribute("data-max-files"), 0);
        this.maxSizeMb = toNumber(this.options.maxSizeMb || this.root.getAttribute("data-max-size"), 0);
        this.accept = this.options.accept || this.root.getAttribute("data-accept") || (this.input ? this.input.getAttribute("accept") : "");
        this.uploadField = this.options.uploadField || this.root.getAttribute("data-upload-field") || "attachment";

        this.init();
    }

    AMGDragDropUploader.prototype.findElement = function (selector) {
        if (!selector) return null;
        if (selector.nodeType === 1) return selector;
        if (selector.charAt && selector.charAt(0) === "#") {
            return document.querySelector(selector);
        }
        return this.root.querySelector(selector);
    };

    AMGDragDropUploader.prototype.init = function () {
        if (!this.input || !this.dropzone || !this.preview) return;

        injectStyles();
        bindGlobalGuard();

        this.input.multiple = this.multiple;
        if (this.accept && !this.input.getAttribute("accept")) {
            this.input.setAttribute("accept", this.accept);
        }

        this.bindEvents();
        this.root.AMGDragDropUploader = this;
        instances.push(this);
    };

    AMGDragDropUploader.prototype.bindEvents = function () {
        var self = this;

        if (this.trigger) {
            this.trigger.addEventListener("click", function (event) {
                event.preventDefault();
                event.stopPropagation();
                self.input.click();
            });
        }

        this.dropzone.addEventListener("click", function (event) {
            if (event.target.closest && event.target.closest("button,a,input,label")) return;
            self.input.click();
        });

        this.input.addEventListener("change", function (event) {
            self.handleFiles(event.target.files);
            self.input.value = "";
        });

        this.dropzone.addEventListener("dragenter", function (event) {
            if (!hasFileDrag(event)) return;
            event.preventDefault();
            self.dragDepth += 1;
            self.dropzone.classList.add("is-dragover");
        });

        this.dropzone.addEventListener("dragover", function (event) {
            if (!hasFileDrag(event)) return;
            event.preventDefault();
            self.dropzone.classList.add("is-dragover");
        });

        this.dropzone.addEventListener("dragleave", function (event) {
            if (!hasFileDrag(event)) return;
            self.dragDepth = Math.max(0, self.dragDepth - 1);
            if (!self.dragDepth) {
                self.dropzone.classList.remove("is-dragover");
            }
        });

        this.dropzone.addEventListener("drop", function (event) {
            if (!hasFileDrag(event)) return;
            event.preventDefault();
            self.dragDepth = 0;
            self.dropzone.classList.remove("is-dragover");
            self.handleFiles(event.dataTransfer.files);
        });
    };

    AMGDragDropUploader.prototype.handleFiles = function (fileList) {
        var self = this;
        var incoming = Array.prototype.slice.call(fileList || []);

        this.clearError();
        if (!incoming.length) return;

        if (!this.multiple) {
            this.clear();
            incoming = incoming.slice(0, 1);
        }

        incoming.forEach(function (file) {
            self.addFile(file);
        });

        this.syncInputFiles();
    };

    AMGDragDropUploader.prototype.addFile = function (file) {
        var maxBytes = this.maxSizeMb ? this.maxSizeMb * 1024 * 1024 : 0;
        var existing = this.files.some(function (savedFile) {
            return fileKey(savedFile) === fileKey(file);
        });

        if (existing) {
            this.showError("This file is already selected.");
            return;
        }

        if (this.maxFiles && this.items.length + 1 > this.maxFiles) {
            this.showError("You can upload maximum " + this.maxFiles + " file(s).");
            return;
        }

        if (maxBytes && file.size > maxBytes) {
            this.showError(file.name + " is larger than " + this.maxSizeMb + " MB.");
            return;
        }

        if (!matchesAccept(file, this.accept)) {
            this.showError(file.name + " is not an allowed file type.");
            return;
        }

        this.files.push(file);
        var item = this.renderItem(file);
        this.items.push(item);

        if (this.autoUpload) {
            this.uploadItem(item);
        } else {
            this.completeItem(item, "Ready");
        }
    };

    AMGDragDropUploader.prototype.renderItem = function (file, existingData) {
        var self = this;
        var item = document.createElement("div");
        var thumb = document.createElement("div");
        var meta = document.createElement("div");
        var name = document.createElement("div");
        var status = document.createElement("div");
        var progress = document.createElement("div");
        var bar = document.createElement("div");
        var remove = document.createElement("button");

        item.className = "amg-uploader__item";
        thumb.className = "amg-uploader__thumb";
        meta.className = "amg-uploader__meta";
        name.className = "amg-uploader__name";
        status.className = "amg-uploader__status";
        progress.className = "amg-uploader__progress";
        bar.className = "amg-uploader__progress-bar";
        remove.className = "amg-uploader__remove";
        remove.type = "button";
        remove.setAttribute("aria-label", "Remove file");
        remove.textContent = "x";
        remove.dataset.attachmentId = "";

        item.file = file || null;
        item.serverName = existingData && existingData.name ? existingData.name : (file ? file.name : "");
        item.serverId = existingData && existingData.id ? existingData.id : "";
        item.uploaded = !!existingData;

        if (file && file.type && file.type.indexOf("image/") === 0) {
            var img = document.createElement("img");
            img.alt = "";
            img.src = URL.createObjectURL(file);
            img.onload = function () {
                URL.revokeObjectURL(img.src);
            };
            thumb.appendChild(img);
        } else {
            thumb.textContent = getExtension(item.serverName || "file").replace(".", "") || "file";
        }

        name.textContent = item.serverName || "Attachment";
        status.textContent = existingData ? "Uploaded" : bytesToLabel(file.size);
        progress.appendChild(bar);
        meta.appendChild(name);
        meta.appendChild(status);
        meta.appendChild(progress);
        item.appendChild(thumb);
        item.appendChild(meta);
        item.appendChild(remove);
        this.preview.appendChild(item);

        item.statusEl = status;
        item.progressBar = bar;
        item.nameEl = name;

        remove.addEventListener("click", function () {
            self.removeItem(item);
        });

        if (existingData) {
            this.completeItem(item, "Uploaded");
        }

        return item;
    };

    AMGDragDropUploader.prototype.completeItem = function (item, message) {
        item.classList.remove("is-error");
        item.classList.add("is-complete");
        item.progressBar.style.width = "100%";
        item.statusEl.textContent = message || "Ready";
    };

    AMGDragDropUploader.prototype.failItem = function (item, message) {
        item.classList.remove("is-complete");
        item.classList.add("is-error");
        item.progressBar.style.width = "100%";
        item.statusEl.textContent = message || "Upload failed";
    };

    AMGDragDropUploader.prototype.getUploadUrl = function () {
        return this.options.uploadUrl || this.root.getAttribute("data-upload-url") || "";
    };

    AMGDragDropUploader.prototype.getRemoveUrl = function () {
        return this.options.removeUrl || this.root.getAttribute("data-remove-url") || "";
    };

    AMGDragDropUploader.prototype.getToken = function () {
        var tokenSelector = this.root.getAttribute("data-token-input");
        var tokenInput = tokenSelector ? document.querySelector(tokenSelector) : null;
        return this.options.token ||
            this.root.getAttribute("data-token") ||
            (tokenInput ? tokenInput.value : "") ||
            document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute("content") ||
            "";
    };

    AMGDragDropUploader.prototype.getRecordId = function () {
        var selector = this.options.recordInput || this.root.getAttribute("data-record-input");
        var input = selector ? document.querySelector(selector) : null;
        return this.options.recordId || (input ? input.value : "") || this.root.getAttribute("data-record-id") || "";
    };

    AMGDragDropUploader.prototype.appendBaseData = function (formData) {
        var token = this.getToken();
        var recordId = this.getRecordId();

        if (token) formData.append("_token", token);
        if (!isNaN(recordId) && Number(recordId) > 0) {
            formData.append("id", recordId);
        } else {
            formData.append("tmp_id", recordId);
        }
        var ticketId = this.root.getAttribute("data-ticket-id");
        if (ticketId) {
            formData.append("ticket_id", ticketId);
        }

        var parentSelector = this.root.getAttribute("data-parent");
        var parent = parentSelector ? document.querySelector(parentSelector) : this.root.closest("form");
        var extraInputs = this.root.getAttribute("data-extra-inputs");

        if (parent && extraInputs) {
            extraInputs.split(",").forEach(function(selector) {
                selector = selector.trim();
                var input = parent.querySelector(selector);
                if (!input) {
                    return;
                }
                formData.append(input.name || input.id,input.value);
            });
        }
    };

    AMGDragDropUploader.prototype.uploadItem = function (item) {
        var self = this;
        var url = this.getUploadUrl();
        var recordId = this.getRecordId();
        var formData = new FormData();
        var xhr = new XMLHttpRequest();

        if (!url || !item.file) {
            this.completeItem(item, "Ready");
            return;
        }

        if (!recordId) {
            this.failItem(item, "Save record before upload.");
            return;
        }

        this.appendBaseData(formData);
        formData.append(this.uploadField, item.file);

        xhr.upload.addEventListener("progress", function (event) {
            if (!event.lengthComputable) return;
            item.progressBar.style.width = Math.round((event.loaded / event.total) * 100) + "%";
        });

        xhr.addEventListener("load", function () {
            var response = readJson(xhr.responseText);
            var uploadedName = response && response.data && (response.data.filename || response.data.original_file_name);
            if (xhr.status >= 200 && xhr.status < 300 && response && response.status === "success") {
                item.uploaded = true;
                item.serverName = uploadedName || item.serverName;
                item.attachmentId = response.data.id || "";
                item.tmpId = response.data.tmp_id || "";
                var removeBtn = item.querySelector(".amg-uploader__remove");
                if (removeBtn) {
                    removeBtn.dataset.attachmentId = response.data.id || "";
                }
                item.serverId = response.data && response.data.id ? response.data.id : item.serverId;
                if (uploadedName) item.nameEl.textContent = uploadedName;
                self.completeItem(item, "Uploaded");
                self.syncInputFiles();
            } else {
                self.failItem(item, response && response.msg ? response.msg : "Upload failed");
            }
        });

        xhr.addEventListener("error", function () {
            self.failItem(item, "Upload failed");
        });

        item.statusEl.textContent = "Uploading...";
        xhr.open("POST", url, true);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhr.send(formData);
    };

    AMGDragDropUploader.prototype.removeItem = function (item) {
        var self = this;
        var removeUrl = this.getRemoveUrl();

        if (item.uploaded && removeUrl) {
            this.removeUploadedItem(item, function (success) {
                if (success) {
                    self.detachItem(item);
                }
            });
            return;
        }

        this.detachItem(item);
    };

    AMGDragDropUploader.prototype.removeUploadedItem = function(item, done) {
        var formData = new FormData();
        var xhr = new XMLHttpRequest();

        this.appendBaseData(formData);
        if (item.serverId) {
            formData.append("id", item.serverId);
        } else {
            formData.append("name", item.serverName || "");
        }
        item.statusEl.textContent = "Removing...";

        xhr.addEventListener("load", function () {
            var response = readJson(xhr.responseText);
            var success = xhr.status >= 200 && xhr.status < 300 && (!response || response.status === "success");
            if (!success) {
                item.statusEl.textContent = response && response.msg ? response.msg : "Unable to remove";
            }
            done(success);
        });

        xhr.addEventListener("error", function () {
            item.statusEl.textContent = "Unable to remove";
            done(false);
        });

        xhr.open("POST", this.getRemoveUrl(), true);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhr.send(formData);
    };

    AMGDragDropUploader.prototype.detachItem = function (item) {
        this.files = this.files.filter(function (file) {
            return file !== item.file;
        });
        this.items = this.items.filter(function (savedItem) {
            return savedItem !== item;
        });
        if (item.parentNode) {
            item.parentNode.removeChild(item);
        }
        this.syncInputFiles();
    };

    AMGDragDropUploader.prototype.syncInputFiles = function () {
        if (this.autoUpload || typeof DataTransfer === "undefined") return;

        var dataTransfer = new DataTransfer();
        this.files.forEach(function (file) {
            dataTransfer.items.add(file);
        });
        this.input.files = dataTransfer.files;
    };

    AMGDragDropUploader.prototype.clear = function () {
        this.files = [];
        this.items = [];
        this.preview.innerHTML = "";
        this.clearError();
        if (this.input) this.input.value = "";
    };

    AMGDragDropUploader.prototype.addExisting = function (fileData) {
        var name = fileData.original_file_name || fileData.name || fileData.filename || "Attachment";
        var item = this.renderItem(null, {
            id: fileData.id || "",
            name: safeDecode(name)
        });
        this.items.push(item);
        return item;
    };

    AMGDragDropUploader.prototype.showError = function (message) {
        if (!this.errorBox) return;
        this.errorBox.textContent = message;
    };

    AMGDragDropUploader.prototype.clearError = function () {
        if (!this.errorBox) return;
        this.errorBox.textContent = "";
    };

    function initAll(context) {
        var scope = context || document;
        var nodes = scope.querySelectorAll("[data-amg-uploader]");
        Array.prototype.forEach.call(nodes, function (node) {
            if (!node.AMGDragDropUploader) {
                new AMGDragDropUploader(node);
            }
        });
    }

    window.AMGDragDropUploader = AMGDragDropUploader;
    window.AMGDragDrop = {
        initAll: initAll,
        instances: instances
    };

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", function () {
            initAll(document);
        });
    } else {
        initAll(document);
    }
})(window, document, window.jQuery);
