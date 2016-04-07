(function($){
    $(function(){
        if (typeof CKEDITOR != 'undefined') {
            $(document).on('click', 'a.sonata-ba-action', function(ev){
                $('.collection-ckeditor').each(function(){
                    var ckeditorId = $(this).attr('id');
                    var data = CKEDITOR.instances[ckeditorId].getData();

                    window.localStorage.setItem(ckeditorId, data);
                });
            });
            $(document).on('sonata.add_element', function(){
                $('.collection-ckeditor').each(function(){
                    var ckeditorId = $(this).attr('id');
                    var editor = CKEDITOR.instances[ckeditorId];
                    var data = window.localStorage.getItem(ckeditorId);

                    editor.setData(data);
                });
            });
        }
    });
})(jQuery);