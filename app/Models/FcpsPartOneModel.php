<?php

namespace App\Models;

use CodeIgniter\Model;

class FcpsPartOneModel extends Model
{
    protected $table      = 'fcps_one_pass_applicants';
    protected $primaryKey = 'id';

    protected $allowedFields = ['date_of_birth', 'mailing_address', 'present_address', 'permanent_address', 'national_id', 'pen_number',
        'subject_id', 'subject', 'contact_res', 'cell', 'tel_office', 'email', 'payment_staus', 'online_trans_ref_no',
        'money_receipt_no', 'payment_amount', 'money_receipt_date', 'training_institute', 'roll',
        'mbbs_year', 'mbbs_institute_id', 'mbbs_institute', 'eligible_for_final_exam', 'training_completion', 'protocol_acceptance', 'dissertaion_acceptance',
        'hashedotp', 'smscounter', 'created_by', 'updated_by', 'updated_at'];

    public function countFilteredData($searchValue = '')
    {
        $builder = $this->db->table('fcps_one_pass_applicants ap');
        $builder->join('speciality sp', 'ap.subject_id = sp.speciality_id', 'left');

        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('ap.fcps_part_one_year_old', $searchValue)
                ->orLike('ap.applicant_name', $searchValue)
                ->orLike('ap.father_name', $searchValue)
                ->orLike('ap.reg_no', $searchValue)
                ->orLike('ap.cell', $searchValue)
                ->orLike('ap.pen_number', $searchValue)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }
    public function getData($searchValue = '', $start = 0, $length = 10)
    {
        $builder = $this->db->table('fcps_one_pass_applicants ap');
        $builder->select('ap.id, ap.fcps_part_one_year, ap.fcps_part_one_session, UPPER(ap.applicant_name) as name, UPPER(ap.father_name) as father_name, ap.pen_number, ap.reg_no, ap.cell, ap.email');
        $builder->join('speciality sp', 'ap.subject_id = sp.speciality_id', 'left');

        // Apply search filter
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('ap.fcps_part_one_year_old', $searchValue)
                ->orLike('ap.applicant_name', $searchValue)
                ->orLike('ap.father_name', $searchValue)
                ->orLike('ap.reg_no', $searchValue)
                ->orLike('ap.cell', $searchValue)
                ->orLike('ap.pen_number', $searchValue)
                ->groupEnd();
        }

        // Limit and offset
        $builder->limit($length, $start);

        $data['candidates']         = $builder->get()->getResultArray();
        $data['totalRecords']       = $this->db->table('fcps_one_pass_applicants')->countAll();
        $data['totalSearchRecords'] = $this->countFilteredData($searchValue);
        return $data;
    }

    public function getPartOneTraineeById($id)
    {
        $builder = $this->db->table('fcps_one_pass_applicants fcps');
        $builder->select('fcps.*, sp.name AS subject_name');
        $builder->join('speciality sp', 'fcps.subject_id = sp.speciality_id', 'left');
        $builder->where('fcps.id', $id);

        return $builder->get()->getRowArray();
    }

    public function getPartOneTraineeByRegNo($regNo)
    {
        $builder = $this->db->table('fcps_one_pass_applicants fcps');
        $builder->select('fcps.*, sp.name AS subject_name, sp.elogbook');
        $builder->join('speciality sp', 'fcps.subject_id = sp.speciality_id', 'left');
        $builder->where('fcps.reg_no', $regNo);

        return $builder->get()->getRowArray();
    }

    public function getTraineeInfoByParams(array $params = [])
    {
        $builder = $this->db->table('fcps_one_pass_applicants ap');
        $builder->select('ap.id, ap.reg_no, ap.fcps_part_one_year, ap.fcps_part_one_session, UPPER(ap.applicant_name) as name, UPPER(ap.father_name) as father_name,
                            ap.pen_number, ap.reg_no, ap.cell, ap.email, ap.password, ap.smscounter, ap.hashedotp');
        $builder->join('speciality sp', 'ap.subject_id = sp.speciality_id', 'left');

        // Apply dynamic where conditions
        foreach ($params as $key => $value) {
            if ($value !== null && $value !== '') {
                // Automatically add table alias if not included
                if (!str_contains($key, '.')) {
                    if ($key == 'cell') {
                        $key = 'LPAD(ap.' . $key . ',11,"0")';
                    } else {
                        $key = 'ap.' . $key;
                    }
                }
                $builder->where($key, $value);
            }
        }

        return $builder->get()->getRowArray(); // ✅ returns single row
    }

    public function checkPaymentInfo($penNumber)
    {
        return $this->db->table('fcps_one_pass_applicants ap')
            ->where('ap.pen_number', $penNumber)
            ->where('ap.money_receipt_no >', 0)
            ->countAllResults();
    }

    public function getPaymentInfoWithPenNo($penNumber)
    {
        return $this->db->table('fcps_one_pass_applicants ap')
            ->select('ap.fcps_part_one_year, ap.fcps_part_one_session, ap.pen_number, ap.money_receipt_no,
                        ap.money_receipt_date, ap.reg_no, ap.applicant_name, ap.subject_id, ap.bmdc_reg_no')
            ->where('ap.pen_number', $penNumber)
            ->get()
            ->getRowArray();
    }

}
