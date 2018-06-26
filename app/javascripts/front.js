
document.addEventListener("DOMContentLoaded", function(event) {
    var button = document.querySelector(".country-selector");
    var h = getOffset( button ).top;
    var body = document.body,html = document.documentElement;
    var page_height = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight );
    console.log(h +  " and " +page_height);

    var position = window.getComputedStyle( button ).getPropertyValue( "position" );
    var bottom = window.getComputedStyle( button ).getPropertyValue( "bottom" );
    var top = window.getComputedStyle( button ).getPropertyValue( "top" );

    if ((position !== "fixed" && h > page_height / 2) || (position === "fixed" && h > 100)) {
        button.className += " weglot-invert";
    }
    return false;
});

function getOffset (element) {
    var top = 0, left = 0;
    do {
        top += element.offsetTop  || 0;
        left += element.offsetLeft || 0;
        element = element.offsetParent;
    } while(element);

    return {
        top: top,
        left: left
    };
}
