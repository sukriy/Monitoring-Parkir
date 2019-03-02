<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Admin_Model extends CI_Model {
	public function sign_in(){
		$_POST['password']=hash_password($_POST['password']);
		$query = "
			select * 
			from account 
			where username = ".$this->post($_POST['username'])." and password = ".$this->post($_POST['password'])." and right(email,2)<>'_1'";
		$query = $this->db->query($query);
		
		if($query->num_rows()==1){
			return $query->result();
		}else{
			return FALSE;
		}
	}
	public function sign_up(){
		$query = "select concat('A',lpad(ifnull(max(mid(id_account,2,length(id_account)-1)),0)+1,9,0)) indeks from account";
		$query = $this->db->query($query);
		$row = $query->result();		
		
		$query = "
			INSERT INTO `account`
			(`id_account`, `nama_lengkap`, `username`, `password`, `level`, `notlpn`, `alamat`, `email`) VALUES 
			(".$this->post($row[0]->indeks).",".$this->post($_POST['nama_lengkap']).",".$this->post($_POST['username']).",".$this->post(hash_password($_POST['pass_confirmation'])).",".
			$this->post('pelanggan').",".$this->post($_POST['notlpn']).",".$this->post($_POST['alamat']).",".$this->post($_POST['email'].'_1').")";
		$query = $this->db->query($query);
		return TRUE;			
	}
	public function konfirmasi(){
		$query = "select * from account where right(email,2)='_1'";
		$query = $this->db->query($query);
		$row = $query->result();
		foreach($row as $key=>$value){
			$email = explode('_',$value->email);
			if($_GET['key']==hash_password($email[0])){
				$query = "update account set email = ".$this->post($email[0])." where id_account = ".$this->post($value->id_account)." and right(email,2)='_1'";
				$query = $this->db->query($query);
				return true;
			}
		}
		return false;
	}
	public function update_sensor(){
		date_default_timezone_set("Asia/Bangkok");
		$tgl=date("Y-m-d H:i:s");		
		$query = "insert into sensor_data(`tgl`, `radar`,`value`) values (".$this->post($tgl).",".$this->post($_GET['radar']).",".$this->post(str_replace(substr($_GET['value'],strripos($_GET['value'],"}")+1),"",$_GET['value'])).")";
		$query = $this->db->query($query);
	}
	public function monitoring($level){
		$query = "select * from sensor_data where radar = ".$this->post($level)." order by tgl desc limit 1";
		$query = $this->db->query($query);
		
		if($query->num_rows()==1){
			return $query->result();
		}else{
			return FALSE;
		}		
	}
	public function all_monitoring(){
		$query = "
			(select * from sensor_data where radar = '1' order by tgl desc limit 1)
			union all
			(select * from sensor_data where radar = '2' order by tgl desc limit 1)		
		";
		$query = $this->db->query($query);
		
		if($query->num_rows()==0){
			return FALSE;
		}else{
			return $query->result();
		}		
	}
	public function temp(){
		date_default_timezone_set("Asia/Bangkok");
		$tgl=date("Y-m-d H:i:s");		
		$query = "select lokasi from transaksi where TIMESTAMPDIFF(MINUTE,tgl_masuk,'".$tgl."') <= 5 and flag='1'";
		$query = $this->db->query($query);
		return $query->result();
	}
	public function ajax_email_double(){
		$id_account = (isset($_POST['id_account']) ? $_POST['id_account'] : (isset($_GET['id_account']) ? $_GET['id_account'] : ''));
		
		if($id_account==''){
			$query = "select email from account where (email = ".$this->post($_POST['email'].'_1')." or email = ".$this->post($_POST['email']).") and email<>''";
		}else{
			$query = "select email from account where (email = ".$this->post($_POST['email'].'_1')." or email = ".$this->post($_POST['email']).") and email<>'' and id_account<>".$this->post($id_account);
		}		
		
		$query = $this->db->query($query);
		$row = $query->num_rows();
		if($row==0)
			return TRUE;

		return FALSE;	
	}
	public function check_kendaraan(){
		if($_SESSION['level']=='pelanggan'){
			$query = "select count(id_kendaraan) byk from kendaraan where id_account = '".$_SESSION['id_account']."'";
			$query = $this->db->query($query);
			$query = $query->result();
			if($query[0]->byk==0){
				$this->session->set_flashdata('check_kendaraan', '<div class="alert alert-danger text-center">Harap Jadi member dengan daftarkan kendaraan Anda</div>');				
			}
		}
		
		return true;
	}
	public function coba(){
		date_default_timezone_set("Asia/Bangkok");
		$tgl=date("Y-m-d H:i:s");		
		
		$query = "INSERT INTO `sensor_data`(`tgl`, `radar`, `value`) VALUES (".$this->post($tgl).",".$this->post('1').",".$this->post('2').")";
		$query = $this->db->query($query);		
		return true;
	}
	
	
	//ACCOUNT
	public function account(){
		$query = "
			select * 
			from account
			where 
				case when '".$_SESSION['level']."' = 'pelanggan'
				then id_account = '".$_SESSION['id_account']."'
				else true end
		";
		$query = $this->db->query($query);
		return $query->result();
	}
	public function account_load($str){
		$query = "select * from account where id_account = ".$this->post($str);
		$query = $this->db->query($query);
		return $query->result();
	}
	public function account_baru(){
		date_default_timezone_set("Asia/Bangkok");
		$tgl=date("Y-m-d H:i:s");
		
		$query = "select concat('A',lpad(ifnull(max(mid(id_account,2,length(id_account)-1)),0)+1,9,0)) indeks from account";
		$query = $this->db->query($query);
		$row = $query->result();
		
		if(isset($_FILES) && $_FILES['gambar']['name']!=''){
			$temp = explode('.',htmlspecialchars($_FILES['gambar']['name'], ENT_QUOTES, 'UTF-8'));
			$_FILES['gambar']['name']=$row[0]->indeks.'.'.$temp[1];
			$_FILES['gambar']['name']=strtolower($_FILES['gambar']['name']);
			
			$config['upload_path']          = 'images/account';
			$config['allowed_types']        = 'gif|jpg|png|jpeg';
			$config['max_size']             = 2000;

			// $this->load->library('upload', $config);
			$this->upload->initialize($config);
			
			if (!$this->upload->do_upload('gambar')){
				$this->session->set_flashdata('account_modif', '<div class="alert alert-danger text-center">'.$this->upload->display_errors().'</div>');
				return false;
			}else{
				$data = array('upload_data' => $this->upload->data());
				$gambar = $data['upload_data']['file_name'];
			}
		}else{
			$gambar = '';
		}
		$_POST['password']=hash_password($_POST['password']);
		$query = "
		INSERT INTO `account`(`id_account`, `nama_lengkap`, `username`, `password`, `email`, `level`, `notlpn`, `alamat`, `gambar`) VALUES 
		(".$this->post($row[0]->indeks).",".$this->post($_POST['nama_lengkap']).",".$this->post($_POST['username']).",".$this->post($_POST['password']).",".$this->post($_POST['email']).",".$this->post($_POST['level']).",".$this->post($_POST['notlpn']).",".$this->post($_POST['alamat']).",".$this->post($gambar).")";
		$query = $this->db->query($query);

		return TRUE;
	}
	public function account_edit(){
		date_default_timezone_set("Asia/Bangkok");
		$tgl=date("Y-m-d H:i:s");
				
		if(isset($_FILES) && $_FILES['gambar']['name']!=''){
			$temp = explode('.',htmlspecialchars($_FILES['gambar']['name'], ENT_QUOTES, 'UTF-8'));
			$_FILES['gambar']['name']=$row[0]->indeks.'.'.$temp[1];
			$_FILES['gambar']['name']=strtolower($_FILES['gambar']['name']);
			
			$config['upload_path']          = 'images/account';
			$config['allowed_types']        = 'gif|jpg|png|jpeg';
			$config['max_size']             = 2000;

			// $this->load->library('upload', $config);
			$this->upload->initialize($config);
			
			if (!$this->upload->do_upload('gambar')){
				$this->session->set_flashdata('account_modif', '<div class="alert alert-danger text-center">'.$this->upload->display_errors().'</div>');
				return false;
			}else{
				$data = array('upload_data' => $this->upload->data());
				$gambar = $data['upload_data']['file_name'];
			}
		}else{
			$gambar = '';
		}
		
		$query = "select * from account where id_account = ".$this->post($_POST['id_account'])." and password = ".$this->post($_POST['password']);
		$query = $this->db->query($query);
		$baris = $query->num_rows();

		if($baris!=1){
			$_POST['password']=hash_password($_POST['password']);
		}

		if($gambar==''){
			$query = "
			UPDATE `account` SET `nama_lengkap`=".$this->post($_POST['nama_lengkap']).",`username`=".$this->post($_POST['username']).",`password`=".$this->post($_POST['password']).",`level`=".$this->post($_POST['level']).", `email` =".$this->post($_POST['email']).",
			`notlpn`=".$this->post($_POST['notlpn']).",`alamat`=".$this->post($_POST['alamat'])." WHERE id_account=".$this->post($_POST['id_account'])."
			";		
		}else{
			$query = "
			UPDATE `account` SET `nama_lengkap`=".$this->post($_POST['nama_lengkap']).",`username`=".$this->post($_POST['username']).",`password`=".$this->post($_POST['password']).",`level`=".$this->post($_POST['level']).", `email` =".$this->post($_POST['email']).",
			`notlpn`=".$this->post($_POST['notlpn']).",`alamat`=".$this->post($_POST['alamat']).",`gambar`=".$this->post($gambar)." WHERE id_account=".$this->post($_POST['id_account'])."
			";	
		}

		$query = $this->db->query($query);
		return TRUE;
	}
	public function ajax_account_hapus(){
		$query = "delete from account where id_account = ".$this->post($_POST['id_account']);
		$query = $this->db->query($query);
		return TRUE;
	}
	public function check_account_id(){
		$query = "select * from account where id_account = ".$this->post($_POST['id_account']);
		$query = $this->db->query($query);
		if($query->num_rows()==1)
			return TRUE;
		else
			return FALSE;
	}
	public function check_account_double(){
		$id_account = (isset($_POST['id_account']) ? $_POST['id_account'] : (isset($_GET['id_account']) ? $_GET['id_account'] : ''));
		
		if($id_account==''){
			$query = "select * from account where username = ".$this->post($_POST['username']);
			$query = $this->db->query($query);
		}else{
			$query = "select * from account where username = ".$this->post($_POST['username'])." and id_account <> ".$this->post($id_account);
			$query = $this->db->query($query);
		}

		$row = $query->num_rows();
		if($row==0)
			return TRUE;

		return FALSE;		
	}	
	public function level(){
		$query = "select * from custom where id_kategori='c0004'";
		$query = $this->db->query($query);
		return $query->result();
	}
	
	
	//CUSTOM
	public function kategori_list(){
		$query = "select * from kategori";
		$query = $this->db->query($query);
		return $query;
	}
	public function ajax_custom(){
		$query = "
		select kategori.nama_kategori, custom.*
		from custom 
		left join kategori on kategori.id_kategori = custom.id_kategori
		where custom.id_kategori = ".$this->post($_POST['id_kategori'])."";
		$query = $this->db->query($query);
		$baris = $query->num_rows();
		
		if($baris==0){
			$array = array();
		}else{
			foreach($query->result() as $row){
				$newdata =  array (
					'nama_kategori' => cetak($row->nama_kategori),
					'id_kategori' => cetak($row->id_kategori),
					'id_custom' => cetak($row->id_custom),
					'nilai' => number_format(cetak($row->nilai),0),
					'nama' => cetak($row->nama),
					'keterangan' => cetak($row->keterangan)
				);
				$array[] = $newdata;
			}			
		}
		
		return $array;
	}
	public function ajax_custom_hapus(){
		$query = "
		delete from custom 
		where id_kategori = ".$this->post($_POST['id_kategori'])." and id_custom = ".$this->post($_POST['id_custom']);
		$query = $this->db->query($query);
		return TRUE;
	}
	public function custom_baru(){
		date_default_timezone_set("Asia/Bangkok");
		$tgl=date("Y-m-d H:i:s");

		$query = "select concat('C',lpad(ifnull(max(mid(id_custom,2,length(id_custom)-1)),0)+1,9,0)) indeks from custom where id_kategori = ".$this->post($_POST['id_kategori']);
		$query = $this->db->query($query);
		$row = $query->result();		
		
		$query = "INSERT INTO `custom`(`id_kategori`, `id_custom`, `nama`, `nilai`, `keterangan`) VALUES 
		(".$this->post($_POST['id_kategori']).",".$this->post($row[0]->indeks).",".$this->post($_POST['nama']).",".$this->post(str_replace(",","",$_POST['nilai'])).",".$this->post($_POST['keterangan']).")";
		$query = $this->db->query($query);
		
		return true;
	}
	public function custom_detail($id_kategori,$id_custom){
		$query = "
			select *
			from custom
			where id_kategori = ".$this->post($id_kategori)." and id_custom = ".$this->post($id_custom)."
		";
		$query = $this->db->query($query);
		return $query->result();
	}
	public function custom_edit(){
		date_default_timezone_set("Asia/Bangkok");
		$tgl=date("Y-m-d H:i:s");
 
		$query = "
			update custom
			set nama = ".$this->post($_POST['nama']).", nilai = ".str_replace(",","",$this->post($_POST['nilai'])).", keterangan = ".$this->post($_POST['keterangan'])."
			where id_kategori = ".$this->post($_POST['id_kategori'])." and id_custom = ".$this->post($_POST['id_custom']);
		$query = $this->db->query($query);
		return true;
	}
	public function check_custom_modif(){
		$id_kategori = (isset($_POST['id_kategori']) ? $_POST['id_kategori'] : (isset($_GET['id']) ? $_GET['id'] : ''));
		$id_custom = (isset($_POST['id_custom']) ? $_POST['id_custom'] : (isset($_GET['id_custom']) ? $_GET['id_custom'] : ''));

		$query = "select id_kategori from kategori where id_kategori = ".$this->post($_POST['id_kategori']);
		$query = $this->db->query($query);

		$row = $query->num_rows();
		if($row==0)
			return FALSE;

		return TRUE;
	}
	public function check_custom_double(){
		$id_kategori = (isset($_POST['id_kategori']) ? $_POST['id_kategori'] : (isset($_GET['id']) ? $_GET['id'] : ''));
		$id_custom = (isset($_POST['id_custom']) ? $_POST['id_custom'] : (isset($_GET['id_custom']) ? $_GET['id_custom'] : ''));

		if($id_custom==''){
			$query = "select nama from custom where id_kategori = ".$this->post($_POST['id_kategori'])." and nama = ".$this->post($_POST['nama']);
			$query = $this->db->query($query);
		}else{
			$query = "select nama from custom where id_kategori = ".$this->post($id_kategori)." and nama = ".$this->post($_POST['nama'])." and id_custom <> ".$this->post($id_custom);
			$query = $this->db->query($query);
		}
		
		$row = $query->num_rows();
		if($row>0) 
			return FALSE;

		return TRUE; 
	}
	
	
	//KENDARAAAN
	public function kendaraan(){
		$query = "
			select kendaraan.*, account.nama_lengkap, pembayaran.id_pembayaran
			from kendaraan
			left join account on account.id_account = kendaraan.id_account
            left join pembayaran on pembayaran.id_kendaraan = kendaraan.id_kendaraan and pembayaran.konfirmasi=''
			where 
				case when '".$_SESSION['level']."' = 'pelanggan'
				then kendaraan.id_account = '".$_SESSION['id_account']."'
				else true end
			";
		$query = $this->db->query($query);
		return $query->result();
	}
	public function kendaraan_load($str){
		$query = "select * from kendaraan where id_kendaraan = ".$this->post($str);
		$query = $this->db->query($query);
		return $query->result();
	}
	public function kendaraan_baru(){
		date_default_timezone_set("Asia/Bangkok");
		$tgl=date("Y-m-d H:i:s");
		
		$query = "select concat('K',lpad(ifnull(max(mid(id_kendaraan,2,length(id_kendaraan)-1)),0)+1,9,0)) indeks from kendaraan";
		$query = $this->db->query($query);
		$row = $query->result();
		
		if(isset($_FILES) && $_FILES['gambar']['name']!=''){
			$temp = explode('.',htmlspecialchars($_FILES['gambar']['name'], ENT_QUOTES, 'UTF-8'));
			$_FILES['gambar']['name']=$row[0]->indeks.'.'.$temp[1];
			$_FILES['gambar']['name']=strtolower($_FILES['gambar']['name']);
			
			$config['upload_path']          = 'images/kendaraan';
			$config['allowed_types']        = 'gif|jpg|png|jpeg';
			$config['max_size']             = 2000;

			// $this->load->library('upload', $config);
			$this->upload->initialize($config);
			
			if (!$this->upload->do_upload('gambar')){
				$this->session->set_flashdata('kendaraan_modif', '<div class="alert alert-danger text-center">'.$this->upload->display_errors().'</div>');
				return false;
			}else{
				$data = array('upload_data' => $this->upload->data());
				$gambar = $data['upload_data']['file_name'];
			}
		}else{
			$gambar = '';
		}
		
		$query = "
			INSERT INTO `kendaraan`(`id_kendaraan`, `id_account`, `plat`, `merek`, `tipe`, `gambar`) VALUES 
			(".$this->post($row[0]->indeks).",".$this->post($_POST['user']).",".$this->post($_POST['plat']).",
			".$this->post($_POST['merek']).",".$this->post($_POST['tipe']).",".$this->post($gambar).")";

		$query = $this->db->query($query);

		return TRUE;
	}
	public function kendaraan_edit(){
		date_default_timezone_set("Asia/Bangkok");
		$tgl=date("Y-m-d H:i:s");
		
		if(isset($_FILES) && $_FILES['gambar']['name']!=''){
			$temp = explode('.',htmlspecialchars($_FILES['gambar']['name'], ENT_QUOTES, 'UTF-8'));
			$_FILES['gambar']['name']=$row[0]->indeks.'.'.$temp[1];
			$_FILES['gambar']['name']=strtolower($_FILES['gambar']['name']);
			
			$config['upload_path']          = 'images/kendaraan';
			$config['allowed_types']        = 'gif|jpg|png|jpeg';
			$config['max_size']             = 2000;

			// $this->load->library('upload', $config);
			$this->upload->initialize($config);
			
			if (!$this->upload->do_upload('gambar')){
				$this->session->set_flashdata('account_modif', '<div class="alert alert-danger text-center">'.$this->upload->display_errors().'</div>');
				return false;
			}else{
				$data = array('upload_data' => $this->upload->data());
				$gambar = $data['upload_data']['file_name'];
			}
		}else{
			$gambar = '';
		}
		
		if($gambar==''){
			$query = "
			UPDATE `kendaraan` SET `id_account`=".$this->post($_POST['user']).",`plat`=".$this->post($_POST['plat']).",
			`merek`=".$this->post($_POST['merek']).",`tipe`=".$this->post($_POST['tipe'])."
			WHERE `id_kendaraan`=".$this->post($_POST['id_kendaraan'])."
			";
		}else{
			$query = "
			UPDATE `kendaraan` SET `id_account`=".$this->post($_POST['user']).",`plat`=".$this->post($_POST['plat']).",
			`merek`=".$this->post($_POST['merek']).",`tipe`=".$this->post($_POST['tipe']).", `gambar`=".$this->post($gambar)." 
			WHERE `id_kendaraan`=".$this->post($_POST['id_kendaraan'])."
			";
		}

		$query = $this->db->query($query);

		return TRUE;
	}
	public function ajax_kendaraan_hapus(){
		$query = "delete from kendaraan where id_kendaraan = ".$this->post($_POST['id_kendaraan']);
		$query = $this->db->query($query);
		return TRUE;
	}
	public function check_kendaraan_id(){
		$query = "select * from kendaraan where id_kendaraan = ".$this->post($_POST['id_kendaraan']);
		$query = $this->db->query($query);
		if($query->num_rows()==1)
			return TRUE;
		else
			return FALSE;
	}
	public function check_kendaraan_double(){
		$id_kendaraan = (isset($_POST['id_kendaraan']) ? $_POST['id_kendaraan'] : (isset($_GET['id_kendaraan']) ? $_GET['id_kendaraan'] : ''));
		
		if($id_kendaraan==''){
			$query = "select * from kendaraan where plat = ".$this->post($_POST['plat']);
			$query = $this->db->query($query);
		}else{
			$query = "select * from kendaraan where plat = ".$this->post($_POST['plat'])." and id_kendaraan <> ".$this->post($id_kendaraan);
			$query = $this->db->query($query);
		}

		$row = $query->num_rows();
		if($row==0)
			return TRUE;

		return FALSE;		
	}	
	public function all_account(){
		if($_SESSION['level']=='manager')
			$query = "select * from account order by nama_lengkap";
		else
			$query = "select * from account where id_account = '".$_SESSION['id_account']."' order by nama_lengkap";
		$query = $this->db->query($query);
		return $query->result();
	}
	public function check_kendaraan_data(){
		$query = "select * from kendaraan where id_kendaraan = ".$this->post($_POST['id_kendaraan'])." and plat = ".$this->post($_POST['plat']);
		$query = $this->db->query($query);
		if($query->num_rows()==1){
			return TRUE;
		}else{
			return FALSE;
		}		
	}
	public function kendaraan_bayar(){
		date_default_timezone_set("Asia/Bangkok");
		$tgl=date("Y-m-d H:i:s");
		$tgl0=date("Y-m-d");
		
		$query = "select concat('B',lpad(ifnull(max(mid(id_pembayaran,2,length(id_pembayaran)-1)),0)+1,9,0)) indeks from pembayaran";
		$query = $this->db->query($query);
		$row = $query->result();	
		
		if(isset($_FILES) && $_FILES['gambar']['name']!=''){
			$temp = explode('.',htmlspecialchars($_FILES['gambar']['name'], ENT_QUOTES, 'UTF-8'));
			$_FILES['gambar']['name']=$row[0]->indeks.'.'.$temp[1];
			$_FILES['gambar']['name']=strtolower($_FILES['gambar']['name']);
			
			$config['upload_path']          = 'images/bayar';
			$config['allowed_types']        = 'gif|jpg|png|jpeg';
			$config['max_size']             = 2000;

			// $this->load->library('upload', $config);
			$this->upload->initialize($config);
			
			if (!$this->upload->do_upload('gambar')){
				$this->session->set_flashdata('kendaraan', '<div class="alert alert-danger text-center">'.$this->upload->display_errors().'</div>');
				return false;
			}else{
				$data = array('upload_data' => $this->upload->data());
				$gambar = $data['upload_data']['file_name'];
			}
		}else{
			$gambar = '';
		}
		$query = "delete from pembayaran where upload = ".$this->post($_SESSION['id_account'])." and konfirmasi = '' and id_kendaraan = ".$this->post($_POST['id_kendaraan']);
		$query = $this->db->query($query);
		
		$query = "
			SET @tgl = (
				select 
				case when '".$tgl0."' > tgl0 then '".$tgl0."'
				else DATE_ADD(tgl0, INTERVAL 1 DAY)
				end
				from (select ifnull(max(tgl_aktif_sampai),'1900-01-01') tgl0 from pembayaran where id_kendaraan='k000000001' order by tgl_aktif_sampai desc limit 1)t
			);
		";
		$query = $this->db->query($query);		

		$query = "
			INSERT INTO `pembayaran`
			(`id_pembayaran`, `tgl_input`, `upload`, `id_kendaraan`, `plat`, `bayar`, `tgl_aktif_dari`, `tgl_aktif_sampai`, `gambar`, `keterangan`) VALUES 
			(".$this->post($row[0]->indeks).",".$this->post($tgl).",".$this->post($_SESSION['id_account']).",".$this->post($_POST['id_kendaraan'])
			.",".$this->post($_POST['plat']).",".$this->post(str_replace(",","",$_POST['bayar'])).",@tgl,DATE_ADD(@tgl, INTERVAL 30 DAY),".$this->post($gambar).",".$this->post($_POST['keterangan']).")";

		$query = $this->db->query($query);
		return TRUE;			
	}
	public function ajax_transaksi_bayar($str){
		// $query = "
		// select nilai from custom cross join kendaraan where id_kategori ='C0003' and id_kendaraan = ".$this->post($str)." and 
		// id_custom = case when lokasi = '' then 'c000000001' else 'c000000002' end";
		$query = "select nilai from custom cross join kendaraan where id_kategori ='C0003' and id_kendaraan = ".$this->post($str);
		$query = $this->db->query($query);
		return $query->result();
	}
	public function ajax_transaksi_konfirmasi(){
		$query = "
		select *
		from pembayaran
		where id_kendaraan = ".$this->post($_POST['id_kendaraan'])." and konfirmasi = ''";
		$query = $this->db->query($query);
		return $query->result();
	}
	public function kendaraan_konfirmasi(){
		date_default_timezone_set("Asia/Bangkok");
		$tgl=date("Y-m-d");
		$query = "
		update kendaraan 
		inner join pembayaran on kendaraan.id_kendaraan=pembayaran.id_kendaraan and konfirmasi=''
		set kendaraan.tgl_aktif_dari=case when kendaraan.tgl_aktif_sampai > ".$this->post($tgl)." then kendaraan.tgl_aktif_dari else 
		pembayaran.tgl_aktif_dari end, kendaraan.tgl_aktif_sampai=pembayaran.tgl_aktif_sampai
		where kendaraan.id_kendaraan = ".$this->post($_POST['id_kendaraan'])." and konfirmasi = ''
		";

		$query = $this->db->query($query);

		$query = "
			update pembayaran set tgl_konfirmasi = ".$this->post($tgl).", konfirmasi = ".$this->post($_SESSION['id_account'])."
			where id_kendaraan = ".$this->post($_POST['id_kendaraan'])." and konfirmasi = '' and gambar<>''";
		$query = $this->db->query($query);

		return TRUE;
	}
	
	
	//TRANSAKSI
	public function transaksi(){
		$query = "
			select transaksi.*, account.nama_lengkap nama_staff
			from transaksi 
			left join account on account.id_account=transaksi.id_accountstaff
			where flag='1' ";
		$query = $this->db->query($query);
		return $query->result();
	}
	public function all_lantai(){
		$query = "select nama from custom where id_kategori='C0002'";
		$query = $this->db->query($query);
		return $query->result();		
	}	
	public function transaksi_baru(){
		date_default_timezone_set("Asia/Bangkok");
		$tgl=date("Y-m-d H:i:s");
		$temp = getdate();
		$kode = substr($temp['year'],2,2).str_pad($temp['mon'],2,"0",STR_PAD_LEFT);		

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
		if($_POST['lokasi'] == ''){
			foreach($byk as $key=>$value){
				if(isset(array_count_values($value)[0])){
					$_POST['lokasi']=$key;
					break;
				}
			}
		}
		if($_POST['lokasi'] == ''){
			return false;
		}
		$query = "
			select concat('".$kode."',lpad(ifnull(max(mid(id_transaksi,5,length(id_transaksi)-1)),0)+1,6,0)) indeks 
			from transaksi where substring(id_transaksi,1,4) = '".$kode."'";
		$query = $this->db->query($query);
		$row = $query->result();

		$monitoring=$this->monitoring($_POST['lokasi']);
		
		if($_POST['lokasi']=='1'){
			$digit = check3($monitoring[0]->value,6,6);
			$posisi = "LT.".$_POST['lokasi']."-Blok A-".array_search(0,$digit);
		}else{
			$digit = check4($monitoring[0]->value,15,4);
			$posisi = "LT.".$_POST['lokasi']."-Blok B-".array_search(0,$digit);
		}

		$query = "
			INSERT INTO `transaksi`(`id_transaksi`, `tgl_masuk`, `jenis`, `lokasi`, `flag`) VALUES 
			(".$this->post($row[0]->indeks).",".$this->post($tgl).",'mobil',".$this->post($posisi).",'1')
			";
		$query = $this->db->query($query);
		
		$query = "
			select *
			from transaksi
			where id_transaksi = ".$this->post($row[0]->indeks);
		$query = $this->db->query($query);
		return $query->result();
	}
	public function load_tiket($str){
		$query = "
			select transaksi.*, account.nama_lengkap nama_staff
			from transaksi 
			left join account on account.id_account=transaksi.id_accountstaff
			where transaksi.id_transaksi=?";
		$query = $this->db->query($query, array($str));
		if($query->num_rows()==1){
			return $query->result();			
		}else{
			return FALSE;
		}
	}
	public function transaksi_keluar($str){
		date_default_timezone_set("Asia/Bangkok");
		$tgl=date("Y-m-d H:i:s");		
		$tgl0=date("Y-m-d");		
		$query = "
			select id_transaksi,	id_accountstaff,	tgl_masuk,	tgl_keluar,	jenis,	plat,	lokasi,	flag, 
			CONCAT(
				TIMESTAMPDIFF(HOUR,tgl_masuk,'".$tgl."'),':',
				LPAD(FLOOR((TIMESTAMPDIFF(SECOND,tgl_masuk,'".$tgl."') - (TIMESTAMPDIFF(HOUR,tgl_masuk,'".$tgl."') * 3600))/60), 2, 0),':',
				LPAD(TIMESTAMPDIFF(SECOND,tgl_masuk,'".$tgl."')-TIMESTAMPDIFF(MINUTE,tgl_masuk,'".$tgl."')*60,2,0)
			) lama,
			nilai,
            case 
            	when (select tgl_aktif_sampai from kendaraan where plat=".$this->post($_POST['plat']).")>=".$this->post($tgl0)." then 
					(ceil(TIMESTAMPDIFF(SECOND,tgl_masuk,'".$tgl."')/3600)*nilai)*0.3
                else 
					(ceil(TIMESTAMPDIFF(SECOND,tgl_masuk,'".$tgl."')/3600)*nilai)
                end nominal
			from transaksi 
			left join custom on id_kategori = 'C0001' and nama=jenis
			where flag='1' and id_transaksi= ".$this->post($str);
		$query = $this->db->query($query, array($str));
		if($query->num_rows()==1){
			return $query->result();			
		}else{
			return FALSE;
		}
	}
	public function check_transaksi_id(){
		$query = "select * from transaksi where flag='1' and id_transaksi = ".$this->post($_POST['id_transaksi']);
		$query = $this->db->query($query);
		if($query->num_rows()==1)
			return TRUE;
		else
			return FALSE;
	}
	public function transaki_bayar(){
		date_default_timezone_set("Asia/Bangkok");
		$tgl=date("Y-m-d H:i:s");
		
		$query = "
		update transaksi set 
			flag = '2', 
			tgl_keluar = '".$tgl."',
			lama = ".$this->post($_POST['lama']).",
			plat = ".$this->post($_POST['plat']).",
			id_accountstaff = ".$this->post($_SESSION['id_account']).",
			bayar = ".$this->post(str_replace(",","",$_POST['bayar']))." 
		where flag='1' and id_transaksi = ".$this->post($_POST['id_transaksi']);
		$query = $this->db->query($query);
		return TRUE;
	}
	public function ajax_transaksi_hapus(){
		$query = "delete from transaksi where id_transaksi = ".$this->post($_POST['id_transaksi']);
		$query = $this->db->query($query);
		return TRUE;
	}

	
	//REPORT
	public function report(){
		date_default_timezone_set("Asia/Bangkok");
		$tgl=date("Y-m-d");		
		if($_POST['jenis']=='transaksi'){
			$query = "
				SELECT transaksi.*, staff.nama_lengkap nama_staff, admin.nama_lengkap nama_approve
				FROM `transaksi`
				left join kendaraan on kendaraan.plat=transaksi.plat
				left join account staff on staff.id_account=transaksi.id_accountstaff	
				left join account admin on admin.id_account=transaksi.id_accountmanager	
				where 
					case when '".$_SESSION['level']."' = 'pelanggan' 
					THEN transaksi.plat in (select plat from kendaraan where id_account = '".$_SESSION['id_account']."')
					else TRUE
					end and 
					transaksi.tgl_masuk between ".$this->post(cetak_tgl($_POST['dari']).' 00:00:00')." and ".$this->post(cetak_tgl($_POST['sampai']).' 23:59:59');
		}else if($_POST['jenis']=='kendaraan'){
			$query = "
				select pembayaran.*, account.nama_lengkap
				from pembayaran
				left join account on account.id_account=pembayaran.konfirmasi
				where 
					case when '".$_SESSION['level']."' = 'pelanggan'
					then id_account = '".$_SESSION['id_account']."'
					else true end and 
					pembayaran.tgl_input between ".$this->post(cetak_tgl($_POST['dari']).' 00:00:00')." and ".$this->post(cetak_tgl($_POST['sampai']).' 23:59:59');
		}else if($_POST['jenis']=='member'){
			$query = "
				select kendaraan.*, account.nama_lengkap
				from kendaraan 
				left join account on account.id_account=kendaraan.id_account
				where tgl_aktif_sampai";
			if($_POST['tipe'] == 'aktif')
				$query .= " >= ".$tgl. " and tgl_aktif_dari <> '0000-00-00'";
			else if($_POST['tipe'] == 'tidak_aktif')
				$query .= " < ".$tgl . " or tgl_aktif_dari = '0000-00-00'";
		}
		$query = $this->db->query($query);
		return $query->result();		
	}
	public function ganti_password(){
		$query = "
			update account
			set password = ".$this->post(hash_password($_POST['pass_confirmation']))."
			where id_account = ".$this->post($_SESSION['id_account']);
		$query = $this->db->query($query);
		return true;
	}
	public function check_password($str){
		$query = "
			select case when password = ".$this->post(hash_password($str))." then 'true'
			else 'false' end nilai
			from account
			where id_account = ".$this->post($_SESSION['id_account']);
		$query = $this->db->query($query);
		return $query->result();		
	}
	public function approve(){
		date_default_timezone_set("Asia/Bangkok");
		$tgl=date("Y-m-d H:i:s");		
		$query = "
			update transaksi
			set tgl_approve='".$tgl."', id_accountmanager='".$_SESSION['id_account']."'
			where tgl_masuk between ".$this->post(cetak_tgl($_POST['dari']).' 00:00:00')." and ".$this->post(cetak_tgl($_POST['sampai']).' 23:59:59');
		$query = $this->db->query($query);
		return true;		
	}
	
	function post($str){
		return $this->db->escape($this->security->xss_clean(trim(strtolower($str))));
	}	
}
?>