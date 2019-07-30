jQuery(document).ready(function(){

   jQuery('.start_date').datetimepicker({
        format:'Y-m-d H:i:s',
        onShow:function( ct ){
         this.setOptions({
         // maxDate:jQuery('.end_date').val()?jQuery('.end_date').val():false
         })
        },
        timepicker:true
       });
       jQuery('.end_date').datetimepicker({
        format:'Y-m-d H:i:s',
        onShow:function( ct ){
         var data = jQuery('.start_date').val();   
         var data_arr = data.split(' ');     
          
         this.setOptions({
            minDate:jQuery('.start_date').val()?new Date(data_arr[0]):false,
         })
        },
        timepicker:true
 });


 });