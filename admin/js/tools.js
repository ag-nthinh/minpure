/*
 * ãƒ­ãƒ¼ãƒ«ã‚ªãƒ¼ãƒãƒ¼
 */
function rollover() { 
    if(document.getElementsByTagName) {
        var images = document.getElementsByTagName("img");
        for(var i=0; i < images.length; i++) {
            if("imgRo"==images[i].getAttribute("classname") || "imgRo"==images[i].getAttribute("class")){
                images[i].onmouseover = function() {
                    this.setAttribute("src", this.getAttribute("src").replace(/^(.+)(\.[a-z]+)$/, "$1_ro$2"));
                }
                images[i].onmouseout = function() {
                    this.setAttribute("src", this.getAttribute("src").replace(/^(.+)_ro(\.[a-z]+)$/, "$1$2"));
                }
            }
        }
    }
}
$(function(){
    if(window.addEventListener){
        window.addEventListener("load", rollover, false);
    } else if(window.attachEvent) {
        window.attachEvent("onload", rollover);
    }
});
/* search form */
$(function(){
    $('.collapsibleSwitch a').click(function(){
        $('.searchSetting').removeAttr('class').attr('class',$(this).parent('li').attr('class')+' searchSetting');return false;
    });
});
/* scroll */
$(function(){
    $('a[href^="#"]').click(function() {
        var offset = 5;
        var id = $(this).attr("href");
        if(!(id=='#')){
            if(id=='#sform')offset=70;
            if($(id).size()>0){
                var target = $(id).offset().top - offset;
                $('html, body').animate({scrollTop:target}, 500);
                return false;
            }
        }
    });
});
// æ¤œç´¢çµæžœã‚½ãƒ¼ãƒˆ
$(function(){
    $('.itemSortBlock .sortIcon').find('a').live('click', function() {
        var table = $(this).closest('div.itemSortBlock');
        $.ajax({
            type: "GET",
            url: "/api/sort-block/",
            data: $(this).data('query'),
            dataType: 'json',
            success: function(res){
                table.empty().append(res);
            }
        });
    });
});
$(function(){
    $(document).ajaxSend(
        function(e,xhr, opts){
            xhr.setRequestHeader("Pre-Referer", document.referrer);
        }
    );
});

// ã‚¹ã‚«ã‚¤ã‚¹ã‚¯ãƒ¬ã‚¤ãƒ‘ãƒ¼åˆ¶å¾¡
// $(function(){
// 
//     var target = $('#skyscraper').find('img');
//     var targetHeight = $('#skyscraper').outerHeight() + 50;
//     var targetTop = target.offset().top;
//     var sidebar = $('#mainBox > .subColumn');
//     var targetLeft = sidebar.offset().left + ((sidebar.outerWidth() / 2) - (target.outerWidth() / 2));
// 
//     var limitTop = $('.companyList').offset().top;
// 
//     var mainHeight = $('#mainBox > .mainColumn').height();
//     var sidebarHeight = sidebar.height();
// 
//     $(window).resize(function() {
//         targetLeft = sidebar.offset().left + ((sidebar.outerWidth() / 2) - (target.outerWidth() / 2));
//         remove();
//     });
// 
//     $(window).scroll(function() { remove(); });
// 
//     var remove = function() {
// 
//         if (mainHeight > sidebarHeight) {
//             var scrollTop = $(document).scrollTop();
//             var scrollBottom = scrollTop + targetHeight;
// 
//             if (scrollTop > targetTop) {
//                 if (scrollBottom >= limitTop) {
//                     var currentTop = limitTop - (scrollTop + targetHeight);
//                     target.css({position: 'fixed', top: currentTop + 'px', left: targetLeft + 'px'})
//                 } else {
//                     target.css({position: 'fixed', top: '0px', left: targetLeft + 'px'});
//                 }
//             } else {
//                 target.css({position: 'static', top: targetTop + 'px', left: targetLeft + 'px'});
//             }
//         }
//     }
// });

