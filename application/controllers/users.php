<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once (APPPATH . 'libraries/REST_Controller.php');

class Users extends REST_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $this->load->model('Users_model', 'model');
        $this->load->model('Ion_auth_model', 'auth');
    }

    public function index_get()
    {
        /*$this->load->view('default/header');
        $this->load->view('bills/index');
        $this->load->view('default/footer');*/
    }

    public function read_get()
    {
        $id = $this->get('id');
        $filters = $this->get('filter');
        //for kendo datasource parameters
        $limit = $this->get('pageSize');
        $offset = $this->get('skip');
        $page = $this->get('page');
        $sort = $this->get('sort');
        $total = 0;
        if($limit>0) {
            //get the total data base on filters
            $total = sizeof($this->model->read($id, $filters));
        }
        $data = array(
            'data' => $this->model->read($id, $filters, $limit, $offset, $sort),
            'total' => $total
        );

        if($this->get('format') == 'json') {
            $message = $data;
            $this->response($message, 200);
        } else {
            $message = array('data' => $data);
            $this->response($message, 200);
        }
    }

    public function create_post()
    {
        $data = $this->post();
        $data['salt'] = $this->auth->store_salt ? $this->auth->salt() : FALSE;
        $data['password']   = $this->auth->hash_password($data['password'], $data['salt']);
        $data['active'] = 1;
        $result = $this->model->create($data);
        if(!isset($result['msg'])) {
            $msg = array('data' => $result);
            $code = 200;
        } else {
            $msg = 'Error: ' . $result['msg'];
            $code = 400;
        }
        $this->response($msg, $code);
    }

    public function update_post()
    {
        $id = $this->post('id');
        $where = array('id' => $id);
        $oldData = $this->model->read($id,null);
        $data = $this->post();
        if($oldData[0]['password'] != $data['password']) {
            $data['salt'] = $this->auth->store_salt ? $this->auth->salt() : FALSE;
            $data['password']   = $this->auth->hash_password($data['password'], $data['salt']);
        }

        $result = $this->model->update($data, $where);
        if(!isset($result['msg'])) {
            $msg = array('data' => $result);
            $code = 200;
        } else {
            $msg = 'Error: ' . $result['msg'];
            $code = 400;
        }
        $this->response($msg, $code);
    }

    public function delete_post()
    {
        $data = $this->post();
        $where = "id = {$data['id']}";
        $result = $this->model->delete($where);
        $this->response($result, 200);
    }
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */