# [My Own Plugins Tab](https://github.com/brasofilo/My-Own-Plugins-Tab)
<sup>*Version 2013.10.21*</sup>

####*Separate your plugins from the others in the Plugins admin screen.*

**For developers**:  
see your plugins, and others', in separated tabs, quite useful when they start to count in dozens.

**For everybody**:  
simply add a comma-separated list of authors and you'll be able to sort the plugins in two groups.

----
 > ###ICON MARKING my plugins

![plugins screen](assets/screenshot-1.png)

----
 > ###PLUGIN SETTINGS

![plugins screen](assets/screenshot-2.png)

----
 > ###WHEN VIEWING 
 > ####<sup>our-or-their plugins (marked counts reflect the screen being viewed -not mine in this case).</sup>

![plugins screen](assets/screenshot-3.png)

----
## Credits
 - Uses [Font Awesome](http://fortawesome.github.io/Font-Awesome/), loaded from a [CDN](http://www.bootstrapcdn.com/#tab_fontawesome), fallback to bundled copies.

 - Plugin settings idea plugged from [WP Maintenance Mode](http://wordpress.org/plugins/wp-maintenance-mode/).

 - The plugin updater is an adaptatation from [***YahnisElsts / plugin-update-checker***](https://github.com/YahnisElsts/plugin-update-checker).  
   I have a testing plugin here: [GitHub Plugin Update Checker](https://github.com/brasofilo/github-plugin-update-checker).
 
## FAQ
The plugin works in Multisite, further tests are needed to confirm/improve the behavior/settings.

## Changelog

**Version 2013.10.21**

* Fixed updater, it was being blocked by `$pagenow==plugins.php`, it runs also in `update.php` and `update-core.php`.

**Version 2013.10.19**

* Trying to get Multisite right, I think it's ok
* Alternative local embed to FontAwesome CDN files

**Version 2013.10.14**

* Plugin launch

## Licence
Released under GPL, you can use it free of charge on your personal or commercial blog.