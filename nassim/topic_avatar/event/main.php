<?php
/**
*
* @package phpBB Extension - tas2580 SEO URLs
* @copyright (c) 2014 tas2580 (https://tas2580.net)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace nassim\topic_avatar\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class main implements EventSubscriberInterface
{
	protected $db;
	/** @var \phpbb\config\config */
	protected $config;
	
	/** @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\request\request */
	private $request;
	
	/* @var \phpbb\user */
	private $user;
	
	/**
	* Constructor
	*
	* @param \phpbb\template\template $template
	* @param \phpbb\template\template $request
	* @access public
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\template\template $template,  \phpbb\request\request $request, \phpbb\user $user)
	{
		$this->db = $db;
		$this->config = $config;
		$this->template = $template;
		$this->request = $request;
		$this->user = $user;
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	* @static
	* @access public
	*/
	static public function getSubscribedEvents()
	{
		return array(

			'core.viewforum_get_topic_data'				=> 'viewforum_topic_avatar',
			'core.viewforum_modify_topicrow'				=> 'viewforum_modify_topicrow',

		);
	}
	
	/**
	* Rewrite links to topics in forum view
	*
	* @param	object	$event	The event object
	* @return	null
	* @access	public
	*/
	public function viewforum_topic_avatar($event)
	{
		$sql_array = $event['sql_array'];
		$sql_array['LEFT_JOIN'][] = array('FROM' => array(USERS_TABLE => 'u'), 'ON' => 'u.user_id = t.topic_poster');
		$sql_array['SELECT'] .= ', u.user_avatar, u.user_avatar_type, u.username';
	 $event['sql_array'] = $sql_array;
		}



public function viewforum_modify_topicrow($event)
	{
		$topic_row = $event['topic_row'];
		$row  = $event['row'];
		$avatar  = $event['row']['user_avatar'];

		if ($row['user_avatar_type'] == "avatar.driver.local") {
					$topic_row['AVATAR'] = '<img src="http://' . $this->config['server_name'] . '' . $this->config['script_path'] . '/images/avatars/gallery/' . $avatar . '" width="35px" height="35px" alt="' . $row['username'] . '" />';

		}
		else if ($row['user_avatar_type'] == "avatar.driver.gravatar") {
		$email = $avatar;
		$size = 35;
		$grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?s=" . $size;
					$topic_row['AVATAR'] = '<img src="' . $grav_url . '"  alt="' . $row['username'] . '" />';
		}
		else if ($row['user_avatar_type'] == "avatar.driver.remote") {
					$topic_row['AVATAR'] = '<img src="' . $avatar . '" width="35px" height="35px"  alt="' . $row['username'] . '" />';
		}
		else if ($row['user_avatar_type'] == "avatar.driver.upload") {
					$topic_row['AVATAR'] = '<img src="http://' . $this->config['server_name'] . '' . $this->config['script_path'] . '/download/file.php?avatar=' . $avatar . '" width="35px" height="35px"  alt="' . $row['username'] . '" />';
		}
		else {
					$topic_row['AVATAR'] = '<img src="http://' . $this->config['server_name'] . '' . $this->config['script_path'] . '/styles/prosilver/theme/images/no_avatar.gif" width="35px" height="35px"  alt="' . $row['username'] . '" />';
		}
		

		$event['topic_row'] = $topic_row;		
		}

}
