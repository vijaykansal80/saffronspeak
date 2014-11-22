jQuery(document).ready(function(){
    jQuery('a.subcategory-expander-link').click(function(e) {
      e.preventDefault();
      jQuery('.subcategory-expander div').slideToggle();
      jQuery(this).find('i').toggleClass('icon-caret-up');
      jQuery(this).find('.main').toggle();
      jQuery(this).find('.extra').toggle();
    });

    jQuery('a.parent-expander-link').click(function(e) {
      e.preventDefault();
      jQuery(this).parent().prev('section.expander').slideToggle(800);
      jQuery(this).find('i').toggleClass('icon-caret-up');
      jQuery(this).find('.main').toggle();
      jQuery(this).find('.extra').toggle();
    });
});
