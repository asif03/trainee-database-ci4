INSERT INTO `fcps_mid_term_applicants`(`midterm_session`, `midterm_year`, `bmdc_reg_no`, `bmdc_reg_no_org`, `roll_no`, `reg_no`, `applicant_name`, `speciality`, `exam_result`) 
SELECT `midterm_session`, `midterm_year`, `bmdc_reg_no`, `bmdc_reg_no_org`, `roll_no`, `pen_no`, `name`, `speciality_name`, `final_result` FROM `mid-term-excel` ORDER BY `midterm_year` ASC, `midterm_session` ASC;

INSERT INTO `mid_term_excel` (`midterm_session`, `midterm_year`, `exam_log_id`, `bmdc_reg_no_org`, `name_old`, `roll_no`, `pen_no`, `eligibility_status`, `speciality_name`, `email`, `written_result`, `final_result`, `is_fifteen_grade`, `is_ten_grade`)
SELECT 'July', 2025, `COL 1`, `COL 2`, `COL 3`, `COL 4`, `COL 5`, `COL 6`, `COL 7`, `COL 8`, `COL 9`, `COL 10`, `COL 11`, `COL 12` FROM `mid_july_25` ORDER BY `COL 1` ASC;

UPDATE `fcps_mid_term_applicants` t1
JOIN `fcps_mid_term_applicants` t2 ON t1.bmdc_reg_no_org = t2.bmdc_reg_no_org
SET t1.bmdc_reg_no = SUBSTRING(t2.`bmdc_reg_no_org`, 2)
WHERE t1.`bmdc_reg_no_org` LIKE 'A%';

UPDATE `fcps_mid_term_applicants` t1
JOIN `fcps_mid_term_applicants` t2 ON t1.bmdc_reg_no_org = t2.bmdc_reg_no_org
SET t1.bmdc_reg_no = SUBSTRING(t2.`bmdc_reg_no_org`, 2)
WHERE t1.`bmdc_reg_no` IS NULL;


UPDATE `mid_term_excel` t1
JOIN `mid_term_excel` t2 ON t1.bmdc_reg_no_org = t2.bmdc_reg_no_org
SET t1.bmdc_reg_no = SUBSTRING(t2.`bmdc_reg_no_org`, 2)
WHERE t1.`bmdc_reg_no_org` LIKE 'A%';

UPDATE `mid_term_excel` t1
JOIN `mid_term_excel` t2 ON t1.bmdc_reg_no_org = t2.bmdc_reg_no_org
SET t1.bmdc_reg_no = t2.`bmdc_reg_no_org`
WHERE t1.`bmdc_reg_no` IS NULL;

UPDATE `mid_term_excel` t1
JOIN `mid_term_excel` t2 ON t1.bmdc_reg_no_org = t2.bmdc_reg_no_org
SET t1.`name` = LEFT(t1.`name_old`, CHAR_LENGTH(t1.`name_old`) - 12)
WHERE t1.`name_old` IS NOT NULL;


UPDATE `mid_term_excel` t1
JOIN `mid_term_excel` t2 ON t1.bmdc_reg_no_org = t2.bmdc_reg_no_org
SET t1.`mobile` = SUBSTR(t1.`name_old`, -12, 12)
WHERE t1.`name_old` IS NOT NULL;


SELECT *
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
WHERE ranked.ranking = 1;


INSERT INTO `fcps_mid_term_applicants`(`midterm_session`, `midterm_year`, `bmdc_reg_no`, `bmdc_reg_no_org`, `roll_no`, `reg_no`, `applicant_name`, `speciality`, `exam_result`) 
SELECT `midterm_session`, `midterm_year`, `bmdc_reg_no`, `bmdc_reg_no_org`, `roll_no`, `pen_no`, `applicant_name`, `speciality_name`, `final_result` FROM `mid_term_excel` WHERE `final_result`!= 'Not Appeared' ORDER BY `exam_log_id` ASC;

INSERT INTO `fcps_mid_term_applicants`(`midterm_session`, `midterm_year`, `bmdc_reg_no`, `bmdc_reg_no_org`, `roll_no`, `reg_no`, `applicant_name`, `speciality`, `exam_result`) 
SELECT `midterm_session`, `midterm_year`, `bmdc_reg_no`, `bmdc_reg_no_org`, `roll_no`, `pen_no`, `applicant_name`, `speciality_name`, 'Failed' FROM `mid_term_excel` WHERE `final_result`= 'Not Appeared' AND `written_result`!='Absent' ORDER BY `exam_log_id` ASC;

