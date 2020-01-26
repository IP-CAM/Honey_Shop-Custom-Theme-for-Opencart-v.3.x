$(function(){
    $("#up").hide();
    $(window).scroll(function(){
        if($(this).scrollTop()>700){
            $("#up").fadeIn();
        }else{$("#up").fadeOut();
        }
    });
    $("#up").click(function(){
        $("body,html").animate({scrollTop:0},1200);
        return false;
    });
});
$(function() {
    var Accordion = function(el, multiple) {
        this.el = el || {};
        this.multiple = multiple || false;
        var dropdownlink = this.el.find('.dropdownlink');
        dropdownlink.on('click',
            { el: this.el, multiple: this.multiple },
            this.dropdown);
    };
    Accordion.prototype.dropdown = function(e) {
        var $el = e.data.el,
            $this = $(this),
            $next = $this.next();
        $next.slideToggle();
        $this.parent().toggleClass('open');

        if(!e.data.multiple) {
            $el.find('.submenuItems').not($next).slideUp().parent().removeClass('open');
        }
    }
    var accordion = new Accordion($('.accordion-menu'), false);
})
function getURLVar(key) {
    var value = [];

    var query = String(document.location).split('?');

    if (query[1]) {
        var part = query[1].split('&');

        for (i = 0; i < part.length; i++) {
            var data = part[i].split('=');

            if (data[0] && data[1]) {
                value[data[0]] = data[1];
            }
        }
        if (value[key]) {
            return value[key];
        } else {
            return '';
        }
    } else {
        var query = String(document.location.pathname).split('/');
        if (query[query.length - 1] == 'cart') value['route'] = 'checkout/cart';
        if (query[query.length - 1] == 'checkout') value['route'] = 'checkout/checkout';

        if (value[key]) {
            return value[key];
        } else {
            return '';
        }
    }
}
$(document).ready(function() {
    $('.text-danger').each(function() {
        var element = $(this).parent().parent();
        if (element.hasClass('form-group')) {
            element.addClass('has-error');
        }
    });
    $('#form-currency .currency-select').on('click', function(e) {
        e.preventDefault();
        $('#form-currency input[name=\'code\']').val($(this).attr('name'));
        $('#form-currency').submit();
    });
    $('#form-language .language-select').on('click', function(e) {
        e.preventDefault();
        $('#form-language input[name=\'code\']').val($(this).attr('name'));
        $('#form-language').submit();
    });
    $('#search input[name=\'search\']').parent().find('button').on('click', function() {
        var url = $('base').attr('href') + 'index.php?route=product/search';
        var value = $('header #search input[name=\'search\']').val();
        if (value) {
            url += '&search=' + encodeURIComponent(value);
        }
        location = url;
    });
    $('#search input[name=\'search\']').on('keydown', function(e) {
        if (e.keyCode == 13) {
            $('header #search input[name=\'search\']').parent().find('button').trigger('click');
        }
    });
    $('#menu .dropdown-menu').each(function() {
        var menu = $('#menu').offset();
        var dropdown = $(this).parent().offset();
        var i = (dropdown.left + $(this).outerWidth()) - (menu.left + $('#menu').outerWidth());
        if (i > 0) {
            $(this).css('margin-left', '-' + (i + 10) + 'px');
        }
    });
    $('#list-view').click(function() {
        $('#content .product-grid > .clearfix').remove();
        $('#content .row > .product-grid').attr('class', 'product-layout product-list col-xs-12');
        $('#grid-view').removeClass('active');
        $('#list-view').addClass('active');
        localStorage.setItem('display', 'list');
    });
    $('#grid-view').click(function() {
        var cols = $('#column-right, #column-left').length;
        if (cols == 2) {
            $('#content .product-list').attr('class', 'product-layout product-grid col-lg-6 col-md-6 col-sm-12 col-xs-12');
        } else if (cols == 1) {
            $('#content .product-list').attr('class', 'product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12');
        } else {
            $('#content .product-list').attr('class', 'product-layout product-grid col-lg-3 col-md-3 col-sm-6 col-xs-12');
        }
        $('#list-view').removeClass('active');
        $('#grid-view').addClass('active');
        localStorage.setItem('display', 'grid');
    });
    if (localStorage.getItem('display') == 'list') {
        $('#list-view').trigger('click');
        $('#list-view').addClass('active');
    } else {
        $('#grid-view').trigger('click');
        $('#grid-view').addClass('active');
    }
    $(document).on('keydown', '#collapse-checkout-option input[name=\'email\'], #collapse-checkout-option input[name=\'password\']', function(e) {
        if (e.keyCode == 13) {
            $('#collapse-checkout-option #button-login').trigger('click');
        }
    });
    $('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
    $(document).ajaxStop(function() {
        $('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
    });
});
var cart = {
    'add': function(product_id, quantity) {
        $.ajax({
            url: 'index.php?route=checkout/cart/add',
            type: 'post',
            data: 'product_id=' + product_id + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
            dataType: 'json',
            beforeSend: function() {
                $('#cart > button').button('loading');
            },
            complete: function() {
                $('#cart > button').button('reset');
            },
            success: function(json) {
                $('.alert-dismissible, .text-danger').remove();

                if (json['redirect']) {
                    location = json['redirect'];
                }

                if (json['success']) {
                    $('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    setTimeout(function () {
                        $('#cart > a').html('<i class="icon-улей demo-icon"></i><span id="cart-total"> '+json['total']+'</span>');
                    }, 100);
                    $('#cart > ul').load('index.php?route=common/cart/info ul li');
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    },
    'update': function(key, quantity) {
        $.ajax({
            url: 'index.php?route=checkout/cart/edit',
            type: 'post',
            data: 'key=' + key + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
            dataType: 'json',
            beforeSend: function() {
                $('#cart > button').button('loading');
            },
            complete: function() {
                $('#cart > button').button('reset');
            },
            success: function(json) {
                setTimeout(function () {
                    $('#cart > a').html('<i class="icon-улей demo-icon"></i><span id="cart-total"> '+json['total']+'</span>');
                }, 100);

                if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
                    location = 'index.php?route=checkout/cart';
                } else {
                    $('#cart > ul').load('index.php?route=common/cart/info ul li');
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    },
    'remove': function(key) {
        $.ajax({
            url: 'index.php?route=checkout/cart/remove',
            type: 'post',
            data: 'key=' + key,
            dataType: 'json',
            beforeSend: function() {
                $('#cart > button').button('loading');
            },
            complete: function() {
                $('#cart > button').button('reset');
            },
            success: function(json) {
                setTimeout(function () {
                    $('#cart > a').html('<i class="icon-улей demo-icon"></i><span id="cart-total"> '+json['total']+'</span>');
                }, 100);

                if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
                    location = 'index.php?route=checkout/cart';
                } else {
                    $('#cart > ul').load('index.php?route=common/cart/info ul li');
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
}
var voucher = {
    'add': function() {

    },
    'remove': function(key) {
        $.ajax({
            url: 'index.php?route=checkout/cart/remove',
            type: 'post',
            data: 'key=' + key,
            dataType: 'json',
            beforeSend: function() {
                $('#cart > button').button('loading');
            },
            complete: function() {
                $('#cart > button').button('reset');
            },
            success: function(json) {
                // Need to set timeout otherwise it wont update the total
                setTimeout(function () {
                    $('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
                }, 100);

                if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
                    location = 'index.php?route=checkout/cart';
                } else {
                    $('#cart > ul').load('index.php?route=common/cart/info ul li');
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
}
var wishlist = {
    'add': function(product_id) {
        $.ajax({
            url: 'index.php?route=account/wishlist/add',
            type: 'post',
            data: 'product_id=' + product_id,
            dataType: 'json',
            success: function(json) {
                $('.alert-dismissible').remove();

                if (json['redirect']) {
                    location = json['redirect'];
                }

                if (json['success']) {
                    $('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                }

                $('#wishlist-total span').html(json['total']);
                $('#wishlist-total').attr('title', json['total']);

                $('html, body').animate({ scrollTop: 0 }, 'slow');
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    },
    'remove': function() {

    }
}
var compare = {
    'add': function(product_id) {
        $.ajax({
            url: 'index.php?route=product/compare/add',
            type: 'post',
            data: 'product_id=' + product_id,
            dataType: 'json',
            success: function(json) {
                $('.alert-dismissible').remove();

                if (json['success']) {
                    $('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

                    $('#compare-total').html(json['total']);

                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    },
    'remove': function() {

    }
}
$(document).delegate('.agree', 'click', function(e) {
    e.preventDefault();
    $('#modal-agree').remove();
    var element = this;
    $.ajax({
        url: $(element).attr('href'),
        type: 'get',
        dataType: 'html',
        success: function(data) {
            html  = '<div id="modal-agree" class="modal">';
            html += '  <div class="modal-dialog">';
            html += '    <div class="modal-content">';
            html += '      <div class="modal-header">';
            html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
            html += '        <h4 class="modal-title">' + $(element).text() + '</h4>';
            html += '      </div>';
            html += '      <div class="modal-body">' + data + '</div>';
            html += '    </div>';
            html += '  </div>';
            html += '</div>';
            $('body').append(html);
            $('#modal-agree').modal('show');
        }
    });
});
(function($) {
    $.fn.autocomplete = function(option) {
        return this.each(function() {
            this.timer = null;
            this.items = new Array();
            $.extend(this, option);
            $(this).attr('autocomplete', 'off');
            $(this).on('focus', function() {
                this.request();
            });
            $(this).on('blur', function() {
                setTimeout(function(object) {
                    object.hide();
                }, 200, this);
            });
            $(this).on('keydown', function(event) {
                switch(event.keyCode) {
                    case 27:
                        this.hide();
                        break;
                    default:
                        this.request();
                        break;
                }
            });
            this.click = function(event) {
                event.preventDefault();

                value = $(event.target).parent().attr('data-value');

                if (value && this.items[value]) {
                    this.select(this.items[value]);
                }
            }
            this.show = function() {
                var pos = $(this).position();

                $(this).siblings('ul.dropdown-menu').css({
                    top: pos.top + $(this).outerHeight(),
                    left: pos.left
                });

                $(this).siblings('ul.dropdown-menu').show();
            }
            this.hide = function() {
                $(this).siblings('ul.dropdown-menu').hide();
            }
            this.request = function() {
                clearTimeout(this.timer);

                this.timer = setTimeout(function(object) {
                    object.source($(object).val(), $.proxy(object.response, object));
                }, 200, this);
            }
            this.response = function(json) {
                html = '';

                if (json.length) {
                    for (i = 0; i < json.length; i++) {
                        this.items[json[i]['value']] = json[i];
                    }

                    for (i = 0; i < json.length; i++) {
                        if (!json[i]['category']) {
                            html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a></li>';
                        }
                    }
                    var category = new Array();

                    for (i = 0; i < json.length; i++) {
                        if (json[i]['category']) {
                            if (!category[json[i]['category']]) {
                                category[json[i]['category']] = new Array();
                                category[json[i]['category']]['name'] = json[i]['category'];
                                category[json[i]['category']]['item'] = new Array();
                            }

                            category[json[i]['category']]['item'].push(json[i]);
                        }
                    }

                    for (i in category) {
                        html += '<li class="dropdown-header">' + category[i]['name'] + '</li>';

                        for (j = 0; j < category[i]['item'].length; j++) {
                            html += '<li data-value="' + category[i]['item'][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'] + '</a></li>';
                        }
                    }
                }

                if (html) {
                    this.show();
                } else {
                    this.hide();
                }

                $(this).siblings('ul.dropdown-menu').html(html);
            }

            $(this).after('<ul class="dropdown-menu"></ul>');
            $(this).siblings('ul.dropdown-menu').delegate('a', 'click', $.proxy(this.click, this));

        });
    }
})(window.jQuery);
/*MY CUSTOM*/
$(document).ready(function(){
    $('#button-close-search').on('click',function () {
        $('#search').fadeOut('slow');
    });
    $('#search-open').on('click',function(){
        $('#search').fadeIn('slow');
    });

    $(window).resize(function() {
        width = $(window).width();
        if (width >= 767) {
            $('.navbar-nav>li').hover(
                function () {
                    $(this).children('.dropdown-menu').fadeIn('slow');
                    $(this).addClass('open');
                },
                function () {
                    $(this).removeClass('open');
                    $('.dropdown-menu').fadeOut('slow');
                }
            );
        }
    });
    $('.plus-6').on('click',function(){
        var res=parseInt($('#quantity-6').val())+1;
        $('#quantity-6').val(res);
    });
    $('.minus-6').on('click',function(){
        var res=parseInt($('#quantity-6').val());
        if(res<=1){
            return false;
        }
        else{
            $('#quantity-6').val(res-1)
        }
    });
    $('.plus-5').on('click',function(){
        var res=parseInt($('#quantity-5').val())+1;
        $('#quantity-5').val(res);
    });
    $('.minus-5').on('click',function(){
        var res=parseInt($('#quantity-5').val());
        if(res<=1){
            return false;
        }
        else{
            $('#quantity-5').val(res-1)
        }
    });
    $('.plus-4').on('click',function(){
        var res=parseInt($('#quantity-4').val())+1;
        $('#quantity-4').val(res);
    });
    $('.minus-4').on('click',function(){
        var res=parseInt($('#quantity-4').val());
        if(res<=1){
            return false;
        }
        else{
            $('#quantity-4').val(res-1)
        }
    });
    $('.plus-3').on('click',function(){
        var res=parseInt($('#quantity-3').val())+1;
        $('#quantity-3').val(res);
    });
    $('.minus-3').on('click',function(){
        var res=parseInt($('#quantity-3').val());
        if(res<=1){
            return false;
        }
        else{
            $('#quantity-3').val(res-1)
        }
    });
    $('.plus-2').on('click',function(){
        var res=parseInt($('#quantity-2').val())+1;
        $('#quantity-2').val(res);
    });
    $('.minus-2').on('click',function(){
        var res=parseInt($('#quantity-2').val());
        if(res<=1){
            return false;
        }
        else{
            $('#quantity-2').val(res-1)
        }
    });
    $('.plus').on('click',function(){
        var res=parseInt($('#quantity').val())+1;
        $('#quantity').val(res);
        if( $('.radio input[type="radio"]:checked').length) {
            // var container=$('.radio input[type="radio"]:checked').parent();
            var price=parseInt($('.radio input[type="radio"]:checked').parent().find('.price_option strong').text());
            $('.product-description .price h5').html(''+price*res+'р.');
        }
    });
    $('.minus').on('click',function(){
        var res=parseInt($('#quantity').val()-1);
        if(res<=0){return false;}
        else{
            $('#quantity').val(res);
            if( $('.radio input[type="radio"]:checked').length){
                var container=$('.radio input[type="radio"]:checked').parent();
                var price=parseInt(container.find('.price_option strong').text());
                alert(price);
                $('.product-description .price h5').html(''+price*res+'р.');
            }}});

    $('.plus-1').on('click',function(){
        var res=parseInt($('#quantity-1').val())+1;
        $('#quantity-1').val(res);
    });
    $('.minus-1').on('click',function(){
        var res=parseInt($('#quantity-1').val());
        if(res<=1){
            return false;
        }
        else{
            $('#quantity-1').val(res-1)
        }
    });

    $('#cart').hover(
        function () {
            $('#cart .dropdown-menu').fadeIn('slow');
            $('#cart').addClass('open');
        },
        function () {
            $('#cart').removeClass('open');
            $('#cart .dropdown-menu').fadeOut('slow');
        }
    );
    $('#common-home').removeClass('container');
    $('#common-home').addClass('container-fluid');
    $('#column-left').addClass('col-lg-3');
    $('#content.category .product-layout').removeClass('col-lg-4  col-xs-12');
    $('#content.category .product-layout').addClass('col-lg-4 col-sm-6 col-xs-6 col-smx');
    $('.prev').hover(
        function () {
            $('.prev-img').fadeIn('slow');
            $('.prev-arrow').fadeOut('slow');
        },
        function () {
            $('.prev-img').fadeOut('slow');
            $('.prev-arrow').fadeIn('slow');
        }
    );
    $('.next').hover(
        function () {
            $('.next-img').fadeIn('slow');
            $('.next-arrow').fadeOut('slow');
        },
        function () {
            $('.next-img').fadeOut('slow');
            $('.next-arrow').fadeIn('slow');
        }
    );
    $('#information-contact .panel-body a').removeClass('.btn-info');
    $('#account-wishlist .table tr td button,#account-address .table tr td a,#account-order .table tr td a').removeClass('btn-primary');
    $('#information-contact .panel-body a,#account-wishlist .table tr td button,#account-address .table tr td a,#account-order .table tr td a').addClass('btn-honey');
    $('#account-address .table tr td a').removeClass('btn-danger');
});
var wow=new WOW({offset:0,mobile:false,});wow.init();