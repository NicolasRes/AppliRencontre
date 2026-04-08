import {Tooltip} from "bootstrap";

export class Message extends HTMLElement {
    constructor() {
        super();
    }

    connectedCallback() {
        // Récupération d'attributs
        const authorId = this.getAttribute('author-id');
        const userId = this.getAttribute('user-id');
        const content = this.getAttribute('content');

        // Wrapper (pour avoir un div comme en Twig)
        const wrapper = document.createElement('div');
        wrapper.classList.add('d-flex', 'mb-2');

        // Création du messageElement (balise p)
        const messageElement = document.createElement('div');

        // Styles bootstrap
        messageElement.classList.add('message',
            'small',
            'p-2',
            'me-3',
            'mb-2',
            'text-white',
            'rounded-3'
        );

        messageElement.innerText = content;
        messageElement.setAttribute('data-bs-toggle', 'tooltip');

        // Cas utilisateur courant
        if (authorId === userId) {
            wrapper.classList.add('justify-content-end');
            messageElement.classList.add('bg-primary', 'mb-2', 'text-start');
        }
        else {
            wrapper.classList.add('justify-content-start');
            messageElement.classList.add('bg-secondary', 'mb-1', 'message-end');
        }

        new Tooltip(messageElement);

        // Structure finale
        wrapper.appendChild(messageElement);

        this.innerHTML = '';
        this.appendChild(wrapper);
    }
}
