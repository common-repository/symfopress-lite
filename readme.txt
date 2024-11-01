=== SymfoPress Lite ===
Contributors: netandreus
Donate link: http://tokarchuk.ru/
Tags: symfony, symfony2, symfony 2, intergation, wordpress, symfony wordpress, composer
Requires at least: 3.0
Tested up to: 3.7
Stable tag: trunk
License: GPLv3

Integrates Symfony 2 Framework to WordPress. Allows you flexible development.

== Description ==

This plugin adds ability to use Symfony 2 components and bundles in your WordPress installation.
You can use Symfony bundles as well as WordPress plugins and develop what you need.

Plugin homepage:
http://tokarchuk.ru/

For support use WordPress.org or this page:
http://tokarchuk.ru/

Thanks!

== Installation ==

1. Upload the `symfopress-lite` folder to `/wp-content/plugins/`.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings -> SymfoPress, click 'Install' opossite 'Install vendors' line.
4. Wait for complete Symfony 2 vendors installation and click to 'Activate dispatching'.
5. Tick the "Activate dispatching" and submit form.
6. Go to /symfopress-demo/ according to view results of running Sf2 code.

== Changelog ==

= 1.0 =
* Add automatic vendors install throught wordpress admin panel.

= 0.5 =

* Starting developing plugin

== Frequently Asked Questions ==

= Where is the code of my demo page(/symfopress-demo/)? =
Controller part is in ./wp-content/plugins/symfopress/src/NetandreusSymfopressBundle/Controller/DemoController.php function indexAction().
And View of this action is in ./wp-content/plugins/symfopress/src/NetandreusSymfopressBundle/Resources/views/Demo/index.html.twig

== Screenshots ==

1. This is a screen shot of SymfoPress Settings (Settings -> Symfopress). When plugin is activated you can install symfony 2 components (vendors).
2. Vendors are installed successfull. Now you can activate dispatching.
3. Dispatching activation.
4. Now dispatching activated. You can switch to demo page.
5. Symfony 2 demo page in your WordPress installation.


= What template engine is using? =
Default template engine in SymfoPress is Twig (http://twig.sensiolabs.org/)

= Where is database connections properties? =
SymfoPress using WordPress wp-config.php file to determine connection properties.

== Upgrade Notice ==
= None. =