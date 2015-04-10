( function( $ ) {

  $(document).ready(function() {

    // Sometimes WordPress adds an inline width attribute to figures.
    // This messes up our columns and makes everything sad. So let's get rid of it!
    $('figure').css('width', '');

    // Expand subcategories on Design Resources page
    $('a.subcategory-expander-link').click(function(e) {
      e.preventDefault();
      $('.subcategory-expander div').slideToggle();
      $(this).find('i').toggleClass('icon-caret-up');
      $(this).find('.main').toggle();
      $(this).find('.extra').toggle();
    });

    // Expand sections within a page (used for the Holiday series in particular, but could be used anywhere)
    $('a.parent-expander-link').click(function(e) {
      if (!($(this).parent().prev('section.expander').is(":visible"))) {
        e.preventDefault();
      }
      $(this).parent().prev('section.expander').slideToggle(800);
      $(this).find('i').toggleClass('icon-caret-up');
      $(this).find('.main').toggle();
      $(this).find('.extra').toggle();
    });

    // Add a placeholder to search input and declare variable for the search input
    var $searchInput = $( '.search-field' );
    $searchInput.attr( 'placeholder', 'Enter your search terms here.' );

    // Deal with clicks on search icon
    $( '.icon-search' ).on( 'click', function() {
      // If the search bar is already open
      if ( $searchInput.hasClass( 'open' ) ) {
        if ( $searchInput.val() ) {
          // Submit our search if there's a search term entered
          $( '.search-form' ).submit();
        } else {
          // Otherwise, close the search box
          $searchInput.removeClass( 'open' );
        }
      // If the search bar isn't already open, open it!
      } else {
        $searchInput.addClass( 'open' );
        $searchInput.focus();
        return false;
      }
    } );

    // If we're on the homepage and we're not on a mobile device, show the search box expanded by default
    if ( $( 'body' ).hasClass( 'home' ) && ! $( '.menu-toggle:visible' ).length ) {
      $searchInput.addClass( 'open' );
    }

    

    // Make sure empty searches aren't submitted
    $( '#searchform' ).on( 'submit', function( e ) {
      if ( ! $searchInput.val() ) {
        e.preventDefault();
        $( '#s' ).attr( 'placeholder', 'Don’t forget to enter a search term!' );
      }
    } );

    // Smooth scrolling
    $(function() {
      $('a[href*=#]:not([href=#])').click(function() {
        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
          var target = $(this.hash);
          target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
          if (target.length) {
            $('html,body').animate({
              scrollTop: target.offset().top
            }, 1000);
          return false;
          }
        }
      });
    });
  });

} )( jQuery );