INSERT INTO `fcps_mid_term_applicants`(`midterm_session`, `midterm_year`, `bmdc_reg_no`, `bmdc_reg_no_org`, `roll_no`, `reg_no`, `applicant_name`, `speciality`, `exam_result`) 
SELECT `midterm_session`, `midterm_year`, `bmdc_reg_no`, `bmdc_reg_no_org`, `roll_no`, `pen_no`, `applicant_name`, `speciality`, `final_result` FROM `mid_all` ORDER BY `midterm_year` ASC, `midterm_session` ASC;

INSERT INTO `fcps_mid_term_applicants`(`midterm_session`, `midterm_year`, `bmdc_reg_no`, `bmdc_reg_no_org`, `roll_no`, `reg_no`, `applicant_name`, `speciality`, `exam_result`) 
SELECT `midterm_session`, `midterm_year`, `bmdc_reg_no`, `bmdc_reg_no_org`, `roll_no`, `pen_no`, `applicant_name`, `speciality_name`, 'Failed' FROM `mid_term_excel` WHERE `final_result`= 'Not Appeared' AND `written_result`='Absent' AND `bmdc_reg_no_org`='A94144' ORDER BY `exam_log_id` ASC;

INSERT INTO `fcps_mid_term_applicants`(`midterm_session`, `midterm_year`, `bmdc_reg_no`, `bmdc_reg_no_org`, `roll_no`, `reg_no`, `applicant_name`, `speciality`, `exam_result`) 
SELECT `midterm_session`, `midterm_year`, `bmdc_reg_no`, `bmdc_reg_no_org`, `roll_no`, `pen_no`, `applicant_name`, `speciality_name`, 'Failed' FROM `mid_term_excel` WHERE `final_result`= 'Not Appeared' AND `written_result`='Absent' AND `bmdc_reg_no_org`='A108013' ORDER BY `exam_log_id` ASC;

INSERT INTO `fcps_mid_term_applicants`(`midterm_session`, `midterm_year`, `bmdc_reg_no`, `bmdc_reg_no_org`, `roll_no`, `reg_no`, `applicant_name`, `speciality`, `exam_result`) 
SELECT `midterm_session`, `midterm_year`, `bmdc_reg_no`, `bmdc_reg_no_org`, `roll_no`, `pen_no`, `applicant_name`, `speciality_name`, 'Failed' FROM `mid_term_excel` WHERE `final_result`= 'Not Appeared' AND `written_result`='Absent' AND `bmdc_reg_no_org`='A115603' ORDER BY `exam_log_id` ASC;

INSERT INTO `fcps_mid_term_applicants`(`midterm_session`, `midterm_year`, `bmdc_reg_no`, `bmdc_reg_no_org`, `roll_no`, `reg_no`, `applicant_name`, `speciality`, `exam_result`, `remarks`) 
SELECT `midterm_session`, `midterm_year`, `bmdc_reg_no`, `bmdc_reg_no_org`, `roll_no`, `pen_no`, `applicant_name`, `speciality_name`, 'Failed', 'RTMD Mr. Khairul Sir request to correct the result status.' FROM `mid_term_excel` WHERE `final_result`= 'Not Appeared' AND `written_result`='Absent' AND `bmdc_reg_no_org`='A94203' ORDER BY `exam_log_id` ASC;

--On request of Masum
INSERT INTO `fcps_mid_term_applicants`(`midterm_session`, `midterm_year`, `bmdc_reg_no`, `bmdc_reg_no_org`, `roll_no`, `reg_no`, `applicant_name`, `speciality`, `exam_result`, `remarks`) 
SELECT `midterm_session`, `midterm_year`, `bmdc_reg_no`, `bmdc_reg_no_org`, `roll_no`, `pen_no`, `applicant_name`, `speciality_name`, 'Failed', 'Mr. Masum corrected the result status.' FROM `mid_term_excel` WHERE `final_result`= 'Not Appeared' AND `written_result`='Absent' AND `bmdc_reg_no_org` IN ('A115583', 'A110550') ORDER BY `exam_log_id` ASC;

