# WP Auto Linker

Associate key characters with URL patterns, and automatically link words in any output

WP Auto Linker introduces a basic "hashtag" system, making it easy to tag, categorize, and automatically link to things in your content.

# Installation

* Download and install using the built in WordPress plugin installer.
* Activate in the "Plugins" area of your admin by clicking the "Activate" link.
* No further setup or configuration is necessary.

# FAQ

### What is all supported?

* "@" to link to users
* "#" to link to post tags
* "$" to link to post categories
* "^" to link to pages

### Examples

{{{
Hey everyone! @admin just created the ^about page. #tgif
}}}

{{{
Hey @bob, can you triage the comments on posts in $kanyewest?.
}}}

### Is this performant?

On input, it's pretty performant. All it does is parse through looking for taxonomy terms to add to posts.

On output, it's maybe less-so, as `the_content` is filtered and links are applied. The more you link, the more objects need to be pulled up so they can be linked to. If you're caching objects and output, this shouldn't really matter to you. You are caching; right?

### Does this create new database tables?

No. It uses WordPress's custom post-type, custom taxonomy, and metadata APIs.

### Does this modify existing database tables?

No. All of WordPress's core database tables remain untouched.

### Where can I get support?

The WordPress support forums: https://wordpress.org/tags/wp-event-calendar/

### Can I contribute?

Yes, please! The number of users needing events and calendars in WordPress is always growing. Having an easy-to-use API and powerful set of functions is critical to managing complex WordPress installations. If this is your thing, please help us out!
