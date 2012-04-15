<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once MODPATH.'core/controllers/nova_write.php';

class Write extends Nova_write {

	public function __construct()
	{
		parent::__construct();
	}

	protected function _email($type, $data)
	{
		// load the resources
		$this->load->library('email');
		$this->load->library('parser');

		// define the variables
		$email = false;

		switch ($type)
		{
			case 'news':
			// set some variables
				$from_name = $this->char->get_character_name($data['author'], true, true);
				$from_email = $this->user->get_email_address('character', $data['author']);
				$subject = $data['category'] .' - '. $data['title'];

				// set the content
				$content = sprintf(
					lang('email_content_news_item'),
					$from_name,
					$data['content']
				);

				// set the email data
				$email_data = array(
					'email_subject' => $subject,
					'email_content' => ($this->email->mailtype == 'html') ? nl2br($content) : $content
				);

				// where should the email be coming from
				$em_loc = Location::email('write_newsitem', $this->email->mailtype);

				// parse the message
				$message = $this->parser->parse_string($em_loc, $email_data, true);

				// get the email addresses
				$emails = $this->user->get_crew_emails(true, 'email_news_items');

				// make a string of email addresses
				$to = implode(',', $emails);

				// set the parameters for sending the email
				//$this->email->from($from_email, $from_name);
				$this->email->from($from_email, $from_name);
				$this->email->to($to);
				$this->email->cc($this->settings->get_setting('external_mailing_list'));
				$this->email->subject($this->options['email_subject'] .' '. $subject);
				$this->email->message($message);
			break;

			case 'news_pending':
				// set some variables
				$from_name = $this->char->get_character_name($data['author'], true, true);
				$from_email = $this->user->get_email_address('character', $data['author']);
				$subject = $data['category'] .' - '. $data['title'];

				// set the content
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

				// set the email data
				$email_data = array(
					'email_subject' => $subject,
					'email_content' => ($this->email->mailtype == 'html') ? nl2br($content) : $content
				);

				// where should the email be coming from
				$em_loc = Location::email('entry_pending', $this->email->mailtype);

				// parse the message
				$message = $this->parser->parse_string($em_loc, $email_data, true);

				// get the email addresses
				$emails = $this->user->get_crew_emails(true, 'email_news_items');

				// make a string of email addresses
				$to = implode(',', $this->user->get_emails_with_access('manage/news', 2));

				// set the parameters for sending the email
				$this->email->from($from_email, $from_name);
				$this->email->to($to);
				$this->email->subject($this->options['email_subject'] .' '. lang('email_subject_news_pending'));
				$this->email->message($message);
			break;

			case 'log':
				// set some variables
				$from_name = $this->char->get_character_name($data['author'], true, true);
				$from_email = $this->user->get_email_address('character', $data['author']);
				$subject = $from_name ."'s ". lang('email_subject_personal_log') ." - ". $data['title'];

				// set the content
				$content = sprintf(
					lang('email_content_personal_log'),
					$from_name,
					$data['content']
				);

				// set the email data
				$email_data = array(
					'email_subject' => $subject,
					'email_content' => ($this->email->mailtype == 'html') ? nl2br($content) : $content
				);

				// where should the email be coming from
				$em_loc = Location::email('write_personallog', $this->email->mailtype);

				// parse the message
				$message = $this->parser->parse_string($em_loc, $email_data, true);

				// get the email addresses
				$emails = $this->user->get_crew_emails(true, 'email_personal_logs');

				// make a string of email addresses
				$to = implode(',', $emails);

				// set the parameters for sending the email
				$this->email->from($from_email, $from_name);
				$this->email->to($to);
				$this->email->cc($this->settings->get_setting('external_mailing_list'));
				$this->email->subject($this->options['email_subject'] .' '. $subject);
				$this->email->message($message);
			break;

			case 'log_pending':
				// set some variables
				$from_name = $this->char->get_character_name($data['author'], true, true);
				$from_email = $this->user->get_email_address('character', $data['author']);
				$subject = $from_name ."'s ". lang('email_subject_personal_log') ." - ". $data['title'];

				// set the content
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

				// set the email data
				$email_data = array(
					'email_subject' => $subject,
					'email_from' => $from_name,
					'email_content' => ($this->email->mailtype == 'html') ? nl2br($content) : $content
				);

				// where should the email be coming from
				$em_loc = Location::email('entry_pending', $this->email->mailtype);

				// parse the message
				$message = $this->parser->parse_string($em_loc, $email_data, true);

				// get the email addresses
				$to = implode(',', $this->user->get_emails_with_access('manage/logs', 2));

				// set the parameters for sending the email
				$this->email->from($from_email, $from_name);
				$this->email->to($to);
				$this->email->subject($this->options['email_subject'] .' '. lang('email_subject_log_pending'));
				$this->email->message($message);
			break;

			case 'post':
				// set some variables
				$subject = $data['mission'] ." - ". $data['title'];
				$mission = lang('email_content_post_mission') . $data['mission'];
				$authors = lang('email_content_post_author') . $this->char->get_authors($data['authors'], true);
				$timeline = lang('email_content_post_timeline') . $data['timeline'];
				$location = lang('email_content_post_location') . $data['location'];

				// get an array of authors
				$authorsArr = explode(',', $data['authors']);

				// find out what's the same
				$same = array_values(array_intersect($authorsArr, $this->session->userdata('characters')));

				// figure out who it should come from
				$from = (in_array($this->session->userdata('main_char'), $same)) ? $this->session->userdata('main_char') : $same[0];

				// set who the email is coming from
				$from_name = $this->char->get_character_name($from, true, true);
				$from_email = $this->user->get_email_address('character', $from);

				// set the content
				$content = sprintf(
					lang('email_content_mission_post'),
					$authors,
					$mission,
					$location,
					$timeline,
					$data['content']
				);

				// set the email data
				$email_data = array(
					'email_content' => ($this->email->mailtype == 'html') ? nl2br($content) : $content
				);

				// where should the email be coming from
				$em_loc = Location::email('write_missionpost', $this->email->mailtype);

				// parse the message
				$message = $this->parser->parse_string($em_loc, $email_data, true);

				// get the email addresses
				$emails = $this->user->get_crew_emails(true, 'email_mission_posts');

				// make a string of email addresses
				$to = implode(',', $emails);

				// set the parameters for sending the email
				$this->email->from($from_email, $from_name);
				$this->email->to($to);
				$this->email->cc($this->settings->get_setting('external_mailing_list'));
				$this->email->subject($this->options['email_subject'] .' '. $subject);
				$this->email->message($message);
			break;

			case 'post_delete':
				// set some variables
				$subject = lang('email_subject_deleted_post');

				// get an array of authors
				$authors = explode(',', $data['authors']);

				// find out what's the same
				$same = array_values(array_intersect($authors, $this->session->userdata('characters')));

				// figure out who it should come from
				$from = (in_array($this->session->userdata('main_char'), $same)) ? $this->session->userdata('main_char') : $same[0];

				// set who the email is coming from
				$from_name = $this->char->get_character_name($from, true, true);
				$from_email = $this->user->get_email_address('character', $from);

				// set the content
				$content = sprintf(
					lang('email_content_mission_post_deleted'),
					$data['title'],
					$from_name
				);

				// set the email data
				$email_data = array(
					'email_content' => ($this->email->mailtype == 'html') ? nl2br($content) : $content
				);

				// where should the email be coming from
				$em_loc = Location::email('write_missionpost_deleted', $this->email->mailtype);

				// parse the message
				$message = $this->parser->parse_string($em_loc, $email_data, true);

				// get the email addresses
				$emails = $this->char->get_character_emails($data['authors']);

				foreach ($emails as $key => $value)
				{
					$pref = $this->user->get_pref('email_mission_posts_delete', $key);

					if ($pref == 'y')
					{
						// don't do anything
					}
					else
					{
						unset($emails[$key]);
					}
				}

				// make a string of email addresses
				$to = implode(',', $emails);

				// set the parameters for sending the email
				$this->email->from($from_email, $from_name);
				$this->email->to($to);
				$this->email->subject($this->options['email_subject'] .' '. $subject);
				$this->email->message($message);
			break;

			case 'post_pending':
				// get an array of authors
				$authors = explode(',', $data['authors']);

				// find out what's the same
				$same = array_values(array_intersect($authors, $this->session->userdata('characters')));

				// figure out who it should come from
				$from = (in_array($this->session->userdata('main_char'), $same)) ? $this->session->userdata('main_char') : $same[0];

				// set who the email is coming from
				$from_name = $this->char->get_character_name($from, true, true);
				$from_email = $this->user->get_email_address('character', $from);
				$subject = $data['mission'] ." - ". $data['title'];

				// set the content
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

				// set the email data
				$email_data = array(
					'email_subject' => $subject,
					'email_from' => $from_name,
					'email_content' => ($this->email->mailtype == 'html') ? nl2br($content) : $content
				);

				// where should the email be coming from
				$em_loc = Location::email('entry_pending', $this->email->mailtype);

				// parse the message
				$message = $this->parser->parse_string($em_loc, $email_data, true);

				// get the email addresses
				$to = implode(',', $this->user->get_emails_with_access('manage/posts', 2));

				// set the parameters for sending the email
				$this->email->from($from_email, $from_name);
				$this->email->to($to);
				$this->email->subject($this->options['email_subject'] .' '. lang('email_subject_post_pending'));
				$this->email->message($message);
			break;

			case 'post_save':
				// set some variables
				$subject = $data['mission'] ." - ". $data['title'] . lang('email_subject_saved_post');
				$mission = lang('email_content_post_mission') . $data['mission'];
				$authors = lang('email_content_post_author') . $this->char->get_authors($data['authors'], true);
				$timeline = lang('email_content_post_timeline') . $data['timeline'];
				$location = lang('email_content_post_location') . $data['location'];

				// get an array of authors
				$authorsArr = explode(',', $data['authors']);

				// find out what's the same
				$same = array_values(array_intersect($authorsArr, $this->session->userdata('characters')));

				// figure out who it should come from
				$from = (in_array($this->session->userdata('main_char'), $same)) ? $this->session->userdata('main_char') : $same[0];

				// set who the email is coming from
				$from_name = $this->char->get_character_name($from, true, true);
				$from_email = $this->user->get_email_address('character', $from);

				// set the content
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

				// set the email data
				$email_data = array(
					'email_content' => ($this->email->mailtype == 'html') ? nl2br($content) : $content
				);

				// where should the email be coming from
				$em_loc = Location::email('write_missionpost_saved', $this->email->mailtype);

				// parse the message
				$message = $this->parser->parse_string($em_loc, $email_data, true);

				// get the email addresses
				$emails = $this->char->get_character_emails($data['authors']);

				foreach ($emails as $key => $value)
				{
					$pref = $this->user->get_pref('email_mission_posts_save', $key);

					if ($pref == 'y')
					{
						// don't do anything
					}
					else
					{
						unset($emails[$key]);
					}
				}

				// make a string of email addresses
				$to = implode(',', $emails);

				// set the parameters for sending the email
				$this->email->from($from_email, $from_name);
				$this->email->to($to);
				$this->email->subject($this->options['email_subject'] .' '. $subject);
				$this->email->message($message);
			break;
		}

		// send the email
		$email = $this->email->send();

		return $email;
	}