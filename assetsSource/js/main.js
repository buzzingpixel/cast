/* eslint-disable no-new */
import CastAudioField from './Components/CastAudioField.js';

const audioFields = document.querySelectorAll('[ref="CastAudioField"]');

audioFields.forEach((el) => {
    const CastAudioFieldInstance = new CastAudioField(el);

    // noinspection ES6ModulesDependencies,TypeScriptUMDGlobal
    new Vue(CastAudioFieldInstance);
});
