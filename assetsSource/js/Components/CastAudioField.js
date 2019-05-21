class CastAudioField {
    constructor (el) {
        return {
            el,

            mounted () {
                this.audioFileName = this.$el.getAttribute('data-audio-file-name');
                this.audioFileId = this.$el.getAttribute('data-audio-file-id');
            },

            data () {
                return {
                    audioFileName: '',
                    audioFileId: '',
                    audioFileUploadLocation: '',
                };
            },
        };
    }
}

export default CastAudioField;
