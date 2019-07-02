define([], function () {
    'use strict';

    return {
        name: 'mock-alt-runner',
        init() {
            setTimeout( () => this.trigger('mock-alt-runner-loaded'), 250);
        },
        loadAreaBroker() {

        }
    };
});
