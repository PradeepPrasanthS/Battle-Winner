<?php 
  include("include/security.php");
  include("include/conn.php");

$ch = curl_init();
curl_setopt_array($ch, array(
    CURLOPT_URL => "https://api.envato.com/v3/market/author/sale?code={$code}",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 20,
    
    CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer {$personalToken}",
        "User-Agent: {$userAgent}"
    )
));

$response = @curl_exec($ch);

$body = @json_decode($response);

if (isset($body->item->name)) {

    $id = $body->item->id;
    $name = $body->item->name;

    if($id == 23898180) {
      
        $selquery4 = "select * from tbl_push_notification where id=1";
        $selresult4 = mysqli_query($conn,$selquery4);
        $selres4 = mysqli_fetch_array($selresult4);

        if(isset($_POST['submit']))
        {

           if($_POST['external_link']!="")
           {
              $external_link = $_POST['external_link'];
           }
           else
           {
              $external_link = false;
           } 

          if($_FILES['big_picture']['name']!="")
          {   

              $big_picture=rand(0,99999)."_".$_FILES['big_picture']['name'];
              $tpath2='images/'.$big_picture;
              move_uploaded_file($_FILES["big_picture"]["tmp_name"], $tpath2);

              $file_path = 'http://'.$_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']).'/images/'.$big_picture;
                
              $content = array(
                               "en" => $_POST['notification_msg']                                                 
                               );

              $fields = array(
                              'app_id' => $selres4['appid'],
                              'included_segments' => array('All'),                                            
                              /*'data' => array("foo" => "bar","cat_id"=>$_POST['cat_id'],"cat_name"=>$cat_name,"external_link"=>$external_link),*/
                              'data' => array("foo" => "bar","external_link"=>$external_link),
                              'headings'=> array("en" => $_POST['notification_title']),
                              'contents' => $content,
                              'big_picture' =>$file_path                    
                              );

              $fields = json_encode($fields);
              // print("\nJSON sent:\n");
              // print($fields);

              $ch = curl_init();
              curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
              curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                                         'Authorization: Basic '.$selres4['auth_key']));
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
              curl_setopt($ch, CURLOPT_HEADER, FALSE);
              curl_setopt($ch, CURLOPT_POST, TRUE);
              curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

              $response = curl_exec($ch);
              curl_close($ch);

              
          }
          else
          {

       
              $content = array(
                               "en" => $_POST['notification_msg']
                                );

              $fields = array(
                              'app_id' => $selres4['appid'],
                              'included_segments' => array('All'),                                      
                              'data' => array("foo" => "bar","cat_id"=>$_POST['cat_id'],"cat_name"=>$cat_name,"external_link"=>$external_link),
                              'headings'=> array("en" => $_POST['notification_title']),
                              'contents' => $content
                              );

              $fields = json_encode($fields);
              // print("\nJSON sent:\n");
              // print($fields);

              $ch = curl_init();
              curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
              curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                                         'Authorization: Basic '.$selres4['auth_key']));
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
              curl_setopt($ch, CURLOPT_HEADER, FALSE);
              curl_setopt($ch, CURLOPT_POST, TRUE);
              curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

              $response = curl_exec($ch);
              
              
              
              curl_close($ch);


          }
              
          $notification_title = mysqli_real_escape_string($conn,$_POST['notification_title']);
          $notification_msg = mysqli_real_escape_string($conn,$_POST['notification_msg']);
          $external_link = mysqli_real_escape_string($conn,$_POST['external_link']);

          $txtDate = date("Y-m-d H:i:s");

          $insquery = "insert into announcement_details (title,message,image,url,created) values('{$notification_title}','{$notification_msg}','{$tpath2}','{$external_link}','{$txtDate}')";

          if(mysqli_query($conn,$insquery))
          {
              //$client_lang='';
              $_SESSION['msg']="Announcement send Successfully";
           
              header( "Location:announcement.php");
              exit;
          }
          else
          {
              //echo $insquery;
              echo '<script type="text/javascript">';
              echo 'setTimeout(function () { swal(
                                                    "Oops...",
                                                    "Something went wrong !!",
                                                    "error"
                                                  );';
              echo '}, 1000);</script>';
          }

        
      }

      if(isset($_GET['id']))
      {
        $id = $_GET['id'];
        
        $getquery1 = "select * from announcement_details where id={$id}";
        $getresult1 = mysqli_query($conn,$getquery1);
        $getres1 = mysqli_fetch_array($getresult1); 
      }

      if(isset($_POST['btnUpdate']))
      {
          $notification_title = mysqli_real_escape_string($conn,$_POST['notification_title']);
          $notification_msg = mysqli_real_escape_string($conn,$_POST['notification_msg']);
          $external_link = mysqli_real_escape_string($conn,$_POST['external_link']);

        if(isset($_FILES['big_picture']))
          {
            $file1 = $_FILES['big_picture'];

            //file properties

            $file1_name=$file1['name'];
            $file1_tmp=$file1['tmp_name'];
            $file1_error=$file1['error'];

            //file extension

            $file_ext=explode('.',$file1_name);
            $file_ext = strtolower($file1_name);

            if($file1_error==0)
            {
              $file1_new = uniqid('',true).'.'.$file_ext;
              $file1_destination='images/'.$file1_new;
              move_uploaded_file($file1_tmp,$file1_destination);
            }

            if(isset($file1_destination))
            {
              $big_picture=$file1_destination;
              
            }
            else
            {
              $big_picture="";
            }
          }
          else
          {
            echo "image not load";
          }
        $txtMdate = date("Y-m-d H:i:s");

        if (!empty($_FILES['big_picture']['name'])) {
          $insquery = "update announcement_details set title='$notification_title', message='$notification_msg', image='{$big_picture}', url='$external_link' where id = $id";
        }
        else
        {
          $insquery = "update announcement_details set title='$notification_title', message='$notification_msg', url='$external_link' where id = $id";
        }

        if(mysqli_query($conn,$insquery))
          {
            header("Location:announcement-history");
          }
          else
          {
              //echo $insquery;
              echo '<script type="text/javascript">';
              echo 'setTimeout(function () { swal(
                                                    "Oops...",
                                                    "Something went wrong !!",
                                                    "error"
                                                  );';
              echo '}, 1000);</script>';
          }

      }
  
   } else {
        header("location:error.php");
      exit;
    }
}
else
{
    header("location:error.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Announcement</title>

    <?php include_once("include/head-section.php"); ?>
    <!-- DataTables -->
    <link href="assets/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
    <link href="assets/plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="assets/plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="assets/plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="assets/plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="assets/plugins/datatables/dataTables.colVis.css" rel="stylesheet" type="text/css"/>
    <link href="assets/plugins/datatables/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="assets/plugins/datatables/fixedColumns.dataTables.min.css" rel="stylesheet" type="text/css"/>
    <script src="https://cdn.ckeditor.com/4.11.3/standard/ckeditor.js"></script>
    <script language="JavaScript" type="text/javascript">
      function checkDelete(){
          return confirm('Are you sure you want to delete this Record?');
      }
    </script>
    <style type="text/css">
      .validation
      {
        font-size: 12px;
        color: #f6504d;
      }
      .validation-box
      {
        border-color: #f6504d;
      }
    </style>
  </head>

  <body class="fixed-left">

    <!-- Begin page -->
    <div id="wrapper">

      <!-- topbar and sidebar -->
      <?php include_once("include/navbar.php"); ?>

      <!-- ============================================================== -->
      <!-- Start right Content here -->
      <!-- ============================================================== -->
      <div class="content-page">
        <!-- Start content -->
        <div class="content">
          <div class="container">

            <!-- Page Content -->
            <div class="row">
              <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card-box">
                  
                  <h4 class="m-t-0 header-title"><b>Announcement</b></h4>
                  <p class="text-muted font-13 m-b-30">
                      Announcement for user.
                  </p>
                  <div class="col-md-12 col-sm-12">
                    <?php if(isset($_SESSION['msg'])){?> 
                     <div class="alert alert-success alert-dismissible" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                      <?php echo $_SESSION['msg'] ; ?></a> </div>
                    <?php unset($_SESSION['msg']);}?> 
                  </div>
                  <?php if(isset($_GET['id'])) { ?>
                  <form action="announcement?id=<?php echo $_GET['id'];?>" data-parsley-validate novalidate method="post" enctype="multipart/form-data">
                    
                    <div class="row">
                        <div class="col-lg-12"> 
                            <div class="row">
                              <div class="col-md-12">
                                <div class="form-group">
                                  <label for="txtDesc">Title *</label>
                                  <input type="text" name="notification_title" id="notification_title" class="form-control" value="<?php echo $getres1['title']; ?>" placeholder="" required>
                                </div>
                              </div>
                              <div class="col-md-12">
                                <div class="form-group">
                                  <label for="txtDesc">Message *</label>
                                  <textarea name="notification_msg" id="notification_msg" class="form-control" required><?php echo $getres1['message']; ?></textarea>
                                  <!-- <script>
                                          CKEDITOR.replace( 'notification_msg' );
                                  </script> -->
                                </div>
                              </div>
                              <div class="col-md-12">
                                <div class="form-group">
                                  <label for="txtDesc">Image</label>
                                  <input type="file" name="big_picture" value="" id="fileupload">
                                  <!-- <div class="fileupload_img"><img type="image" src="assets/images/add-image.png" alt="category image" /></div> -->
                                  <small>Recommended resolution: 600x293 or 650x317 or 700x342 or 750x366</small>
                                </div>
                              </div>
                              <div class="col-md-12">
                                <div class="form-group">
                                  <label for="txtDesc">External Link</label>
                                  <input type="text" name="external_link" id="external_link" class="form-control" value="<?php echo $getres1['url']; ?>" placeholder="http://www.pubg.skyforcoding.com">
                                </div>
                              </div>
                            </div><br>
                        </div>
                    </div>
                     <!-- end row -->

                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group text-right m-b-0">
                          <button class="btn btn-primary waves-effect waves-light" type="submit" name="btnUpdate" > Send</button>
                          <!-- <a href="user-list.php" class="btn btn-default waves-effect waves-light m-l-5"> Cancel</a> -->
                          <a href="announcement-history" class="btn btn-default waves-effect waves-light"> Cancel</a>
                        </div>
                      </div>
                    </div>
                  </form>
                  <?php } else { ?>
                  <form action="" data-parsley-validate novalidate method="post" enctype="multipart/form-data">
                    
                    <div class="row">
                        <div class="col-lg-12"> 
                            <div class="row">
                              <div class="col-md-12">
                                <div class="form-group">
                                  <label for="txtDesc">Title *</label>
                                  <input type="text" name="notification_title" id="notification_title" class="form-control" value="" placeholder="" required>
                                </div>
                              </div>
                              <div class="col-md-12">
                                <div class="form-group">
                                  <label for="txtDesc">Message *</label>
                                  <textarea name="notification_msg" id="notification_msg" class="form-control" required></textarea>
                                  <!-- <script>
                                          CKEDITOR.replace( 'notification_msg' );
                                  </script> -->
                                </div>
                              </div>
                              <div class="col-md-12">
                                <div class="form-group">
                                  <label for="txtDesc">Image</label>
                                  <input type="file" name="big_picture" value="" id="fileupload">
                                  <!-- <div class="fileupload_img"><img type="image" src="assets/images/add-image.png" alt="category image" /></div> -->
                                  <small>Recommended resolution: 600x293 or 650x317 or 700x342 or 750x366</small>
                                </div>
                              </div>
                              <div class="col-md-12">
                                <div class="form-group">
                                  <label for="txtDesc">External Link</label>
                                  <input type="text" name="external_link" id="external_link" class="form-control" value="" placeholder="http://www.pubg.skyforcoding.com">
                                </div>
                              </div>
                            </div><br>
                        </div>
                    </div>
                     <!-- end row -->

                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group text-right m-b-0">
                          <button class="btn btn-primary waves-effect waves-light" type="submit" name="submit" > Send</button>
                          <!-- <a href="user-list.php" class="btn btn-default waves-effect waves-light m-l-5"> Cancel</a> -->
                          <a href="privacy-policy" class="btn btn-default waves-effect waves-light"> Cancel</a>
                        </div>
                      </div>
                    </div>
                  </form>
                  <?php } ?>
                </div>
              </div>
            </div>
            <!-- /Page Content -->

          </div> <!-- container -->
                               
        </div> <!-- content -->

        <?php include_once("include/footer.php"); ?>

      </div>
      <!-- ============================================================== -->
      <!-- End Right content here -->
      <!-- ============================================================== -->
      
    </div>
    <!-- END wrapper -->

    <script>
        var resizefunc = [];
    </script>

    <!-- jQuery  -->
    <?php include_once("include/common_js.php"); ?>
      
      <script src="assets/plugins/moment/moment.js"></script>
      
      <script src="assets/js/jquery.core.js"></script>
      <script src="assets/js/jquery.app.js"></script>
      <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
      <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
      <script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>

      <script src="assets/plugins/datatables/dataTables.buttons.min.js"></script>
      <script src="assets/plugins/datatables/buttons.bootstrap.min.js"></script>
      <script src="assets/plugins/datatables/jszip.min.js"></script>
      <script src="assets/plugins/datatables/pdfmake.min.js"></script>
      <script src="assets/plugins/datatables/vfs_fonts.js"></script>
      <script src="assets/plugins/datatables/buttons.html5.min.js"></script>
      <script src="assets/plugins/datatables/buttons.print.min.js"></script>
      <script src="assets/plugins/datatables/dataTables.fixedHeader.min.js"></script>
      <script src="assets/plugins/datatables/dataTables.keyTable.min.js"></script>
      <script src="assets/plugins/datatables/dataTables.responsive.min.js"></script>
      <script src="assets/plugins/datatables/responsive.bootstrap.min.js"></script>
      <script src="assets/plugins/datatables/dataTables.scroller.min.js"></script>
      <script src="assets/plugins/datatables/dataTables.colVis.js"></script>
      <script src="assets/plugins/datatables/dataTables.fixedColumns.min.js"></script>

      <script src="assets/pages/datatables.init.js"></script>

      <script type="text/javascript">
          $(document).ready(function () {
              $('#datatable').dataTable();
              $('#datatable-keytable').DataTable({keys: true});
              $('#datatable-responsive').DataTable();
              $('#datatable-colvid').DataTable({
                  "dom": 'C<"clear">lfrtip',
                  "colVis": {
                      "buttonText": "Change columns"
                  }
              });
              $('#datatable-scroller').DataTable({
                  ajax: "assets/plugins/datatables/json/scroller-demo.json",
                  deferRender: true,
                  scrollY: 380,
                  scrollCollapse: true,
                  scroller: true
              });
              var table = $('#datatable-fixed-header').DataTable({fixedHeader: true});
              var table = $('#datatable-fixed-col').DataTable({
                  scrollY: "300px",
                  scrollX: true,
                  scrollCollapse: true,
                  paging: false,
                  fixedColumns: {
                      leftColumns: 1,
                      rightColumns: 1
                  }
              });
          });
          TableManageButtons.init();

      </script>
    
  </body>
</html>