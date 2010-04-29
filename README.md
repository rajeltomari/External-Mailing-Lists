Nova External Mailing Lists
===========================
Developer: Dustin Lennon
Email: demonicpagan@gmail.com

This application is developed under the licenses of Nova and CodeIgniter.

Install Instructions
--------------------
The following application will alter your Nova installation to handle external mailing lists. To install this
application you need to perform the following steps.

*Note: This at this moment assumes that the external mailing list you are using allows anyone to post to alleviate
the need to add each one of your members of your group to your mailing list. However, if you opt to go this route,
that is totally your choice and you are free to do so. You can read how this MOD started here:
http://forums.anodyne-productions.com/viewtopic.php?f=62&t=2806

1. Log into your Nova installation.

2. Goto Site Management > Settings.

3. Click "Manage User-Created Settings »"

4. Click "Add User-Created Setting"

5. Fill in the Label text box and Setting Key text box.
   Example (the setting key in this example is what is used in write.php):
     Label: External Mailing List
	 Setting Key: external_mailing_list (this is only an example, make this whatever you want, just remember it for
	 use in write.php)

(You could create 3 different settings for the different kinds of posts if you want, just repeat steps 4 and 5 as
needed.)

6. Upload application/controllers/write.php to your application/controllers folder of your Nova install replacing 
the existing one if you haven't already modified this file. If you already have changes in this file, it's best 
that you just take the contents of this file and add it into your existing write.php file.

***UCIP MEMBERS***
You will need to modify the application/controllers/write.php to avoid getting double emails (one from Nova, one from
UCIP mailing list).

Comment out every occurance of $this->email->cc($this->settings->get_setting('external_mailing_list');

Change the following lines

Line	From						To
70 		$this->email->to($to);		$this->email->to($this->settings->get_setting('external_mailing_list');
154		$this->email->to($to);		$this->email->to($this->settings->get_setting('external_mailing_list');
256		$this->email->to($to);		$this->email->to($this->settings->get_setting('external_mailing_list');

If you experience any issues please submit a bug report on
http://github.com/demonicpagan/Nova-External-Mailing-Lists/issues.

You can always get the latest source from http://github.com/demonicpagan/Nova-External-Mailing-Lists as well.

Changelog - Dates are in Epoch time
-----------------------------------
1272510152: Created a more readable README for GitHub.

1270020606: Started work with external email lists. This will submit news, logs, and posts to an external mailing
			list that you have set up in your Nova settings.
