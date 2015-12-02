/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
define(['taoTests/runner/plugin'], function (pluginFactory){

    var _defaults = {};

    var pluginImpl = {
        name : 'nextButton',
        init : function (testRunner, cfg){
            
            var $container = $(cfg.container);
            var $button = $('<button class="next">');
            this.$button = $button;
            
            //create button
            $container.append($button);
            $button.click(function(){
                testRunner.next();
            });
            
            //event handler
            testRunner.on('itemready', function(){
                var state = this.getState();
                var isLast = false;//can get this information from test runner's state var
                if(isLast){
                    $button.hide();
                }
            });
        },
        destroy : function (){
            this.$button.remove();
        },
        show : function (){
            this.$button.show();
        },
        hide : function (){
            this.$button.hide();
        },
        enable : function (){
            this.$button.removeClass('disable');
        },
        disable : function (){
            this.$button.addClass('disable');
        }
    };

    return function pluginNextButton(config){
        return pluginFactory(pluginImpl, _defaults)(config);
    };
});