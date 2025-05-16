<?php
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=pos_db", "root", "");
        } catch (PDOException $f) {
            echo $f->getMessage();
        }

        ?>


        <?php
        // Daily

        function calculateTotalDailySalesEarnings($startDate, $endDate) {
            global $pdo;

            $totalSalesQuery = $pdo->prepare("SELECT SUM(total) as total_earnings FROM tbl_invoice WHERE DATE(order_date) BETWEEN :startDate AND :endDate");
            $totalSalesQuery->bindParam(":startDate", $startDate);
            $totalSalesQuery->bindParam(":endDate", $endDate);

            if ($totalSalesQuery->execute()) {
                $totalSalesResult = $totalSalesQuery->fetch(PDO::FETCH_ASSOC);

                return ($totalSalesResult !== false && $totalSalesResult['total_earnings'] !== null) ? $totalSalesResult['total_earnings'] : 0;
            } else {
                // Handle the error
                $errorInfo = $totalSalesQuery->errorInfo();
                echo "Error: " . $errorInfo[2];
                return 0; // Return 0 or handle the error as needed
            }
        }

        function calculateTotalDailyQuantity($startDate, $endDate) {
            global $pdo;

            $totalQuantityQuery = $pdo->prepare("SELECT SUM(qty) as total_qty FROM tbl_invoice_details WHERE invoice_id IN (SELECT invoice_id FROM tbl_invoice WHERE DATE(order_date) BETWEEN :startDate AND :endDate)");
            $totalQuantityQuery->bindParam(":startDate", $startDate);
            $totalQuantityQuery->bindParam(":endDate", $endDate);

            if ($totalQuantityQuery->execute()) {
                $totalQuantityResult = $totalQuantityQuery->fetch(PDO::FETCH_ASSOC);

                return ($totalQuantityResult !== false && $totalQuantityResult['total_qty'] !== null) ? $totalQuantityResult['total_qty'] : 0;
            } else {
                // Handle the error
                $errorInfo = $totalQuantityQuery->errorInfo();
                echo "Error: " . $errorInfo[2];
                return 0; // Return 0 or handle the error as needed
            }
        }

        // Monthly

        function calculateTotalMonthlySalesEarnings($selectedMonth, $selectedYear) {
            global $pdo;

            $totalSalesQuery = $pdo->prepare("SELECT SUM(total) as total_earnings FROM tbl_invoice WHERE MONTH(order_date) = :selectedMonth AND YEAR(order_date) = :selectedYear");
            $totalSalesQuery->bindParam(":selectedMonth", $selectedMonth);
            $totalSalesQuery->bindParam(":selectedYear", $selectedYear);

            if ($totalSalesQuery->execute()) {
                $totalSalesResult = $totalSalesQuery->fetch(PDO::FETCH_ASSOC);

                return ($totalSalesResult !== false && $totalSalesResult['total_earnings'] !== null) ? $totalSalesResult['total_earnings'] : 0;
            } else {
                // Handle the error
                $errorInfo = $totalSalesQuery->errorInfo();
                echo "Error: " . $errorInfo[2];
                return 0; // Return 0 or handle the error as needed
            }
        }

        function calculateTotalMonthlyQuantity($selectedMonth, $selectedYear) {
            global $pdo;

            $totalQuantityQuery = $pdo->prepare("SELECT SUM(qty) as total_qty FROM tbl_invoice_details WHERE invoice_id IN (SELECT invoice_id FROM tbl_invoice WHERE MONTH(order_date) = :selectedMonth AND YEAR(order_date) = :selectedYear)");
            $totalQuantityQuery->bindParam(":selectedMonth", $selectedMonth);
            $totalQuantityQuery->bindParam(":selectedYear", $selectedYear);

            if ($totalQuantityQuery->execute()) {
                $totalQuantityResult = $totalQuantityQuery->fetch(PDO::FETCH_ASSOC);

                return ($totalQuantityResult !== false && $totalQuantityResult['total_qty'] !== null) ? $totalQuantityResult['total_qty'] : 0;
            } else {
                // Handle the error
                $errorInfo = $totalQuantityQuery->errorInfo();
                echo "Error: " . $errorInfo[2];
                return 0; // Return 0 or handle the error as needed
            }
        }

        // Range Monthly

        function calculateTotalRangeMonthlySalesEarnings($fromMonth, $fromYear, $toMonth, $toYear) {
            global $pdo;

            $totalRangeMonthlySalesQuery = $pdo->prepare("SELECT SUM(total) as total_earnings FROM tbl_invoice WHERE (MONTH(order_date) >= :fromMonth AND YEAR(order_date) >= :fromYear) AND (MONTH(order_date) <= :toMonth AND YEAR(order_date) <= :toYear)");
            $totalRangeMonthlySalesQuery->bindParam(":fromMonth", $fromMonth);
            $totalRangeMonthlySalesQuery->bindParam(":fromYear", $fromYear);
            $totalRangeMonthlySalesQuery->bindParam(":toMonth", $toMonth);
            $totalRangeMonthlySalesQuery->bindParam(":toYear", $toYear);
            $totalRangeMonthlySalesQuery->execute();
            $totalRangeMonthlySalesResult = $totalRangeMonthlySalesQuery->fetch(PDO::FETCH_ASSOC);

            return ($totalRangeMonthlySalesResult !== false && $totalRangeMonthlySalesResult['total_earnings'] !== null) ? $totalRangeMonthlySalesResult['total_earnings'] : 0;
        }

        function calculateTotalRangeMonthlyQuantity($fromMonth, $fromYear, $toMonth, $toYear) {
            global $pdo;

            $totalRangeQuantityQuery = $pdo->prepare("SELECT SUM(qty) as total_qty FROM tbl_invoice_details WHERE invoice_id IN (SELECT invoice_id FROM tbl_invoice WHERE (MONTH(order_date) >= :fromMonth AND YEAR(order_date) >= :fromYear) AND (MONTH(order_date) <= :toMonth AND YEAR(order_date) <= :toYear))");
            $totalRangeQuantityQuery->bindParam(":fromMonth", $fromMonth);
            $totalRangeQuantityQuery->bindParam(":fromYear", $fromYear);
            $totalRangeQuantityQuery->bindParam(":toMonth", $toMonth);
            $totalRangeQuantityQuery->bindParam(":toYear", $toYear);
            $totalRangeQuantityQuery->execute();
            $totalRangeQuantityResult = $totalRangeQuantityQuery->fetch(PDO::FETCH_ASSOC);

            return ($totalRangeQuantityResult !== false && $totalRangeQuantityResult['total_qty'] !== null) ? $totalRangeQuantityResult['total_qty'] : 0;
        }
        function calculateYearlySalesEarnings($year) {
            global $pdo;
        
            $totalSalesQuery = $pdo->prepare("SELECT SUM(total) as total_earnings FROM tbl_invoice WHERE YEAR(order_date) = :year");
            $totalSalesQuery->bindParam(":year", $year);
            $totalSalesQuery->execute();
        
            $totalSalesResult = $totalSalesQuery->fetch(PDO::FETCH_ASSOC);
        
            return ($totalSalesResult !== false && $totalSalesResult['total_earnings'] !== null) ? $totalSalesResult['total_earnings'] : 0;
        }
        
        function calculateYearlyQuantity($year) {
            global $pdo;
        
            $totalQuantityQuery = $pdo->prepare("SELECT SUM(qty) as total_qty FROM tbl_invoice_details WHERE invoice_id IN (SELECT invoice_id FROM tbl_invoice WHERE YEAR(order_date) = :year)");
            $totalQuantityQuery->bindParam(":year", $year);
            $totalQuantityQuery->execute();
        
            $totalQuantityResult = $totalQuantityQuery->fetch(PDO::FETCH_ASSOC);
        
            return ($totalQuantityResult !== false && $totalQuantityResult['total_qty'] !== null) ? $totalQuantityResult['total_qty'] : 0;
        }
        
        function calculateRangeYearlySalesEarnings($startDate, $endDate) {
            global $pdo;
        
            $totalRangeSalesQuery = $pdo->prepare("SELECT SUM(total) as total_earnings FROM tbl_invoice WHERE DATE(order_date) BETWEEN :startDate AND :endDate");
            $totalRangeSalesQuery->bindParam(":startDate", $startDate);
            $totalRangeSalesQuery->bindParam(":endDate", $endDate);
            $totalRangeSalesQuery->execute();
        
            $totalRangeSalesResult = $totalRangeSalesQuery->fetch(PDO::FETCH_ASSOC);
        
            return ($totalRangeSalesResult !== false && $totalRangeSalesResult['total_earnings'] !== null) ? $totalRangeSalesResult['total_earnings'] : 0;
        }
        
        function calculateRangeYearlyQuantity($startDate, $endDate) {
            global $pdo;
        
            $totalRangeQuantityQuery = $pdo->prepare("SELECT SUM(qty) as total_qty FROM tbl_invoice_details WHERE invoice_id IN (SELECT invoice_id FROM tbl_invoice WHERE DATE(order_date) BETWEEN :startDate AND :endDate)");
            $totalRangeQuantityQuery->bindParam(":startDate", $startDate);
            $totalRangeQuantityQuery->bindParam(":endDate", $endDate);
            $totalRangeQuantityQuery->execute();
        
            $totalRangeQuantityResult = $totalRangeQuantityQuery->fetch(PDO::FETCH_ASSOC);
        
            return ($totalRangeQuantityResult !== false && $totalRangeQuantityResult['total_qty'] !== null) ? $totalRangeQuantityResult['total_qty'] : 0;
        }
        
?>



