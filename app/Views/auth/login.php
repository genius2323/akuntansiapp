<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div class="login-box">
  <div class="login-logo">
    <a href="#"><b>Login</b> System</a>
  </div>
  
  <div class="card">
    <div class="card-body login-card-body">
      <?php if (session('error')): ?>
        <div class="alert alert-danger"><?= session('error') ?></div>
      <?php endif; ?>

      <?php if (isset($errors)): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
              <li><?= esc($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form action="<?= base_url('authenticate') ?>" method="post">
        <?= csrf_field() ?>
        
        <div class="input-group mb-3">
          <input type="text" name="username" class="form-control" placeholder="Username" 
                 value="<?= old('username') ?>" autofocus>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        
        <div class="form-group mb-3">
          <select name="department" class="form-control">
              <?php foreach ($departments as $dept): ?>
                  <option value="<?= $dept['id'] ?>"><?= esc($dept['name']) ?></option>
              <?php endforeach; ?>
          </select>
        </div>
        
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Login</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<?= $this->endSection() ?>