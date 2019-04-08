window.onload = function()
{



  		function ukr(obj) {
  		    obj = obj.replace(/[^а-яА-ЯіІїЇ ]/ig,'');
  			return obj
  		}


         jQuery('.checkforminputs').on('click', function(e){

  	       	val1 = jQuery('#sender_name').val();

  	       	val2 = jQuery('#recipient_name').val();

  	        if  (val1 != ukr(val1) )  {
  	          	e.preventDefault();
  	          	alert('П. І. Б відправника  повинно бути написане кирилицею. виправіть це та повторіть спробу'); // pop alert message
  	          	jQuery('#sender_name').val( ukr(val1) );
  	          	return false;
  	        }
  	        else if (val2 != ukr(val2) ) {
  	          	e.preventDefault();
  	          	alert('П. І. Б отримувача повинно бути написане кирилицею. виправіть це та повторіть спробу'); // pop alert message
  	          	jQuery('#recipient_name').val( ukr(val2) );
  	          	return false;
  	        }
  	        else{

  	        	return true;

  	        }


          //jQuery('#recipient_name');
          //jQuery('#sender_name');

        });

 var MyDiv1 = document.getElementById("errno");
  if(MyDiv1){
      var h = document.getElementById('errno').childNodes[0].clientHeight;
      h-=20;
      var MyDiv2 = document.getElementById('messagebox');
      MyDiv2.innerHTML = MyDiv1.innerHTML;
      MyDiv2.style.height = h + 'px';
      MyDiv1.childNodes[0].style.height = 0 + 'px';
      MyDiv1.childNodes[0].style.padding = 0 ;
      MyDiv2.classList.add('error');
  }

  var MyDiv3 = document.getElementById("nnnid");
  if(MyDiv3){
      MyDiv3 = document.getElementById("nnnid");
      var h = 100 + 'px';
      console.log(h);
      var MyDiv4 = document.getElementById('messagebox');
      MyDiv4.innerHTML = MyDiv3.innerHTML;
      MyDiv4.style.height = h;
      MyDiv4.style.padding = '8px';
      MyDiv2.classList.add('updated');

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
