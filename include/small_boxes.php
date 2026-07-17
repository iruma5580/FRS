            <!-- Small boxes (Stat box) -->
            <div class="row">
              <div class="col-lg-2 col-8">
                <!-- small box -->
                <div class="small-box bg-info">
                  <div class="inner">
                    <?php
                      $user_query = "SELECT * from assets ";
                      $user_query_run = mysqli_query($conn,$user_query);
                      if($total_users = mysqli_num_rows($user_query_run))
                      {
                        echo '<h3 class="mb-0"> '.$total_users.'</h3>';
                        echo "Total Assets In Service";
                      }
                      else
                      {
                        echo '<h3 class="mb-0">No Data Found</h3>';
                      }
                    ?>
                  </div>
                  <div class="icon">
                    <i class="ion ion-bag"></i>
                  </div>
                  <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-2 col-8">
                <!-- small box -->
                <div class="small-box bg-success">
                  <div class="inner">
                    <?php
                      $user_query = "SELECT * from assets where Status='In Service' ";
                      $user_query_run = mysqli_query($conn,$user_query);
                      if($total_users = mysqli_num_rows($user_query_run))
                      {
                        echo '<h3 class="mb-0"> '.$total_users.'</h3>';
                        echo "Total Assets In Service";
                      }
                      else
                      {
                        echo '<h3 class="mb-0">No Data Found</h3>';
                      }
                    ?>
                  </div>
                  <div class="icon">
                    <i class="ion ion-wrench"></i>
                  </div>
                  <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-2 col-8">
                <!-- small box -->
                <div class="small-box bg-warning">
                  <div class="inner">
                    <?php
                      $user_query = "SELECT * from assets where Status='Repair' ";
                      $user_query_run = mysqli_query($conn,$user_query);
                      if($total_users = mysqli_num_rows($user_query_run))
                      {
                        echo '<h3 class="mb-0"> '.$total_users.'</h3>';
                        echo "Need to Repair";
                      }
                      else
                      {
                        echo '<h3 class="mb-0">No Data Found</h3>';
                      }
                    ?>
                  </div>
                  <div class="icon">
                    <i class="ion ion-settings"></i>
                  </div>
                  <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-2 col-8">
                <!-- small box -->
                <div class="small-box bg-danger">
                  <div class="inner">
                    <?php
                      $user_query = "SELECT * from assets where Status='In Storage' ";
                      $user_query_run = mysqli_query($conn,$user_query);
                      if($total_users = mysqli_num_rows($user_query_run))
                      {
                        echo '<h3 class="mb-0"> '.$total_users.'</h3>';
                        echo "In Storage";
                      }
                      else
                      {
                        echo '<h3 class="mb-0">No Data Found</h3>';
                      }
                    ?>
                  </div>
                  <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                  </div>
                  <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-2 col-8">
                <!-- small box -->
                <div class="small-box bg-danger">
                  <div class="inner">
                      <?php
                        $user_query = "SELECT * from assets where Status='Disposed' ";
                        $user_query_run = mysqli_query($conn,$user_query);
                        if($total_users = mysqli_num_rows($user_query_run))
                        {
                          echo '<h3 class="mb-0"> '.$total_users.'</h3>';
                          echo "Need to Disposed";
                        }
                        else
                        {
                          echo '<h3 class="mb-0">No Data Found</h3>';
                        }
                      ?>
                  </div>
                  <div class="icon">
                    <i class="ion ion-trash-b"></i>
                  </div>
                  <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
            </div>
            <!-- /.row -->
