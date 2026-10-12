<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Welcome to BCPS</title>
  <style>
  body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    color: #333333;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
  }

  .container {
    max-width: 600px;
    margin: 20px auto;
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-top: 5px solid #0056b3;
  }

  .content {
    padding: 20px 0;
  }

  .credentials-box {
    background-color: #f9f9f9;
    border: 1px solid #cccccc;
    padding: 15px;
    margin: 20px 0;
    border-radius: 4px;
    text-align: left;
  }

  .credentials-box p {
    margin: 5px 0;
  }

  .important-text {
    font-weight: bold;
    color: #d9534f;
  }

  .footer {
    padding-top: 10px;
    border-top: 1px solid #eeeeee;
    font-size: 12px;
    text-align: center;
    color: #777777;
  }

  .footer a {
    color: #0056b3;
    text-decoration: none;
  }
  </style>
</head>

<body>
  <div class="container">
    <div class="header" style="text-align: center; padding-bottom: 10px; border-bottom: 1px solid #eeeeee;">
      <h2>Bangladesh College of Physicians & Surgeons (BCPS)</h2>
    </div>
    <div class="content">
      <p>Dear <?=esc($recipientName)?>,</p>

      <?php if ($elogBookStatus): ?>
      <p>
        Your registration no. (afeter passing FCPS Part-I) of Bangladesh College of Physicians & Surgeons (BCPS) is
        below here:
      </p>
      <?php else: ?>
      <p>
        Your registration with the Bangladesh College of Physicians & Surgeons (BCPS) is now complete. Below are your
        login credentials:
      </p>
      <?php endif; ?>

      <div class="credentials-box">
        <?php if ($elogBookStatus): ?>
        <p><strong>Registration Number:</strong> <span class="important-text"><?=esc($regNumber)?></span></p>
        <p><strong>Login URL:</strong> <a href="<?=esc($loginUrl)?>">eLogbook Portal</a></p>
        <?php else: ?>
        <p><strong>Registration Number (User ID):</strong> <span class="important-text"><?=esc($regNumber)?></span></p>
        <p><strong>Your Temporary Password:</strong> <span class="important-text"><?=esc($password)?></span></p>
        <p><strong>Login URL:</strong> <a href="<?=esc($loginUrl)?>"><?=esc($loginUrl)?></a></p>
        <?php endif; ?>
      </div>

      <?php if (!$elogBookStatus): ?>
      <p>Please use these credentials to <a href="<?=esc($loginUrl)?>">log in to your account</a> immediately.</p>
      <?php endif; ?>
      <p>For any assistance, feel free to contact our support team.</p>
      <p>Warm regards,</p>
      <p>
        BCPS ICT Department<br>
        Bangladesh College of Physicians & Surgeons (BCPS)
      </p>
    </div>
    <div class="footer">
      <p>
        <a href="<?=esc($websiteUrl)?>">BCPS Website</a> |
        <a href="mailto:<?=esc($supportEmail)?>">Support Email: <?=esc($supportEmail)?></a> |
        Contact: <?=esc($contactNumber)?>
      </p>
    </div>
  </div>
</body>

</html>