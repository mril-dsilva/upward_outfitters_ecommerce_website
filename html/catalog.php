<?php
    require 'check_authentication.php';
    check_auth();
?>

 <!-- Include Navigation Bar function php-->
<?php 
require 'navbar.php'; 
?>

<?php

$sql_location = './sql/';

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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        h1 {
            text-align: center;
            color: #333;
            margin: 20px 0;
        }

        form {
            text-align: center;
            margin-bottom: 20px;
        }

        form label {
            font-size: 1em;
            color: #555;
        }

        form select {
            padding: 5px;
            font-size: 1em;
        }

        form input[type="submit"] {
            padding: 8px 15px;
            font-size: 1em;
            background-color: #757575;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form input[type="submit"]:hover {
            background-color: #FF9800;
        }

        table {
            width: 90%;
            margin: 0 auto;
            border-collapse: collapse;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        thead {
            background-color: #FF9800;
            color: white;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tbody tr:hover {
            background-color: #f5f5f5;
        }

        button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #757575;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #FF9800;
        }
    </style>
</head>
<body>

<?php  show_navbar($conn);   
?>

    <h1>Product Catalog</h1>

    <!--  Product Category Filter -->
    <form action="catalog.php" method="GET">
            <label for="category">Filter by Category</label>
            <select name="category">
                <option value="">All Categories</option>
                <?php create_category_options($conn) ?>
            </select>
            <input type="submit" value="Filter" />
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
                        if (!empty($_GET['category']) && $_GET['category'] != $product['product_category_id']) {
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
                        echo '<td><input type="number" name="quantity[' . htmlspecialchars($product['product_id']) . ']" min="1" value="1"></td>';
                        echo '<td><input type="checkbox" name="product_ids[]" value="' . htmlspecialchars($product['product_id']) . '"></td>';
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
	p.product_category_id,
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

function create_category_options($conn){
    global $sql_location;

    $sel_tbl = file_get_contents($sql_location . 'category_retrieve.sql');
    $result = $conn -> query($sel_tbl);

    $queries = $result -> fetch_all();
    $n_rows = $result -> num_rows;
    $n_cols = $result -> field_count;
    $fields = $result -> fetch_fields();
    $result -> close();
    $conn -> next_result();

    for ($i = 0; $i < $n_rows; $i++){
        $id = $queries[$i][0]; ?>
        <option value=<?php echo $id;?>><?php echo $queries[$i][1];?></option>
    <?php }
}

// Close the database connection
$conn->close();
?>
