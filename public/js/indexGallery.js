$(function () {
    var qsRegex;
  
    $('.noresults').hide();
  
    var $grid = $('.grid').isotope({
      itemSelector: '.movie',
      masonry: {
        gutter: 10,
        fitWidth: true
      },
      getSortData: {
        release: '.release',
        updated: '.updated'
      }
    });

    // select filters
    $('.all-filters').on('change', 'select.filter', function () {
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
        $('html').animate({scrollTop: 0});
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

    $('.sort').on('change', function () {
      $grid.isotope({ 
        sortBy: $(this).find(':selected').attr('data-sort-value'),
        sortAscending: false 
      });
    });

    $('.reset').on('click', function () {
        $('.grid').isotope({filter: '*',});
        $grid.isotope({ sortBy: 'original-order', sortAscending: true });
        $('.filter, .sort').val('*');
        $('.quicksearch').val('');
        $('.selectpicker').selectpicker('val', '*');
        $('.noresults').hide();
        updateFilterCount();
        $('html').animate({scrollTop: 0});
    });
});