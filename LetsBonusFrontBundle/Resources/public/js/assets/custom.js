function dataLayerAction(BBDD_id,affiliation,revenue,tax,shipping,b_id,price,brand,category) {
    dataLayer.push({ 
      'event' : 'purchaseEvent',
      'ecommerce': {
            'currencyCode': 'EUR', //3 character code, f.e. ‘EUR’
            'purchase': {
                'actionField': {
                  'id': BBDD_id, //transaction BBDD id
                  'affiliation': affiliation, //values: cashback, promotion, coupon 
                  'revenue': revenue, //product price
                  'tax': tax, //discount in €
                  'shipping': shipping, //commission in €
                  'coupon': '' //always empty
                },
                'products': [ 
                   {
                    'name': b_id, //Brand id
                    'id': BBDD_id, //Brand BBDD id
                    'price': price, //product price
                    'brand': brand, //product brand
                    'category': category, //product category                    
                    'variant': '', //always empty
                    'quantity': 1, //always 1
                    'coupon': '' //always empty
              }
                ]
            }
        }
    });
}
function setSessionCookie(cname, cvalue) {
    document.cookie = cname + "=" + cvalue + "; ";
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
    }
    return "";
}

function toggleUserArea(url) {
    if (url.trim() != "") {
        $.ajax({
            method: "GET",
            url: url,
            dataType: "json",
            beforeSend: function() {
                /*$('#openModal').show();*/
            },
            success: function(result) {
                if (result.url != undefined) {
                    document.location = result.url;
                } else {
                    $('#user_area_content').html(result.html);
                    /*$('#openModal').hide();*/
                }
            }
        });
    }
}

function checkTransferEligibility(url, breadcrumb) {
    var transferData = [];
    if (url.trim() != "") {
        $.ajax({
            method: "GET",
            url: url,
            dataType: "json",
            beforeSend: function() {
                /*$('#openModal').show();*/
            },
            success: function(result) {
                if (result.url != undefined) {
                    document.location = result.url;
                } else {
                    if(result.status == 1) {
                        $('#eligibilityError').hide();
                        toggleUserArea(result.transferUrl);

                        $('[userroute]').each(function() {
                            $(this).removeClass('active');
                        });
                        
                        setUserAreaBreadcrumb(breadcrumb);
                    } else {
                        $('#eligibilityError').show();
                    }
                    /*$('#openModal').hide();*/
                }
            }
        });
    }
}
function setUserAreaBreadcrumb(breadcrumb) {
    $('#user-actions [breadcrumb="'+breadcrumb+'"]').addClass('active');
    $('.breadcrumb #breadcrumb').html(breadcrumb);
    $("#user_nav").val($("#user_nav option[breadcrumb='"+breadcrumb+"']").val());
}
function setClassToLastLoadedElement(parentElementId, childElementClass, classToAddRemove, elementPointer) {
    var totalChildElements = $(parentElementId + " " + childElementClass).length;
    var counter = 1;
    $("div[id^='" + elementPointer + "']").find(childElementClass).removeClass(classToAddRemove);
    $(parentElementId + " " + childElementClass).each(function() {
        if (counter == totalChildElements) {
            $(this).addClass(classToAddRemove);
        }
        counter++;
    });
}

function loadMore(btnid, sectionHiddenCounter, parentElementPointer, sectionLastElementClassAddRemove, targetAddRemoveElementClassName) {
    $(btnid).click(function() {
        var currentSectionPointer = $(sectionHiddenCounter).val();
        currentSectionPointer = parseInt(currentSectionPointer) + 1;
        if ($('#' + parentElementPointer + currentSectionPointer).length > 0) {
            if (sectionLastElementClassAddRemove.trim() != "" && targetAddRemoveElementClassName.trim() != "") {
                setClassToLastLoadedElement('#' + parentElementPointer + currentSectionPointer, targetAddRemoveElementClassName, sectionLastElementClassAddRemove, parentElementPointer);
            }
            $('#' + parentElementPointer + currentSectionPointer).show();
        }
        $(sectionHiddenCounter).val(currentSectionPointer);
        if ($("div[id^='" + parentElementPointer + "']").length == currentSectionPointer) {
            $(btnid).hide();
        }
    });
}

function defaultLoadMore(sectionHiddenCounter, defaultLoadSection, parentElementPointer, btnid) {
    $(sectionHiddenCounter).val(defaultLoadSection);
    if ($('#' + parentElementPointer + defaultLoadSection).length > 0) {
        $('#' + parentElementPointer + defaultLoadSection).show();
        var currentSectionPointer = $(sectionHiddenCounter).val();
        if ($("div[id^='" + parentElementPointer + "']").length <= currentSectionPointer) {
            $(btnid).hide();
        }
    }
}

$(function() {
    $('nav#menu').mmenu();
});

//right offer sidebar
function descshow() {
    showdesc();
}

function deschide() {
    hidedesc();
}

var cities = '';
function citiesList(path) {
   /* var url = path + "/CityLoader/countriesToCities.json"
    $.ajax({
        method: "GET",
        url: url,
        dataType: "json",
        success: function(result) {
            cities = result;
        }
    });*/
}
function autocityLoaderOnReady() {
    // set country code
    $.get("https://ipinfo.io", function (response) {
        $("#country_code").text(response.country);
    }, "jsonp");
    // End set country code
    // Auto-City drop down for pop up Header
    $("#city").keypress(function (e) {
        $("div.pac-container").css("z-index", "");
        $("div.pac-container").css("display", "none");
        autoCityLoder('city');
        $("div.pac-container").addClass("city_loader");
        $("div.city_loader").css("z-index", "400000 !important");
        $("div.city_loader").css("display", "block !important");
    });
    // Auto-City drop down for pop up Product
    $("#box_city").keypress(function (e) {
        $("div.pac-container").css("z-index", "");
        $("div.pac-container").css("display", "none");
        autoCityLoder('box_city');
        $("div.pac-container").addClass("box_city_loader");
        $("div.box_city_loader").css("z-index", "400000 !important");
        $("div.box_city_loader").css("display", "block !important");
    });
}

function doFacebookLogin(facebookUrl, tokenGenerate) {
    FB.login(function (response) {
        if (response.authResponse) {
            FB.api('/me?fields=id,first_name,last_name,email,gender,locale,picture', function (response) {
                var url = facebookUrl;
                FB.getLoginStatus(function (response) {
                    if (response.status === 'connected') {
                        var uid = response.authResponse.userID;
                        var accessToken = response.authResponse.accessToken;
                        $.ajax({
                            method: "POST",
                            url: tokenGenerate,
                            data: {accessToken: accessToken},
                            success: function (result) {
                                if (result == 1) {
                                    window.location = url;
                                }
                            }
                        });
                    } else if (response.status === 'not_authorized') {
                        // console.log("Please check credentials");
                    } else {
                        // the user isn't logged in to Facebook.
                    }
                });
                /* FB.logout(function(response) {
                 });*/
            });
        } else {
            // console.log('User cancelled login');
        }
    }, {scope: 'email,user_friends'});
}

function showLoading() {
    $("#loader_image_load_more").show();
}
function hideLoading() {
    $("#loader_image_load_more").hide();
}

