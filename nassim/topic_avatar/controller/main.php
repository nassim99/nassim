<?php
/**
*
* @package phpBB Extension - topic avatar by nassim
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace nassim\topic_avatar\controller;

class main
{
	protected $db;
		/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\user */
	protected $user;

	/**
	* Constructor
	*
	* @param \phpbb\config\config		$config
	* @param \phpbb\controller\helper	$helper
	* @param \phpbb\template\template	$template
	* @param \phpbb\user				$user
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\user $user)
	{
		$this->db = $db;
		$this->config = $config;
		$this->helper = $helper;
		$this->template = $template;
		$this->user = $user;
	}

	/**
	* Demo controller for route /avater/{name}
	*
	* @param string		$name
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle($name)
	{

		if (is_numeric ($name)) 
	 {
		
		$sql_array = array(
			'SELECT'	=> "u.user_avatar, u.user_id, u.username, u.user_avatar_type",

			'FROM'		=> array(
				USERS_TABLE	=> 'u',
			),
			'WHERE'		=> "u.user_id = $name",
		);
		
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, 1);

		
	while ($row = $this->db->sql_fetchrow($result))
		{
		if ($row['user_avatar_type'] == "avatar.driver.local") {
		$this->template->assign_vars(array(
			'AVATAR'					=> '<img src="./images/avatars/gallery/' . $row['user_avatar'] . '" width="35px" height="35px" alt="' . $row['username'] . '" />',
			));
		}
		else if ($row['user_avatar_type'] == "avatar.driver.gravatar") {
		$email = $row['user_avatar'];
		$size = 35;
		$grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?s=" . $size;
		$this->template->assign_vars(array(
			'AVATAR'					=> '<img src="' . $grav_url . '"  alt="' . $row['username'] . '" />',
			));
		}
		else if ($row['user_avatar_type'] == "avatar.driver.remote") {
		$this->template->assign_vars(array(
			'AVATAR'					=> '<img src="' . $row['user_avatar'] . '" width="35px" height="35px"  alt="' . $row['username'] . '" />',
			));
		}
		else if ($row['user_avatar_type'] == "avatar.driver.upload") {
		$this->template->assign_vars(array(
			'AVATAR'					=> '<img src="./download/file.php?avatar=' . $row['user_avatar'] . '" width="35px" height="35px"  alt="' . $row['username'] . '" />',
			));
		}
		else {
		$this->template->assign_vars(array(
			'AVATAR'					=> '<img src="./styles/prosilver/theme/images/no_avatar.gif" with="35px" height="35px"  alt="' . $row['username'] . '" />',
			));		
		}



		return $this->helper->render('avatar_body.html', $name);
	
		}

 } else {
 
 echo '<div style="text-align:center;"><b>WTH are you doing !!! .. STOP that</b></div>';
 }
	

	}
	
	
}
