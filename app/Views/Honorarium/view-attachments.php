<?php
    $attachments = [
    ['type' => 'signature', 'attachement_title' => 'Applicant’s Signature & Date'],
    ['type' => 'photograph', 'attachement_title' => 'Recent passport size color photograph'],
    ['type' => 'letter', 'attachement_title' => 'Congratulation letter of FCPS Part-I'],
    ['type' => 'certificate', 'attachement_title' => 'Certificate of MBBS/BDS'],
    ['type' => 'registration', 'attachement_title' => 'Permanent registration certificate of BMDC'],
    ['type' => 'training_certificate', 'attachement_title' => 'Training certificates (if applicable)'],
    ['type' => 'nid_card', 'attachement_title' => 'National ID Card'],
    ['type' => 'cheque', 'attachement_title' => 'Bank Cheque Book'],
    ['type' => 'provi_certifice', 'attachement_title' => 'Provisional training certificate'],
    ['type' => 'other_document1', 'attachement_title' => 'Joining letter/ Testimonial'],
    ['type' => 'other_document2', 'attachement_title' => 'Other necessary documents'],
    ['type' => 'fcps_congrats', 'attachement_title' => 'FCPS Part-I Congratulations Letter'],
    ['type' => 'midterm_congrats', 'attachement_title' => 'FCPS Midterm Congratulations Letter (if applicable)'],
    ];
?>
<table class="table">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">File Name</th>
      <th scope="col">View</th>
    </tr>
  </thead>
  <tbody>
    <?php
        $sl = 1;
        foreach ($files as $file) {
            $key = array_search($file['type'], array_column($attachments, 'type'));
        ?>
    <tr>
      <td scope="row"><?=$sl++;?></td>
      <td><?=$attachments[$key]['attachement_title']?></td>
      <td><a href="<?=base_url()?>public/uploads/honorariums/<?=$file['file_name']?>" target="_blank">View</a></td>
    </tr>
    <?php }?>
  </tbody>
</table>