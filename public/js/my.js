$(".navbar-brand").filter(function () {
    console.log(this);
    return this.href == location.href.replace(/#.*/, "");
}).addClass("text-success");