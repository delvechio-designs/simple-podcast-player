jQuery(function($){
  $('.spp-col').wpColorPicker();

  $('#spp_pick_art').on('click',function(e){
     e.preventDefault();
     const frame = wp.media({title:'Select default artwork',multiple:false,library:{type:'image'}});
     frame.on('select',()=>{
        const url = frame.state().get('selection').first().toJSON().url;
        $('#spp_default_art').val(url);
        $('#spp_art_prev').attr('src',url);
     });
     frame.open();
  });
});
