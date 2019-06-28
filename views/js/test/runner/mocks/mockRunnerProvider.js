define([], function () {
    'use strict';

    return {
        name: 'mock-runner',
        init() {
            setTimeout( () => this.trigger('mock-runner-loaded'), 250);
        },
        loadAreaBroker() {

        }
    };
});
