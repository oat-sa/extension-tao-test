define(['taoTests/runner/plugin'], function (pluginFactory) {
    'use strict';

    return pluginFactory({
        name: 'mock',
        init: function () {
            var self = this;
            setTimeout(function() {
                self.trigger('loaded');
            }, 250);
        }
    });
});
