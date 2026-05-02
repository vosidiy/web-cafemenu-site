(function () {
    function supportsFileReplacement() {
        if (
            typeof window === 'undefined' ||
            typeof window.DataTransfer !== 'function' ||
            typeof window.File !== 'function'
        ) {
            return false;
        }

        try {
            return !!new window.DataTransfer();
        } catch (error) {
            return false;
        }
    }

    function isRasterFile(file) {
        return ['image/jpeg', 'image/png', 'image/webp'].includes(file.type);
    }

    function replaceInputFiles(input, files) {
        var transfer = new window.DataTransfer();
        Array.prototype.forEach.call(files, function (file) {
            transfer.items.add(file);
        });
        input.files = transfer.files;
    }

    function asUploadFile(result, originalFile) {
        if (result instanceof File) {
            return result;
        }

        return new File([result], originalFile.name, {
            type: result.type || originalFile.type,
            lastModified: Date.now(),
        });
    }

    window.initMenuItemImageUploader = function initMenuItemImageUploader(config) {
        if (!config || typeof window.Compressor === 'undefined') {
            return;
        }

        var canReplaceInputFiles = supportsFileReplacement();
        var field = document.querySelector(config.fieldSelector);
        var input = document.getElementById(config.inputId);
        var currentImage = document.querySelector(config.currentImageSelector);
        var status = document.querySelector(config.statusSelector);
        var previewShell = document.querySelector(config.previewShellSelector);
        var previewImage = document.querySelector(config.previewImageSelector);
        var removeButton = document.querySelector(config.removeButtonSelector);

        if (
            !field ||
            !input ||
            !status ||
            !previewShell ||
            !previewImage ||
            !removeButton
        ) {
            return;
        }

        field.classList.add('is-enhanced');

        var previewUrl = null;

        function revokePreviewUrl() {
            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
                previewUrl = null;
            }
        }

        function setStatus(message, isError) {
            status.textContent = message || '';
            status.classList.toggle('d-none', message === '');
            status.classList.toggle('is-error', Boolean(isError));
        }

        function hasSelectedFile() {
            return Boolean(input.files && input.files.length > 0);
        }

        function toggleExistingPreview() {
            if (!currentImage) {
                return;
            }

            currentImage.classList.toggle('d-none', hasSelectedFile());
        }

        function showPreviewFile(file) {
            revokePreviewUrl();

            if (!file || !isRasterFile(file)) {
                previewImage.removeAttribute('src');
                previewShell.classList.add('d-none');
                return;
            }

            previewUrl = URL.createObjectURL(file);
            previewImage.src = previewUrl;
            previewShell.classList.remove('d-none');
        }

        function syncUiWithSelection(previewFile) {
            var file = hasSelectedFile() ? input.files[0] : null;

            toggleExistingPreview();
            showPreviewFile(previewFile || file);

            if (!file) {
                setStatus('', false);
            }
        }

        function clearSelection(keepStatus) {
            if (canReplaceInputFiles) {
                replaceInputFiles(input, []);
            } else {
                input.value = '';
            }

            syncUiWithSelection(null);

            if (!keepStatus) {
                setStatus('', false);
            }
        }

        function compressSelectedFile(file) {
            if (!file) {
                syncUiWithSelection(null);
                return;
            }

            toggleExistingPreview();

            if (!isRasterFile(file)) {
                setStatus(config.labels.failed, true);
                clearSelection(true);
                return;
            }

            setStatus(config.labels.optimizing, false);

            new window.Compressor(file, {
                maxWidth: config.maxImageDimension,
                maxHeight: config.maxImageDimension,
                quality: config.outputQuality,
                success: function (result) {
                    var uploadFile = asUploadFile(result, file);
                    try {
                        if (canReplaceInputFiles) {
                            replaceInputFiles(input, [uploadFile]);
                            syncUiWithSelection(uploadFile);
                        } else {
                            syncUiWithSelection(file);
                        }

                        setStatus('', false);
                    } catch (error) {
                        setStatus(config.labels.failed, true);
                        syncUiWithSelection(file);
                    }
                },
                error: function () {
                    setStatus(config.labels.failed, true);
                    syncUiWithSelection(file);
                },
            });
        }

        function assignFiles(fileList) {
            if (!fileList || fileList.length === 0) {
                return;
            }

            var file = fileList[0];

            if (!canReplaceInputFiles || !isRasterFile(file)) {
                setStatus(config.labels.failed, true);
                clearSelection(true);
                return;
            }

            if (canReplaceInputFiles) {
                replaceInputFiles(input, [file]);
            }

            compressSelectedFile(canReplaceInputFiles ? input.files[0] || null : file);
        }

        input.addEventListener('change', function () {
            compressSelectedFile(input.files && input.files[0] ? input.files[0] : null);
        });

        removeButton.addEventListener('click', function () {
            clearSelection(false);
        });

        window.addEventListener('beforeunload', revokePreviewUrl);

        syncUiWithSelection(null);
    };
})();