// validating email address
function validateCustomEmail(sEmail) {
    return /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/.test(sEmail);
}
//password string checking function
function checkStrength(password, result) {
    var strength = 0;
    if (password.length < 6) {
        $(result).removeClass();
        $(result).addClass('short');
        return 'Demasiado corto'
    }
    if (password.length > 7) strength += 1;
    if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) strength += 1;
    if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/)) strength += 1;
    if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1;
    if (password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1;
    if (strength < 2) {
        $(result).removeClass();
        $(result).addClass('weak');
        return 'Contraseña débil'
    } else if (strength == 2) {
        $(result).removeClass();
        $(result).addClass('good');
        return 'Bueno'
    } else {
        $(result).removeClass();
        $(result).addClass('strong');
        return 'Fuerte'
    }
}
$(document).ready(function() {

    function fbLoginInitialization(facebook_client_id) {
        window.fbAsyncInit = function () {
            FB.init({
                appId: facebook_client_id,
                xfbml: true,
                version: 'v2.1'
            });

        };
        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    }

    /******* Mini privacy policy popup start******/
        if(!getCookie("first_load")) {
            $("#small-policy-popup").show("slow");
            setSessionCookie("first_load",1);
        } else {
            $("#small-policy-popup").hide();
        }

        $(document).on("click","#cls-btn-policy",function(){
          $(this).closest("div#small-policy-popup").hide();
        });
    /******* Mini privacy policy popup start******/

      $(".popup-close").click(function(e) {
        $("#gotostore_login").hide();
        e.stopPropagation();
    });
        
    $('#gotostore_login .popup_wrapper_login').click(function(e){
         $("#gotostore_login").show();
        e.stopPropagation();
     });
     $('#gotostore_login .dis-tbl').click(function(e){
        $("#gotostore_login").hide();
        e.stopPropagation();
     });
    // autocity loader
    autocityLoaderOnReady();
    //reset scroll to initial position on show condition
    $(".conditions-txt span").hover(function(){
      $(this).find(".offer-box-hover").scrollTop(0);
    });
    //slide up on home page down arrow key click slide
    $('.cashback-condition-arrow').click(function(){
      var top = $(this).closest('.offer-box-hover').scrollTop();
      top = top + 60;
        $(this).closest('.offer-box-hover').animate({ //animate element that has scroll
            scrollTop: top //for scrolling
        }, 100);
    });
    //slide up on home page down arrow key hover slide down button
    function slide(obj) {
      var top = $(obj).closest('.offer-box-hover').scrollTop();
      top = top + 30;
        $(obj).closest('.offer-box-hover').animate({ //animate element that has scroll
            scrollTop: top //for scrolling
        }, 500);
    }
    var timer;
    $('.cashback-condition-arrow').hover(function(){
      timer = setInterval(slide, 500 ,this);
    }, function() {
      clearInterval(timer);
    });
    //banner title description
    var showChar = 300; // How many characters are shown by default
    var ellipsestext = "...";
    var moretext = "Ver más";
    var lesstext = "Ver menos";
    $('.more_banner_description').each(function() {
        var content = $(this).html();
        if (content.length > showChar) {
            var c = content.substr(0, showChar);
            var h = content.substr(showChar, content.length - showChar);
            var html = c + '<span class="moreellipses">' + ellipsestext + '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';
            $(this).html(html);
        }
    });
    
    $(".morelink").click(function() {
        if ($(this).hasClass("less")) {
            $(this).removeClass("less");
            $(this).html(moretext);
        } else {
            $(this).addClass("less");
            $(this).html(lesstext);
        }
        $(this).parent().prev().toggle();
        $(this).prev().toggle();
        return false;
    });
    // end banner title description
    
    $("#acceder").on("click", function() {
        $(".registration_header").hide("slow");
        $(".login_header").show("slow");
    });

    $("#registration_link").on("click", function() {
        $(".login_header").hide("slow");
        $(".registration_header").show("slow");
    });

    //right offer sidebar
    if ($('.actual-description').text().length < 50) {
        $('.viewmore').hide();
        $('.viewless').hide();
    }

    window.showdesc = function() {
        $('.fulldescription').show();
        $('.viewless').show();
        $('.lessdescription').hide();
        $('.viewmore').hide();
    };
    window.hidedesc = function() {
        $('.fulldescription').hide();
        $('.viewless').hide();
        $('.lessdescription').show();
        $('.viewmore').show();
    };

    //Marcas view page show/hide description
    $("span.brand-desc-show-more a").on("click", function() {
        var $that = $(this);
        if ($(this).hasClass("show-more-desc")) {
            $(".brand-desc").animate({
                "height": "100%"
            }, 100, function() {
                $that.html("Ver menos");
                $that.attr("title", "Ver menos");
                $that.addClass("show-less-desc").removeClass("show-more-desc");
            });
        } else if ($(this).hasClass("show-less-desc")) {
            $(".brand-desc").animate({
                "height": "54px"
            }, 500, function() {
                $that.html("Ver más");
                $that.attr("title", "Ver más");
                $that.addClass("show-more-desc").removeClass("show-less-desc");
            });
        }
    });

    //forget password
    $('#forgot_password').keyup(function() {
        $('#forgot_password_result').text(checkStrength($(this).val(), "#forgot_password_result"));
    });
    $("#forgot_repeat_password").focusout(function() {
        var repeat_password = $(this).val();
        var password = document.getElementById("forgot_password").value;
        if (repeat_password != password) {
            $("#forgot_password_result").text("La contraseña introducida no coincide con la contraseña confirmada");
            return false;
        } else {
            $("#forgot_password_result").text("");
            return true;
        }
    });
    $("#forgot_password_submit").click(function(e) {
        var repeat_password = document.getElementById("forgot_repeat_password").value;
        var password = document.getElementById("forgot_password").value;
        if (repeat_password != password) {
            e.preventDefault();
        }
    });
    
    //top registration pop up credentials start
    $(".profile").hide();
    $("#profile-down-arrow").click(function() {
        $(".profile").toggle();
    });
    $("#login").click(function(e) {
        $("#registration-dropdown").hide("slow");
        $(".social-login-sub").slideToggle("slow", function() {});
        e.stopPropagation();
    });
    $("#registration").click(function(e) {
        $(".social-login-sub").hide("slow");
        $("#registration-dropdown").slideToggle("slow", function() {
            $('.ui-autocomplete').css('position', 'absolute');
            $('.ui-autocomplete').css('cursor', 'default');
        });
        e.stopPropagation();
    });
    $("#registration-dropdown").click(function(e) {
        e.stopPropagation();
    });
    $(".social-login-sub").click(function(e) {
        e.stopPropagation();
    });
    $("#ui-id-1").click(function(e) {
        e.stopPropagation();
    });

    $('#reg_submit').click(function(e) {
        var sEmail = $('.registration-form #email').val();
        var repeat_password = document.getElementById("repeat_password").value;
        var password = document.getElementById("password_reg").value;
        if (validateCustomEmail(sEmail) == false && password != repeat_password) {
            $('#reg_email_result').text("Invalid Email");
            $("#result").text("La contraseña introducida no coincide con la contraseña confirmada");
            $('#reg_email_result').addClass('short');
            $(".registration_header").show();
            e.preventDefault();
        } else if (validateCustomEmail(sEmail) == false) {
            $('#reg_email_result').text("Invalid Email");
            $('#reg_email_result').addClass('short');
            $(".registration_header").show();
            e.preventDefault();
        } else if (password != repeat_password) {
            $("#result").text("La contraseña introducida no coincide con la contraseña confirmada");
            $(".registration_header").show();
            e.preventDefault();
        } else {
            $('#reg_email_result').text("");
            $("#result").text("");
            return true;
        }
    });

    $('#box_reg_submit').click(function(e) {
        var sEmail = $('.regi-maxrow-inner #email').val();
        if (validateCustomEmail(sEmail)) {
            $('#popup_email_result').text("Valid Email");
            $('#popup_email_result').addClass('strong');
        } else {
            $('#popup_email_result').text("Invalid Email");
            $('#popup_email_result').addClass('short');
            e.preventDefault();
        }
    });

    $('#page_reg_submit').click(function(e) {
        var sEmail = $('#email_page').val();
        if (validateCustomEmail(sEmail)) {
            $('#email_result_page').text("Valid Email");
            $('#email_result_page').addClass('strong');
        } else {
            $('#email_result_page').text("Invalid Email");
            $('#email_result_page').addClass('short');
            e.preventDefault();
        }
    });
    $('#password_reg').keyup(function() {
        $('#result').text(checkStrength($(this).val(), "#result"));
    });
    $("#repeat_password").focusout(function() {
        var repeat_password = $(this).val();
        var password = document.getElementById("password_reg").value;
        if (repeat_password != password) {
            $("#result").text("La contraseña introducida no coincide con la contraseña confirmada");
            return false;
        } else {
            $("#result").text("");
            return true;
        }
    });
    $("#reg_submit").click(function(e) {
        var repeat_password = document.getElementById("repeat_password").value;
        var password = document.getElementById("password_reg").value;
        if (repeat_password != password) {
            e.preventDefault();
        }
    });
    //top registration pop up credentials end
    //product registration pop up credentials start
    $('#box_password_reg').keyup(function() {
        $('#box_result').text(checkStrength($(this).val(), "#box_result"));
    });

    $('#password_reg_page').keyup(function() {
        $('#result_page').text(checkStrength($(this).val(), "#result_page"));
    });
    $(".box_repeat_password").focusout(function() {
        var repeat_password = $(this).val();
        var password = document.getElementById("box_password_reg").value;
        if (repeat_password != password) {
            $(".result").text("La contraseña introducida no coincide con la contraseña confirmada");
            return false;
        } else {
            $(".result").text("");
            return true;
        }
    });
    $("#box_reg_submit").click(function(e) {
        var repeat_password = document.getElementById("box_repeat_password").value;
        var password = document.getElementById("box_password_reg").value;
        if (repeat_password != password) {
            e.preventDefault();
        }
    });
    //product registration pop up credentials end
    $(document).click(function(e) {
        $(".registration_header").hide("slow");
        $(".login_header").hide("slow");
        $("div.pac-container").css("display", "none !important");
        $("#small-policy-popup").hide();
        e.stopPropagation();
    });

    // default menu category selected
    $(".category-sidebar-inner ul li.parent_cat").children("ul").hide();
    $(".category-sidebar-inner ul li.child_cat").children("ul").hide();

    var id = '';
    var class_name = '';
    var middle_cat = '';
    var child_cat = '';
    pathArray = location.href.split('/');
    var catId = "undefined";
    for (i = 0; i < pathArray.length; i++) {
        if (pathArray[i].match("ofertas-")) {
            catId_url = pathArray[i].split('ofertas-');

            if (typeof pathArray[i + 1] != 'undefined') {
                if (pathArray[i + 1] != '') {
                    middle_cat = pathArray[i + 1];
                }
            }
            if (typeof pathArray[i + 2] != 'undefined') {
                if (pathArray[i + 2] != '') {
                    child_cat = pathArray[i + 2];
                }
            }
            catId = catId_url[1];
        }
    }
    var parentclassName = ".parent_category_id_" + catId.replace(/[^0-9a-zA-Z\xC0-\xFF \-]/g, '');
    var parentCategoryElement = $(document).find(parentclassName);
    parentCategoryElement.parent().addClass('active selected');
    if (parentCategoryElement.parent().hasClass("parent_cat")) {
        parentCategoryElement.next().css('display', 'block');
    }

    if (middle_cat != '') {
        var middleclassName = ".category_id_" + middle_cat;
        var middleCategoryElement = $(document).find(middleclassName);
        middleCategoryElement.parent().addClass('active selected');
        if (middleCategoryElement.parent().hasClass("child_cat")) {
            middleCategoryElement.parents("li.parent_cat").removeClass('selected');
            middleCategoryElement.next().css('display', 'block');
        }
    }
    if (child_cat != '') {
        var childclassName = ".child_category_id_" + child_cat;
        var childCategoryElement = $(document).find(childclassName);
        childCategoryElement.parent().addClass('active selected');
        childCategoryElement.parents("li.child_cat").removeClass('selected');
        childCategoryElement.parents("li.parent_cat").removeClass('selected');
    }
    // End default menu category selected

    // Category list page 3 image responsive slider
    setInterval(function() {
        $("#owl-demo").each(function() {
            $("#owl-demo").owlCarousel({
                autoPlay: 3000, //Set AutoPlay to 3 seconds
                items: 1,
                itemsDesktopSmall: [979, 1]
            });
        });
    }, 1000);

    //End Category list page 3 image responsive slider

    $(".brand_grid_view").hide();
    $(".tiendas_grid_view").hide();
    $(".collection_grid_view").hide();
    /* $(".go_to_store_up").click(function(){
         $("#gotostore_login").show();
         $("#gotostore_login ul.social-login-sub").show();
     });*/
    $(".go_to_store_down").click(function() {
        $("#gotostore_login").show();
        $("#gotostore_login ul.social-login-sub").show();
    });

    setInterval(function() {
        $("#owl-banner").each(function() {
            $("#owl-banner").owlCarousel({
                items: 1,
                loop: true,
                margin: 10,
                pagination: true,
                itemsCustom: true,
                singleItem: true,
                itemsScaleUp: false,
                slideSpeed: 5000,
                paginationSpeed: 2000,
                dots: true,
                autoPlay: true,
                autoplay: true,
                smartSpeed: 3000,
                autoplayTimeout: 5000
            });
        });
    }, 1000);

    setInterval(function() {
        $("#owl-banner2").each(function() {
            $("#owl-banner2").owlCarousel({
                items: 3,
                slideBy: 4,
                loop: true,
                itemsDesktop: [1199, 3],
                itemsDesktopSmall: [980, 3],
                itemsTablet: [768, 2],
                itemsTabletSmall: false,
                itemsMobile: [479, 1],
                itemsCustom: false,
                singleItem: false,
                itemsScaleUp: false,
                pagination: false,
                nav: true,
                dots: true,
                margin: 10,
                autoPlay: false,
                navigation: true,
                responsive: {
                    0: { items: 1},
                    480: { items: 1},
                    768: { items: 2},
                    980: { items: 3},
                    1199: { items: 3}
                }
            });
        });
    }, 1000);

    setInterval(function() {
        $("#owl-banner12").each(function() {
            $("#owl-banner12").owlCarousel({
                items: 3,
                itemsDesktop: [1199, 3],
                itemsDesktopSmall: [980, 3],
                itemsTablet: [768, 2],
                itemsTabletSmall: false,
                itemsMobile: [479, 1],
                itemsCustom: false,
                singleItem: false,
                itemsScaleUp: false,
                pagination: false,
                nav: true,
                margin: 10,
                dots: true,
                autoPlay: false,
                navigation: true,
                responsive: {
                    0: { items: 1},
                    480: { items: 1},
                    768: { items: 2},
                    980: { items: 3},
                    1199: { items: 3}
                }
            });
        });
    }, 1000);

    setInterval(function() {
        $("#owl-banner13").each(function() {
            $("#owl-banner13").owlCarousel({
                items: 3,
                itemsDesktop: [1199, 3],
                itemsDesktopSmall: [980, 3],
                itemsTablet: [768, 2],
                itemsTabletSmall: false,
                itemsMobile: [479, 1],
                itemsCustom: false,
                singleItem: false,
                itemsScaleUp: false,
                pagination: false,
                nav: true,
                margin: 10,
                dots: true,
                autoPlay: false,
                navigation: true,
                responsive: {
                    0: { items: 1},
                    480: { items: 1},
                    768: { items: 2},
                    980: { items: 3},
                    1199: { items: 3}
                }
            });
        });
    }, 1000);

    setInterval(function() {
        $("#owl-banner14").each(function() {
            $("#owl-banner14").owlCarousel({
                items: 3,
                itemsDesktop: [1199, 3],
                itemsDesktopSmall: [980, 3],
                itemsTablet: [768, 2],
                itemsTabletSmall: false,
                itemsMobile: [479, 1],
                itemsCustom: false,
                singleItem: false,
                itemsScaleUp: false,
                pagination: false,
                nav: true,
                margin: 10,
                dots: true,
                autoPlay: false,
                navigation: true,
                responsive: {
                    0: { items: 1},
                    480: { items: 1},
                    768: { items: 2},
                    980: { items: 3},
                    1199: { items: 3}
                }
            });
        });
    }, 1000);

    setInterval(function() {
        $("#owl-shoes-top").each(function() {
            $("#owl-shoes-top").owlCarousel({
                items: 3,
                itemsDesktop: [1199, 3],
                itemsDesktopSmall: [980, 3],
                itemsTablet: [768, 2],
                itemsTabletSmall: false,
                itemsMobile: [479, 1],
                itemsCustom: false,
                singleItem: false,
                itemsScaleUp: false,
                pagination: false,
                nav: true,
                margin: 10,
                dots: true,
                autoPlay: false,
                navigation: true,
                responsive: {
                    0: { items: 1},
                    480: { items: 1},
                    768: { items: 2},
                    980: { items: 3},
                    1199: { items: 3}
                }
            });
        });
    }, 1000);

    setInterval(function() {
        $("#owl-shoes").each(function() {
            $("#owl-shoes").owlCarousel({
                items: 4,
                itemsDesktop: [1199, 4],
                itemsDesktopSmall: [980, 3],
                itemsTablet: [768, 2],
                itemsTabletSmall: false,
                itemsMobile: [479, 1],
                itemsCustom: false,
                singleItem: false,
                itemsScaleUp: false,
                pagination: false,
                autoPlay: false,
                nav: true,
                dots: true,
                navigation: true,
                responsive: {
                    0: { items: 1},
                    480: { items: 2},
                    768: { items: 2},
                    980: { items: 4},
                    1199: { items: 4}
                }
            });
        });
    }, 1000);

    setInterval(function() {
        $("#owl-review").each(function() {
            $("#owl-review").owlCarousel({
                // Most important owl features
                items: 4,
                itemsDesktop: [1199, 4],
                itemsDesktopSmall: [980, 3],
                itemsTablet: [768, 2],
                itemsTabletSmall: false,
                itemsMobile: [479, 1],
                itemsCustom: false,
                singleItem: false,
                itemsScaleUp: false,
                pagination: false,
                nav: true,
                autoPlay: false,
                dots: true,
                navigation: true,
                responsiveClass: true,
                responsive: {
                    0: { items: 1},
                    480: { items: 2},
                    768: { items: 2},
                    980: { items: 4},
                    1199: { items: 4}
                }
            });
        });
    }, 1000);

    $(".destacard-tabing ul li a").click(function(event) {
        event.preventDefault();
        $(this).parent().addClass("current");
        $(this).parent().siblings().removeClass("current");
        var tab = $(this).attr("href");
        $(".tab-inner").not(tab).css("display", "none");
        $(tab).fadeIn();
    });

    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $('.scrollToTop').fadeIn();
        } else {
            $('.scrollToTop').fadeOut();
        }
    });

    //Click event to scroll to top
    $('.scrollToTop').click(function() {
        $('html, body').animate({
            scrollTop: 0
        }, 800);
        return false;
    });
    /*$(".offer-box-hover").hide();
    $(".offer-box .conditions-txt span").hover(function(event) {
          $('.offer-box-link').find($(".offer-box-link .offer-box-hover")).show();
      });*/

    $(".offer-box-link .conditions-txt span").hover(function() {
        $(this).find($(".offer-box-link .offer-box-hover")).toggleClass("open-hover");
    });
    $(".shoes-link .sharing-txt span").hover(function() {
        $(this).find($(".shoes-link .offer-box-hover")).toggleClass("open-hover");
    });


    $(".social-register").click(function() {
        $(this).toggleClass("soc-open1");
    })

    $(".social-login").click(function() {
        $(this).toggleClass("soc-open1");
    })



    /*$('#tab-content > div').hide();
       $('#tab-content > div:first').show();
         $('#tab-content > div:first').next().show();
     $('#tab-nav li').click(function(event) {
         event.preventDefault();
         $('#tab-nav li a').removeClass("active");
         $(this).find('a').addClass("active");
         $('#tab-content > div').hide();
          $('#tab-content > div').next(div).hide();

         var indexer = $(this).index(); //gets the current index of (this) which is #nav li
         $('#tab-content div:eq(' + indexer + ')').fadeIn(); //uses whatever index the link has to open the corresponding box */

    $("[id^='parent_cat_']").hover(function() {
        var parent_cat_id = this.id;
        var id = parent_cat_id.replace('parent_cat_', '#cat_');
        var imageId = $(this).parent().attr('id').replace('parentcatimage_', '#parent_cat_image_');
        $('.inner-menu .gift-shop-image').hide();
        $('.category').hide();
        $('.child-category').hide();
        $(id).show();
        $(imageId).show();
    });

    $("[id^='category_menu_']").hover(function() {
        var child_cat_id = this.id;
        var id = child_cat_id.replace('category_menu_', '#childcat_');
        var imageId = $(this).parent().attr('id').replace('categoryimage_', '#cat_image_');

        $('.inner-menu .gift-shop-image').hide();
        $('.child-category').hide();
        $(id).show();
        $(imageId).show();
    });


    $("[id^='childcatimage_']").hover(function() {
        var source_id = this.id;
        var id = source_id.replace('childcatimage_', '#childcat_image_');
        $('.inner-menu .gift-shop-image').hide();
        $(id).show();
    });

    $(".popup-close").click(function() {
        $("#gotostore_login").hide();
    });

    // Display Default Menu Category Image
    $("#default_cat_id").hover(function() {
        $('.inner-menu .gift-shop-image').hide();
        $('.category').hide();
        $('.child-category').hide();
        $("#default_cat_image").show();
    });

    // Display Default Menu Ofertas Image
    $("#ofertas_menu").hover(function() {
        $('.inner-menu .gift-shop-image').hide();
        $("#offertas_default_image").show();
    });
    /* Start code to display brand voucher section by default and show more click functionality*/
    var sectionHiddenCounter = '#voucher_section_counter';
    var defaultLoadSection = 1;
    var parentElementPointer = "voucher_section_";
    var btnid = "#voucher_view_more";
    var targetAddRemoveElementClassName = ".brand-cupon-row";
    var sectionLastElementClassAddRemove = "lst-row";

    /* Start Code to load default section of brand voucher if exist */
    defaultLoadMore(sectionHiddenCounter, defaultLoadSection, parentElementPointer, btnid);
    /* End Code to load default section of brand voucher if exist */

    /* Start Added class to the last part of the loading section */
    var lastSectionId = $("[id^='" + parentElementPointer + "']").length - 1;
    setClassToLastLoadedElement('#' + parentElementPointer + lastSectionId, targetAddRemoveElementClassName, sectionLastElementClassAddRemove, parentElementPointer);
    /* End Added class to the last part of the loading section */

    /* Start Code to add load more sections of brand voucher section */
    loadMore(btnid, sectionHiddenCounter, parentElementPointer, sectionLastElementClassAddRemove, targetAddRemoveElementClassName);
    /* End Code to add load more sections of brand voucher section */

    /* End code to display brand voucher section by default and show more click functionality*/


    /* Start code to display brand shop section by default and show more click functionality*/

    var sectionHiddenCounter = '#shop_section_counter';
    var defaultLoadSection = 1;
    var parentElementPointer = "shop_section_";
    var btnid = "#shop_view_more";
    var targetAddRemoveElementClassName = "";
    var sectionLastElementClassAddRemove = "";

    /* Start Code to load default section of brand shop if exist */
    defaultLoadMore(sectionHiddenCounter, defaultLoadSection, parentElementPointer, btnid);
    /* End Code to load default section of brand shop if exist */

    /* Start Code to add load more sections of brand shop section */
    loadMore(btnid, sectionHiddenCounter, parentElementPointer, sectionLastElementClassAddRemove, targetAddRemoveElementClassName);
    /* End Code to add load more sections of brand shop section */

    /* End code to display brand shop section by default and show more click functionality*/


    $('#scroll-to-cond-button').click(function() {
        $('html, body').animate({
            scrollTop: $("#conditions-section").offset().top
        }, 1000);
    });

    $('#go-to-shop').click(function() {
        $('html, body').animate({
            scrollTop: $("#brand-cupon-discount-section").offset().top
        }, 1000);
    });

    $("[id^='marca_']").hover(function() {
        var source_id = this.id;
        var related_brand_id = source_id.replace('marca_', '#related_brand_');
        var related_shop_id = source_id.replace('marca_', '#related_shop_');
        $('.related-marcaas').hide();
        $('.related-shops').hide();
        $(related_brand_id).show();
        $(related_shop_id).show();
    });

    // display first section of shops on brand page by default 
    $('#product_section_counter').val(1);
    if ($('#product_section_1').length > 0) {
        $('#product_section_1').show();
        var currentSectionPointer = $('#product_section_counter').val();
        /* if($("div[id^='product_section_']").length <= currentSectionPointer){
             $('#product_view_more').hide();
         }*/
    }

    /*   $('#product_view_more').click(function(){

           var currentSectionPointer = $('#product_section_counter').val();
           currentSectionPointer = parseInt(currentSectionPointer) + 1;
           if($('#product_section_'+currentSectionPointer).length > 0){
               $('#product_section_'+currentSectionPointer).show();
           }   
           $('#product_section_counter').val(currentSectionPointer);

           if($("div[id^='product_section_']").length == currentSectionPointer){
               $('#product_view_more').hide();
           }
       });*/
    $('#bestcashback_section_counter').val(1);
    if ($('#bestcashback_section_1').length > 0) {
        $('#bestcashback_section_1').show();
        var currentSectionPointer = $('#bestcashback_section_counter').val();
        /*  if($("div[id^='bestcashback_section_']").length <= currentSectionPointer){
              $('#bestcashback_view_more').hide();
          }*/
    }

    /*$('#bestcashback_view_more').click(function(){
     
        var currentSectionPointer = $('#bestcashback_section_counter').val();
        currentSectionPointer = parseInt(currentSectionPointer) + 1;
        if($('#bestcashback_section_'+currentSectionPointer).length > 0){
            $('#bestcashback_section_'+currentSectionPointer).show();
        }   
        $('#bestcashback_section_counter').val(currentSectionPointer);

        if($("div[id^='bestcashback_section_']").length == currentSectionPointer){
            $('#bestcashback_view_more').hide();
        }
    });*/
    $('#bestcoupons_section_counter').val(1);
    if ($('#bestcoupons_section_1').length > 0) {
        $('#bestcoupons_section_1').show();
        var currentSectionPointer = $('#bestcoupons_section_counter').val();
        /*   if($("div[id^='bestcoupons_section_']").length <= currentSectionPointer){
               $('#bestcoupons_view_more').hide();
           }*/
    }

    /*  $('#bestcoupons_view_more').click(function(){
       
          var currentSectionPointer = $('#bestcoupons_section_counter').val();
          currentSectionPointer = parseInt(currentSectionPointer) + 1;
          if($('#bestcoupons_section_'+currentSectionPointer).length > 0){
              $('#bestcoupons_section_'+currentSectionPointer).show();
          }   
          $('#bestcoupons_section_counter').val(currentSectionPointer);

          if($("div[id^='bestcoupons_section_']").length == currentSectionPointer){
              $('#bestcoupons_view_more').hide();
          }
      });*/
    //end display first section of shops on brand page by default 

    $('#category_section_counter').val(1);
    if ($('#category_section_1').length > 0) {
        $('#category_section_1').show();
        var currentSectionPointer = $('#category_section_counter').val();
        if ($("div[id^='category_section_']").length <= currentSectionPointer) {
            $('#category_view_more').hide();
        }
    }

    $('#category_view_more').click(function() {

        var currentSectionPointer = $('#category_section_counter').val();
        currentSectionPointer = parseInt(currentSectionPointer) + 1;
        if ($('#category_section_' + currentSectionPointer).length > 0) {
            $('#category_section_' + currentSectionPointer).show();
        }
        $('#category_section_counter').val(currentSectionPointer);

        if ($("div[id^='category_section_']").length == currentSectionPointer) {
            $('#category_view_more').hide();
        }
    });

    $('#brand_section_counter').val(1);
    if ($('#brand_section_1').length > 0) {
        $('#brand_section_1').show();
        var currentSectionPointer = $('#brand_section_counter').val();
        if ($("div[id^='brand_section_']").length <= currentSectionPointer) {
            $('#brand_view_more').hide();
        }
    }

    $('#brand_view_more').click(function() {

        var currentSectionPointer = $('#brand_section_counter').val();
        currentSectionPointer = parseInt(currentSectionPointer) + 1;
        if ($('#brand_section_' + currentSectionPointer).length > 0) {
            $('#brand_section_' + currentSectionPointer).show();
        }
        $('#brand_section_counter').val(currentSectionPointer);

        if ($("div[id^='brand_section_']").length == currentSectionPointer) {
            $('#brand_view_more').hide();
        }
    });


    $('#category_side_menu_counter').val(1);
    if ($('#category_side_menu_1').length > 0) {
        $('#category_side_menu_1').show();
        var currentSectionPointer = $('#category_side_menu_counter').val();
        if ($("div[id^='category_side_menu_']").length <= currentSectionPointer) {
            $('#cat_left_menu_view_more').hide();
        }
    }

    $('#cat_left_menu_view_more').click(function() {

        var currentSectionPointer = $('#category_side_menu_counter').val();
        currentSectionPointer = parseInt(currentSectionPointer) + 1;
        if ($('#category_side_menu_' + currentSectionPointer).length > 0) {
            $('#category_side_menu_' + currentSectionPointer).show();
        }
        $('#category_side_menu_counter').val(currentSectionPointer);

        if ($("div[id^='category_side_menu_']").length == currentSectionPointer) {
            $('#cat_left_menu_view_more').hide();
        }
    });


    $('#tiendas_section_counter').val(1);
    if ($('#tiendas_section_1').length > 0) {
        $('#tiendas_section_1').show();
        var currentSectionPointer = $('#tiendas_section_counter').val();
        if ($("div[id^='tiendas_section_']").length <= currentSectionPointer) {
            $('#tiendas_view_more').hide();
        }
    }

    $('#tiendas_view_more').click(function() {

        var currentSectionPointer = $('#tiendas_section_counter').val();
        currentSectionPointer = parseInt(currentSectionPointer) + 1;
        if ($('#tiendas_section_' + currentSectionPointer).length > 0) {
            $('#tiendas_section_' + currentSectionPointer).show();
        }
        $('#tiendas_section_counter').val(currentSectionPointer);

        if ($("div[id^='tiendas_section_']").length == currentSectionPointer) {
            $('#tiendas_view_more').hide();
        }
    });

    $('#collection_section_counter').val(1);
    if ($('#collection_section_1').length > 0) {
        $('#collection_section_1').show();
        var currentSectionPointer = $('#collection_section_counter').val();
        if ($("div[id^='collection_section_']").length <= currentSectionPointer) {
            $('#collection_view_more').hide();
        }
    }

    $('#collection_view_more').click(function() {

        var currentSectionPointer = $('#collection_section_counter').val();
        currentSectionPointer = parseInt(currentSectionPointer) + 1;
        if ($('#collection_section_' + currentSectionPointer).length > 0) {
            $('#collection_section_' + currentSectionPointer).show();
        }
        $('#collection_section_counter').val(currentSectionPointer);

        if ($("div[id^='collection_section_']").length == currentSectionPointer) {
            $('#collection_view_more').hide();
        }
    });

    //end display first section of shops on brand page by default 
    // social share

    (function() {
        var po = document.createElement('script');
        po.type = 'text/javascript';
        po.async = true;
        po.src = 'https://apis.google.com/js/plusone.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(po, s);
    })();
    //


    /* Ofertas menu hover content change code start */

    $("#cashback_voucher").hover(function() {
        $(".offer-menu-image").hide();
        $("#cashback_voucher_content").show();
    });

    $("#discount_offer").hover(function() {
        $(".offer-menu-image").hide();
        $("#discount_offer_content").show();
    });

    // Showing random cashback or discount on each hover of menu start
    $('#ofertas_menu').hover(function() {
        var elementIdArray = ["#cashback_voucher_content", "#discount_offer_content"];
        var element = elementIdArray[Math.floor(elementIdArray.length * Math.random())];
        $(".offer-menu-image").hide();
        $(element).show();
    });
    // Showing random cashback or discount on each hover of menu end

    /* Ofertas menu hover content change code end */



    /* Code start to hide condition and intro section on marcas page if there is no cashback */
    if ($('#no-cashback').length > 0) {
        $('#cashback_detail_content').hide();
    }
    /* Code end to hide condition and intro section on marcas page if there is no cashback */


    /* Start code to display brand review section by default and show more click functionality*/

    var sectionHiddenCounter = '#brand_review_section_counter';
    var defaultLoadSection = 1;
    var parentElementPointer = "brand_review_section_";
    var btnid = "#brand_review_view_more";
    var targetAddRemoveElementClassName = ".opinion-block-inner";
    var sectionLastElementClassAddRemove = "bor-lst";

    /* Start Code to load default section of brand review if exist */
    defaultLoadMore(sectionHiddenCounter, defaultLoadSection, parentElementPointer, btnid);
    /* End Code to load default section of brand review if exist */

    /* Start Added class to the last part of the loading section */
    setClassToLastLoadedElement('#' + parentElementPointer + defaultLoadSection, targetAddRemoveElementClassName, sectionLastElementClassAddRemove, parentElementPointer);
    /* End Added class to the last part of the loading section */

    /* Start Code to add load more sections of brand review section */
    loadMore(btnid, sectionHiddenCounter, parentElementPointer, sectionLastElementClassAddRemove, targetAddRemoveElementClassName);
    /* End Code to add load more sections of brand review section */

    /* End code to display brand review section by default and show more click functionality*/

    //brand list page grid view show more
    $('#brand_list_grid_section_counter').val(1);
    if ($('#brand_grid_section_1').length > 0) {
        $('#brand_grid_section_1').show();
        var currentSectionPointer = $('#brand_list_grid_section_counter').val();
        if ($("div[id^='brand_grid_section_']").length <= currentSectionPointer) {
            $('#brand_view_more').hide();
        }
    }

    $('#brand_view_more').click(function() {

        var currentSectionPointer = $('#brand_list_grid_section_counter').val();
        currentSectionPointer = parseInt(currentSectionPointer) + 1;
        if ($('#brand_grid_section_' + currentSectionPointer).length > 0) {
            $('#brand_grid_section_' + currentSectionPointer).show();
        }
        $('#brand_list_grid_section_counter').val(currentSectionPointer);

        if (($("div[id^='brand_grid_section_']").length * 2) == currentSectionPointer) {
            $('#brand_view_more').hide();
        }
    });

    $('#tiendas_list_grid_section_counter').val(1);
    if ($('#tiendas_grid_section_1').length > 0) {
        $('#tiendas_grid_section_1').show();
        var currentSectionPointer = $('#tiendas_list_grid_section_counter').val();
        if ($("div[id^='tiendas_grid_section_']").length <= currentSectionPointer) {
            $('#tiendas_view_more').hide();
        }
    }

    $('#tiendas_view_more').click(function() {

        var currentSectionPointer = $('#tiendas_list_grid_section_counter').val();
        currentSectionPointer = parseInt(currentSectionPointer) + 1;
        if ($('#tiendas_grid_section_' + currentSectionPointer).length > 0) {
            $('#tiendas_grid_section_' + currentSectionPointer).show();
        }
        $('#tiendas_list_grid_section_counter').val(currentSectionPointer);

        if (($("div[id^='tiendas_grid_section_']").length * 2) == currentSectionPointer) {
            $('#tiendas_view_more').hide();
        }
    });

    $('#collection_list_grid_section_counter').val(1);
    if ($('#collection_grid_section_1').length > 0) {
        $('#collection_grid_section_1').show();
        var currentSectionPointer = $('#collection_list_grid_section_counter').val();
        if ($("div[id^='collection_grid_section_']").length <= currentSectionPointer) {
            $('#collection_view_more').hide();
        }
    }

    $('#collection_view_more').click(function() {

        var currentSectionPointer = $('#collection_list_grid_section_counter').val();
        currentSectionPointer = parseInt(currentSectionPointer) + 1;
        if ($('#collection_grid_section_' + currentSectionPointer).length > 0) {
            $('#collection_grid_section_' + currentSectionPointer).show();
        }
        $('#collection_list_grid_section_counter').val(currentSectionPointer);

        if (($("div[id^='collection_grid_section_']").length * 2) == currentSectionPointer) {
            $('#collection_view_more').hide();
        }
    });
    /* User Area page JS start here */

    /*set default selected option as profile on load start*/
    $("#user_nav").val($("#user_nav option[id='profile']").val());
    /*set default selected option as profile on load end*/
    
    $(document).on('change', '#user_nav', function() {
        var url = "";
        $('[userroute]').each(function() {
            $(this).removeClass('active');
        });

        var opt = $("#"+$(this).attr('id') +" option:selected").attr('breadcrumb');        
        $('#breadcrumb').html(opt);
        $('a[breadcrumb="'+opt+'"]').addClass('active');
        url = $(this).val();
        toggleUserArea(url);
    });

    $(document).on('click', '[userroute]', function() {
        var breadcrumb  = $(this).attr('breadcrumb');
        var url         = $(this).attr('userroute');
        setSessionCookie('userroute_url', url);

        if(breadcrumb == "transferir mi dinero") {
            checkTransferEligibility(url, breadcrumb);
        } else {
            toggleUserArea(url);
            $('[userroute]').each(function() {
                $(this).removeClass('active');
            });
            
            
            setUserAreaBreadcrumb(breadcrumb);
        }



    });

    $(document).on('click', '[id^="fav_"]', function() {
        var url = $(this).attr('data-url');
        var fav_id = $(this).attr('id');
        var id = fav_id.replace('fav_', '');
        var routeurl = $(this).attr("user-route");
        
        $.ajax({
            method: "POST",
            url: url,
            dataType: "json",
            data: {
                id: id
            },
            beforeSend: function() {
                /*$('#openModal').show();*/
            },
            success: function(result) {
                /*$('#openModal').hide();*/
                toggleUserArea(routeurl);
            }
        });
    });

    $(document).on("click", "#save-bankdetail", function() {
        if($(this).is(':checked')) {
            $("#user-transferamount-edit-form").removeAttr("novalidate");
        } else {
            $("#user-transferamount-edit-form").attr("novalidate","novalidate");
        }
    });
    
    $(document).on("submit", "#user-transferamount-edit-form",function(event){
        event.preventDefault();
        var url = $(this).attr('action');
        $.ajax({
            method: "POST",
            url: url,
            data: {
                data: $(this).serialize()
            },
            dataType: "json",
            beforeSend: function() {
                
            },
            success: function(result) {
                toggleUserArea(result.url);
                setUserAreaBreadcrumb("Mi Cashback");
            }
        });

    });

    /* User Area page JS end here */
    $(document).on("change", "#user_history_type", function() {
        var url = $(this).attr('data-url');
        var selectedStatus = $(this).val();
        $.ajax({
            method: "POST",
            url: url,
            dataType: "json",
            data: {
                requestedStatus: selectedStatus
            },
            beforeSend: function() {
            },
            success: function(result) {
                if (result.url != undefined) {
                    document.location = result.url;
                } else {
                    $('#user_area_content').html(result.html);
                }
            }
        });
    });

    /* --- Contact Form :: Validation Start --- */
    function validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

    function erroDialog(check) {
        var content = "";
        if (check == 0) {
            content = 'Por favor, rellena todos los campos marcados con *.';
        } else if (check == 1) {
            content = 'Por favor, comprueba que tu email es correcto.';
        }
        $('#dialog').dialogBox({
            hasClose: true,
            effect: 'fade',
            confirm: function() {
                $('#message').dialogBox({
                    title: 'Hello Word',
                    content: 'I am a dialogBox',
                    hasClose: true
                })
            },
            title: ' ',
            content: content,
            callback: function() {
                console.log('loading over')
            }
        })
    }

    $("#submitContact").on('click', function() {
        if ($('#00N2400000BQjuM').val() == "" || $('#00N2400000BQjuM').val() == null) {
            erroDialog(0);
            return false;
        }
        if ($('#00N2400000ByrAj').val() == "" || $('#00N2400000ByrAj').val() == null) {
            erroDialog(0);
            return false;
        }
        var name = $("#name").val();
        if (name == "" || name == null) {
            erroDialog(0);
            return false;
        }
        var email = $(".contactEmail").val();

        if (!validateEmail(email)) {
            erroDialog(1);
            return false;
        }
        var description = $("#description").val();
        if (description == "" || description == null) {
            erroDialog(0);
            return false;
        }

        var tienda = $("#00N2400000ByrAg").val();
        if (tienda == "" || tienda == null) {
            erroDialog(0);
            return false;
        }
        return true;
    })
    var myOptions = {};
    var cashbackOptions = {
        '_cómo_funciona_cashback_': '¿Cómo funciona Cashback?',
        'quiero_comprar_y_tengo_una_duda': 'Quiero comprar y tengo una duda',
        'no_puedo_comprar': 'No puedo comprar',
        'no_he_recibido_confirmación_de_la_compra': 'No he recibido confirmación de la compra',
        '_cuándo_tendré_disponible_el_cashback_': '¿Cuándo tendré disponible el cashback?',
        '_cuándo_llegará_mi_dinero_': '¿Cuándo llegará mi dinero?',
        'otras_consultas': 'Otras consultas'
    };
    var cuponesOptions = {
        'cupones_cómo_funcionan_los_cupones_descuento_': '¿Cómo funcionan los cupones descuento?',
        'cupones_quiero_comprar_y_tengo_una_duda': 'Quiero comprar y tengo una duda',
        'cupones_no_puedo_comprar': 'No puedo comprar',
        'cupones_no_se_aplica_el_cupón_descuento_': '¿No se aplica el cupón descuento?',
        'cupones_otras_consultas': 'Otras consultas'
    };
    var shoppiDayOptions = {
        'shoppiday_mi_cuenta': 'Mi cuenta',
        'shoppiday_quieres_trabajar_con_nosotros_': '¿Quieres trabajar con nosotros?'
    };
    var toSelect = $('#00N2400000ByrAj');
    var fromSelect = $("#00N2400000BQjuM").val();
    if (fromSelect == 'CashBack') {
        myOptions = cashbackOptions;
    }
    if (fromSelect == 'Cupones') {
        myOptions = cuponesOptions;
    }
    if (fromSelect == 'Shoppiday') {
        myOptions = shoppiDayOptions;
    }
    toSelect.append(
        $('<option></option>').val('').html('--Ninguno--')
    );
    $.each(myOptions, function(val, text) {
        toSelect.append(
            $('<option></option>').val(val).html(text)
        );
    });
    $("#00N2400000BQjuM").change(function() {
        if (this.value == 'CashBack') {
            myOptions = cashbackOptions;
        }
        if (this.value == 'Cupones') {
            myOptions = cuponesOptions;
        }
        if (this.value == 'Shoppiday') {
            myOptions = shoppiDayOptions;
        }
        toSelect.empty();
        toSelect.append(
            $('<option></option>').val('').html('--Ninguno--')
        );
        $.each(myOptions, function(val, text) {
            toSelect.append(
                $('<option></option>').val(val).html(text)
            );
        });
    });

    /* --- Contact Form :: Validation End --- */
    //Condition toggle on category page
    $(document).on('click', '.cupon-option-condition', function() {
        $(this).parent().parent().next(".cup-box").toggle('slow');
        $(this).toggleClass('condition-open');
    });
    //Tipo toggle on category page responsive layout
    $(document).on('click', '.sub-top-tp-tipo', function() {
        $(this).parent().siblings(".sub-check-box").toggle('fold');
        $(this).toggleClass('tipo-opened');
    });

    //transfer request on desktop
    $(document).on('click', '.transfer_request', function() {
        var cashbackId = $(this).attr('data-id');
        var cashbackUrl = $(this).attr('data-url');
        var parentTd = $(this).parent();
        var grandParent = parentTd.parent();
        var update_status = grandParent.find('.status');
        $.ajax({
            type: "POST",
            data: {
                cashbackId: cashbackId
            },
            dataType: "json",
            url: cashbackUrl,
            success: function(data) {
                var outputClass = 'transfer_request_msg';
                if (data.error) {
                    outputClass += ' transfer_request_error';
                } else {
                    outputClass += ' transfer_request_success';
                    update_status.html(data.updatedStatus);
                }
                var output = '<tr><td class="' + outputClass + '" colspan="8">' + data.msg + '</td></tr>';
                grandParent.before(output).delay(5000).fadeOut();
            }
        });
    });

    //transfer request on responsive view
    $(document).on('click', '.transfer_request_resp', function() {
        var cashbackId = $(this).attr('data-id');
        var cashbackUrl = $(this).attr('data-url');
        var transfer_request_msg = $(this).parent().siblings('.list-det-wrap-r1-1-1').find('.transfer_request_msg');
        var parentDiv = $(this).parent();
        var update_status = parentDiv.parent().parent().find('.status_wrapper').find('.status');
        $.ajax({
            type: "POST",
            data: {
                cashbackId: cashbackId
            },
            dataType: "json",
            url: cashbackUrl,
            success: function(data) {
                parentDiv.hide();
                if (data.error) {
                    transfer_request_msg.addClass('transfer_request_error').html(data.msg).delay(5000).fadeOut();
                } else {
                    transfer_request_msg.addClass('transfer_request_success').html(data.msg).delay(5000).fadeOut();
                    update_status.html(data.updatedStatus);

                }
            }
        });
    });

    //AYUDA page accordion
    $(function() {
        $(".accordion").accordion({
            collapsible: true,
            active: false,
            heightStyle: 'content',
            header: '.accordion_content h3'
        });
    });

    //

    /*************************como-functiona****************************/
    $("#owl-demo").owlCarousel({
        autoPlay: 3000, //Set AutoPlay to 3 seconds     
        items : 1,
        itemsDesktopSmall : [979,1]
    });
    setInterval(function(){
        $("#como_functions").owlCarousel({
            navigation : true, // Show next and prev buttons
            slideSpeed : 300,
            paginationSpeed : 400,
            singleItem:true,
            // "singleItem:true" is a shortcut for:
            items : 1
            // itemsDesktop : false,
            // itemsDesktopSmall : false,
            // itemsTablet: false,
            // itemsMobile : false
        });
    },1000);

    setInterval(function(){
        $("#como_slider2").owlCarousel({
            navigation : true, // Show next and prev buttons
            slideSpeed : 300,
            paginationSpeed : 400,
            singleItem:true,
            loop: true,  
            // "singleItem:true" is a shortcut for:
            items : 1
            // itemsDesktop : false,
            // itemsDesktopSmall : false,
            // itemsTablet: false,
            // itemsMobile : false
        });
    },1000);

    /*************************como-functiona****************************/

});
// js conflict solved

