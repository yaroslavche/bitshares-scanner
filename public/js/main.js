function initSlider()
{
    $('.currency-slider').slick({
        dots: false,
        arrows: false,
        infinite: true,
        autoplay: true,
        autoplaySpeed: 3000,
        speed: 0,
        slidesToShow: 5,
        slidesToScroll: 5,
        cssEase: 'linear',
        margin: '30px',
        responsive: [
                        {
                            "breakpoint": 1025,
                            "settings": {
                                "slidesToShow": 4,
                                "slidesToScroll": 1
                            }
                        },
                        {
                            "breakpoint": 768,
                            "settings": {
                                "slidesToShow": 2,
                                "slidesToScroll": 1
                            }
                        },
                        {
                            "breakpoint": 526,
                            "settings": {
                                "slidesToShow": 1,
                                "slidesToScroll": 1
                            }
                        },
                    ]
    });
}

$(document).ready( function() {
    jQuery('.currency-slider').empty();
    jQuery.post('/market/top/12', {}, function(response) {
        jQuery.each(response, function(i, market) {
            jQuery('.currency-slider').append('<div class="currency_carousel-item"><div class="currency-block">' + market.pair + '<span class="">' + market.price + '</span></div></div>');
        });
        initSlider();
	}, 'json');
    var $hamburger = $(".hamburger");
    $hamburger.on("click", function(e) {
        $hamburger.toggleClass("is-active");
        $("nav ul").slideToggle( "slow", function() {
            // Animation complete.
        });
        // Do something else, like open/close menu
    });

    $(".nano").nanoScroller();

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })


    /*Анімування головного пошуку на малих екранах*/
    if ( $(window).width() < 769 ) {
        $('.searchForm button').click(function(evt){
            evt.preventDefault();
            $('.searchForm').toggleClass('active');
        })

    }


    $(".filter-table").DataTable( {
        language: { search: "", searchPlaceholder: "Filter" },
        "bPaginate": false,
        "info": false,
        "autoWidth": false
    } );
    $('.dataTables_filter').addClass('form-default tableForm').append('<button></button>');



    /*Анімування пошуку таблиць на малих екранах*/
    if ( $(window).width() < 1201 ) {
        $('.dataTables_filter button').click(function(evt){
            evt.preventDefault();
            $('.tableForm input').toggleClass('active');
            $('.dataTables_filter').toggleClass('active');
        })

    }

    /*Фільтр таблиці по радіокнопках*/
    $("input[name='filterStatus']").change(function () {

        var classes;

        $("input[name='filterStatus']").each(function () {
            if ($(this).is(":checked")) {
                classes = $(this).val();
                $(".filter-table tbody tr").hide();
                $(".filter-table tbody tr").find('td.'+classes).parent().show();
            }
        });
    });

    /*Grid list toggle*/
    $('.grid-list-toggle .list').click(function() {
        $(this).addClass('active');
        $('.grid-list-toggle .grid').removeClass('active');
        $('.grid-list-table').removeClass('grid').addClass('list');
        $('.table-panel .panel-body').addClass('table-responsive');
        return false;
    });

    $('.grid-list-toggle .grid').click(function() {
        $(this).addClass('active');
        $('.grid-list-toggle .list').removeClass('active');
        $('.grid-list-table').removeClass('list').addClass('grid');
        $('.table-panel .panel-body').removeClass('table-responsive');
        return false;
    });




    /*tabs*/
    $('.tabs h3').click(function(){
        var tab_id = $(this).attr('data-tab');

        $('.tabs h3').removeClass('active');
        $('.tab_content').removeClass('active');

        $(this).addClass('active');
        $("#"+tab_id).addClass('active');
    });

});
