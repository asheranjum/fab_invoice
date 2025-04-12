<?php
require 'session.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fab Invoice</title>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style-1.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
  <script src="assets/js/bootstrap.bundle.min.js"></script>

  <div class="container-fluid" style="background: linear-gradient(125deg, #ed9a1e, #ed9a1e, #fdfdfe, #111ab9, #070fb5);">
    <div class="row">
      <div class="col-md-12 mt-3 text-center">
        <img src="assets/images/logo.png" alt="Logo" style="width: 200px; height: auto;">
      </div>

      <div class="col-md-4">
        <form action="logout.php" method="POST">
          <button type="submit" class="btn btn-danger logout-button">Logout</button>
        </form>
      </div>
    </div>

    <div class="row">
      <div class="col-md-4 mt-5">
        <div class="dropdown">
          <button class="btn btn-lg create-btn dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false"> Create Invoice </button>
          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <li><a class="dropdown-item" href="invoice_bedding-furniture.php" target="_blank">Bedding/Furniture</a></li>
            <li><a class="dropdown-item" href="invoice_electric.php" target="_blank">Electric</a></li>
          </ul>
        </div>
      </div>
    </div>

    <div class="row justify-content-end">
      <div class="col-md-3 d-flex mb-2">
        <form method="GET" action="index.php" class="d-flex w-100">
          <input class="form-control" type="search" name="search" placeholder="Search By Franchise Name" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" aria-label="Search">
          <button class="btn search-icon" type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
        </form>
      </div>
    </div>
  </div>

  <div class="container my-4">
    <?php
    require 'config/database.php';

    $records_per_page = 25;
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $search_query = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
    
    $offset = ($current_page - 1) * $records_per_page;
    
    $sql_count = "SELECT COUNT(*) AS total FROM invoices WHERE company_name LIKE '%$search_query%'";
    $result_count = mysqli_query($conn, $sql_count);
    
    if (!$result_count) {
        die("Query failed: " . mysqli_error($conn));
    }
    
    $row_count = mysqli_fetch_assoc($result_count);
    $total_records = $row_count['total'];
    $total_pages = ceil($total_records / $records_per_page);
    
    $sql = "SELECT * FROM invoices WHERE company_name LIKE '%$search_query%' LIMIT $offset, $records_per_page";
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    ?>

    <?php if (mysqli_num_rows($result) > 0): ?>
      <div class="row table-responsive">
        <div class="col-md-12 table-responsive table-container">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Date</th>
                <th>Franchise Name</th>
                <th>Type</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                  <td style="color:#011f7f;"><?php echo htmlspecialchars($row['date']); ?></td>
                  <td style="color:#011f7f;"><?php echo htmlspecialchars($row['company_name']); ?></td>
                  <td style="color:#011f7f;"><?php echo htmlspecialchars($row['invoice_type']); ?></td>
                  <td>
                    <?php if($row['invoice_type'] == 'Bedding' || $row['invoice_type'] == 'Furniture'){ ?>
                      <button type="button" class="btn action-btn" onclick="window.open('pdf_bedding-furniture.php?invoice_id=<?php echo urlencode($row['id']); ?>', '_blank')">Download PDF</button>
                      <button type="button" class="btn action-btn" onclick="window.open('update_bedding-furniture_invoice.php?invoice_id=<?php echo urlencode($row['id']); ?>', '_blank')">Edit</button>
                      <?php } else{ ?>
                        <button type="button" class="btn action-btn" onclick="window.open('pdf_electric.php?invoice_id=<?php echo urlencode($row['id']); ?>', '_blank')">Download PDF</button>
                    <button type="button" class="btn action-btn" onclick="window.open('update_electric_invoice.php?invoice_id=<?php echo urlencode($row['id']); ?>', '_blank')">Edit</button>
                    <?php } ?>
                    <button type="button" class="btn action-btn" onclick="location.href='duplicate_invoice.php?invoice_id=<?php echo urlencode($row['id']); ?>'">Duplicate</button>
                    <button type="button" class="btn action-btn" onclick="if(confirm('Are you sure you want to delete this invoice?')) location.href='delete_invoice.php?invoice_id=<?php echo urlencode($row['id']); ?>'">Delete</button>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12 ml-3 mt-5">
          <nav>
            <ul class="pagination justify-content-center">
              <li class="page-item <?php echo ($current_page == 1) ? '' : ''; ?>">
                <a class="page-link page-controls" href="index.php?page=<?php echo ($current_page - 1); ?><?php echo $search_query ? '&search=' . urlencode($search_query) : ''; ?>" tabindex="-1">Previous</a>
              </li>
              <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item page-indexing<?php echo ($i == $current_page) ? 'active' : ''; ?>">
                  <a class="page-link page-indexing" href="index.php?page=<?php echo $i; ?><?php echo $search_query ? '&search=' . urlencode($search_query) : ''; ?>"><?php echo $i; ?></a>
                </li>
              <?php endfor; ?>
              <li class="page-item <?php echo ($current_page == $total_pages) ? '' : ''; ?>">
                <a class="page-link page-controls" href="index.php?page=<?php echo ($current_page + 1); ?><?php echo $search_query ? '&search=' . urlencode($search_query) : ''; ?>">Next</a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    <?php else: ?>
      <h3>No Record Found</h3>
    <?php endif; ?>

    <?php
    mysqli_close($conn);
    ?>
  </div>
</body>

</html>