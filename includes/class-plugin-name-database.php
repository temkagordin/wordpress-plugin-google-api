<?php


class TableDb
{

    public $db;
    public $table_name;

    public function __construct()
    {
        global $wpdb;

        $this->db = $wpdb;
        $this->table_name = $this->db->prefix . "table";


    }

    public function sheetsDb($col, $row, $s_id, $gid_id, $val)
    {
        $sql = "SELECT id,value FROM $this->table_name WHERE `col_char` = '$col' AND `row_num` = '$row' AND `sheet_id` = '$s_id' AND `gid_id` = '$gid_id'";
        $res = $this->db->get_results($sql);

        if (empty($res)) {
            $sql = "INSERT INTO $this->table_name (col_char, row_num, sheet_id, gid_id, value, created_at)
                VALUE ('$col','$row','$s_id','$gid_id','$val',NOW())";
            $res = $this->db->get_results($sql);
        } else {
            $id = $res[0]->id;
            $value = $res[0]->value;
            if ($value != $val) {
                $sql = "UPDATE $this->table_name SET col_char='$col',row_num='$row',sheet_id='$s_id',gid_id='$gid_id',value='$val',updated_at='NOW()' WHERE `id`='$id'";
                $res = $this->db->get_results($sql);
            }
        }
        return $res;
    }

    public function GetSheetInfoById($id)
    {
        $sql="SELECT * FROM $this->table_name WHERE `sheet_id` = $id";
        $res = $this->db->get_results($sql);
        return $res;
    }
    public function GetGidIdInfoBySheetId($id)
    {
        $sql="SELECT DISTINCT  gid_id FROM $this->table_name WHERE sheet_id = '$id'";
        $res = $this->db->get_results($sql);
        return $res;
    }
    public function GetInfoGrid($gid,$sheetid){
        $sql="SELECT col_char,row_num,value FROM $this->table_name WHERE sheet_id = '$sheetid' AND gid_id = '$gid'";
        $res = $this->db->get_results($sql);
        return $res;
    }


}