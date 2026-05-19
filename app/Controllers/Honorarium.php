<?php

namespace App\Controllers;

use App\Models\BankModel;
use App\Models\HonorariumInformationModel;
use App\Models\HonorariumPreviousTrainingModel;
use App\Models\HonorariumSlotModel;
use App\Models\InstituteModel;
use App\Models\SpecialityModel;
use App\Models\TrainingCategoryModel;
use Dompdf\Dompdf;

class Honorarium extends BaseController
{
    protected $honorariumModel;
    protected $specialityModel;
    protected $HonorariumSlotModel;
    protected $instituteModel;
    protected $bankModel;
    protected $trainingCategoryModel;
    protected $honorariumPrevTrainingModel;

    public function __construct()
    {
        $this->honorariumModel             = new HonorariumInformationModel();
        $this->specialityModel             = new SpecialityModel();
        $this->HonorariumSlotModel         = new HonorariumSlotModel();
        $this->instituteModel              = new InstituteModel();
        $this->bankModel                   = new BankModel();
        $this->trainingCategoryModel       = new TrainingCategoryModel();
        $this->honorariumPrevTrainingModel = new HonorariumPreviousTrainingModel();
    }

    public function index()
    {
        // Check if the authenticated user has the 'bills.index' permission
        if (!auth()->user()->can('bills.index')) {
            // User does not have permission, so deny access.
            //return redirect()->back()->with('error', 'You are not authorized to edit posts.');
            //return redirect()->to('/403');
            return redirect()->to('/403')->with('error', 'You are not authorized to view list.');
        }

        $data = [
            'title'       => 'Honorarium',
            'pageTitle'   => 'Bills Information',
            //'slots'       => $this->honorariumModel->getSlots(),
            'slots'       => array(
                ['id' => 1, 'slot_name' => 'January - June'],
                ['id' => 2, 'slot_name' => 'July - December'],
            ),
            'honorariums' => $this->honorariumModel->getHonorariums(),
        ];

        //dd($data['slots']);

        return view('Honorarium/index', $data);
    }

    public function getStatistics()
    {
        $request    = service('request');
        $jsonParams = $request->getJSON();

        // Access data
        $honorariumYear    = $jsonParams->honorariumYear ?? null;
        $honorariumSession = $jsonParams->honorariumSession ?? null;

        $statistics = $this->honorariumModel->getStatistics($honorariumYear, $honorariumSession);

        $data = [
            'labels' => array_keys($statistics),
            'values' => array_values($statistics),
        ];

        echo json_encode($data);
        exit;
    }

    public function getSearchedHonorariums()
    {
        $request = service('request');

        // Get DataTables parameters
        $draw              = $request->getPost('draw');
        $start             = $request->getPost('start');
        $length            = $request->getPost('length');
        $searchValue       = $request->getPost('search')['value'];
        $honorariumYear    = $request->getPost('honorariumYear');
        $honorariumSession = $request->getPost('honorariumSession');

        // Fetch data from model
        $data          = $this->honorariumModel->getHonorariums($searchValue, $start, $length, $honorariumYear, $honorariumSession);
        $totalRecords  = $this->honorariumModel->countAllHonorariums($honorariumYear, $honorariumSession);
        $totalFiltered = $this->honorariumModel->countFilteredHonorariums($searchValue, $honorariumYear, $honorariumSession);

        $response = [
            "draw"            => intval($draw),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ];

        return $this->response->setJSON($response);
    }