DELIMITER $$
CREATE TRIGGER `trg_update_honorarium_information` BEFORE UPDATE ON `honorarium_information`
 FOR EACH ROW BEGIN
 		INSERT INTO `honorarium_information_log`(`applicant_id`, `bmdc_reg_no`, `training_institute_id`, `department_name`, `honorarium_slot_id`, `honorarium_year`, `previous_training_inmonth`, `honorarium_position`, `eligible_status`, `bill_sl_no`, `eligiblity_date`, `eligible_by`, `payment_status`, `payment_date`, `payment_amount`, `payment_by`, `status`, `remarks`, `ref_id`)
        VALUES (OLD.`applicant_id`, OLD.`bmdc_reg_no`, OLD.`training_institute_id`, OLD.`department_name`, OLD.`honorarium_slot_id`, OLD.`honorarium_year`, OLD.`previous_training_inmonth`, OLD.`honorarium_position`, OLD.`eligible_status`, OLD.`bill_sl_no`, OLD.`eligiblity_date`, OLD.`eligible_by`, OLD.`payment_status`, OLD.`payment_date`, OLD.`payment_amount`, OLD.`payment_by`, OLD.`status`, OLD.`remarks`, OLD.`id`);
END
$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `trg_update_applicant_information` BEFORE UPDATE ON `applicant_information`
 FOR EACH ROW BEGIN
 		INSERT INTO `applicant_information_log`(`applicant_id`, `name`, `father_spouse_name`, `mother_name`, `date_of_birth`, `nataionality`, `religion`, `nid`, `address`, `mobile`, `telephone`, `email`, `permanent_address`, `mbbs_bds_year`, `mbbs_institute_id`, `mbbs_bds_institute`, `bmdc_reg_type`, `bmdc_reg_no`, `bmdc_validity`, `speciality_id`, `fcps_speciallity`, `fcps_roll`, `fcps_year`, `fcps_month`, `fcps_reg_no`, `pen_no`, `continuing`, `continuing_start_date`, `continuing_end_date`, `continuing_fcps_traning`, `mid_term_session`, `mid_term_year`, `mid_term_result`, `mid_term_roll`, `account_name`, `bank_id`, `bank_name`, `branch_name`, `account_no`, `routing_number`, `undertaking_confirmation`, `eligible_status`, `eligible_by`, `eligiblity_date`, `reject_reason`, `rejected_by`, `reject_date`, `status`, `gander`, `action_type`)
        VALUES (OLD.`applicant_id`, OLD.`name`, OLD.`father_spouse_name`, OLD.`mother_name`, OLD.`date_of_birth`, OLD.`nataionality`, OLD.`religion`, OLD.`nid`, OLD.`address`, OLD.`mobile`, OLD.`telephone`, OLD.`email`, OLD.`permanent_address`, OLD.`mbbs_bds_year`, OLD.`mbbs_institute_id`, OLD.`mbbs_bds_institute`, OLD.`bmdc_reg_type`, OLD.`bmdc_reg_no`, OLD.`bmdc_validity`, OLD.`speciality_id`, OLD.`fcps_speciallity`, OLD.`fcps_roll`, OLD.`fcps_year`, OLD.`fcps_month`, OLD.`fcps_reg_no`, OLD.`pen_no`, OLD.`continuing`, OLD.`continuing_start_date`, OLD.`continuing_end_date`, OLD.`continuing_fcps_traning`, OLD.`mid_term_session`, OLD.`mid_term_year`, OLD.`mid_term_result`, OLD.`mid_term_roll`, OLD.`account_name`, OLD.`bank_id`, OLD.`bank_name`, OLD.`branch_name`, OLD.`account_no`, OLD.`routing_number`, OLD.`undertaking_confirmation`, OLD.`eligible_status`, OLD.`eligible_by`, OLD.`eligiblity_date`, OLD.`reject_reason`, OLD.`rejected_by`, OLD.`reject_date`, OLD.`status`, OLD.`gander`, 'UPDATE');
