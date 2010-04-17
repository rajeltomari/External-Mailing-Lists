<?php
/*
|---------------------------------------------------------------
| ADMIN - WRITE CONTROLLER
|---------------------------------------------------------------
|
| File: controllers/write.php
| System Version: 1.0
|
| Controller that handles the WRITE section of the admin system.
|
*/

require_once APPPATH . 'controllers/base/write_base.php';

class Write extends Write_base {

	function Write()
	{
		parent::Write_base();
	}

	function _email($type = '', $data = '')
	{
		/* load the libraries */
		$this->load->library('email');
		$this->load->library('parser');

		/* define the variables */
		$email = FALSE;

		switch ($type)
		{
			case 'news':
				/* set some variables */
				$from_name = $this->char->get_character_name($data['author'], TRUE, TRUE);
				$from_email = $this->user->get_email_address('character', $data['author']);
				$subject = $data['category'] .' - '. $data['title'];

				/* set the content */
				$content = sprintf(
					lang('email_content_news_item'),
					$from_name,
					$data['content']
				);

				/* set the email data */
				$email_data = array(
					'email_subject' => $subject,
					'email_content' => ($this->email->mailtype == 'html') ? nl2br($content) : $content
				);

				/* where should the email be coming from */
				$em_loc = email_location('write_newsitem', $this->email->mailtype);

				/* parse the message */
				$message = $this->parser->parse($em_loc, $email_data, TRUE);

				/* get the email addresses */
				$emails = $this->user->get_crew_emails(TRUE, 'email_news_items');

				/* make a string of email addresses */
				$to = implode(',', $emails);

				/* set the parameters for sending the email */
				$this->email->from($from_email, $from_name);
				$this->email->to($to);
				$this->email->cc($this->settings->get_setting('external_mailing_list'));
				$this->email->subject($this->options['email_subject'] .' '. $subject);
				$this->email->message($message);

				break;

			case 'news_pending':
				/* set some variables */
				$from_name = $this->char->get_character_name($data['author'], TRUE, TRUE);
				$from_email = $this->user->get_email_address('character', $data['author']);
				$subject = $data['category'] .' - '. $data['title'];

				/* set the content */
				$content = sprintf(
					lang('email_content_entry_pending'),
					lang('global_newsitem'),
					$data['title'],
					$from_name,
					lang('global_newsitem'),
					$data['content'],
					lang('global_newsitem'),
					site_url('login/index')
				);

				/* set the email data */
				$email_data = array(
					'email_subject' => $subject,
					'email_content' => ($this->email->mailtype == 'html') ? nl2br($content) : $content
				);

				/* where should the email be coming from */
				$em_loc = email_location('entry_pending', $this->email->mailtype);

				/* parse the message */
				$message = $this->parser->parse($em_loc, $email_data, TRUE);

				/* get the email addresses */
				$emails = $this->user->get_crew_emails(TRUE, 'email_news_items');

				/* make a string of email addresses */
				$to = implode(',', $this->user->get_emails_with_access('manage/news', 2));

				/* set the parameters for sending the email */
				$this->email->from($from_email, $from_name);
				$this->email->to($to);
				$this->email->subject($this->options['email_subject'] .' '. lang('email_subject_news_pending'));
				$this->email->message($message);

				break;

			case 'log':
				/* set some variables */
				$from_name = $this->char->get_character_name($data['author'], TRUE, TRUE);
				$from_email = $this->user->get_email_address('character', $data['author']);
				$subject = $from_name ."'s ". lang('email_subject_personal_log') ." - ". $data['title'];

				/* set the content */
				$content = sprintf(
					lang('email_content_personal_log'),
					$from_name,
					$data['content']
				);

				/* set the email data */
				$email_data = array(
					'email_subject' => $subject,
					'email_content' => ($this->email->mailtype == 'html') ? nl2br($content) : $content
				);

				/* where should the email be coming from */
				$em_loc = email_location('write_personallog', $this->email->mailtype);

				/* parse the message */
				$message = $this->parser->parse($em_loc, $email_data, TRUE);

				/* get the email addresses */
				$emails = $this->user->get_crew_emails(TRUE, 'email_personal_logs');

				/* make a string of email addresses */
				$to = implode(',', $emails);

				/* set the parameters for sending the email */
				$this->email->from($from_email, $from_name);
				$this->email->to($to);
				$this->email->cc($this->settings->get_setting('external_mailing_list'));
				$this->email->subject($this->options['email_subject'] .' '. $subject);
				$this->email->message($message);

				break;

			case 'log_pending':
				/* set some variables */
				$from_name = $this->char->get_character_name($data['author'], TRUE, TRUE);
				$from_email = $this->user->get_email_address('character', $data['author']);
				$subject = $from_name ."'s ". lang('email_subject_personal_log') ." - ". $data['title'];

				/* set the content */
				$content = sprintf(
					lang('email_content_entry_pending'),
					lang('global_personallog'),
					$data['title'],
					$from_name,
					lang('global_personallog'),
					$data['content'],
					lang('global_personallog'),
					site_url('login/index')
				);

				/* set the email data */
				$email_data = array(
					'email_subject' => $subject,
					'email_from' => $from_name,
					'email_content' => ($this->email->mailtype == 'html') ? nl2br($content) : $content
				);

				/* where should the email be coming from */
				$em_loc = email_location('entry_pending', $this->email->mailtype);

				/* parse the message */
				$message = $this->parser->parse($em_loc, $email_data, TRUE);

				/* get the email addresses */
				$to = implode(',', $this->user->get_emails_with_access('manage/logs', 2));

				/* set the parameters for sending the email */
				$this->email->from($from_email, $from_name);
				$this->email->to($to);
				$this->email->subject($this->options['email_subject'] .' '. lang('email_subject_log_pending'));
				$this->email->message($message);

				break;

			case 'post':
				/* set some variables */
				$subject = $data['mission'] ." - ". $data['title'];
				$mission = lang('email_content_post_mission') . $data['mission'];
				$authors = lang('email_content_post_author') . $this->char->get_authors($data['authors'], TRUE);
				$timeline = lang('email_content_post_timeline') . $data['timeline'];
				$location = lang('email_content_post_location') . $data['location'];

				/* figure out who it needs to come from */
				$my_chars = array();

				/* find out how many of the submitter's characters are in the string */
				foreach ($this->session->userdata('characters') as $value)
				{
					if (strstr($data['authors'], $value) !== FALSE)
					{
						$my_chars[] = $value;
					}
				}

				/* set who the email is coming from */
				$from_name = $this->char->get_character_name($my_chars[0], TRUE, TRUE);
				$from_email = $this->user->get_email_address('character', $my_chars[0]);

				/* set the content */
				$content = sprintf(
					lang('email_content_mission_post'),
					$authors,
					$mission,
					$location,
					$timeline,
					$data['content']
				);

				/* set the email data */
				$email_data = array(
					'email_content' => ($this->email->mailtype == 'html') ? nl2br($content) : $content
				);

				/* where should the email be coming from */
				$em_loc = email_location('write_missionpost', $this->email->mailtype);

				/* parse the message */
				$message = $this->parser->parse($em_loc, $email_data, TRUE);

				/* get the email addresses */
				$emails = $this->user->get_crew_emails(TRUE, 'email_mission_posts');

				/* make a string of email addresses */
				$to = implode(',', $emails);

				/* set the parameters for sending the email */
				$this->email->from($from_email, $from_name);
				$this->email->to($to);
				$this->email->cc($this->settings->get_setting('external_mailing_list'));
				$this->email->subject($this->options['email_subject'] .' '. $subject);
				$this->email->message($message);

				break;

			case 'post_delete':
				/* set some variables */
				$subject = lang('email_subject_deleted_post');

				/* figure out who it needs to come from */
				$my_chars = array();

				/* find out how many of the submitter's characters are in the string */
				foreach ($this->session->userdata('characters') as $value)
				{
					if (strstr($data['authors'], $value) !== FALSE)
					{
						$my_chars[] = $value;
					}
				}

				/* set who the email is coming from */
				$from_name = $this->char->get_character_name($my_chars[0], TRUE, TRUE);
				$from_email = $this->user->get_email_address('character', $my_chars[0]);

				/* set the content */
				$content = sprintf(
					lang('email_content_mission_post_deleted'),
					$data['title'],
					$from_name
				);

				/* set the email data */
				$email_data = array(
					'email_content' => ($this->email->mailtype == 'html') ? nl2br($content) : $content
				);

				/* where should the email be coming from */
				$em_loc = email_location('write_missionpost_deleted', $this->email->mailtype);

				/* parse the message */
				$message = $this->parser->parse($em_loc, $email_data, TRUE);

				/* get the email addresses */
				$emails = $this->char->get_character_emails($data['authors']);

				foreach ($emails as $key => $value)
				{
					$pref = $this->user->get_pref('email_mission_posts_delete', $key);

					if ($pref == 'y')
					{
						/* don't do anything */
					}
					else
					{
						unset($emails[$key]);
					}
				}

				/* make a string of email addresses */
				$to = implode(',', $emails);

				/* set the parameters for sending the email */
				$this->email->from($from_email, $from_name);
				$this->email->to($to);
				$this->email->subject($this->options['email_subject'] .' '. $subject);
				$this->email->message($message);

				break;

			case 'post_pending':
				$chars = explode(',', $data['authors']);

				$from_name = $this->char->get_character_name($chars[0], TRUE, TRUE);
				$from_email = $this->user->get_email_address('character', $chars[0]);
				$subject = $data['mission'] ." - ". $data['title'];

				/* set the content */
				$content = sprintf(
					lang('email_content_entry_pending'),
					lang('global_missionpost'),
					$data['title'],
					$from_name,
					lang('global_missionpost'),
					$data['content'],
					lang('global_missionpost'),
					site_url('login/index')
				);

				/* set the email data */
				$email_data = array(
					'email_subject' => $subject,
					'email_from' => $from_name,
					'email_content' => ($this->email->mailtype == 'html') ? nl2br($content) : $content
				);

				/* where should the email be coming from */
				$em_loc = email_location('entry_pending', $this->email->mailtype);

				/* parse the message */
				$message = $this->parser->parse($em_loc, $email_data, TRUE);

				/* get the email addresses */
				$to = implode(',', $this->user->get_emails_with_access('manage/posts', 2));

				/* set the parameters for sending the email */
				$this->email->from($from_email, $from_name);
				$this->email->to($to);
				$this->email->subject($this->options['email_subject'] .' '. lang('email_subject_post_pending'));
				$this->email->message($message);

				break;

			case 'post_save':
				/* set some variables */
				$subject = $data['mission'] ." - ". $data['title'] . lang('email_subject_saved_post');
				$mission = lang('email_content_post_mission') . $data['mission'];
				$authors = lang('email_content_post_author') . $this->char->get_authors($data['authors'], TRUE);
				$timeline = lang('email_content_post_timeline') . $data['timeline'];
				$location = lang('email_content_post_location') . $data['location'];

				/* figure out who it needs to come from */
				$my_chars = array();

				/* find out how many of the submitter's characters are in the string */
				foreach ($this->session->userdata('characters') as $value)
				{
					if (strstr($data['authors'], $value) !== FALSE)
					{
						$my_chars[] = $value;
					}
				}

				/* set who the email is coming from */
				$from_name = $this->char->get_character_name($my_chars[0], TRUE, TRUE);
				$from_email = $this->user->get_email_address('character', $my_chars[0]);

				/* set the content */
				$content = sprintf(
					lang('email_content_mission_post_saved'),
					$data['title'],
					site_url('login/index'),
					$authors,
					$mission,
					$location,
					$timeline,
					$data['content']
				);

				/* set the email data */
				$email_data = array(
					'email_content' => ($this->email->mailtype == 'html') ? nl2br($content) : $content
				);

				/* where should the email be coming from */
				$em_loc = email_location('write_missionpost_saved', $this->email->mailtype);

				/* parse the message */
				$message = $this->parser->parse($em_loc, $email_data, TRUE);

				/* get the email addresses */
				$emails = $this->char->get_character_emails($data['authors']);

				foreach ($emails as $key => $value)
				{
					$pref = $this->user->get_pref('email_mission_posts_save', $key);

					if ($pref == 'y')
					{
						/* don't do anything */
					}
					else
					{
						unset($emails[$key]);
					}
				}

				/* make a string of email addresses */
				$to = implode(',', $emails);

				/* set the parameters for sending the email */
				$this->email->from($from_email, $from_name);
				$this->email->to($to);
				$this->email->subject($this->options['email_subject'] .' '. $subject);
				$this->email->message($message);

				break;
		}

		/* send the email */
		$email = $this->email->send();

		/* return the email variable */
		return $email;
	}
}

/* End of file write.php */
/* Location: ./application/controllers/write.php */