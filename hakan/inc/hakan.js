function like_button_clicked(btn, postID) {
    //alert('button clicked.');
    jQuery.ajax({
        url: ajax_object.ajax_url,
        type: 'post',
        data: {post_id : postID, action : 'like_form'},
        success: function (result) {
            var str = 'like';
            if(result == 'liked'){
                str = "unlike";
            }
            else{
                str = 'like';
            }
            jQuery(btn).html(str);
        }



    })

}