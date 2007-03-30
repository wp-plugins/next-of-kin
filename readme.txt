=== Next Of Kin ===
Tags: death, will, afterlife

Next Of Kin is the plugin we don't want to install. It handles what happens after we die.
It monitors your own visits to your wordpress system, and will send you a warning email after a number of
weeks (of your choice) without a visit.
If you fail to visit your blog even after that, the system will send a mail you wrote to whoever you choose.

All time intervals and messages are customizable.

This plugin is useful for those that want their blog to out-live them, and serve as an online memorial.
Even without use of this plugin, all are recommended to make sure someone can handle their website in
case of emergency.

== Installation ==

1. 	Upload to your plugins folder, usually `wp-content/plugins/`
2. 	Activate the plugin on the plugin screen
3. 	The plugin works per user - each user on your wordpress that wants its functionality, should go to
	Options -> Next Of Kin, and enable it there.
4.	Once enabled, input your options and email messages.

== Frequently Asked Questions ==

= Why do I need to use a plugin like this? I'm going to live for ever! =

No, you're not.

= I got a warning email, what do I do? =

Go to your options panel, and hit Reset.

= Does this count only visits to my dashboard, or will it know me when I visit the blog? =

Every visit to your wordpress system, blog or dashboard, count, as long as you're logged in.

= What if I just don't care much for my blog, and visit it only once every few months? =

First, you can set the time intervals, up to 30 weeks before the system assumes you're no longer.
Secondly, maybe this plugin is not for you. The idea behind this plugin is that we visit our blogs
daily, if not hourly, so if something happens to you, the blog will be the first to feel it.

= I'm not using this plugin because I'm afraid my mother will get a false email from me saying I'm dead! =

The system will first send you a warning email after a default time period of two weeks without a visit.
A week later it will send you another warning, and also a warning to your chosen next of kin, whom
you can ask to contact you and tell you to go in and reset. only after these two time periods, will the
plugin allow itself to assume the worst.

= How can I know this will work? =

If you're eager to test this before it matters, edit nextofkin.php: line 45 states the number of seconds in
a week. If you change this to 300, then the time intervals will be five minutes worth, instead of a week.
Enter an email address of your own as the second recepient (If you only have one, try using a mytrashmail.com
address), and don't access your blog for a short while.
Note - somebody needs to access your blog for the mails to be sent, but with search robots and spam bots, it
shouldn't take long.
After you finish testing, fix line 45 to its inital value (7 * 24 * 60 * 60 = 604800) and hit Reset in the options panel.
