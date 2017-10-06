$(document).foundation();

// init

$(document).ready(function(){
    $(".main-hp-scroll-link a").on('click', function(e){
        e.preventDefault();
        TweenLite.to(window, 1, {scrollTo:"#presentation", ease:Power3.easeInOut});
    });
});

var controller = new ScrollMagic.Controller();

// Top menu animation

var scene = new ScrollMagic.Scene({
        triggerElement: "#menu-trigger"
})
.setTween(".homepage .top-bar", 0.5, {marginTop:"0"})
.addIndicators({name: "1 (duration: 0)"})
.addTo(controller);
