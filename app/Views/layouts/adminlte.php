<!DOCTYPE html>
<html lang="en">
<head>
  <?= $this->include('partials/_head') ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <?= $this->include('partials/_sidebar') ?>  
  <div class="content-wrapper-with-footer">
    <?= $this->include('partials/_navbar') ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper" style="min-height: calc(100vh - 3.5rem);">
      <?= $this->renderSection('content') ?>
    </div>

    <?= $this->include('partials/_footer') ?>
  </div>
</div>

<!-- Scripts -->
<script src="<?= base_url('assets/adminlte/plugins/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/js/adminlte.min.js') ?>"></script>
<script>
  $(document).ready(function() {
    // Inisialisasi state awal
    $('body').addClass('sidebar-mini');
    
    // Handler hover
    $('.main-sidebar').hover(
      function() {
        $('body').removeClass('sidebar-collapse');
      },
      function() {
        // Kosongkan (biarkan tetap mini saat mouse leave)
      }
    );
    
    // Handler klik di luar sidebar
    $(document).on('click', function(e) {
      if (!$(e.target).closest('.main-sidebar').length) {
        $('body').addClass('sidebar-mini');
      }
    });
  });
</script>
<script>
  $(document).ready(function() {
    // Inisialisasi
    $('body').addClass('sidebar-mini');
    
    // Handler hover sidebar
    $('.main-sidebar').hover(
      function() {
        $('body').removeClass('sidebar-mini');
      },
      function() {
        $('body').addClass('sidebar-mini');
      }
    );
    
    // Handler resize window
    $(window).resize(function() {
      if ($(window).width() > 768) {
        $('body').addClass('sidebar-mini');
      }
    });
  });
</script>
<script>
  $(document).ready(function() {
    // Inisialisasi
    $('body').addClass('sidebar-mini');
    
    // Handler hover
    $('.main-sidebar').hover(
      function() {
        $('.content-wrapper, .main-header').css('margin-left', '250px');
      },
      function() {
        $('.content-wrapper, .main-header').css('margin-left', '70px');
      }
    );
  });
</script>
<script>
  $(document).ready(function() {
    // Inisialisasi sidebar mini
    $('body').addClass('sidebar-mini');
    
    // Handler hover sidebar
    $('.main-sidebar').hover(
      function() {
        $('body').removeClass('sidebar-mini');
      },
      function() {
        $('body').addClass('sidebar-mini');
      }
    );
    
    // Handler resize window
    $(window).resize(function() {
      adjustLayout();
    });
    
    function adjustLayout() {
      if ($(window).width() < 768) {
        $('body').removeClass('sidebar-mini');
      } else {
        $('body').addClass('sidebar-mini');
      }
    }
  });
</script>
</body>
</html>