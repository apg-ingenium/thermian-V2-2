export class EventBus {

    #subscribers;

    constructor() {
        this.#subscribers = {};
    }

    publish(eventName, event) {
        this.#findSubscribersFor(eventName)
            .map((subscriber) => subscriber(event));
    }

    subscribe(eventName, subscriber) {
        if (!this.#containsSubscribersFor(eventName)) {
            this.#subscribers[eventName] = [];
        }
        this.#subscribers[eventName].push(subscriber);
    }

    #findSubscribersFor(eventName) {
        return this.#containsSubscribersFor(eventName)
            ? this.#subscribers[eventName]
            : [];
    }

    #containsSubscribersFor(eventName) {
        return eventName in this.#subscribers;
    }

}
