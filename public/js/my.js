$(".navbar-brand").filter(function () {
    return this.href == location.href.replace(/#.*/, "");
}).addClass("text-success");

$( '[data-toggle="tooltip"]' ).tooltip({
    trigger: 'hover'
});

$('img').on('error', function(e) {
    e.preventDefault();
    $(this).attr('src', '/img/posters/thumbs/notfound.jpg');
});