// Filtering functions
function removeActive(eachObj, mainObj) {
    if ($(eachObj).hasClass("active") != $(mainObj).hasClass("active")) {
        if ($(eachObj).hasClass("active") == true) {
            $(eachObj).removeClass("active");
            $(eachObj).removeClass("selected");
        }
    }
}

function removeCharacterActive(eachObj, mainObj) {
    if ($(eachObj).children("a").hasClass("active") != $(mainObj).children("a").hasClass("active")) {
        if ($(eachObj).children("a").hasClass("active") == true) {
            $(eachObj).children("a").removeClass("active");
        }
    }
}

function filtering(info, alphabets, view, offer, category_id_string, url, addtofevlist, shorting) {

    $.ajax({
        type: "POST",
        data: {
            data_array: info,
            alphabet: alphabets,
            view: view,
            offer: offer,
            category_id_string: category_id_string,
            addtofevlist: addtofevlist,
            shorting: shorting
        },
        dataType: "json",
        url: url,
        success: function(data) {
            $('#loader_image').hide();
            $("#brand_data_ajax").html(data.html);
            $('.chech-box-2 label').each(function() {
                if ($(this).hasClass("active") == true) {
                    var checkID = $(this).attr('for');
                    var finalID = "#" + checkID;
                    $(finalID).prop("checked", true);                    
                }
            });
        }

    });
}

