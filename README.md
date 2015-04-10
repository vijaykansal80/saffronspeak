This is a custom theme for the Saffron Marigold blog, built on  `_s` for a solid foundation.


# Get started

First, you'll want to have a new WordPress install on your local development machine. I recommend using [MAMP](https://codex.wordpress.org/Installing_WordPress_Locally_on_Your_Mac_With_MAMP) for a simple, but relatively flexible option.

Once that's done, open up your terminal.
`cd` into your `/wp-content/themes` directory, then clone the repo:
`git clone git@github.com:Saffron-Marigold/saffronspeak.git`

Log into your WordPress admin, change the theme to "Safflower," and now you're cooking with gas!

# Install some plugins

In order to ensure that you can see and test all the theme features, I recommend installing the following plugins:

- Jetpack
- Relevanssi
- Advanced Excerpt
- WordPress Popular Posts
- Category Order and Taxonomy Terms Order
- Yet Another Related Posts Plugin

# Making changes to your CSS

While you can adjust the theme's style.css file directly, I'd highly recommend using Sass instead. This will ensure that your code will be much more maintainable in the future, and will make changes far simpler. If you're totally new to Sass, it may help to [read this guide](http://thesassway.com/beginner/getting-started-with-sass-and-compass). Sass uses the same syntax as regular CSS, so it isn't hard to get started with.

## Getting started with Sass

First, you'll need to [install Compass](http://compass-style.org/install/).

This repo already includes a config file that will compile your stylesheets, so all you need to do is open your Terminal, navigate to the theme directory, and type `compass watch`.

Now, open any .scss file in the `/sass` folder and make a change. (Try changing a few of the colour variables in `/sass/variables-site/colors.scss` for very obvious results.) Refresh your test environment, and you'll see the colours throughout the site have changes!

Try to keep your Sass as DRY as possible. Variables are set in `/variables-site` and reusable snippets are available in `/mixins`. Use these wherever possible. Try to `@extend` elements rather than rewriting the same blocks of code.

# Responsive breakpoints

Breakpoints have been defined within `/sass/variables-site/_breakpoints.scss`. The code has been re-written to be mobile-first as much as possible, and media queries are mixed into the code for easier back-and-forth.

You can target a breakpoint like so:

    a
      @include tablet {
        color: purple;
      }
    }

This will make all links purple at tablet size or above.

You can also use the `.hide-mobile` CSS class anywhere within your HTML to selectively choose elements (spans of text, etc) that shouldn't display on mobile devices. This can make it easier to adapt longer strings of text for a smaller screen.

## Using the grid

We're using [Girder](http://comfypixel.com/Girder) for our grid structure. To keep as much of the presentational markup in the CSS as possible, we're primarily using its mixins for the grid structures, like so:

    .panel {
      @include unit(half);
    }

Consult the [documentation](http://comfypixel.com/Girder/guide.html) for more in-depth usage.

# Committing your changes

Generally speaking, I'd recommend working on changes in a feature branch. This way, you can test your changes on the test server prior to pushing to the production server.

*Changes should only be pushed to master only once they've been fully tested!*

To test your changes on the test server, log in via SSH and pull the branch:

    ssh ssarwate@ssarwate.mail.pairserver.com
    cd public_html/blog/wp-content/themes/safflower
    git fetch origin
    git checkout -b [branch-name] origin/[branch-name]

Switch between existing branches by checking them out:

    git checkout [branch-name]


Once you're certain your feature will work as expected, merge the branch to master, and pull to the production machine.

If you're new to feature branches, [read this guide](https://guides.github.com/introduction/flow/) for more information.
