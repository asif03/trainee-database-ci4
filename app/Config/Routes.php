<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Frontend Routes
$routes->get('/', 'Home::index');
$routes->get('/registration-no-sms', 'Home::registrationNoSms');
$routes->get('/contact-us', 'Home::contactUs');
$routes->get('/honorariums', 'Home::honorariums');

$routes->get('/send-sms', function () {
    $smsService = new \App\Services\SmsService();
    $response   = $smsService->singleSms('01724296191', 'Test message from CodeIgniter SMS Service', uniqid());

    print_r($response);
});

$routes->get('/401', function () {
    return view('401');
});

$routes->get('/403', function () {
    return view('errors/html/error_403');
});

$routes->get('/profile', 'UserController::profile');

$routes->get('/dashboard', 'Dashboard::index');

$routes->group('api', static function ($routes) {

});

$routes->group('fcps-part-one', static function ($routes) {
    $routes->get('passed-candidates', 'PartOneController::index', ['as' => 'partone.list']);
    $routes->post('fetch-candidates', 'PartOneController::getSearchedCandidates');
    $routes->get('fetch-part1-passed-candidate/(:num)', 'PartOneController::getCandidateByRegNo/$1', ['as' => 'partone.candidate.show']);
    $routes->get('edit-part1-passed-candidate/(:num)', 'PartOneController::edit/$1', ['as' => 'partone.candidate.edit']);
    $routes->put('update-part1-info', 'PartOneController::updatePart1Info', ['as' => 'partone.candidate.part1.update']);

    $routes->post('fetch-otp-candidate', 'Home::sendOtp');
    $routes->post('verify-otp', 'Home::verifyOtp');
});

$routes->group('trainings', static function ($routes) {

    $routes->get('get-supervisors/(:num)', 'TraineeController::getSupervisorsByInstitute/$1');
    $routes->get('get-supervisor-details/(:num)', 'TraineeController::getSupervisorById/$1');

    //For Trainee
    $routes->get('basic-info', 'TraineeController::traineeBasicInfo', ['as' => 'trainee.basic.info']);
    $routes->post('basic-info', 'TraineeController::traineeBasicInfoUpdate', ['as' => 'trainee.basic.info.update']);
    $routes->get('progress-reports', 'TraineeController::createProgressReport', ['as' => 'trainee.progress.reports.create']);
    $routes->post('progress-reports', 'TraineeController::storeProgressReport');
    $routes->get('fetch-progress-report/(:num)', 'TraineeController::showProgressReport/$1');
    $routes->get('progress-reports/(:num)', 'TraineeController::editProgressReport/$1');

    $routes->get('training-application', 'TraineeController::trainingApplication', ['as' => 'trainee.training.application']);
    $routes->post('training-store-application', 'TraineeController::storeTrainingApplication');
    $routes->get('honorarium-bill-application', 'TraineeController::honorariumBillApplication', ['as' => 'trainee.honorarium.application']);
    $routes->post('honorarium-bill-application', 'TraineeController::storeBillApplication');

    //For Admin
    $routes->get('trainee-list', 'TrainingController::trainees');
    $routes->post('fetch-trainees', 'TrainingController::getSearchedTrainees');
    $routes->get('trainees/(:num)', 'TrainingController::getTrainee/$1');
    $routes->post('approve-progress-report', 'TrainingController::approveProgressReport');
    $routes->post('receive-progress-report', 'TrainingController::receiveProgressReport');

});

$routes->group('applications', ['filter' => 'groups:superadmin,admin,rtm-admin,rtm-user'], static function ($routes) {
    $routes->get('/', 'Application::index', ['as' => 'applications.index']);
    $routes->post('fetch-applicants', 'Application::getSearchedApplicants', ['as' => 'applications.get']);

    $routes->get('edit/(:num)', 'Application::edit/$1', ['as' => 'applications.edit']);
    $routes->put('update-basic', 'Application::updateBasicInfo', ['as' => 'applications.basic.update']);
    $routes->put('update-fcps', 'Application::updateFcpsInfo', ['as' => 'applications.fcps.update']);
    $routes->put('update-mbbs', 'Application::updateMbbsInfo', ['as' => 'applications.mbbs.update']);
    $routes->put('update-bank', 'Application::updateBankInfo', ['as' => 'applications.bank.update']);

    $routes->post('fetch-files', 'Application::getFilesInfo');
    $routes->post('approve-applicant', 'Application::approveApplicant', ['as' => 'applications.approve']);
    $routes->post('reject-applicant', 'Application::rejectApplicant', ['as' => 'applications.reject']);
});