    public function approveHonorarium()
    {
        // Check if the authenticated user has the 'bills.approve' permission
        if (!auth()->user()->can('bills.approve')) {
            // User does not have permission, so deny access.
            //return redirect()->back()->with('error', 'You are not authorized to edit posts.');
            //return redirect()->to('/403');
            //return redirect()->to('/403')->with('error', 'You are not authorized to approve bills.');
            return $this->response->setJSON(['status' => 'error', 'message' => 'You are not authorized to approve bills.']);
        }

        $request = service('request');

        $honorariumId = $request->getPost('honorariumId');

        $isApproved = $this->honorariumModel->approveHonorarium($honorariumId);

        if ($isApproved) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Approved successfully.']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to approve.']);
        }
    }

    public function rejectHonorarium()
    {
        // Check if the authenticated user has the 'bills.approve' permission
        if (!auth()->user()->can('bills.reject')) {
            // User does not have permission, so deny access.
            //return redirect()->back()->with('error', 'You are not authorized to edit posts.');
            //return redirect()->to('/403');
            //return redirect()->to('/403')->with('error', 'You are not authorized to approve bills.');
            return $this->response->setJSON(['status' => 'error', 'message' => 'You are not authorized to reject bills.']);
        }

        $request = service('request');

        $honorariumId = $request->getPost('honorariumId');
        $rejectReason = $request->getPost('rejectReason');

        $isRejected = $this->honorariumModel->rejectHonorarium($honorariumId, $rejectReason);

        if ($isRejected) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Rejected successfully.']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to reject applicant.']);
        }
    }

    public function getHonorarium($id)
    {
        $honorarium = $this->honorariumModel->getHonorarium($id);

        $data = [
            'title'      => 'Bill Details',
            'honorarium' => $honorarium,
        ];

        if ($honorarium) {
            return view('Honorarium/view_details', $data);
        } else {
            echo 'Honorarium not found.';
        }
    }

    public function getBillInfo($id)
    {
        $honorarium = $this->honorariumModel->getHonorarium($id);

        $data = [
            'title'      => 'Bill Details',
            'speciality' => $this->specialityModel->findAll(),
            'slots'      => $this->HonorariumSlotModel->findAll(),
            'institute'  => $this->instituteModel->where('status', true)->where('honorarium_status', true)->findAll(),
            'banks'      => $this->bankModel->findAll(),
            'honorarium' => $honorarium,
        ];

        if ($honorarium) {
            return view('Honorarium/edit', $data);
        } else {
            echo 'Honorarium not found.';
        }
    }

    public function getFilesInfo()
    {
        $request = service('request');

        $applicationId = $request->getPost('applicationId');

        $files = $this->honorariumModel->getAttachements($applicationId);

        return view('Honorarium/view-attachments', ['files' => $files]);
    }

    public function downloadHonorariumForm($honorariumId)
    {
        //$request      = service('request');
        //$honorariumId = $request->getPost('honorariumId');
        $honorarium = $this->honorariumModel->getHonorarium($honorariumId);

        //return view('Honorarium/pdf_form', ['honorarium' => $honorarium]);

        if ($honorarium) {
            $dompdf = new Dompdf();
            $html   = view('Honorarium/pdf_form', ['honorarium' => $honorarium]);
            $dompdf->setOptions(new \Dompdf\Options(['isRemoteEnabled' => true]));
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            //$dompdf->stream('honorarium_' . $honorarium['bmdc_reg_no'] . $honorariumId . '.pdf', ['Attachment' => true]);

            $filename = 'honorarium_' . $honorarium['bmdc_reg_no'] . $honorariumId . '.pdf';

            //IMPORTANT HEADERS
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            echo $dompdf->output();
            exit; //VERY IMPORTANT

        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Honorarium not found.']);
        }
    }

    public function create()
    {
        $data = [
            'title' => 'Add Honorarium',
        ];

        return view('Honorarium/create', $data);
    }

    public function store()
    {
        $this->honorariumModel->save([
            'honorarium_name'   => $this->request->getPost('honorarium_name'),
            'honorarium_amount' => $this->request->getPost('honorarium_amount'),
            'honorarium_date'   => $this->request->getPost('honorarium_date'),
            'honorarium_status' => $this->request->getPost('honorarium_status'),
        ]);

        return redirect()->to('/honorariums');
    }

    public function edit($id)
    {
        // Check if the authenticated user has the 'bills.approve' permission
        if (!auth()->user()->can('bills.edit')) {
            // User does not have permission, so deny access.
            //return redirect()->to('/403');
            return redirect()->to('/403')->with('error', 'You are not authorized to edit bills.');
            //return $this->response->setJSON(['status' => 'error', 'message' => 'You are not authorized to reject bills.']);
        }

        $data = [
            'title'      => 'Edit Honorarium',
            'honorarium' => $this->model->find($id),
        ];

        return view('Honorarium/edit', $data);
    }

    public function update($honorariumId)
    {
        // Check if the authenticated user has the 'bills.approve' permission
        if (!auth()->user()->can('bills.update')) {
            // User does not have permission, so deny access.
            //return redirect()->to('/403');
            return redirect()->to('/403')->with('error', 'You are not authorized to update bills.');
            //return $this->response->setJSON(['status' => 'error', 'message' => 'You are not authorized to update bills.']);
        }

        $request   = service('request');
        $updatedId = $request->getPost('honorariumId');

        if (!$updatedId) {
            return redirect()->back()->with('error', 'Invalid applicant ID.');
        }

        $department = $this->specialityModel->where('name', $request->getPost('department'))->first();

        if ($department) {
            $departmentId = $department['speciality_id'];
        } else {
            $departmentId = '';
        }

        // Update honorarium information
        $data = [
            'training_institute_id'     => $request->getPost('trainingInstitute'),
            'department_id'             => $departmentId,
            'department_name'           => $request->getPost('department'),
            'previous_training_inmonth' => $request->getPost('previousTrainingPeriod'),
            'current_training_slot'     => $request->getPost('currentTrainingSlot'),
            'updated_at'                => date('Y-m-d H:i:s'),
            'updated_by'                => service('auth')->user()->id,
        ];

        if ($this->honorariumModel->update($updatedId, $data)) {
            return redirect()->to('/bills')->with('success', 'Basic Information updated successfully.');
        } else {
            return redirect()->back()->with('errors', 'Failed to update honorarium information.');
        }
    }

    public function getHonorariumTrainings($honorariumId)
    {
        $honorarium = $this->honorariumModel->getHonorarium($honorariumId);
        //$honorariumTrainings = $this->honorariumModel->getHonorariumTrainings($honorariumId);
        $honorariumTrainings = $this->honorariumModel->getPreviousTrainings($honorarium['applicant_id']);

        $data = [
            'title'               => 'Bill Details',
            'honorarium'          => $honorarium,
            'honorariumTrainings' => $honorariumTrainings,
        ];

        return view('Honorarium/view_training_details', $data);

    }

    public function getHonorariumTrainingInfo($honorariumId)
    {
        $honorarium = $this->honorariumModel->getHonorarium($honorariumId);

        $data = [
            'title'             => 'Bill Details',
            'specialities'      => $this->specialityModel->findAll(),
            'institutes'        => $this->instituteModel->where('status', true)->findAll(),
            'categories'        => $this->trainingCategoryModel->findAll(),
            'honorarium'        => $honorarium,
            //'previousTrainings' => $this->honorariumModel->getHonorariumTrainings($honorariumId),
            'previousTrainings' => $this->honorariumModel->getPreviousTrainings($honorarium['applicant_id']),
        ];

        if ($honorarium) {
            return view('Honorarium/edit_prev_training', $data);
        } else {
            echo 'Honorarium not found.';
        }
    }

    public function updateHonorariumTrainingInfo($honorariumId)
    {
        // Check if the authenticated user has the 'bills.approve' permission
        if (!auth()->user()->can('bills.training.update')) {
            // User does not have permission, so deny access.
            //return redirect()->to('/403');
            return redirect()->to('/403')->with('error', 'You are not authorized to update training of bills.');
            //return $this->response->setJSON(['status' => 'error', 'message' => 'You are not authorized to update bills.']);
        }

        $request = service('request');

        // Get previous training record IDs
        $prevTrainingRecordIds = $request->getPost('prevTrainingRecordId');

        /*echo '<pre>';
        print_r($prevTrainingRecordIds);
        echo '</pre>';
        die;*/
        //Removed previous training records
        $this->honorariumPrevTrainingModel
            ->where('honorarium_id', $honorariumId)
            ->whereNotIn('id', $prevTrainingRecordIds)
            ->delete();

        foreach ($prevTrainingRecordIds as $index => $recordId) {
            if ($recordId == '') {
                $tdata = [
                    'honorarium_id'         => $honorariumId,
                    'slot_sl_no'            => $request->getPost('prevTrainingSlot')[$index],
                    'training_from'         => $request->getPost('prevTrainingFromDt')[$index],
                    'training_to'           => $request->getPost('prevTrainingToDt')[$index],
                    'training_institute_id' => $request->getPost('prevTrainingInstitute')[$index],
                    'speciality_id'         => $request->getPost('prevTrainingDepartment')[$index],
                    'training_category_id'  => $request->getPost('prevTrainingCategory')[$index],
                    'honorarium_taken'      => $request->getPost('prevTrainingHonorariumTaken')[$index],
                    'created_at'            => date('Y-m-d H:i:s'),
                ];

                $this->honorariumPrevTrainingModel->insert($tdata);
            } else {
                $tdata = [
                    'id'                    => $recordId, // null for new inserts
                    'honorarium_id'         => $honorariumId,
                    'slot_sl_no'            => $request->getPost('prevTrainingSlot')[$index],
                    'training_from'         => $request->getPost('prevTrainingFromDt')[$index],
                    'training_to'           => $request->getPost('prevTrainingToDt')[$index],
                    'training_institute_id' => $request->getPost('prevTrainingInstitute')[$index],
                    'speciality_id'         => $request->getPost('prevTrainingDepartment')[$index],
                    'training_category_id'  => $request->getPost('prevTrainingCategory')[$index],
                    'honorarium_taken'      => $request->getPost('prevTrainingHonorariumTaken')[$index],
                ];

                $this->honorariumPrevTrainingModel->update($recordId, $tdata);
            }
        }

        // Update applicant information
        $hdata = [
            'previous_training_inmonth' => $request->getPost('previousTrainingPeriod'),
            'updated_at'                => date('Y-m-d H:i:s'),
            'updated_by'                => service('auth')->user()->id,
        ];

        if ($this->honorariumModel->update($honorariumId, $hdata)) {
            return redirect()->to('/bills')->with('success', 'Basic Information updated successfully.');
        } else {
            return redirect()->back()->with('errors', 'Failed to update honorarium information.');
        }
    }

    public function delete($id)
    {
        $this->model->delete($id);

        return redirect()->to('/honorariums');
    }
}