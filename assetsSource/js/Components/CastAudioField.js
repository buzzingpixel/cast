class CastAudioField {
    /**
     * @param {HTMLElement} el
     */
    constructor (el) {
        const Instance = this;

        // noinspection ES6ModulesDependencies,TypeScriptUMDGlobal
        Instance.vm = new Vue({
            el,

            data: {
                uploadIconIsActive: null,
                uploadInProgress: null,
                hasFile: null,
                uploadPath: null,
                fileName: null,
                mimeType: null,
                fileSize: null,
            },

            watch: {
                hasFile: (val) => {
                    const { vm } = Instance;

                    if (val) {
                        return;
                    }

                    vm.uploadPath = '';
                    vm.fileName = '';
                    vm.mimeType = '';
                    vm.fileSize = '';
                },
            },

            mounted () {
                const self = this;
                const { $el } = self;

                Instance.csrfTokenName = String($el.getAttribute('data-csrf-token-name'));

                Instance.csrfToken = String($el.getAttribute('data-csrf-token'));

                Instance.uploadKey = String($el.getAttribute('data-upload-key'));

                Instance.uploadUrl = String($el.getAttribute('data-upload-url'));

                self.hasFile = $el.getAttribute('data-file-name') !== '';

                self.fileName = String($el.getAttribute('data-file-name'));

                self.mimeType = String($el.getAttribute('data-mime-type'));

                self.fileSize = String($el.getAttribute('data-file-size'));
            },

            methods: {
                preventDefault (e) {
                    e.preventDefault();
                    e.stopPropagation();
                },

                dragOver (e) {
                    const self = this;

                    self.preventDefault(e);

                    self.uploadIconIsActive = true;
                },

                dragLeave (e) {
                    const self = this;

                    self.preventDefault(e);

                    self.uploadIconIsActive = false;
                },

                drop (e) {
                    const self = this;

                    self.preventDefault(e);

                    self.uploadIconIsActive = false;
                    self.uploadInProgress = true;

                    try {
                        const file = e.dataTransfer.files[0];
                        const type = file.type.split('/')[0];

                        if (type !== 'audio') {
                            throw new Error('Not an audio file');
                        }

                        Instance.uploadFile(
                            file,
                            (fileLocation, fileName) => {
                                self.uploadComplete(fileLocation, fileName);
                            },
                            () => {
                                self.uploadFailed();
                            },
                        );
                    } catch (error) {
                        self.uploadInProgress = false;
                    }
                },

                uploadComplete (file) {
                    const self = this;

                    self.uploadInProgress = false;
                    self.hasFile = true;
                    self.uploadPath = file.location;
                    self.fileName = file.name;
                    self.mimeType = file.mimeType;
                    self.fileSize = file.fileSize;
                },

                uploadFailed () {
                    const self = this;

                    self.uploadInProgress = false;
                },

                removeFile () {
                    this.hasFile = false;
                },
            },
        });
    }

    /**
     * @param {File} File
     * @param {CallableFunction} [SuccessCallback]
     * @param {CallableFunction} [FailureCallback]
     */
    uploadFile (File, SuccessCallback, FailureCallback) {
        const Instance = this;
        const ajaxData = new FormData();

        ajaxData.append(Instance.csrfTokenName, Instance.csrfToken);

        ajaxData.append('upload_key', Instance.uploadKey);

        ajaxData.append('file', File, File.name);

        // noinspection ES6ModulesDependencies,JSUnresolvedVariable
        axios.post(
            Instance.uploadUrl,
            ajaxData,
            {
                responseType: 'json',
                validateStatus: status => status === 200 || status === 400,
            },
        )
            .then((resp) => {
                Instance.uploadKey = resp.data.newUploadKey;

                if (!resp.data.success && typeof FailureCallback === 'function') {
                    FailureCallback(resp);
                    return;
                }

                if (typeof SuccessCallback !== 'function') {
                    return;
                }

                SuccessCallback(resp.data.file);
            })
            .catch((err) => {
                if (typeof FailureCallback !== 'function') {
                    return;
                }

                FailureCallback(err);
            });
    }
}

export default CastAudioField;
