/* eslint-disable no-new */
import CastAudioField from './Components/CastAudioField.js';
import FormSubmission from './Components/FormSubmission.js';

(() => {
    const audioFields = document.querySelectorAll('[ref="CastAudioField"]');
    const body = document.querySelectorAll('body').item(0);
    const forms = document.querySelectorAll('form');

    audioFields.forEach((field) => {
        new CastAudioField(field);
    });

    forms.forEach((form) => {
        new FormSubmission(form, body);
    });
})();
