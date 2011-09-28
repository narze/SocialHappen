<?php
class Page_user_data_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * @private Change page user data from id to field name
	 * @param $page_user
	 * @param $page_id
	 * @return $page_user
	 */ 
	function _fetch_page_user_data_by_page_id($page_user = array(), $page_id = NULL){
		if(!isset($page_user['user_data']) || !$pages = $this->db->get_where('page', array('page_id' => $page_id))-> result_array()){
			return FALSE;
		} 
		$page = $pages[0];
		$page_user_fields = json_decode($page['page_user_fields'], TRUE);
		$page_user['user_data'] = json_decode($page_user['user_data'] ,TRUE);
		
		foreach($page_user['user_data'] as $key => $value){
			$page_user['user_data'][$page_user_fields[$key]['name']] = $value;
			unset($page_user['user_data'][$key]);
		}
		return $page_user;
	}
	
	/**
	 * Get page user
	 * @param $user_id
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function get_page_user_by_user_id_and_page_id($user_id = NULL, $page_id = NULL) {
		$page_users = $this -> db -> get_where('page_user_data', array('user_id' => $user_id, 'page_id' => $page_id)) -> result_array();
		if(isset($page_users[0])){
			$page_user = $this->_fetch_page_user_data_by_page_id($page_users[0], $page_id);
			$users = $this->db->get_where('user', array('user_id'=>$user_id))->result_array();
			$user = $this->socialhappen->map_one_v($users[0], 'user_gender');
			return array_merge($page_user, issetor($user,array()));
		}
		return NULL;
	}
	
	/**
	 * Get page users
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function get_page_users_by_page_id($page_id = NULL) {
		$this->db->join('user','user.user_id=page_user_data.user_id');
		$page_users = $this -> db -> get_where('page_user_data', array('page_id' => $page_id)) -> result_array();
		$page_users = $this->socialhappen->map_v($page_users, 'user_gender');
		foreach($page_users as &$page_user){
			$page_user = $this->_fetch_page_user_data_by_page_id($page_user, $page_id);
		}
		unset($page_user);
		return $page_users;
	}
	
	/**
	 * Adds page user
	 * @param array $data
	 * @return $user_id
	 * @author Manassarn M.
	 */
	function add_page_user($data = array()){
		if(!$data || !isset($data['user_id']) || !$data['user_id'] || !isset($data['page_id']) || !$data['page_id']){
			return FALSE;
		}
		if($this->get_page_user_by_user_id_and_page_id($data['user_id'], $data['page_id'])){
			return FALSE;
		}
		if(!$fields = $this->db->get_where('page',array('page_id'=> $data['page_id']))->result_array()){
			return FALSE;
		}
		if(!$users = $this->db->get_where('user',array('user_id'=> $data['user_id']))->result_array()){
			return FALSE;
		}
		
		$processed_data = array();
		$fields = json_decode(issetor($fields[0]['page_user_fields']),TRUE);
		if(is_array($fields)){
			foreach ($fields as $key => $value){
				if(isset($data['user_data'][$value['name']])){
					switch ($value['type']){
						case 'checkbox':
							$processed_data[$key] = array();
							$items = $value['items'];
							if(is_array($data['user_data'][$value['name']])) {
								foreach($data['user_data'][$value['name']] as $data_value){
									if(in_array($data_value, $items)){
										$processed_data[$key][] = $data_value;
									}
								}
							}
						break;
						
						case 'radio':
							if(in_array($data['user_data'][$value['name']], $value['items'])){
								$processed_data[$key] = $data['user_data'][$value['name']];
							}
						break;
						
						case 'text':
						case 'textarea':
						default:
						$processed_data[$key] = $data['user_data'][$value['name']];
					}
				} else if (issetor($value['required']) == TRUE) {
					return FALSE;
				} else {
					$processed_data[$key] = NULL;
				}
			}
		}
		$data['user_data'] = json_encode($processed_data);
		return $this -> db -> insert('page_user_data', $data);
	}
	
	/**
	 * Update page user
	 * @param $user_id
	 * @param $page_id
	 * @param array $user_data
	 * @author Manassarn M.
	 */
	function update_page_user_by_user_id_and_page_id($user_id = NULL, $page_id = NULL, $user_data = array()){
		if(!$user_data || !$page_id || !$user_id || !$page_user = $this->get_page_user_by_user_id_and_page_id($user_id, $page_id)) {
			return FALSE;
		}
		$processed_data = array();
		$fields = $this->db->get_where('page',array('page_id'=> $page_id))->result_array();
		$fields = json_decode(issetor($fields[0]['page_user_fields']),TRUE);
		if(is_array($fields)){
			foreach ($fields as $key => $value){
				if(isset($user_data[$value['name']]) && $user_data[$value['name']]){
					switch ($value['type']){
						case 'checkbox':
							$processed_data[$key] = array();
							$items = $value['items'];
							foreach($user_data[$value['name']] as $data_key => $data_value){
								if(isset($items[$data_value])){
									$processed_data[$key][] = $data_value;
								}
							}
						break;
						
						case 'radio':
							if(in_array($user_data[$value['name']], $value['items'])){
								$processed_data[$key] = $user_data[$value['name']];
							}
						break;
						
						case 'text':
						case 'textarea':
						default:
						$processed_data[$key] = $user_data[$value['name']];
					}
				} else if (isset($user_data[$value['name']]) && issetor($value['required']) == TRUE) {
					return FALSE;
				} else {
					$processed_data[$key] = $page_user['user_data'][$value['name']];
				}
			}
		}
		$user_data = array();
		$user_data['user_data'] = json_encode($processed_data);
		return $this->db->update('page_user_data', $user_data, array('user_id' => $user_id, 'page_id' => $page_id));
	}
	
	/**
	 * Removes page user
	 * @param $user_id
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function remove_page_user_by_user_id_and_page_id($user_id = NULL, $page_id = NULL){
		$this->db->delete('page_user_data', array('user_id' => $user_id, 'page_id' => $page_id));
		return $this->db->affected_rows()==1;
	}

	// /**
	 // * Check if user is existed
	 // * @param $user_id
	 // * @param $page_id
	 // * @return TRUE if user exists
	 // * @author Manassarn M.
	 // */
	// function check_exist($user_id = NULL, $page_id = NULL) {
		// $this -> db -> from('user');
		// $this -> db -> where( array('user_id' => $user_id, 'page_id' => $page_id));
		// $count = $this -> db -> count_all_results();
		// return ($count != 0);
	// }
	
	// /**
	 // * Count users
	 // * @param $page_id
	 // * @author Manassarn M.
	 // */
	// function count_users_by_page_id($page_id = NULL){
		// $this->db->where(array('page_id' => $page_id));
		// $this -> db -> join('user_apps', 'user_apps.user_id=user.user_id');
		// return $this->db->count_all_results('user');
	// }
}
/* End of file page_user_data_model.php */
/* Location: ./application/models/page_user_data_model.php */
