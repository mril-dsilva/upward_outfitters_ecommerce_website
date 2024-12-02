<?php

$config = parse_ini_file('/../../mysql.ini');
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

//  Retrieve the Product Data
$products = getProducts($conn); 
// Display Products
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Catalog</title>
    <link rel="stylesheet" href="styles.css"> <!-- Optional: Add a CSS file to style the page -->
</head>
<body>
    <h1>Product Catalog</h1>

    <!--  Product Category Filter -->
    <form method="GET" action="">
        <label for="category">Filter by Category:</label>
        <select name="category" id="category">
            <option value="">All Categories</option>
            <?php
            // Get categories from the database
            $categories = getCategories($conn); // This function will now use the database connection.
            foreach ($categories as $category) {
                echo '<option value="' . htmlspecialchars($category['id']) . '">' . htmlspecialchars($category['name']) . '</option>';
            }
            ?>
        </select>
        <button type="submit">Filter</button>
    </form>

    <!--  Display Products in a Table with Add to Cart Option -->
    <form method="POST" action="cart.php">
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Warranty Length (Months)</th>
                    <th>Select Quantity</th>
                    <th>Confirm</th>
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
                        echo '<td>' . htmlspecialchars($product['name']) . '</td>';
                        echo '<td>$' . htmlspecialchars($product['sale_price']) . '</td>';
                        echo '<td>' . htmlspecialchars($product['description']) . '</td>';
                        echo '<td>' . htmlspecialchars($product['warranty_length']) . '</td>';
                        echo '<td><input type="number" name="quantity[' . htmlspecialchars($product['id']) . ']" min="0" value="0"></td>';
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

// Function to retrieve products from the database
function getProducts($conn) {
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);

    $products = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = [
                'id' => $row['product_id'],
                'name' => $row['product_name'],
                'sale_price' => $row['product_sale_price'],
                'description' => $row['product_description'],
                'warranty_length' => $row['product_warranty_length'],
                'category_id' => $row['product_category_id'],
            ];
        }
    }
    return $products;
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
