=== Auto Post Title ===
Contributors: thoughtlabllc, lellimecnar
Tags: title, post title, auto, custom post type, meta, post meta
Requires at least: 2.0.2
Tested up to: 3.1
Stable tag: trunk

Use shortcodes to automatically generate and format titles.

== Description ==

There are times when you want to automatically include information into the title of a post, page, or custom post type. This plugin adds an item to the Admin Settings menu called "Title Format." Clicking on this link will give you an options page where you will find a text field for each post type that you have registered in WordPress, including Post and Page. In these text fields, you are able to use shortcodes to build a title format that will be used to generate the title of all posts of that type. For example, say I had a blog with several authors, and I wanted to include the author's name into the title of every post. I could use something like:
`[title] - by [author]`
So, if I had a post by John Smith, with a title of "An Excellent Post," the title would be displayed as: "An Excellent Post - by John Smith"

> Here are the tags that are available: 
> 
> - id
> - title
> - author
> - date (*use the attribute: format="" to format date. [Click here](http://php.net/manual/en/function.date.php) for formatting info.*)
> - modified (*also takes the format="" attribute.*)
> - category
> - content
> - excerpt
> - status (*published, draft, etc.*)
> - type (*the post type*)
> - name (*the post slug*)
> - comments (*show number of comments*)

---

###Another example:

I might have a website which uses custom post types to catalog my inventory of cars for sale. My 'cars' post type will be displayed in the Auto Post Title options page as 'Cars Title.'
Assuming that I have already set up my cars with a year, make, and model as meta data, I would then enter something like this:
`[year] [make] [model] For Sale!`
Each of my cars will then be shown with a title similar to: "2011 Chevy Camaro For Sale!"

---

You can also use custom taxonomies as tags. Only the first term will be used.

== Installation ==

1. Upload `auto-post-title.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Navigate to "Title Format" under the admin settings menu.
1. Type your formatting rules into the appropriate text fields.

== Screenshots ==

1. This is the optios page as you will see it if you have no custom post types registered.

== Changelog ==

= 1.1 =
* Fixed problem with navigation menu showing the formatted title on single posts or pages.

= 1.2 =
* Added support for post category, and custom taxonomies.

= 1.2.1 =
* Fixed custom titles not showing in title tags.

= 1.2.2 =
* Fixed a bug with 1.2
