function delay(callback, ms) {
  var timer = 0;
  return function () {
      var context = this, args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function () {
          callback.apply(context, args);
      }, ms || 0);
  };
}

$(function () {
  $("#search").on('keyup touchend', delay(function (e) {
      e.preventDefault();

      $('#results').empty();
      var search = $('#search');
      var path = $("#search").attr("data-path");

      if ($(this).val().length) {
          $.ajax({
              type: "POST",
              url: path,
              dataType: "json",
              data: search.serialize(),
              cache: false,
              success: function (response) {
                  $.each(response, function (key, movie) {
                      $.each(movie, function (key, movie) {
                          if (movie.title.toLowerCase().indexOf(Object.values(search)[0]["value"]) !== -1 && movie.poster_path != null) {
                              $('#results').append(`<a href="?code=${movie.id}" title="${movie.title} | ${movie.release_date}"><img src="https://image.tmdb.org/t/p/w92${movie.poster_path}"></a>`);
                          };
                      });
                  });
              },
              error: function (response) {
                  console.log(response);
              }
          });
      } else {
          $('#results').empty();
      };
  }, 500));
});