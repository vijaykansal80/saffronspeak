jQuery(document).ready(function(){
    jQuery('a.subcategory-expander-link').click(function(e) {
      e.preventDefault();
      jQuery('.subcategory-expander div').slideToggle();
      jQuery(this).find('i').toggleClass('icon-caret-up');
    });
});
