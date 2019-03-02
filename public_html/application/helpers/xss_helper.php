<?php
	function cetak($str){
		return htmlentities(ucwords($str), ENT_QUOTES, 'UTF-8');
	}
	function cetak_tgl($str){
		$temp = explode('-',$str);
		return $temp[2].'-'.$temp[1].'-'.$temp[0];
	}
	function hash_password($password){
		$salt = "security";
		$hash = hash('sha512',$salt.$password);
		return $hash;
	}
	function cetak_tglwkt($str){
		if($str=='0000-00-00 00:00:00'){
			return '';
		}else{
			$temp0 = explode(' ',$str);
			$temp = explode('-',$temp0[0]);
			return $temp[2].'-'.$temp[1].'-'.$temp[0].' '.$temp0[1];
		}
	}
	function check3($nilai, $batas, $rapat) {
		$batas2 = $batas;
		$rapat2 = $rapat;
		$nilai = json_decode($nilai, true);
		//3 mobil
		$min = array(0, 74, 110);
		$max = array(73, 109, 166);
		$mobil = array(0, 0, 0);
		$check = array();
		$hitung = 0;
		$i = 0;

		foreach($nilai as $key=>$value){
			if($key=='90'){
				if($value>5){
					$hitung = 0;
					$i++;
				}
			}
			if($key=='34'){
				if($value>$batas){
					$hitung=0;
					$i++;
				}
			}
			if($i==2){
				$batas = $batas2-1;
				$rapat = $rapat2+1;
			}else{
				$batas = $batas2;
				$rapat = $rapat2;
			}
			if($max[$i]<$key){
				if($hitung>=$rapat){
					$mobil[$i]=1;
				}
				$i++;
				$hitung=0;
			}
			if($min[$i]<=$key && $max[$i]>=$key){
				if($value <= $batas){
					$temp = array(
						'derajat' => $key,
						'nilai' => $value
					);
					$check[$i][]=$temp;
					$hitung++;
				}
			}
			
			if($max[count($mobil)-1]==$key){
				if($hitung>=$rapat){
					$mobil[$i]=1;
				}
				$hitung=0;
			}			
			
		}
		// pre($nilai); 
		// pre($check);
		$CI =& get_instance();
		$temp = $CI->db1->temp();
		foreach($temp as $key=>$value){
			$posisi = explode("-",str_replace("lt.","",$value->lokasi));
			if($posisi[0]=='1'){
				$mobil[$posisi[2]]=1;
			}			
		}
		return $mobil;
	}
	function check4($nilai, $batas, $rapat) {
		$batas2 = $batas;
		$rapat2 = $rapat;
		$nilai = json_decode($nilai, true);
		$min = array( 0, 46,  91, 136);
		$max = array(45, 90, 135, 166);
		$mobil = array(0, 0, 0, 0);
		$check = array();
		$hitung = 0;
		$i = 0;

		foreach($nilai as $key=>$value){
			if($max[$i]<$key){
				if($hitung>=$rapat){
					$mobil[$i]=1;
				}
				$i++;
				$hitung=0;
			}
			if($i==0 || $i==3){
				$batas = $batas2-8;				
			}else{
				$batas = $batas2;
			}
			if($i==1 || $i==0){
				$rapat = $rapat2-1;
			}else if($i==3){
				$rapat = $rapat2+1;
			}else{
				$rapat = $rapat2;
			}
			if($min[$i]<=$key && $max[$i]>=$key){
				if($value <= $batas){
					$temp = array(
						'derajat' => $key,
						'nilai' => $value
					);
					$check[$i][]=$temp;
					$hitung++;
				}
			}
			if($max[count($mobil)-1]==$key){
				if($hitung>=$rapat){
					$mobil[$i]=1;
				}
				$hitung=0;
			}			
		}
		// pre($nilai);
		// pre($check);
		$CI =& get_instance();
		$temp = $CI->db1->temp();
		foreach($temp as $key=>$value){
			$posisi = explode("-",str_replace("lt.","",$value->lokasi));
			if($posisi[0]=='2'){
				$mobil[$posisi[2]]=1;
			}
		}
		return $mobil;
	}
	function pre($txt){
		echo '<pre>';
		print_r($txt);
		echo '</pre>';	
	}
