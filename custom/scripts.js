( function( $ ) {

  $(document).ready(function() {

      // Expand subcategories on Design Resources page
      $('a.subcategory-expander-link').click(function(e) {
        e.preventDefault();
        $('.subcategory-expander div').slideToggle();
        $(this).find('i').toggleClass('icon-caret-up');
        $(this).find('.main').toggle();
        $(this).find('.extra').toggle();
      });

      // Expand sections within a page
      $('a.parent-expander-link').click(function(e) {
        if (!($(this).parent().prev('section.expander').is(":visible"))) {
          e.preventDefault();
        }
        $(this).parent().prev('section.expander').slideToggle(800);
        $(this).find('i').toggleClass('icon-caret-up');
        $(this).find('.main').toggle();
        $(this).find('.extra').toggle();
      });

      // Add a placeholder to search input
      $( '#s' ).attr( 'placeholder', 'Enter your search terms here.' );

      // Pull out the search bar when clicked
      $( '.icon-search' ).on( 'click', function() {
        var $searchInput = $( '#s' );
        if ( $searchInput.hasClass( 'open' ) ) {
          $( '#searchform' ).submit();
        } else {
          $searchInput.addClass( 'open' );
          $searchInput.focus();
          return false;
        }
      });

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
