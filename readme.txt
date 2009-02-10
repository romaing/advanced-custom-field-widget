=== Advanced Custom Field Widget ===
CONTRIBUTORS: athenaofdelphi, scottwallick
TAGS: custom field, custom value, custom key, field, value, key, post meta, meta, get_post_meta, widget, sidebar, multiple widgets
REQUIRES AT LEAST: 2.5
TESTED UP TO: 2.7
STABLE TAG: 0.4

The Advanced Custom Field Widget is an extension of the Custom Field Widget by Scott Wallick, and displays values of custom field keys.

== Description ==

The Advanced Custom Field Widget is an extension of the Custom Field Widget by Scott Wallick, and displays values of custom field keys, allowing post- and page-specific meta sidebar content.

For more information about Scott's orginal Custom Field Widget, check out [plaintxt.org](http://www.plaintxt.org/experiments/custom-field-widget/).

== Installation ==

Installing this plugin, is just like installing any other WordPress plugin.

1. Download Advanced Custom Field Widget
2. Extract the `/adv-custom-field-widget/` folder from the archive
3. Upload this folder to `../wp-contents/plugins/`
4. Activate the plugin in *Dashboard > Plugins*
5. Customize from the *Design > Widgets* menu

In other words, just upload the `/adv-custom-field-widget/` folder and its contents to your plugins folder.

For more information about plugins and installing them, please review the [managing plugins](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins "Installing Plugins - WordPress Codex") section of the WordPress Codex.
 
== Advanced vs. Original ==

The key differences relate to the control you have over the content.

The original plugin provided the option to grab a custom field and drop it in a widget like this:-

`BEFORE WIDGET EVENT
   WIDGET TITLE
   TEXT
   CUSTOM FIELD
AFTER WIDGET EVENT`
	
I started using it, to provide a nice side bar link to Amazon for items I had reviewed.   This raised some points:-

