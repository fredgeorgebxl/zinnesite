$(document).foundation();

// init
$(document).ready(function(){
    $(".top-bar").css("marginTop", "-200px");
});

var controller = new ScrollMagic.Controller();

// Top menu animation

var scene = new ScrollMagic.Scene({
        triggerElement: "#menu-trigger"
})
.setTween(".top-bar", 0.5, {marginTop:"0"})
.addIndicators({name: "1 (duration: 0)"})
.addTo(controller);