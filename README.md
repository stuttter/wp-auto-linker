# WP Auto Linker

Automatically link keywords to users, tags, categories, pages, and more. Associate key characters with URL patterns, and automatically link words in any output.

WP Auto Linker introduces a basic "hashtag" system, making it easy to tag, categorize, and automatically link to things in your content.

# Installation

* Download and install using the built in WordPress plugin installer.
* Activate in the "Plugins" area of your admin by clicking the "Activate" link.
* No further setup or configuration is necessary.

# FAQ

### What is all supported?

* `@` to link to users
* `#` to link to post tags
* `$` to link to post categories
* `^` to link to pages

### Examples

```
Hey everyone! @admin just created the ^about page. #tgif
```

```
Hey @bob, can you triage the comments on posts in $kanyewest?
```

### How do I create a custom linker?

Something like:

```
// Instantiate the linker
$linker = new WP_Auto_Linker();

// Author Archives
$linker->add_linker( array(
	'name'   => esc_html__( 'Single Posts', 'wp-auto-linker' ),
	'char'   => '!',
	'output' => array(
		'filter_single' => array( $linker, 'single_post_link' )
	),
	'input'  => false
) );
```

* Name - Not used, but could be used for a UI
* Char - The control character used on input & output
* Output - Accepts an array of filter callbacks for single, all, or no matches
* Input - Accepts an array of filter callbacks for single, all, or no matches

For now, you'll want to create your own callbacks for input & output. As this matures, ideally we'll have a collection of them that can be conveniently hooked into for any post type, taxonomy, etc...

### Is this performant?

On input, it's pretty performant. All it does is parse through looking for taxonomy terms to add to posts.

On output, it's maybe less-so, as `the_content` is filtered and links are applied. The more you link, the more objects need to be pulled up so they can be linked to. If you're caching objects and output, this shouldn't really matter to you. You are caching; right?

### Can I extend this for my own objects?

Yes! The main Autolinker class is flexible enough to be used on and for anything, and the `wp_auto_linker_setup_default_links()` function is a good example of how you might link your custom post-types & taxonomies together.

### Does this create new database tables?

No. It uses WordPress's custom post-type, custom taxonomy, and metadata APIs.

### Does this modify existing database tables?

No. All of WordPress's core database tables remain untouched.

### Where can I get support?

The WordPress support forums: https://wordpress.org/plugins/tags/wp-auto-linker/

### Can I contribute?

Yes, please!
