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
            
            var self = this;
            
            //listen item response change
            var itemResponses = {
                RESPONSE1 : 1,
                RESPONSE2 : ['A', 'B', 'C']
            };
            
            this.active = true;
            
            //get ready to submit "on move" (warning ! not "on next" because it will currently fail)
            testRunner.before('move', function (e){
                
                var done = e.done();
                
                //submit it to the server (the delay simulates latency)
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
            //remove listners
        },
        //does not need to implement show/hide because it is not a graphic plugin
        enable : function (){
            this.active = true;
        },
        disable : function (){
            this.active = false;
        }
    };

    return function pluginSubmitter(config){
        return pluginFactory(pluginImpl, _defaults)(config);
    };
});