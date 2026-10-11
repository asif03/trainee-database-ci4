<?php

namespace App\Controllers;

use App\Models\FcpsPartOneModel;
use App\Models\HonorariumInformationModel;
use App\Services\EmailService;
use App\Services\SmsService;

class Home extends BaseController
{
    protected $fcpsPartOneModel;
    protected $honorariumModel;

    public function __construct()
    {
        $this->fcpsPartOneModel = new FcpsPartOneModel();
        $this->honorariumModel  = new HonorariumInformationModel();
    }

    public function index(): string
    {
        return view('home');
    }

    public function registrationNoSms(): string
    {
        return view('registration_no_sms');
    }

    public function honorariums(): string
    {
        $honorariumYear    = env('bill.currentYear', date('Y'));
        $honorariumSession = env('bill.currentSlot', '1');
        $bills             = $this->honorariumModel->exportBillInformation($honorariumYear, $honorariumSession);

        return view('honorariums', ['bills' => $bills]);
    }

    public function geteLogbokPayment($id)
    {
        // Define the target endpoint with the dynamic ID
        $url = "https://eportal.bcps.edu.bd/api/auth/v1/get-course-payment-data/{$id}";

        $client = \Config\Services::curlrequest();

        try {
            $response = $client->get($url, [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            $status = $response->getStatusCode();
            $body   = $response->getBody();

            // Debug
            /*echo "<pre>";
            print_r([
            'status' => $status,
            'body'   => $body,
            'json'   => json_decode($body, true),
            ]);
            echo "</pre>";
            die;*/

            // Or return the JSON response
            return $body;

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function sendOtp()
    {
        $penNo          = $this->request->getPost('penNo');
        $mobileNo       = $this->request->getPost('mobileNo');
        $emailAddress   = $this->request->getPost('emailAddress');
        $chooseOption   = $this->request->getPost('chooseOption');
        $elogBookStatus = false; // Initialize the eLogbook status variable

        if ($penNo == '' && ($mobileNo == '' || $emailAddress == '')) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Please fill out required fields!',
                'csrf_token' => csrf_hash(), // send fresh token
            ]);
        }

        $params = [];

        if ($chooseOption === 'both') {
            $params = [
                'pen_number' => $penNo,
                //'cell'       => $mobileNo,
                'cell'       => str_pad($mobileNo, 11, "0", STR_PAD_LEFT),
                'email'      => $emailAddress,
            ];
        } elseif ($chooseOption === 'sms') {
            $params = [
                'pen_number' => $penNo,
                'cell'       => str_pad($mobileNo, 11, "0", STR_PAD_LEFT),
            ];
        } elseif ($chooseOption === 'email') {
            $params = [
                'pen_number' => $penNo,
                'email'      => $emailAddress,
            ];
        }

        $checkPayment = $this->fcpsPartOneModel->checkPaymentInfo($params['pen_number'] ?? '');

        if ($checkPayment == 0) {

            $candidatePaymentInfo = $this->fcpsPartOneModel->getPaymentInfoWithBmdc($params['pen_number'] ?? '');

            // e-Logbook payment check for year 2025 and above
            if ($candidatePaymentInfo['fcps_part_one_year'] >= 2025) {

                $eLogbookPayment = $this->geteLogbokPayment($candidatePaymentInfo['bmdc_reg_no'] ?? '');
                $paymentInfo     = json_decode($eLogbookPayment, true);

                if (isset($paymentInfo['success']) && $paymentInfo['success'] === true) {

                    $partOnePassedInfo = $this->fcpsPartOneModel->getPartOneTraineeByRegNo($paymentInfo['data']['reg_no'] ?? '');

                    //Check if applicant is in e-Logbook
                    if ($partOnePassedInfo['elogbook'] == 'Y') {
                        $elogBookStatus = true;
                    } else {
                        $elogBookStatus = false;
                    }

                    $updateData = [
                        'reg_no'             => $candidatePaymentInfo['bmdc_reg_no'] ?? null,
                        'money_receipt_no'   => $paymentInfo['data']['paymentRefNo'] ?? null,
                        'money_receipt_date' => $paymentInfo['data']['paymentDate'] ?? null,
                        'payment_status'     => $paymentInfo['data']['paymentStaus'] ?? 'Not Paid',
                        'paid_amount'        => $paymentInfo['data']['paymentAmount'] ?? null,
                    ];

                } elseif (isset($paymentInfo['success']) && $paymentInfo['success'] === false) {
                    return $this->response->setJSON([
                        'success'    => false,
                        'message'    => $paymentInfo['message'] . ' for the provided PEN number. Please complete the Payment/Registration & Payment in <a href="https://eportal.bcps.edu.bd/" target="_blank">eLogbook</a> to proceed.',
                        'csrf_token' => csrf_hash(), // send fresh token
                    ]);
                } else {
                    return $this->response->setJSON([
                        'success'    => false,
                        'message'    => 'No record found for the provided PEN number.',
                        'csrf_token' => csrf_hash(), // send fresh token
                    ]);
                }

            } else {
                return $this->response->setJSON([
                    'success'    => false,
                    'message'    => 'No payment record found for the provided PEN number. Please complete the payment to proceed.',
                    'csrf_token' => csrf_hash(), // send fresh token
                ]);
            }
            if (empty($candidatePaymentInfo)) {
                return $this->response->setJSON([
                    'success'    => false,
                    'message'    => 'No payment record found for the provided PEN number. Please complete the payment to proceed.',
                    'csrf_token' => csrf_hash(), // send fresh token
                ]);
            }

            //return dd($candidatePaymentInfo); //geteLogbokPayment(bmdc)

        }

        $trainee = $this->fcpsPartOneModel->getTraineeInfoByParams($params);

        if ($trainee) {

            $otp = rand(1000, 9999);

            if ($chooseOption === 'sms' && $trainee['smscounter'] >= env('sms.max', 2)) {
                return $this->response->setJSON([
                    'success'    => false,
                    'message'    => 'You’ve already requested your Registration No. and Password via SMS twice. Please try a different method to continue.',
                    'csrf_token' => csrf_hash(), // send fresh token
                ]);
            }

            if ($chooseOption === 'both' && $trainee['smscounter'] >= env('sms.max', 2)) {
                $chooseOption === 'email';
            }

            if ($chooseOption === 'email') {

                $data = [
                    'recipient_name' => $trainee['name'],
                    'otp'            => $otp,
                ];

                $to      = $trainee['email'];
                $subject = 'One-Time Password (OTP) from Bangladesh College of Physicians & Surgeons';

                // Load the HTML as a string
                $message = view('Emails/otp_template', $data);

                $emailService = new EmailService();
                $result       = $emailService->sendEmail($to, $subject, $message); // You can pass parameters if needed

                if ($result) {

                    $hashedOtp = password_hash($otp, PASSWORD_DEFAULT);

                    $updateData = [
                        'hashedotp' => $hashedOtp,
                    ];

                    $isUpdate = $this->fcpsPartOneModel->update($trainee['id'], $updateData);

                    if ($isUpdate) {
                        return $this->response->setJSON([
                            'success'    => true,
                            'message'    => 'OTP sent successfully to your ' . $chooseOption,
                            'csrf_token' => csrf_hash(), // send fresh token
                        ]);
                    } else {
                        return $this->response->setJSON([
                            'success'    => false,
                            'message'    => 'OTP sent failed to your ' . $chooseOption,
                            'csrf_token' => csrf_hash(), // send fresh token
                        ]);
                    }
                } else {
                    return $this->response->setJSON([
                        'success'    => false,
                        'message'    => 'OTP sent failed to your ' . $chooseOption,
                        'csrf_token' => csrf_hash(), // send fresh token
                    ]);
                }
            }

            if ($chooseOption === 'sms') {
                $mobile  = $trainee['cell'];
                $message = 'One-Time Password (OTP) from Bangladesh College of Physicians & Surgeons(BCPS) is: ' . $otp . '. Please do not share this OTP with anyone.';

                $smsService = new SmsService();
                $response   = $smsService->singleSms($mobile, $message, uniqid());

                $responseData = json_decode($response);

                if ($responseData->status == 'SUCCESS') {
                    $hashedOtp = password_hash($otp, PASSWORD_DEFAULT);

                    $updateData = [
                        'hashedotp' => $hashedOtp,
                    ];

                    $isUpdate = $this->fcpsPartOneModel->update($trainee['id'], $updateData);

                    if ($isUpdate) {
                        return $this->response->setJSON([
                            'success'    => true,
                            'message'    => 'OTP sent successfully to your ' . $chooseOption,
                            'csrf_token' => csrf_hash(), // send fresh token
                        ]);
                    } else {
                        return $this->response->setJSON([
                            'success'    => false,
                            'message'    => 'OTP sent failed to your ' . $chooseOption,
                            'csrf_token' => csrf_hash(), // send fresh token
                        ]);
                    }
                } else {
                    return $this->response->setJSON([
                        'success'    => false,
                        'message'    => 'OTP sent failed to your ' . $chooseOption,
                        'csrf_token' => csrf_hash(), // send fresh token
                    ]);
                }
            }

            if ($chooseOption === 'both') {
                $data = [
                    'recipient_name' => $trainee['name'],
                    'otp'            => $otp,
                ];

                $to      = $trainee['email'];
                $subject = 'One-Time Password (OTP) from Bangladesh College of Physicians & Surgeons';

                // Load the HTML as a string
                $message = view('Emails/otp_template', $data);

                $emailService = new EmailService();
                $result       = $emailService->sendEmail($to, $subject, $message); // You can pass parameters if needed

                if ($result) {

                    $hashedOtp = password_hash($otp, PASSWORD_DEFAULT);

                    $updateData = [
                        'hashedotp' => $hashedOtp,
                    ];

                    $isUpdate = $this->fcpsPartOneModel->update($trainee['id'], $updateData);
                } else {
                    $hashedOtp = password_hash($otp, PASSWORD_DEFAULT);

                    $updateData = [
                        'hashedotp' => $hashedOtp,
                    ];

                    $isUpdate = $this->fcpsPartOneModel->update($trainee['id'], $updateData);
                }

                $smsResponse         = new \stdClass(); // create a blank object
                $smsResponse->status = 'FAILED';

                if ($isUpdate) {
                    $mobile  = $trainee['cell'];
                    $message = 'One-Time Password (OTP) from Bangladesh College of Physicians & Surgeons(BCPS) is: ' . $otp . '. Please do not share this OTP with anyone.';

                    $smsService = new SmsService();
                    $response   = $smsService->singleSms($mobile, $message, uniqid());

                    $smsResponse = json_decode($response);
                }

                if (!$result && $smsResponse->status != 'SUCCESS') {
                    return $this->response->setJSON([
                        'success'    => false,
                        'message'    => 'OTP sent failed to your ' . $chooseOption . 'option',
                        'csrf_token' => csrf_hash(), // send fresh token
                    ]);
                }

                if ($result || $smsResponse->status == 'SUCCESS') {
                    return $this->response->setJSON([
                        'success'    => true,
                        'message'    => 'OTP sent successfully to your Email or Mobile.',
                        'csrf_token' => csrf_hash(), // send fresh token
                    ]);
                }
            }

        } else {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Invalid PEN or Mobile number or Email!',
                'csrf_token' => csrf_hash(), // send fresh token
            ]);
        }

    }

    public function verifyOtp()
    {
        $penNo        = $this->request->getPost('penNo');
        $mobileNo     = $this->request->getPost('mobileNo');
        $emailAddress = $this->request->getPost('emailAddress');
        $chooseOption = $this->request->getPost('chooseOption');
        $otp          = $this->request->getPost('otp');

        if ($penNo == '' && ($mobileNo == '' || $emailAddress == '')) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Please fill out required fields!',
                'csrf_token' => csrf_hash(), // send fresh token
            ]);
        }

        $params = [];

        if ($chooseOption === 'both') {
            $params = [
                'pen_number' => $penNo,
                //'cell'       => $mobileNo,
                'cell'       => str_pad($mobileNo, 11, "0", STR_PAD_LEFT),
                'email'      => $emailAddress,
            ];
        } elseif ($chooseOption === 'sms') {
            $params = [
                'pen_number' => $penNo,
                'cell'       => str_pad($mobileNo, 11, "0", STR_PAD_LEFT),
            ];
        } elseif ($chooseOption === 'email') {
            $params = [
                'pen_number' => $penNo,
                'email'      => $emailAddress,
            ];
        }

        $trainee = $this->fcpsPartOneModel->getTraineeInfoByParams($params);

        if ($trainee) {

            if (password_verify($otp, $trainee['hashedotp'])) {

                if ($chooseOption === 'both' && $trainee['smscounter'] >= env('sms.max', 2)) {
                    $chooseOption === 'email';
                }

                if ($chooseOption === 'email') {

                    $data = [
                        'recipientName' => $trainee['name'],
                        'regNumber'     => $trainee['reg_no'],
                        'password'      => $trainee['password'],
                        // Footer variables (use actual application settings)
                        'websiteUrl'    => 'https://www.bcps.edu.bd',
                        'supportEmail'  => 'it@bcps.edu.bd',
                        'contactNumber' => '+880-2-222284189',
                        'loginUrl'      => site_url('login'), // Assuming you have a route named 'login'
                    ];

                    $to      = $trainee['email'];
                    $subject = 'Welcome! Your BCPS Registration Credentials';

                    // Load the HTML as a string
                    $message = view('Emails/registration_welcome', $data);

                    $emailService = new EmailService();
                    $result       = $emailService->sendEmail($to, $subject, $message); // You can pass parameters if needed

                    if ($result) {
                        return $this->response->setJSON([
                            'success'    => true,
                            'message'    => 'Registration info send successfully!',
                            'csrf_token' => csrf_hash(), // send fresh token
                        ]);
                    }
                } elseif ($chooseOption === 'sms') {
                    $mobile  = $trainee['cell'];
                    $message = 'Welcome! Your BCPS Registration Credentials: Registration No.: ' . $trainee['reg_no'] . ', Password: ' . $trainee['password'] . '. Login at: ' . site_url('login') . '. For support, contact +880-222284189.';

                    $smsService   = new SmsService();
                    $response     = $smsService->singleSms($mobile, $message, uniqid());
                    $responseData = json_decode($response);
                    if ($responseData->status == 'SUCCESS') {

                        $updateData = [
                            'smscounter' => $trainee['smscounter'] + 1,
                        ];

                        $isUpdate = $this->fcpsPartOneModel->update($trainee['id'], $updateData);
                        if ($isUpdate) {

                            return $this->response->setJSON([
                                'success'    => true,
                                'message'    => 'Registration info send successfully!',
                                'csrf_token' => csrf_hash(), // send fresh token
                            ]);
                        } else {
                            return $this->response->setJSON([
                                'success'    => false,
                                'message'    => 'Woops! something heppen.',
                                'csrf_token' => csrf_hash(), // send fresh token
                            ]);
                        }
                    } else {
                        return $this->response->setJSON([
                            'success'    => false,
                            'message'    => 'Failed to send registration info via SMS!',
                            'csrf_token' => csrf_hash(), // send fresh token
                        ]);
                    }

                } elseif ($chooseOption === 'both') {
                    $data = [
                        'recipientName' => $trainee['name'],
                        'regNumber'     => $trainee['reg_no'],
                        'password'      => $trainee['password'],
                        // Footer variables (use actual application settings)
                        'websiteUrl'    => 'https://www.bcps.edu.bd',
                        'supportEmail'  => 'it@bcps.edu.bd',
                        'contactNumber' => '+880-2-222284189',
                        'loginUrl'      => site_url('login'), // Assuming you have a route named 'login'
                    ];

                    $to      = $trainee['email'];
                    $subject = 'Welcome! Your BCPS Registration Credentials';

                    // Load the HTML as a string
                    $message = view('Emails/registration_welcome', $data);

                    $emailService = new EmailService();
                    $result       = $emailService->sendEmail($to, $subject, $message); // You can pass parameters if needed

                    if ($result) {
                        $mobile  = $trainee['cell'];
                        $message = 'Welcome! Your BCPS Registration Credentials: Registration No.: ' . $trainee['reg_no'] . ', Password: ' . $trainee['password'] . '. Login at: ' . site_url('login') . '. For support, contact +880-222284189.';

                        $smsService   = new SmsService();
                        $response     = $smsService->singleSms($mobile, $message, uniqid());
                        $responseData = json_decode($response);
                        if ($responseData->status == 'SUCCESS') {

                            $updateData = [
                                'smscounter' => $trainee['smscounter'] + 1,
                            ];

                            $isUpdate = $this->fcpsPartOneModel->update($trainee['id'], $updateData);
                            if ($isUpdate) {

                                return $this->response->setJSON([
                                    'success'    => true,
                                    'message'    => 'Registration info send successfully!',
                                    'csrf_token' => csrf_hash(), // send fresh token
                                ]);
                            }
                        }
                    } else {
                        $mobile  = $trainee['cell'];
                        $message = 'Welcome! Your BCPS Registration Credentials: Registration No.: ' . $trainee['reg_no'] . ', Password: ' . $trainee['password'] . '. Login at: ' . site_url('login') . '. For support, contact +880-222284189.';

                        $smsService   = new SmsService();
                        $response     = $smsService->singleSms($mobile, $message, uniqid());
                        $responseData = json_decode($response);
                        if ($responseData->status == 'SUCCESS') {

                            $updateData = [
                                'smscounter' => $trainee['smscounter'] + 1,
                            ];

                            $isUpdate = $this->fcpsPartOneModel->update($trainee['id'], $updateData);
                            if ($isUpdate) {

                                return $this->response->setJSON([
                                    'success'    => true,
                                    'message'    => 'Registration info send successfully!',
                                    'csrf_token' => csrf_hash(), // send fresh token
                                ]);
                            }
                        }
                    }

                    if (!$result && $responseData->status != 'SUCCESS') {
                        return $this->response->setJSON([
                            'success'    => false,
                            'message'    => 'Failed to send registration info via Email and SMS!',
                            'csrf_token' => csrf_hash(), // send fresh token
                        ]);
                    }

                } else {
                    return $this->response->setJSON([
                        'success'    => false,
                        'message'    => 'Woops! something heppen.',
                        'csrf_token' => csrf_hash(), // send fresh token
                    ]);
                }
            } else {
                return $this->response->setJSON([
                    'success'    => false,
                    'message'    => 'Invalid OTP!',
                    'csrf_token' => csrf_hash(), // send fresh token
                ]);
            }
        }
    }

    public function contactUs(): string
    {
        return view('contact');
    }
}
