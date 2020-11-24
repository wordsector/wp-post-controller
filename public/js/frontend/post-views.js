jQuery(document).ready(function($){
        
    $.ajax({
        type: "POST",    
        url:wppc_post_views_local.ajax_url,                    
        dataType: "json",
        data:{action:"wppc_set_post_views_ajax",post_id:wppc_post_views_local.post_id, wppc_nonce:wppc_post_views_local.wppc_nonce},
        success:function(response){                                                             
        },
        error: function(response){                            
        }
        });
});