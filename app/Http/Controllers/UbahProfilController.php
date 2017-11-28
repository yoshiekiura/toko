<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use App\Role;
use App\Komunitas;
use App\KomunitasPenggiat;
use App\KomunitasCustomer;
use Jenssegers\Agent\Agent;
use App\Customer;
use App\UserWarung; 
use App\LokasiPelanggan; 
use App\BankWarung;
use App\BankKomunitas;
use Session;
use App\KeranjangBelanja;
use Intervention\Image\ImageManagerStatic as Image;
use File;
use Indonesia;

class UbahProfilController extends Controller
{   
//UBAH PROFIL USER PELANGGAN
	public function ubah_profil_pelanggan() {
    	//PILIH USER -> LOGIN
		$user = Auth::user();
		//FOTO WARMART
		$logo_warmart = "".asset('/assets/img/examples/warmart_logo.png')."";
        //PELANGGAN, WARUNG, KOMUNITAS
		$pelanggan = Customer::select(['id','email','password','name', 'alamat', 'wilayah', 'no_telp','tgl_lahir','tipe_user', 'status_konfirmasi'])->where('id', $user->id)->first();
		$komunitas_pelanggan = KomunitasCustomer::where('user_id',$user->id)->first();

		//DATA LOKASI PELANGGAN
		$lokasi_pelanggan = LokasiPelanggan::where('id_pelanggan',$user->id)->first();
		//select provinsi
		$provinsi = Indonesia::allProvinces()->pluck('name','id');
		//select provinsi
		//select kabupaten
		$kabupaten = Indonesia::allCities()->pluck('name','id');
		//select kabupaten
		//select kecamatan
		$kecamatan = Indonesia::allDistricts()->pluck('name','id');
		//select kecamatan
		//select kelurahan
		$kelurahan = Indonesia::allVillages()->pluck('name','id');
		//select kelurahan

		$keranjang_belanjaan = KeranjangBelanja::with(['produk','pelanggan'])->where('id_pelanggan',Auth::user()->id)->get();
		$cek_belanjaan = $keranjang_belanjaan->count();  
		return view('ubah_profil.ubah_profil_pelanggan',['user' => $pelanggan, 'pelanggan' => $pelanggan, 'komunitas_pelanggan' => $komunitas_pelanggan, 'cek_belanjaan' => $cek_belanjaan, 'logo_warmart' => $logo_warmart,'lokasi_pelanggan'=>$lokasi_pelanggan,'provinsi'=>$provinsi,'kabupaten'=>$kabupaten,'kecamatan'=>$kecamatan,'kelurahan'=>$kelurahan]);
	}

//UBAH PROFIL USER PELANGGAN
	public function proses_ubah_profil_pelanggan(Request $request) {
		//VALIDASI
		$this->validate($request, [ 
			'name' 		=> 'required', 
			'no_telp' 	=> 'required|unique:users,no_telp,'.$request->id,
			'email' 	=> 'unique:users,email,'.$request->id, 
			'alamat' 	=> 'required',
		]);
		//UPDATE USER PELANGGAN
		Customer::find($request->id)->update([
			'name'              => $request->name,
			'email'             => $request->email, 
			'alamat'            => $request->alamat,
			'no_telp'           => $request->no_telp,
			'tgl_lahir'         => $request->tgl_lahir,
		]);

		//JIKA SEBELUMNYA SUDAH ADA DI KOMUNITAS
		if ($request['komunitas'] != "") {
			//HAPUS KOMUNITAS LAMA
			KomunitasCustomer::where('user_id',$request->id)->delete();
			LokasiPelanggan::where('id_pelanggan',$request->id)->delete();

			//INSERT KOMUNITAS BARU
			if (isset($request['komunitas'])) {
				KomunitasCustomer::create(['user_id' =>$request->id ,'komunitas_id' => $request['komunitas']]);

						//UPDATE USER PELANGGAN
				LokasiPelanggan::create(['id_pelanggan' =>$request->id ,'provinsi' => $request['provinsi'],'kabupaten' => $request['kabupaten'],'kecamatan' => $request['kecamatan'],'kelurahan' => $request['kelurahan']]);

			}
		}

		return redirect()->route('daftar_produk.index');
	}	

//UBAH PROFIL USER WARUNG
	public function ubah_profil_warung() {
    	//PILIH USER -> LOGIN
		$user = Auth::user(); 
		$user_warung = UserWarung::with(['kelurahan'])->find($user->id);

		if ($user_warung->id_warung != Auth::user()->id_warung) {
			Auth::logout();
			return response()->view('error.403');
		}
		else{
			return view('ubah_profil.ubah_profil_warung')->with(compact('user_warung','user'));
		}
	}

//UBAH PROFIL USER WARUNG
	public function proses_ubah_profil_warung(Request $request) {
		$user_warung = UserWarung::find(Auth::user()->id);
		if ($user_warung->id_warung != Auth::user()->id_warung) {
			Auth::logout();
			return response()->view('error.403');
		}
		else{
		//VALIDASI
			$this->validate($request, [
				'name'      => 'required',
				'alamat'    => 'required',
				'kelurahan' => 'required', 
				'email'     => 'required|without_spaces|unique:users,email,'.$request->id,
				'no_telp'   => 'required|without_spaces|unique:users,no_telp,'.$request->id,
				'foto_ktp'  => 'image|max:3072'
			]);

         //UPDATE USER WARUNG
			$user_warung->update([
				'name'      => $request->name,
				'email'     => $request->email, 
				'no_telp'   => $request->no_telp, 
				'alamat'    => $request->alamat,
				'wilayah'   => $request->kelurahan, 
			]);

		//UPDATE FOTO KTP
			if ($request->hasFile('foto_ktp')) {
				$foto_ktp = $request->file('foto_ktp');

			// Mengambil file yang diupload
				$uploaded_foto = $foto_ktp;
			// mengambil extension file
				$extension = $uploaded_foto->getClientOriginalExtension();

			// membuat nama file random berikut extension
				$filename = str_random(40) . '.' . $extension;
				$image_resize = Image::make($foto_ktp->getRealPath());        
				$image_resize->fit(300);
				$image_resize->save(public_path('foto_ktp_user/' .$filename));

			// hapus foto ktp lama, jika ada
				if ($user_warung->foto_ktp) {
					$old_foto = $user_warung->foto_ktp;
					$filepath = public_path() . DIRECTORY_SEPARATOR . 'foto_ktp_user'
					. DIRECTORY_SEPARATOR . $user_warung->foto_ktp;
					try {
						File::delete($filepath);
					}
					catch (FileNotFoundException $e) {
					// Foto sudah dihapus/tidak ada
					}
				}
				$user_warung->foto_ktp = $filename;

				$user_warung->save();
			}

			$pesan_alert = 
			'<div class="container-fluid">
			<div class="alert-icon">
			<i class="material-icons">check</i>
			</div>
			<b>Sukses : Berhasil Merubah Profil "'.$user_warung->name.'"</b>
			</div>';

			Session::flash("flash_notification", [
				"level"     => "success",
				"message"   => $pesan_alert
			]);

			return redirect()->back();
		}
	}	 

//UBAH PROFIL USER KOMUNITAS
	public function ubah_profil_komunitas() {
    	//PILIH USER -> LOGIN
		$user = Auth::user(); 
		$komunitas = Komunitas::with(['kelurahan','warung','komunitas_penggiat','bank_komunitas'])->find($user->id); 

		return view('ubah_profil.ubah_profil_komunitas')->with(compact('user','komunitas')); 
	}

//UBAH PROFIL USER PELANGGAN
	public function proses_ubah_profil_komunitas(Request $request) {

        //end masukan data bank komunitas
		//VALIDASI 
		$this->validate($request, [
			'email'     => 'required|without_spaces|unique:users,email,'. $request->id,
			'name'      => 'required|unique:users,name,'. $request->id,
			'alamat'    => 'required',
			'kelurahan' => 'required',
			'no_telp'   => 'required|without_spaces|unique:users,no_telp,'. $request->id,
			'nama_bank' => 'required',
			'no_rekening' => 'required',
			'an_rekening' => 'required',
			'id_warung' => 'required',
		]);

         //insert
		$komunitas = Komunitas::where('id',$request->id)->update([
			'email' =>$request->email,
			'name' =>$request->name,
			'alamat' =>$request->alamat,
			'wilayah' =>$request->kelurahan,
			'no_telp' =>$request->no_telp,
			'id_warung' =>$request->id_warung,
		]);

		$cek_komunitas_penggiat = KomunitasPenggiat::where('komunitas_id',$request->id)->count(); 

         //masukan data penggiat komunitas
		if ($cek_komunitas_penggiat == 0) {
			$komunitaspenggiat = KomunitasPenggiat::create([
				'nama_penggiat' =>$request->name_penggiat,
				'alamat_penggiat'  =>$request->alamat_penggiat,
				'komunitas_id'=>$request->id 
			]);
		}else{
			if ($request->name_penggiat != "" AND $request->alamat_penggiat != ""){
				$komunitaspenggiat = KomunitasPenggiat::where('komunitas_id',$request->id)->update([
					'nama_penggiat' =>$request->name_penggiat,
					'alamat_penggiat'  =>$request->alamat_penggiat
				]);
			} 
		} 

		$cek_bank_komunitas = BankKomunitas::where('komunitas_id',$request->id)->count(); 
         //masukan data bank komunitas 
		if ($cek_bank_komunitas == 0) {
			$bankkomunitas = BankKomunitas::create([
				'nama_bank' =>$request->nama_bank,
				'no_rek'    =>$request->no_rekening,
				'atas_nama' =>$request->an_rekening ,
				'komunitas_id'=>$request->id              
			]);  
		}else{
			if ($request->nama_bank != "" AND $request->no_rekening != "" AND $request->an_rekening != "" ){
				$bankkomunitas = BankKomunitas::where('komunitas_id',$request->id)->update([
					'nama_bank' =>$request->nama_bank,
					'no_rek'    =>$request->no_rekening,
					'atas_nama' =>$request->an_rekening              
				]);
			} 

		}

		Session::flash("flash_notification", [
			"level"     => "success",
			"message"   => "Profil Berhasil Di Ubah"
		]);

		return redirect()->back();
	}	 

//UBAH PROFIL USER ADMIN
	public function ubah_profil_admin() {
    	//PILIH USER -> LOGIN
		$user = Auth::user(); 
		$user_admin = UserWarung::find($user->id);
		$user_admin['nama'] = $user_admin->name;
		return $user_admin;
	}

//UBAH PROFIL USER ADMIN
	public function proses_ubah_profil_admin(Request $request) {
		//VALIDASI
		$this->validate($request, [
			'nama'      => 'required',
			'email'     => 'required|without_spaces|unique:users,email,'.$request->id,
			'no_telp'   => 'required|without_spaces|unique:users,no_telp,'.$request->id,
			'alamat'    => 'required',

		]);

         //UPDATE USER ADMIN
		$user_warung = User::where('id',$request->id)->update([
			'name'      => $request->nama,
			'email'     => $request->email, 
			'no_telp'     => $request->no_telp, 
			'alamat'    => $request->alamat
		]);


	}


