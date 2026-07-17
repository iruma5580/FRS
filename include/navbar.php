<?php
include_once('./include/dashboard_session.php');
?>

<!-- Modal structure -->
<div id="logoutModal" class="custom-logout-modal" aria-hidden="true" role="dialog" aria-labelledby="modalTitle" aria-describedby="modalDesc" tabindex="-1">
  <div class="custom-logout-modal-content" role="document">
    <h2 id="modalTitle">Confirm Logout</h2>
    <p id="modalDesc">Are you sure you want to log out?</p>
    <div class="modal-buttons">
      <button id="confirmLogout" class="btn confirm">Yes, Logout</button>
      <button id="cancelLogout" class="btn cancel">Cancel</button>
    </div>
  </div>
</div>


<style>
  /* Modal backdrop */
  .custom-logout-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0; top: 0;
    width: 100vw; height: 100vh;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(3px);
    align-items: center;
    justify-content: center;
  }
  .custom-logout-modal[aria-hidden="false"] {
    display: flex;
  }

  /* Modal box */
  .custom-logout-modal-content {
  
    color: #000000;
    padding: 24px 32px;
    border-radius: 12px;

    width: 90%;
    box-shadow: 0 8px 24px rgba(0,0,0,0.7);
    text-align: center;
    font-family: Arial, sans-serif;
  }

  .custom-logout-modal-content h2 {
    margin-top: 0;
    font-size: 1.5rem;
  }

  .custom-logout-modal-content p {
    margin: 16px 0 24px;
    font-size: 1rem;
  }

  .modal-buttons {
    display: flex;
    justify-content: center;
    gap: 16px;
  }

  .btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    font-size: 1rem;
    transition: background-color 0.3s ease;
  }

  .btn.confirm {
    background-color: #e74c3c;
    color: white;
  }
  .btn.confirm:hover {
    background-color: #c0392b;
  }

  .btn.cancel {
    background-color: #555;
    color: white;
  }
  .btn.cancel:hover {
    background-color: #333;
  }

  .main-sidebar {
  position: relative;
}

  .sidebar-about {
  position: absolute;
  bottom: 10px;
  left: 0;
  width: 100%;
  padding: 0 1rem;
}


</style>
   
   <nav class="main-header navbar navbar-expand navbar-white navbar-light"  style="background-color: #000235 !important; ">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item" >
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars text-white"></i></a>
      </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
        <a class="nav-link text-white" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
        </a>
        </li>
        <!--begin::User Menu Dropdown-->
        <li class="nav-item dropdown user-menu" >
        <a href="#" class="nav-link" data-toggle="dropdown" >
        <img
            src="<?php echo htmlspecialchars( $picture); ?>"
            class="user-image rounded-circle shadow"
            alt="User Image" onerror="this.onerror=null;this.src='uploads/default-user.jpg';" 
        />
        <span class="d-none d-md-inline text-white"><?php echo htmlspecialchars($fullname); ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end" style="background-color: #000235 !important; ">
        <!--begin::User Image-->
        
        <li class="user-header text-bg-primary" style="background-color: #000235 !important;">
            <img
                src="<?php echo htmlspecialchars( $picture); ?>"
                class="rounded-circle shadow"
                alt="User Image"
                onerror="this.onerror=null;this.src='uploads/default-user.jpg';" 
            />
            <p class="text-white">
                <?php echo htmlspecialchars($username); ?> - <?php echo htmlspecialchars($user_type); ?>
                <small>Member since <?php echo htmlspecialchars($created_at); ?></small>
            </p>
        </li>

        <!--end::User Image-->
        <!--begin::Menu Footer-->
        <li class="user-footer" style="background-color: #000235 !important; ">
            <a href="profile.php?page=profile" class="btn btn-default btn-flat text-white" style="background-color: #000235 !important; ">Profile</a>

            <!-- <form id="logoutForm" method="POST" action="logout.php" style="display:inline;">
            <button  id="logoutBtn" class="btn btn-default btn-flat float-end text-white float-right" style="background-color: #000235 !important; ">Sign out</button>
            </form> -->
            <!-- Hidden form to submit logout -->
          <form id="logoutForm" method="POST" action="logout.php" style="display:none;"></form>
          <button id="logoutBtn" class="btn btn-default btn-flat float-end text-white float-right" style="background-color: #000235 !important; ">Sign out</button>

        </li>
        <!--end::Menu Footer-->
        </ul>
        </li>
        <!--end::User Menu Dropdown-->

    </ul>
    </nav>