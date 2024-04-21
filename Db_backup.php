<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Db_backup extends CI_Controller {

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

	public function __construct() {
        parent::__construct();

		date_default_timezone_set('Asia/Karachi'); 

        $this->load->model(array(
            "mod_common","mod_backup"
        ));
        
    }

	public function index()
	{
		error_reporting(E_ALL);
	if ($this->input->post()) {

		
					///insert log///////////////
			$udata['created_datetime'] =date('Y-m-d H:i:s');
			$udata['created_by'] = $this->session->userdata('id');
			$udata['dt'] = $this->input->post('from_date');
			
			$table='tbl_db_log';
			$res = $this->mod_common->insert_into_table($table,$udata);

		
			//$data['backup_list'] = $this->mod_backup->backup($this->input->post());			
    		$this->EXPORT_TABLES("localhost","r7cxayum_ae","C#M1_FC7}?v0","r7cxayum_ae",$this->input->post('from_date')); 
		
		
		
		
		}
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "DataBase Backup";		
		$this->load->view($this->session->userdata('language')."/db_backup",$data);
	}

 

//EXPORT_TABLES("localhost","kashifmp_eshadbu","Esha@user@admin","kashifmp_esha_db" ); 
		//optional: 5th parameter - to backup specific tables only: array("mytable1","mytable2",...)   
		//optional: 6th parameter - backup filename
		// IMPORTANT NOTE for people who try to change strings in SQL FILE before importing, MUST READ:  goo.gl/2fZDQL
					
// https://github.com/tazotodua/useful-php-scripts  
function EXPORT_TABLES($host,$user,$pass,$name,$date,$tables=false, $backup_name=false){
$backup_name_zip='';	
	
	set_time_limit(3000); $mysqli = new mysqli($host,$user,$pass,$name); $mysqli->select_db($name); $mysqli->query("SET NAMES 'utf8'");
	$queryTables = $mysqli->query('SHOW TABLES'); while($row = $queryTables->fetch_row()) { $target_tables[] = $row[0]; }	if($tables !== false) { $target_tables = array_intersect( $target_tables, $tables); } 
	$content = "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\r\nSET time_zone = \"+00:00\";\r\n\r\n\r\n/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\r\n/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\r\n/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\r\n/*!40101 SET NAMES utf8 */;\r\n--\r\n-- Database: `".$name."`\r\n--\r\n\r\n\r\n";
	foreach($target_tables as $table){
		if (empty($table)){ continue; } 
		$result	= $mysqli->query('SELECT * FROM `'.$table.'`'); 
		$fields_amount=$result->field_count;  $rows_num=$mysqli->affected_rows; 
		$res = $mysqli->query('SHOW CREATE TABLE '.$table);	$TableMLine=$res->fetch_row(); 
		$content .= "\n\n".$TableMLine[1].";\n\n";   $TableMLine[1]=str_ireplace('CREATE TABLE `','CREATE TABLE IF NOT EXISTS `',$TableMLine[1]);
		for ($i = 0, $st_counter = 0; $i < $fields_amount;   $i++, $st_counter=0) {
			while($row = $result->fetch_row())	{ //when started (and every after 100 command cycle):
				if ($st_counter%100 == 0 || $st_counter == 0 )	{$content .= "\nINSERT INTO ".$table." VALUES";}
					$content .= "\n(";    for($j=0; $j<$fields_amount; $j++){ $row[$j] = str_replace("\n","\\n", addslashes($row[$j]) ); if (isset($row[$j])){$content .= '"'.$row[$j].'"' ;}  else{$content .= '""';}	   if ($j<($fields_amount-1)){$content.= ',';}   }        $content .=")";
				//every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
				if ( (($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num) {$content .= ";";} else {$content .= ",";}	$st_counter=$st_counter+1;
			}
		} $content .="\n\n\n";
	}
	$content .= "\r\n\r\n/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\r\n/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\r\n/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;";
	
	/// here is to encrypt the content
	$action='encrypt';
	$content =$this->encrypt_decrypt($action, $content);
	
	$backup_name = $backup_name ? $backup_name : $name.'___('.date('H-i-s').'_'.$date.').sql';
	$backup_name_zip = $backup_name_zip ? $backup_name_zip : 'mydb_('.date('H-i-s').'_'.$date.').zip';

$myfile = fopen($backup_name, "w") or die("Unable to open file!");
 
fwrite($myfile, $content);
fclose($myfile);
 
	// ob_get_clean(); header('Content-Type: application/octet-stream');  header("Content-Transfer-Encoding: Binary"); 
	// header('Content-Length: '. (function_exists('mb_strlen') ? mb_strlen($content, '8bit'): strlen($content)) );   
	// header("Content-disposition: attachment; filename=\"".$backup_name."\""); 
	$z = new ZipArchive();
	$z->open($backup_name_zip, ZIPARCHIVE::CREATE);
	 
	$z->addFile($backup_name);
	$z->close();
	//echo $content;
	
	header("Content-type: application/zip"); 
    header("Content-Disposition: attachment; filename=$backup_name_zip"); 
    header("Pragma: no-cache"); 
    header("Expires: 0"); 
    readfile("$backup_name_zip"); 
	
	unlink($backup_name);
	unlink($backup_name_zip);
	 exit;
}      //see import.php too



function encrypt_decrypt($action, $string) {
    $output = false;

    $encrypt_method = "AES-256-CBC";
    $secret_key = 'myseckey';
    $secret_iv = 'myseciv';

    // hash
    $key = hash('sha256', $secret_key);
    
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    }
    else if( $action == 'decrypt' ){
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }

    return $output;
}


}
