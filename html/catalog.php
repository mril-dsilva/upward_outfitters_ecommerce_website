<?php
    require 'check_authentication.php';
    check_auth();
?>

 <!-- Include Navigation Bar function php-->
<?php 
require 'navbar.php'; 
?>

<?php

// Show all errors from the PHP interpreter.
ini_set('display_errors', 1);    
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Show all errors from the MySQLi Extension.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);  

$config = parse_ini_file('../../mysql.ini');
$dbname = 'upward_outfitters';
$conn = new mysqli(
    $config['mysqli.default_host'],
    $config['mysqli.default_user'],
    $config['mysqli.default_pw'],
    $dbname
);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//Retrieve the Product Data
$products = getProducts($conn); 

//  Display Products
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Product Catalog</title> 
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP and Bootstrap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php  show_navbar($conn);   
?>

    <h1>Product Catalog</h1>

    <!--  Product Category Filter -->
    <form method="GET" action="">
        <label for="category">Filter by Category:</label>
        <select name="category" id="category">
            <option value="">All Categories</option>
            <?php
            // Get categories from the database
            foreach ($categories as $category) {
                echo '<option value="' . htmlspecialchars($category['id']) . '">' . htmlspecialchars($category['name']) . '</option>';
            }
            ?>
        </select>
        <button type="submit">Filter</button>
    </form>

    <!-- Display Products in a Table with Add to Cart Option -->
    <form method="POST" action="cart.php">
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                <th>Product Name</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Warranty Length (Months)</th>
                    <th>Length</th>
                    <th>Sizes</th>
                    <th>Brand</th>
                    <th>Quantity</th>
                    <th>Select</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($products)) {
                    foreach ($products as $product) {
                        // Filter by selected category if necessary
                        if (!empty($_GET['category']) && $_GET['category'] != $product['category_id']) {
                            continue;
                        }

                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($product['product_name']) . '</td>';
                        echo '<td>$' . htmlspecialchars($product['product_sale_price']) . '</td>';
                        echo '<td>' . htmlspecialchars($product['product_description']) . '</td>';
                        echo '<td>' . htmlspecialchars($product['product_warranty_length']) . '</td>';
                        if($product['product_lengths'] !== null){
                            echo '<td>' . htmlspecialchars($product['product_lengths']) . '</td>';
                        }
                        else{
                            echo '<td>' . " " . '</td>';
                        }
                        if($product['product_sizes'] !== null){
                            echo '<td>' . htmlspecialchars($product['product_sizes']) . '</td>';
                        }
                        else{
                            echo '<td>' . " " . '</td>';
                        }
                        if($product['product_brand_name'] !== null){
                            echo '<td>' . htmlspecialchars($product['product_brand_name']) . '</td>';
                        }
                        else{
                            echo '<td>' . " " . '</td>';
                        }
                        echo '<td><input type="number" name="quantity[' . htmlspecialchars($product['id']) . ']" min="1" value="1"></td>';
                        echo '<td><input type="checkbox" name="product_ids[]" value="' . htmlspecialchars($product['id']) . '"></td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="6">No products available.</td></tr>';
                }
                ?>
            </tbody>
        </table>
        <button type="submit">Add to Cart</button>
    </form>
</body>
</html>

<?php

function getProducts($conn) {
    $sql = "
    SELECT 
        p.product_id,
        p.product_name,
        p.product_sale_price,
        p.product_description,
        p.product_warranty_length,
        pb.product_brand_name,
        pc.product_category_name,
        p.product_discontinued,
        p.product_discount_pct,
        GROUP_CONCAT(DISTINCT pl.product_length ORDER BY pl.product_length ASC) AS product_lengths,
        GROUP_CONCAT(DISTINCT ps.product_size ORDER BY ps.product_size ASC) AS product_sizes,
        GROUP_CONCAT(DISTINCT pss.product_shoe_size ORDER BY pss.product_shoe_size ASC) AS product_shoe_sizes,
        GROUP_CONCAT(DISTINCT pcap.product_capacity ORDER BY pcap.product_capacity ASC) AS product_capacities
    FROM 
        products p
    LEFT JOIN 
        product_brands pb ON p.product_brand_id = pb.product_brand_id
    LEFT JOIN 
        product_categories pc ON p.product_category_id = pc.product_category_id
    LEFT JOIN 
        products_length pl ON p.product_id = pl.product_id
    LEFT JOIN 
        products_size ps ON p.product_id = ps.product_id
    LEFT JOIN 
        products_shoe_size pss ON p.product_id = pss.product_id
    LEFT JOIN 
        products_capacity pcap ON p.product_id = pcap.product_id
    GROUP BY 
        p.product_id, p.product_name, p.product_sale_price, p.product_description, 
        p.product_warranty_length, pb.product_brand_name, pc.product_category_name,
        p.product_discontinued, p.product_discount_pct";
    
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}

// Function to retrieve categories from the database
function getCategories($conn) {
    $sql = "SELECT * FROM product_categories";
    $result = $conn->query($sql);
 
    $categories = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = [
                'id' => $row['product_category_id'],
                'name' => $row['product_category_name'],
            ];
        }
    }
    return $categories;
}

// Close the database connection
$conn->close();
?>