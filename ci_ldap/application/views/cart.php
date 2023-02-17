<?php include_once("constantv.php") ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?=app_name?> | Shopping Cart </title>

    <!-- Bootstrap -->
    <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
   <link href="../vendors/iCheck/skins/flat/green.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="../build/css/custom.min.css" rel="stylesheet">
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
                <h3>Cart# <small>123456789</small></h3>
              </div>

<!--               <div class="title_right">
                <div class="col-md-5 col-sm-5   form-group pull-right top_search">
                  <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search for...">
                    <span class="input-group-btn">
                      <button class="btn btn-default" type="button">Go!</button>
                    </span>
                  </div>
                </div>
              </div> -->
            </div>

            <div class="clearfix"></div>

            <div class="row" style="display: block;">
                            
              <div class="clearfix"></div>

              <div class="col-md-12 col-sm-12  ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Prodoct <small>Listing</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#">Settings 1</a>
                            <a class="dropdown-item" href="#">Settings 2</a>
                          </div>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>

                  <div class="x_content">

                    <!-- <p>Add class <code>bulk_action</code> to table for bulk actions options on row select</p> -->
                    <div class="col-md-8 col-sm-8  ">
                      <div class="table-responsive">
                        <table class="table table-striped jambo_table bulk_action">
                          <thead>
                            <tr class="headings">
                              <th>
                                <input type="checkbox" id="check-all" class="flat">
                              </th>
                              <th class="column-title">Product </th>
                              <th class="column-title">Unit Price </th>
                              <th class="column-title">Quantity </th>
                              <th class="column-title">Total Price </th>
                              <th class="column-title no-link last"><span class="nobr">Action</span>
                              </th>
                              <th class="bulk-actions" colspan="7">
                                <a class="antoo" style="color:#fff; font-weight:500;">Bulk Actions ( <span class="action-cnt"> </span> ) <i class="fa fa-chevron-down"></i></a>
                              </th>
                            </tr>
                          </thead>

                          <tbody>
                            <tr class="even pointer">
                              <td class="a-center ">
                                <input type="checkbox" class="flat" name="table_records">
                              </td>
                              <td class=" ">
                                <div class="image-wrapper float-left pr-3">
                                    <img src="images/item2.jpg" width="100" height="100" alt=""/>
                                </div>
                                <div class="">
                                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nihil ad, ex eaque fuga minus reprehendeim soluta omnis 
                                </div>
                              </td>
                              <td class=" ">$15.00 </td>
                              <td class=" "><input type="number" name="" value="1" width="10"></i></td>
                              <td class="a-right a-right ">$45.00</td>
                              <td class=" last"><a href="#">Delete</a>
                              </td>
                            </tr>

                            <tr class="even pointer">
                              <td class="a-center ">
                                <input type="checkbox" class="flat" name="table_records">
                              </td>
                              <td class=" ">
                                <div class="image-wrapper float-left pr-3">
                                    <img src="images/item2.jpg" width="100" height="100" alt=""/>
                                </div>
                                <div class="">
                                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nihil ad, ex eaque fuga minus reprehendeim soluta omnis 
                                </div>
                              </td>
                              <td class=" ">$15.00 </td>
                              <td class=" "><input type="number" name="" value="1" width="10"></i></td>
                              <td class="a-right a-right ">$45.00</td>
                              <td class=" last"><a href="#">Delete</a>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <div class="col-md-4 col-sm-4  ">
 
    <div class="row">
        <div class="well col-xs-12 col-sm-12 col-md-12 col-xs-offset-1 col-sm-offset-1 col-md-offset-3">
            
            <div class="row">
                <div class="text-center">
                    <h5>SUMMARY</h5>
                </div>
                </span>
                <table class="table table-hover">
                    <thead>
                        <tr>

                            <th>&nbsp;</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>


                        <tr>

                            <td class="text-right">
                            <p>
                                <strong>Price: </strong>
                            </p>
                            <p>
                                <strong>Shipment: </strong>
                            </p>
                            <p>
                                <strong>Tax: </strong>
                            </p></td>
                            <td class="text-center">
                            <p>
                                <strong>$90.00</strong>
                            </p>
                            <p>
                                <strong>$5.00</strong>
                            </p>
                            <p>
                                <strong>$6.94</strong>
                            </p>

                          </td>
                        </tr>
                        <tr>

                            <td class="text-right"><h4><strong>Total: </strong></h4></td>
                            <td class="text-center text-danger"><h4><strong>$101.94</strong></h4></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-success btn-lg btn-block">
                    Checkout Now   <span class="glyphicon glyphicon-chevron-right"></span>
                </button></td>
            </div>
        </div>



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
    <script src="../vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
   <script src="../vendors/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!-- FastClick -->
    <script src="../vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="../vendors/nprogress/nprogress.js"></script>
    <!-- iCheck -->
    <script src="../vendors/iCheck/icheck.min.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="../build/js/custom.min.js"></script>

    <script type="text/javascript">
      $(document).ready(function(){
        $('#menu_toggle').click();
        $('#menu_toggle').click();

      });
    </script>


  </body>
</html>
