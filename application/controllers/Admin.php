<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('language');

        $this->uid = 1;
        //$_language = $this->session->userdata("language");
        $option = array(
			'table' => LANGUAGE,
            'where' => array('is_default' => 1),
            'single' => true
        );
        $default_language = $this->Common_model->customGet($option);
        if (!empty($default_language)) {
            if ($default_language->language_code) {
                $this->lang->load($default_language->language_code, strtolower($default_language->language_name));
            } else {
                $this->lang->load('en', 'english');
            }
        } else {
            $this->lang->load('en', 'english');
        }
        $this->Session_model->checkAdminSession();
        
    }

    public function index() {
        if ($this->session->userdata('user_id') == TRUE) {
            redirect('admin/dashboard');
        } else {
            redirect('login');
        }
    }

    function isLogin() {
        if (!$this->session->userdata('id')) {
            redirect('login');
        }
    }

	/**
     * Function Name: logout
     * Description:   To admin logout
     */
    public function logout() {
        $this->session->sess_destroy();
        delete_cookie("login");
        redirect("login");
    }

   
    /**
     * Function Name: dashboard
     * Description:   To admin dashboard
     */
    public function dashboard() {
        //$this->load->library('datatables');
        $data['parent'] = "dashboard";
        $this->template->load('default', 'auth/dashboard', $data);
    }

    public function booking_ajax() {
        $search = $this->input->post('searchstr');
        $limitOffset = $this->input->post('limitOffset');
        $booking_date = date('Y-m-d');
        $booking_type = $this->input->post('booking_type');
        if (!empty($booking_type) && $booking_type == 'current') {
            $where = array('booking.booking_date' => $booking_date);
        } else {
            $where = array();
        }

        if ($this->session->userdata('role') == 'agent') {
            $where['booking.agent_id'] = $this->session->userdata('id');
        }

        echo $this->Common_model->booking_ajax($search, null, null, $where);
    }

    public function exportCsvBooking($type = '') {

        $options = array(
            'table' => 'mw_booking',
            'order' => array('id' => 'DESC'),
        );
        if ($type == 'today') {
            $options['where'] = array('booking_date' => date('Y-m-d'));
        }
        if ($this->session->userdata('role') != 'admin') {
            $options['where']['agent_id'] = $this->session->userdata('id');
        }
        $bookings = $this->Common_model->customGet($options);
        $response = array();
        if (!empty($bookings)) {
            foreach ($bookings as $rows) {
                $temp['name'] = $rows->name;
                $temp['status'] = getStatusStr($rows->status);
                $temp['section'] = getFloorDetail($rows->floor_id);
                $temp['no_of_persons'] = $rows->no_of_persons;
                $temp['booking_date'] = dateFormateManage($rows->booking_date);
                $temp['time_from'] = timeFormateManage($rows->time_from);
                $temp['time_to'] = timeFormateManage($rows->time_to);
                $temp['email'] = $rows->email;
                $temp['mobile'] = $rows->mobile;
                $temp['referrer'] = $rows->referrer;
                $temp['comment'] = $rows->comment;

                $response[] = $temp;
            }
        }
        $array = array();


        $title = array(
            'Name',
            'STATUS',
            'Section',
            'No of persons',
            'Booking date',
            'Time From',
            'Time To',
            'Email',
            'Mobile',
            'Referrer',
            'Comment'
        );
        $array[] = $title;
        foreach ($response as $client) {
            $array[] = $client;
        }
        array_to_csv($array, 'All-Reservation-' . date('Y-m-d h:i A') . '.csv');
    }

    public function exportExcelBooking1($type = '') {

        $options = array(
            'table' => 'mw_booking',
            'order' => array('id' => 'DESC'),
        );
        if ($type == 'today') {
            $options['where'] = array('booking_date' => date('Y-m-d'));
        }
        $bookings = $this->Common_model->customGet($options);
        $response = array();
        if (!empty($bookings)) {
            foreach ($bookings as $rows) {
                $temp['name'] = $rows->name;
                $temp['email'] = $rows->email;
                $temp['mobile'] = $rows->mobile;
                $temp['section'] = getFloorDetail($rows->floor_id);
                $temp['no_of_persons'] = $rows->no_of_persons;
                $temp['booking_date'] = dateFormateManage($rows->booking_date);
                $temp['time_from'] = timeFormateManage($rows->time_from);
                $temp['time_to'] = timeFormateManage($rows->time_to);
                $temp['referrer'] = $rows->referrer;
                $temp['comment'] = $rows->comment;
                $temp['status'] = getStatusStr($rows->status);
                $response[] = $temp;
            }
        }
        $array = array();
        $title = array(
            'Name',
            'Email',
            'Mobile',
            'Section',
            'No of persons',
            'Booking date',
            'Time From',
            'Time To',
            'Referrer',
            'Comment',
            'STATUS'
        );
        $array[] = $title;
        foreach ($response as $client) {
            $array[] = $client;
        }
        array_to_excel($array, 'All-Reservation-' . date('Y-m-d h:i A') . '.xls');
    }

    public function exportExcelBooking($type = '') {
        $this->load->library('PHPExcel');
        $options = array(
            'table' => 'mw_booking',
            'order' => array('id' => 'DESC'),
        );
        if ($type == 'today') {
            $options['where'] = array('booking_date' => date('Y-m-d'));
        }
        if ($this->session->userdata('role') != 'admin') {
            $options['where']['agent_id'] = $this->session->userdata('id');
        }
        $bookings = $this->Common_model->customGet($options);
        $objPHPExcel = new PHPExcel();
// Set document properties
        $objPHPExcel->getProperties()->setCreator("Santanna")
                ->setLastModifiedBy("Maarten Balliauw")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");
// Add some data

        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);

        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("A1", 'Name')
                ->setCellValue("B1", 'STATUS')
                ->setCellValue("C1", 'Section')
                ->setCellValue("D1", 'No of persons')
                ->setCellValue("E1", 'Booking date')
                ->setCellValue("F1", 'Time From')
                ->setCellValue("G1", 'Time To')
                ->setCellValue("H1", 'Email')
                ->setCellValue("I1", 'Mobile')
                ->setCellValue("J1", 'Referrer')
                ->setCellValue("K1", 'Comment');

        $x = 2;
        foreach ($bookings as $sub) {
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("A$x", $sub->name)
                    ->setCellValue("B$x", getStatusStr($sub->status))
                    ->setCellValue("C$x", getFloorDetail($sub->floor_id))
                    ->setCellValue("D$x", $sub->no_of_persons)
                    ->setCellValue("E$x", dateFormateManage($sub->booking_date))
                    ->setCellValue("F$x", timeFormateManage($sub->time_from))
                    ->setCellValue("G$x", timeFormateManage($sub->time_to))
                    ->setCellValue("H$x", $sub->email)
                    ->setCellValue("I$x", $sub->mobile)
                    ->setCellValue("J$x", $sub->referrer)
                    ->setCellValue("K$x", $sub->comment);
            $x++;
        }


// Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Reservation');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Santanna-Reservation.xls"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    /**
     * Function Name: changepassword
     * Description:   To change admin password view
     */
    public function changepassword() {
        $data['parent'] = "password";
        $data['error'] = "";
        $data['message'] = "";
        $this->template->load('default', 'auth/changepassword', $data);
    }

    
    /**
     * Function Name: dochangepassword
     * Description:   To change admin password
     */
    public function change_password() {
        $data['parent'] = "Password";
        $data['title'] = "Password";
        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[6]|max_length[12]');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required|matches[new]');

        if ($this->form_validation->run() == false) {
            $this->template->load('default', 'auth/changepassword', $data);
        } else {
            if ($this->session->userdata('id') != '' && $this->session->userdata('role') == 'admin') {
                $results = $this->Common_model->getsingle(ADMIN, array('password' => md5($this->input->post('old'))));
            } else if($this->session->userdata('id') != '' && $this->session->userdata('role') == 'agent'){
                $results = $this->Common_model->getsingle(AGENTS, array('password' => md5($this->input->post('old'))));
            }else if($this->session->userdata('id') != '' && $this->session->userdata('role') == 'store'){
                $results = $this->Common_model->getsingle(STORE, array('password' => md5($this->input->post('old'))));
            }
            if (empty($results)) {
                $this->session->set_flashdata('error', lang('password_change_invalid_old'));
                redirect('admin/changepassword');
            }
            $pswdArr = array('password' => md5($this->input->post('new')));
            $where = array('id' => $this->session->userdata("id"));
            if ($this->session->userdata('id') != '' && $this->session->userdata('role') == 'admin') {
                if ($this->Common_model->updateFields(ADMIN, $pswdArr, $where)) {
                    $this->session->set_flashdata('success', lang('password_change_successful'));
                    redirect('admin/changepassword');
                } else {
                    $this->session->set_flashdata('error', lang('password_change_unsuccessful'));
                    redirect('admin/changepassword');
                }
            } else if($this->session->userdata('id') != '' && $this->session->userdata('role') == 'agent') {
                if ($this->Common_model->updateFields(AGENTS, $pswdArr, array('id' => $this->session->userdata('id')))) {
                    $this->session->set_flashdata('success', lang('password_change_successful'));
                    redirect('admin/changepassword');
                } else {
                    $this->session->set_flashdata('error', lang('password_change_unsuccessful'));
                    redirect('admin/changepassword');
                }
            }else if($this->session->userdata('id') != '' && $this->session->userdata('role') == 'store'){
                if ($this->Common_model->updateFields(AGENTS, $pswdArr, array('id' => $this->session->userdata('id')))) {
                    $this->session->set_flashdata('success', lang('password_change_successful'));
                    redirect('admin/changepassword');
                } else {
                    $this->session->set_flashdata('error', lang('password_change_unsuccessful'));
                    redirect('admin/changepassword');
                }
            }
        }
    }

    /**
     * Function Name: users
     * Description:   To Get All Users
     */
    public function users() {
        $data['parent'] = "users";
        $data['users'] = $this->Common_model->getAll(USERS);
        $this->template->load('default', 'user/users', $data);
    }

    /**
     * Function Name: export_users
     * Description:   To Export All Users
     */
    public function export_users() {
        $users = $this->Common_model->getAll(USERS, 'name', 'ASC');
        if ($users['total_count'] > 0) {
            $print_array = array();
            foreach ($users['result'] as $value) {
                $print_array[] = array('name' => $value->name, 'email' => $value->email, 'gender' => $value->gender, 'registration_date' => convertDateTime($value->created_date));
            }

            $filename = "users-" . date('d-F-Y-h-i-A') . ".csv";
            $fp = fopen('php://output', 'w');
            header('Content-type: application/csv');
            header('Content-Disposition: attachment; filename=' . $filename);
            fputcsv($fp, array('User Name', 'Email', 'Gender', 'Registration Date'));
            foreach ($print_array as $row) {
                fputcsv($fp, $row);
            }
        }
    }

    /**
     * Function Name: block_unblock
     * Description:   To Block/Unlock Users
     */
    public function block_unblock() {
        $user_id = decoding($this->input->get('id'));
        $flag = $this->input->get('type');
        if ($user_id) {
            $status = $this->Common_model->updateFields(USERS, array('is_blocked' => $flag), array('id' => $user_id));
            if ($status) {
                $success_msg = ($flag == 1) ? 'User blocked successfully' : 'User unblocked successfully';
                $this->session->set_flashdata('success', $success_msg);
            } else {
                $this->session->set_flashdata('error', NO_CHANGES);
            }
        } else {
            $this->session->set_flashdata('error', GENERAL_ERROR);
        }
        redirect('admin/users');
    }

    

    public function databse_backup() {
        ini_set('memory_limit', '1020M');
        $this->load->dbutil();
        $prefs = array(
            'format' => 'sql',
            'filename' => 'feedback_clone.sql'
        );
        $backup = $this->dbutil->backup($prefs);
        $db_name = 'SANTANNA-DB-BACKUP-' . date("Y-m-d") . '-' . time() . '.sql';
        $this->load->helper('download');
        $this->load->helper('file');
        write_file('database-cron/' . $db_name, $backup);
        exit();
    } 

}


