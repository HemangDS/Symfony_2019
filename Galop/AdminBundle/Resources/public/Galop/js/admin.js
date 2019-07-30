$(document).ready(function(){
    var element = $(".full_article");
    if($(element).length > 0) {
        var editorid = $(element).attr('id');
        $("#cke_"+editorid+" .cke_contents").on("keyup","textarea",function(){
            //var content = $(this).val().replace(/<img[^>]*>/g,"");
            var content = $($(this).val()).text().substring(0,200) + "...";
            $(".short_article").val(content);
        });
        
        var editor = CKEDITOR.instances[editorid];
        editor.on( 'change', function( evt ) {
            //var content = evt.editor.getData().replace(/<img[^>]*>/g,"");
            var content = $(evt.editor.getData()).text().substring(0,200) + "...";
            $(".short_article").val(content);
        });
    }
});