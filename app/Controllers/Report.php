<?php

namespace App\Controllers;

use App\Models\ApplicantInformationModel;
use App\Models\BankModel;
use App\Models\HonorariumInformationModel;
use App\Models\HonorariumSlotModel;
use App\Models\InstituteModel;
use App\Models\SpecialityModel;
use Config\App;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Report extends BaseController
{
    protected $applicantInformationModel;
    protected $honorariumModel;
    protected $specialityModel;
    protected $HonorariumSlotModel;
    protected $instituteModel;
    protected $bankModel;

    public function __construct()
    {
        $this->honorariumModel           = new HonorariumInformationModel();
        $this->specialityModel           = new SpecialityModel();
        $this->HonorariumSlotModel       = new HonorariumSlotModel();
        $this->instituteModel            = new InstituteModel();
        $this->bankModel                 = new BankModel();
        $this->applicantInformationModel = new ApplicantInformationModel();
    }

    public function applications()
    {
        $data = [
            'title'           => 'Application Report',
            'pageTitle'       => 'Application Report',
            'pageDescription' => 'Generate reports for applications.',
        ];

        return view('Reports/application', $data);
    }

    public function bills()
    {
        $data = [
            'title'      => 'Bill Report',
            'pageTitle'  => 'Bill Report',
            'institutes' => $this->instituteModel->where('status', true)->where('honorarium_status', true)->findAll(),
            'slots'      => $this->honorariumModel->getSlots(),
        ];

        return view('Reports/bill', $data);
    }

    public function getBillInfo()
    {
        $where = [];

        $trainingInstitute = $this->request->getPost('trainingInstitute');
        $eligibleStatus    = $this->request->getPost('eligibleStatus');

        $where['hi.honorarium_year']    = $this->request->getPost('honorariumYear');
        $where['hi.honorarium_slot_id'] = $this->request->getPost('honorariumSession');

        if ($trainingInstitute) {
            $where['hi.training_institute_id'] = $trainingInstitute;
        }
        if ($eligibleStatus) {
            $where['hi.eligible_status'] = $eligibleStatus;
        }

        // Fetching additional data for each user
        $data = $this->honorariumModel->getBillInfos($where);

        return $this->response->setJSON([
            'data' => $data,
        ]);
    }

    public function exportBillToExcel()
    {
        $where = [];

        $honorariumYear    = $this->request->getPost('honorariumYear');
        $honorariumSlot    = $this->request->getPost('honorariumSession');
        $trainingInstitute = $this->request->getPost('trainingInstitute');
        $eligibleStatus    = $this->request->getPost('eligibleStatus');

        $where['hi.honorarium_year']    = $this->request->getPost('honorariumYear');
        $where['hi.honorarium_slot_id'] = $this->request->getPost('honorariumSession');
        if ($trainingInstitute) {
            $where['hi.training_institute_id'] = $trainingInstitute;
        }
        if ($eligibleStatus) {
            $where['hi.eligible_status'] = $eligibleStatus;
        }

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        //headers
        $sheet->setCellValue('A1', 'Bill Sl No');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Mobile');
        $sheet->setCellValue('D1', 'BMDC Reg No');
        $sheet->setCellValue('E1', 'Online Reg No');
        $sheet->setCellValue('F1', 'Date of Birth');
        $sheet->setCellValue('G1', 'NID');
        $sheet->setCellValue('H1', 'FCPS Speciallity');
        $sheet->setCellValue('I1', 'FCPS Session');
        $sheet->setCellValue('J1', 'FCPS Year');
        $sheet->setCellValue('K1', 'Gander');
        $sheet->setCellValue('L1', 'Institute Name');
        $sheet->setCellValue('M1', 'Department Name');
        $sheet->setCellValue('N1', 'Total Previous Training');
        $sheet->setCellValue('O1', 'Current Training Slot');
        $sheet->setCellValue('P1', 'Applied for Hornorarium');
        $sheet->setCellValue('Q1', 'Bank Name');
        $sheet->setCellValue('R1', 'Branch Name');
        $sheet->setCellValue('S1', 'Account No');
        $sheet->setCellValue('T1', 'Routing No');
        $sheet->setCellValue('U1', 'Honorarium Year');
        $sheet->setCellValue('V1', 'Honorarium Session');
        $sheet->setCellValue('W1', 'Eligible Status');

        // Fetching additional data for each user
        $data = $this->honorariumModel->getBillInfos($where);

        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item['bill_sl_no']);
            $sheet->setCellValue('B' . $row, $item['name']);
            $sheet->setCellValue('C' . $row, $item['mobile']);
            $sheet->setCellValue('D' . $row, $item['bmdc_reg_no']);
            $sheet->setCellValueExplicit('E' . $row, $item['fcps_reg_no'], DataType::TYPE_STRING);
            $sheet->setCellValue('F' . $row, $item['date_of_birth']);
            $sheet->setCellValueExplicit('G' . $row, $item['nid'], DataType::TYPE_STRING);
            $sheet->setCellValue('H' . $row, $item['fcps_speciallity']);
            $sheet->setCellValue('I' . $row, $item['fcps_month']);
            $sheet->setCellValue('J' . $row, $item['fcps_year']);
            $sheet->setCellValue('K' . $row, $item['gander']);
            $sheet->setCellValue('L' . $row, $item['training_institute_name']);
            $sheet->setCellValue('M' . $row, $item['department_name']);
            $sheet->setCellValue('N' . $row, $item['previous_training_inmonth']);
            $sheet->setCellValue('O' . $row, $item['current_training_slot']);
            $sheet->setCellValue('P' . $row, $item['honorarium_position']);
            $sheet->setCellValue('Q' . $row, $item['new_bank_name']);
            $sheet->setCellValue('R' . $row, $item['branch_name']);
            $sheet->setCellValueExplicit('S' . $row, $item['account_no'], DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('T' . $row, $item['routing_number'], DataType::TYPE_STRING);
            $sheet->setCellValue('U' . $row, $item['honorarium_year']);
            $sheet->setCellValue('V' . $row, $item['slot_name']);

            // Set eligible status
            if ($item['eligible_status'] == 'Y') {
                $item['eligible_status'] = 'Eligible';
            } elseif ($item['eligible_status'] === 'N') {
                $item['eligible_status'] = 'Rejected';
            } else {
                $item['eligible_status'] = 'Pending';
            }

            $sheet->setCellValue('W' . $row, $item['eligible_status']);

            $row++;
        }

        // Output file
        $filename = 'Report_' . $honorariumYear . $honorariumSlot . '_' . date('Y-m-d h:i:sa') . '.xlsx';

        // Send headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        exit;
    }

    public function getApplicationInfo()
    {
        $where = [];

        $fcpsYear       = $this->request->getPost('fcpsYear');
        $fcpsSession    = $this->request->getPost('fcpsSession');
        $eligibleStatus = $this->request->getPost('eligibleStatus');

        if ($fcpsYear) {
            $where['ap.fcps_year'] = $fcpsYear;
        }
        if ($fcpsSession) {
            $where['ap.fcps_month'] = $fcpsSession;
        }
        if ($eligibleStatus) {
            $where['ap.eligible_status'] = $eligibleStatus;
        }

        // Fetching additional data for each user
        $data = $this->applicantInformationModel->getApplicationInfos($where);

        return $this->response->setJSON([
            'data' => $data,
        ]);
    }

    public function exportApplicationToExcel()
    {
        $where = [];

        $fcpsYear       = $this->request->getPost('fcpsYear');
        $fcpsSession    = $this->request->getPost('fcpsSession');
        $eligibleStatus = $this->request->getPost('eligibleStatus');

        if ($fcpsYear) {
            $where['ap.fcps_year'] = $fcpsYear;
        }
        if ($fcpsSession) {
            $where['ap.fcps_month'] = $fcpsSession;
        }
        if ($eligibleStatus) {
            $where['ap.eligible_status'] = $eligibleStatus;
        }

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        //headers
        $sheet->setCellValue('A1', 'Name');
        $sheet->setCellValue('B1', 'Father Name');
        $sheet->setCellValue('C1', 'Mother Name');
        $sheet->setCellValue('D1', 'Date of Birth');
        $sheet->setCellValue('E1', 'NID');
        $sheet->setCellValue('F1', 'Address');
        $sheet->setCellValue('G1', 'Mobile');
        $sheet->setCellValue('H1', 'Email');
        $sheet->setCellValue('I1', 'BMDC Reg No');
        $sheet->setCellValue('J1', 'BMDC Validity');
        $sheet->setCellValue('K1', 'Online Reg No');
        $sheet->setCellValue('L1', 'FCPS Speciallity');
        $sheet->setCellValue('M1', 'FCPS Session');
        $sheet->setCellValue('N1', 'FCPS Year');
        $sheet->setCellValue('O1', 'Gander');
        $sheet->setCellValue('P1', 'Bank Name');
        $sheet->setCellValue('Q1', 'Branch Name');
        $sheet->setCellValue('R1', 'Account No');
        $sheet->setCellValue('S1', 'Routing No');
        $sheet->setCellValue('T1', 'Eligible Status');
        //$sheet->setCellValue('T1', 'Remarks');

        // Fetching additional data for each user
        $data = $this->applicantInformationModel->getApplicationInfos($where);

        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item['name']);
            $sheet->setCellValue('B' . $row, $item['father_spouse_name']);
            $sheet->setCellValue('C' . $row, $item['mother_name']);
            $sheet->setCellValue('D' . $row, $item['date_of_birth']);
            $sheet->setCellValueExplicit('E' . $row, $item['nid'], DataType::TYPE_STRING);
            $sheet->setCellValue('F' . $row, $item['address']);
            $sheet->setCellValue('G' . $row, $item['mobile']);
            $sheet->setCellValue('H' . $row, $item['email']);
            $sheet->setCellValue('I' . $row, $item['bmdc_reg_no']);
            $sheet->setCellValue('J' . $row, $item['bmdc_validity']);
            $sheet->setCellValueExplicit('K' . $row, $item['fcps_reg_no'], DataType::TYPE_STRING);
            $sheet->setCellValue('L' . $row, $item['fcps_speciallity']);
            $sheet->setCellValue('M' . $row, $item['fcps_month']);
            $sheet->setCellValue('N' . $row, $item['fcps_year']);
            $sheet->setCellValue('O' . $row, $item['gander']);
            $sheet->setCellValue('P' . $row, $item['bank_name']);
            $sheet->setCellValue('Q' . $row, $item['branch_name']);
            $sheet->setCellValueExplicit('R' . $row, $item['account_no'], DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('S' . $row, $item['routing_number'], DataType::TYPE_STRING);
            // Set eligible status
            if ($item['eligible_status'] == 'Y') {
                $item['eligible_status'] = 'Eligible';
            } elseif ($item['eligible_status'] === 'N') {
                $item['eligible_status'] = 'Rejected';
            } else {
                $item['eligible_status'] = 'Pending';
            }

            $sheet->setCellValue('T' . $row, $item['eligible_status']);

            $row++;
        }

        // Output file
        $filename = 'Application' . '_' . date('Y-m-d h:i:sa') . '.xlsx';

        // Send headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        exit;
    }

}
