<?php
defined('BASEPATH') or exit('No direct script access allowed');
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

class Onepage extends CI_Controller
{

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->db->select("*");
		$this->db->from('wishes');
		$this->db->order_by('wishTimestamp DESC');
		$qr = $this->db->get();
		if ($qr->num_rows() > 0)
			$data['datas'] = $qr->result();
		else
			$data['datas'] = false;

		$this->load->view('onepage', $data);
	}

	public function wishes()
	{
		$this->form_validation->set_rules('name', 'Name', 'trim|xss_clean|required');
		$this->form_validation->set_rules('message', 'Message', 'trim|xss_clean|required');

		if ($this->form_validation->run()) {
			if (IS_AJAX) {
				$name = $this->input->post('name');
				$message = $this->input->post('message');
				$wishTimestamp = date('Y-m-d H:i:s');

				$param = array(
					'wishName' => $name,
					'wishDesc' => $message,
					'wishTimestamp' => $wishTimestamp
				);

				$proses = $this->db->insert('wishes', $param);

				if ($proses)
					echo json_encode(['message' => 'Success', 'status' => 'success']);
				else {
					$error = $this->db->error();
					echo json_encode(['message' => 'Failed, ' . $error['code'] . ': ' . $error['message'], 'status' => 'error']);
				}
			}
		} else {
			echo json_encode(['message' => 'Ooops!! Something Wrong!! ' . validation_errors(), 'status' => 'error']);
		}
	}

	public function loadwishes()
	{
		if (IS_AJAX) {
			$this->db->select("*");
			$this->db->from('wishes');
			$this->db->order_by('wishTimestamp DESC');
			$qr = $this->db->get();
			if ($qr->num_rows() > 0)
				$datas = $qr->result();
			else
				$datas = false;

			$message = '';
			if ($datas != false) {
				foreach ($datas as $row) {
					$message .= '<div class="oc-item">
									<div class="testimonial">
										<div class="testi-content">
											<p>' . $row->wishDesc . '</p>
											<div class="testi-meta">
											' . $row->wishName . '
											</div>
										</div>
									</div>
								</div>';
				}
			}
			echo $message;
		}
	}
}