function tiendas_filtering(info, alphabets, view, offer, category_id_string, url, addtofevlist) {

    $.ajax({
        type: "POST",
        data: {
            data_array: info,
            alphabet: alphabets,
            view: view,
            offer: offer,
            category_id_string: category_id_string,
            addtofevlist: addtofevlist
        },
        dataType: "json",
        url: url,
        success: function(data) {
            $("#tiendas_data_ajax").html(data.html);
            $('#loader_image').hide();
        }

    });
}

function collection_filtering(info, alphabets, view, offer, category_id_string, url, addtofevlist) {

    $.ajax({
        type: "POST",
        data: {
            data_array: info,
            alphabet: alphabets,
            view: view,
            offer: offer,
            category_id_string: category_id_string,
            addtofevlist: addtofevlist
        },
        dataType: "json",
        url: url,
        success: function(data) {
            console.log(data.html)
            $("#collection_data_ajax").html(data.html);
            $('#loader_image').hide();
        }

    });
}

function cat_filtering(info, alphabets, view, offer, category_id_string, url, addtofevlist, shorting) {

    $.ajax({
        type: "POST",
        data: {
            data_array: info,
            alphabet: alphabets,
            view: view,
            offer: offer,
            category_id_string: category_id_string,
            addtofevlist: addtofevlist,
            shorting: shorting
        },
        dataType: "json",
        url: url,
        success: function(data) {
            $('#loader_image').hide();
            $("#category_data_ajax").html(data.html);
            $('.chech-box-2 label').each(function() {
                if ($(this).hasClass("active") == true) {
                    var checkID = $(this).attr('for');
                    var finalID = "#" + checkID;
                    $(finalID).prop("checked", true);
                }
            });            
        }

    });
}
function loadSearchFilter(info, url, addtofevlist, target_count, basePath, execute_count) {
    var offer = '';
    var alphabets = '';
    var view = "";
    var category_id_string = "";
    var offer_arr = [];
    var shorting = '';
    var dynamic_filter_url = '';

    $('body').on('click', '.brand-alpha-order-inner span', function() {
        var offer_arr = [];
        $('#loader_image').show();
        var mainObj = this;
        $('.brand-alpha-order-inner span').each(function() {
            var eachObj = this;
            removeActive(eachObj, mainObj);
        });
        $(this).toggleClass("active");
        if ($(this).hasClass("active") == true) {
            var alphabets = $(this).text();
        } else {
            var alphabets = '';
        }
        if ($('.main-category-post-promo-right select').val() && $('.main-category-post-promo-right select').val() != 0) {
            shorting = $(this).val();
        } else {
            shorting = '';
        }
        $('.category-checkbox label').each(function() {
            if ($(this).hasClass("active") == true) {
                if ($.inArray($(this).text(), offer_arr) == -1) {
                    //not found it
                    offer_arr.push($(this).text());
                }
            }
        });
        /*$('ul.child-sub-menu div li').each(function() {
            if($(this).hasClass( "active" ) == true)
            {
                category_id_string = $(this).children("a").attr("class");
            }
        });*/
        $('div.category-sidebar-inner ul.child-sub-menu li').each(function() {
            if ($(this).hasClass("active") == true) {
                category_id_string = $(this).children("a").attr("data-class");
            }
        });
        var searchedTerm = $("#searched-keyword").val();
        front.search.filter(info, alphabets, view, offer_arr, category_id_string, url, addtofevlist, shorting, target_count, dynamic_filter_url, execute_count, searchedTerm);
    });

    $('body').on('click', '.category-checkbox label', function() {
        $('#loader_image').show();
        var mainObj = this;
        var offer_arr = [];
        var i = 0;
        /*  $('.brand-list_alphabets span').each(function() {
              if($(this).children("a").hasClass( "active" ) == true)
              {
                  alphabets  = $(this).text();
              }
          });*/
        $('.brand-alpha-order-inner span').each(function() {
            if ($(this).hasClass("active") == true) {
                alphabets = $(this).text();
            }
        });
        if ($('.main-category-post-promo-right select').val() && $('.main-category-post-promo-right select').val() != 0) {
            shorting = $(this).val();
        } else {
            shorting = '';
        }
        $('div.category-sidebar-inner ul.child-sub-menu li').each(function() {
            if ($(this).hasClass("active") == true) {
                category_id_string = $(this).children("a").attr("data-class");
            }
        });
        $(this).toggleClass("active");
        $('.category-checkbox label').each(function() {
            if ($(this).hasClass("active") == true) {
               if ($.inArray($(this).text(), offer_arr) == -1) {
                    //not found it
                    offer_arr.push($(this).text());
                }
            }
        });
        var searchedTerm = $("#searched-keyword").val();
        front.search.filter(info, alphabets, view, offer_arr, category_id_string, url, addtofevlist, shorting, target_count, dynamic_filter_url, execute_count, searchedTerm);
    });

    $('body').on('click', 'div.category-sidebar-inner ul.child-sub-menu li', function(event) {
        $('#loader_image').show();
        if ($(this).hasClass("child_cat") == true) {
            $(this).parents("li.parent_cat").removeClass("selected");
            var mainObj = this;
            $('ul.child-sub-menu li.child_cat').each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(mainObj).children("a").next("ul").hide("slow");
            });
            $(this).siblings("li").each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(eachObj).children("ul").hide("slow");
            });
        } else if ($(this).hasClass("parent_cat") == true) {
            var mainObj = this;
            $('ul.child-sub-menu li.parent_cat').each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(mainObj).children("a").next("ul").hide("slow");
            });
            $(this).siblings("li").each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(eachObj).children("ul").hide("slow");
            });
        } else {
            if ($(this).parents("li.child_cat").hasClass("selected")) {
                $(this).parents("li.child_cat").removeClass("selected");
            }
            if ($(this).parents("li.parent_cat").hasClass("selected")) {
                $(this).parents("li.parent_cat").removeClass("selected");
            }
            var mainObj = this;
            $(this).siblings("li").each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(eachObj).children("ul").hide("slow");
            });
        }
        /*  $('.brand-list_alphabets span').each(function() {
              if($(this).children("a").hasClass( "active" ) == true)
              {
                  alphabets  = $(this).text();
              }
          });*/
        $('.brand-alpha-order-inner span').each(function() {
            if ($(this).hasClass("active") == true) {
                alphabets = $(this).text();
            }
        });
        if ($(this).hasClass("active") == false) {
            $(this).addClass("active selected");
            $(this).children('a').next("ul").show("slow");
            if ($(this).children('a').next("ul").children("li").hasClass("child_cat")) {
                $(this).children('a').next("ul").children("li.child_cat").removeClass("active selected");
                $(this).children('a').next("ul").children("li.child_cat").children('a').next("ul").children("li").removeClass("active selected");
                $(this).children('a').next("ul").children("li.child_cat").children('a').next("ul").hide();

            }
        } else {
            $(this).removeClass("active selected");
            $(this).children('a').next("ul").hide("slow");
        }
        $('.category-checkbox label').each(function() {
            if ($(this).hasClass("active") == true) {
               if ($.inArray($(this).text(), offer_arr) == -1) {
                    //not found it
                    offer_arr.push($(this).text());
                }
            }
        });
        if ($('.main-category-post-promo-right select').val() && $('.main-category-post-promo-right select').val() != 0) {
            shorting = $(this).val();
        } else {
            shorting = '';
        }
        if ($(this).hasClass("active") == true) {
            category_id_string = $(this).children("a").attr("data-class");
        } else {
            category_id_string = '';
        }
        var searchedTerm = $("#searched-keyword").val();
        front.search.filter(info, alphabets, view, offer_arr, category_id_string, url, addtofevlist, shorting, target_count, dynamic_filter_url, execute_count, searchedTerm);
        event.stopPropagation();
    });

    $('body').on('change', '.main-category-post-promo-right select', function() {
        $('#loader_image').show();
        /* $('.brand-list_alphabets span').each(function() {
             if($(this).children("a").hasClass( "active" ) == true)
             {
                 alphabets  = $(this).text();
             }
         });*/
        $('.brand-alpha-order-inner span').each(function() {
            if ($(this).hasClass("active") == true) {
                alphabets = $(this).text();
            }
        });
        $(this).toggleClass("active");
        $('.category-checkbox label').each(function() {
            if ($(this).hasClass("active") == true) {
                if ($.inArray($(this).text(), offer_arr) == -1) {
                    //not found it
                    offer_arr.push($(this).text());
                }
            }
        });
        /* $('ul.child-sub-menu div li').each(function() {
             if($(this).hasClass( "active" ) == true)
             {
                 category_id_string = $(this).children("a").attr("class");
             }
         });*/
        $('div.category-sidebar-inner ul.child-sub-menu li').each(function() {
            if ($(this).hasClass("active") == true) {
                category_id_string = $(this).children("a").attr("data-class");
            }
        });
        if ($(this).val()) {
            shorting = $(this).val();
        } else {
            shorting = '';
        }
        var searchedTerm = $("#searched-keyword").val();
        front.search.filter(info, alphabets, view, offer_arr, category_id_string, url, addtofevlist, shorting, target_count, dynamic_filter_url, execute_count, searchedTerm);
    });
    
}
function loadCategoryFilter(info, id, url, addtofevlist, target_count, basePath, execute_count) {
    var offer = '';
    var alphabets = '';
    var view = "";
    var category_id_string = "";
    var offer_arr = [];
    var shorting = '';
    var dynamic_filter_url = '';

    $('body').on('click', '.brand-alpha-order-inner span', function() {
         var offer_arr = [];
        $('#loader_image').show();
        var mainObj = this;
        $('.brand-alpha-order-inner span').each(function() {
            var eachObj = this;
            removeActive(eachObj, mainObj);
        });
        $(this).toggleClass("active");
        if ($(this).hasClass("active") == true) {
            var alphabets = $(this).text();
        } else {
            var alphabets = '';
        }
        if ($('.main-category-post-promo-right select').val() && $('.main-category-post-promo-right select').val() != 0) {
            shorting = $(this).val();
        } else {
            shorting = '';
        }
        $('.category-checkbox label').each(function() {
            if ($(this).hasClass("active") == true) {
                if ($.inArray($(this).text(), offer_arr) == -1) {
                    //not found it
                    offer_arr.push($(this).text());
                }
            }
        });
        /*$('ul.child-sub-menu div li').each(function() {
            if($(this).hasClass( "active" ) == true)
            {
                category_id_string = $(this).children("a").attr("class");
            }
        });*/
        $('div.category-sidebar-inner ul.child-sub-menu li').each(function() {
            if ($(this).hasClass("active") == true) {
                category_id_string = $(this).children("a").attr("data-class");
            }
        });

        front.category.filter(info, alphabets, view, offer_arr, category_id_string, url, addtofevlist, shorting, target_count, dynamic_filter_url, execute_count);
    });

    $('body').on('click', '.category-checkbox label', function() {
        $('#loader_image').show();
        var mainObj = this;
        var offer_arr = [];
        var i = 0;
        /*  $('.brand-list_alphabets span').each(function() {
              if($(this).children("a").hasClass( "active" ) == true)
              {
                  alphabets  = $(this).text();
              }
          });*/
        $('.brand-alpha-order-inner span').each(function() {
            if ($(this).hasClass("active") == true) {
                alphabets = $(this).text();
            }
        });
        if ($('.main-category-post-promo-right select').val() && $('.main-category-post-promo-right select').val() != 0) {
            shorting = $(this).val();
        } else {
            shorting = '';
        }
        $('div.category-sidebar-inner ul.child-sub-menu li').each(function() {
            if ($(this).hasClass("active") == true) {
                category_id_string = $(this).children("a").attr("data-class");
            }
        });
        $(this).toggleClass("active");
        $('.category-checkbox label').each(function() {
            if ($(this).hasClass("active") == true) {
               if ($.inArray($(this).text(), offer_arr) == -1) {
                    //not found it
                    offer_arr.push($(this).text());
                }
            }
        });

        front.category.filter(info, alphabets, view, offer_arr, category_id_string, url, addtofevlist, shorting, target_count, dynamic_filter_url, execute_count);
    });



    $('body').on('click', 'div.category-sidebar-inner ul.child-sub-menu li', function(event) {
        if($(this).hasClass("selected")){
            var z = 'categorias';
            target_count = 1;
            var dynamic_filter_url = basePath + '/' + z;
            window.history.pushState("", "", basePath + '/');
            var dynamic_filter_url = basePath + '/' + z;

          /*  $('div.category-sidebar-inner ul.child-sub-menu li').each(function(){
                if ($(this).hasClass("active") == true) 
                {
                    $(this).removeClass("active");
                    $(this).children('a').next("ul").hide("slow");
                }
                   
            });*/

            if (typeof (history.pushState) != "undefined") {
                var obj = {Title: z, Url: z};
                history.pushState(obj, obj.Title, obj.Url);
            } else {
                alert("Browser does not support HTML5.");
            }
        }else {
            var x = $(this).children().attr('onclick').split(',');
            var y = x[0].split('(');
            var z = y[1].slice(1, -1)
            window.history.pushState("", "", basePath + '/');
            var dynamic_filter_url = basePath + '/' + z;
            console.log(dynamic_filter_url);
            if (typeof (history.pushState) != "undefined") {
                var obj = {Title: z, Url: z};
                history.pushState(obj, obj.Title, obj.Url);
            } else {
                alert("Browser does not support HTML5.");
            }
        }

        $('#loader_image').show();
        if ($(this).hasClass("child_cat") == true) {
            $(this).parents("li.parent_cat").removeClass("selected");
            var mainObj = this;
            $('ul.child-sub-menu li.child_cat').each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(mainObj).children("a").next("ul").hide("slow");
            });
            $(this).siblings("li").each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(eachObj).children("ul").hide("slow");
            });
        } else if ($(this).hasClass("parent_cat") == true) {
            var mainObj = this;
            $('ul.child-sub-menu li.parent_cat').each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(mainObj).children("a").next("ul").hide("slow");
            });
            $(this).siblings("li").each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(eachObj).children("ul").hide("slow");
            });
        } else {
            if ($(this).parents("li.child_cat").hasClass("selected")) {
                $(this).parents("li.child_cat").removeClass("selected");
            }
            if ($(this).parents("li.parent_cat").hasClass("selected")) {
                $(this).parents("li.parent_cat").removeClass("selected");
            }
            var mainObj = this;
            $(this).siblings("li").each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(eachObj).children("ul").hide("slow");
            });
        }
        /*  $('.brand-list_alphabets span').each(function() {
              if($(this).children("a").hasClass( "active" ) == true)
              {
                  alphabets  = $(this).text();
              }
          });*/
        $('.brand-alpha-order-inner span').each(function() {
            if ($(this).hasClass("active") == true) {
                alphabets = $(this).text();
            }
        });
        if ($(this).hasClass("active") == false) {
            $(this).addClass("active selected");
            $(this).children('a').next("ul").show("slow");
            if ($(this).children('a').next("ul").children("li").hasClass("child_cat")) {
                $(this).children('a').next("ul").children("li.child_cat").removeClass("active selected");
                $(this).children('a').next("ul").children("li.child_cat").children('a').next("ul").children("li").removeClass("active selected");
                $(this).children('a').next("ul").children("li.child_cat").children('a').next("ul").hide();

            }
        } else {
            $(this).removeClass("active selected");
            $(this).children('a').next("ul").hide("slow");
        }
        $('.category-checkbox label').each(function() {
            if ($(this).hasClass("active") == true) {
               if ($.inArray($(this).text(), offer_arr) == -1) {
                    //not found it
                    offer_arr.push($(this).text());
                }
            }
        });
        if ($('.main-category-post-promo-right select').val() && $('.main-category-post-promo-right select').val() != 0) {
            shorting = $(this).val();
        } else {
            shorting = '';
        }
        if ($(this).hasClass("active") == true) {
            category_id_string = $(this).children("a").attr("data-class");
        } else {
            category_id_string = '';
        }
        front.category.filter(info, alphabets, view, offer_arr, category_id_string, url, addtofevlist, shorting, target_count, dynamic_filter_url, execute_count);
        event.stopPropagation();
    });
    $('body').on('change', '.main-category-post-promo-right select', function() {
        $('#loader_image').show();
        /* $('.brand-list_alphabets span').each(function() {
             if($(this).children("a").hasClass( "active" ) == true)
             {
                 alphabets  = $(this).text();
             }
         });*/
        $('.brand-alpha-order-inner span').each(function() {
            if ($(this).hasClass("active") == true) {
                alphabets = $(this).text();
            }
        });
        $(this).toggleClass("active");
        $('.category-checkbox label').each(function() {
            if ($(this).hasClass("active") == true) {
                if ($.inArray($(this).text(), offer_arr) == -1) {
                    //not found it
                    offer_arr.push($(this).text());
                }
            }
        });
        /* $('ul.child-sub-menu div li').each(function() {
             if($(this).hasClass( "active" ) == true)
             {
                 category_id_string = $(this).children("a").attr("class");
             }
         });*/
        $('div.category-sidebar-inner ul.child-sub-menu li').each(function() {
            if ($(this).hasClass("active") == true) {
                category_id_string = $(this).children("a").attr("data-class");
            }
        });
        if ($(this).val()) {
            shorting = $(this).val();
        } else {
            shorting = '';
        }
        front.category.filter(info, alphabets, view, offer_arr, category_id_string, url, addtofevlist, shorting, target_count, dynamic_filter_url, execute_count);
    });

    // Responsive view filtering
    $('body').on('click', '.chech-box-2 label', function() {
        $('#loader_image').show();
        var mainObj = this;
        var offer_arr = [];
        $(this).toggleClass("active");
        var checkID = $(this).attr('for');
        var finalID = "#" + checkID;
        $(finalID).prop("checked", true);
        $('.chech-box-2 label').each(function() {
            if ($(this).hasClass("active") == true) {
                offer_arr.push($(this).text().split(' ')[0]);
            }
        });
        front.category.filter(info, alphabets, view, offer_arr, category_id_string, url, addtofevlist, shorting, target_count, execute_count);
    });
}

