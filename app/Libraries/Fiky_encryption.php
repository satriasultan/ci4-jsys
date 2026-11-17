<?php
/**
 * Created by PhpStorm.
 *  * User: FIKY-PC
 *  * Date: 7/7/20, 2:48 PM
 *  * Last Modified: 7/7/20, 2:48 PM.
 *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  Copyright© 2020 .All rights reserved.
 *
 */

namespace App\Libraries;

class Fiky_encryption
{
    /** @var \CodeIgniter\Session\Session */
    public $session;

    /** @var \CodeIgniter\Database\BaseConnection */
    public $db;

    /** @var \CodeIgniter\Encryption\EncrypterInterface */
    public $encrypter;

    public function __construct()
    {
        //parent::__construct();
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect('default');
        $configx         = new \Config\Encryption();
        $configx->key    = 's3CrE3THerEx8713df232Jsdf3';
        $configx->driver = 'OpenSSL';
        $configx->blockSize = 16;
        $configx->digest = 'SHA256';
        $this->encrypter = \Config\Services::encrypter($configx);
        //$this->important_class = new Important_class();
    }


    function mysecreturl($p1,$encryptype){
        $config         = new \Config\Encryption();
        $config->key    = 's3CrE3THerEx8713df232Jsdf3';
        $config->driver = 'OpenSSL';
        $encrypter = \Config\Services::encrypter($config);

        if ($encryptype === 'enkript') {
            return bin2hex($encrypter->encrypt($p1));
        } else if ($encryptype === 'dekript') {
            return $encrypter->decrypt(hex2bin($p1));
        } else {
            return 'FAILED YOUR DATA CHIPER';
        }
    }

    function dekript($p1){
        $p1 = trim((string) $p1);

        // hex2bin() melempar warning bila panjang ganjil / bukan hex (PHP 8+)
        if ($p1 === '' || strlen($p1) % 2 !== 0 || ! ctype_xdigit($p1)) {
            return '';
        }

        return $this->encrypter->decrypt(hex2bin($p1));
    }


    /* SODIUM ENCRYPTION ACTIVATE SODIUM EXTENSION */

    /*for url*/
    function secretbox($string,$encodetrue) {
        $key = random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        if ($encodetrue === 'enkript'){
            return urlencode(sodium_crypto_secretbox( $string, $nonce, $key));
        } else if ($encodetrue === 'dekript') {
            return sodium_crypto_secretbox_open( urldecode($string), $nonce, $key);
        } else {
            return 'BAD CHIPER';
        }

    }
    function secret_box_dekript($string) {
        $key = random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        return sodium_crypto_secretbox_open( $string, $nonce, $key);
    }

