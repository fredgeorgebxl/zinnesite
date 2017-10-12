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
.on("end", function (event) {
    console.log("Hit end point of scene.");
})
.addIndicators({name: "1 (duration: 0)"})
.addTo(controller);

function scrollToAnchor(anchor){
    TweenLite.to(window, 1, {scrollTo:anchor, ease:Power3.easeInOut});
}