import {Tooltip} from "bootstrap";

export class Message extends HTMLElement {
    constructor() {
        super();
    }

    connectedCallback() {
        const authorId = this.getAttribute('author-id');
        const userId = this.getAttribute('user-id');
        const content = this.getAttribute('content');

        const messageElement = document.createElement('p');

        messageElement.classList.add('message', 'small', 'p-2', 'me-3', 'mb-2', 'text-white', 'rounded-3', 'text-start');
        messageElement.innerText = content;
        messageElement.setAttribute('data-bs-toggle', 'tooltip');

        if (authorId === userId) {
            messageElement.classList.add('message-end');
        }

        new Tooltip(messageElement);

        this.appendChild(messageElement);

        this.innerHTML = messageElement.outerHTML;
    }
}