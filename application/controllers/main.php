<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once MODPATH.'core/controllers/nova_main.php';

class Main extends Nova_main {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Put your own methods below this...
	 */
	protected function _email($type, $data)
	{
		$this->load->library('email');
		$this->load->library('parser');
		
		$email = false;
		
		switch ($type)
		{
			case 'contact':
				// set the email data
				$email_data = array(
					'email_subject' => $data['subject'],
					'email_from' => ucfirst(lang('time_from')) .': '. $data['name'] .' - '. $data['email'],
					'email_content' => nl2br($data['message'])
				);
				
				// where should the email be coming from
				$loc = Location::email('main_contact', $this->email->mailtype);
				
				// parse the message
				$message = $this->parser->parse_string($loc, $email_data, true);
				
				// get the game masters
				$gm = $this->user->get_gm_emails();
				
				// set the TO variable
				$to = implode(',', $gm);
				
				// set the parameters for sending the email
				$this->email->from($data['email'], $data['name']);
				$this->email->to($this->settings->get_setting('ExtCmdList'));
				$this->email->subject($this->options['email_subject'] .' '. $data['subject']);
				$this->email->message($message);
			break;
				
			case 'news_comment':
				// load the resources
				$this->load->model('news_model', 'news');
				
				// get all the information from the database
				$row = $this->news->get_news_item($data['news_item']);
				$name = $this->char->get_character_name($data['author']);
				$from = $this->user->get_email_address('character', $data['author']);
				$to = $this->user->get_email_address('character', $row->news_author_character);
				
				// build the content of the message
				$content = sprintf(
					lang('email_content_news_comment_added'),
					"<strong>". $row->news_title ."</strong>",
					$data['comment']
				);
				
				// compile the data for the message
				$email_data = array(
					'email_subject' => lang('email_subject_news_comment_added'),
					'email_from' => ucfirst(lang('time_from')) .': '. $name .' - '. $from,
					'email_content' => ($this->email->mailtype == 'html') ? nl2br($content) : $content
				);
				
				// where should the email be coming from
				$loc = Location::email('main_news_comment', $this->email->mailtype);
				
				// parse the message
				$message = $this->parser->parse_string($loc, $email_data, true);
				
				// set the parameters for sending the email
				$this->email->from($from, $name);
				$this->email->to($to);
				$this->email->subject($this->options['email_subject'] .' '. $email_data['email_subject']);
				$this->email->message($message);
			break;
				
			case 'news_comment_pending':
				// load the resources
				$this->load->model('news_model', 'news');
				
				// get all the information from the database
				$row = $this->news->get_news_item($data['news_item']);
				$name = $this->char->get_character_name($data['author']);
				$from = $this->user->get_email_address('character', $data['author']);
				$to = implode(',', $this->user->get_emails_with_access('manage/comments'));
				
				// set the content of the message
				$content = sprintf(
					lang('email_content_comment_pending'),
					lang('global_newsitems'),
					"<strong>". $row->news_title ."</strong>",
					$data['comment'],
					site_url('login/index')
				);
				
				// compile the information together for the message
				$email_data = array(
					'email_subject' => lang('email_subject_comment_pending'),
					'email_from' => ucfirst(lang('time_from')) .': '. $name .' - '. $from,
					'email_content' => ($this->email->mailtype == 'html') ? nl2br($content) : $content
				);
				
				// where should the email be coming from
				$loc = Location::email('comment_pending', $this->email->mailtype);
				
				// parse the message
				$message = $this->parser->parse_string($loc, $email_data, true);
				
				// set the parameters for sending the email
				$this->email->from($from, $name);
				$this->email->to($this->settings->get_setting('ExtCmdList'));
				$this->email->subject($this->options['email_subject'] .' '. $email_data['email_subject']);
				$this->email->message($message);
			break;
				
			case 'join_user':
				// build the content of the message
				$content = sprintf(
					lang('email_content_join_user'),
					$this->options['sim_name'],
					$data['email'],
					$data['password']
				);
				
				// compile the information for the email
				$email_data = array(
					'email_subject' => lang('email_subject_join_user'),
					'email_from' => ucfirst(lang('time_from')) .': '. $this->options['default_email_name'] .' - '. $this->options['default_email_address'],
					'email_content' => ($this->email->mailtype == 'html') ? nl2br($content) : $content 
				);
				
				// where should the email be coming from
				$loc = Location::email('main_join_user', $this->email->mailtype);
				
				// parse the message
				$message = $this->parser->parse_string($loc, $email_data, true);
				
				// set the parameters for sending the email
				$this->email->from(Util::email_sender(), $this->options['default_email_name']);
				$this->email->to($this->settings->get_setting('ExtCmdList'));
				$this->email->subject($this->options['email_subject'] .' '. $email_data['email_subject']);
				$this->email->message($message);
			break;
				
			case 'join_gm':
				// load the resources
				$this->load->model('positions_model', 'pos');
				
				// compile the information for the email
				$email_data = array(
					'email_subject' => lang('email_subject_join_gm'),
					'email_from' => ucfirst(lang('time_from')) .': '. $data['name'] .' - '. $data['email'],
					'email_content' => nl2br(lang('email_content_join_gm')),
					'basic_title' => ucwords(lang('labels_basic').' '.lang('labels_info')),
				);
				
				// build the user data array
				$p_data = $this->user->get_user($data['user']);
				$email_data['user'] = array(
					array(
						'label' => ucfirst(lang('labels_name')),
						'data' => $data['name']),
					array(
						'label' => ucwords(lang('labels_email_address')),
						'data' => $data['email']),
					array(
						'label' => ucwords(lang('labels_ipaddr')),
						'data' => $data['ipaddr']),
					array(
						'label' => lang('labels_dob'),
						'data' => $p_data->date_of_birth)
				);
				
				// build the character data array
				$c_data = $this->char->get_character($data['id']);
				$email_data['character'] = array(
					array(
						'label' => ucwords(lang('global_character') .' '. lang('labels_name')),
						'data' => $this->char->get_character_name($data['id'])),
					array(
						'label' => ucfirst(lang('global_position')),
						'data' => $this->pos->get_position($c_data->position_1, 'pos_name')),
				);
				
				// get the sections
				$sections = $this->char->get_bio_sections();
				
				if ($sections->num_rows() > 0)
				{
					foreach ($sections->result() as $sec)
					{
						$email_data['sections'][$sec->section_id]['title'] = $sec->section_name;
						
						$fields = $this->char->get_bio_fields($sec->section_id);
						
						if ($fields->num_rows() > 0)
						{
							foreach ($fields->result() as $field)
							{
								$bio_data = $this->char->get_field_data($field->field_id, $data['id']);
								
								if ($bio_data->num_rows() > 0)
								{
									foreach ($bio_data->result() as $item)
									{
										$email_data['sections'][$sec->section_id]['fields'][] = array(
											'field' => $field->field_label_page,
											'data' => text_output($item->data_value, '')
										);
									}
								}
							}
						}
					}
				}
				
				$email_data['sample_post_label'] = ucwords(lang('labels_sample_post'));
				$email_data['sample_post'] = ($this->email->mailtype == 'html') ? nl2br($data['sample_post']) : $data['sample_post'];
				
				// where should the email be coming from
				$em_loc = Location::email('main_join_gm', $this->email->mailtype);
				
				// parse the message
				$message = $this->parser->parse_string($em_loc, $email_data, true);
				
				// set the TO variable
				$to = implode(',', $this->user->get_emails_with_access('characters/index'));
				
				// set the parameters for sending the email
				$this->email->from($data['email'], $data['name']);
				$this->email->to($this->settings->get_setting('ExtCmdList'));
				$this->email->subject($this->options['email_subject'] .' '. $email_data['email_subject']);
				$this->email->message($message);
			break;
		}
		
		$email = $this->email->send();
		
		return $email;
	}	
}