$routes->group('applications', ['filter' => 'groups:admin,rtm-admin,rtm-user,user'], static function ($routes) {
    $routes->get('fetch-application/(:num)', 'Application::getApplication/$1', ['as' => 'applications.show']);
    $routes->get('download-application-form/(:num)', 'Application::downloadApplicationForm/$1');
});

$routes->group('bills', ['filter' => 'groups:superadmin,admin,rtm-admin,rtm-user'], static function ($routes) {
    $routes->get('/', 'Honorarium::index', ['as' => 'bills.index']);
    $routes->post('get-statistics', 'Honorarium::getStatistics');
    $routes->post('fetch-honorariums', 'Honorarium::getSearchedHonorariums');
    $routes->post('approve-honorarium', 'Honorarium::approveHonorarium', ['as' => 'bills.approve']);
    $routes->post('reject-honorarium', 'Honorarium::rejectHonorarium', ['as' => 'bills.reject']);

    $routes->get('fetch-honorarium/(:num)', 'Honorarium::getHonorarium/$1');
    $routes->get('fetch-honorarium/edit/(:num)', 'Honorarium::getBillInfo/$1', ['as' => 'bills.edit']);
    $routes->put('update-honorarium/(:num)', 'Honorarium::update/$1', ['as' => 'bills.update']);
    $routes->get('fetch-honorarium-training/edit/(:num)', 'Honorarium::getHonorariumTrainingInfo/$1', ['as' => 'bills.training.edit']);
    $routes->put('update-honorarium-training/(:num)', 'Honorarium::updateHonorariumTrainingInfo/$1', ['as' => 'bills.training.update']);

    $routes->post('fetch-files', 'Honorarium::getFilesInfo');

    //$routes->get('download-honorarium-form/(:num)', 'Honorarium::downloadHonorariumForm/$1');
    //$routes->get('fetch-honorarium-trainings/(:num)', 'Honorarium::getHonorariumTrainings/$1');

    /*$routes->get('/users', 'Admin::users', ['as' => 'admin.users']);
$routes->get('/users/(:num)', 'Admin::user/$1', ['as' => 'admin.user']);
$routes->get('/roles', 'Admin::roles', ['as' => 'admin.roles']);
$routes->get('/roles/(:num)', 'Admin::role/$1', ['as' => 'admin.role']);
$routes->get('/permissions', 'Admin::permissions', ['as' => 'admin.permissions']);
$routes->get('/permissions/(:num)', 'Admin::permission/$1', ['as' => 'admin.permission']);*/
});
$routes->group('bills', ['filter' => 'groups:admin,rtm-admin,rtm-user,user'], static function ($routes) {
    $routes->get('download-honorarium-form/(:num)', 'Honorarium::downloadHonorariumForm/$1');
    $routes->get('fetch-honorarium-trainings/(:num)', 'Honorarium::getHonorariumTrainings/$1');
});

$routes->group('reports', ['filter' => 'groups:superadmin,admin'], static function ($routes) {
    $routes->get('applications', 'Report::applications');
    $routes->get('bills', 'Report::bills');
    $routes->post('get-bills', 'Report::getBillInfo');
    $routes->post('get-applications', 'Report::getApplicationInfo');
    $routes->post('export-bill-to-excel', 'Report::exportBillToExcel');
    $routes->post('export-application-to-excel', 'Report::exportApplicationToExcel');
});

$routes->group('users', static function ($routes) {
    $routes->get('assign-user-role', 'UserController::assignRoleViewForm');
});

$routes->group('superadmin', ['filter' => 'groups:superadmin'], static function ($routes) {
    //$routes->group('superadmin', static function ($routes) {
    $routes->get('db-seed', 'SeedController::index');
});

service('auth')->routes($routes);
