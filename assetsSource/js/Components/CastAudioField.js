/**
 * TODO: respond to file close being clicked
 */
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

                self.castFileName = String($el.getAttribute('data-audio-file-name'));

                self.hasFile = $el.getAttribute('data-audio-file-name') !== '';
            },

            data: {
                uploadIconIsActive: null,
                uploadInProgress: null,
                hasFile: null,
                castUploadPath: null,
                castFileName: null,
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

                uploadComplete (fileLocation, fileName) {
                    const self = this;

                    self.uploadInProgress = false;

                    self.hasFile = true;

                    self.castUploadPath = fileLocation;

                    self.castFileName = fileName;
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
                }

                if (typeof SuccessCallback !== 'function') {
                    return;
                }

                SuccessCallback(resp.data.file.location, resp.data.file.name);
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
