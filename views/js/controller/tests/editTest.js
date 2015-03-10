
define(['module', 'jquery','helpers','ui/lock', 'ui/feedback', 'i18n'],
	function(module, $, helpers, lock, feedback, __){

        var editTestController = {
            start : function(options){
                var config = module.config();
        
                if(config.msg !== false){
                    var lk = lock($('#lock-box')).hasLock(config.msg,
                        {
                            released : function() {
                            	feedback().success(__('The test has been released'));
                                this.close();
                            },
                            failed : function() {
                            	feedback().error(__('The test could not be released'));
                            },
                            url: helpers._url('release','Lock','tao'),
                            uri: config.uri
                        });
                }
            }
        };

        return editTestController;
});
