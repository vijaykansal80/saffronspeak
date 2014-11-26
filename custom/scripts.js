jQuery(document).ready(function() {

    // Expand subcategories on Design Resources page
    jQuery('a.subcategory-expander-link').click(function(e) {
      e.preventDefault();
      jQuery('.subcategory-expander div').slideToggle();
      jQuery(this).find('i').toggleClass('icon-caret-up');
      jQuery(this).find('.main').toggle();
      jQuery(this).find('.extra').toggle();
    });

    // Expand sections within a page
    jQuery('a.parent-expander-link').click(function(e) {
      if (!(jQuery(this).parent().prev('section.expander').is(":visible"))) {
        e.preventDefault();
      }
      jQuery(this).parent().prev('section.expander').slideToggle(800);
      jQuery(this).find('i').toggleClass('icon-caret-up');
      jQuery(this).find('.main').toggle();
      jQuery(this).find('.extra').toggle();
    });

    // Smooth scrolling
    jQuery(function() {
      jQuery('a[href*=#]:not([href=#])').click(function() {
        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
          var target = jQuery(this.hash);
          target = target.length ? target : jQuery('[name=' + this.hash.slice(1) +']');
          if (target.length) {
            jQuery('html,body').animate({
              scrollTop: target.offset().top
            }, 1000);
          return false;
          }
        }
      });
    });
});
