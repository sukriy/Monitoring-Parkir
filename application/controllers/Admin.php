<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Admin extends CI_Controller {
	public function index(){
		if($this->session->userdata('id_account')==''){
			$this->load->view('page/sign_in');
		}else{
			$data = $this->check();
			$this->db1->check_kendaraan();
			$this->load->view('page/home',$data);
		}
	}
	function csrf_redirect(){
		$this->session->set_flashdata('message', '<div class="alert alert-danger text-center">CRSF code error</div>');
		redirect($this->agent->referrer());
	}

	public function sign_in(){
		$sign_in = $this->db1->sign_in();
		if($sign_in==FALSE){
			$this->session->set_flashdata('sign_in', '<div class="alert alert-danger text-center">Username / Password Salah</div>');
			$this->load->view('page/sign_in');
		}else{
			$newdata = array(
				'id_account'  => $sign_in[0]->id_account,
				'username'  => $sign_in[0]->username,
				'level'  => $sign_in[0]->level,
				'nama_lengkap'  => $sign_in[0]->nama_lengkap,
				'gambar' => $sign_in[0]->gambar,
				'form_key' => ''
			);
			$this->session->set_userdata($newdata);
			redirect('Admin');
		}
	}
	public function sign_up(){
		if(isset($_POST['submit'])){
			$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required|max_length[255]|min_length[6]');
			$this->form_validation->set_rules('username', 'Username', 'trim|required|max_length[255]|min_length[6]|callback_check_account_double');
			$this->form_validation->set_rules('pass_confirmation', 'Password', 'trim|required|max_length[255]|min_length[6]');
			$this->form_validation->set_rules('pass', 'Password confirmation', 'trim|matches[pass_confirmation]');
			$this->form_validation->set_rules('notlpn', 'Nomor Telepon', 'trim|max_length[20]|numeric');
			$this->form_validation->set_rules('email', 'Email', 'trim|required');
			$this->form_validation->set_rules('alamat', 'Alamat', 'trim|max_length[1000]');
			
			if ($this->form_validation->run() == FALSE){
				$this->load->view('page/sign_up');
			}else{
				if($_SESSION['form_key']!=$_POST['uniqid']){
					$_SESSION['form_key']=$_POST['uniqid'];
					$sign_up = $this->db1->sign_up();
					$to = $_POST['email'];
					$subject = "Konfirmasi Email";

					$message = "Dear ".$_POST['nama_lengkap'].",<br>";
					$message .= "Please complete last register by klik below link ".base_url('Admin/konfirmasi?key=').hash_password($_POST['email']);
					$message .= "<br>Best Regards<br>Parking";

					$header = "From:parking@parking.com \r\n";
					$header .= "MIME-Version: 1.0\r\n";
					$header .= "Content-type: text/html\r\n";

					$retval = mail ($to,$subject,$message,$header);
					// if($retval)
						// echo 'ok';
					// else 
						// echo 'no';
					// exit();

					$this->session->set_flashdata('sign_in', '<div class="alert alert-success text-center">Harap konfirmasi Email</div>');					
					redirect('Admin');
				}else{
					redirect('Admin');
				}
			}
		}else{
			$this->session->set_userdata('form_key', '');
			$this->load->view('page/sign_up');
		}
	}
	public function konfirmasi(){
		$konfirmasi = $this->db1->konfirmasi();
		if($konfirmasi){
			$this->session->set_flashdata('sign_in', '<div class="alert alert-success text-center">Berhasil Register</div>');
		}else{ 
			$this->session->set_flashdata('sign_in', '<div class="alert alert-warning text-center">key tidak dikenal</div>');
		}
		redirect('Admin');
	}
	public function update_sensor(){
		$update_sensor = $this->db1->update_sensor();
	}
	public function ajax_email_double(){
		$data['check']=$this->db1->ajax_email_double();		
		echo json_encode(array("nilai" => $data['check'], "csrf" => array("name" => $this->security->get_csrf_token_name(), "hash" => $this->security->get_csrf_hash())));		
		exit();			
	}
	public function menu(){
		$data['menulist'] = array(
			'kendaraan' => array (
				'view' => array (
					'controller' => 'kendaraan',
					'link' => "<li>".anchor(base_url('Admin/Kendaraan'),'<i class="menu-icon fa fa-puzzle-piece"></i>Kendaraan')."</li>",
					'parameter' => ''
				),
				'baru' => array (
					'controller' => 'kendaraan_modif',
					'link' => "",
					'parameter' => 'baru'
				),
				'edit' => array (
					'controller' => 'kendaraan_modif',
					'link' => "",
					'parameter' => 'edit'
				),
				'hapus' => array (
					'controller' => 'ajax_kendaraan_hapus',
					'link' => "",
					'parameter' => ''
				),
				'bayar' => array (
					'controller' => 'kendaraan_bayar',
					'link' => "",
					'parameter' => 'bayar'
				),
				'konfirmasi' => array (
					'controller' => 'kendaraan_bayar',
					'link' => "",
					'parameter' => 'konfirmasi'
				)
			),
			'account' => array (
				'view' => array(
					'controller' => 'account',
					'link' => "<li>".anchor(base_url('Admin/Account'),  '<i class="menu-icon fa fa-users"></i>Account')."</li>",
					'parameter' => ''
				),
				'baru' => array(
					'controller' => 'account_modif',
					'link' => "",
					'parameter' => 'baru'
				),
				'edit' => array(
					'controller' => 'account_modif',
					'link' => "",
					'parameter' => 'edit'
				),
				'hapus' => array(
					'controller' => 'ajax_account_hapus',
					'link' => "",
					'parameter' => ''
				)			
			),
			'transaksi' => array (
				'view' => array (
					'controller' => 'transaksi',
					'link' => "<li>".anchor(base_url('Admin/Transaksi'),  '<i class="menu-icon fa fa-truck"></i>Transaksi')."</li>",
					'parameter' => ''
				),
				'baru' => array (
					'controller' => 'transaksi_modif',
					'link' => "",
					'parameter' => 'baru'
				),
				'print' => array (
					'controller' => 'transaksi_print',
					'link' => "",
					'parameter' => ''
				),
				'bayar' => array (
					'controller' => 'ajax_transaksi_bayar',
					'link' => "",
					'parameter' => ''
				),
				'hapus' => array (
					'controller' => 'ajax_transaksi_hapus',
					'link' => "",
					'parameter' => ''
				),
				'keluar' => array (
					'controller' => 'transaksi_keluar',
					'link' => "<li>".anchor(base_url('Admin/Transaksi_Keluar'),  '<i class="menu-icon fa fa-angle-double-left"></i>Keluar')."</li>",
					'parameter' => 'submit'
				),
				'approve' => array (
					'controller' => 'ajax_report_approve',
					'link' => "",
					'parameter' => ''
				)
			),
			'custom' => array (
				'view' => array (
					'controller' => 'custom',
					'link' => "<li>".anchor(base_url('Admin/Custom'),   '<i class="menu-icon fa fa-cogs"></i>Custom')."</li>",
					'parameter' => ''
				),
				'baru' => array(
					'controller' => 'custom_Modif',
					'link' => "",
					'parameter' => 'baru'
				),
				'edit' => array(
					'controller' => 'custom_Modif',
					'link' => "",
					'parameter' => 'edit'
				),
				'hapus' => array(
					'controller' => 'ajax_custom_hapus',
					'link' => "",
					'parameter' => ''
				)
			),
			'report' => array (
				'view' => array(
					'controller' => 'report',
					'link' => "<li>".anchor(base_url('Admin/Report'),  '<i class="menu-icon fa fa-table"></i>Laporan')."</li>",
					'parameter' => 'submit'
				)
			)
		);
		$data['hak_akses'] = array(
			'manager' => array (
				'account' => array('view','baru','edit','hapus'),
				'kendaraan' => array('view','baru','edit','hapus','bayar','konfirmasi'),
				'transaksi' => array('view','baru','print','bayar','keluar'),
				'custom' => array('view','baru','edit','hapus'),				
				'report' => array('view')
			),
			'staff' => array (
				'account' => array('view','baru','edit','hapus'),
				'kendaraan' => array('view','baru','edit','hapus','konfirmasi'),
				'transaksi' => array('view','baru','print','hapus','bayar','keluar'),
				'report' => array('view')
			),
			'pelanggan' => array (
				'kendaraan' => array('view', 'baru', 'edit', 'hapus', 'bayar'),
				'report' 	=> array('view')
			) 
		);		
		return $data;
	}	
	public function check(){
		if($this->session->userdata('id_account')==''){
			redirect('Admin');
		}else{
			$data = $this->menu();
			// if($_SESSION['level']!='admin'){
				$ada = 9;
				foreach($data['menulist'] as $key=>$value){
					foreach($value as $key2=>$value2){
						if(strtolower($value2['controller'])==strtolower($this->router->fetch_method()) && $ada!=1){
							$ada = 0;
							if(isset($_POST['submit']) && $_POST['submit']!=$value2['parameter']){
								continue; 
							}
							if(isset($data['hak_akses'][$_SESSION['level']][$key])){
								$nilai = implode(",",$data['hak_akses'][$_SESSION['level']][$key]);
								$nilai = explode(",",$nilai);
								if (in_array($key2, $nilai)){
									$ada = 1;
								}
							}
						}
					}
				}
				if($ada == 0){
					$this->session->set_flashdata('hak_akses', '<div class="alert alert-danger text-center">Anda Tidak punya akses</div>');
					redirect('Admin');
				} 
			// }
			return $data;
		}
	}
	public function monitoring(){
		$monitoring=$this->db1->monitoring($_GET['level']);
		$gmbr = base_url('images/sensor/LT_').$_GET['level'].'_';

		if($_GET['level']=='1'){
			$digit = check3($monitoring[0]->value,6,6);
		}else{
			$digit = check4($monitoring[0]->value,15,4);
		}
		$gmbr .= implode("_",$digit).'.jpg';
		echo '<img src="'.$gmbr.'" class="mx-auto d-block">';
	} 
	public function coba(){
		$this->load->view('page/coba');
	}
	public function ajax_coba(){
		$monitoring=$this->db1->all_monitoring();
		$byk = array(
			'1' => array(),
			'2' => array()
		);
		foreach($monitoring as $key=>$value){
			if($value->radar=='1'){
				$byk[$value->radar] = '<img src="'.base_url('images/sensor/LT_').$value->radar.'_'.implode("_",check3($value->value,6,6)).'.jpg" class="mx-auto d-block">';
			}else{
				$byk[$value->radar] = '<img src="'.base_url('images/sensor/LT_').$value->radar.'_'.implode("_",check4($value->value,15,4)).'.jpg" class="mx-auto d-block">';
			}
		}
		for($i=count($byk); $i>0; $i--){
			echo $byk[$i];
		}
	} 

	
	//Account
	public function account(){
		$data = $this->check();
		$data['load']=$this->db1->account();
		$this->load->view('page/account',$data);
	}
	public function account_Modif(){
		$data = $this->check();
		$id_account = (isset($_POST['id_account']) ? $_POST['id_account'] : (isset($_GET['id_account']) ? $_GET['id_account'] : ''));
		if(isset($_POST['submit'])){
			$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required|max_length[255]|min_length[6]');
			$this->form_validation->set_rules('username', 'Username', 'trim|required|max_length[255]|min_length[6]|callback_check_account_double');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|max_length[255]|min_length[6]');
			$this->form_validation->set_rules('re_password', 'Re Password', 'trim|required|matches[password]');
			$this->form_validation->set_rules('level', 'Level', 'callback_check_level');
			$this->form_validation->set_rules('notlpn', 'Nomor Telepon', 'trim|max_length[20]|numeric');
			$this->form_validation->set_rules('alamat', 'Alamat', 'trim|max_length[1000]');
			$this->form_validation->set_rules('email', 'Email', 'trim|callback_check_email_double');
			
			if($_POST['submit']=='edit')
				$this->form_validation->set_rules('id_account', 'ID Account', 'required|callback_check_account_id');
			
			if ($this->form_validation->run() == FALSE){
				if($id_account!=''){
					$data['load']=$this->db1->account_load($id_account);
				}
				$data['level']=$this->db1->level();
				$this->load->view('page/account_modif',$data);
			}else{
				if($_SESSION['form_key']!=$_POST['uniqid']){
					$_SESSION['form_key']=$_POST['uniqid'];					
				
					if($_POST['submit']=='baru'){
						$account_baru=$this->db1->account_baru();
						if($account_baru){
							$this->session->set_flashdata('account', '<div class="alert alert-success text-center">Berhasil Input</div>');
							redirect('Admin/Account');
						}else{
							$data['level']=$this->db1->level();
							$this->load->view('page/account_modif',$data);
						}
					}else if($_POST['submit']=='edit'){
						$account_edit=$this->db1->account_edit();
						if($account_edit){
							$this->session->set_flashdata('account', '<div class="alert alert-success text-center">Berhasil Input</div>');
							redirect('Admin/Account');
						}else{
							$data['level']=$this->db1->level();
							$this->load->view('page/account_modif',$data);
						}
					}
				}else{
					redirect('Admin/Account');
				}
			}
		}else{ 
			if($id_account!=''){
				$data['load']=$this->db1->account_load($id_account);
			}
			$data['level']=$this->db1->level();
			$this->load->view('page/account_modif',$data);
		}		
	}	
	public function check_email_double(){
		$check=$this->db1->ajax_email_double();		
		$this->form_validation->set_message('check_email_double', 'Email sudah ada');
		return $check;
	}
	public function check_level($str){
		$nilai = FALSE;
		$check=$this->db1->level();		
		foreach($check as $key=>$value){
			if($value->nama == strtolower($str)){
				$nilai = TRUE;
			}
		}
		$this->form_validation->set_message('check_level', 'Level user tidak ada');
		return $nilai;		
	}
	public function check_account_double(){
		$check=$this->db1->check_account_double();		
		$this->form_validation->set_message('check_account_double', 'Nama sudah ada');
		return $check;
	}	
	public function check_account_id($str){
		$check=$this->db1->check_account_id($str);
		$this->form_validation->set_message('check_account_id', 'ID tidak ditemukan');
		return $check;		
	}
	public function ajax_account_hapus(){
		$data = $this->check();
		$data['check']=$this->db1->ajax_account_hapus();		
		echo json_encode(array("nilai" => $data['check'], "csrf" => array("name" => $this->security->get_csrf_token_name(), "hash" => $this->security->get_csrf_hash())));		
		exit();		
	}
	public function ajax_account_double(){
		$data['check']=$this->db1->check_account_double();		
		echo json_encode(array("nilai" => $data['check'], "csrf" => array("name" => $this->security->get_csrf_token_name(), "hash" => $this->security->get_csrf_hash())));		
		exit();
	}


	//CUSTOM
	public function Custom(){
		$data = $this->check();
		$data['list']=$this->db1->kategori_list();
		$this->load->view('page/custom',$data);
	}
	public function Custom_Modif(){
		$data = $this->check();
		$id_kategori = (isset($_POST['id_kategori']) ? $_POST['id_kategori'] : (isset($_GET['id']) ? $_GET['id'] : ''));
		$id_custom = (isset($_POST['id_custom']) ? $_POST['id_custom'] : (isset($_GET['id_custom']) ? $_GET['id_custom'] : ''));
		
		if($id_kategori==''){
			redirect('Admin/Custom');
			exit();
		}
		
		if(isset($_POST['submit'])){
			$this->form_validation->set_rules('id_kategori', 'Kategori', 'trim|required|callback_check_custom_modif|callback_check_custom_double');
			$this->form_validation->set_rules('nama', 'Nama', 'trim|required|max_length[85]');
			$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|max_length[1000]');
			if($_POST['submit']=='edit')
				$this->form_validation->set_rules('id_custom', 'Custom', 'trim|required');
			
			if ($this->form_validation->run() == FALSE){
				if($id_custom!=''){
					$data['detail']=$this->db1->custom_detail($id_kategori,$id_custom);
				}	
				$this->load->view('page/custom_modif',$data);
			}else{
				if($_SESSION['form_key']!=$_POST['uniqid']){
					$_SESSION['form_key']=$_POST['uniqid'];					
					
					if($_POST['submit']=='baru'){
						$data['custom_baru']=$this->db1->custom_baru();
						$this->session->set_flashdata('custom', '<div class="alert alert-success text-center">Berhasil Input</div>');
					}else if($_POST['submit']=='edit'){
						$this->db1->custom_edit();
						$this->session->set_flashdata('custom', '<div class="alert alert-success text-center">Berhasil Update</div>');
					}
				}
				$this->session->set_flashdata('id_kategori', $_POST['id_kategori']);
				redirect('Admin/Custom');
			}
		}else{
			if($id_custom!=''){
				$data['detail']=$this->db1->custom_detail($id_kategori,$id_custom);
			}
			$this->load->view('page/custom_modif', $data);
		}
	}
	public function ajax_custom(){
		$data['load']=$this->db1->ajax_custom();	
		echo json_encode(array("nilai" => $data['load'], "csrf" => array("name" => $this->security->get_csrf_token_name(), "hash" => $this->security->get_csrf_hash())));		
		exit();
	}
	public function ajax_custom_hapus(){
		$data = $this->check();
		$this->session->set_flashdata('id_kategori', $_POST['id_kategori']);
		$data['ajax_custom_hapus']=$this->db1->ajax_custom_hapus();	
		echo json_encode(array("nilai" => $data['ajax_custom_hapus'], "csrf" => array("name" => $this->security->get_csrf_token_name(), "hash" => $this->security->get_csrf_hash())));		
		exit();
	}
	public function ajax_custom_double(){
		$data['check']=$this->db1->check_custom_double();		
		echo json_encode(array("nilai" => $data['check'], "csrf" => array("name" => $this->security->get_csrf_token_name(), "hash" => $this->security->get_csrf_hash())));		
		exit();
	}
	public function check_custom_modif(){
		$data['check']=$this->db1->check_custom_modif();		
		$this->form_validation->set_message('check_custom_modif', 'Data tidak sesuai');
		return $data['check'];
	}
	public function check_custom_double(){
		$data['check']=$this->db1->check_custom_double();		
		$this->form_validation->set_message('check_custom_double', 'Nama Opsi sudah ada');
		return $data['check'];
	}


	//KENDARAAN
	public function kendaraan(){
		$data = $this->check();
		$data['load']=$this->db1->kendaraan();
		$this->load->view('page/kendaraan',$data);		
	}
	public function kendaraan_modif(){
		$data = $this->check();
		$id_kendaraan = (isset($_POST['id_kendaraan']) ? $_POST['id_kendaraan'] : (isset($_GET['id_kendaraan']) ? $_GET['id_kendaraan'] : ''));
		if(isset($_POST['submit'])){
			$this->form_validation->set_rules('user', 'User', 'trim|required|callback_check_user_kendaraan');
			$this->form_validation->set_rules('plat', 'Plat', 'trim|required|max_length[10]');
			$this->form_validation->set_rules('merek', 'Merek', 'trim|required|max_length[255]');
			$this->form_validation->set_rules('tipe', 'Tipe', 'trim|required|max_length[255]');

			if($_POST['submit']=='edit')
				$this->form_validation->set_rules('id_kendaraan', 'ID Kendaraan', 'required|callback_check_kendaraan_id');
			
			if ($this->form_validation->run() == FALSE){
				if($id_kendaraan!=''){
					$data['load']=$this->db1->kendaraan_load($id_kendaraan);
				}
				$data['user']=$this->db1->all_account();
				$data['lantai']=$this->db1->all_lantai();
				$this->load->view('page/kendaraan_modif',$data);
				
			}else{
				if($_SESSION['form_key']!=$_POST['uniqid']){
					$_SESSION['form_key']=$_POST['uniqid'];					
				
					if($_POST['submit']=='baru'){
						$kendaraan_baru=$this->db1->kendaraan_baru();
						if($kendaraan_baru){
							$this->session->set_flashdata('kendaraan', '<div class="alert alert-success text-center">Berhasil Input</div>');
							redirect('Admin/Kendaraan');
						}else{
							$this->load->view('page/kendaraan_modif');
						}
					}else if($_POST['submit']=='edit'){
						$kendaraan_edit=$this->db1->kendaraan_edit();
						if($kendaraan_edit){
							$this->session->set_flashdata('kendaraan', '<div class="alert alert-success text-center">Berhasil Input</div>');
							redirect('Admin/Kendaraan');
						}else{
							$this->load->view('page/kendaraan_modif');
						}
					}
				}else{
					redirect('Admin/Kendaraan');
				}
			}
		}else{
			if($id_kendaraan!=''){
				$data['load']=$this->db1->kendaraan_load($id_kendaraan);
			}
			$data['user']=$this->db1->all_account();
			$data['lantai']=$this->db1->all_lantai();
			$this->load->view('page/kendaraan_modif',$data);
		}		
	}	
	public function check_kendaraan_id($str){
		$check=$this->db1->check_kendaraan_id($str);
		$this->form_validation->set_message('check_kendaraan_id', 'ID tidak ditemukan');
		return $check;		
	}
	public function check_user_kendaraan($str){
		$nilai = FALSE;
		if($_SESSION['level']=='manager'){
			$check=$this->db1->all_account($str); 
			foreach($check as $key=>$value){
				if($value->id_account==$str)
					$nilai=TRUE;
				if($nilai==TRUE)
					break;
			}
		}else{
			if($_SESSION['id_account']==$str){
				$nilai=TRUE;
			}
		}
		$this->form_validation->set_message('check_user_kendaraan', 'User tidak sesuai');
		return $nilai;
	}
	public function check_kendaraan_double(){
		$check=$this->db1->check_kendaraan_double();		
		$this->form_validation->set_message('check_kendaraan_double', 'Plat sudah ada');
		return $check;
	}	
	public function ajax_kendaraan_hapus(){
		$data = $this->check();
		$data['check']=$this->db1->ajax_kendaraan_hapus();		
		echo json_encode(array("nilai" => $data['check'], "csrf" => array("name" => $this->security->get_csrf_token_name(), "hash" => $this->security->get_csrf_hash())));		
		exit();		
	}
	public function ajax_kendaraan_double(){
		$data['check']=$this->db1->check_kendaraan_double();		
		echo json_encode(array("nilai" => $data['check'], "csrf" => array("name" => $this->security->get_csrf_token_name(), "hash" => $this->security->get_csrf_hash())));		
		exit();
	}
	public function kendaraan_bayar(){
		$data = $this->check();
		if(isset($_POST['submit'])){
			$this->form_validation->set_rules('id_kendaraan', 'ID Kendaraan', 'trim|required|callback_check_kendaraan_data');
			$this->form_validation->set_rules('bayar', 'Bayar', 'trim|callback_check_kendaraan_bayar');
			$this->form_validation->set_rules('plat', 'Plat', 'trim|required');
			if ($this->form_validation->run() == FALSE){
				$this->session->set_flashdata('kendaraan', "<div class='alert alert-danger text-center'>".validation_errors()."</div>");
				redirect('Admin/Kendaraan');
			}else{	
				if($_SESSION['form_key']!=$_POST['uniqid']){
					$_SESSION['form_key']=$_POST['uniqid'];	
					if($_POST['submit']=='bayar'){
						$this->session->set_flashdata('kendaraan', '<div class="alert alert-success text-center">Berhasil Input</div>');
						$check = $this->db1->kendaraan_bayar();
					}else if($_POST['submit']=='konfirmasi'){
						$this->session->set_flashdata('kendaraan', '<div class="alert alert-success text-center">Berhasil Konfirmasi</div>');
						$check = $this->db1->kendaraan_konfirmasi();
					}
					redirect('Admin/Kendaraan');
				}else{
					redirect('Admin/Kendaraan');
				}
			}
		}else{
			redirect('Admin/Kendaraan');
		}
	}
	public function check_kendaraan_data($str){
		$check=$this->db1->check_kendaraan_data($str);
		$this->form_validation->set_message('check_kendaraan_data', 'Data tidak sesuai');
		return $check;
	}
	public function check_kendaraan_bayar($str){
		$check=$this->db1->ajax_transaksi_bayar($_POST['id_kendaraan']);
		$str = str_replace(",","",$str);
		if($str>0){
			$this->form_validation->set_message('check_kendaraan_bayar', 'Nominal bayar tidak sesuai');
			if($str==$check[0]->nilai){
				$nilai = TRUE;
			}else{
				$nilai = FALSE;
			}
		}else{
			$this->form_validation->set_message('check_kendaraan_bayar', 'The Bayar field is required.');
			$nilai = FALSE;
		}
		return $nilai;
	}
	public function ajax_transaksi_konfirmasi(){
		$data = $this->check();
		$data['check']=$this->db1->ajax_transaksi_konfirmasi();		
		echo json_encode(array("nilai" => $data['check'], "csrf" => array("name" => $this->security->get_csrf_token_name(), "hash" => $this->security->get_csrf_hash())));		
		exit();
	}
	
	
	//TRANSAKSI
	public function transaksi(){
		$data = $this->check();
		$data['load']=$this->db1->transaksi();
		$this->load->view('page/transaksi',$data);		
	}
	public function transaksi_modif(){
		$data = $this->check();
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
						$byk[$value->radar] = check3($value->value,6,6);
					}else{
						$byk[$value->radar] = check4($value->value,15,4);
					}
				}
				$data['byk'] = $byk;
				$data['lantai']=$this->db1->all_lantai();
				$this->load->view('page/transaksi_modif',$data);
			}else{
				if($_SESSION['form_key']!=$_POST['uniqid']){
					$_SESSION['form_key']=$_POST['uniqid'];
				
					if($_POST['submit']=='baru'){
						$transaksi_baru = $this->db1->transaksi_baru();
						if($transaksi_baru==FALSE){
							$this->session->set_flashdata('transaksi_modif', '<div class="alert alert-danger text-center">Parkiran Penuh</div>');
						}else{
							$this->session->set_flashdata('transaksi_print', $transaksi_baru[0]->id_transaksi);
						}
					}
				}
				redirect('Admin/transaksi_modif');
			}
		}else{
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
			$this->load->view('page/transaksi_modif',$data);
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
					$digit = check3($monitoring[0]->value,6,6);
				}else{
					$digit = check4($monitoring[0]->value,15,4);
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
		$data = $this->check();
		$data['load']=$this->db1->load_tiket($_GET['print']);
		if($data['load']==FALSE){
			echo 'NOT FOUND';
		}else{
			$this->load->view('page/transaksi_print',$data);
		}
	}	
	public function transaksi_keluar(){
		$data = $this->check();
		if(isset($_POST['submit'])){
			$this->form_validation->set_rules('id_transaksi', 'ID Transaksi', 'trim|required|callback_check_transaksi_id');
			$this->form_validation->set_rules('waktu', 'Waktu', 'trim|required');
			$this->form_validation->set_rules('lama', 'Lama', 'trim|required');
			$this->form_validation->set_rules('bayar', 'Bayar', 'trim|callback_check_nominal');
			
			if ($this->form_validation->run()){
				if($_SESSION['form_key']!=$_POST['uniqid']){
					$_SESSION['form_key']=$_POST['uniqid'];	
					$data['bayar'] = $this->db1->transaki_bayar();
					$this->session->set_flashdata('transaksi_keluar', $_POST['id_transaksi']);
					redirect('Admin/Transaksi_Keluar');
				}else{
					redirect('Admin/Transaksi_Keluar');
				}					
			}else{
				$this->load->view('page/transaksi_keluar',$data);
			}
		}else{
			$this->load->view('page/transaksi_keluar',$data);
		}
	}
	public function ajax_transaksi_bayar(){
		$data['check']=$this->db1->ajax_transaksi_bayar($_POST['id_kendaraan']);
		echo json_encode(array("nilai" => $data['check'], "csrf" => array("name" => $this->security->get_csrf_token_name(), "hash" => $this->security->get_csrf_hash())));		
		exit();
	}
	public function ajax_transaksi_keluar(){
		$data = $this->check();
		$data['check']=$this->db1->transaksi_keluar($_POST['id_transaksi']);
		echo json_encode(array("nilai" => $data['check'], "csrf" => array("name" => $this->security->get_csrf_token_name(), "hash" => $this->security->get_csrf_hash())));		
		exit();
	}
	public function ajax_transaksi_id(){
		$data['check']=$this->db1->check_transaksi_id();
		echo json_encode(array("nilai" => $data['check'], "csrf" => array("name" => $this->security->get_csrf_token_name(), "hash" => $this->security->get_csrf_hash())));		
		exit();
	}
	public function check_transaksi_id(){
		$check=$this->db1->check_transaksi_id();
		$this->form_validation->set_message('check_transaksi_id', 'ID tidak ditemukan');
		return $check;
	}
	public function check_nominal($str){
		if(str_replace(",","",$str)<0){
			$this->form_validation->set_message('check_nominal', 'Nominal Bayar harus diisi');
			return FALSE;
		}else{
			$this->form_validation->set_message('check_nominal', 'Data tidak sesuai');
		}
		
		$check = $this->db1->transaksi_keluar($_POST['id_transaksi']);
		if($check[0]->nominal == str_replace(",","",$str)){
			$_POST['lama'] = $check[0]->lama;
			$nilai = TRUE;
		}else{
			$nilai = FALSE;
		}
	}
	public function ajax_transaksi_hapus(){
		$data = $this->check();
		$data['check']=$this->db1->ajax_transaksi_hapus();		
		echo json_encode(array("nilai" => $data['check'], "csrf" => array("name" => $this->security->get_csrf_token_name(), "hash" => $this->security->get_csrf_hash())));		
		exit();		
	}	
	
	
	//REPORT
	public function report(){
		$data = $this->check();
		if(isset($_POST['submit'])){
			$this->form_validation->set_rules('jenis', 'Jenis Report', 'trim|required');
			$this->form_validation->set_rules('dari', 'Dari Tanggal', 'trim|callback_check_tgl|callback_check_tgl_range');
			$this->form_validation->set_rules('sampai', 'Sampai Tanggal', 'trim|callback_check_tgl');
			$this->form_validation->set_rules('tipe', 'Tipe', 'trim|callback_check_tipe');
			
			if ($this->form_validation->run() == TRUE){
				$data['load']=$this->db1->report();
			}
			$this->load->view('page/report',$data);
		}else{
			$this->load->view('page/report',$data);
		}
	}
	public function ajax_report_approve(){
		$data = $this->check();
		$approve = $this->db1->approve();
		exit();
	}
	public function check_tipe($str){
		$this->form_validation->set_message('check_tipe', '%s harap dipilih');
		if($_POST['jenis']=='member'){
			if($_POST['tipe']=='aktif' || $_POST['tipe']=='tidak_aktif'){
				return true;
			}else{
				return false;
			}
		}else{
			return true;
		}	
	}
	public function check_tgl($str){
		$this->form_validation->set_message('check_tgl', 'Format %s tidak sesuai');
		if($_POST['jenis']=='member'){
			return true;
		}else{
			$str = explode('-',$str);
			return checkdate($str[1],$str[0],$str[2]);			
		}
	}
	public function check_tgl_range(){
		$this->form_validation->set_message('check_tgl_range', 'Range tanggal tidak sesuai');
		if($_POST['jenis']=='member'){
			return true;
		}
		$dari = strtotime($_POST['dari']);
		$sampai = strtotime($_POST['sampai']);

		if ($dari > $sampai) {
			return FALSE;
		}else{
			return TRUE;
		}
	}
	public function ganti_password(){
		$data = $this->check();
		if(isset($_POST['submit'])){
			$this->form_validation->set_rules('password_lama', 'Password Lama', 'trim|required|callback_check_password');
			$this->form_validation->set_rules('pass_confirmation', 'Password Baru', 'trim|required|min_length[6]|max_length[255]');
			$this->form_validation->set_rules('pass', 'Password Confirmation', 'trim|matches[pass_confirmation]');
			if ($this->form_validation->run() == FALSE){
				$this->load->view('page/ganti_password');
			}else{
				if($_SESSION['form_key']!=$_POST['uniqid']){
					$_SESSION['form_key']=$_POST['uniqid'];
					$check = $this->db1->ganti_password();
					$this->session->set_flashdata('ganti_password', '<div class="alert alert-success text-center">Berhasil Ganti Password</div>');
					redirect('admin/ganti_password');
				}else{
					redirect('admin/ganti_password');
				}
			}
		}else{
			$this->load->view('page/ganti_password');
		}
	}
	public function check_password($str){
		$this->form_validation->set_message('check_password', '%s tidak sesuai');
		$check = $this->db1->check_password($str);
		if($check[0]->nilai=='true')
			return TRUE;
		else
			return FALSE;
	}
	
	//LOGOUT
	public function Logout() {
		$this->session->unset_userdata(array('id_account','username','nama_lengkap','gambar','form_key'));
		redirect('Admin');
	}
}