    function crypto_auth($string){
        $key = random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES); // 256 bit
        return $mac = urlencode(sodium_crypto_auth($string, $key));
    }

    function crypto_verify($string,$pwnya){
        $key = random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES); // 256 bit
        return sodium_crypto_auth_verify(urldecode($pwnya), $string, $key) ? 1 : 0;
    }

    /* BLOWFISH ENKRIPSI
       mcrypt sudah dihapus sejak PHP 7.2 dan utf8_encode() sejak PHP 8.2.
       Implementasi memakai OpenSSL. Pada OpenSSL 3 Blowfish pindah ke legacy
       provider sehingga BF-ECB bisa tidak tersedia (default build PHP 8.5 Windows),
       dalam kondisi itu dipakai AES-256-ECB agar fungsi tetap berjalan (round-trip). */
    function blowfish_enkript($p1, $key = null) {
        $data = (string) $p1;
        $size = $this->bf_block_size();
        $rest = strlen($data) % $size;
        if ($rest !== 0) {
            $data .= str_repeat("\0", $size - $rest);
        }

        $jwb1 = openssl_encrypt($data, $this->bf_cipher(), $this->bf_key($key), OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);

        return $jwb1 === false ? '' : $jwb1;
    }

    function blowfish_dekript($p1, $key = null) {
        $jwb1 = openssl_decrypt((string) $p1, $this->bf_cipher(), $this->bf_key($key), OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);

        return $jwb1 === false ? '' : rtrim($jwb1, "\0");
    }

    private function bf_key($key = null) {
        if ($key === null || $key === '') {
            $key = defined('THISKEY') ? THISKEY : 'THIS_KEY_HAS_LIMIT';
        }

        return (string) $key;
    }

    private function bf_cipher() {
        static $cipher = null;
        if ($cipher === null) {
            $cipher = in_array('BF-ECB', openssl_get_cipher_methods(), true) ? 'BF-ECB' : 'AES-256-ECB';
        }

        return $cipher;
    }

    private function bf_block_size() {
        return $this->bf_cipher() === 'BF-ECB' ? 8 : 16;
    }

    function sealed($p1){
        $jwb1 = bin2hex(base64_encode(bin2hex($p1)));
        $dd = $jwb1;
        return $dd;
    }
    function unseal($_p1) {
        $p1 = trim((string) $_p1);

        // hex2bin()/base64_decode() harus divalidasi agar tidak muncul warning PHP 8+
        if ($p1 === '' || strlen($p1) % 2 !== 0 || ! ctype_xdigit($p1)) {
            return '';
        }

        $step1 = hex2bin($p1);
        $step2 = base64_decode((string) $step1);
        if ($step2 === '' || strlen($step2) % 2 !== 0 || ! ctype_xdigit($step2)) {
            return '';
        }

        $jwb1 = hex2bin($step2);

        return $jwb1 === false ? '' : $jwb1;
    }


    /*belum bisa*/


    function openssl_enkript($plaintext){
        $ivlen = openssl_cipher_iv_length(CHIPERKU);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $tag = 5;
        return $ciphertext = openssl_encrypt($plaintext, CHIPERKU, THISKEY,$options=0, $iv, $tag);
    }

    function openssl_dekript($chiper){
        $ivlen = openssl_cipher_iv_length(CHIPERKU);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $tag = 5;
        return $original_plaintext = openssl_decrypt($chiper, CHIPERKU, THISKEY, $options=0, $iv, $tag);
    }

    function EncryptDecrypt($oldText, $encryptIt, $key, $encryptType){
        $ciphers = openssl_get_cipher_methods();
        $foundEncType = false;
        for($pointer=0; $pointer<count($ciphers); $pointer=$pointer+1){
            if($ciphers[$pointer]==$encryptType){$foundEncType=true;}
        }
        if(!$foundEncType){
            $encryptType = CHIPERKU; // Default value used if not set or listed.
        }
        if($encryptIt){
            $newText = openssl_encrypt($oldText,$encryptType,$key);
        } else {
            $newText = openssl_decrypt($oldText,$encryptType,$key);
        }
        return $newText;
    }

    function sslPrm()
    {
        return array("your_password","IV (optional)","aes-128-cbc");
    }
    function sslEnc($msg)
    {
        list ($pass, $iv, $method)=$this->sslPrm();
        if(function_exists('openssl_encrypt'))
            return urlencode(openssl_encrypt(urlencode($msg), $method, $pass, false, $iv));
        else
            return urlencode(exec("echo \"".urlencode($msg)."\" | openssl enc -".urlencode($method)." -base64 -nosalt -K ".bin2hex($pass)." -iv ".bin2hex($iv)));
    }
    function sslDec($msg)
    {
        list ($pass, $iv, $method)=$this->sslPrm();
        if(function_exists('openssl_decrypt'))
            return trim(urldecode(openssl_decrypt(urldecode($msg), $method, $pass, false, $iv)));
        else
            return trim(urldecode(exec("echo \"".urldecode($msg)."\" | openssl enc -".$method." -d -base64 -nosalt -K ".bin2hex($pass)." -iv ".bin2hex($iv))));
    }

    function jDatatable($p1){
        return json_encode(array("dataTables" => $p1));
    }

    function getUserIP()
    {

        // Get real visitor IP behind CloudFlare network
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if(filter_var($client, FILTER_VALIDATE_IP))
        {
            $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP))
        {
            $ip = $forward;
        }
        else
        {
            $ip = $remote;
        }

        return $ip;
    }


    function constant($p1){

        $langx = $this->session->get('site_lang');

        if ($p1=='datatable_language') {
            return "
                        \"lengthMenu\": \"". 'Show _MENU_ data per page' ." \",
                        \"zeroRecords\": \"". 'Ooooops Sory Data Not Available' ." \",
                        \"info\": \"". 'Showing page _PAGE_ from _PAGES_ ' ." \",
                        \"infoEmpty\": \"". 'Ooooops Sory Data Empty '." \",
                        \"infoFiltered\": \"". 'Ooooops Sory Data Empty ' ." \",
                        \"loadingRecords\": \"". 'Processing load data... ' ." \",
                        \"processing\":     \"". 'Processing... ' ." \",
                        \"search\": \"". 'SEARCH ' ." \",
                        \"paginate\": {
                                        \"first\":      \"". 'First' ." \",
                                        \"last\":       \"". 'Last' ." \",
                                        \"next\":       \"". 'Next' ." \",
                                        \"previous\":   \"". 'Previous' ." \",
                                    },
                    ";
        } else {
            return 'NO OBJECT CONSTANTA';
        }

        /*
    if ($p1=='datatable_language') {
        return "
            \"lengthMenu\": \"Menampilkan _MENU_ data per halaman\",
            \"zeroRecords\": \"Ooooops Maaf Data Tidak Tersedia\",
            \"info\": \"Menampilkan halaman _PAGE_ dari _PAGES_\",
            \"infoEmpty\": \"Ooooops Maaf Data Tidak Tersedia\",
            \"infoFiltered\": \"(filter dari _MAX_ total data tersedia)\",
            \"loadingRecords\": \"Sedang load data...\",
            \"processing\":     \"Sedang Memproses...\",
            \"search\": \"Pencarian _INPUT_\",
            \"paginate\": {
                            \"first\":      \"Awal\",
                            \"last\":       \"Akhir\",
                            \"next\":       \"Lanjut\",
                            \"previous\":   \"Sebelumnya\",
                        },
        ";
    } else {
        return 'NO OBJECT CONSTANTA';
    } */

    }

    //function UniqueMachineID($salt = "GARAM") {
    function getMac(){
        $salt = "THIS_KEY_HAS_LIMIT";

        /* Hash mesin bersifat tetap per PC, jadi cukup dihitung sekali lalu disimpan.
           Tanpa cache, pembacaan ID disk dipanggil pada setiap refresh halaman. */
        static $cachedMac = null;
        if ($cachedMac !== null) {
            return $cachedMac;
        }

        $cacheFile = $this->machineIdCacheFile();
        $stored    = $this->readMachineIdCache($cacheFile);
        if ($stored !== null) {
            return $cachedMac = $stored;
        }

        $machineId = $this->readMachineId();
        $hash      = md5($salt.md5($machineId));

        if ($cacheFile !== null) {
            @file_put_contents($cacheFile, $hash, LOCK_EX);
        }

        return $cachedMac = $hash;
    }

    /* Lokasi cache machine id (writable/machine_id.cache) */
    private function machineIdCacheFile(){
        $cacheDir = defined('WRITEPATH') ? WRITEPATH : sys_get_temp_dir().DIRECTORY_SEPARATOR;

        return rtrim($cacheDir, '\\/').DIRECTORY_SEPARATOR.'machine_id.cache';
    }

    /* Cache hanya dipakai bila berisi md5 yang valid */
    private function readMachineIdCache($cacheFile){
        if ($cacheFile === null || ! is_file($cacheFile)) {
            return null;
        }

        $stored = trim((string) @file_get_contents($cacheFile));

        return preg_match('/^[0-9a-f]{32}$/', $stored) === 1 ? $stored : null;
    }

    /* ID mesin tanpa diskpart.
       diskpart butuh elevation dan memunculkan jendela console / teks
       "Microsoft DiskPart" pada halaman. Sebagai gantinya dipakai Disk GUID
       (nilai yang sama dengan "Disk ID" pada diskpart > detail disk) yang dibaca
       lewat WMI in-process (COM) sehingga tidak ada proses eksternal sama sekali. */
    private function readMachineId(){
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $diskId = $this->windowsDiskGuid();
            if ($diskId !== null) {
                return $diskId;
            }

            // cadangan: UUID produk (juga tanpa elevation)
            $uuid = $this->windowsProductUuid();
            if ($uuid !== null) {
                return $uuid;
            }
        } else {
            $uuid = $this->runHiddenCommand('blkid -o value -s UUID');
            if ($uuid !== null) {
                $lines = preg_split('/\r?\n/', $uuid);
                if (! empty($lines[0])) {
                    return trim($lines[0]);
                }
            }
        }

        // sama seperti perilaku lama: bila ID disk tidak terbaca dipakai nama PC
        $hostname = gethostname();

        return $hostname === false || $hostname === '' ? 'UNKNOWN-MACHINE' : $hostname;
    }

    private function windowsDiskGuid(){
        $guid = $this->wmiValue('root\\Microsoft\\Windows\\Storage', 'SELECT Guid FROM MSFT_Disk WHERE Number = 0', 'Guid');
        if ($guid === null) {
            $guid = $this->runHiddenCommand('powershell -NoProfile -NonInteractive -WindowStyle Hidden -ExecutionPolicy Bypass -Command "(Get-Disk -Number 0).Guid"');
        }

        return $this->normalizeUuid($guid);
    }

    private function windowsProductUuid(){
        $uuid = $this->wmiValue('root\\cimv2', 'SELECT UUID FROM Win32_ComputerSystemProduct', 'UUID');
        if ($uuid === null) {
            $uuid = $this->runHiddenCommand('powershell -NoProfile -NonInteractive -WindowStyle Hidden -ExecutionPolicy Bypass -Command "(Get-CimInstance -ClassName Win32_ComputerSystemProduct).UUID"');
        }

        $uuid = $this->normalizeUuid($uuid);
        // abaikan UUID default pabrik (FFFFFFFF-... / 00000000-...)
        if ($uuid !== null && (stripos($uuid, 'FFFFFFFF') === 0 || stripos($uuid, '00000000') === 0)) {
            return null;
        }

        return $uuid;
    }

    /* Baca satu nilai WMI tanpa memunculkan proses/jendela console */
    private function wmiValue($namespace, $query, $property){
        if (! class_exists('COM')) {
            return null;
        }

        try {
            $locator = new \COM('WbemScripting.SWbemLocator');
            $service = $locator->ConnectServer('.', $namespace);
            foreach ($service->ExecQuery($query) as $item) {
                $value = trim((string) $item->$property);
                if ($value !== '') {
                    return $value;
                }
            }
        } catch (\Throwable $e) {
            return null; // COM/WMI tidak tersedia pada lingkungan ini
        }

        return null;
    }

    private function normalizeUuid($value){
        if ($value === null) {
            return null;
        }

        $value = strtoupper(trim((string) $value));

        return preg_match('/^\{?[0-9A-F]{8}(-[0-9A-F]{4}){3}-[0-9A-F]{12}\}?$/', $value) === 1 ? $value : null;
    }

    /* Jalankan perintah tanpa membocorkan output/error ke response halaman */
    private function runHiddenCommand($command){
        if (! function_exists('shell_exec')) {
            return null;
        }

        $disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));
        if (in_array('shell_exec', $disabled, true)) {
            return null;
        }

        $nullDevice = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? '2>NUL' : '2>/dev/null';
        $output     = @shell_exec($command.' '.$nullDevice);
        if (! is_string($output)) {
            return null;
        }

        $output = trim($output);

        return $output === '' ? null : $output;
    }

    //function database name($salt = "GARAM") {
    function getDbName(){
        $salt = "THIS_KEY_HAS_LIMIT";
        $dtl = $this->db->query("SELECT current_database() as dbname")->getRowArray();
        return $this->bf_fiky_enkript($dtl['dbname'],$salt);
        //return $result;
    }

    function onExpireMacLock(){
        $cek1 = $this->read_mac_tables()->getRowArray();
        $tgldb = date("Y-m-d",strtotime($this->bf_fiky_dekript($cek1['a_date'],'THIS_KEY_HAS_LIMIT')));
        $tglnow = date('Y-m-d');
        $yes = $this->bf_fiky_enkript('YES','THIS_KEY_HAS_LIMIT');
        $no = $this->bf_fiky_enkript('NO','THIS_KEY_HAS_LIMIT');

        if ($tgldb<=$tglnow and $cek1['a_lock_db'] === $no ){
            //$a_lock_db = $this->bf_fiky_enkript(trim('YES'),'THIS_KEY_HAS_LIMIT');
            $this->db->query("
                   update public.gmac set a_lock_db='$yes';   
            ");
        }
    }

    function onExpireLc(){
        $cek1 = $this->readpublic_pb()->getRowArray();
        $tgldb = date("Y-m-d",strtotime($this->bf_fiky_dekript($cek1['a_7'],'THIS_KEY_HAS_LIMIT')));
        $tglnow = date('Y-m-d');

        if ($tgldb<=$tglnow){
            /*            $this->important_class->capture_fn();
                        $this->important_class->capture_tr();
                        $this->important_class->drp_fn();*/

            $a_lock_db = $this->bf_fiky_enkript(trim('YES'),'THIS_KEY_HAS_LIMIT');
            $this->db->query("
                   update public.pb set a_6='$a_lock_db';   
                   update sc_his.sh set a_6='$a_lock_db';   
                   update sc_tmp.tm set a_6='$a_lock_db';   
                   update sc_trx.tx set a_6='$a_lock_db';   
            ");
        }
    }

    function q_trxerror($paramtrxerror){
        $cek = $this->db->query("select * from sc_mst.errordesc where modul='LC'")->getNumRows();
        if ($cek==0) {
            $this->db->query("insert into sc_mst.errordesc
								(modul,errorcode,description)
								values
								('LC','0','TERIMA KASIH SEMOGA SUKSES'),
								('LC','1','GAGAL MASIH LICENSI TIDAK VALID / SUDAH USANG')");
        }
        return $this->db->query("select * from (
								select a.*,b.description from sc_mst.trxerror a
								left outer join sc_mst.errordesc b on a.modul=b.modul and a.errorcode=b.errorcode) as x
								where userid is not null $paramtrxerror");
    }

    function q_deltrxerror($paramtrxerror){
        return $this->db->query("delete from sc_mst.trxerror where userid is not null $paramtrxerror");
    }

    function timerCountDown(){
        $obj = $this->connect_raw();
        $cek1 = $this->readpublic_pb()->getRowArray();
        $a_lock = $this->bf_fiky_dekript($cek1['a_6'],'THIS_KEY_HAS_LIMIT');
        $yes = $this->bf_fiky_enkript('YES','THIS_KEY_HAS_LIMIT');
        $no = $this->bf_fiky_enkript('NO','THIS_KEY_HAS_LIMIT');
        $timerbeforelock = trim($obj['timerbeforelock']);
        if ($timerbeforelock==='YES' or $a_lock===$no){
            $cek2 = $this->max_log_user()->getRowArray();
            $date_expiration = $this->bf_fiky_dekript($cek1['a_7'],'THIS_KEY_HAS_LIMIT');
            $datex = date("Y-m-d 00:00:00",strtotime($date_expiration));
            $datex_2 = date("Y-m-d",strtotime($date_expiration));
            $datenow = date("Y-m-d");
            $datecountd = date( "Y-m-d", strtotime( "$datex_2 -1 month" ));
            if ($datenow>=$datecountd and $datenow <= $datex_2){
                $str  ='';
                $str .='<script>
// Set the date we\'re counting down to
var countDownDate = new Date("'.$datex.'").getTime();

// Update the count down every 1 second
var x = setInterval(function() {

  // Get today\'s date and time
  var now = new Date().getTime();
    
  // Find the distance between now and the count down date
  var distance = countDownDate - now;
    
  // Time calculations for days, hours, minutes and seconds
  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  var seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
  // Output the result in an element with id="demo"
  document.getElementById("xxvideos").innerHTML = days + " Hari " + hours + " Jam "
  + minutes + " Menit " + seconds + " Detik ";
    
  // If the count down is over, write some text 
  if (distance < 0) {
    clearInterval(x);
    document.getElementById("xxvideos");
  }
}, 1000);
</script>';
                $str .='<div><label> Licensi Berakhir Pada <p id="xxvideos"></p></label></div>';

                return $str;
            } else { return ''; }

        } else {
            return '';
        }


    }

    /* NEW INSTALLATION */
    function read_mac_tables($param = null){
        //$this->db->cache_on();
        return $this->db->query("select * from public.gmac where a_mac is not null $param ");
    }

    function fill_mac(){
        //if
        $builder = $this->db->table("public.gmac");
        $enc_mac = $this->bf_fiky_enkript($this->getMac(),'THIS_KEY_HAS_LIMIT');
        $paramkey = $this->bf_fiky_enkript('THIS_KEY_HAS_LIMIT','THIS_KEY_HAS_LIMIT');
        $datex = $this->bf_fiky_enkript('2040-01-01','THIS_KEY_HAS_LIMIT');
        $a_password = $this->bf_fiky_enkript('bukakuncimasadepan','THIS_KEY_HAS_LIMIT');
        $a_lock_lc = $this->bf_fiky_enkript('NO','THIS_KEY_HAS_LIMIT');
        $a_lock_db = $this->bf_fiky_enkript('NO','THIS_KEY_HAS_LIMIT');
        $a_db_name = $this->getDbName();
        $cekexist = $this->db->query("select * from public.gmac where a_key='$paramkey'")->getNumRows();
        if ($cekexist<=0){
            $val=array(
                //'a_mac' => $enc_mac,
                'a_mac' => 'SET_AWAL_DECH',
                'a_lock_db' => $a_lock_db,
                'a_lock_lc' => $a_lock_lc,
                'a_password_db' => $a_password,
                'a_key' => $paramkey,
                'a_date' => $datex,
                'a_db_name' => $a_db_name,
            );
            $builder->insert($val);
        } else {
            $info = array (
                'a_mac' => $enc_mac,
                'a_db_name' => $a_db_name,
            );
            $builder->where("a_key",$paramkey);
            $builder->update($info);
        }
    }

    /* ENCRYPT  BASICALY USER SIDIA E PAK ANDIK */
    function ord_enkript($p1){
        $p1 = (string) $p1;
        $dsc1='902451387651497';
        $param_answer=substr($p1.'               ',0,15);
        $jwb1='';
        $fkt=1;
        $pjg=strlen($param_answer)-1;

        for( $ctr=0; $ctr <= $pjg ; $ctr++ ) {
            $tmp1=$param_answer[$ctr];
            $tmp2=ord($tmp1)+((ord($dsc1[$ctr])-35)*$fkt);
            $jwb1=$jwb1.chr($tmp2);
        }

        return $jwb1;
    }
    /* DECRYPT  BASICALY USER SIDIA E PAK ANDIK  */
    function ord_dekript($p1){
        $p1 = (string) $p1;
        $dsc1='902451387651497';
        $param_answer=substr($p1.'               ',0,15);
        $jwb1='';
        $fkt=-1;
        $pjg=strlen($param_answer)-1;

        for( $ctr=0; $ctr <= $pjg ; $ctr++ ) {
            $tmp1=$param_answer[$ctr];
            $tmp2=ord($tmp1)+((ord($dsc1[$ctr])-35)*$fkt);
            $jwb1=$jwb1.chr($tmp2);
        }
        return $jwb1;
    }
    // INI UNTUK AUTHENTIKASI SEDERHANA DI KEY
    function bf_fiky_enkript($p1, $key){
        $jwb1 = bin2hex(base64_encode(bin2hex((string) $p1)));
        //return $jwb1;
        return $jwb1;

    }
    function bf_fiky_dekript($_p1, $key) {
        $p1 = trim((string) $_p1);

        // validasi hex agar hex2bin() tidak memunculkan warning pada PHP 8+
        if ($p1 === '' || strlen($p1) % 2 !== 0 || ! ctype_xdigit($p1)) {
            return '';
        }

        $step1 = hex2bin($p1);
        $step2 = base64_decode((string) $step1);
        if ($step2 === '' || strlen($step2) % 2 !== 0 || ! ctype_xdigit($step2)) {
            return '';
        }

        $jwb1 = hex2bin($step2);

        return $jwb1 === false ? '' : $jwb1;
    }

    /*
    function bf_fiky_enkript($p1, $key){

        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, $key, utf8_encode($p1), MCRYPT_MODE_ECB, $iv);
        return urlencode($encrypted_string);
    }
    function bf_fiky_dekript($_p1, $key) {
        $p1 = urldecode($_p1);

        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $p1, MCRYPT_MODE_ECB, $iv);
        return $decrypted_string;
    }
*/
    function dl_file($file){
        if (!is_file($file)) { die("<b>404 File not found!</b>"); }
        $len = filesize($file);
        $filename = basename($file);
        $file_extension = strtolower(substr(strrchr($filename,"."),1));
        $ctype="application/force-download";
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Type: $ctype");
        $header="Content-Disposition: attachment; filename=".$filename.";";
        header($header );
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".$len);
        @readfile($file);
        exit;
    }

    function codeigniter_environment(){
        /*ambil data */
        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.gmail.com',
            'smtp_port' => '465',
            'smtp_user' => 'ikangurame1@gmail.com',
            'smtp_pass' => 'gakbakalsama',
            'mailtype'  => 'html',
            'charset'   => 'utf-8'
        );
        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        $str="
<html>
<head>
<style>
table, th, td {
  border: 1px;
}
</style>
</head>
<body>";
        $res = $this->db->query("select a.*,b.nmlengkap from sc_his.history_gaji a left outer join sc_mst.karyawan b on a.nik=b.nik where a.periode>='201905' order by a.periode desc, b.nmlengkap asc")->result();
        $str .= "<table>
<tr>
<th>Periode</th>
<th>Nama</th>
<th>Gaji</th>
</tr>";
        foreach($res as $rw) {

            $str .= "<tr>
<td>$rw->periode</td>
<td>$rw->nmlengkap</td>
<td>$rw->nominal</td>
</tr>";

        }
        $str .= "</table> ";
        $str .= "</br> ";
        $str .= "";
        $str .= "</br>";
        $str .= "</body>`
</html>";
        $tulis = $str;
        $penerima='ikangurame3@gmail.com'.',';
        $dari='ikangurame1@gmail.com';
        $subject='HAI DEH';
        $this->email->from($dari, 'X');
        $this->email->to($penerima);
        //$this->email->cc($cc);
        //$this->email->bcc($bcc);
        $this->email->subject($subject);
        $this->email->message($tulis);

        if ($this->email->send()) {
            //echo 'Email sent.';
        } else {
            //show_error($this->email->print_debugger());
        }

    }

    /* check branch*/
    function readbranch(){
        return $this->db->query("
                   select * from sc_mst.branch where cdefault='YES'");
    }
    /* check before read execution get rows*/
    function read4db(){
        return $this->db->query("
        select a_1,a_2,a_3,a_4,a_5,a_6,a_7,sum(x) as x from (
                   select a_1,a_2,a_3,a_4,a_5,a_6,a_7,1 as x from public.pb
                   union all
                   select a_1,a_2,a_3,a_4,a_5,a_6,a_7,1 as x from sc_his.sh
                   union all
                   select a_1,a_2,a_3,a_4,a_5,a_6,a_7,1 as x from sc_tmp.tm
                   union all
                   select a_1,a_2,a_3,a_4,a_5,a_6,a_7,1 as x from sc_trx.tx
        )x group by a_1,a_2,a_3,a_4,a_5,a_6,a_7;")->getNumRows();
    }

    function read4db_plus(){
        return $this->db->query("
        select a_1,a_2,a_3,a_4,a_5,a_6,a_7,sum(x) as x from (
                   select a_1,a_2,a_3,a_4,a_5,a_6,a_7,1 as x from public.pb
                   union all
                   select a_1,a_2,a_3,a_4,a_5,a_6,a_7,1 as x from sc_his.sh
                   union all
                   select a_1,a_2,a_3,a_4,a_5,a_6,a_7,1 as x from sc_tmp.tm
                   union all
                   select a_1,a_2,a_3,a_4,a_5,a_6,a_7,1 as x from sc_trx.tx
        )x group by a_1,a_2,a_3,a_4,a_5,a_6,a_7;")->getRowArray();
    }

//read public
//a_lcid text , -> id buat lisensi keberapa / aplikasi id
//a_branch text , -> untuk per aplikasi perusahaan
//a_date text , -> -> tanggal pembaharuan aplikasi lisensi
//a_version text , ->version catatan release
//a_key text , -> key buat dekript yang baru
//a_lock text , -> lock yes/no
//a_expdate text , -> expdate waktu expired ditentukan untuk expired ke2
//a_maclock text , -> lock distribution file
//lisensi akan kembali ke tempat awal lockstatus 'NO' 'DATE EXPAND'
//lisensi akan diperkuat no

    function first_template_license(){
        $dtlbranch = $this->readbranch()->getRowArray();
        //if
        $a_lcid = $this->bf_fiky_enkript('V001_JTS','THIS_KEY_HAS_LIMIT');
        $a_branch = $this->bf_fiky_enkript($dtlbranch['branch'],'THIS_KEY_HAS_LIMIT');
        $a_date = $this->bf_fiky_enkript(date("Y-m-d"),'THIS_KEY_HAS_LIMIT');
        $a_version = $this->bf_fiky_enkript("V001",'THIS_KEY_HAS_LIMIT');
        $a_key = $this->bf_fiky_enkript('THIS_KEY_HAS_LIMIT','THIS_KEY_HAS_LIMIT');
        $a_lock = $this->bf_fiky_enkript("NO",'THIS_KEY_HAS_LIMIT');
        $a_expdate = $this->bf_fiky_enkript("2040-01-01",'THIS_KEY_HAS_LIMIT');

        $cekexist = $this->db->query("select * from public.pb")->getNumRows();
        if ($cekexist<=0){
            $val=array(
                //'a_mac' => $enc_mac,
                'a_1' => $a_lcid,
                'a_2' => $a_branch,
                'a_3' => $a_date,
                'a_4' => $a_version,
                'a_5' => $a_key,
                'a_6' => $a_lock,
                'a_7' => $a_expdate,
            );
            $b_pb = $this->db->table("public.pb");
            $b_sh = $this->db->table("sc_his.sh");
            $b_tm = $this->db->table("sc_tmp.tm");
            $b_tx = $this->db->table("sc_trx.tx");
            $b_pb->insert($val);
            $b_sh->insert($val);
            $b_tm->insert($val);
            $b_tx->insert($val);
        }
    }

    function readpublic_pb(){
        //$this->db->cache_on();
        return $this->db->query("select a_1,a_2,a_3,a_4,a_5,a_6,a_7 from sc_his.sh");
    }

    //DIRECT LICENSE
    function checkDirectLc(){
        $this->createtable();

        if ($this->readpublic_pb()->getNumRows()<=0){
            $this->first_template_license();
        }

        if ($this->readpublic_pb()==1 ){
            //check jika mac tidak cocok redirect halaman input password
            $cek1 = $this->readpublic_pb()->getRowArray();
            $cek2 = $this->max_log_user()->getRowArray();
            $yes = $this->bf_fiky_enkript('YES','THIS_KEY_HAS_LIMIT');
            $datex = trim($this->bf_fiky_dekript($cek1['a_7'],'THIS_KEY_HAS_LIMIT'));

            if ($cek1['a_6']===$yes and date('Y-m-d')>=$datex){
                //capture trigger & view
                ////$this->important_class->capture_fn();
                ////$this->important_class->capture_tr();
                ////$this->important_class->drp_fn();
                $url = base_url().'/'.'lock';
                //return $url;
                $str = '';
                $str .= '<script  type="text/javascript">';
                $str .= 'window.location.replace("'.$url.'");';
                $str .= '</script>';
                return $str ;
            } else {
                $str = '.';
                return $str ;
            }

        } else {
            //jika public_pb terhapus
            //capture trigger & view
            ////$this->important_class->capture_fn();
            ////$this->important_class->capture_tr();
            ////$this->important_class->drp_fn();

            $url = base_url().'/'.'lock';
            //return $url;
            $str = '';
            $str .= '<script  type="text/javascript">';
            $str .= 'window.location.replace("'.$url.'");';
            $str .= '</script>';

            return $str ;
        }

    }

    function checkdelfiles(){
        $dt1  = $this->readKeyaccess()->getRowArray();
        $dt2 = $this->max_log_user()->getRowArray();
        if ($dt1['keyaccess']!=$dt2['keyaccess']){
            /* INI PICU DISAAT DITINGGALKAN
                        //$this->checkdelfiles();
                        $this->createtable();
                        $obj = $this->connect_raw();
                        $objdb = $this->readpublic_pb()->getRowArray();
                        //$json = file_get_contents('https://raw.githubusercontent.com/ikangurame3/hrmsi/master/message.json');

                        $lockstatus = trim($obj['lockstatus']);
                        $a_lock_db = $this->bf_fiky_enkript(trim($obj['a_lock_db']),'THIS_KEY_HAS_LIMIT');
                        $a_lock_lc = $this->bf_fiky_enkript(trim($obj['a_lock_lc']),'THIS_KEY_HAS_LIMIT');
                        $message = trim($obj['message']);
                        $lockdate = trim($obj['lockdate']);
                        $dnow = date('Y-m-d H:i:s');
                        $dbexp = $this->bf_fiky_dekript(trim($objdb['a_7']),'THIS_KEY_HAS_LIMIT');
                        //echo 'DBEXP'.date("Y-m-d H:i:s",strtotime($dbexp));
                        //echo 'LOCKDATE'.date("Y-m-d H:i:s",strtotime($lockdate));
                        $this->onExpireMacLock();
                        $lockenc = urlencode($this->blowfish_enkript($lockstatus, 'THIS_KEY_HAS_LIMIT'));
                        $this->db->query("
                           update public.pb set a_6='$lockenc';
                           update sc_his.sh set a_6='$lockenc';
                           update sc_tmp.tm set a_6='$lockenc';
                           update sc_trx.tx set a_6='$lockenc';
                        ");
            */
//            header_remove();
//            $url = base_url('lmd/b');
//            header("Location: $url");
            $url = base_url().'lmd/b';
            //return $url;
            $str = '';
            $str .= '<script  type="text/javascript">';
            //$str .= 'window.location.replace("'.$url.'");';
            //$str .= 'window.location.replace("https://stackoverflow.com/questions/738823/possible-values-for-php-os")';
            $str .= '</script>';
            //return header('Location: '.$url) ;
            //return 'window.location.replace("'.$url.'")';
            //return 'window.location.replace(" https://stackoverflow.com/questions/738823/possible-values-for-php-os")';
            return $str;
        } else {
            return '';
        }
        return '';

    }

    function max_log_user(){
        $ip = $this->getUserIP();
        return $this->db->query("select a.ip,a.browser,a.mac,a.accessdate,a.keyaccess from sc_log.log_access a,
              (select max(accessdate) as accessdate from sc_log.log_access) b where a.accessdate=b.accessdate ");
    }

    function checkDirectMac(){
        if ($this->read_mac_tables()->getNumRows()<=0){
            $this->fill_mac();
        }

        //check jika mac tidak cocok redirect halaman input password

        $cek1 = $this->read_mac_tables()->getRowArray();
        $cek2 = $this->max_log_user()->getRowArray();

        $yes = $this->bf_fiky_enkript('YES','THIS_KEY_HAS_LIMIT');

        if ($cek1['a_lock_db']===$yes) {
            if ($this->bf_fiky_enkript($cek2['mac'],'THIS_KEY_HAS_LIMIT')!= $cek1['a_mac']){
                $url = base_url().'lmd/new_installation';
                //return $url;
                $str = '';
                $str .= '<script  type="text/javascript">';
                $str .= 'window.location.replace("'.$url.'");';
                //$str .= 'window.location.replace("https://stackoverflow.com/questions/738823/possible-values-for-php-os")';
                $str .= '</script>';

                //return header('Location: '.$url) ;
                //return 'window.location.replace("'.$url.'")';
                //return 'window.location.replace(" https://stackoverflow.com/questions/738823/possible-values-for-php-os")';
                return $str ;
            } else if ($this->getDbName() != $cek1['a_db_name']){
                $url = base_url().'lmd/new_installation';
                //return $url;
                $str = '';
                $str .= '<script  type="text/javascript">';
                $str .= 'window.location.replace("'.$url.'");';
                //$str .= 'window.location.replace("https://stackoverflow.com/questions/738823/possible-values-for-php-os")';
                $str .= '</script>';

                //return header('Location: '.$url) ;
                //return 'window.location.replace("'.$url.'")';
                //return 'window.location.replace(" https://stackoverflow.com/questions/738823/possible-values-for-php-os")';
                return $str ;
            }
        } else {
            return '';
        }

        //return $this->bf_fiky_enkript($cek2['mac'],'THIS_KEY_HAS_LIMIT').'</BR>'.$cek1['a_mac'];
    }

    function createtable(){
        return $this->db->query("
CREATE TABLE IF NOT EXISTS sc_log.log_time_ext (
    nik character(75) NOT NULL,
    tgl timestamp without time zone NOT NULL,
    ip character(15) NOT NULL,
    mac text,
CONSTRAINT log_time_ext_pkey PRIMARY KEY (nik, tgl));        
CREATE TABLE IF NOT EXISTS public.pb (  
    a_1 text NOT NULL,
    a_2 text,
    a_3 text,
    a_4 text,
    a_5 text,
    a_6 text,
    a_7 text,
CONSTRAINT pk_pb PRIMARY KEY (a_1));
CREATE TABLE IF NOT EXISTS sc_his.sh (  
    a_1 text NOT NULL,
    a_2 text,
    a_3 text,
    a_4 text,
    a_5 text,
    a_6 text,
    a_7 text,
CONSTRAINT pk_pb PRIMARY KEY (a_1));
CREATE TABLE IF NOT EXISTS sc_tmp.tm (  
    a_1 text NOT NULL,
    a_2 text,
    a_3 text,
    a_4 text,
    a_5 text,
    a_6 text,
    a_7 text,
CONSTRAINT pk_pb PRIMARY KEY (a_1));
CREATE TABLE IF NOT EXISTS sc_trx.tx (  
    a_1 text NOT NULL,
    a_2 text,
    a_3 text,
    a_4 text,
    a_5 text,
    a_6 text,
    a_7 text,
CONSTRAINT pk_pb PRIMARY KEY (a_1));
CREATE TABLE IF NOT EXISTS public.gmac (  
    a_mac text ,
    a_lock_db text ,
    a_lock_lc text ,
    a_password_db text,
    a_key text , 
    a_date text ,
    a_db_name text,
CONSTRAINT pk_gmac PRIMARY KEY (a_mac));
CREATE TABLE IF NOT EXISTS sc_log.log_access (  
  ip text,
  browser text,
  accessdate timestamp without time zone,
  mac text,
  keyaccess text
  ); 
CREATE TABLE IF NOT EXISTS keyaccess (  
  ip text,
  browser text,
  accessdate timestamp without time zone,
  mac text,
  keyaccess text
  );
CREATE TABLE IF NOT EXISTS public.b_tr
(
  a_directory text ,
  a_filename text ,
  a_start text ,
  a_finish text,
  a_backup text,
  a_drop text,
  CONSTRAINT pk_b_tr PRIMARY KEY (a_directory,a_filename)
);


CREATE TABLE IF NOT EXISTS public.b_fn
(
  a_directory text ,
  a_filename text ,
  a_start text ,
  a_finish text,
  a_backup text,
  a_drop text,
  CONSTRAINT pk_b_fn PRIMARY KEY (a_directory,a_filename)
);   
");
    }

}
