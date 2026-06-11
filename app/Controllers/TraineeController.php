<?php

namespace App\Controllers;

use App\Models\ApplicantFileModel;
use App\Models\ApplicantInformationModel;
use App\Models\BankModel;
use App\Models\DesignationModel;
use App\Models\FcpsPartOneModel;
use App\Models\HonorariumInformationModel;
use App\Models\HonorariumPreviousTrainingModel;
use App\Models\HonorariumSlotModel;
use App\Models\InstituteModel;
use App\Models\MbbsInstituteModel;
use App\Models\MidTermModel;
use App\Models\ProgressReportModel;
use App\Models\SpecialityModel;
use App\Models\SupervisorModel;
use App\Models\TrainingCategoryModel;

class TraineeController extends BaseController
{
    protected $trainingInstituteModel;
    protected $mbbsInstituteModel;
    protected $specialityModel;
    protected $designationModel;
    protected $progressReportModel;
    protected $supervisorModel;
    protected $fcpsPartOneModel;
    protected $applicantInformationModel;
    protected $honorariumSlotModel;
    protected $bankModel;
    protected $trainingCategoryModel;
    protected $honorariumInformationModel;
    protected $honorariumPreviousTrainingModel;
    protected $applicantFileModel;
    protected $midTermModel;
    protected $db;

    public function __construct()
    {
        $this->trainingInstituteModel          = new InstituteModel();
        $this->mbbsInstituteModel              = new MbbsInstituteModel();
        $this->specialityModel                 = new SpecialityModel();
        $this->designationModel                = new DesignationModel();
        $this->progressReportModel             = new ProgressReportModel();
        $this->supervisorModel                 = new SupervisorModel();
        $this->fcpsPartOneModel                = new FcpsPartOneModel();
        $this->applicantInformationModel       = new ApplicantInformationModel();
        $this->bankModel                       = new BankModel();
        $this->honorariumSlotModel             = new HonorariumSlotModel();
        $this->trainingCategoryModel           = new TrainingCategoryModel();
        $this->honorariumInformationModel      = new HonorariumInformationModel();
        $this->honorariumPreviousTrainingModel = new HonorariumPreviousTrainingModel();
        $this->applicantFileModel              = new ApplicantFileModel();
        $this->midTermModel                    = new MidTermModel();
        $this->db                              = \Config\Database::connect();
    }

    public function traineeBasicInfo()
    {
        // Check if the authenticated user has the 'trainee.basic.info' permission
        if (!auth()->user()->can('trainee.basic.info')) {
            // User does not have permission, so deny access.
            //return redirect()->back()->with('error', 'You are not authorized to edit posts.');
            //return redirect()->to('/403');
            return redirect()->to('/403')->with('error', 'You are not authorized to access this information.');
        }

        $data['basicInfo'] = $this->fcpsPartOneModel->getPartOneTraineeByRegNo(auth()->user()->username);

        //dd($data);

        return view('Trainee/basic-info', $data);

    }

    public function traineeBasicInfoUpdate()
    {
        // Check if the authenticated user has the 'trainee.basic.info.update' permission
        if (!auth()->user()->can('trainee.basic.info.update')) {
            // User does not have permission, so deny access.
            //return redirect()->back()->with('error', 'You are not authorized to edit posts.');
            //return redirect()->to('/403');
            return redirect()->to('/403')->with('error', 'You are not authorized to access this information.');
        }

        $rules = [
            'cell' => [
                'rules'  => 'required|regex_match[/^01[3-9]\d{8}$/]',
                'errors' => [
                    'required'    => 'Mobile number is required.',
                    'regex_match' => 'Please enter a valid mobile number with 11 digits.',
                ],
            ],
        ];

        $data = $this->request->getPost(array_keys($rules));

        if (!$this->validateData($data, $rules)) {
            return $this->traineeBasicInfo();
        }

        $validData = $this->validator->getValidated();
        $regNo     = auth()->user()->username;

        $updateData = [
            'cell'       => $validData['cell'],
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => auth()->user()->id,
        ];

        $update = $this->fcpsPartOneModel
            ->where('reg_no', $regNo)
            ->set($updateData)
            ->update();

        if ($update) {

            $applicantInformation = $this->applicantInformationModel->where('fcps_reg_no', $regNo)->first();

            //dd($applicantInformation);

            if ($applicantInformation) {
                $updateApplicationData = [
                    'mobile'     => $validData['cell'],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => auth()->user()->id,
                ];

                $update = $this->applicantInformationModel
                    ->where('fcps_reg_no', $regNo)
                    ->set($updateApplicationData)
                    ->update();
            }

            return redirect()->back()->with('success', 'Information updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Ohh! Something went wrong...!');
        }
    }

    public function getSupervisorsByInstitute($instituteId)
    {
        $data = $this->supervisorModel->where('institute_id', $instituteId)->findAll();

        return $this->response->setJSON($data);
    }

    public function getSupervisorById($supervisorId)
    {
        $data = $this->supervisorModel->find($supervisorId);

        return $this->response->setJSON($data);
    }

    public function createProgressReport()
    {
        // Check if the authenticated user has the 'trainee.progress.reports.create' permission
        if (!auth()->user()->can('trainee.progress.reports.create')) {
            // User does not have permission, so deny access.
            //return redirect()->back()->with('error', 'You are not authorized to edit posts.');
            //return redirect()->to('/403');
            return redirect()->to('/403')->with('error', 'You are not authorized to access this information.');
        }

        helper('form');

        $trainingInstitutes = $this->trainingInstituteModel
            ->where('status', true)
            ->orderBy('name', 'ASC') // or 'DESC'
            ->findAll();
        $data['trainingInstitutes'] = $trainingInstitutes;

        $departments          = $this->specialityModel->where('status', true)->findAll();
        $data['departments']  = $departments;
        $data['specialities'] = $departments;
        $designations         = $this->designationModel
            ->where('status', true)
            ->orderBy('designation', 'ASC') // or 'DESC'
            ->findAll();
        $data['designations'] = $designations;

        return view('Trainee/trainings', $data);

    }

