/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
define(['lodash', 'taoTests/runner/plugin'], function (_, pluginFactory){

    var _defaults = {};

    var pluginImpl = {
        name : 'responseSubmitter',
        init : function (testRunner, cfg){
            console.log(true, 'called init', testRunner, cfg);
            
            var self = this;
            
            //listen item response change
            var itemResponses = {
                RESPONSE1 : 1,
                RESPONSE2 : ['A', 'B', 'C']
            };
            
            _.delay(function(){
                self.trigger('submit', itemResponses);
            }, 200);
            
            //get ready to submit
            testRunner.before('next', function (e){
                
                var done = e.done();
                
                //submit it to the server
                _.delay(function(){
                    var success = true;
                    if(success){
                        self.trigger('submit', itemResponses);//this will also call testRunner.trigger('sumbit.responseSubmitter')
                        done();
                    }else{
                        //how to trigger error ?
                        e.prevent();
                    }
                }, 200);
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

    return function pluginSubmitter(config){
        return pluginFactory(pluginImpl, _defaults)(config);
    };
});