	//CARI WILAYAH 
	public function cek_kabupaten(Request $request) 
  	{	
  		# Tarik ID_wilayah & tipe_wilayah
  		$id_wilayah = $request->id;
  		$type_wilayah = $request->type;

  		# Inisialisasi variabel berdasarkan masing-masing tabel dari model
  		# dimana ID target sama dengan ID inputan
  		$kabupaten = Indonesia::allCities()->where('province_id', $id_wilayah);
  		$kecamatan = Indonesia::allDistricts()->where('city_id', $id_wilayah);
  		$kelurahan = Indonesia::allVillages()->where('district_id', $id_wilayah);

  		# Buat pilihan "Switch Case" berdasarkan variabel "type" dari form
  		switch($type_wilayah):
  			# untuk kasus "kabupaten"
  			case 'kabupaten':
  				  		$return = '<option value="">--PILIH KABUPATEN--</option>';
  						# lakukan perulangan untuk tabel kabupaten lalu kirim
  						foreach($kabupaten as $kabupatens){
  						# isi nilai return
  						$return .= "<option value='$kabupatens->id'>$kabupatens->name</option>";
  						# kirim
  						} 
  					return $return;
  			break;
  			# untuk kasus "kecamatan"
  			case 'kecamatan':
  				$return = '<option value="">--PILIH KECAMATAN--</option>';
  				foreach($kecamatan as $kecamatans){
  						# isi nilai return
  						$return .= "<option value='$kecamatans->id'>$kecamatans->name</option>";
  						# kirim
  						} 
  				return $return;
  			break;
  			# untuk kasus "kelurahan"
  			case 'kelurahan':
  				$return = '<option value="">--PILIH KELURAHAN--</option>';
  				foreach($kelurahan as $kelurahans) {
  					$return .= "<option value='$kelurahans->id'>$kelurahans->name</option>";
  					}
  				return $return;
  			break;
  		# pilihan berakhir
  		endswitch;

  	}

}
