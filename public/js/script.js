window.onload = function()
{

    function operatedInput(inputID){ document.getElementById(inputID).onkeypress = check; function check(e){ var evt = (e) ? e : window.event; var code = (document.all) ? evt.keyCode:evt.charCode; if ((code < 1040) || (code > 1103)){ alert('для цього поля доступна тільки кирилиця'); return false;} } } 

    try{
      operatedInput('recipient_name');
    }
    catch( message){

    }
    jQuery(function(){
     console.log('ready');
    jQuery('.tab-click').on('click', function(e){
      jQuery(this).siblings().removeClass('nav-tab-active');
      jQuery(this).addClass('nav-tab-active');
      e.preventDefault();
      hrefselector= jQuery(this).attr('href');
      jQuery(hrefselector).parent().children('.active').removeClass('active');
      jQuery(hrefselector).addClass('active');
      });
    });
}
