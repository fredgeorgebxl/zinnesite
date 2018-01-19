$(document).foundation();

// init

$(document).ready(function(){
    $(".scrollLink").on('click', function(e){
        e.preventDefault();
        scrollToAnchor($(this).attr("href"));
    });
});

var controller = new ScrollMagic.Controller();

// Top menu animation

var scene = new ScrollMagic.Scene({
        triggerElement: "#menu-trigger"
})
.setTween(".homepage .top-bar", 0.5, {marginTop:"0"})
.addIndicators({name: "1 (duration: 0)"})
.addTo(controller)
.on("start", function (event) {
    TweenLite.from(".top-bar-title", 1, {scale: 0.4, ease: Elastic.easeOut, delay: 0.2 });
});

var scene = new ScrollMagic.Scene({
        triggerElement: "#menu-trigger",
        duration: 1000
})
.setTween(".presentation-content", {marginTop:"-50vh"})
.addIndicators({name: "2 (duration: 1000)"})
.addTo(controller);

function scrollToAnchor(anchor){
    TweenLite.to(window, 1, {scrollTo:anchor, ease:Power3.easeInOut});
}