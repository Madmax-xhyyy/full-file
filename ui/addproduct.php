<?php

include_once 'connectdb.php';
session_start();

include_once "header.php";

if (isset($_POST['btnsave'])) {

    $product = $_POST['txtProductcode'];
    $description = $_POST['txtdescription'];
    $servicetype = $_POST['txtstock']; // Service Type dropdown
    $additionalfee = $_POST['txtsaleprice']; // Additional Fee input
    $purchaseprice = $_POST['txtpurchaseprice'];
    $saleprice = $_POST['txtsaleprice2']; // Sale Price input

    
    $stockqty = isset($_POST['txtStockQty']) ? $_POST['txtStockQty'] : null;
    $brand = isset($_POST['txtBrand']) ? $_POST['txtBrand'] : null;
    $expirydate = isset($_POST['txtExpirydate']) && $_POST['txtExpirydate'] !== '' ? $_POST['txtExpirydate'] : null; // expected YYYY-MM-DD or null

    $f_name = $_FILES['myfile']['name'];
    $f_tmp = $_FILES['myfile']['tmp_name'];
    $f_size = $_FILES['myfile']['size'];

    $f_extension = explode('.', $f_name);
    $f_extension = strtolower(end($f_extension));

    $f_newfile = uniqid() . '.' . $f_extension;
    $store = "productimages/" . $f_newfile;

    if ($f_extension == 'jpg' || $f_extension == 'jpeg' || $f_extension == 'png' || $f_extension == 'gif') {

        if ($f_size >= 1000000) {
            $_SESSION['status'] = "max file should be 1MB";
            $_SESSION['status_code'] = "warning";
        } else {
            if (move_uploaded_file($f_tmp, $store)) {

                $productimage = $f_newfile;

                $insert = $pdo->prepare("INSERT INTO tbl_product(product, category, description, purchaseprice, saleprice, image, stock, brand, expirydate)
                    VALUES(:product, :category, :description,:pprice, :saleprice, :img, :stock, :brand, :expirydate)");

                $insert->bindParam(':product', $product);
                $insert->bindParam(':category', $category);
                $insert->bindParam(':description', $description);
                $insert->bindParam(':pprice', $purchaseprice);
                $insert->bindParam(':saleprice', $saleprice);
                $insert->bindParam(':img', $productimage);

                // bind new fields
                $insert->bindParam(':stock', $stockqty);
                $insert->bindParam(':brand', $brand);
                $insert->bindParam(':expirydate', $expirydate);

                if ($insert->execute()) {
                    $_SESSION['status'] = "product inserted successfully";
                    $_SESSION['status_code'] = "success";
                } else {
                    $_SESSION['status'] = "product inserted failed";
                    $_SESSION['status_code'] = "error";
                }
            } else {
                $_SESSION['status'] = "Failed to upload image";
                $_SESSION['status_code'] = "error";
            }
        }
    } else {
        $_SESSION['status'] = "only jpg, jpeg, png and gif can be uploaded";
        $_SESSION['status_code'] = "warning";
    }
}
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Add Product</h1>
            <hr>
            <a href="productlist.php" class="btn btn-info"><span class="report-count"> View product you entered</span></a>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <!-- <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Starter Page</li> -->
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">

          <div class="card card-warning card-outline">
              <div class="card-header">
                <h5 class="m-0">Product</h5>
              </div>

              <form action="" method="post" enctype="multipart/form-data">  
                <div class="row">
                  <div class="col-md-6">

                    <div class="form-group">
                        <label>Product Code:</label>
                        <input type="text" class="form-control" placeholder="Enter Product Code" name="txtProductcode" required>
                    </div>

                    <div class="form-group">
                      <label>Category:</label>
                      <select id="category" class="form-control" name="txtBarcode" required>
                        <option value="" disabled selected>Select Category</option>
                        <option value="5 kg (Medium)">5 kg (Medium)</option>
                        <option value="11 Kg (Standard)">11 Kg (Standard)</option>
                        <option value="22 Kg (Large)">22 Kg (Large)</option>
                        <option value="50 Kg (Extra Large)">50 Kg (Extra Large)</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <label>Description:</label>
                      <input type="text" class="form-control" placeholder="Enter Description" name="txtdescription" required>
                    </div>

                    <div class="form-group">
                      <label>Purchase Price:</label>
                      <input type="text" id="txtpurchaseprice" class="form-control" placeholder="Enter Purchase Price" name="txtpurchaseprice" required>
                    </div>

                    <div class="form-group">
                      <label>Sale Price:</label>
                      <input type="text" id="txtsaleprice2" class="form-control" placeholder="Enter Sale Price" name="txtsaleprice2" required>
                    </div>
                    
             
                    <div class="form-group">
                      <label>Stock Quantity:</label>
                      <input type="number" id="txtStockQty" min="0" step="1" class="form-control" placeholder="Enter Stock Quantity" name="txtStockQty">
                    </div>

                    <div class="form-group">
                      <label>Brand:</label>
                      <input type="text" class="form-control" placeholder="Enter Brand" name="txtBrand">
                    </div>

                    <div class="form-group">
                      <label>Expiry Date:</label>
                      <input type="date" class="form-control" name="txtExpirydate">
                    </div>

                    <div class="form-group">
                        <label>Product Image:</label>
                        <input type="file" class="input-group" name="myfile" required>
                        <p>Upload image</p>
                    </div>

                  </div>
                </div>

                <div class="card-footer">
                  <div class="text-center">
                    <button type="submit" class="btn btn-primary" name="btnsave">Save Product</button>
                  </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                  var serviceType = document.getElementById('servicetype');
                  var additionalFee = document.getElementById('additionalfee');
                  if(serviceType && additionalFee) {
                    serviceType.addEventListener('change', function() {
                      if(this.value === 'Delivery') {
                        additionalFee.value = '50.00';
                      } else if(this.value === 'Pick-up') {
                        additionalFee.value = '0.00';
                      } else {
                        additionalFee.value = '';
                      }
                    });
                  }
                });
                </script>
                <script>
   
                document.addEventListener('DOMContentLoaded', function() {
                  var mapping = {
                    '5 kg (Medium)': { purchase: '400.00', sale: '450.00', brand: 'Petron', stock: 50 },
                    '11 Kg (Standard)': { purchase: '900.00', sale: '1000.00', brand: 'Petron', stock: 60 },
                    '22 Kg (Large)': { purchase: '1700.00', sale: '1850.00', brand: 'Petron', stock: 40 },
                    '50 Kg (Extra Large)': { purchase: '3800.00', sale: '4200.00', brand: 'Petron', stock: 30 }
                  };

                  var cat = document.getElementById('category');
                  var purchase = document.getElementById('txtpurchaseprice');
                  var sale = document.getElementById('txtsaleprice2');
                  var brand = document.getElementById('txtBrand');
                  var stock = document.getElementById('txtStockQty');

                  if(!cat) return;

                  function applyDefaults(val) {
                    var cfg = mapping[val];
                    if(cfg) {
                      if(purchase) purchase.value = cfg.purchase;
                      if(sale) sale.value = cfg.sale;
                      if(brand) brand.value = cfg.brand;
                      if(stock && typeof cfg.stock !== 'undefined') stock.value = cfg.stock;
                    } else {
                      if(purchase) purchase.value = '';
                      if(sale) sale.value = '';
                      if(brand) brand.value = '';
                      if(stock) stock.value = '';
                    }
                  }

                  cat.addEventListener('change', function(){
                    applyDefaults(this.value);
                  });

                  // If select2 is used, also listen for its select event
                  if (typeof jQuery !== 'undefined' && jQuery.fn && jQuery.fn.select2) {
                    jQuery(cat).on('select2:select', function(e){
                      var val = (e.params && e.params.data && (e.params.data.id || e.params.data.text)) || this.value;
                      applyDefaults(val);
                    });
                  }
                });
                </script>
              </form>
            </div>
          </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php
  include_once("footer.php");
  ?>

<?php 
if(isset($_SESSION['status']) && $_SESSION['status']!=='')
{
  ?>
<script>
  Swal.fire({
    icon: '<?php echo $_SESSION['status_code'];?>',
    title: '<?php echo $_SESSION['status'];?>'
  })
</script>
<?php
unset($_SESSION['status']);
}
?>