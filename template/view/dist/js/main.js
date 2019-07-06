$(function () {
    $('a').tooltip();
});
// ---------------
$(function () {
    $("#example1").DataTable();
    $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false
    });
});

// ----------------

$("#menu-toggle").click(function (e) {
    e.preventDefault();
    if ($('#wrapper').hasClass('active')) {
        $('#wrapper').removeClass('active');
        $('body').css('overflow', 'auto');
    } else {
        $('#wrapper').addClass('active');
        $('body').css('overflow', 'hidden');
    }
});
//----------------

$(function () {
    $(".fullscreen-carousel").owlCarousel({
        items: 1,
        center: true,
        dots: false,
        autoplay: true,
        autoplayTimeout: 5000,
        smartSpeed: 900,
        nav: true, //comment for sbi logo
        navText: ['<i class="fa fa-angle-left fa-2x"></i>', '<i class="fa fa-angle-right fa-2x"></i>'],
        loop: true,
        mouseDrag: true,
        touchDrag: true,
        navigation: true,
        singleItem: true,
        animateIn: 'fadeIn', // add this
        animateOut: 'fadeOut', // and this
        transitionStyle: "fade"
    });
    var owl_gal = $(".galley-carousel").owlCarousel({
        items: 1,
        center: true,
        dots: false,
        autoplay: true,
        autoplayTimeout: 5000,
        smartSpeed: 900,
        nav: false,
        navText: ['<i class="fa fa-angle-left fa-2x"></i>', '<i class="fa fa-angle-right fa-2x"></i>'],
        loop: true,
        mouseDrag: true,
        touchDrag: true
    });

    $(".gallery-box .gallery-fa .left-gallery").click(function () {
        owl_gal.trigger('prev.owl.carousel');
    })

    $(".gallery-box .gallery-fa .right-gallery").click(function () {
        owl_gal.trigger('next.owl.carousel');
    })

    var owl_4way = $(".4way-carousel").owlCarousel({
        items: 1,
        center: true,
        dots: false,
        autoplay: true,
        autoplayTimeout: 5000,
        smartSpeed: 900,
        nav: false,
        navText: ['<i class="fa fa-angle-left fa-2x"></i>', '<i class="fa fa-angle-right fa-2x"></i>'],
        loop: true,
        mouseDrag: true,
        touchDrag: true
    });

    $(".gallery-box .gallery-fa .left-4way").click(function () {
        owl_4way.trigger('prev.owl.carousel');
    })

    $(".gallery-box .gallery-fa .right-4way").click(function () {
        owl_4way.trigger('next.owl.carousel');
    })
});
//---------

$(document).ready(function () {

    $('#search a').click(function () {
        $(this).next('#custom-search-input').stop(true).slideToggle('500')
    })

});

//-----------

$(document).ready(function () {

    $('#page-menu li a').click(function () {
        $(this).next('ul').stop(true).slideToggle('500');
        $(this).find('i').toggleClass('fa fa-chevron-right fa fa-chevron-down')
    })

    $('#mobile-menu a').click(function () {
        $(this).next('.mobile-drop').stop(true).slideToggle('500');
        // $(this).find('i').toggleClass('fa fa-chevron-right fa fa-chevron-down')
    })

});

//----------

$(function () {
    $('.menu-icon').click(function () {
        if ($('#header .nav').hasClass('open')) {
            $('#header .nav').removeClass('open');
            $('body').css('overflow', 'hidden');
            $('#header .nav').css({
                transform: "translateX(0)"
            });
        } else {
            $('#header .nav').addClass('open');
            $('body').css('overflow', 'auto');
            $('#header .nav').css({
                transform: "translateX(-100%)"
            });
        }
    });

    var vH = $(window).height();

    $('.fullscreen').css('height', vH);
    $('.halfscreen').css('height', vH / 2);

    var headerTop = $('.header-top').outerHeight();
    var headerBottom = $('.header-bottom').outerHeight();
    var headerBoth = $('#header').outerHeight();

    var headerHeight = headerBoth;
    var windowHeight = vH - headerHeight;

    $('#header .nav').css('height', windowHeight);

    $(window).scroll(function () {
        if ($(this).scrollTop() > headerBoth) {
            $('#header').addClass('menu-fixed');
            $('#header .nav').css({
                position: 'fixed',
                top: headerBottom
            })
        } else {
            $('#header .header-bottom').removeClass('menu-fixed');
            $('#header .nav').css({
                position: 'absolute',
                top: 'auto'
            })
        }
    });

    //        var vH = $(window).height();
    //        
    //        $('.fullscreen').css('height', vH);
    //        $('.halfscreen').css('height', vH / 2);
    //        
    //        var headerTop = $('.header-top').outerHeight();
    //        var headerBottom = $('.header-bottom').outerHeight();
    //        
    //        var headerHeight = headerTop + headerBottom;
    //        var windowHeight = vH - headerHeight;
    //        
    //        $('#header .nav').css('height', windowHeight);
    //        
    //        $(window).scroll(function() {
    //            if($(this).scrollTop() > headerTop) {
    //                $('#header .header-bottom').addClass('menu-fixed');
    //                $('#header .nav').css({
    //                    position: 'fixed',
    //                    top: headerBottom
    //                })
    //            } else {
    //                $('#header .header-bottom').removeClass('menu-fixed');
    //                $('#header .nav').css({
    //                    position: 'absolute',
    //                    top: 'auto'
    //                })
    //            }
    //        });

});

//--------------------------
//     $(document).ready(function(){
//     $('[data-toggle="tooltip"]').tooltip();   
// });

//----------------------
//    window.onload = setInterval(clock,1000);
//
//    function clock()
//    {
//      var d = new Date();
//      
//      var date = d.getDate();
//      
//      var month = d.getMonth();
//      var montharr =["Jan","Feb","Mar","April","May","June","July","Aug","Sep","Oct","Nov","Dec"];
//      month=montharr[month];
//      
//      var year = d.getFullYear();
//      
//      var day = d.getDay();
//      var dayarr =["Sun","Mon","Tues","Wed","Thurs","Fri","Sat"];
//      day=dayarr[day];
//      
//      var hour =d.getHours();
//      var min = d.getMinutes();
//      var sec = d.getSeconds();
//      var ampm = hour >=12 ?'pm' : 'am';
//      hour = hour% 12;
//      hour = hour ? hour : 12;
//      document.getElementById("date").innerHTML=day+" "+date+" "+month+" "+year+" "+hour+":"+min+":"+sec;
//      document.getElementById("date2").innerHTML=day+" "+date+" "+month+" "+year+" "+hour+":"+min+":"+sec;
//    }
//-----------------

//--------------
