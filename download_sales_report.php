<?php
require_once('includes/load.php'); // Include necessary files

if(isset($_POST['submit'])){
    $req_dates = array('start-date','end-date');
    validate_fields($req_dates);

    if(empty($errors)){
        $start_date   = remove_junk($db->escape($_POST['start-date']));
        $end_date     = remove_junk($db->escape($_POST['end-date']));
        $results      = find_sale_by_dates($start_date,$end_date);

        // Generate CSV data
        $csv_data = generate_sales_csv($results);

        // Set appropriate headers
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="sales_report.csv"');

        // Output CSV data
        echo $csv_data;
        exit();
    } else {
        $session->msg("d", $errors);
        redirect('sales_report.php', false);
    }
} else {
    $session->msg("d", "Select dates");
    redirect('sales_report.php', false);
}

// Function to generate CSV data from sales results
function generate_sales_csv($results) {
    ob_start(); // Start output buffering
    $output = fopen('php://output', 'w'); // Open output stream

    // Add CSV header
    fputcsv($output, array('Date', 'Product Title', 'Buying Price', 'Selling Price', 'Total Qty', 'TOTAL'));

    // Add data rows
    foreach($results as $result) {
        fputcsv($output, array(
            $result['date'],
            $result['name'],
            $result['buy_price'],
            $result['sale_price'],
            $result['total_sales'],
            $result['total_saleing_price']
        ));
    }

    fclose($output); // Close output stream
    return ob_get_clean(); // Get buffered output and clean buffer
}
