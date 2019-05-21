class CastAudioField {
    constructor (el) {
        const Instance = this;

        return {
            el,

            mounted () {
                const self = this;

                const { $el } = self;

                Instance.csrfTokenName = String($el.getAttribute('data-csrf-token-name'));

                Instance.csrfToken = String($el.getAttribute('data-csrf-token'));

                Instance.uploadKey = String($el.getAttribute('data-upload-key'));

                Instance.uploadUrl = String($el.getAttribute('data-upload-url'));

                self.hasFile = $el.getAttribute('data-audio-file-name') !== '';
            },

            data: {
                uploadIconIsActive: false,
                uploadInProgress: false,
                hasFile: false,
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
                            () => {
                                self.uploadComplete();
                            },
                            () => {
                                self.uploadFailed();
                            },
                        );
                    } catch (error) {
                        self.uploadInProgress = false;
                    }
                },

                uploadComplete () {
                    const self = this;

                    self.uploadInProgress = false;
                },

                uploadFailed () {
                    const self = this;

                    self.uploadInProgress = false;
                },
            },
        };
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
        axios.post(Instance.uploadUrl, FormData)
            .then((resp) => {
                console.log('then', resp);

                if (typeof SuccessCallback !== 'function') {
                    return;
                }

                SuccessCallback();
            })
            .catch(() => {
                if (typeof FailureCallback !== 'function') {
                    return;
                }

                FailureCallback();
            });
    }
}

export default CastAudioField;
