<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inv_category_model extends CI_Model
{
	private $table;
		
	public function __construct()
	{
		parent::__construct();		
		$this->load->database();
		$this->table = 'inv_category';
	}
	
	public function read($id, $filter, $limit=NULL, $offset=NULL, $sort=NULL)
	{
		if(is_array($filter['filters'])) {
			foreach($filter['filters'] as $f) {
				switch($f['operator']) {
					case 'eq':
						$this->db->where("{$f['field']} =", $f['value']);
						break;
					case 'neq':
						$this->db->where("{$f['field']} !=", $f['value']);
						break;
					case 'lt':
						$this->db->where("{$f['field']} <", $f['value']);
						break;
					case 'lte':
						$this->db->where("{$f['field']} <=", $f['value']);
						break;
					case 'gt':
						$this->db->where("{$f['field']} >", $f['value']);
						break;
					case 'gte':
						$this->db->where("{$f['field']} >=", $f['value']);
						break;
					case 'startswith':
						$this->db->like($f['field'], $f['value'], 'after');
						break;
					case 'endswith':
						$this->db->like($f['field'], $f['value'], 'before');
						break;
					case 'contains':
						$this->db->like($f['field'], $f['value'], 'both');
						break;
					default:
						break;
				}
			}
		}
	
		if($id) {
			$this->db->where("{$this->table}.invCategoryId =", $id);
		}	
		
		if( $limit > 0) {
			$this->db->limit($limit, $offset);
		} 
			
		
		if(!empty($sort)) {
			foreach($sort as $s) {
				$this->db->order_by($s['field'], $s['dir']);
			}
		} else {
			$this->db->order_by('invCategoryId', 'asc');
		}
		 
		$query = $this->db->get($this->table);
		//echo $this->db->last_query();exit;
		$results = "";
		if($query->num_rows() > 0) {
			$results = $query->result_array();
		}		
		$query->free_result();
		
		return $results;
	}
	
	public function create($data)
	{		
		$data = $this->filter_input($data);	
		$query = $this->db->insert_string($this->table, $data);
		$result = $this->db->query($query);		
		
		if($this->db->_error_message()) {
			$data = array('msg' => $this->db->_error_message());		
		} else {
			$id = $this->db->insert_id();
			$data = $this->read($id, null);
		} 	
		return $data;		
	}
	
	public function update($data, $where)
    {
    	$data = $this->filter_input($data);		    	
    	$query = $this->db->update_string($this->table, $data, $where);
    	$result = $this->db->query($query);		
		if($this->db->_error_message()) {
			$data = array('msg' => $this->db->_error_message());
		} 	
		return $data;   	
    }
	
	public function delete($where)
	{
		$this->db->where($where);		
		$query = $this->db->delete($this->table);
		if($this->db->_error_message()) {
			$data = array('msg' => $this->db->_error_message());
		} else {	
			$data =  $this->db->affected_rows();
		} 	
		return $data; 	
	}		

	public function select($sql)
	{
		$query = $this->db->query($sql);
	
		$error = $this->db->_error_message();
		if(!empty($error))
		{
			echo "ERROR: " . $error . "<br/>";
			echo "SQL: " . $this->db->last_query() . "<br/>";
			exit;
		} else {
			$results = array();
			if($query->num_rows() > 0) {
				$results = $query->result_array();
			}
			$query->free_result();
		}
	
		return $results;
	}
	
	public function filter_input($data)
	{		
		foreach($data as $field => $value) {		
			if(!$this->db->field_exists($field, $this->table)) {
				unset($data[$field]);
			}				
		}
		return $data;
	} 	
}