function loadBrandFilter(info,url,addtofevlist,target_count,basePath,execute_count) {
    var offer = '';
    var alphabets = '';
    var view = "";
    var category_id_string = "";
    var offer_arr = [];
    var shorting = '';
    var dynamic_filter_url = '';

    $('body').on('click', '.brand-alpha-order-inner span', function() {
         var offer_arr = [];
        $('#loader_image').show();
        var mainObj = this;
        $('.brand-alpha-order-inner span').each(function() {
            var eachObj = this;
            removeActive(eachObj, mainObj);
        });
        $(this).toggleClass("active");
        if ($(this).hasClass("active") == true) {
            var alphabets = $(this).text();
        } else {
            var alphabets = '';
        }
        if ($('.main-category-post-promo-right select').val() && $('.main-category-post-promo-right select').val() != 0) {
            shorting = $(this).val();
        } else {
            shorting = '';
        }
        $('.category-checkbox label').each(function() {
            if ($(this).hasClass("active") == true) {
                if ($.inArray($(this).text(), offer_arr) == -1) {
                    //not found it
                    offer_arr.push($(this).text());
                }
            }
        });
        /*$('ul.child-sub-menu div li').each(function() {
            if($(this).hasClass( "active" ) == true)
            {
                category_id_string = $(this).children("a").attr("class");
            }
        });*/
        $('div.category-sidebar-inner ul.child-sub-menu li').each(function() {
            if ($(this).hasClass("active") == true) {
                category_id_string = $(this).children("a").attr("data-class");
            }
        });

        front.brand.filter(info, alphabets, view, offer_arr, category_id_string, url, addtofevlist, shorting, target_count, dynamic_filter_url, execute_count);
    });

    $('body').on('click', '.category-checkbox label', function() {
        var redirectUrl = $(this).attr("url-route-to");
        if(redirectUrl.trim() != "") {
            $(this).toggleClass("active");
            window.location=redirectUrl;
            return true;
        }

        $('#loader_image').show();
        var mainObj = this;
        var offer_arr = [];
        var i = 0;
        /*  $('.brand-list_alphabets span').each(function() {
              if($(this).children("a").hasClass( "active" ) == true)
              {
                  alphabets  = $(this).text();
              }
          });*/
        $('.brand-alpha-order-inner span').each(function() {
            if ($(this).hasClass("active") == true) {
                alphabets = $(this).text();
            }
        });
        if ($('.main-category-post-promo-right select').val() && $('.main-category-post-promo-right select').val() != 0) {
            shorting = $(this).val();
        } else {
            shorting = '';
        }
        $('div.category-sidebar-inner ul.child-sub-menu li').each(function() {
            if ($(this).hasClass("active") == true) {
                category_id_string = $(this).children("a").attr("data-class");
            }
        });
         if (url.indexOf("cashback") >= 0)
            {
                //var z = 'cashback';
                if($(this).text() != 'Cupones')
                {
                    $(this).toggleClass("active");
                }
            }
            else if (url.indexOf("cupones-descuento") >= 0)
            {
                if($(this).text() != 'Cashback')
                {
                    $(this).toggleClass("active");
                }
            }
        
        $('.category-checkbox label').each(function() {
            if ($(this).hasClass("active") == true) {
               if ($.inArray($(this).text(), offer_arr) == -1) {
                    //not found it
                    offer_arr.push($(this).text());
                }
            }            
        });

        front.brand.filter(info, alphabets, view, offer_arr, category_id_string, url, addtofevlist, shorting, target_count, dynamic_filter_url, execute_count);
    });
    $('body').on('click', 'div.category-sidebar-inner ul.child-sub-menu li', function(event) {
         if($(this).hasClass("selected")){
            if (url.indexOf("cashback") >= 0)
            {
                var z = 'cashback';
            }
            else if (url.indexOf("cupones-descuento") >= 0)
            {
                var z = 'cupones-descuento';
            }

            target_count = 1;
            var dynamic_filter_url = basePath + '/' + z;
            window.history.pushState("", "", basePath + '/');
            var dynamic_filter_url = basePath + '/' + z;
           /*  $('div.category-sidebar-inner ul.child-sub-menu li').each(function(){
                if ($(this).hasClass("active") == true) 
                {
                    $(this).removeClass("active");
                    $(this).children('a').next("ul").hide("slow");
                }
                   
            });*/
            if (typeof (history.pushState) != "undefined") {
                var obj = {Title: z, Url: z};
                history.pushState(obj, obj.Title, obj.Url);
            } else {
                alert("Browser does not support HTML5.");
            }
        }else {
            var x = $(this).children().attr('onclick').split(',');
            var y = x[0].split('(');
            var z = y[1].slice(1, -1)
            window.history.pushState("", "", basePath + '/');
            var dynamic_filter_url = basePath + '/' + z;
            console.log(dynamic_filter_url);
            if (typeof (history.pushState) != "undefined") {
                var obj = {Title: z, Url: z};
                history.pushState(obj, obj.Title, obj.Url);
            } else {
                alert("Browser does not support HTML5.");
            }
        }
        $('#loader_image').show();
        if ($(this).hasClass("child_cat") == true) {
            $(this).parents("li.parent_cat").removeClass("selected");
            var mainObj = this;
            $('ul.child-sub-menu li.child_cat').each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(mainObj).children("a").next("ul").hide("slow");
            });
            $(this).siblings("li").each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(eachObj).children("ul").hide("slow");
            });
        } else if ($(this).hasClass("parent_cat") == true) {
            var mainObj = this;
            $('ul.child-sub-menu li.parent_cat').each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(mainObj).children("a").next("ul").hide("slow");
            });
            $(this).siblings("li").each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(eachObj).children("ul").hide("slow");
            });
        } else {
            if ($(this).parents("li.child_cat").hasClass("selected")) {
                $(this).parents("li.child_cat").removeClass("selected");
            }
            if ($(this).parents("li.parent_cat").hasClass("selected")) {
                $(this).parents("li.parent_cat").removeClass("selected");
            }
            var mainObj = this;
            $(this).siblings("li").each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(eachObj).children("ul").hide("slow");
            });
        }
        /*  $('.brand-list_alphabets span').each(function() {
              if($(this).children("a").hasClass( "active" ) == true)
              {
                  alphabets  = $(this).text();
              }
          });*/
        $('.brand-alpha-order-inner span').each(function() {
            if ($(this).hasClass("active") == true) {
                alphabets = $(this).text();
            }
        });
        if ($(this).hasClass("active") == false) {
            $(this).addClass("active selected");
            $(this).children('a').next("ul").show("slow");
            if ($(this).children('a').next("ul").children("li").hasClass("child_cat")) {
                $(this).children('a').next("ul").children("li.child_cat").removeClass("active selected");
                $(this).children('a').next("ul").children("li.child_cat").children('a').next("ul").children("li").removeClass("active selected");
                $(this).children('a').next("ul").children("li.child_cat").children('a').next("ul").hide();

            }
        } else {
            $(this).removeClass("active selected");
            $(this).children('a').next("ul").hide("slow");
        }
        $('.category-checkbox label').each(function() {
            if ($(this).hasClass("active") == true) {
                if ($.inArray($(this).text(), offer_arr) == -1) {
                    //not found it
                    offer_arr.push($(this).text());
                }
            }
        });
        if ($('.main-category-post-promo-right select').val() && $('.main-category-post-promo-right select').val() != 0) {
            shorting = $(this).val();
        } else {
            shorting = '';
        }
        if ($(this).hasClass("active") == true) {
            category_id_string = $(this).children("a").attr("data-class");
        } else {
            category_id_string = '';
        }
        front.brand.filter(info, alphabets, view, offer_arr, category_id_string, url, addtofevlist, shorting, target_count, dynamic_filter_url, execute_count);
        event.stopPropagation();
    });
    $('body').on('change', '.main-category-post-promo-right select', function() {
        $('#loader_image').show();
        /* $('.brand-list_alphabets span').each(function() {
             if($(this).children("a").hasClass( "active" ) == true)
             {
                 alphabets  = $(this).text();
             }
         });*/
        $('.brand-alpha-order-inner span').each(function() {
            if ($(this).hasClass("active") == true) {
                alphabets = $(this).text();
            }
        });
        $(this).toggleClass("active");
        $('.category-checkbox label').each(function() {
            if ($(this).hasClass("active") == true) {
                if ($.inArray($(this).text(), offer_arr) == -1) {
                    //not found it
                    offer_arr.push($(this).text());
                }
            }
        });
        /* $('ul.child-sub-menu div li').each(function() {
             if($(this).hasClass( "active" ) == true)
             {
                 category_id_string = $(this).children("a").attr("class");
             }
         });*/
        $('div.category-sidebar-inner ul.child-sub-menu li').each(function() {
            if ($(this).hasClass("active") == true) {
                category_id_string = $(this).children("a").attr("data-class");
            }
        });
        if ($(this).val()) {
            shorting = $(this).val();
        } else {
            shorting = '';
        }

        front.brand.filter(info, alphabets, view, offer_arr, category_id_string, url, addtofevlist, shorting, target_count, dynamic_filter_url, execute_count);
    });

    // Responsive view filtering
    $('body').on('click', '.chech-box-2 label', function() {
        $('#loader_image').show();
        var mainObj = this;
        var offer_arr = [];
        $(this).toggleClass("active");
        var checkID = $(this).attr('for');
        var finalID = "#" + checkID;
        $(finalID).prop("checked", true);
        $('.chech-box-2 label').each(function() {
            if ($(this).hasClass("active") == true) {
                offer_arr.push($(this).text().split(' ')[0]);
            }
        });

        front.brand.filter(info, alphabets, view, offer_arr, category_id_string, url, addtofevlist, shorting, target_count, dynamic_filter_url, execute_count);
    });
}

