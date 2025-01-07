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
        <button type="button" class="btn btn-dark btn-lg create-btn" onclick="window.open('invoice.php', '_blank');">Create New Invoice</button>
      </div>
    </div>

    <div class="row justify-content-end">
      <div class="col-md-3 d-flex mb-2">
        <form method="GET" action="index.php" class="d-flex w-100">
          <input class="form-control" type="search" name="search" placeholder="Search By RunSheet Number" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" aria-label="Search">
          <button class="btn btn-dark" type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
        </form>
      </div>
    </div>
  </div>

  <div class="container my-4">
    <?php
    require 'config/database.php';

    $records_per_page = 25;
    $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
    $search_query = isset($_GET['search']) ? $_GET['search'] : '';

    $offset = ($current_page - 1) * $records_per_page;

    $sql_count = "SELECT COUNT(*) AS total FROM invoice WHERE runsheet LIKE '%$search_query%'";
    $result_count = mysqli_query($conn, $sql_count);
    $row_count = mysqli_fetch_assoc($result_count);
    $total_records = $row_count['total'];
    $total_pages = ceil($total_records / $records_per_page);

    $sql = "SELECT * FROM invoice WHERE runsheet LIKE '%$search_query%' LIMIT $offset, $records_per_page";
    $result = mysqli_query($conn, $sql);
    ?>

    <?php if (mysqli_num_rows($result) > 0): ?>
      <div class="row table-responsive">
        <div class="col-md-12 table-responsive table-container">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Date</th>
                <th>Run Sheet No</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                  <td><?php echo htmlspecialchars($row['date']); ?></td>
                  <td><?php echo htmlspecialchars($row['runsheet']); ?></td>
                  <td>
                    <a href="pdf.php?invoice_id=<?php echo urlencode($row['invoice_id']); ?>" style="font-size: 20px;" title="Download"> Download PDF</a>
                    <a href="update_invoice.php?invoice_id=<?php echo urlencode($row['invoice_id']); ?>" class="fa fa-edit" style="font-size: 20px;" title="Edit"></a>
                    <a href="delete_invoice.php?invoice_id=<?php echo urlencode($row['invoice_id']); ?>" class="fa fa-trash text-danger" style="font-size: 20px;" title="Delete" onclick="return confirm('Are you sure you want to delete this invoice?');"></a>
                   
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
              <li class="page-item <?php echo ($current_page == 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="index.php?page=<?php echo ($current_page - 1); ?><?php echo $search_query ? '&search=' . urlencode($search_query) : ''; ?>" tabindex="-1">Previous</a>
              </li>
              <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                  <a class="page-link" href="index.php?page=<?php echo $i; ?><?php echo $search_query ? '&search=' . urlencode($search_query) : ''; ?>"><?php echo $i; ?></a>
                </li>
              <?php endfor; ?>
              <li class="page-item <?php echo ($current_page == $total_pages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="index.php?page=<?php echo ($current_page + 1); ?><?php echo $search_query ? '&search=' . urlencode($search_query) : ''; ?>">Next</a>
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
