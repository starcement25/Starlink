// JavaScript Document
var $ = jQuery.noConflict();

// $(window).scroll(function() {    
//     var scroll = $(window).scrollTop();

//     if (scroll >= 50) {
//         $(".headerSec").addClass("darkHeader");
//     } else {
//         $(".headerSec").removeClass("darkHeader");
//     }
    
//     if ($(window).width() < 767) {
//         $('.headerSec').removeClass('darkHeader');
//     }

// });

$(document).ready(function () {
	
	// $("#add-sec-carousel").owlCarousel({
  //       autoplay: true,
  //       items : 4, 
  //       navText: false,
  //            animateOut: 'fadeOutRight',
  //       autoplayTimeout:3000,
	// 	dots: false, 
	// 	loop:true,      
	// 	nav: true,

    
  //      navText : ['<i class="fa fa-angle-left" aria-hidden="true"></i>','<i class="fa fa-angle-right" aria-hidden="true"></i>'],
	// 	mouseDrag:true,
	// 	lazyLoad : false,
	// 	responsive:{
  //       0:{
  //           items:1
  //       },
  //       600:{
  //           items:2
  //       },
  //       900:{
  //           items:3
  //       },
  //       1000:{
  //           items:3
  //       }
  //   }
  //     });

  
	
});	

//----------------------------------------------- 
// --

// wow = new WOW(
//     {
//       animateClass: 'animated',
//       offset:       100,
//       callback:     function(box) {
//         //console.log("WOW: animating <" + box.tagName.toLowerCase() + ">")
//       }
//     }
// );
// wow.init();
    
// AOS.init();

  // Page loading animation
//   $(window).on('load', function() {
//     if($('.cover').length){
//         $('.cover').parallax({
//             imageSrc: $('.cover').data('image'),
//             zIndex: '1'
//         });
//     }

//     $("#preloader").animate({
//         'opacity': '0'
//     }, 600, function(){
//         setTimeout(function(){
//             $("#preloader").css("visibility", "hidden").fadeOut();
//         }, 300);
//     });
// });

/*Scroll to top when arrow up clicked*/
// $(window).scroll(function() {
//     var height = $(window).scrollTop();
//     if (height > 200) {
//         $('#back2Top').fadeIn();
//     } else {
//         $('#back2Top').fadeOut();
//     }
//   });
//   $(document).ready(function() {
//     $("#back2Top").click(function(event) {
//         event.preventDefault();
//         $("html, body").animate({ scrollTop: 0 }, 600);
//         return false;
//     });
  
//   });
  /*Scroll to top when arrow up clicked END*/

//   $(window).load(function () {
//     $('.dl').hide();
//   });


jQuery(function($) {
    if ($(window).width() > 769) {
      $('.navbar .dropdown').hover(function() {
        $(this).find('.dropdown-menu').first().stop(true, true).delay(250).slideDown();
  
      }, function() {
        $(this).find('.dropdown-menu').first().stop(true, true).delay(100).slideUp();
  
      });
  
      $('.navbar .dropdown > a').click(function() {
        location.href = this.href;
      });
  
    }
  });
    

  // AOS.init({disable: 'mobile'});
  // AOS.init({
  //   disable: function() {
  //     var maxWidth = 800;
  //     return window.innerWidth < maxWidth;
  //   }
  // });


 

  // $("a[rel^='auctionPhoto']").prettyPhoto({social_tools:false});