function loadTiendasFilter(info, url, addtofevlist,target_count,basePath,execute_count) {
     var view = "";
    $('body').on('click', '.list_view', function() {
        $('#loader_image').show();
        view = "list";
         $('.view').text("list");
        $(".tiendas_grid_view").hide();
        $(".tiendas_list_view").show();
        $('#loader_image').hide();
    });
    $('body').on('click', '.grid_view', function() {
        $('#loader_image').show();
        view = "grid";
        $('.view').text("grid");
        $(".tiendas_list_view").hide();
        $(".tiendas_grid_view").show();
        $('#loader_image').hide();
    });

    var offer = '';
    var alphabets = '';
    var view = "";
    var category_id_string = "";
    var offer_arr = [];
    var dynamic_filter_url = '';
    $('body').on('click', '.brand-list_alphabets span', function() {
         var offer_arr = [];
        $('#loader_image').show();
        var mainObj = this;
        $('.brand-list_alphabets span').each(function() {
            var eachObj = this;
            removeCharacterActive(eachObj, mainObj);
        });

        $(this).children("a").toggleClass("active");

        if ($(this).children("a").hasClass("active") == true) {
            var alphabets = $(this).text();
        } else {
            var alphabets = '';
        }
        $('.alphabet').text(alphabets);

        /*if($(this).children("a").text().trim() == $('.alphabet').text().trim())
        {
            $('.alphabet').text('');
        }*/

        $('.brand-alpha-order-inner span').each(function() {
            if ($(this).text() == alphabets) {
                $(this).addClass("active");
            } else {
                $(this).removeClass("active");
            }
        });

        $('.category-checkbox label').each(function() {
            if ($(this).hasClass("active") == true) {
                if ($.inArray($(this).text(), offer_arr) == -1) {
                    //not found it
                    offer_arr.push($(this).text());
                }
            }
        });
        $('ul.child-sub-menu div li').each(function() {

            if ($(this).hasClass("active") == true) {
                category_id_string = $(this).children("a").attr("data-class");
            }
        });
        front.tiendas.filter(info, alphabets.trim(), view, offer_arr, category_id_string, url, addtofevlist, target_count, dynamic_filter_url, execute_count);
    });

    $('body').on('click', '.brand-alpha-order-inner span', function() {
        $('#loader_image').show();
         var offer_arr = [];
        var mainObj = this;
        $('.brand-alpha-order-inner span').each(function() {
            var eachObj = this;
            removeActive(eachObj, mainObj);
        });
        $(this).toggleClass("active");
        if ($(this).hasClass("active") == true) {
            var alphabets = $(this).text();
        } else {
            var alphabets = '';
        }
        $('.alphabet').text(alphabets);


        $('.brand-list_alphabets span').each(function() {
            if ($(this).children("a").text() == alphabets) {
                $(this).children("a").addClass("active");
            } else {
                $(this).children("a").removeClass("active");
            }
        });



        $('.category-checkbox label').each(function() {
            if ($(this).hasClass("active") == true) {
                if ($.inArray($(this).text(), offer_arr) == -1) {
                    //not found it
                    offer_arr.push($(this).text());
                }
            }
        });
        $('ul.child-sub-menu div li').each(function() {
            if ($(this).hasClass("active") == true) {
                category_id_string = $(this).children("a").attr("data-class");
            }
        });
        front.tiendas.filter(info, alphabets.trim(), view, offer_arr, category_id_string, url, addtofevlist, target_count, dynamic_filter_url, execute_count);
    });
    $('body').on('click', '.category-checkbox label', function() {
        $('#loader_image').show();
        var mainObj = this;
        var offer_arr = [];
        var i = 0;
        $('.brand-list_alphabets span').each(function() {
            if ($(this).children("a").hasClass("active") == true) {
                alphabets = $(this).text();
            }
        });
        $('ul.child-sub-menu div li').each(function() {
            if ($(this).hasClass("active") == true) {
                category_id_string = $(this).children("a").attr("data-class");
            }
        });
        $(this).toggleClass("active");
        $('.category-checkbox label').each(function() {
            if ($(this).hasClass("active") == true) {
                if ($.inArray($(this).text(), offer_arr) == -1) {
                    //not found it
                    offer_arr.push($(this).text());
                }
            }
        });
        
        front.tiendas.filter(info, alphabets.trim(), view, offer_arr, category_id_string, url, addtofevlist, target_count, dynamic_filter_url, execute_count);
    });
    $('body').on('click', 'div.category-sidebar-inner ul.child-sub-menu li', function(event) {
         if($(this).hasClass("selected")){
            var z = 'tienda';
            target_count = 1;
            var dynamic_filter_url = basePath + '/' + z;
            window.history.pushState("", "", basePath + '/');
            var dynamic_filter_url = basePath + '/' + z;
          /*  $('div.category-sidebar-inner ul.child-sub-menu li').each(function(){
                if ($(this).hasClass("active") == true) 
                {
                    $(this).removeClass("active");
                    $(this).children('a').next("ul").hide("slow");
                }
                   
            });*/
            if (typeof (history.pushState) != "undefined") {
                var obj = {Title: z, Url: z};
                history.pushState(obj, obj.Title, obj.Url);
            } else {
                alert("Browser does not support HTML5.");
            }
        }else {
            var x = $(this).children().attr('onclick').split(',');
            var y = x[0].split('(');
            var z = y[1].slice(1, -1)
            window.history.pushState("", "", basePath + '/');
            var dynamic_filter_url = basePath + '/' + z;
            console.log(dynamic_filter_url);
            if (typeof (history.pushState) != "undefined") {
                var obj = {Title: z, Url: z};
                history.pushState(obj, obj.Title, obj.Url);
            } else {
                alert("Browser does not support HTML5.");
            }
        }
        $('#loader_image').show();
        if ($(this).hasClass("child_cat") == true) {
            $(this).parents("li.parent_cat").removeClass("selected");
            var mainObj = this;
            $('ul.child-sub-menu li.child_cat').each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(mainObj).children("a").next("ul").hide("slow");
            });
            $(this).siblings("li").each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(eachObj).children("ul").hide("slow");
            });
        } else if ($(this).hasClass("parent_cat") == true) {
            var mainObj = this;
            $('ul.child-sub-menu li.parent_cat').each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(mainObj).children("a").next("ul").hide("slow");
            });
            $(this).siblings("li").each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(eachObj).children("ul").hide("slow");
            });
        } else {
            if ($(this).parents("li.child_cat").hasClass("selected")) {
                $(this).parents("li.child_cat").removeClass("selected");
            }
            if ($(this).parents("li.parent_cat").hasClass("selected")) {
                $(this).parents("li.parent_cat").removeClass("selected");
            }
            var mainObj = this;
            $(this).siblings("li").each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(eachObj).children("ul").hide("slow");
            });
        }
        $('.brand-list_alphabets span').each(function() {
            if ($(this).children("a").hasClass("active") == true) {
                alphabets = $(this).text();
            }
        });
        if ($(this).hasClass("active") == false) {
            $(this).addClass("active selected");
            $(this).children('a').next("ul").show("slow");
            if ($(this).children('a').next("ul").children("li").hasClass("child_cat")) {
                $(this).children('a').next("ul").children("li.child_cat").removeClass("active selected");
                $(this).children('a').next("ul").children("li.child_cat").children('a').next("ul").children("li").removeClass("active selected");
                $(this).children('a').next("ul").children("li.child_cat").children('a').next("ul").hide();

            }
        } else {
            $(this).removeClass("active selected");
            $(this).children('a').next("ul").hide("slow");
        }
        $('.category-checkbox label').each(function() {
            if ($(this).hasClass("active") == true) {
                if ($.inArray($(this).text(), offer_arr) == -1) {
                    //not found it
                    offer_arr.push($(this).text());
                }
            }
        });
        if ($(this).hasClass("active") == true) {
            category_id_string = $(this).children("a").attr("data-class");
        } else {
            category_id_string = '';
        }
        front.tiendas.filter(info, alphabets.trim(), view, offer_arr, category_id_string, url, addtofevlist, target_count, dynamic_filter_url, execute_count);
        event.stopPropagation();
    });
}

