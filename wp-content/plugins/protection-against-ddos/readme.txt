=== Protection Against DDoS ===
Contributors: wpchefgadget
Tags: DDoS, peformance, security, brute force, login
Requires at least: 3.5.2
Tested up to: 5.4
Stable tag: 1.5.2

Protects your login, xmlrpc and RSS feeds pages against DDoS attacks. Denies access to your site from certain countries via CloudFlare.

== Description ==

This plugin resolves performance issues caused by brute force attacks described in the WordPress Codex here: [https://codex.wordpress.org/Brute_Force_Attacks](https://codex.wordpress.org/Brute_Force_Attacks)

> **From WordPress Codex:**

> *Due to the nature of these attacks, you may find your server's memory goes through the roof, causing performance problems. This is because the number of http requests (that is the number of times someone visits your site) is so high that servers run out of memory.*

> *A common attack point on WordPress is to hammer the wp-login.php file over and over until they get in or the server dies. You can do some things to protect yourself.*

Protection Against DDoS plugin addresses these issues very well.

It also allows to deny access to common WordPress features that get frequently attacked, like xmlrpc or RSS feeds pages.

CloudFlare users can allow or deny access for visitors from specified countries.

**All checks are done via the .htaccess file so that bogus requests can't even reach your WordPress site and get bounced at the web server level.** You can also specify exactly where they can be bounced to.

= Compatibility =

* Doesn't have any known conflicts with any other security plugins.
* Fully compatible with WordPress multisites.

Advanced users can get more technical information on the [FAQ page](https://wordpress.org/plugins/protection-against-ddos/faq/).

== Frequently Asked Questions ==

= How does the plugin work? =

The plugin starts working right after you install it. It utilizes a very simple idea: when a real user accesses the login page, the plugin sets a validation cookie for this user. After the user submitted the log in form, the plugin checks if the cookie is there and correct. If so, the user is allowed to log in. Otherwise the user gets bounced off. Since malicious bots attack the WordPress login page directly, they don't get the protection cookie and hence always get bounced off. Moreover validation happens at the server level BEFORE WordPress is even accessed (via .htaccess file) and hence no load is directed to the WordPress at all. The secure cookie is encrypted and unique for every site so the bots can't falsify it. Simple and effective!

= Can it protect against any DDoS attack? =

This plugin protects against DDoS CAUSED by brute-force attacks ONLY. This is the most common cause for an operational WordPress site to be down though. If your site is under attack for other reasons (for example if you got a lot of traffic to one of your posts) this plugin will not help!

= What are the system requirements? =

This plugin only works on the servers that support .htaccess files. Most Linux servers do.

== Screenshots ==

1. Settings page.

== Changelog ==

= 1.5.2 =
* Added access control for autodiscover/autodiscover.xml and wpad.dat.

= 1.5.1 =
* Can deny access to xmlrpc, RSS and certain countries now.

= 1.4.1 =
* Multisite compatibility implemented.

= 1.3 =
* Validation cookie is set via JavaScript now and encrypted.

= 1.2 =
* Redirect POST-requests only for login page.

= 1.1 =
* Set validation cookie for all GET-requests.
* Use random cookie name for better security.

= 1.0 =
* Initial release.