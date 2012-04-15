Nova External Mailing Lists
===========================
Developer: Dustin Lennon<br />
Email: <demonicpagan@gmail.com>

This application is developed under the licenses of Nova and CodeIgniter.

Install Instructions
--------------------
The following application will alter your Nova installation to send to an external mailing list(s). To install this
application you need to perform the following steps.

*Note: This application, at this moment, assumes that the external mailing list you are using allows anyone to post to alleviate
the need to add each one of your members of your group to your mailing list.

1. Log into your Nova installation.

2. Goto Site Management > Settings.

3. Click "Manage User-Created Settings &raquo;"

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