function loadCollectionFilter(info, url, addtofevlist,target_count,basePath,execute_count) {
    var view = "";
    $('body').on('click', '.list_view', function() {
        $('#loader_image').show();
        view = "list";
        $('.view').text("list");
        $(".collection_grid_view").hide();
        $(".collection_list_view").show();
        $('#loader_image').hide();

    });
    $('body').on('click', '.grid_view', function() {
        $('#loader_image').show();
        view = "grid";
        $('.view').text("grid");
        $(".collection_list_view").hide();
        $(".collection_grid_view").show();
        $('#loader_image').hide();
    });

    var offer = '';
    var alphabets = '';
    var category_id_string = "";
    var offer_arr = [];
    var dynamic_filter_url = '';
    $('body').on('click', '.brand-list_alphabets span', function() {
         var offer_arr = [];
        $('#loader_image').show();
        var mainObj = this;
        $('.brand-list_alphabets span').each(function() {
            var eachObj = this;
            removeCharacterActive(eachObj, mainObj);
        });

        $(this).children("a").toggleClass("active");

        if ($(this).children("a").hasClass("active") == true) {
            var alphabets = $(this).text();
        } else {
            var alphabets = '';
        }
        $('.alphabet').text(alphabets);
        $('.brand-alpha-order-inner span').each(function() {
            if ($(this).text() == alphabets) {
                $(this).addClass("active");
            } else {
                $(this).removeClass("active");
            }
        });

        $('.category-checkbox label').each(function() {
            if ($(this).hasClass("active") == true) {
                if ($.inArray($(this).text(), offer_arr) == -1) {
                    //not found it
                    offer_arr.push($(this).text());
                }
            }
        });
        $('ul.child-sub-menu div li').each(function() {

            if ($(this).hasClass("active") == true) {
                category_id_string = $(this).children("a").attr("data-class");
            }
        });

       front.collection.filter(info, alphabets, view, offer_arr, category_id_string, url, addtofevlist, target_count, dynamic_filter_url, execute_count);
    });

    $('body').on('click', '.brand-alpha-order-inner span', function() {
         var offer_arr = [];
        $('#loader_image').show();
        var mainObj = this;
        $('.brand-alpha-order-inner span').each(function() {
            var eachObj = this;
            removeActive(eachObj, mainObj);
        });
        $(this).toggleClass("active");
        if ($(this).hasClass("active") == true) {
            var alphabets = $(this).text();
        } else {
            var alphabets = '';
        }
        $('.alphabet').text(alphabets);

        $('.brand-list_alphabets span').each(function() {
            if ($(this).children("a").text() == alphabets) {
                $(this).children("a").addClass("active");
            } else {
                $(this).children("a").removeClass("active");
            }
        });



        $('.category-checkbox label').each(function() {
            if ($(this).hasClass("active") == true) {
                if ($.inArray($(this).text(), offer_arr) == -1) {
                    //not found it
                    offer_arr.push($(this).text());
                }
            }
        });
        $('ul.child-sub-menu div li').each(function() {
            if ($(this).hasClass("active") == true) {
                category_id_string = $(this).children("a").attr("data-class");
            }
        });
        front.collection.filter(info, alphabets, view, offer_arr, category_id_string, url, addtofevlist, target_count, dynamic_filter_url, execute_count);
    });
    $('body').on('click', '.category-checkbox label', function() {
        $('#loader_image').show();
        var mainObj = this;
        var offer_arr = [];
        var i = 0;
        $('.brand-list_alphabets span').each(function() {
            if ($(this).children("a").hasClass("active") == true) {
                alphabets = $(this).text();
            }
        });
        $('ul.child-sub-menu div li').each(function() {
            if ($(this).hasClass("active") == true) {
                category_id_string = $(this).children("a").attr("data-class");
            }
        });
        $(this).toggleClass("active");
        $('.category-checkbox label').each(function() {
            if ($(this).hasClass("active") == true) {
                if ($.inArray($(this).text(), offer_arr) == -1) {
                    //not found it
                    offer_arr.push($(this).text());
                }
            }
        });
        front.collection.filter(info, alphabets, view, offer_arr, category_id_string, url, addtofevlist, target_count, dynamic_filter_url, execute_count);
    });
    $('body').on('click', 'div.category-sidebar-inner ul.child-sub-menu li', function(event) {
        var res = url.split(basePath+"/ofertas/");
        var collectionName;
        for(var i=0;i<res.length;i++){
            if(i==1)
            {
                collectionName = res[i];
            }
        }
       
        if($(this).hasClass("selected")){
            var z = collectionName;
            target_count = 1;
            var dynamic_filter_url = basePath +'/ofertas/'+collectionName;
            window.history.pushState("", "", basePath + '/');
            var dynamic_filter_url = basePath +'/ofertas/'+collectionName;
          /*  $('div.category-sidebar-inner ul.child-sub-menu li').each(function(){
                if ($(this).hasClass("active") == true) 
                {
                    $(this).removeClass("active");
                    $(this).children('a').next("ul").hide("slow");
                }
                   
            });*/
            if (typeof (history.pushState) != "undefined") {
                var obj = {Title: 'ofertas/'+collectionName, Url: 'ofertas/'+collectionName};
                history.pushState(obj, obj.Title, obj.Url);
            } else {
                alert("Browser does not support HTML5.");
            }
        }else {
            var x = $(this).children().attr('onclick').split(',');
            var y = x[0].split('(');
            var z = y[1].slice(1, -1);
          
            window.history.pushState("", "", basePath + '/');
            var dynamic_filter_url = basePath +'/ofertas/'+collectionName + '/' + z;
           
            console.log(dynamic_filter_url);
            if (typeof (history.pushState) != "undefined") {
                var obj = {Title: 'ofertas/'+collectionName + '/' + z, Url: 'ofertas/'+collectionName + '/' + z};
                history.pushState(obj, obj.Title, obj.Url);
            } else {
                alert("Browser does not support HTML5.");
            }
        }
        $('#loader_image').show();
        if ($(this).hasClass("child_cat") == true) {
            $(this).parents("li.parent_cat").removeClass("selected");
            var mainObj = this;
            $('ul.child-sub-menu li.child_cat').each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(mainObj).children("a").next("ul").hide("slow");
            });
            $(this).siblings("li").each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(eachObj).children("ul").hide("slow");
            });
        } else if ($(this).hasClass("parent_cat") == true) {
            var mainObj = this;
            $('ul.child-sub-menu li.parent_cat').each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(mainObj).children("a").next("ul").hide("slow");
            });
            $(this).siblings("li").each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(eachObj).children("ul").hide("slow");
            });
        } else {
            if ($(this).parents("li.child_cat").hasClass("selected")) {
                $(this).parents("li.child_cat").removeClass("selected");
            }
            if ($(this).parents("li.parent_cat").hasClass("selected")) {
                $(this).parents("li.parent_cat").removeClass("selected");
            }
            var mainObj = this;
            $(this).siblings("li").each(function() {
                var eachObj = this;
                removeActive(eachObj, mainObj);
                $(eachObj).children("ul").hide("slow");
            });
        }
        $('.brand-list_alphabets span').each(function() {
            if ($(this).children("a").hasClass("active") == true) {
                alphabets = $(this).text();
            }
        });
        if ($(this).hasClass("active") == false) {
            $(this).addClass("active selected");
            $(this).children('a').next("ul").show("slow");
            if ($(this).children('a').next("ul").children("li").hasClass("child_cat")) {
                $(this).children('a').next("ul").children("li.child_cat").removeClass("active selected");
                $(this).children('a').next("ul").children("li.child_cat").children('a').next("ul").children("li").removeClass("active selected");
                $(this).children('a').next("ul").children("li.child_cat").children('a').next("ul").hide();

            }
        } else {
            $(this).removeClass("active selected");
            $(this).children('a').next("ul").hide("slow");
        }
        $('.category-checkbox label').each(function() {
            if ($(this).hasClass("active") == true) {
               if ($.inArray($(this).text(), offer_arr) == -1) {
                    //not found it
                    offer_arr.push($(this).text());
                }
            }
        });
        if ($(this).hasClass("active") == true) {
            category_id_string = $(this).children("a").attr("data-class");
        } else {
            category_id_string = '';
        }
        front.collection.filter(info, alphabets, view, offer_arr, category_id_string, url, addtofevlist, target_count, dynamic_filter_url, execute_count);
        event.stopPropagation();
    });
}
// End Filtering Functions


function autoCityLoder(fieldName) {
    
    autocomplete = new google.maps.places.Autocomplete(
          (document.getElementById(fieldName)),
          {
            types: ['(cities)'],
            componentRestrictions: {country: $("#country_code").text()}
          });

}

function registerClick(shopId, shopHistoryId, shopOffers, tabType, tabId, tabPosition, Url, affiliateUrl, sessionId, event, shopImage, cashbackPrice, affiliateUrlOrigin, programId) {
    event.preventDefault();
    $.ajax({
        method: "POST",
        url: Url,
        dataType: "html",
        data: {
            shopId: shopId,
            shopHistoryId: shopHistoryId,
            shopOffers: shopOffers,
            tabType: tabType,
            tabId: tabId,
            tabPosition: tabPosition,
            affiliateUrl: affiliateUrl,
            affiliateUrlOrigin: affiliateUrlOrigin,
            programId: programId,
        },
        beforeSend: function() {
            if (sessionId != '') {
                $('b#cashback_bold').text(cashbackPrice + " de Cashback.");
                $("#click_shop_image").attr("src", shopImage);
                $(".popup_parent").show();
                $(".cashback_popup").show();
            }
        },
        success: function(result, e) {
            if (sessionId != '') {
                var delay = 500;
                setTimeout(function() {
                    $('.cashback_popup').hide();
                    $(".popup_parent").hide();
                    $(".go_to_store_up a").attr('href', affiliateUrl);
                    window.open(affiliateUrl, '_blank');
                }, delay)
            } else {
                $("#gotostore_login").show();
                $("#gotostore_login ul.social-login-sub").show();
            }
        }
    });
}

function closePopUp(flag) {
    $(".popup_parent").hide();
    if (flag == 1) {
        /*$(".popup_wrapper").hide();*/
    } else if (flag == 2) {
        $(".cupones_popup").hide();
    } else if (flag == 3) {
        $(".offer_popup").hide();
    }
}
var holdtext = '';

function cuponGenerate(shopId, shopHistoryId, shopOffers, tabType, tabId, tabPosition, Url, affiliateUrl, sessionId, event, shopImage, voucherCode, trackUri, voucherProgramName, discountAmount, isPercentage, voucherExpireDate, voucherDescription) {
    event.preventDefault();
    $.ajax({
        method: "POST",
        url: Url,
        dataType: "html",
        data: {
            shopId: shopId,
            shopHistoryId: shopHistoryId,
            shopOffers: shopOffers,
            tabType: tabType,
            tabId: tabId,
            tabPosition: tabPosition,
            affiliateUrl: affiliateUrl,
        },
        beforeSend: function() {
            //if (sessionId != '') {
            $("#cupones_shop_image").attr("src", shopImage);
            $("#cupones_shop_image").attr("height", 142);
            $("#cupones_shop_image").attr("width", 103);
            $("#voucher_code").val(voucherCode);
            $("b#voucher_programm_bold").text(voucherProgramName);
            /*if(isPercentage) {
                $("p#voucher_discount").text("Codigo ahorro del " + discountAmount + "% en electrodomesticos");
            }else{
                $("p#voucher_discount").text("Codigo ahorro del " + discountAmount + " en electrodomesticos");
            }*/
            $("p#voucher_discount").text(voucherDescription);
            $("p#voucher_expire").text("Promocion valida hatsa el " + voucherExpireDate + ". No aplicable a otras promociones y cupones descuento.");
            $(".popup_parent").show();
            $(".cupones_popup").show();
            copyToClipboard(document.getElementById("voucher_code"));
            //}
        },
        success: function(result, e) {
            //if (sessionId != ''){                
            document.getElementById("submit_voucher").addEventListener("click", function() {
                copyToClipboard(document.getElementById("voucher_code"));
            });
            /*var delay = 500;
            setTimeout(function() {*/
                    copyToClipboard(document.getElementById("voucher_code"));
                    window.open(trackUri, '_blank');
                /*}, delay)*/
                /*}
                else
                {
                    $("#gotostore_login").show();
                    $("#gotostore_login ul.social-login-sub").show();
                }*/
        }
    });
}

function offerGenerate(shopId, shopHistoryId, shopOffers, tabType, tabId, tabPosition, Url, affiliateUrl, sessionId, event, shopImage, voucherCode, trackUri, voucherProgramName, discountAmount, isPercentage, voucherExpireDate, voucherDescription) {
    event.preventDefault();
    $.ajax({
        method: "POST",
        url: Url,
        dataType: "html",
        data: {
            shopId: shopId,
            shopHistoryId: shopHistoryId,
            shopOffers: shopOffers,
            tabType: tabType,
            tabId: tabId,
            tabPosition: tabPosition,
            affiliateUrl: affiliateUrl,
        },
        beforeSend: function() {
            //if (sessionId != '') {
            $("#offer_shop_image").attr("src", shopImage);
            $("#cupones_shop_image").attr("height", 142);
            $("#cupones_shop_image").attr("width", 103);
            $("b#voucher_programm_bold").text(voucherProgramName);
            /*if(isPercentage) {
                $("p#voucher_discount").text("Codigo ahorro del " + discountAmount + "% en electrodomesticos");
            }else{
                $("p#voucher_discount").text("Codigo ahorro del " + discountAmount + " en electrodomesticos");
            }*/
            $("p#voucher_discount").text(voucherDescription);
            $("p#voucher_expire").text("Promocion valida hatsa el " + voucherExpireDate + ". No aplicable a otras promociones y cupones descuento.");
            $(".popup_parent").show();
            $(".offer_popup").show();
            //}
        },
        success: function(result, e) {
            //if (sessionId != ''){                
            window.open(trackUri, '_blank');
            $("#submit_offer").on('click', function() {
                window.open(trackUri, '_blank');
            })
                /*}
                else
                {
                    $("#gotostore_login").show();
                    $("#gotostore_login ul.social-login-sub").show();
                }*/
        }
    });
}

function copyToClipboard(elem) {
    // create hidden text element, if it doesn't already exist
    var targetId = "_hiddenCopyText_";
    var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
    var origSelectionStart, origSelectionEnd;
    if (isInput) {
        // can just use the original source element for the selection and copy
        target = elem;
        origSelectionStart = elem.selectionStart;
        origSelectionEnd = elem.selectionEnd;
    } else {
        // must use a temporary form element for the selection and copy
        target = document.getElementById(targetId);
        if (!target) {
            var target = document.createElement("textarea");
            target.style.position = "absolute";
            target.style.left = "-9999px";
            target.style.top = "0";
            target.id = targetId;
            document.body.appendChild(target);
        }
        target.textContent = elem.textContent;
    }
    // select the content
    var currentFocus = document.activeElement;
    target.focus();
    target.setSelectionRange(0, target.value.length);

    // copy the selection
    var succeed;
    try {
        succeed = document.execCommand("copy");
    } catch (e) {
        succeed = false;
    }
    // restore original focus
    if (currentFocus && typeof currentFocus.focus === "function") {
        currentFocus.focus();
    }

    if (isInput) {
        // restore prior selection
        elem.setSelectionRange(origSelectionStart, origSelectionEnd);
    } else {
        // clear temporary content
        target.textContent = "";
    }
    return succeed;
}

