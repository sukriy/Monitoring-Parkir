<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Home extends CI_Controller {
	public function index(){
		if(isset($_POST['submit'])){
			$this->form_validation->set_rules('lokasi', 'Lokasi', 'trim|callback_check_lantai');
			if ($this->form_validation->run() == FALSE){
				$monitoring=$this->db1->all_monitoring();
				$byk = array(
					'1' => array(),
					'2' => array()
				);
				foreach($monitoring as $key=>$value){
					if($value->radar=='1'){
						$byk[$value->radar] = check3($value->value,8,6);
					}else{
						$byk[$value->radar] = check4($value->value,15,3);
					}
				}
				$data['byk'] = $byk;
				$data['lantai']=$this->db1->all_lantai();
				$this->load->view('page/tiket',$data);	
			}else{
				if($_SESSION['form_key']!=$_POST['uniqid']){
					$_SESSION['form_key']=$_POST['uniqid'];
				
					if($_POST['submit']=='baru'){
						$transaksi_baru = $this->db1->transaksi_baru();
						if($transaksi_baru==FALSE){
							$this->session->set_flashdata('tiket', '<div class="alert alert-danger text-center">Parkiran Penuh</div>');
						}else{
							$this->session->set_flashdata('transaksi_print', $transaksi_baru[0]->id_transaksi);
						}
					}
				}
				redirect('Home');
			}
		}else{
			$this->session->set_userdata('form_key', '');
			$monitoring=$this->db1->all_monitoring();
			$byk = array(
				'1' => array(),
				'2' => array()
			);
			foreach($monitoring as $key=>$value){
				if($value->radar=='1'){
					$byk[$value->radar] = check3($value->value,6,6);
				}else{
					$byk[$value->radar] = check4($value->value,15,4);
				}
			}
			$data['byk'] = $byk;
			$data['lantai']=$this->db1->all_lantai();
			$this->load->view('page/tiket',$data);	
		}
	}
	public function check_lantai($str){
		if($str == ''){
			$nilai = TRUE;
		}else{
			$nilai = FALSE;
			$lantai=$this->db1->all_lantai();
			foreach($lantai as $key=>$value){
				if($value->nama==$str && $nilai==FALSE)
					$nilai = TRUE;
			}
			if($nilai==TRUE){
				$monitoring=$this->db1->monitoring($str);
				if($str=='1'){
					$digit = check3($monitoring[0]->value,8,6);
				}else{
					$digit = check4($monitoring[0]->value,15,3);
				}
				if(empty(array_count_values($digit)[0])){
					$this->form_validation->set_message('check_lantai', 'LT '.$str.' Parkiran Penuh');
					$nilai=FALSE;
				}
			}else{
				$this->form_validation->set_message('check_lantai', 'Data tidak sesuai');
			}
		}
		return $nilai;
	}
	public function transaksi_print(){
		$data['load']=$this->db1->load_tiket($_GET['print']);
		if($data['load']==FALSE){
			echo 'NOT FOUND';
		}else{
			$this->load->view('page/transaksi_print',$data);
		}
	}		
	public function ajax_transaksi_id(){
		$data['check']=$this->db1->check_transaksi_id();
		echo json_encode(array("nilai" => $data['check'], "csrf" => array("name" => $this->security->get_csrf_token_name(), "hash" => $this->security->get_csrf_hash())));		
		exit();
	}
}
?>