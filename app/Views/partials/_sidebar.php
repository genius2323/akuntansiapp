<aside class="main-sidebar sidebar-dark-primary elevation-4" style="position: fixed;">
  <!-- Brand Logo Mini -->
     <a href="<?= site_url('/') ?>" class="brand-link text-center py-3" style="height: auto;">
        <span class="brand-text font-weight-light">
            <i class="fas fa-store d-block" style="font-size: 1.5rem; line-height: 50px;"></i>
            <style>
            .brand-link-mini {
                display: block;
                width: 50px;
                height: 50px;
                text-align: center;
                line-height: 50px;
                font-size: 1.5rem;
                color: #fff;
            }
            .sidebar-mini .brand-link {
                display: none;
            }
            </style>
        </span>
    </a>
  
  <!-- Sidebar -->
  <div class="sidebar" style="height: calc(100vh - 50px);">
    <?= $this->include('partials/_sidebar_menu') ?>
  </div>
</aside>