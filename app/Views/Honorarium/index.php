<?php $this->extend('layout')?>

<?php $this->section('title')?>Honorarium<?php $this->endSection()?>

<?php $this->section('pageheader')?>
<h4 class="page-title"><?=$pageTitle?></h4>
<ul class="breadcrumbs">
  <li class="nav-home">
    <a href="dashboard">
      <i class="fas fa-home"></i>
    </a>
  </li>
  <li class="separator">
    <i class="fa fa-chevron-right" aria-hidden="true"></i>
  </li>
  <li class="nav-item">
    <a href="#">Bills</a>
  </li>
  <li class="separator">
    <i class="fa fa-chevron-right" aria-hidden="true"></i>
  </li>
  <li class="nav-item">
    <a href="#">Hohorarium Info</a>
  </li>
</ul>
<?php $this->endSection()?>

<?php $this->section('main')?>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header flex justify-between">
        <h4 class="card-title">List of Applicants applied for honorarium</h4>
        <div class="mt-2 d-flex gap-3 justify-content-center align-items-center">
          <h6 class="fw-bold">Select Year & Session:</h6>
          <div class="form-floating col-3">
            <select class="form-select" id="honorariumYear" aria-label="Floating label select example"
              onchange="displayStatistics()">
              <?php for ($year = date("Y"); $year >= 2024; $year--): ?>
              <option value="<?=$year?>"><?=$year?></option>
              <?php endfor?>
            </select>
            <label for="honorariumYear">Honorarium Year</label>
          </div>
          <div class="form-floating col-3">
            <select class="form-select" id="honorariumSession" aria-label="Floating label select example"
              onchange="displayStatistics()">
              <?php foreach ($slots as $slot): ?>
              <option value="<?=$slot['id']?>"><?=$slot['slot_name']?></option>
              <?php endforeach?>
            </select>
            <label for="honorariumSession">Honorarium Slot</label>
          </div>
          <div class="col-3">
            <canvas id="doughnutChart"></canvas>
          </div>
        </div>
        <div class="card-body">
          <table id="billList" class="display" style="width:100%">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Father/Spouse Name</th>
                <th>BMDC Reg. No.</th>
                <th>Online Reg. No.</th>
                <th>Bill Sl. No.</th>
                <th>Session</th>
                <th>Year</th>
                <th>Files</th>
                <th>Eligible Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Father/Spouse Name</th>
                <th>BMDC Reg. No.</th>
                <th>Online Reg. No.</th>
                <th>Bill Sl. No.</th>
                <th>Session</th>
                <th>Year</th>
                <th>Files</th>
                <th>Eligible Status</th>
                <th>Action</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="honorariumModal" tabindex="-1" aria-labelledby="honorariumModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="honorariumModalLabel">Applicant's File Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalContents"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="viewHonorariumModal" tabindex="-1" aria-labelledby="viewHonorariumLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewHonorariumLabel">Applicant's Bill Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="viewHonorariumContents"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="viewHonorariumEditModal" tabindex="-1" aria-labelledby="viewHonorariumEditLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content" id="viewHonorariumEditContents"></div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="viewTrainingModal" tabindex="-1" aria-labelledby="viewTrainingLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content" id="viewHonorariumTrainingContents"></div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="viewHonorariumTrainingEditModal" tabindex="-1"
  aria-labelledby="viewHonorariumTrainingEditLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content" id="viewHonorariumTrainingEditContents"></div>
  </div>
</div>
<?php $this->endSection()?>

<?php $this->section('pageScripts')?>
<script>
loadChartData(); // Call function to load data
function loadChartData() {

  var honorariumYear = $('#honorariumYear').val();
  var honorariumSession = $('#honorariumSession').val();

  fetch('<?=base_url('bills/get-statistics')?>', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        honorariumYear: honorariumYear,
        honorariumSession: honorariumSession
      }) // Sending POST data
    })
    .then(response => response.json())
    .then(res => {
      const ctx = document.getElementById('doughnutChart').getContext('2d');
      new Chart(ctx, {
        type: 'doughnut',
        data: {
          datasets: [{
            data: res.values,
            backgroundColor: ['#ffc107', '#31ce36', '#dc3545']
          }],
          labels: res.labels,
          options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
              position: "bottom",
            },
            layout: {
              padding: {
                left: 5,
                right: 5,
                top: 5,
                bottom: 5,
              },
            },
          },
        }
      });
    })
    .catch(error => console.error('Error fetching data:', error));
}

