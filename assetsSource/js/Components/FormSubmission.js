class FormSubmission {
    /**
     * @param {HTMLElement} el
     * @param {HTMLElement} body
     */
    constructor (el, body) {
        const Instance = this;

        Instance.body = body;

        el.addEventListener('submit', () => {
            Instance.submitResponder();
        });
    }

    submitResponder () {
        const Instance = this;
        const container = document.createElement('div');
        const waitingContainer = document.createElement('div');
        const waiting = document.createElement('div');
        const bounce1 = document.createElement('div');
        const bounce2 = document.createElement('div');
        const bounce3 = document.createElement('div');

        container.setAttribute('class', 'CastAudioOverlay');
        waitingContainer.setAttribute('class', 'CastAudioOverlay__WaitingContainer');
        waiting.setAttribute('class', 'WaitingAnimation');
        bounce1.setAttribute('class', 'WaitingAnimation__Bounce WaitingAnimation__Bounce--One');
        bounce2.setAttribute('class', 'WaitingAnimation__Bounce WaitingAnimation__Bounce--Two');
        bounce3.setAttribute('class', 'WaitingAnimation__Bounce WaitingAnimation__Bounce--Three');

        waiting.appendChild(bounce1);
        waiting.appendChild(bounce2);
        waiting.appendChild(bounce3);
        waitingContainer.appendChild(waiting);
        container.appendChild(waitingContainer);
        Instance.body.appendChild(container);
    }
}

export default FormSubmission;
