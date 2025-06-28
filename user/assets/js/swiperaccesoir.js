var swiperCategories = new Swiper(".accesoir__container", {
    spaceBetween: 20,     loop: true,
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    breakpoints: {
        350: {
            slidesPerView: 1,          
               spaceBetween: 20,
        },
        768: {
            slidesPerView: 2,         
                spaceBetween: 20,
        },
        992: {
            slidesPerView: 3,      
                   spaceBetween: 20,
        },
        1200: {
            slidesPerView: 4,     
                    spaceBetween: 20,
        },
        1400: {
            slidesPerView: 5,           
              spaceBetween: 20,
        },
    },
});



var swiper = new Swiper(".home", {
    loop: true, 
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    
    autoplay: {
      delay: 3500, 
      disableOnInteraction: false, 
    },
  });




  