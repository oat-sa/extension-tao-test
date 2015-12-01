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
            console.log(true, 'called init', testRunner, cfg);
            
            //create button
            //append listner
            var self = this;
            var $myContainer = $('#next');
            $myContainer.append('<button>');
            $myContainer.click(function(){
                testRunner.next();
            });

            testRunner.on('itemready', function(){
                var state = this.getState();
                var isLast = false;//can get this information from test runner's state var
                if(isLast){
                    $myContainer.hide();
                }
            });
            
        },
        destroy : function (){
            console.log(true, 'called destory');
        },
        show : function (){
            console.log(true, 'called show');
        },
        hide : function (){
            console.log(true, 'called hide');
        },
        enable : function (){
            console.log(true, 'called enable');
        },
        disable : function (){
            console.log(true, 'called disable');
        }
    };

    return function pluginNextButton(config){
        return pluginFactory(pluginImpl, _defaults)(config);
    };
});