END
$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `trg_delete_applicant_information` BEFORE DELETE ON `applicant_information`
 FOR EACH ROW BEGIN
 		INSERT INTO `applicant_information_log`(`applicant_id`, `name`, `father_spouse_name`, `mother_name`, `date_of_birth`, `nataionality`, `religion`, `nid`, `address`, `mobile`, `telephone`, `email`, `permanent_address`, `mbbs_bds_year`, `mbbs_institute_id`, `mbbs_bds_institute`, `bmdc_reg_type`, `bmdc_reg_no`, `bmdc_validity`, `speciality_id`, `fcps_speciallity`, `fcps_roll`, `fcps_year`, `fcps_month`, `fcps_reg_no`, `pen_no`, `continuing`, `continuing_start_date`, `continuing_end_date`, `continuing_fcps_traning`, `mid_term_session`, `mid_term_year`, `mid_term_result`, `mid_term_roll`, `account_name`, `bank_id`, `bank_name`, `branch_name`, `account_no`, `routing_number`, `undertaking_confirmation`, `eligible_status`, `eligible_by`, `eligiblity_date`, `reject_reason`, `rejected_by`, `reject_date`, `status`, `gander`, `action_type`)
        VALUES (OLD.`applicant_id`, OLD.`name`, OLD.`father_spouse_name`, OLD.`mother_name`, OLD.`date_of_birth`, OLD.`nataionality`, OLD.`religion`, OLD.`nid`, OLD.`address`, OLD.`mobile`, OLD.`telephone`, OLD.`email`, OLD.`permanent_address`, OLD.`mbbs_bds_year`, OLD.`mbbs_institute_id`, OLD.`mbbs_bds_institute`, OLD.`bmdc_reg_type`, OLD.`bmdc_reg_no`, OLD.`bmdc_validity`, OLD.`speciality_id`, OLD.`fcps_speciallity`, OLD.`fcps_roll`, OLD.`fcps_year`, OLD.`fcps_month`, OLD.`fcps_reg_no`, OLD.`pen_no`, OLD.`continuing`, OLD.`continuing_start_date`, OLD.`continuing_end_date`, OLD.`continuing_fcps_traning`, OLD.`mid_term_session`, OLD.`mid_term_year`, OLD.`mid_term_result`, OLD.`mid_term_roll`, OLD.`account_name`, OLD.`bank_id`, OLD.`bank_name`, OLD.`branch_name`, OLD.`account_no`, OLD.`routing_number`, OLD.`undertaking_confirmation`, OLD.`eligible_status`, OLD.`eligible_by`, OLD.`eligiblity_date`, OLD.`reject_reason`, OLD.`rejected_by`, OLD.`reject_date`, OLD.`status`, OLD.`gander`, 'DELETE');
END
$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `trg_update_fcps_one_pass_applicants` BEFORE UPDATE ON `fcps_one_pass_applicants`
 FOR EACH ROW BEGIN
 		INSERT INTO `fcps_one_pass_applicants_log`(`reg_no`, `applicant_name`, `father_name`, `mother_name`, `date_of_birth`, `old_date_of_birth`, `mailing_address`, `present_address`, `permanent_address`, `national_id`, `pen_number`, `subject_id`, `subject`, `contact_res`, `cell`, `tel_office`, `email`, `money_receipt_no`, `money_receipt_date`, `fcps_part_one_year_old`, `fcps_part_one_year`, `fcps_part_one_session`, `present_posting_designation`, `training_institute`, `name_address_supervisor`, `password`, `roll`, `mbbs_year`, `mbbs_institute_id`, `mbbs_institute`, `eligible_for_final_exam`, `training_completion`, `protocol_acceptance`, `dissertaion_acceptance`, `hashedotp`, `smscounter`, `status`, `ref_id`, `action_type`)
        VALUES (OLD.`reg_no`, OLD.`applicant_name`, OLD.`father_name`, OLD.`mother_name`, OLD.`date_of_birth`, OLD.`old_date_of_birth`, OLD.`mailing_address`, OLD.`present_address`, OLD.`permanent_address`, OLD.`national_id`, OLD.`pen_number`, OLD.`subject_id`, OLD.`subject`, OLD.`contact_res`, OLD.`cell`, OLD.`tel_office`, OLD.`email`, OLD.`money_receipt_no`, OLD.`money_receipt_date`, OLD.`fcps_part_one_year_old`, OLD.`fcps_part_one_year`, OLD.`fcps_part_one_session`, OLD.`present_posting_designation`, OLD.`training_institute`, OLD.`name_address_supervisor`, OLD.`password`, OLD.`roll`, OLD.`mbbs_year`, OLD.`mbbs_institute_id`, OLD.`mbbs_institute`, OLD.`eligible_for_final_exam`, OLD.`training_completion`, OLD.`protocol_acceptance`, OLD.`dissertaion_acceptance`, OLD.`hashedotp`, OLD.`smscounter`, OLD.`status`, OLD.`id`, 'UPDATE');