* I found a problem with multiple post pages.  The widget would display the custom field value from the first post on the page
* I wanted to pick the selected custom field from random published posts
* I wanted to be able to select from two different custom fields with only one being used as the 'random' source (allowing me to link on a review page, but then to ignore the item as though I didn't recommend it when picking randomly form the list of available items)
* I wanted a portion of the widget to be fixed and permanently displayed
  
With all these points in mind, this is how the widget now operates.

First up, it has 4 new sections added.  Fixed text 1 and 2, pretext and posttext:-

`BEFORE WIDGET EVENT
  WIDGET TITLE
  FIXEDTEXT1 (If present)
  ----- ONLY PRESENT IF CUSTOM FIELD VALUE IS PRESENT ---
    TEXT (If present)
    PRETEXT (If present)
    CUSTOM FIELD 
    POSTTEXT (If present)
  -------------------------------------------------------
  FIXEDTEXT2 (If present)	
AFTER WIDGET EVENT`

The content I wanted to inject was nearly perfectly formed, but I needed to wrap it in some additional formatting.  Clearly, I'm not going to want to drop the same formatting in for every single occurence of the custom field, so I added `PRETEXT` and `POSTTEXT` to allow you to add standard content before and after the actual field content.

I also added:-

* `FIXEDTEXT1`
* `FIXEDTEXT2`

These two fields allow you to specify additional content in certain situations.  These fixed text items have four modes:-

* A - Displayed always (Takes priority over the others)
* M - Displayed only if there is some main content (if R is present, then R will take priority if content is randomised)
* R - Displayed if there is some main content and it has been randomised	
* N - Displayed if there is no main content
	
There are also two options, 'Randomise on single post pages' and 'Randomise on other pages'.  These allow you to have the widget pick random entries for the specified field from published articles, on pages (single and multiple post respectively) that don't have field entries of their own.  It should be noted that the random selections will come only from the primary key field (see below for more information).

At least one section (`FIXEDTEXT1`, `FIXEDTEXT2` or `CUSTOM FIELD`) MUST be present for the widget to be displayed at all.

You can also specify two key fields.  These obey the following rules:-

* The primary will always be used if data for it is present
* When displaying random content on pages that don't have linked fields, only content from the primary key field is used
* The secondary will be used only for single post pages if there is no data present in the primary field

*So what does this mean?*  Well, one of the reasons I modified Scott's original plugin was because I wanted to have specific items displayed on the pages they were reviewed on.  That was easy with the original.  And I then wanted to be able to randomly select a reviewed item for display on other pages.  That was also pretty easy, but then I got to thinking... what happens if I review something and I wouldn't recommend it in a million years.  I'd still want to link to it on the review page, but I wouldn't want to cycle it through other pages.  So, I added the secondary key field.  I have a key field called `amazon` and another called `amazon-notrecommended`.  If I want an item to cycle through I pop it in under the `amazon` field, if not, it goes in under the `amazon-notrecommended` field and then only ever gets seen on it's review page.

Version 0.2 added the ability to draw content for the widget from another page.  To use this facility, simply add the custom field 'acfw-linkto' to a page and specify the page ID that contains the content you want to draw in.  When writing a section that will have common sidebar content coming from the widget, you can write once and use many.

Version 0.3 added the ability to draw specific field content for the widget from another page.  To use this facility, simply add the custom field 'KEY-linkto' to a page (where KEY is the name of the custom field the widget is linked to).  So for example, lets say I want to draw content for the field 'amazon' from page 204, I would add the custom field 'amazon-linkto' and set it's value to 204.  This takes precedence over the general 'acfw-linkto' field.

Version 0.4 has changed the deactivation code such that it doesn't delete your settings, instead, settings are now deleted when you uninstall the plugin.  The translation domain has also been changed to 'acf-widget' and a POT file has been produced.

== License ==

Advanced Custom Field Widget, a plugin for WordPress, (C) 2008-09 by Christina Louise Warne (based on Custom Field Widget, a plugin for WordPress, (C) 2008 by Scott Allan Wallick, licensed under the [GNU General Public License](http://www.gnu.org/licenses/gpl.html "GNU General Public License")), is licensed under the [GNU General Public License](http://www.gnu.org/licenses/gpl.html "GNU General Public License").

Advanced Custom Field Widget is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

Advanced Custom Field Widget is distributed in the hope that it will be useful, but **without any warranty**; without even the implied warranty of **merchantability** or **fitness for a particular purpose**. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with Advanced Custom Field Widget. If not, see [http://www.gnu.org/licenses/](http://www.gnu.org/licenses/ "GNU General Public Licenses").

== Frequently Asked Questions ==

= What does this plugin do? =

Advanced Custom Field Widget displays the custom field values of a specified custom field key in your sidebar.

= Isn't this just a rip off of Scott's plugin? =

No, I don't think so, a whole bunch of stuff has been added.

= Aren't you just trying to steal his thunder? =

No, if I were, would I have even mentioned that the plugin is based on another?  Would I have stated quite clearly that it was originally written by a guy called Scott?  Would I have linked to his site?

If I was into plagiarism, then maybe I would have neglected to mention him, but I'm not.  So credit where it's due.  Scott wrote the original plugin and I'm very grateful to him for doing so, because he did a good job... nice code, with plenty of comments.  As a result, I was able to quickly understand the mechanics involved and modify it to suit my needs, and in the spirit of open source, I'm now making my version available for anyone who wants it.

= Why 'Advanced Custom Field Widget' and not 'Custom Field Widget 0.2' ? =

Well, simply...

* It's not my decision what happens with Scott's plugin, so to call it 'Custom Field Widget 0.2' would have been rude
* It's highly likely I'll modify this version further
* Compared to the orginal, it is slightly more advanced and provides you with more control

= Uninstalling The Plugin =

I got tired of the plugin deleting it's configuration when it was deactivated.  Now it's available via WordPress.org, I figured I needed to do something about it given that people will use the automatic update feature and watch their configuration disappear as the plugin is deactivated during the upgrade.

So, the plugin now does not delete it's configuration when it is deactivated.  Instead it uses the 'uninstall.php' feature available in WordPress 2.7+ to clean up the options in the database when it is physically deleted, and then only if the user has the ability to activate plugins.

If you find the plugin works with earlier versions of WordPress and you want to clean up your DB when you uninstall it, the options field you are looking for is 'widget_adv_custom_field'.

This uninstall function is new (as of version 0.4), so if it's slightly buggy, please let me know and I'll see if I can fix it up.  I have just spent quite a while getting it to work and making sure it only deletes the config when it is deleted, but I'm only human and I could have got it wrong, so if you lose your config... apologies... profuse apologies.

== Screenshots ==

1. Widget configuration options
2. In place on my site displaying the Amazon associates links connected to items I've reviewed