function displayStatistics() {
  loadChartData();
  $('#billList').DataTable().ajax.reload();
}

$('#billList').DataTable({
  "processing": true,
  "serverSide": true,
  "responsive": true,
  "ajax": {
    "url": "<?=base_url('bills/fetch-honorariums')?>",
    "type": "POST",
    "data": function(data) {
      data.honorariumYear = $('#honorariumYear').val();
      data.honorariumSession = $('#honorariumSession').val();
    },
  },
  "columns": [{
      "data": "id"
    },
    {
      "data": "name"
    },
    {
      "data": "father_spouse_name"
    },
    {
      "data": "bmdc_reg_no"
    },
    {
      "data": "fcps_reg_no"
    },
    {
      "data": "bill_sl_no"
    },
    {
      "data": "slot_name"
    },
    {
      "data": "honorarium_year"
    },
    {
      "data": "applicant_id",
      "render": function(data, type, row) {
        return `<button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#honorariumModal" onclick="getFilesInfo(${row.applicant_id})"><i class="fa fa-eye" aria-hidden="true"></i></button>`;
      }
    },
    {
      "data": "eligible_status",
      "render": function(data, type, row) {
        if (data == 'P') {
          return `<span class="badge rounded-pill badge-warning">Pending</span>`;
        } else if (data == 'Y') {
          return `<span class="badge rounded-pill badge-success">Eligible</span>`;
        } else if (data == 'N') {
          return `<span class="badge rounded-pill badge-danger">Not Eligible</span>`;
        } else {
          return `<span class="badge rounded-pill badge-danger">Rejected</span>`;
        }
      }
    },
    {
      "data": null,
      "render": function(data, type, row) {
        $action = '';
        if (row.eligible_status == 'P') {
          <?php if (auth()->user() && auth()->user()->can('bills.approve')): ?>
          $action +=
            `<button class="btn btn-success font-weight-bold btn-approve btn-sm" data-id="${row.id}"><i class="fas fa-check-circle"></i> Approve</button> `;
          <?php endif; ?>
          <?php if (auth()->user() && auth()->user()->can('bills.reject')): ?>
          $action +=
            `<button class="btn btn-danger font-weight-bold btn-reject btn-sm" data-id="${row.id}"><i class="fas fa-times-circle"></i> Reject</button> `;
          <?php endif; ?>
        }
        $action +=
          `<button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewHonorariumModal" onclick="loadHonorariumView(${row.id})"><i class="fa fa-eye" aria-hidden="true"></i></button>`;
        $action +=
          `<button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewTrainingModal" onclick="loadTrainingView(${row.id})"><i class="fa fa-list"></i></button>`;
        if (row.eligible_status == 'P') {
          <?php if (auth()->user() && auth()->user()->can('bills.edit')): ?>
          $action +=
            `<button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewHonorariumEditModal" onclick="loadEditView(${row.id})"><i class="fas fa-edit"></i></button>`;
          <?php endif; ?>
        }
        if (row.eligible_status == 'P') {
          <?php if (auth()->user() && auth()->user()->can('bills.training.edit')): ?>
          $action +=
            `<button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewHonorariumTrainingEditModal" onclick="loadEditTrainingView(${row.id})"><i class="fa fa-tasks" aria-hidden="true"></i></button>`;
          <?php endif; ?>
        }
        if (row.eligible_status == 'P') {
          $action +=
            `<a class="btn btn-outline-info btn-sm" href="<?=base_url('bills/download-honorarium-form')?>/${row.id}" target="_blank"><i class="fas fa-download"></i></a>`;
        }
        return $action;
      }
    }
  ],
  "columnDefs": [{
      "target": 0,
      "visible": false,
      "searchable": false
    },
    {
      "targets": [4],
      "className": "dt-left"
    },
    {
      "targets": [3, 5, 9],
      "className": "dt-center"
    },
    {
      "target": 6,
      "orderable": false,
      "searchable": false
    },
    {
      "target": 7,
      "orderable": false,
      "searchable": false
    },
    {
      "target": 8,
      "orderable": false
    },
  ]
});