    public function storeProgressReport()
    {
        // Check if the authenticated user has the 'trainee.progress.reports.create' permission
        if (!auth()->user()->can('trainee.progress.reports.create')) {
            // User does not have permission, so deny access.
            //return redirect()->back()->with('error', 'You are not authorized to edit posts.');
            //return redirect()->to('/403');
            return redirect()->to('/403')->with('error', 'You are not authorized to access this information.');
        }

        helper('form');
        $validation = service('validation');

        $rules = [
            'instituteName'         => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'You must choose a Institute.',
                ],
            ],
            'departmentName'        => [
                'label' => 'Department',
                'rules' => 'required',
            ],
            'beds'                  => 'required|is_natural_no_zero',
            'trainees'              => 'required|is_natural',
            'facultyMembers'        => 'required|is_natural',
            'fromDate'              => 'required',
            'toDate'                => 'required',
            'supervisorName'        => 'required_if[supervisor,99999999]',
            'supervisorDesignation' => 'required_if[supervisor,99999999]',
            'supervisorMobile'      => 'required_if[supervisor,99999999]',
            'supervisorSubject'     => 'required_if[supervisor,99999999]',
            'attendance'            => 'required',
            'knowledge'             => 'required',
            'skill'                 => 'required',
            'attitude'              => 'required',
        ];

        $data = $this->request->getPost(array_keys($rules));

        if (!$this->validateData($data, $rules)) {
            return $this->createProgressReport();
        }

        // If you want to get the validated data.
        $validData = $this->validator->getValidated();

        //dd($validData);

        $model  = model(ProgressReportModel::class);
        $reg_no = auth()->user()->username;

        if ($this->request->getPost('supervisor') === '99999999') {
            $supervisorId = $this->supervisorModel->insert([
                'supervisor_name' => $validData['supervisorName'],
                'institute_id'    => $validData['instituteName'],
                'department_id'   => $validData['departmentName'],
                'designation_id'  => $validData['supervisorDesignation'],
                'subject_id'      => $validData['supervisorSubject'],
                'mobile'          => $validData['supervisorMobile'],
                'email'           => $this->request->getPost('supervisorEmail'),
                'mailing_address' => $this->request->getPost('supervisorAddress'),
            ]);
        } else {
            $supervisorId = $this->request->getPost('supervisor');

            $supervisorDesignationDetails = $this->designationModel->find($validData['supervisorDesignation']);
            dd($supervisorDesignationDetails);
        }

        $successId = $model->insert([
            'reg_no'                   => $reg_no,
            'training_institute_id'    => $validData['instituteName'],
            'department_id'            => $validData['departmentName'],
            'no_of_beds'               => $validData['beds'],
            'no_of_trainees'           => $validData['trainees'],
            'no_of_faculty_mem'        => $validData['facultyMembers'],

            'training_start_date'      => $validData['fromDate'],
            'training_end_date'        => $validData['toDate'],
            'countable_duration_month' => 6,

            'supervisor_id'            => $supervisorId,
            //'supervisor_name'          => $validData['supervisorName'],
            'supervisor_designation'   => $validData['supervisorDesignation'],
            //'subject_id'               => $validData['supervisorSubject'],
            //'supervisor_mobile_no'     => $validData['supervisorMobile'],

            'attendance'               => $validData['attendance'],
            'knowledge'                => $validData['knowledge'],
            'skill'                    => $validData['skill'],
            'attitude'                 => $validData['attitude'],
        ]);

        if ($successId) {

            return redirect()->back()->with('success', 'Data saved successfully.');
        } else {
            return redirect()->back()->with('error', 'Ohh! Something went wrong...!');
        }
    }

    public function showProgressReport($reportId)
    {
        $progressReportDetails = $this->progressReportModel->getProgressReportById($reportId);

        $data['progressReport'] = $progressReportDetails;

        return view('Trainee/view-report-details', $data);
    }

    public function editProgressReport($reportId)
    {
        echo 'Asif';
    }

    public function checkApplicationConstraints($regNo)
    {
        $data = [
            'isError'     => false,
            'message'     => '',
            'application' => null,
        ];

        //Training application opens
        $trainingApplicationStatus = env('training.applicaion', 'close');
        if ($trainingApplicationStatus == 'close') {
            $data = [
                'isError'     => true,
                'message'     => 'Training application is not open right now!',
                'application' => null,
            ];

            return $data;
        }

        //Application already exists or not
        $checkTrainingApplication = $this->applicantInformationModel->checkBcpsRegiAlreadyUsed($regNo);
        if ($checkTrainingApplication) {

            $applicant = $this->applicantInformationModel->getApplicantInfoByRegNo($regNo);
            $data      = [
                'isError'     => true,
                'message'     => 'You have already applied for training.',
                'application' => $applicant,
            ];

            return $data;
        }

        $fcpsPartOneInfo = $this->fcpsPartOneModel->getPartOneTraineeByRegNo($regNo);

        //Applicant is passed before 2020
        if ($fcpsPartOneInfo['fcps_part_one_year'] < 2020) {

            $data = [
                'isError'     => true,
                'message'     => 'You are not eligible for application due to the completion of your FCPS Part-I before 2020.',
                'application' => null,
            ];

            return $data;
        }

        //Check E-logbook candidates
        if ($fcpsPartOneInfo['fcps_part_one_year'] >= 2025) {

            $specialityCheck = $this->specialityModel
                ->where([
                    'speciality_id' => $fcpsPartOneInfo['subject_id'],
                    'elogbook'      => 'Y',
                ])
                ->countAllResults();

            if ($specialityCheck > 0) {

                $data = [
                    'isError'     => true,
                    'message'     => 'You are not eligible here for application. Go to <a href="https://eportal.bcps.edu.bd/" target="_blank"><u>e-Logbook</u></a> for application.',
                    'application' => null,
                ];
            }

            return $data;
        }

        return $data;
    }

    public function trainingApplication()
    {
        // Check if the authenticated user has the 'trainee.training.application' permission
        if (!auth()->user()->can('trainee.training.application')) {
            // User does not have permission, so deny access.
            //return redirect()->back()->with('error', 'You are not authorized to edit posts.');
            //return redirect()->to('/403');
            return redirect()->to('/403')->with('error', 'You are not authorized to access this information.');
        }

        $applicantInfos = $this->checkApplicationConstraints(auth()->user()->username);

        //dd($applicantInfos);

        if ($applicantInfos['isError']) {
            return view('Trainee/application-status', $applicantInfos);
        } else {

            helper('form');

            $trainingInstitutes         = $this->trainingInstituteModel->where('status', true)->orderBy('name', 'ASC')->findAll();
            $data['trainingInstitutes'] = $trainingInstitutes;

            $mbbsInstitutes         = $this->mbbsInstituteModel->where('status', true)->orderBy('name', 'ASC')->findAll();
            $data['mbbsInstitutes'] = $mbbsInstitutes;

            $departments          = $this->specialityModel->where('status', true)->orderBy('name', 'ASC')->findAll();
            $data['departments']  = $departments;
            $data['specialities'] = $departments;
            $designations         = $this->designationModel->where('status', true)->orderBy('designation', 'ASC')->findAll();
            $data['designations'] = $designations;

            $banks         = $this->bankModel->where('status', true)->orderBy('bank_name', 'ASC')->findAll();
            $data['banks'] = $banks;

            $generalInfo = $this->fcpsPartOneModel->getPartOneTraineeByRegNo(auth()->user()->username);

            /*dd(auth()->user());
            echo auth()->user()->reg_no;
            dd($generalInfo);*/

            $res = $this->checkApplicationConstraints($generalInfo['reg_no']);

            if (!$res['isError']) {
                $data['response']    = $res;
                $data['generalInfo'] = $generalInfo;
            } else {
                $data['response'] = $res;
            }

            //$data['validation'] = $this->validator;

            return view('Trainee/training-application', $data);
        }
    }

    public function storeTrainingApplication()
    {
        //dd($this->request->getPost());
        // Check if the authenticated user has the 'trainee.training.application' permission
        /*if (!auth()->user()->can('trainee.training.application')) {
        // User does not have permission, so deny access.
        //return redirect()->back()->with('error', 'You are not authorized to edit posts.');
        //return redirect()->to('/403');
        return redirect()->to('/403')->with('error', 'You are not authorized to access this information.');
        }*/

        helper('form');
        $validation = service('validation');

        //dd($this->request->getPost());

        $rules = [
            'dob'           => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Date of birth can\'t be blank.',
                ],
            ],
            'nationality'   => [
                'label' => 'Nationality',
                'rules' => 'required',
            ],
            'gender'        => 'required',
            'nationalID'    => [
                'label' => 'National ID',
                'rules' => 'required',
            ],
            'mobile'        => 'required',
            'email'         => 'required',
            'bankName'      => 'required',
            'bankBranch'    => 'required',
            'accountNumber' => 'required',
            'routingNumber' => 'required',
            'bmdc_reg_type' => [
                'label' => 'BMDC Reg. Type',
                'rules' => 'required',
            ],
            'bmdc_reg_no'   => [
                // Rule format: composite_unique[table_name.first_column,second_column,third_column,...]
                // We are checking if the combination of 'unique_id' AND 'user_type_id' is unique in the 'users' table.
                'rules'  => 'required|composite_unique[applicant_information.bmdc_reg_type,bmdc_reg_no]',
                'errors' => [
                    'composite_unique' => 'The provided BMDC Reg. No. is already in use. Please check and try again.',
                    'required'         => 'The BMDC Reg. No. is required.',
                ],
            ],

            /*'trainees'              => 'required|is_natural',
        'facultyMembers'        => 'required|is_natural',
        'fromDate'              => 'required',
        'toDate'                => 'required',
        'supervisorName'        => 'required',
        'supervisorDesignation' => 'required',
        'supervisorMobile'      => 'required',
        'supervisorSubject'     => 'required',
        'attendance'            => 'required',
        'knowledge'             => 'required',
        'skill'                 => 'required',
        'attitude'              => 'required',*/
        ];

        $data = $this->request->getPost(array_keys($rules));

        if (!$this->validateData($data, $rules)) {
            //return $this->trainingApplication();
            //dd($this->validator->getErrors());
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        //dd($data);

        // If you want to get the validated data.
        $validData = $this->validator->getValidated();

        //dd($validData);

        //$successId = 1;

        //echo $this->request->getPost('religion');
        //die;

        $bcpsRegNo   = auth()->user()->username;
        $generalInfo = $this->fcpsPartOneModel->getPartOneTraineeByRegNo($bcpsRegNo);

        //dd($generalInfo);

        $inputData = [
            'name'                    => $generalInfo['applicant_name'],
            'father_spouse_name'      => $generalInfo['father_name'],
            'mother_name'             => $generalInfo['mother_name'],
            'date_of_birth'           => $validData['dob'],
            'nataionality'            => $validData['nationality'],
            'religion'                => $this->request->getPost('religion'),
            'nid'                     => $validData['nationalID'],
            'gander'                  => $validData['gender'],
            'address'                 => $this->request->getPost('communicationAddress'),
            'mobile'                  => $validData['mobile'],
            'telephone'               => $this->request->getPost('residenceTel'),
            'email'                   => $validData['email'],
            'permanent_address'       => $this->request->getPost('permanentAddress'),
            'continuing'              => $this->request->getPost('residencyStatus'),
            'continuing_start_date'   => $this->request->getPost('residencyStartDate'),
            'continuing_end_date'     => $this->request->getPost('residencyEndDate'),
            'continuing_fcps_traning' => $this->request->getPost('currentFCPSTrainingStatus'),
            'bmdc_reg_type'           => $this->request->getPost('bmdc_reg_type'),
            'bmdc_reg_no'             => $this->request->getPost('bmdc_reg_no'),
            //'bmdc_validity'      => $this->request->getPost('communicationAddress'),
            'speciality_id'           => $generalInfo['subject_id'],
            'fcps_roll'               => $this->request->getPost('fcpsRollNo'),
            'fcps_year'               => $generalInfo['fcps_part_one_year'],
            'fcps_month'              => $generalInfo['fcps_part_one_session'],
            'fcps_reg_no'             => $bcpsRegNo,
            'pen_no'                  => $generalInfo['pen_number'],
            'mbbs_bds_year'           => $this->request->getPost('qualificationYear'),
            'mbbs_institute_id'       => $this->request->getPost('qualificationInstitute'),
            'account_name'            => $generalInfo['applicant_name'],
            'bank_id'                 => $validData['bankName'],
            'branch_name'             => $validData['bankBranch'],
            'account_no'              => $validData['accountNumber'],
            'routing_number'          => $validData['routingNumber'],
        ];

        //dd($inputData);

        $successId = $this->applicantInformationModel->insert($inputData);

        if ($successId) {

            if ($this->request->getPost('currentFCPSTrainingStatus')) {
                $this->db->table('fcps_traning')->insert([
                    'applicant_id'    => $successId,
                    'institute_name'  => $this->request->getPost('currentInstitute'),
                    'department'      => $this->request->getPost('currentDepartment'),
                    'supervisor_name' => $this->request->getPost('supervisorName'),
                    'designation'     => $this->request->getPost('supervisorDesignation'),
                    'start_date'      => $this->request->getPost('startDate'),
                    'end_date'        => $this->request->getPost('endDate'),
                ]);
            }

            // --- STEP 2: HANDLE FILE UPLOADS ---
            $uploadedFiles  = $this->request->getFiles();
            $savedFileNames = [];
            //$uploadPath     = WRITEPATH . 'uploads/applications/';
            $uploadPath = FCPATH . 'public/uploads/honorariums/';

            //dd($uploadedFiles);

            // Ensure the upload directory exists
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            /*echo '<pre>';
            print_r($uploadedFiles);
            echo '</pre>';*/

            // Loop through the expected file fields (enclosure1, enclosure2, etc.)
            foreach ($uploadedFiles as $fieldName => $file) {

                // Check if the file is a valid upload and the upload was successful
                if ($file->isValid() && !$file->hasMoved()) {
                    // Generate a secure, unique name for the file to prevent conflicts
                    $newName = $file->getRandomName();

                    // Move the file from temp storage to your desired folder
                    $file->move($uploadPath, $newName);

                    if ($fieldName == 'signatureFile') {
                        $savedFileNames[] = [
                            'fileType' => 'signature',
                            'fileName' => $newName,
                        ];
                    } elseif ($fieldName == 'photoFile') {
                        $savedFileNames[] = [
                            'fileType' => 'photograph',
                            'fileName' => $newName,
                        ];
                    } elseif ($fieldName == 'fcpsPartIFile') {
                        $savedFileNames[] = [
                            'fileType' => 'letter',
                            'fileName' => $newName,
                        ];
                    } elseif ($fieldName == 'mbbsCertFile') {
                        $savedFileNames[] = [
                            'fileType' => 'certificate',
                            'fileName' => $newName,
                        ];
                    } elseif ($fieldName == 'bmdcRegCertFile') {
                        $savedFileNames[] = [
                            'fileType' => 'registration',
                            'fileName' => $newName,
                        ];
                    } elseif ($fieldName == 'trainingCertFile') {
                        $savedFileNames[] = [
                            'fileType' => 'training_certificate',
                            'fileName' => $newName,
                        ];
                    } elseif ($fieldName == 'chequeBookFile') {
                        $savedFileNames[] = [
                            'fileType' => 'cheque',
                            'fileName' => $newName,
                        ];
                    } elseif ($fieldName == 'nidFile') {
                        $savedFileNames[] = [
                            'fileType' => 'nid_card',
                            'fileName' => $newName,
                        ];
                    } elseif ($fieldName == 'joiningLetterFile') {
                        $savedFileNames[] = [
                            'fileType' => 'other_document1',
                            'fileName' => $newName,
                        ];
                    } elseif ($fieldName == 'otherDocsFile') {
                        $savedFileNames[] = [
                            'fileType' => 'other_document2',
                            'fileName' => $newName,
                        ];
                    }

                    if (isset($newName)) {
                        // Reset for the next file
                        unset($newName);
                    }
                } else if ($file->getError() !== UPLOAD_ERR_NO_FILE) {
                    // Handle actual upload errors (e.g., file size, type)
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => "File upload failed for {$fieldName}: " . $file->getErrorString(),
                    ])->setStatusCode(400);
                }
            }

            /*echo '<pre>';
            print_r($savedFileNames);
            echo '</pre>';*/

            //Save file information in the database
            if (!empty($savedFileNames)) {

                $inputFileData = [];
                foreach ($savedFileNames as $fileData) {
                    $inputFileData[] = [
                        'applicant_id' => $successId,
                        'file_name'    => $fileData['fileName'],
                        'type'         => $fileData['fileType'],
                        'status'       => 1,
                    ];
                }

                //dd($inputFileData);

                $this->applicantFileModel->insertBatch($inputFileData);
            }

            if ($this->request->getPost('hasPreviousTraining') == 'on') {

                $previousInstitutes   = $this->request->getPost('prevInstitute');
                $previousDepartments  = $this->request->getPost('prevDepartment');
                $previousSupervisors  = $this->request->getPost('prevSupervisorName');
                $previousDesignations = $this->request->getPost('prevDesignation');
                $previousFromDates    = $this->request->getPost('prevStartDate');
                $previousToDates      = $this->request->getPost('prevEndDate');

                $inputPreviousTrainingData = [];

                foreach ($previousInstitutes as $index => $institute) {

                    if (!empty($institute)) {
                        $inputPreviousTrainingData[] = [
                            'applicant_id'    => $successId,
                            'inistitute_name' => $institute,
                            'department'      => $previousDepartments[$index],
                            'supervisor_name' => $previousSupervisors[$index],
                            'designation'     => $previousDesignations[$index],
                            'start_date'      => $previousFromDates[$index],
                            'end_date'        => $previousToDates[$index],
                        ];
                    }
                }
                //dd($inputPreviousTrainingData);

                if (!empty($inputPreviousTrainingData)) {
                    $this->db->table('fcps_training_before')->insertBatch($inputPreviousTrainingData);
                }
            }

            //dd($this->request->getPost('hasPreviousTraining'));

            if (!empty($this->request->getPost('futureInstitute'))) {

                $futureInstitutes  = $this->request->getPost('futureInstitute');
                $futureDepartments = $this->request->getPost('futureDepartment');
                $futureFromDates   = $this->request->getPost('futureStartDate');
                $futureToDates     = $this->request->getPost('futureEndDate');

                $inputFutureTrainingData = [];

                foreach ($futureInstitutes as $i => $institute) {

                    if (!empty($institute)) { // ignore empty rows
                        $inputFutureTrainingData[] = [
                            'applicant_id'   => $successId,
                            'institute_name' => $institute,
                            'department'     => $futureDepartments[$i],
                            'start_date'     => $futureFromDates[$i],
                            'end_date'       => $futureToDates[$i],
                        ];
                    }
                }
                //dd($inputFutureTrainingData);

                if (!empty($inputFutureTrainingData)) {
                    $this->db->table('choice_institute')->insertBatch($inputFutureTrainingData);
                }
            }

            return redirect()->back()->with('success', 'Data saved successfully.');
        } else {
            return redirect()->back()->with('error', 'Ohh! Something went wrong...!');
        }

    }

    public function checkHonorariumRestrictions($bcpsRegNo)
    {
        $data = [
            'isError'    => false,
            'message'    => '',
            'honorarium' => null,
        ];

        //Check if the applicant is passed before 2020
        $partOnePassedInfo = $this->fcpsPartOneModel->getPartOneTraineeByRegNo($bcpsRegNo);
        if ($partOnePassedInfo['fcps_part_one_year'] < 2020) {
            $data = [
                'isError'    => true,
                'message'    => 'Before 2020 passed FCPS Part-I applicant are not eligible for honorarium!',
                'honorarium' => null,
            ];
            return $data;
        }

        //Check if applicant is in e-Logbook
        if ($partOnePassedInfo['fcps_part_one_year'] >= 2025 && $partOnePassedInfo['elogbook'] == 'Y') {
            $data = [
                'isError'    => true,
                'message'    => 'You are not eligible here for application. Go to <a href="https://eportal.bcps.edu.bd/" target="_blank"><u>e-Logbook</u></a> for application.',
                'honorarium' => null,
            ];
            return $data;
        }

        //Honorarium application opens
        $honorariumStatus = env('bill.honorarium', 'close');

        /*if ($bcpsRegNo == '2023070760') {
        $honorariumStatus = 'open';
        }*/

        if ($honorariumStatus == 'close') {

            $applicant = $this->applicantInformationModel->getApplicantInfoByRegNo($bcpsRegNo);

            if (!$applicant) {
                $data = [
                    'isError'    => true,
                    'message'    => 'Training application not found! Please apply before submit the bill form. For apply <a class="text-success" href="' . base_url("trainings/training-application") . '"><u>Click Here</u></a>',
                    'honorarium' => null,
                ];
                return $data;
            }

            $where = [
                'hi.applicant_id'       => $applicant['applicant_id'],
                'hi.honorarium_slot_id' => env('bill.currentSlot', 0),
                'hi.honorarium_year'    => env('bill.currentYear', date('Y')),
            ];

            $billInfos = $this->honorariumInformationModel->getBillInfos($where);

            if (count($billInfos) > 0) {

                $slotYear = env('bill.currentSlot', 0) == 1 ? 'January-June, ' . env('bill.currentYear', date('Y')) : 'July-December, ' . env('bill.currentYear', date('Y'));

                $data = [
                    'isError'    => true,
                    'message'    => 'You have already applied for ' . $slotYear,
                    'honorarium' => $billInfos,
                ];

                return $data;
            }

            $data = [
                'isError'    => true,
                'message'    => 'Bill application is not open right now!',
                'honorarium' => null,
            ];

            return $data;
        }

        $applicant                = $this->applicantInformationModel->getApplicantInfoByRegNo($bcpsRegNo);
        $checkTrainingApplication = $this->applicantInformationModel->checkBcpsRegiAlreadyUsed($bcpsRegNo);

        if (!$checkTrainingApplication) {

            $data = [
                'isError'    => true,
                'message'    => 'Training application not found! Please apply before submit the bill form. For apply <a class="text-success" href="' . base_url("trainings/training-application") . '"><u>Click Here</u></a>',
                'honorarium' => null,
            ];
            return $data;
        }

        $where = [
            'hi.applicant_id'       => $applicant['applicant_id'],
            'hi.honorarium_slot_id' => env('bill.currentSlot', 0),
            'hi.honorarium_year'    => env('bill.currentYear', date('Y')),
        ];

        $billInfos = $this->honorariumInformationModel->getBillInfos($where);

        if (count($billInfos) > 0) {

            $slotYear = env('bill.currentSlot', 0) == 1 ? 'January-June, ' : 'July-December, ' . env('bill.currentYear', date('Y'));

            $data = [
                'isError'    => true,
                'message'    => 'You have already applied for ' . $slotYear,
                'honorarium' => $billInfos,
            ];

            return $data;
        }

        return $data;

    }

    public function honorariumBillApplication()
    {
        // Check if the authenticated user has the 'trainee.honorarium.application' permission
        if (!auth()->user()->can('trainee.honorarium.application')) {
            // User does not have permission, so deny access.
            //return redirect()->back()->with('error', 'You are not authorized to edit posts.');
            //return redirect()->to('/403');
            //return redirect()->to('/403')->with('error', 'You are not authorized to approve bills.');
            return $this->response->setJSON(['status' => 'error', 'message' => 'You are not authorized to create bills.']);
        }

        $billInfos = $this->checkHonorariumRestrictions(auth()->user()->username);

        //dd($billInfos);

        if ($billInfos['isError']) {
            return view('Trainee/honorarium-status', $billInfos);
        } else {

            helper('form');

            $generalInfo = $this->fcpsPartOneModel->getPartOneTraineeByRegNo(auth()->user()->username);

            $trainingInstitutes = $this->trainingInstituteModel
                ->where('honorarium_status', true)
                ->where('status', true)
                ->orderBy('name', 'ASC')
                ->findAll();
            $data['trainingInstitutes'] = $trainingInstitutes;

            $prevTrainingInstitutes = $this->trainingInstituteModel
                ->where('status', true)
                ->orderBy('name', 'ASC')
                ->findAll();
            $data['prevTrainingInstitutes'] = $prevTrainingInstitutes;

            $trainingCategories         = $this->trainingCategoryModel->findAll();
            $data['trainingCategories'] = $trainingCategories;

            $departments           = $this->specialityModel->where('status', true)->findAll();
            $data['departments']   = $departments;
            $data['specialities']  = $departments;
            $designations          = $this->designationModel->where('status', true)->findAll();
            $data['designations']  = $designations;
            $data['slots']         = $this->honorariumSlotModel->where('status', true)->findAll();
            $data['basicInfo']     = $generalInfo;
            $applicant             = $this->applicantInformationModel->getApplicantInfoByRegNo($generalInfo['reg_no']);
            $data['applicantInfo'] = $applicant;
            $data['banks']         = $this->bankModel->where('status', true)->findAll();

            $data['honorarium'] = array(
                'maxHonorariumCnt' => 0,
            );

            if ($applicant) {
                $sqlHonorarium = "select MAX(honorarium_position) AS maxHonorariumCnt from honorarium_information where eligible_status='Y' AND bmdc_reg_no='" . $applicant['bmdc_reg_no'] . "' AND applicant_id=" . $applicant['applicant_id'];

                $query              = $this->db->query($sqlHonorarium);
                $data['honorarium'] = $query->getRow();

                $whereLastHonorarium = [
                    'hi.applicant_id'        => $applicant['applicant_id'],
                    'hi.honorarium_position' => $data['honorarium']->maxHonorariumCnt,
                ];

                $lastHonorariumData = $this->honorariumInformationModel->getBillInfos($whereLastHonorarium);
                $prevTrainingData   =
                $this->honorariumPreviousTrainingModel->getPreviousTrainingsByApplicationId($applicant['applicant_id']);

                //dd($lastHonorariumData);

                if ($prevTrainingData) {
                    foreach ($prevTrainingData as $prevTraining) {
                        $trainings[] = [
                            'id'                      => $prevTraining['id'],
                            'honorarium_id'           => $prevTraining['honorarium_id'],
                            'slot_sl_no'              => $prevTraining['slot_sl_no'],
                            'training_from'           => $prevTraining['training_from'],
                            'training_to'             => $prevTraining['training_to'],
                            'training_institute_id'   => $prevTraining['training_institute_id'],
                            'training_institute_name' => $prevTraining['training_institute_name'],
                            'speciality_id'           => $prevTraining['speciality_id'],
                            'department_name'         => $prevTraining['department_name'],
                            'training_category_id'    => $prevTraining['training_category_id'],
                            'training_category_title' => $prevTraining['training_category_title'],
                            'honorarium_taken'        => $prevTraining['honorarium_taken'],
                            'disable_for_bill'        => true, // Set this flag to true for all previous trainings
                        ];
                    }
                } else {
                    $trainings = [];
                }

                //dd($trainings);
                //echo count($trainings);

                //$lastHonorariumData = [];

                //dd($lastHonorariumData);

                if (count($lastHonorariumData) > 0) {

                    if ($lastHonorariumData[0]['honorarium_slot_id'] == 1) {
                        $trainingFromDt = $lastHonorariumData[0]['honorarium_year'] . '-01-01';
                        $trainingToDt   = $lastHonorariumData[0]['honorarium_year'] . '-06-30';
                    } elseif ($lastHonorariumData[0]['honorarium_slot_id'] == 2) {
                        $trainingFromDt = $lastHonorariumData[0]['honorarium_year'] . '-07-01';
                        $trainingToDt   = $lastHonorariumData[0]['honorarium_year'] . '-12-31';
                    }

                    $lastTrainingData = [
                        'id'                      => null,
                        'honorarium_id'           => $lastHonorariumData[0]['id'],
                        'slot_sl_no'              => count($trainings) + 1,
                        'training_from'           => $trainingFromDt,
                        'training_to'             => $trainingToDt,
                        'training_institute_id'   => $lastHonorariumData[0]['training_institute_id'],
                        'training_institute_name' => $lastHonorariumData[0]['training_institute_name'],
                        'speciality_id'           => $lastHonorariumData[0]['department_id'],
                        'department_name'         => $lastHonorariumData[0]['department_name'],
                        'training_category_id'    => null,
                        'training_category_title' => null,
                        'honorarium_taken'        => true,
                        'disable_for_bill'        => false, // Set this flag to true for all previous trainings
                    ];

                    $trainings[count($trainings) > 0 ? count($trainings) : 0] = $lastTrainingData;

                    $data['totalTrainings'] = $trainings;
                } else {
                    $data['totalTrainings'] = $trainings;
                }

                if ($data['honorarium']->maxHonorariumCnt == null) {
                    $data['honorarium']->maxHonorariumCnt = 0;
                }
            }

            //dd($data);

            return view('Trainee/honorarium-application', $data);
        }
    }

    public function storeBillApplication()
    {
        // Check if the authenticated user has the 'trainee.honorarium.application' permission
        if (!auth()->user()->can('trainee.honorarium.application')) {
            // User does not have permission, so deny access.
            //return redirect()->back()->with('error', 'You are not authorized to edit posts.');
            //return redirect()->to('/403');
            return redirect()->to('/403')->with('error', 'You are not authorized to access this information.');
        }

        $request = service('request');

        // --- STEP 1: RETRIEVE NON-FILE DATA ---
        $collectedDataJson = $request->getPost('collectedDataJson');
        if (empty($collectedDataJson)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Missing application data.',
            ])->setStatusCode(400);
        }

        // Decode the JSON string back into a PHP array
        $data = json_decode($collectedDataJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data format error. Could not decode application data.',
            ])->setStatusCode(400);
        }

        $bmdcValidity       = $data['bmdcValidity'];
        $honorariumPosition = $data['honorariumPosition'];
        $trainingType       = $data['trainingType'];

        $generalInfo = $this->fcpsPartOneModel->getPartOneTraineeByRegNo(auth()->user()->username);
        $applicant   = $this->applicantInformationModel->getApplicantInfoByRegNo($generalInfo['reg_no']);

        $applicantId = $applicant['applicant_id'];
        $bmdcRegNo   = $applicant['bmdc_reg_no'];

        // --- Step 1: Server-Side Validation ---
        // You should perform model or service validation here using CI4's Validation library
        if ($bmdcValidity < date('Y-m-d')) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'BMDC Validity date is expired.',
            ]);
        }

        if ($trainingType == 'Advance' && $data['coursePeriod'] < 24) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'For Advance training, previous training period must be at least 24 months.',
            ]);
        }

        if ($data['coursePeriod'] > 0) {
            if (empty($data['previousTrainingDetails'])) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Please enter the previous training details.',
                ]);
            }
        }

        //print_r($data);
        //dd($data);

        //Already applied check
        $billInfos = $this->honorariumInformationModel->getBillInfos([
            'hi.applicant_id'       => $applicantId,
            'hi.honorarium_slot_id' => $data['honorariumPeriod'],
            'hi.honorarium_year'    => $data['honorariumYear'],
        ]);

        if (count($billInfos) > 0) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'You have already applied for this honorarium period.',
            ]);
        }

        //Check mid-term result
        $query = $this->db->query('SELECT *
                                    FROM (
                                    SELECT
                                    `midterm_session`,
                                    `midterm_year`,
                                    `bmdc_reg_no`,
                                    `exam_result`,
                                    DENSE_RANK() OVER (
                                    PARTITION BY `bmdc_reg_no`
                                    ORDER BY `midterm_year` DESC, `midterm_session` DESC
                                    ) AS ranking
                                    FROM fcps_mid_term_applicants
                                    ) AS ranked
                                    WHERE ranked.ranking = 1 AND ranked.bmdc_reg_no = "' . $bmdcRegNo . '"');

        $midTermResult = $query->getRowArray();

        if ($honorariumPosition >= 7) {
            if ($midTermResult) {
                if ($midTermResult['exam_result'] != 'Passed') {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'You are not eligible for the honorarium because you did not pass the Midterm Exam.',
                    ]);

                }
            } else {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'You are not eligible for the honorarium because you did not take the Midterm Exam.',
                ]);
            }
        } elseif ($honorariumPosition > 4 && $honorariumPosition < 7) {if ($midTermResult) {if (count($midTermResult) < 1) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'You are not eligible for the honorarium because you did not take the Midterm Exam.',
            ]);
        }} else {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'You are not eligible for the honorarium because you did not take the Midterm Exam.',
            ]);
        }}

        /*echo '
        <pre>';
        print_r($midTermResult);
        echo '</pre>';
        dd($midTermResult);*/

        // --- STEP 2: HANDLE FILE UPLOADS ---
        $uploadedFiles  = $this->request->getFiles();
        $savedFileNames = [];
        $uploadPath     = FCPATH . 'public/uploads/honorariums/';

        // Ensure the upload directory exists
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Loop through the expected file fields (enclosure1, enclosure2, etc.)
        foreach ($uploadedFiles as $fieldName => $file) {

            // Check if the file is a valid upload and the upload was successful
            if ($file->isValid() && !$file->hasMoved()) {

                // 🔥 CHECK FILE SIZE BEFORE UPLOAD
                $maxSize = 100 * 1024; // 100 KB
                if ($file->getSize() > $maxSize) {

                    if ($fieldName == 'enclosure1') {
                        $fieldName = 'Provisional training certificate';
                    } elseif ($fieldName == 'enclosure2') {
                        $fieldName = 'Bank Cheque book';
                    } elseif ($fieldName == 'enclosure3') {
                        $fieldName = 'FCPS Part-I Congratulation Letter';
                    } elseif ($fieldName == 'enclosure4') {
                        $fieldName = 'Mid-Term Congratulation Letter';
                    } elseif ($fieldName == 'enclosure5') {
                        $fieldName = 'NID/Smart Card';
                    }

                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => "{$fieldName} file exceeds 100 KB limit.",
                    ]);
                }

                $fileTitle = '';
                if ($fieldName == 'enclosure1') {
                    $fileTitle = 'Provisional_Certificate';
                } elseif ($fieldName == 'enclosure2') {
                    $fileTitle = 'Cheque_Book';
                } elseif ($fieldName == 'enclosure3') {
                    $fileTitle = 'FCPS_Congratulation';
                } elseif ($fieldName == 'enclosure4') {
                    $fileTitle = 'Mid-Term_Congratulation';
                } elseif ($fieldName == 'enclosure5') {
                    $fileTitle = 'NID_Card';
                }

                // Generate a secure, unique name for the file to prevent conflicts
                $uniqueFileNme = $file->getRandomName();
                $newName       = $applicant['bmdc_reg_no'] . '-' . $fileTitle . '-' . $uniqueFileNme;

                // Move the file from temp storage to your desired folder
                $file->move($uploadPath, $newName);

                if ($fieldName == 'enclosure1') {
                    // Store the new file name for database saving
                    //$savedFileNames[$fieldName] = $newName;
                    $savedFileNames[] = [
                        'fileType' => 'provi_certifice',
                        'fileName' => $newName,
                    ];
                } elseif ($fieldName == 'enclosure2') {
                    $savedFileNames[] = [
                        'fileType' => 'cheque',
                        'fileName' => $newName,
                    ];
                } elseif ($fieldName == 'enclosure3') {
                    $savedFileNames[] = [
                        'fileType' => 'fcps_congrats',
                        'fileName' => $newName,
                    ];
                } elseif ($fieldName == 'enclosure4') {
                    $savedFileNames[] = [
                        'fileType' => 'midterm_congrats',
                        'fileName' => $newName,
                    ];
                } elseif ($fieldName == 'enclosure5') {
                    $savedFileNames[] = [
                        'fileType' => 'nid_card',
                        'fileName' => $newName,
                    ];
                }

            } else if ($file->getError() !== UPLOAD_ERR_NO_FILE) {
                // Handle actual upload errors (e.g., file size, type)
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => "File upload failed for {$fieldName}: " . $file->getErrorString(),
                ])->setStatusCode(400);
            }
        }

        // --- STEP 3: Database Operations ---
        try {

            $builder = $this->db->table('honorarium_information');
            $builder->selectMax('bill_sl_no');
            $builder->where('status', 1);
            $builder->where('honorarium_slot_id', $data['honorariumPeriod']);
            $builder->where('honorarium_year', $data['honorariumYear']);
            $query            = $builder->get();
            $maxBillSerialRow = $query->getRowArray();

            $maxBillSerial = $maxBillSerialRow['bill_sl_no'] ?? env('bill.startSlNo', 0); // default 1 if null

            $department = $this->specialityModel->find($data['currentDepartment']);
            if ($data['coursePeriod'] > 0) {
                $currentTrainingSlot = count($data['previousTrainingDetails']) > 0 ? count($data['previousTrainingDetails']) + 1 : 1;
            } else {
                $currentTrainingSlot = 1;
            }

            $savedData = [
                'applicant_id'              => $applicant['applicant_id'],
                'bmdc_reg_no'               => $applicant['bmdc_reg_no'],
                'training_type'             => $data['trainingType'],
                'current_training_slot'     => $currentTrainingSlot,
                'training_institute_id'     => $data['currentTrainingInstitute'],
                'department_id'             => $data['currentDepartment'],
                'department_name'           => $department['name'],
                'honorarium_slot_id'        => $data['honorariumPeriod'],
                'honorarium_year'           => $data['honorariumYear'],
                'previous_training_inmonth' => $data['coursePeriod'],
                'honorarium_position'       => $data['honorariumPosition'],
                'bill_sl_no'                => $maxBillSerial + 1,
            ];

            $newHonorariumId = $this->honorariumInformationModel->insert($savedData);

            if ($newHonorariumId) {

                //Update applicant info
                $updatedData = [
                    'date_of_birth' => $data['dob'],
                    'nid'           => $data['nidNo'],
                    'gander'        => $data['gender'],
                    'bmdc_validity' => $data['bmdcValidity'],
                    'mobile'        => $data['mobileNo'],
                    'email'         => $data['email'],
                    'updated_at'    => date('Y-m-d H:i:s'),
                    'updated_by'    => auth()->user()->id,
                ];

                if ($data['midTermAppeared'] == 'on') {
                    $updatedData = array_merge($updatedData, [
                        'mid_term_session' => $data['midTermExamSession'],
                        'mid_term_year'    => $data['midTermExamYear'],
                        'mid_term_result'  => $data['midTermExamResult'],
                        'mid_term_roll'    => $data['midTermExamRollNo'],
                    ]);
                }

                $approvedHonorariums = $this->honorariumInformationModel->getApprovedHonorariumByApplicantId($applicant['applicant_id']);

                if ($approvedHonorariums) {
                    if (count($approvedHonorariums) == 0) {
                        $updatedData = array_merge($updatedData, [
                            'bank_id'        => $data['bank'],
                            'branch_name'    => $data['branchName'],
                            'account_no'     => $data['accountNo'],
                            'routing_number' => $data['routingNumber'],
                        ]);
                    }
                }

                $this->applicantInformationModel->update($applicant['applicant_id'], $updatedData);

                //print_r($approvedHonorariums);
                //print_r($data);
                //print_r($updatedData);
                //dd($data);

                //print_r($data['previousTrainingDetails']);

                $prevTrainings = $this->honorariumPreviousTrainingModel->getPreviousTrainingsByApplicationId($applicant['applicant_id']);

                if ($prevTrainings) {

                    //Delete existing previous training details
                    //$this->honorariumPreviousTrainingModel->where('honorarium_id', $newHonorariumId)->delete();

                    //Insert updated previous training details
                    if ($data['coursePeriod'] > 0) {

                        foreach ($data['previousTrainingDetails'] as $prevTraining) {

                            //print_r($prevTraining);
                            echo $prevTraining['honorariumBillStatus'];

                            if ($prevTraining['honorariumBillStatus'] == 'false') {
                                // Update existing record

                                $prevTrainingData[] = [
                                    'honorarium_id'         => $newHonorariumId,
                                    'slot_sl_no'            => $prevTraining['slot'],
                                    'training_from'         => $prevTraining['fromDate'],
                                    'training_to'           => $prevTraining['toDate'],
                                    'speciality_id'         => $prevTraining['subject'],
                                    'training_institute_id' => $prevTraining['institute'],
                                    'training_category_id'  => $prevTraining['category'],
                                    'honorarium_taken'      => true,
                                ];
                            }
                        }

                        //print_r($prevTrainingData);

                        $this->honorariumPreviousTrainingModel->insertBatch($prevTrainingData);
                    }
                } else {

                    //Inser previous training details
                    if ($data['coursePeriod'] > 0) {

                        foreach ($data['previousTrainingDetails'] as $prevTraining) {
                            $prevTrainingData[] = [
                                'honorarium_id'         => $newHonorariumId,
                                'slot_sl_no'            => $prevTraining['slot'],
                                'training_from'         => $prevTraining['fromDate'],
                                'training_to'           => $prevTraining['toDate'],
                                'speciality_id'         => $prevTraining['subject'],
                                'training_institute_id' => $prevTraining['institute'],
                                'training_category_id'  => $prevTraining['category'],
                                'honorarium_taken'      => $prevTraining['honorariumTaken'],
                            ];
                        }

                        $this->honorariumPreviousTrainingModel->insertBatch($prevTrainingData);
                    }
                }
                //Insert or Update files
                $applicantFiles = $this->applicantFileModel
                    ->where('applicant_id', $applicant['applicant_id'])
                    ->findAll();

                //dd($applicantFiles);
                /*echo '<pre>';
                print_r($applicantFiles);
                echo '</pre>';*/
                //die;

                if ($applicantFiles) {
                    foreach ($applicantFiles as $key => $value) {
                        $applicantFileCategory[] = $value['type'];
                    }
                    foreach ($savedFileNames as $file) {
                        if (!in_array($file['fileType'], $applicantFileCategory)) {
                            $fileData = [
                                'applicant_id' => $applicant['applicant_id'],
                                'file_name'    => $file['fileName'],
                                'type'         => $file['fileType'],
                            ];

                            $this->applicantFileModel->insert($fileData);
                        } /*else {
                    $fileData = [
                    'file_name' => $file['fileName'],
                    'modified'  => date('Y-m-d H:i:s'),
                    ];

                    echo '<pre>';
                    print_r($fileData);
                    echo '</pre>';
                    die;

                    $this->applicantFileModel
                    ->where('type', $file['fileType'])
                    ->where('applicant_id', $file['applicant_id'])
                    ->update(null, $fileData);

                    //$this->applicantFileModel->update($applicant['fiile_id'], $fileData);
                    }*/

                    }
                } else {
                    foreach ($savedFileNames as $file) {
                        $fileData = [
                            'applicant_id' => $applicant['applicant_id'],
                            'file_name'    => $file['fileName'],
                            'type'         => $file['fileType'],
                        ];

                        $this->applicantFileModel->insert($fileData);
                    }
                }

                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Your bill has been submitted successfully!',
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Honorarium Submission Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'A server error occurred during data saving.',
            ])->setStatusCode(500);
        }
    }
}