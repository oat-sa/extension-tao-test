
define(['module', 'jquery','helpers','ui/lock', 'ui/feedback', 'i18n'],
	function(module, $, helpers, lock, feedback, __){

        var editTestController = {
            start : function(options){
                var config = module.config();
        
            	var $lockDiv = $('#lock-box');
                if($lockDiv.length == 1){
                    var lk = lock($lockDiv).hasLock($lockDiv.data('msg'),
                        {
                            released : function() {
                            	feedback().success(__('The test has been released'));
                                this.close();
                            },
                            failed : function() {
                            	feedback().error(__('The test could not be released'));
                            },
                            url: helpers._url('release','Lock','tao'),
                            uri: $lockDiv.data('id')
                        });
                }
            }
        };

        return editTestController;
});