END
$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `trg_delete_fcps_one_pass_applicants` BEFORE DELETE ON `fcps_one_pass_applicants`
 FOR EACH ROW BEGIN
 		INSERT INTO `fcps_one_pass_applicants_log`(`reg_no`, `applicant_name`, `father_name`, `mother_name`, `date_of_birth`, `old_date_of_birth`, `mailing_address`, `present_address`, `permanent_address`, `national_id`, `pen_number`, `subject_id`, `subject`, `contact_res`, `cell`, `tel_office`, `email`, `money_receipt_no`, `money_receipt_date`, `fcps_part_one_year_old`, `fcps_part_one_year`, `fcps_part_one_session`, `present_posting_designation`, `training_institute`, `name_address_supervisor`, `password`, `roll`, `mbbs_year`, `mbbs_institute_id`, `mbbs_institute`, `eligible_for_final_exam`, `training_completion`, `protocol_acceptance`, `dissertaion_acceptance`, `hashedotp`, `smscounter`, `status`, `ref_id`, `action_type`)
        VALUES (OLD.`reg_no`, OLD.`applicant_name`, OLD.`father_name`, OLD.`mother_name`, OLD.`date_of_birth`, OLD.`old_date_of_birth`, OLD.`mailing_address`, OLD.`present_address`, OLD.`permanent_address`, OLD.`national_id`, OLD.`pen_number`, OLD.`subject_id`, OLD.`subject`, OLD.`contact_res`, OLD.`cell`, OLD.`tel_office`, OLD.`email`, OLD.`money_receipt_no`, OLD.`money_receipt_date`, OLD.`fcps_part_one_year_old`, OLD.`fcps_part_one_year`, OLD.`fcps_part_one_session`, OLD.`present_posting_designation`, OLD.`training_institute`, OLD.`name_address_supervisor`, OLD.`password`, OLD.`roll`, OLD.`mbbs_year`, OLD.`mbbs_institute_id`, OLD.`mbbs_institute`, OLD.`eligible_for_final_exam`, OLD.`training_completion`, OLD.`protocol_acceptance`, OLD.`dissertaion_acceptance`, OLD.`hashedotp`, OLD.`smscounter`, OLD.`status`, OLD.`id`, 'DELETE');
END
$$
DELIMITER ;


SELECT hi.id, hi.applicant_id, hi.bill_sl_no, hi.training_type, ap.name, ap.mobile, hi.bmdc_reg_no, ap.fcps_reg_no, ap.date_of_birth, ap.nid, ap.fcps_speciallity, ap.fcps_year, ap.fcps_month, ap.gander, hi.training_institute_id, ti.name AS training_institute_name, hi.department_id, sp.name AS department_name, hi.`current_training_slot`, hi.previous_training_inmonth, hi.honorarium_position, bnk.bank_name AS new_bank_name, ap.branch_name, ap.account_no, ap.routing_number, hi.honorarium_year,  hi.honorarium_slot_id, hs.slot_name, hi.eligible_status 
FROM `honorarium_information` hi, applicant_information ap, honorarium_slot hs, institute ti, speciality sp, banks bnk,
(SELECT `applicant_id`, `bmdc_reg_no`, `training_institute_id` 
FROM `honorarium_information` WHERE `honorarium_year`=2025 AND `honorarium_slot_id`=1) AS prv
WHERE hi.applicant_id=prv.`applicant_id`
	AND hi.bmdc_reg_no = prv.`bmdc_reg_no`
    AND hi.applicant_id = ap.applicant_id
    AND hi.`training_institute_id` = prv.`training_institute_id`
    AND hi.honorarium_slot_id = hs.id
    AND hi.department_id = sp.speciality_id
    AND ap.bank_id = bnk.id
    AND hi.training_institute_id = ti.institute_id
	AND hi.`honorarium_year`=2025 
    AND hi.`honorarium_slot_id`=2
