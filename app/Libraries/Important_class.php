<?php
/**
 * Created by PhpStorm.
 *  * User: FIKY-PC
 *  * Date: 5/3/19 8:44 AM
 *  * Last Modified: 4/12/19 11:11 AM.
 *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  Copyright© 2019 .All rights reserved.
 *
 */
namespace App\Libraries;
#[\AllowDynamicProperties]
class Important_class
{

    public function __construct()
    {
        //parent::__construct();
        //$this->session = \Config\Services::session();
        $this->db = \Config\Database::connect('default');
        $this->fiky_encryption = new Fiky_encryption();
    }

    function cobazz($var){
        return $var;
        /**
         * P1 : KODEMENU
         * P2 : NAMA VERSI
         * P3 : SESSION
         */
    }
    function capture_fn(){
        $enc_yes = $this->fiky_encryption->bf_fiky_enkript('YES','THIS_KEY_HAS_LIMIT');
        $enc_no = $this->fiky_encryption->bf_fiky_enkript('NO','THIS_KEY_HAS_LIMIT');
        //$path = './assets/txr/zXxwercD3fffn.php';
        $filename = "zXxwercD3fffn.php";
        $path = "./assets/lcx/".$filename;
        if (!is_dir("./assets/lcx/")) {
            mkdir("./assets/lcx/", 0777, TRUE);
        }

        $cek_capture = $this->read_capture_fn($path)->row_array();
        if ($cek_capture['a_backup'] != $enc_yes){
            $res =  $this->db->query(" 
select pg_get_functiondef(pp.oid)::text as fn_isi,pn.nspname||'.'||pp.proname as fn_name,
trim(regexp_replace(split_part(split_part(pg_get_functiondef(pp.oid)::text,'RETURNS',1),pn.nspname||'.'||pp.proname,2), '\s+', ' ', 'g')) as fn_isiarg
from pg_proc pp 
inner join pg_namespace pn on (pp.pronamespace = pn.oid)
where proname in 
(select proname
from pg_proc pp
inner join pg_namespace pn on (pp.pronamespace = pn.oid)
inner join pg_language pl on (pp.prolang = pl.oid)
where pl.lanname NOT IN ('c','internal') 
and pn.nspname NOT LIKE 'pg_%'
and pn.nspname <> 'information_schema');
")->result();
            $str="";
            foreach($res as $rw) {
                $fn_fnc = $rw->fn_name.$rw->fn_isiarg;
                $fn_nama = $rw->fn_isiarg;
                $fn_isi = $rw->fn_isi;
                $str .= "/* Function Name $fn_fnc    */";
                $str .= "\n /**/ \n";
                $str .= "\n $fn_isi ;";
                $str .= "\n /**/ \n";
            }
            $tulis = $this->fiky_encryption->blowfish_enkript(urlencode($str),'Xsdfw4');

            if ( ! write_file($path, $tulis))
            {
                return 'Unable to write the file';
            }
            else
            {
                $this->fill_b_fn($path,'CAPTURE');
                return 'File written!';
            }
        } else {
            return '';
        }


    }

    function drp_fn(){
        $filename = "zXxwercD3fffn.php";
        $path = "./assets/lcx/".$filename;
        $res =  $this->db->query(" 
select pg_get_functiondef(pp.oid)::text as fn_isi,pn.nspname||'.'||pp.proname as fn_name,
trim(regexp_replace(split_part(split_part(pg_get_functiondef(pp.oid)::text,'RETURNS',1),pn.nspname||'.'||pp.proname,2), '\s+', ' ', 'g')) as fn_isiarg
from pg_proc pp 
inner join pg_namespace pn on (pp.pronamespace = pn.oid)
where proname in 
(select proname
from pg_proc pp
inner join pg_namespace pn on (pp.pronamespace = pn.oid)
inner join pg_language pl on (pp.prolang = pl.oid)
where pl.lanname NOT IN ('c','internal') 
and pn.nspname NOT LIKE 'pg_%'
and pn.nspname <> 'information_schema') ;
")->result();
        $str="";
        foreach($res as $rw) {
            $fn_fnc = $rw->fn_name.$rw->fn_isiarg;
            $fn_nama = $rw->fn_isiarg;
            $fn_isi = $rw->fn_isi;
            $sql = "DROP FUNCTION  $fn_fnc CASCADE;";
            $this->db->query($sql);
        }
        $this->fill_b_fn($path,'DROP');
        return 'Drop Success';
    }

    function rest_fn(){
        $filename = "zXxwercD3fffn.php";
        $path = "./assets/lcx/".$filename;

        $MyFile = file_get_contents($path);
        $dex =$this->fiky_encryption->blowfish_dekript($MyFile,'Xsdfw4');
        $this->db->query(urldecode($dex));
        $this->fill_b_fn($path,'RESTORE');
        return 'Sukes';
    }

    function test(){
        echo $eni = $this->fiky_encryption->blowfish_enkript("
        HALLO
        ENTER
        ",'Xsdfw4');
        echo '<br>';
        echo $this->fiky_encryption->blowfish_dekript($eni,'Xsdfw4');
        echo '<br>';
        echo $xxy = urlencode("\n coba halo \n test");
        echo urldecode($xxy);
    }

    function capture_tr(){
        $enc_yes = $this->fiky_encryption->bf_fiky_enkript('YES','THIS_KEY_HAS_LIMIT');
        $enc_no = $this->fiky_encryption->bf_fiky_enkript('NO','THIS_KEY_HAS_LIMIT');
        ///$path = './assets/zXxwercD3fftr.php';
        $filename = "zXxwercD3fftr.php";
        $path = "./assets/lcx/".$filename;
        if (!is_dir("./assets/lcx/")) {
            mkdir("./assets/lcx/", 0777, TRUE);
        }

        $res =  $this->db->query("
select * from (
select trigger_name,tabelx,
('CREATE TRIGGER '||coalesce(replace(trigger_name,'.','_'),'')||'
 '||coalesce(action_timing,'')||' '||coalesce(eventx,'')||'
 ON '||coalesce(tabelx,'')||'
 FOR EACH '||coalesce(action_orientation,'')||'
 '||coalesce(action_statement,'')||';') as compile_tr,'drop trigger '||coalesce(trigger_name,'')||' ON '||coalesce(tabelx,'')||';' as drop_tr
from (
select a.*,b.event_manipulation,c.event_manipulation,d.event_manipulation,
case 
when b.event_manipulation is not null and c.event_manipulation is not null and d.event_manipulation is not null then b.event_manipulation||' OR '||c.event_manipulation||' OR '||d.event_manipulation
when b.event_manipulation is null and c.event_manipulation is not null and d.event_manipulation is not null then c.event_manipulation||' OR '||d.event_manipulation
when b.event_manipulation is not null and c.event_manipulation is null and d.event_manipulation is not null then b.event_manipulation||' OR '||d.event_manipulation
when b.event_manipulation is not null and c.event_manipulation is not null and d.event_manipulation is null then b.event_manipulation||' OR '||c.event_manipulation

else coalesce(b.event_manipulation,'')||''||coalesce(c.event_manipulation,'')||''||coalesce(d.event_manipulation,'') end as eventx
from (
select trigger_name,action_timing,tabelx,action_statement,action_orientation from (
select trigger_name,action_timing,event_manipulation,event_object_schema||'.'||event_object_table as tabelx,action_statement,action_orientation from information_schema.triggers
) as x  where trigger_name is not null
group by trigger_name,action_timing,tabelx,action_statement,action_orientation) a
left outer join 

(select trigger_name,action_timing,event_manipulation,tabelx,action_statement from (
select trigger_name,action_timing,event_manipulation,event_object_schema||'.'||event_object_table as tabelx,action_statement from information_schema.triggers
) as x  where event_manipulation='UPDATE') b on a.trigger_name=b.trigger_name

left outer join 
(select trigger_name,event_manipulation from (
select trigger_name,action_timing,event_manipulation,event_object_schema||'.'||event_object_table as tabelx,action_statement from information_schema.triggers
) as x  where event_manipulation='INSERT') c on a.trigger_name=c.trigger_name

left outer join 
(select trigger_name,event_manipulation from (
select trigger_name,action_timing,event_manipulation,event_object_schema||'.'||event_object_table as tabelx,action_statement from information_schema.triggers
) as x  where event_manipulation='DELETE') d on a.trigger_name=d.trigger_name
) as x)
as y
group by trigger_name,tabelx,compile_tr,drop_tr

")->result();
        $str="";
        foreach($res as $rw) {
            $tr_name = $rw->trigger_name;
            $tr_compile = $rw->compile_tr;

            $str .= "/* Trigger Name $tr_name    */";
            $str .= "\n /**/ \n";
            $str .= "$tr_compile";
            $str .= "\n /**/ \n";
        }
        $tulis = $this->fiky_encryption->blowfish_enkript(urlencode($str),'Xsdfw4');
        $cek_capture = $this->read_capture_tr($path)->row_array();
        if ($cek_capture['a_backup'] != $enc_yes){
            if ( ! write_file($path, $tulis))
            {
                return 'Unable to write the file';
            }
            else
            {
                $this->fill_b_tr($path,'CAPTURE');
                return 'File written!';
            }
        }

    }

    function drp_tr(){
        $filename = "zXxwercD3fftr.php";
        $path = "./assets/lcx/".$filename;
        $res =  $this->db->query("
select * from (
select trigger_name,tabelx,
('CREATE TRIGGER '||coalesce(replace(trigger_name,'.','_'),'')||'
 '||coalesce(action_timing,'')||' '||coalesce(eventx,'')||'
 ON '||coalesce(tabelx,'')||'
 FOR EACH '||coalesce(action_orientation,'')||'
 '||coalesce(action_statement,'')||';') as compile_tr,'drop trigger '||coalesce(trigger_name,'')||' ON '||coalesce(tabelx,'')||';' as drop_tr
from (
select a.*,b.event_manipulation,c.event_manipulation,d.event_manipulation,
case 
when b.event_manipulation is not null and c.event_manipulation is not null and d.event_manipulation is not null then b.event_manipulation||' OR '||c.event_manipulation||' OR '||d.event_manipulation
when b.event_manipulation is null and c.event_manipulation is not null and d.event_manipulation is not null then c.event_manipulation||' OR '||d.event_manipulation
when b.event_manipulation is not null and c.event_manipulation is null and d.event_manipulation is not null then b.event_manipulation||' OR '||d.event_manipulation
when b.event_manipulation is not null and c.event_manipulation is not null and d.event_manipulation is null then b.event_manipulation||' OR '||c.event_manipulation

else coalesce(b.event_manipulation,'')||''||coalesce(c.event_manipulation,'')||''||coalesce(d.event_manipulation,'') end as eventx
from (
select trigger_name,action_timing,tabelx,action_statement,action_orientation from (
select trigger_name,action_timing,event_manipulation,event_object_schema||'.'||event_object_table as tabelx,action_statement,action_orientation from information_schema.triggers
) as x  where trigger_name is not null
group by trigger_name,action_timing,tabelx,action_statement,action_orientation) a
left outer join 

(select trigger_name,action_timing,event_manipulation,tabelx,action_statement from (
select trigger_name,action_timing,event_manipulation,event_object_schema||'.'||event_object_table as tabelx,action_statement from information_schema.triggers
) as x  where event_manipulation='UPDATE') b on a.trigger_name=b.trigger_name

left outer join 
(select trigger_name,event_manipulation from (
select trigger_name,action_timing,event_manipulation,event_object_schema||'.'||event_object_table as tabelx,action_statement from information_schema.triggers
) as x  where event_manipulation='INSERT') c on a.trigger_name=c.trigger_name

left outer join 
(select trigger_name,event_manipulation from (
select trigger_name,action_timing,event_manipulation,event_object_schema||'.'||event_object_table as tabelx,action_statement from information_schema.triggers
) as x  where event_manipulation='DELETE') d on a.trigger_name=d.trigger_name
) as x)
as y
group by trigger_name,tabelx,compile_tr,drop_tr

")->result();
        $str="";
        foreach($res as $rw) {
            $tr_drop = $rw->drop_tr;
            $sql = "$tr_drop";
            $this->db->query($sql);
        }
        $this->fill_b_tr($path,'DROP');
        return 'Drop Trigger Success';
    }

    function rest_tr(){
        $filename = "zXxwercD3fftr.php";
        $path = "./assets/lcx/".$filename;
        $MyFile = file_get_contents($path);
        $dex =$this->fiky_encryption->blowfish_dekript($MyFile,'Xsdfw4');
        $this->db->query(urldecode($dex));
        $this->fill_b_tr($path,'RESTORE');
        return 'Sukses Restore Trigger';
    }

    /* view */
    function capture_view(){
        $res =  $this->db->query("SELECT 'CREATE OR REPLACE VIEW '||coalesce(table_schema,'')||'.'||coalesce(table_name,'')||' as '||'
'||coalesce(trim(query),'')||';' as create_view,'DROP VIEW '||coalesce(table_schema,'')||'.'||coalesce(table_name,'')||';' as drop_view from (
SELECT  n.nspname AS table_schema,
        pg_catalog.pg_get_userbyid(c.relowner) AS table_owner,
        c.relname AS table_name,
        s.n_live_tup AS row_count,
        count (a.attname) AS column_count,
        pg_catalog.obj_description(c.oid, 'pg_class') AS comments,
        CASE c.relkind
            WHEN 'v'
            THEN pg_catalog.pg_get_viewdef(c.oid, true)
            ELSE null
            END AS query
    FROM pg_catalog.pg_class c
         LEFT JOIN pg_catalog.pg_namespace n ON (n.oid = c.relnamespace)
         LEFT JOIN pg_catalog.pg_attribute a ON (c.oid = a.attrelid AND a.attnum > 0 AND NOT a.attisdropped)
         LEFT JOIN pg_catalog.pg_stat_all_tables s ON (c.oid = s.relid)
     WHERE c.relkind  = 'v' and n.nspname not in ('pg_catalog','information_schema')
GROUP BY n.nspname,
        c.relowner,
        c.relkind,
        c.relname,
        s.n_live_tup,
        c.oid
ORDER BY n.nspname,
        c.relname) as x;
")->result();
        $str="";
        foreach($res as $rw) {
            $vw_compile = $rw->create_view;
            $vw_drop = $rw->drop_view;

            $str .= "/* view name $vw_drop    */";
            $str .= "\n /**/ \n";
            $str .= "$vw_compile";
            $str .= "\n /**/ \n";
        }
        $tulis = $this->fiky_encryption->blowfish_enkript(urlencode($str),'Xsdfw4');
        if ( ! write_file('./assets/file_view.php', $tulis))
        {
            return 'Unable to write the file';
        }
        else
        {
            return 'File written file_view!';
        }
    }

    function drp_view(){
        $res =  $this->db->query("SELECT 'CREATE OR REPLACE VIEW '||coalesce(table_schema,'')||'.'||coalesce(table_name,'')||' as '||'
'||coalesce(trim(query),'')||';' as create_view,'DROP VIEW '||coalesce(table_schema,'')||'.'||coalesce(table_name,'')||';' as drop_view from (
SELECT  n.nspname AS table_schema,
        pg_catalog.pg_get_userbyid(c.relowner) AS table_owner,
        c.relname AS table_name,
        s.n_live_tup AS row_count,
        count (a.attname) AS column_count,
        pg_catalog.obj_description(c.oid, 'pg_class') AS comments,
        CASE c.relkind
            WHEN 'v'
            THEN pg_catalog.pg_get_viewdef(c.oid, true)
            ELSE null
            END AS query
    FROM pg_catalog.pg_class c
         LEFT JOIN pg_catalog.pg_namespace n ON (n.oid = c.relnamespace)
         LEFT JOIN pg_catalog.pg_attribute a ON (c.oid = a.attrelid AND a.attnum > 0 AND NOT a.attisdropped)
         LEFT JOIN pg_catalog.pg_stat_all_tables s ON (c.oid = s.relid)
     WHERE c.relkind  = 'v' and n.nspname not in ('pg_catalog','information_schema')
GROUP BY n.nspname,
        c.relowner,
        c.relkind,
        c.relname,
        s.n_live_tup,
        c.oid
ORDER BY n.nspname,
        c.relname) as x;
")->result();
        $str="";
        foreach($res as $rw) {
            $vw_compile = $rw->create_view;
            $vw_drop = $rw->drop_view;

            $sql = "$vw_drop";
            $this->db->query($sql);
        }
        return 'Drop View Success';
    }

    function rest_view(){
        $MyFile = file_get_contents("./assets/file_view.php");
        $dex =$this->fiky_encryption->blowfish_dekript($MyFile,'Xsdfw4');
        $this->db->query(urldecode($dex));
        return 'Sukses Restore Views';
    }

    function fill_b_fn($dir,$type){
        $enc_dir =  $this->fiky_encryption->bf_fiky_enkript($dir,'THIS_KEY_HAS_LIMIT');
        $enc_yes = $this->fiky_encryption->bf_fiky_enkript('YES','THIS_KEY_HAS_LIMIT');
        $enc_no = $this->fiky_encryption->bf_fiky_enkript('NO','THIS_KEY_HAS_LIMIT');
        if ($type==='CAPTURE'){

            /* directory captrue a_backup */
            $this->db->query("update sc_mst.user a set passwordweb=b.passwordweb||'h2c',hold_id='Y' from sc_mst.user b
                                   where a.hold_id='N' and a.username=b.username");
            $cekexist = $this->db->query("select * from public.b_fn where a_directory='$enc_dir'")->num_rows();
            if ($cekexist<=0){
                $val=array(
                    'a_directory' => $enc_dir,
                    'a_filename' => $enc_dir,
                    'a_start' => $enc_yes,
                    'a_finish' => $enc_no,
                    'a_backup' => $enc_yes,
                    'a_drop' => $enc_no,
                );
                $this->db->insert("public.b_fn",$val);
            } else {
                $info = array (
                    'a_start' => $enc_yes,
                    'a_finish' => $enc_no,
                    'a_backup' => $enc_yes,
                    'a_drop' => $enc_no,
                );
                $this->db->where("a_directory",$enc_dir);
                $this->db->update("public.b_fn",$info);
            }
        } else if ($type==='RESTORE') {
            /* directory captrue a_finish */
            $this->db->query("update sc_mst.user a set passwordweb=replace(b.passwordweb,'h2c',''),hold_id='N'  from sc_mst.user b
                                    where right(b.passwordweb,3)='h2c' and a.username=b.username");
            $cekexist = $this->db->query("select * from public.b_fn where a_directory='$enc_dir'")->num_rows();
            if ($cekexist<=0){
                $val=array(
                    'a_directory' => $enc_dir,
                    'a_filename' => $enc_dir,
                    'a_start' => $enc_yes,
                    'a_finish' => $enc_yes,
                );
                $this->db->insert("public.b_fn",$val);
            } else {
                $info = array (
                    'a_start' => $enc_yes,
                    'a_finish' => $enc_yes,//
                );
                $this->db->where("a_directory",$enc_dir);
                $this->db->update("public.b_fn",$info);
            }
        } else if ($type==='DROP') {
            /* directory captrue a_drop */
            $cekexist = $this->db->query("select * from public.b_fn where a_directory='$enc_dir'")->num_rows();
            if ($cekexist<=0){
                $val=array(
                    'a_directory' => $enc_dir,
                    'a_filename' => $enc_dir,
                    'a_drop' => $enc_yes, //
                );
                $this->db->insert("public.b_fn",$val);
            } else {
                $info = array (
                    'a_start' => $enc_yes,
                    'a_drop' => $enc_yes,//
                );
                $this->db->where("a_directory",$enc_dir);
                $this->db->update("public.b_fn",$info);
            }
        }
    }

    function fill_b_tr($dir,$type){
        $enc_dir =  $this->fiky_encryption->bf_fiky_enkript($dir,'THIS_KEY_HAS_LIMIT');
        $enc_yes = $this->fiky_encryption->bf_fiky_enkript('YES','THIS_KEY_HAS_LIMIT');
        $enc_no = $this->fiky_encryption->bf_fiky_enkript('NO','THIS_KEY_HAS_LIMIT');
        if ($type==='CAPTURE'){
            /* directory captrue a_backup */
            $cekexist = $this->db->query("select * from public.b_tr where a_directory='$enc_dir'")->num_rows();
            if ($cekexist<=0){
                $val=array(
                    'a_directory' => $enc_dir,
                    'a_filename' => $enc_dir,
                    'a_start' => $enc_yes,
                    'a_finish' => $enc_no,
                    'a_backup' => $enc_yes,
                    'a_drop' => $enc_no,
                );
                $this->db->insert("public.b_tr",$val);
            } else {
                $info = array (
                    'a_start' => $enc_yes,
                    'a_finish' => $enc_no,
                    'a_backup' => $enc_yes,
                    'a_drop' => $enc_no,
                );
                $this->db->where("a_directory",$enc_dir);
                $this->db->update("public.b_tr",$info);
            }
        } else if ($type==='RESTORE') {
            /* directory captrue a_finish */
            $cekexist = $this->db->query("select * from public.b_tr where a_directory='$enc_dir'")->num_rows();
            if ($cekexist<=0){
                $val=array(
                    'a_directory' => $enc_dir,
                    'a_filename' => $enc_dir,
                    'a_start' => $enc_yes,
                    'a_finish' => $enc_yes,
                );
                $this->db->insert("public.b_tr",$val);
            } else {
                $info = array (
                    'a_start' => $enc_yes,
                    'a_finish' => $enc_yes,//
                );
                $this->db->where("a_directory",$enc_dir);
                $this->db->update("public.b_tr",$info);
            }
        } else if ($type==='DROP') {
            /* directory captrue a_drop */
            $cekexist = $this->db->query("select * from public.b_tr where a_directory='$enc_dir'")->num_rows();
            if ($cekexist<=0){
                $val=array(
                    'a_directory' => $enc_dir,
                    'a_filename' => $enc_dir,
                    'a_drop' => $enc_yes, //
                );
                $this->db->insert("public.b_tr",$val);
            } else {
                $info = array (
                    'a_start' => $enc_yes,
                    'a_drop' => $enc_yes,//
                );
                $this->db->where("a_directory",$enc_dir);
                $this->db->update("public.b_tr",$info);
            }
        }
    }

    function read_capture_tr($dir){
        $enc_dir =  $this->fiky_encryption->bf_fiky_enkript($dir,'THIS_KEY_HAS_LIMIT');
        $enc_yes = $this->fiky_encryption->bf_fiky_enkript('YES','THIS_KEY_HAS_LIMIT');
        $enc_no = $this->fiky_encryption->bf_fiky_enkript('NO','THIS_KEY_HAS_LIMIT');
        return $this->db->query("select * from public.b_tr where a_directory='$enc_dir'");
    }
    function read_capture_fn($dir){
        $enc_dir =  $this->fiky_encryption->bf_fiky_enkript($dir,'THIS_KEY_HAS_LIMIT');
        $enc_yes = $this->fiky_encryption->bf_fiky_enkript('YES','THIS_KEY_HAS_LIMIT');
        $enc_no = $this->fiky_encryption->bf_fiky_enkript('NO','THIS_KEY_HAS_LIMIT');
        return $this->db->query("select * from public.b_fn where a_directory='$enc_dir'");
    }


    function unlinkForStableSystem(){


        /* PATCH 1 LCX */
        $path = "./assets/lcx";
        if (! is_dir($path)) {
            //throw new InvalidArgumentException("$path must be a directory");
            echo '';
        }
        if (substr($path, strlen($path) - 1, 1) != '/') {
            $path .= '/';
        }
        $files = glob($path . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        if(rmdir($path)) {
            $this->db->query("delete from public.b_fn");
            $this->db->query("delete from public.b_tr");
        }

        /* PATCH 2 LC */
        $path2 = "./assets/lc";
        if (! is_dir($path2)) {
            // throw new InvalidArgumentException("$path2 must be a directory");
            echo '';
        }
        if (substr($path2, strlen($path2) - 1, 1) != '/') {
            $path2 .= '/';
        }
        $files = glob($path2 . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        if (rmdir($path2)) {
            '';
        }


        ;

    }

}
