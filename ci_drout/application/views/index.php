


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?=app_name?> | Home</title>

    <!-- Bootstrap -->
    <link href="<?php echo base_url()?>/assets/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?php echo base_url()?>/assets/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="<?php echo base_url()?>/assets/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- bootstrap-progressbar -->
    <link href="<?php echo base_url()?>/assets/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="<?php echo base_url()?>/assets/build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="nav-sm">
    <div class="container body">
      <div class="main_container">
        <?php include_once("sidebar.php");?>


        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Featured Products</h3>
              </div>

              <div class="title_right">
                <div class="col-md-5 col-sm-5   form-group pull-right top_search">
                  <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search for...">
                    <span class="input-group-btn">
                      <button class="btn btn-default" type="button">Go!</button>
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12">
                <div class="">
                  <div class="x_content">




                    <br />
                    <div class="row" rem="card container">

                     <!-- start of card -->
                     <a href="item_view.php">
                      <div class="col-md-3   widget widget_tally_box">
                        <div class="x_panel ui-ribbon-container fixed_height_item_card">
                          <div class="ui-ribbon-wrapper">
                            <div class="ui-ribbon">
                              30% Off
                            </div>
                          </div>
                          <div class="x_title">
                            <h2>MVP Shoe</h2>
                            <div class="clearfix"></div>
                          </div>
                          <div class="x_content">

                            <div style="text-align: center; margin-bottom: 17px">
                            	<img src="<?php echo base_url()?>/uploads/sample1.jpg" width="240" height="240">
                            </div>
                            <div class="flex">
                              <ul class="list-inline count2">
                                <li>
                                  <h3>$59.00</h3>
                                  <span>Price</span>
                                </li>
                                <li>
                                  <h3>&nbsp;</h3>
                                  <span>&nbsp;</span>
                                </li>
                                <li>
                                  <h3>******</h3>
                                  <span>Rating</span>
                                </li>
                              </ul>
                            </div>

                            <p>Sample description of the product! Sample description of the product! Sample description of the product!</p>

                          </div>
                        </div>
                      </div>
                     </a>
                      <!-- end of card -->



            <div class="page-title">
              <div class="title_left">
                <h3>Popular Products</h3>
              </div>
            </div>
<br><br><br><br>
            <div class="clearfix"></div>



                     <!-- start of card -->
                     <a href="item_view.php">
                      <div class="col-md-3   widget widget_tally_box">
                        <div class="x_panel ui-ribbon-container fixed_height_item_card">
                          <div class="ui-ribbon-wrapper">
                            <div class="ui-ribbon">
                              30% Off
                            </div>
                          </div>
                          <div class="x_title">
                            <h2>Baby Kit bag</h2>
                            <div class="clearfix"></div>
                          </div>
                          <div class="x_content">

                            <div style="text-align: center; margin-bottom: 17px">
                            	<img src="<?php echo base_url()?>/uploads/sample1.jpg" width="240" height="240">
                            </div>
                            <div class="flex">
                              <ul class="list-inline count2">
                                <li>
                                  <h3>$8.00</h3>
                                  <span>Price</span>
                                </li>
                                <li>
                                  <h3>&nbsp;</h3>
                                  <span>&nbsp;</span>
                                </li>
                                <li>
                                  <h3>******</h3>
                                  <span>Rating</span>
                                </li>
                              </ul>
                            </div>

                            <p>Sample description of the product! Sample description of the product! Sample description of the product!</p>

                          </div>
                        </div>
                      </div>
                     </a>
                      <!-- end of card -->



                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
          <div class="pull-right">
            Powered by <a href="https://colorlib.com">Colorlib</a>
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->

      </div>
    </div>


    <!-- jQuery -->
    <script src="<?php echo base_url()?>/assets/vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
   <script src="<?php echo base_url()?>/assets/vendors/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!-- FastClick -->
    <script src="<?php echo base_url()?>/assets/vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="<?php echo base_url()?>/assets/vendors/nprogress/nprogress.js"></script>
    <!-- Chart.js -->
    <script src="<?php echo base_url()?>/assets/vendors/Chart.js/dist/Chart.min.js"></script>
    <!-- jQuery Sparklines -->
    <script src="<?php echo base_url()?>/assets/vendors/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
    <!-- easy-pie-chart -->
    <script src="<?php echo base_url()?>/assets/vendors/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js"></script>
    <!-- bootstrap-progressbar -->
    <script src="<?php echo base_url()?>/assets/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="<?php echo base_url()?>/assets/build/js/custom.min.js"></script>

    <script type="text/javascript">
    	$(document).ready(function(){
	    	$('#menu_toggle').click();
	    	$('#menu_toggle').click();

    	});
    </script>


  </body>
</html>
