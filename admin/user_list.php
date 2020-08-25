<?php
session_start();
require '../config/config.php';
require '../config/common.php';

if (empty($_SESSION['user_id']) && empty($_SESSION['logged_in'])) {
  header('Location: login.php');
}

// အရင်ဆုံး search form ကနေပို့လိုက်တဲ့ search key လေးကို cookie ထဲမှာ save လုပ်ပြီးသိမ်းထားလိုက်ပါတယ်။
if ($_POST['search']) {
  setcookie('search',$_POST['search'], time() + (86400 * 30), "/");
}

?>


<?php include('header.php'); ?>
    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">user Listings</h3>
              </div>
              <?php
                if (!empty($_GET['pageno'])) {
                  $pageno = $_GET['pageno'];
                }else{
                  $pageno = 1;
                }

                $numOfrecs = 5;
                $offset = ($pageno - 1) * $numOfrecs;

                //ပြီးရင် အောက်မှာ search key ရှိမရှိ စစ်ရတဲ့ နေရာမှာ cookie ထဲက search key ရှိမရှိကိုပါ ထပ်စစ်ပါမယ်။ && !isset($_COOKIE['search']) မရှိရင်တော့ record အကုန်ပြပြီး ရှိရင်တော့ အောက်က else condition ထဲရောက်ပြီး search နဲ့ pagination တွဲပြီးတော့ အလုပ်လုပ်နိုင်သွားပါလိမ့်မယ်။
                if (empty($_POST['search']) && !isset($_COOKIE['search'])) {
                  $stmt = $pdo->prepare("SELECT * FROM users ORDER BY id DESC");
                  $stmt->execute();
                  $rawResult = $stmt->fetchAll();

                  $total_pages = ceil(count($rawResult) / $numOfrecs);

                  $stmt = $pdo->prepare("SELECT * FROM users ORDER BY id DESC LIMIT $offset,$numOfrecs");
                  $stmt->execute();
                  $result = $stmt->fetchAll();
                }else{
                  //အောက်က search key ကိုတော့ cookie ထဲက value assign လုပ်ပေးလိုက်ပါမယ်
                  $searchKey = $_COOKIE['search'];
                  $stmt = $pdo->prepare("SELECT * FROM users WHERE name LIKE '%$searchKey%' ORDER BY id DESC");
                  $stmt->execute();
                  $rawResult = $stmt->fetchAll();

                  $total_pages = ceil(count($rawResult) / $numOfrecs);

                  $stmt = $pdo->prepare("SELECT * FROM users WHERE name LIKE '%$searchKey%' ORDER BY id DESC LIMIT $offset,$numOfrecs");
                  $stmt->execute();
                  $result = $stmt->fetchAll();
                }

              ?>
              <!-- /.card-header -->
              <div class="card-body">
                <div>
                  <a href="user_add.php" type="button" class="btn btn-success">Create User</a>
                </div>
                <br>
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th style="width: 10px">#</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Role</th>
                      <th style="width: 40px">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if ($result) {
                      $i = 1;
                      foreach ($result as $value) { ?>
                        <tr>
                          <td><?php echo $i;?></td>
                          <td><?php echo escape($value['name'])?></td>
                          <td><?php echo escape($value['email'])?></td>
                          <td><?php echo $value['role'] == 1 ? 'admin': 'user';?></td>
                          <td>
                            <div class="btn-group">
                              <div class="container">
                                <a href="user_edit.php?id=<?php echo $value['id']?>" type="button" class="btn btn-warning">Edit</a>
                              </div>
                              <div class="container">
                                <a href="user_delete.php?id=<?php echo $value['id']?>"
                                  onclick="return confirm('Are you sure you want to delete this item')"
                                  type="button" class="btn btn-danger">Delete</a>
                              </div>
                            </div>
                          </td>
                        </tr>
                    <?php
                      $i++;
                      }
                    }
                    ?>
                  </tbody>
                </table><br>
                <nav aria-label="Page navigation example" style="float:right">
                  <ul class="pagination">
                    <li class="page-item"><a class="page-link" href="?pageno=1">First</a></li>
                    <li class="page-item <?php if($pageno <= 1){ echo 'disabled';} ?>">
                      <a class="page-link" href="<?php if($pageno <= 1) {echo '#';}else{ echo "?pageno=".($pageno-1);}?>">Previous</a>
                    </li>
                    <li class="page-item"><a class="page-link" href="#"><?php echo $pageno; ?></a></li>
                    <li class="page-item <?php if($pageno >= $total_pages){ echo 'disabled';} ?>">
                      <a class="page-link" href="<?php if($pageno >= $total_pages) {echo '#';}else{ echo "?pageno=".($pageno+1);}?>">Next</a>
                    </li>
                    <li class="page-item"><a class="page-link" href="?pageno=<?php echo $total_pages?>">Last</a></li>
                  </ul>
                </nav>
              </div>
              <!-- /.card-body -->

            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  <?php include('footer.html')?>
