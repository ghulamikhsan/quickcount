<?php
	
	namespace Energibangsa\Cepet\helpers;
	
	use App;
	use Cache;
	use Config;
	use DB;
	use Excel;
	use File;
	use Hash;
	use Log;
	use Mail;
	use PDF;
	use Request;
	use Route;
	use Session;
	use Storage;
	use Schema;
	use Validator;
	use Auth;
	
	class EB
	{

		#VIEW TEMPLATE
		public static function load($template = '', $view = '' , $view_data = array(), $view_add = array())
		{   
			$set  = $view_data;
			$data = array_merge($set, $view_add);
			$data['contents'] = view($view, $view_data);
			$data['menus']    = Request::session()->get('menus');

			return view($template, $data);
		}

		#GET MENU
		public static function getMenu($id_privilege){
			$menu_data = DB::table('menus')
				->distinct()->select('urutan','menus.id_menu','nama_menu as nama','link','ikon')
				->join('permissions','permissions.id_menu','=','menus.id_menu')
				->where('id_privilege',$id_privilege)
				->orderBy('urutan')->get();
			foreach($menu_data as $key => $mn){
				$from = DB::table('submenus')
					->select('menus.id_menu','submenus.id_sub_menu','submenus.nama_sub_menu as nama_sub','submenus.link as link_sub','submenus.urutan')
					->join('menus','menus.id_menu','=','submenus.id_menu')
					->whereRaw(DB::raw("nl_menus.id_menu=$mn->id_menu"));
				$sub_menu_data = DB::table(DB::raw("({$from->toSql()}) NL_SUB"))
					->select('sub.*')
					// ->mergeBindings($from->getQuery())
					->join('permissions', function ($join) {
						$join->on('permissions.id_menu', '=', 'sub.id_menu')
							->on('permissions.id_sub_menu','=','sub.id_sub_menu');
					})
					->where('permissions.id_privilege',$id_privilege)
					->orderBy('sub.urutan')->get();
				$menu_data[$key]->sub_menu = $sub_menu_data;
			}
			return $menu_data;
		}

		#GET ACCESS
		public static function getAccess($id_privilege){
			$menu = DB::table('permissions')
				->select('menus.link as link_menu','submenus.link as link_sub_menu')
				->leftJoin('submenus', 'submenus.id_sub_menu', '=', 'permissions.id_sub_menu')
				->join('menus', 'menus.id_menu', '=', 'permissions.id_menu')
				->orderBy('menus.id_menu','asc')
				->where('id_privilege', $id_privilege)->get();
			$access_menu     = array_filter(array_column($menu->toArray(),'link_menu'));
			$access_sub_menu = array_filter(array_column($menu->toArray(),'link_sub_menu'));
			$access = array_merge($access_menu,$access_sub_menu);
			$forbidden  = DB::table('menus')
				->select('submenus.link as link_sub_menu','menus.link as link_menu')
				->leftJoin('submenus', 'submenus.id_menu', '=', 'menus.id_menu')
				->whereNotIn('menus.link', $access)
				->orWhere(function ($query) use($access){
					$query->whereNotIn('submenus.link', $access);
				})->get();
			$sec_menu     = array_filter(array_column($forbidden->toArray(),'link_menu'));
			$sec_sub_menu = array_filter(array_column($forbidden->toArray(),'link_sub_menu'));
			$sec = array_merge($sec_menu,$sec_sub_menu);
			return array('access_list' => $access, 'forbidden_list' => $sec);
		}


		#PRINT DEBUG
		public static function print($data, $pre = TRUE){
			echo "<pre>";
			print_r($data);
			echo "</pre>";
		}
		
		#ROUTING
        public static function routeController($prefix, $controller, $namespace = null)
		{
			
			$prefix = trim($prefix, '/') . '/';
			
			$namespace = ($namespace) ?: 'App\Http\Controllers';
			
			try {
				Route::get($prefix, ['uses' => $controller . '@getIndex', 'as' => $controller . 'GetIndex']);
				
				$controller_class = new \ReflectionClass($namespace . '\\' . $controller);
				$controller_methods = $controller_class->getMethods(\ReflectionMethod::IS_PUBLIC);
				$wildcards = '/{one?}/{two?}/{three?}/{four?}/{five?}';
				foreach ($controller_methods as $method) {
					if ($method->class != 'Illuminate\Routing\Controller' && $method->name != 'getIndex') {
						if (substr($method->name, 0, 3) == 'get') {
							$method_name = substr($method->name, 3);
							$slug = array_filter(preg_split('/(?=[A-Z])/', $method_name));
							$slug = strtolower(implode('-', $slug));
							$slug = ($slug == 'index') ? '' : $slug;
							Route::get($prefix . $slug . $wildcards, ['uses' => $controller . '@' . $method->name, 'as' => $controller . 'Get' . $method_name]);
						} elseif (substr($method->name, 0, 4) == 'post') {
							$method_name = substr($method->name, 4);
							$slug = array_filter(preg_split('/(?=[A-Z])/', $method_name));
							Route::post($prefix . strtolower(implode('-', $slug)) . $wildcards, [
								'uses' => $controller . '@' . $method->name,
								'as' => $controller . 'Post' . $method_name,
							]);
						}
					}
				}
			} catch (\Exception $e) {
			
			}
		}

		#TO BASE64
		public static function toBase64($path){
			$type = pathinfo($path, PATHINFO_EXTENSION);
			if($type){
				$aa = file_get_contents($path);
				$base64 = 'data:image/' . $type . ';base64,' . base64_encode($aa);
				return $base64;
			}else{
				return null;
			}
		}

		#BASE URL
		public static function baseUrl($path=''){
			// return 'http://localhost:8000/neraca/public/' . $path;
			return url($path);
		}
		
		#TEMPLATE URL
		public static function templateUrl($path=''){
			// return 'http://localhost:8000/neraca/public/assets/template/' . $path;
			return asset('assets/template/'.$path);
		}

		#CUSTOM URL
		public static function customUrl($path=''){
			// return 'http://localhost:8000/neraca/public/assets/custom/' . $path;
			return asset('assets/custom'.$path);
		}

		#VALIDASI
		public static function Validator($data = [])
		{
			
			$validator = Validator::make(Request::all(), $data);
			if ($validator->fails()) {
				$result = array();
				$message = $validator->errors();
				$result['api_status'] = 0;
				$result['api_code'] = 401;
				$result['api_message'] = $message;
				// $result['api_message'] = $message->all(':message')[0];
				$res = response()->json($result);
				return $res->send();
			}
		}
		
		#REQUEST & VALIDASI
		public static function Input($name = null, $rule = []){
			$rule =  array($name => $rule);
			$validator = Validator::make(Request::all(), $rule);
			if ($validator->fails()) {
				$result = array();
				$message = $validator->errors();
				$result['api_status'] = 0;
				$result['api_code'] = 401;
				$result['api_message'] = $message;
				// $result['api_message'] = $message->all(':message')[0];
				$res = response()->json($result);
				$res->send();
				exit;
			}
		}

		public static function insert($table, $save){
			// jika memiliki column created_at
			if (Schema::hasColumn($table, 'created_at')) {
				if (isset($save[0]) && is_array($save[0])) {
					foreach ($save as $key => $value) {
						$save[$key]['created_at']	= new \DateTime();	
					}
				} else {
					$save['created_at']	= new \DateTime();
				}
			}

			$result = DB::table($table)->insert($save);
			if($result){
				self::createLog('CREATE_TB '.$table);
				return $result;				;
			}
			return false;
		}

		public static function insertID($table, $save){
			// jika memiliki column created_at
			if (Schema::hasColumn($table, 'created_at')) {
				if (isset($save[0]) && is_array($save[0])) {
					foreach ($save as $key => $value) {
						$save[$key]['created_at']	= new \DateTime();	
					}
				} else {
					$save['created_at']	= new \DateTime();
				}
			}

			$result = DB::table($table)->insertGetId($save);
			if($result){
				self::createLog('CREATE_TB '.$table);
				return $result;
			}
			return false;
		}

		public static function delete($note, $data){
			$temp = json_encode($data->first());
			if (Schema::hasColumn($note, 'deleted_at')) {
				$result = $data->update([
					'deleted_at' => new \DateTime()
				]);
				$note .= " add deleted_at";
			} else {
				$result = $data->delete();
			}

			if($result){
				self::createLog('DELETE_TB '.$note, $temp);
				return true;
			}
			return $result;
		}

		public static function permanentDelete($note, $data){
			$temp = json_encode($data->first());
			$result = $data->delete();

			if($result){
				self::createLog('DELETE_PERMANENT_TB '.$note, $temp);
				return true;
			}
			return $result;
		}

		public static function restore($note, $parameter){
			$temp = DB::table($note)->where($parameter)->first();
			$temp = json_encode($temp);
			$result = DB::table($note)->where($parameter)->update([
				'deleted_at' => NULL,
			]);

			if($result){
				self::createLog('RESTORE_TB '.$note, $temp);
				return true;
			}
			return $result;
		}

		public static function update($table, $save, $parameter){
			$temp = DB::table($table)->where($parameter)->first();
			$temp = json_encode($temp);
			if (Schema::hasColumn($table, 'updated_at')) {
				$save['updated_at']	= new \DateTime();
			}
			$result = DB::table($table)->where($parameter)->update($save);
			if($result){
				self::createLog('UPDATE_TB '.$table, $temp);
				return true;
			}
			return false;
		}

		public static function createLog($errors, $note = null,  $type = 'info'){
			$ip      = Request::ip();
			$input   = json_encode(Request::input());
			$url     = Request::url();
			$message = is_array($errors) ? json_encode($errors) : $errors;
			$user    = Auth::user() ? Auth::user()->username : '';
			$method  = Request::getMethod();
			$text    = "[IP: ". $ip. "] [USER: ".$user."] [URL: ".$url."] [METHOD: ".$method."] [PARAMETER: ".$input."] [MESSAGE: ".$message."] [KETERANGAN: ".$note."]";

			switch ($type) {
				case 'info':
					Log::info($text);
					break;

				case 'emergency':
					Log::emergency($text);
					break;
					
				case 'alert':
					Log::alert($text);
					break;

				case 'critical':
					Log::critical($text);
					break;

				case 'error':
					Log::error($text);
				break;

				case 'warning':
					Log::warning($text);
					break;

				case 'notice':
					Log::notice($text);
					break;
					
				case 'debug':
					Log::debug($text);
					break;
				default:
					# code...
					break;
			}
		}

		/**
		 * DATE 
		 * example: 2020-03-17 12:00:00
		 */
		#Selasa, 17 Maret 2019
		public static function getFullDate($date){
			date_default_timezone_set('Asia/Jakarta');
            $tanggal = self::getTanggal($date);
            $bulan   = self::bulan(self::getBulan($date));
            $tahun   = self::getTahun($date);
            return self::hari($tanggal) .', '.$tanggal.' '.$bulan.' '.$tahun;  
		}

		public static function dateFormat($date, $format = 'Y-m-d H:i:s'){
			return date($format, strtotime($date));
		}

		public static function getTanggal($date){
			return substr($date,8,2);
		}

		public static function getBulan($date){
			return substr($date,5,2);
		}
		
		public static function getTahun($date){
			return substr($date,0,4);
		}

		public static function getHour($date){
			return substr($date, 11,5);
		}

		public static function hari($date){
			$hari = date('D', strtotime($date));
			switch ($hari) {
				case 'Sun':
					return 'Minggu';
					break;
				case 'Mon':
					return 'Senin';
					break;
				case 'Tue':
					return 'Selasa';
					break;
				case 'Wed':
					return 'Rabu';
					break;
				case 'Thu':
					return 'Kamis';
					break;
				case 'Fri':
					return 'Jumat';
					break;
				case 'Sat':
					return 'Sabtu';
					break;
			}
		}

		public static function bulan($bln){
			switch ($bln){
				case 1: 
					return "Januari";
					break;
				case 2:
					return "Februari";
					break;
				case 3:
					return "Maret";
					break;
				case 4:
					return "April";
					break;
				case 5:
					return "Mei";
					break;
				case 6:
					return "Juni";
					break;
				case 7:
					return "Juli";
					break;
				case 8:
					return "Agustus";
					break;
				case 9:
					return "September";
					break;
				case 10:
					return "Oktober";
					break;
				case 11:
					return "November";
					break;
				case 12:
					return "Desember";
					break;
			}
		} 

		//  by Dimas
		public static function getById($table, $id, $field = "")
		{
			$query = DB::table($table);
			return $field != "" ? $query->where($field, '=', $id) : $query->where('id', '=', $id);
		}

		public static function getFirstUri()
		{
			$url = explode('/', url()->current());
			array_pop($url);
			$url = implode('/', $url);
			return $url;
		}

		public static function numberToAlphabet($number)
		{
			$alphabet = array( 'a', 'b', 'c', 'd', 'e',
                       'f', 'g', 'h', 'i', 'j',
                       'k', 'l', 'm', 'n', 'o',
                       'p', 'q', 'r', 's', 't',
                       'u', 'v', 'w', 'x', 'y',
                       'z'
					   );
			return $alphabet[$number-1];
		}

		public static function invoiceNumber($type)
		{
			// format abc 17 03 2020 001

			$type = explode(' ', $type);
			$type = count($type) == 1 ? substr($type[0],0,3) : substr($type[0],0,1) . substr($type[1],0,2);
			$type = strtoupper(substr($type,0,3));
			$date = date('dmY');
			$counter = DB::table('transactions')->count() % 999;
			$counter = sprintf("%03s", $counter);

			return $type.$date.$counter;
		}

		/**
		 * uploadFile
		 *
		 * @param  string $name name of request file
		 * @param  string $upload_path path to save
		 * @param  string $filename name of file
		 * @param  array $config config file (allowed_type, max_size, required, max_size)
		 * @return void
		 */
		public static function uploadFile($name, $upload_path, $filename = null, $config = array() ){
			$valid = true;
			$msg   = 'success';
			$file  = Request::file($name);
			
			// Add Config when null
			if(!array_key_exists('allowed_type',$config))
				$config['allowed_type'] = 'jpg';
			if(!array_key_exists('max_size',$config))
				$config['max_size'] = '1024';
			if(!array_key_exists('required',$config))
				$config['required'] = false;

			if($config['required'] && !$file){
				$response['status'] = 0;
				$response['message'] = $name . ' Wajib di isi.';
				return $response;
			}

			if(!in_array(strtolower($file->extension()),explode('|', $config['allowed_type']))){
				$valid = false;
				$msg   = 'Upload Gagal, Extension '.strtolower($file->extension()).' Extension yang di ijinkan hanya '.  $config['allowed_type'];
			}

			if($file->getSize() > ($config['max_size'] * 1000)){
				$valid = false;
				$msg   = 'Upload Gagal, Maksimal size '. $config['max_size'] .' KB';
			}

			if($valid){
				if($filename){
					$result = $file->storeAs(
						$upload_path, $filename . '.' .$file->extension()
					);
				}else{
					$result = $file->store($upload_path);
				}
				$response['status'] = 1;
				$response['message'] = 'success';
				$response['filename'] = $result;
			}else{
				$response['status'] = 0;
				$response['message'] = $msg;
			}

			return $response;
		}

		public static function getImage($url)
		{
			if (filter_var($url, FILTER_VALIDATE_URL)) {
				return $url;
			}

			return $url ? url("/storage/".$url) : null;
		}

		public static function getUrl($url)
		{
			if (filter_var($url, FILTER_VALIDATE_URL)) {
				return $url;
			}
			
			return $url ? url("/storage/".$url) : null;
		}
    }