// Handle click event on View button
$('#billList tbody').on('click', '.btn-approve', function() {
  var honorariumId = $(this).data('id');

  Swal.fire({
    title: "Are you sure to make Eligible?",
    text: "You won't be able to revert this!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, Make Eligible!"
  }).then((result) => {
    if (result.isConfirmed) {

      // AJAX request
      $.ajax({
        url: '<?=base_url('bills/approve-honorarium')?>',
        type: 'POST',
        data: {
          honorariumId: honorariumId
        },
        dataType: 'json',
        success: function(response) {
          if (response.status == 'success') {
            Swal.fire({
              title: "Approved!",
              text: response.message,
              icon: "success"
            });
          } else {
            Swal.fire({
              title: "Error!",
              text: response.message,
              icon: "error"
            });
          }
          // Reload DataTable
          $('#billList').DataTable().ajax.reload();
        },
        error: function(xhr, status, error) {
          console.error('Error:', error);
        }
      });
    }
  });
});

// Handle click event on View button
$('#billList tbody').on('click', '.btn-reject', function() {
  var honorariumId = $(this).data('id');

  Swal.fire({
    title: "Are you sure to Reject?",
    icon: "warning",
    input: "textarea",
    inputLabel: "Reject Reason",
    inputPlaceholder: "Enter reason for rejection",
    inputAttributes: {
      autocapitalize: "off"
    },
    inputValidator: (value) => {
      if (!value) {
        return "You need to write something!";
      }
    },
    showCancelButton: true,
    confirmButtonText: "Reject it",
    showLoaderOnConfirm: true,
    allowOutsideClick: () => !Swal.isLoading()
  }).then((result) => {
    if (result.isConfirmed) {
      // AJAX request
      $.ajax({
        url: '<?=base_url('bills/reject-honorarium')?>',
        type: 'POST',
        data: {
          honorariumId: honorariumId,
          rejectReason: result.value
        },
        dataType: 'json',
        success: function(response) {
          // Show notification
          if (response.status == 'success') {
            Swal.fire({
              title: "Rejected!",
              text: response.message,
              icon: "success"
            });
          } else {
            Swal.fire({
              title: "Error!",
              text: response.message,
              icon: "error"
            });
          }
          // Reload DataTable
          $('#billList').DataTable().ajax.reload();
        },
        error: function(xhr, status, error) {
          Swal.fire({
            title: "Error!",
            text: error,
            icon: "error"
          });
        }
      });
    }
  });
});

function getFilesInfo(applicationId) {

  $.ajax({
    url: '<?=base_url('bills/fetch-files')?>',
    type: 'POST',
    data: {
      applicationId: applicationId
    },
    dataType: 'html',
    success: function(response) {
      $('#modalContents').html(response);
    },
    error: function(xhr, status, error) {
      console.error('Error:', error);
    }
  });
}

function loadHonorariumView(honorariumId) {
  $.ajax({
    type: 'GET',
    url: '<?php echo base_url(); ?>bills/fetch-honorarium/' + honorariumId,
    success: function(response) {
      $('#viewHonorariumContents').html(response);
    },
    error: function(xhr, status, error) {
      console.error('Error:', error);
    }
  });
}

function loadEditView(honorariumId) {
  $.ajax({
    type: 'GET',
    url: '<?php echo base_url(); ?>bills/fetch-honorarium/edit/' + honorariumId,
    success: function(response) {
      $('#viewHonorariumEditContents').html(response);
    },
    error: function(xhr, status, error) {
      console.error('Error:', error);
    }
  });
}

function loadTrainingView(honorariumId) {
  $.ajax({
    type: 'GET',
    url: '<?php echo base_url(); ?>bills/fetch-honorarium-trainings/' + honorariumId,
    success: function(response) {
      $('#viewHonorariumTrainingContents').html(response);
    },
    error: function(xhr, status, error) {
      console.error('Error:', error);
    }
  });
}

function loadEditTrainingView(honorariumId) {
  $.ajax({
    type: 'GET',
    url: '<?php echo base_url(); ?>bills/fetch-honorarium-training/edit/' + honorariumId,
    success: function(response) {
      $('#viewHonorariumTrainingEditContents').html(response);
    },
    error: function(xhr, status, error) {
      console.error('Error:', error);
    }
  });
}
</script>
<?php $this->endSection()?>