ORDER BY hi.`bill_sl_no` ASC;


SELECT hi.bill_sl_no, hi.training_type, ap.name, ap.mobile, hi.bmdc_reg_no, ap.fcps_reg_no, ap.date_of_birth, ap.nid, ap.fcps_speciallity, ap.fcps_year, ap.fcps_month, ap.gander, ti.name AS training_institute_name, sp.name AS department_name, hi.`current_training_slot`, hi.previous_training_inmonth, hi.honorarium_position, bnk.bank_name AS new_bank_name, ap.branch_name, ap.account_no, ap.routing_number, hi.honorarium_year, hs.slot_name, hi.eligible_status 
FROM `honorarium_information` hi, `applicant_information` ap, `honorarium_slot` hs, `institute` ti, `speciality` sp, `banks` bnk,
(SELECT `applicant_id`, `bmdc_reg_no`, `training_institute_id` 
FROM `honorarium_information` WHERE `honorarium_year`=2025 AND `honorarium_slot_id`=1) AS prv
WHERE hi.applicant_id=prv.`applicant_id`
	AND hi.bmdc_reg_no = prv.`bmdc_reg_no`
    AND hi.applicant_id = ap.applicant_id
    AND hi.`training_institute_id` = prv.`training_institute_id`
    AND hi.honorarium_slot_id = hs.id
    AND hi.department_id = sp.speciality_id
    AND ap.bank_id = bnk.id
    AND hi.training_institute_id = ti.institute_id
	AND hi.`honorarium_year`=2025 
    AND hi.`honorarium_slot_id`=2
ORDER BY hi.`bill_sl_no` ASC;


SELECT hi.bill_sl_no, hi.training_type, ap.name, ap.mobile, hi.bmdc_reg_no, ap.fcps_reg_no, ap.date_of_birth, ap.nid, ap.fcps_speciallity, ap.fcps_year, ap.fcps_month, ap.gander, ti.name AS training_institute_name, sp.name AS department_name, hi.`current_training_slot`, hi.previous_training_inmonth, hi.honorarium_position, bnk.bank_name, ap.branch_name, ap.account_no, ap.routing_number, hi.honorarium_year, hs.slot_name, hi.eligible_status 
FROM `honorarium_information` hi, `applicant_information` ap, `honorarium_slot` hs, `institute` ti, `speciality` sp, `banks` bnk,
(SELECT `applicant_id`, `bmdc_reg_no`, `training_institute_id` 
FROM `honorarium_information` WHERE `honorarium_year`=2025 AND `honorarium_slot_id`=1) AS prv
WHERE hi.applicant_id=prv.`applicant_id`
	AND hi.bmdc_reg_no = prv.`bmdc_reg_no`
    AND hi.applicant_id = ap.applicant_id
    AND hi.`training_institute_id` = prv.`training_institute_id`
    AND hi.honorarium_slot_id = hs.id
    AND hi.department_id = sp.speciality_id
    AND ap.bank_id = bnk.id
    AND hi.training_institute_id = ti.institute_id
	AND hi.`honorarium_year`=2025 
    AND hi.`honorarium_slot_id`=2
ORDER BY hi.`bill_sl_no` ASC;


UPDATE `fcps_one_pass_applicants` t1
JOIN `applicant_information` t2 ON t1.`reg_no` = t2.fcps_reg_no
SET t1.`date_of_birth` = t2.date_of_birth
WHERE t1.`date_of_birth` IS NULL;

UPDATE `fcps_one_pass_applicants` t1
JOIN `applicant_information` t2 ON t1.`reg_no` = t2.fcps_reg_no
SET t1.`present_address` = t2.address
WHERE t1.`present_address` IS NULL OR t1.`present_address`='';

UPDATE `fcps_one_pass_applicants` t1
JOIN `applicant_information` t2 ON t1.`reg_no` = t2.fcps_reg_no
SET t1.`permanent_address` = t2.`permanent_address`
WHERE t1.`permanent_address` IS NULL OR t1.`permanent_address`='';

Website Link: https://trainee-database.bcps.edu.bd/login (After login go to menu: "Update Information")