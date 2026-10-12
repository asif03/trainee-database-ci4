<?php

namespace App\Models;

use CodeIgniter\Model;

class JoinTrainingInfoModel extends Model
{
    protected $table      = 'join_training_info';
    protected $primaryKey = 'id';

    public function getJoiningInfoByParams(array $params = [])
    {
        $builder = $this->db->table('join_training_info jn');
        $builder->select('jn.id, jn.inst_id, jn.dept_id, jn.current_slot, jn.applicant_id, jn.submission_year, jn.submission_session');
        $builder->join('fcps_one_pass_applicants ap', 'jn.applicant_id = ap.id', 'inner');

        // Apply dynamic where conditions
        foreach ($params as $key => $value) {
            if ($value !== null && $value !== '') {
                // Automatically add table alias if not included
                if (!str_contains($key, '.')) {
                    $key = 'jn.' . $key;
                }
                $builder->where($key, $value);
            }
        }

        //dd($builder->getCompiledSelect()); // Debug: Output the compiled SQL query

        return $builder->get()->getRowArray(); // ✅ returns single row
    }
}
