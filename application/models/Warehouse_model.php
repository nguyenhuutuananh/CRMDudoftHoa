<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Warehouse_model extends CRM_Model
{
    private $statuses;
    function __construct()
    {
        parent::__construct();
    }
    public function get_products($id_category) {
        $this->db->where('category_id', $id_category);
        return $this->db->get('tblitems')->result_array();
    }
    function get_full($id_warehouse) {
        if(is_numeric($id_warehouse)) {
            $this->db->where('warehouseid', $id_warehouse);
            $warehouse = $this->db->get('tblwarehouses')->row();
            if($warehouse) {
                $this->db->from('tblwarehouses_products');
                $this->db->where('warehouse_id', $warehouse->warehouseid);
                $this->db->join('tblitems', 'product_id = tblitems.id' ,'left');
                $warehouse->detail = $this->db->get()->result();

                return $warehouse;
            }
        }
        return false;
    }
    function get_products_in_warehouse($id_warehouse) {
        if(is_numeric($id_warehouse)) {
            $this->db->where('warehouse_id', $id_warehouse);
            $this->db->join('tblitems', 'tblitems.id = tblwarehouses_products.product_id', 'left');
            return $this->db->get('tblwarehouses_products')->result_array();
        }
        return false;
    }
    /**
     * Get task by id
     * @param  mixed $id task id
     * @return object
     */
    public function get_roles()
    {
        $is_admin = is_admin();
        $roles = $this->db->get('tblroles')->result_array();
        return $roles;
    }
    public function add_warehouse($data)
    {
        if (is_admin()) {
            $this->db->insert('tblwarehouses',$data);
            if ($this->db->affected_rows() >0) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function update_warehouse($data_vestion,$id)
    {
        if (is_admin()) {
            // var_dump($data_vestion);die();
            $this->db->where('warehouseid',$id);
            $this->db->update('tblwarehouses',$data_vestion);
            if ($this->db->affected_rows() >0) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function delete_warehouse($id)
    {
        if (is_admin()) {
            $this->db->where('warehouseid', $id);
            $this->db->delete('tblwarehouses');
            if ($this->db->affected_rows() > 0) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function get_row_warehouse($id)
    {
        if (is_admin()) {
            $this->db->select('tblwarehouses.*');
            $this->db->where('tblwarehouses.warehouseid', $id);
            return $this->db->get('tblwarehouses')->row();
        }
    }

//    public function get_call_logs_and_staff($id, $where = array())
//    {
//        $this->db->where('c.ID', $id);
//        $this->db->join('tblstaff s', 'c.assigned = s.staffid', 'left');
//        $this->db->select('s.*');
//        $this->db->from('tblcall_logs c');
////        $task = $this->db->get('tblcall_logs')->row();
////        $task->assignees       = $this->get_task_assignees($id);
//        return $this->db->get()->result_array();
//    }
//    public function get_task_assignees($id)
//    {
//        $this->db->select('id,tblstafftaskassignees.staffid as assigneeid,assigned_from,firstname,lastname');
//        $this->db->from('tblstafftaskassignees');
//        $this->db->join('tblstaff', 'tblstaff.staffid = tblstafftaskassignees.staffid', 'left');
//        $this->db->where('taskid', $id);
//        return $this->db->get()->result_array();
//    }
//    public function get_all_assignees()
//    {
//        $this->db->select('*');
//        $this->db->from('tblstaff');
////        $this->db->join('tblstaff', 'tblstaff.staffid = tblstafftaskassignees.staffid', 'left');
////        $this->db->where('taskid', $id);
//        return $this->db->get()->result_array();
//    }
//

//
//
//    public function remove_assignee($id, $taskid)
//    {
//        $task = $this->get($taskid);
//        $this->db->where('id', $id);
//        $assignee_data = $this->db->get('tblstafftaskassignees')->row();
//        $this->db->where('id', $id);
//        $this->db->delete('tblstafftaskassignees');
//        if ($this->db->affected_rows() > 0) {
//            if ($task->rel_type == 'project') {
//                $this->projects_model->log_activity($task->rel_id, 'project_activity_task_assignee_removed', $task->name . ' - ' . get_staff_full_name($assignee_data->staffid), $task->visible_to_client);
//            }
//            return true;
//        }
//        return false;
//    }
//
//
//
//    public function update($data,$id)
//    {
//        if (is_admin()) {
//            $this->db->where('ID', $id);
//            if(!isset($data['checkout']))
//            {
//                $data['checkout']=0;
//            }
//            $data['assigned']=implode(',',$data['assigned']);
//            $this->db->update('tblcall_logs',$data);
//            if ($this->db->affected_rows() >0) {
////                logActivity('Reminder Deleted [' . ucfirst($reminder->rel_type) . 'ID: ' . $reminder->id . ' Description: ' . $reminder->description . ']');
//                return true;
//            }
//            return false;
//        }
//        return false;
//    }
//    public function add($data,$idlead)
//    {
//        if (is_admin()) {
//            if(!isset($data['checkout']))
//            {
//                $data['checkout']=0;
//            }
//            $data['id_lead']=$idlead;
//            $data['assigned']=implode(',',$data['assigned']);
//            $this->db->insert('tblcall_logs',$data);
//            if ($this->db->affected_rows() >0) {
//                return true;
//            }
//            return false;
//        }
//        return false;
//    }
}
