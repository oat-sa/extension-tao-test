define([], function () {
    'use strict';

    return {
        name: 'mock-runner',
        init: function () {
            var self = this;
            setTimeout(function() {
                self.trigger('mock-provider-loaded');
            }, 250);
        },
        loadAreaBroker : function() {

        }
    };
});
