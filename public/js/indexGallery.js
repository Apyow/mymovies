$(function () {
    var qsRegex;
  
    $('.noresults').hide();
  
    var $grid = $('.grid').isotope({
      itemSelector: '.movie',
      masonry: {
        gutter: 10,
        fitWidth: true
      }
    });
  
    // select filters
    var $filters = $('.all-filters').on('change', 'select.filter', function () {
        $('.noresults').hide();
        var isoFilters = [];
        $('.filter').each(function () {
            var filter = $(this).val();
            if (filter !== '*') {
                isoFilters.push($(this).find(':selected').attr('data-filter'));
            }
        });
  
        var selector = isoFilters.join('');              
        $('.grid').isotope({filter: selector});
  
        if ($('.grid').data('isotope').filteredItems.length === 0) { $('.noresults').show(); }
        updateFilterCount();
        return false;
    });
  
    // quicksearch filter
    var $quicksearch = $('.quicksearch').keyup( debounce( function() {
      qsRegex = new RegExp( $quicksearch.val(), 'gi' );
  
      $grid.isotope({
        filter: function() {
          var $this = $(this);
          var searchResult = qsRegex ? $this.text().match( qsRegex ) : true;
          return searchResult;
        }
      });
  
      updateFilterCount();
    }) );
  
    // debounce so filtering doesn't happen every millisecond
    function debounce( fn, threshold ) {
      var timeout;
      return function debounced() {
        if ( timeout ) {
          clearTimeout( timeout );
        }
        function delayed() {
          fn();
          timeout = null;
        }
        setTimeout( delayed, threshold || 100 );
      };
    }
  
    function updateFilterCount() {$('.filter-count').text( $grid.data('isotope').filteredItems.length );}
    updateFilterCount();
  
    $('.reset').on('click', function () {
        $('.grid').isotope({filter: '*',});
        $('.filter').val('*');
        $('.quicksearch').val('');
        $('.selectpicker').selectpicker('val', '*');
        $('.noresults').hide();
        updateFilterCount();
    });
});