var front = {
    category: {
        loadMore: function(targetCount, ajaxUrl, OF, AF, CF){
            $.ajax({
                method: "POST",
                url: ajaxUrl,
                dataType: "json",
                data: {
                    target_count: targetCount,
                    alphabet: AF,
                    offer: JSON.parse(OF),
                    category_id_string: CF,
                },
                beforeSend: function() {
                    //$("#loader_image_load_more").show();
                    //$( "div.popup_parent" ).show();
                },
                success: function(result) {
                    $('.category-sidebar-right').children().remove();
                    $('.category-sidebar-right').append(result.html);
                    //$("#loader_image_load_more").hide();
                    //$( "div.popup_parent" ).hide();
                }
            });
        },
        loadMoreCat: function(targetCount, ajaxUrl){
            $.ajax({
                method: "POST",
                url: ajaxUrl,
                dataType: "json",
                data: {
                    target_count: targetCount,
                },
                beforeSend: function() {
                    //$("#loader_image_load_more").show();
                    //$( "div.popup_parent" ).show();
                },
                success: function(result) {
                    $('.category-sidebar-right').children().remove();
                    $('.category-sidebar-right').append(result.html);
                    //$("#loader_image_load_more").hide();
                    //$( "div.popup_parent" ).hide();
                }
            });
        },
        filter: function(info, alphabets, view, offer, category_id_string, url, addtofevlist, shorting, target_count, dynamic_filter_url, execute_count){
            $.ajax({
                type: "POST",
                data: {
                    data_array: info,
                    alphabet: alphabets,
                    view: view,
                    offer: offer,
                    category_id_string: category_id_string,
                    addtofevlist: addtofevlist,
                    shorting: shorting,
                    target_count: target_count,
                    execute_count: execute_count,
                },
                dataType: "json",
                url: dynamic_filter_url,
                beforeSend: function() {
                    //$("#loader_image_load_more").show();
                    //$( "div.popup_parent" ).show();
                },
                success: function(result) {
                    $('.category-sidebar-right').children().remove();
                    $('.category-sidebar-right').append(result.html);
                    $('.chech-box-2 label').each(function() {
                        if ($(this).hasClass("active") == true) {
                            var checkID = $(this).attr('for');
                            var finalID = "#" + checkID;
                            $(finalID).prop("checked", true);
                        }
                    });
                    //$("#loader_image_load_more").hide();
                    //$( "div.popup_parent" ).hide();
                }

            });
        }
    },
    search: {
        loadMore: function(targetCount, ajaxUrl, OF, AF, CF, searchedTerm){
            $.ajax({
                method: "POST",
                url: ajaxUrl,
                dataType: "json",
                data: {
                    q: searchedTerm,
                    target_count: targetCount,
                    alphabet: AF,
                    offer: JSON.parse(OF),
                    category_id_string: CF,
                },
                beforeSend: function() {
                    //$("#loader_image_load_more").show();
                    //$( "div.popup_parent" ).show();
                },
                success: function(result) {
                    $('.category-sidebar-right').children().remove();
                    $('.category-sidebar-right').append(result.html);
                    //$("#loader_image_load_more").hide();
                    //$( "div.popup_parent" ).hide();
                }
            });
        },
        loadMoreCat: function(targetCount, ajaxUrl, searchedTerm){
            $.ajax({
                method: "POST",
                url: ajaxUrl,
                dataType: "json",
                data: {
                    q: searchedTerm,
                    target_count: targetCount,
                },
                beforeSend: function() {
                    //$("#loader_image_load_more").show();
                    //$( "div.popup_parent" ).show();
                },
                success: function(result) {
                    $('.category-sidebar-right').children().remove();
                    $('.category-sidebar-right').append(result.html);
                    //$("#loader_image_load_more").hide();
                    //$( "div.popup_parent" ).hide();
                }
            });
        },
        filter: function(info, alphabets, view, offer, category_id_string, url, addtofevlist, shorting, target_count, dynamic_filter_url, execute_count, searchedTerm){
            $.ajax({
                type: "POST",
                data: {
                    q: searchedTerm,
                    data_array: info,
                    alphabet: alphabets,
                    view: view,
                    offer: offer,
                    category_id_string: category_id_string,
                    addtofevlist: addtofevlist,
                    shorting: shorting,
                    target_count: target_count,
                    execute_count: execute_count,
                },
                dataType: "json",
                url: dynamic_filter_url,
                beforeSend: function() {
                    //$("#loader_image_load_more").show();
                    //$( "div.popup_parent" ).show();
                },
                success: function(result) {
                    $('.category-sidebar-right').children().remove();
                    $('.category-sidebar-right').append(result.html);
                    $('.chech-box-2 label').each(function() {
                        if ($(this).hasClass("active") == true) {
                            var checkID = $(this).attr('for');
                            var finalID = "#" + checkID;
                            $(finalID).prop("checked", true);
                        }
                    });
                    //$("#loader_image_load_more").hide();
                    //$( "div.popup_parent" ).hide();
                }

            });
        }
    },
    brand: {
    loadMore: function(targetCount, ajaxUrl, OF, AF, CF){
        $.ajax({
            method: "POST",
            url: ajaxUrl,
            dataType: "json",
            data: {
                target_count: targetCount,
                alphabet: AF,
                offer: JSON.parse(OF),
                category_id_string: CF,
            },
            beforeSend: function() {
                //$("#loader_image_load_more").show();
                //$( "div.popup_parent" ).show();
            },
            success: function(result) {
                $('.category-sidebar-right').children().remove();
                $('.category-sidebar-right').append(result.html);
                //$("#loader_image_load_more").hide();
                //$( "div.popup_parent" ).hide();
            }
        });
    },
    loadMoreCat: function(targetCount, ajaxUrl){
        $.ajax({
            method: "POST",
            url: ajaxUrl,
            dataType: "json",
            data: {
                target_count: targetCount,
            },
            beforeSend: function() {
                //$("#loader_image_load_more").show();
                //$( "div.popup_parent" ).show();
            },
            success: function(result) {
                $('.category-sidebar-right').children().remove();
                $('.category-sidebar-right').append(result.html);
                //$("#loader_image_load_more").hide();
                //$( "div.popup_parent" ).hide();
            }
        });
    },

    filter: function(info, alphabets, view, offer, category_id_string, url, addtofevlist, shorting, target_count, dynamic_filter_url, execute_count){
        $.ajax({
            type: "POST",
            data: {
                data_array: info,
                alphabet: alphabets,
                view: view,
                offer: offer,
                category_id_string: category_id_string,
                addtofevlist: addtofevlist,
                shorting: shorting,
                target_count: target_count,
                execute_count: execute_count,
            },
            dataType: "json",
            url: dynamic_filter_url,
            beforeSend: function() {
                //$("#loader_image_load_more").show();
                //$( "div.popup_parent" ).show();
            },
            success: function(result) {
                $('.category-sidebar-right').children().remove();
                $('.category-sidebar-right').append(result.html);
                $('.chech-box-2 label').each(function() {
                    if ($(this).hasClass("active") == true) {
                        var checkID = $(this).attr('for');
                        var finalID = "#" + checkID;
                        $(finalID).prop("checked", true);
                    }
                });
                //$("#loader_image_load_more").hide();
                //$( "div.popup_parent" ).hide();
            }

        });
     }
    },

    collection: {
    loadMore: function(targetCount, ajaxUrl, OF, AF, CF){
        $.ajax({
            method: "POST",
            url: ajaxUrl,
            dataType: "json",
            data: {
                target_count: targetCount,
                alphabet: AF,
                offer: JSON.parse(OF),
                category_id_string: CF,
            },
            beforeSend: function() {
                //$("#loader_image_load_more").show();
                //$( "div.popup_parent" ).show();
            },
            success: function(result) {

                $('.category-sidebar-right').children().remove();
                $('.category-sidebar-right').append(result.html);
                var view =  $(".view").text();
                 var alphabet =  $('.alphabet').text().trim();
                $('.brand-alpha-order-inner span').each(function() {
                    if ($(this).text() == alphabet) {
                        $(this).addClass("active");
                    } else {
                        $(this).removeClass("active");
                    }
                });

                $('.brand-list_alphabets span').each(function() {
                    if ($(this).children("a").text() == alphabet) {
                        $(this).children("a").addClass("active");
                    } else {
                        $(this).children("a").removeClass("active");
                    }
                });
                if (view == 'grid')
                {
                    $(".collection_list_view").hide();
                    $(".collection_grid_view").show();
                }
                else
                {
                    $(".collection_grid_view").hide();
                    $(".collection_list_view").show();
                }
                //$("#loader_image_load_more").hide();
                //$( "div.popup_parent" ).hide();
            }
        });
    },
    loadMoreCat: function(targetCount, ajaxUrl){
        $.ajax({
            method: "POST",
            url: ajaxUrl,
            dataType: "json",
            data: {
                target_count: targetCount,
            },
            beforeSend: function() {
                //$("#loader_image_load_more").show();
                //$( "div.popup_parent" ).show();
            },
            success: function(result) {
                $('.category-sidebar-right').children().remove();
                $('.category-sidebar-right').append(result.html);
                var view =  $(".view").text();
                var alphabet =  $('.alphabet').text().trim();
                $('.brand-alpha-order-inner span').each(function() {
                    if ($(this).text() == alphabet) {
                        $(this).addClass("active");
                    } else {
                        $(this).removeClass("active");
                    }
                });

                $('.brand-list_alphabets span').each(function() {
                    if ($(this).children("a").text() == alphabet) {
                        $(this).children("a").addClass("active");
                    } else {
                        $(this).children("a").removeClass("active");
                    }
                });
                if (view == 'grid')
                {
                    $(".collection_list_view").hide();
                    $(".collection_grid_view").show();
                }
                else
                {
                    $(".collection_grid_view").hide();
                    $(".collection_list_view").show();
                }
                //$("#loader_image_load_more").hide();
                //$( "div.popup_parent" ).hide();
            }
        });
    },
    filter: function(info, alphabets, view, offer, category_id_string, url, addtofevlist, target_count, dynamic_filter_url, execute_count){

        $.ajax({
            type: "POST",
            data: {
                data_array: info,
                alphabet: alphabets,
                view: view,
                offer: offer,
                category_id_string: category_id_string,
                addtofevlist: addtofevlist,
                target_count: target_count,
                execute_count: execute_count,
            },
            dataType: "json",
            url: dynamic_filter_url,
            beforeSend: function() {
                //$("#loader_image_load_more").show();
                //$( "div.popup_parent" ).show();
            },
            success: function(result) {
                $('.category-sidebar-right').children().remove();
                $('.category-sidebar-right').append(result.html);
                var view =  $(".view").text();
                var alphabet =  $('.alphabet').text().trim();
                $('.brand-alpha-order-inner span').each(function() {
                    if ($(this).text() == alphabet) {
                        $(this).addClass("active");
                    } else {
                        $(this).removeClass("active");
                    }
                });

                $('.brand-list_alphabets span').each(function() {
                    if ($(this).children("a").text() == alphabet) {
                        $(this).children("a").addClass("active");
                    } else {
                        $(this).children("a").removeClass("active");
                    }
                });

                if (view == 'grid')
                {
                    $(".collection_list_view").hide();
                    $(".collection_grid_view").show();
                }
                else
                {
                    $(".collection_grid_view").hide();
                    $(".collection_list_view").show();
                }
                $('.chech-box-2 label').each(function() {
                    if ($(this).hasClass("active") == true) {
                        var checkID = $(this).attr('for');
                        var finalID = "#" + checkID;
                        $(finalID).prop("checked", true);
                    }
                });
                //$("#loader_image_load_more").hide();
                //$( "div.popup_parent" ).hide();
            }

        });
     }
    },

    tiendas: {
    loadMore: function(targetCount, ajaxUrl, OF, AF, CF){
        $.ajax({
            method: "POST",
            url: ajaxUrl,
            dataType: "json",
            data: {
                target_count: targetCount,
                alphabet: AF,
                offer: JSON.parse(OF),
                category_id_string: CF,
            },
            beforeSend: function() {
                //$("#loader_image_load_more").show();
                //$( "div.popup_parent" ).show();
            },
            success: function(result) {

                $('.category-sidebar-right').children().remove();
                $('.category-sidebar-right').append(result.html);
                var view =  $(".view").text();
                 var alphabet =  $('.alphabet').text().trim();
                $('.brand-alpha-order-inner span').each(function() {
                    if ($(this).text() == alphabet) {
                        $(this).addClass("active");
                    } else {
                        $(this).removeClass("active");
                    }
                });

                $('.brand-list_alphabets span').each(function() {
                    if ($(this).children("a").text() == alphabet) {
                        $(this).children("a").addClass("active");
                    } else {
                        $(this).children("a").removeClass("active");
                    }
                });
                if (view == 'grid')
                {
                    $(".tiendas_list_view").hide();
                    $(".tiendas_grid_view").show();
                }
                else
                {
                    $(".tiendas_grid_view").hide();
                    $(".tiendas_list_view").show();
                }
                //$("#loader_image_load_more").hide();
                //$( "div.popup_parent" ).hide();
            }
        });
    },
    loadMoreCat: function(targetCount, ajaxUrl){
        $.ajax({
            method: "POST",
            url: ajaxUrl,
            dataType: "json",
            data: {
                target_count: targetCount,
            },
            beforeSend: function() {
                //$("#loader_image_load_more").show();
                //$( "div.popup_parent" ).show();
            },
            success: function(result) {
                $('.category-sidebar-right').children().remove();
                $('.category-sidebar-right').append(result.html);
                var view =  $(".view").text();
                 var alphabet =  $('.alphabet').text().trim();
                $('.brand-alpha-order-inner span').each(function() {
                    if ($(this).text() == alphabet) {
                        $(this).addClass("active");
                    } else {
                        $(this).removeClass("active");
                    }
                });

                $('.brand-list_alphabets span').each(function() {
                    if ($(this).children("a").text() == alphabet) {
                        $(this).children("a").addClass("active");
                    } else {
                        $(this).children("a").removeClass("active");
                    }
                });
                 if (view == 'grid')
                {
                    $(".tiendas_list_view").hide();
                    $(".tiendas_grid_view").show();
                }
                else
                {
                    $(".tiendas_grid_view").hide();
                    $(".tiendas_list_view").show();
                }
                //$("#loader_image_load_more").hide();
                //$( "div.popup_parent" ).hide();
            }
        });
    },
    filter: function(info, alphabets, view, offer, category_id_string, url, addtofevlist, target_count, dynamic_filter_url, execute_count){

        $.ajax({
            type: "POST",
            data: {
                data_array: info,
                alphabet: alphabets,
                view: view,
                offer: offer,
                category_id_string: category_id_string,
                addtofevlist: addtofevlist,
                target_count: target_count,
                execute_count: execute_count,
            },
            dataType: "json",
            url: dynamic_filter_url,
            beforeSend: function() {
                //$("#loader_image_load_more").show();
                //$( "div.popup_parent" ).show();
            },
            success: function(result) {
                $('.category-sidebar-right').children().remove();
                $('.category-sidebar-right').append(result.html);
                var view =  $(".view").text();
                var alphabet =  $('.alphabet').text().trim();
                $('.brand-alpha-order-inner span').each(function() {
                    if ($(this).text() == alphabet) {
                        $(this).addClass("active");
                    } else {
                        $(this).removeClass("active");
                    }
                });

                $('.brand-list_alphabets span').each(function() {
                    if ($(this).children("a").text() == alphabet) {
                        $(this).children("a").addClass("active");
                    } else {
                        $(this).children("a").removeClass("active");
                    }
                });

                if (view == 'grid') {
                    $(".tiendas_list_view").hide();
                    $(".tiendas_grid_view").show();
                } else {
                    $(".tiendas_grid_view").hide();
                    $(".tiendas_list_view").show();
                }
                $('.chech-box-2 label').each(function() {
                    if ($(this).hasClass("active") == true) {
                        var checkID = $(this).attr('for');
                        var finalID = "#" + checkID;
                        $(finalID).prop("checked", true);
                    }
                });
            }

        });
     }
    }
};
