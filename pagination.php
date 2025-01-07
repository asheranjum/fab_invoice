<?php
// Pagination logic
function renderPagination($current_page, $total_pages) {
    echo '<ul class="pagination justify-content-center">';

    // Previous Page Button
    echo '<li class="page-item ' . ($current_page <= 1 ? 'disabled' : '') . '">
            <a class="page-link" href="?page=' . ($current_page - 1) . '" tabindex="-1">Previous</a>
          </li>';

    // Page Numbers
    for ($page = 1; $page <= $total_pages; $page++) {
        echo '<li class="page-item ' . ($page == $current_page ? 'active' : '') . '">
                <a class="page-link" href="?page=' . $page . '">' . $page . '</a>
              </li>';
    }

    // Next Page Button
    echo '<li class="page-item ' . ($current_page >= $total_pages ? 'disabled' : '') . '">
            <a class="page-link" href="?page=' . ($current_page + 1) . '">Next</a>
          </li>';

    echo '</ul>';
}
?>
