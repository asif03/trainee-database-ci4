<?php $this->extend(config('Auth')->views['layout'])?>

<?php $this->section('title')?>BCPS :: <?=lang('Auth.login')?><?php $this->endSection()?>

<?php $this->section('main')?>

<div class="login-card p-5">
  <div class="text-center mb-5">
    <!-- Logo, consistent with the landing page -->
    <div class="text-nowrap logo-img text-center d-block py-3 w-100 text-decoration-none d-flex
      align-items-center justify-content-center gap-1">
      <img src="<?php echo base_url(); ?>public/logo.png" width="40" height="40" alt="BCPS Logo" />
    </div>
    <h3 class=" fw-bold text-success mb-1">Trainee Login</h3>
    <p class="text-muted">Welcome back! Please sign in to your account.</p>
    <p class="text-danger fw-bold">Update Information Except e-Logbook [FCPS Part-I trainees (Medicine, Surgery,
      Obstetrics &
      Gynaecology,
      Paediatrics, and
      Allied subjects) who passed from January, 2025 onward]</p>
  </div>
  <?php if (session('error') !== null): ?>
  <div class="alert alert-danger" role="alert">
    <?=session('error')?>
  </div>
  <?php elseif (session('errors') !== null): ?>
  <div class="alert alert-danger" role="alert">
    <?php if (is_array(session('errors'))): ?>
    <?php foreach (session('errors') as $error): ?>
    <?=$error?>
    <br>
    <?php endforeach?>
    <?php else: ?>
    <?=session('errors')?>
    <?php endif?>
  </div>
  <?php endif?>

  <?php if (session('message') !== null): ?>
  <div class="alert alert-success" role="alert"><?=session('message')?></div>
  <?php endif?>

  <form action="<?=url_to('login')?>" method="post">
    <?=csrf_field()?>
    <div class="mb-3">
      <label for="floatingUserNameInput" class="form-label text-muted"><?=lang('Auth.username')?></label>
      <input type="text" name="username" inputmode="username" autocomplete="username"
        class="form-control rounded-pill py-2" id="floatingUserNameInput" placeholder="BCPS 10 Digits Reg. No."
        value="<?=old('username')?>" required>
    </div>
    <div class="mb-3">
      <label for="floatingPasswordInput" class="form-label text-muted"><?=lang('Auth.password')?></label>
      <input type="password" class="form-control rounded-pill py-2" id="floatingPasswordInput" name="password"
        inputmode="text" autocomplete="current-password" placeholder="<?=lang('Auth.password')?>" required>
    </div>
    <div class="d-grid mb-4">
      <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm">
        Login
      </button>
    </div>
    <div class="text-center">
      <a href="<?=base_url('registration-no-sms')?>" class="text-green text-decoration-none fw-bold">Forgot
        Registration No. & Password?</a>
    </div>
    <hr class="my-4">
    <div class="text-center">
      <p class="text-muted">Don't have Registration No. & Password? <a href="<?=base_url('registration-no-sms')?>"
          class="text-green text-decoration-none fw-bold">Click Here</a></p>
    </div>
  </form>

</div>

<?php $this->endSection()?>