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

//Retrieve the Product Data
$products = getProducts($conn); 

//  Display Products
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Product Catalog</title> 
    <style>
        /* styling for the navigation bar */
        nav {
            background-color: #333;
            overflow: hidden;
        }
        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        nav ul li {
            padding: 14px 20px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            display: block;
        }
        
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <ul>
        <li><a href="home.php">Home</a></li>    
        <?php
            if (isset($_SESSION['user_role'])) {
                if ($_SESSION['user_role'] == 1) { // Employee
                    echo '<li><a href="inventory.php">Inventory</a></li>';
                    echo '<li><a href="partners.php">Partners</a></li>';
                    echo '<li><a href="transactions.php">Transactions</a></li>';

                }
                if ($_SESSION['user_role'] == 0) { // Customer
                    echo '<li><a href="cart.php">Cart <img src="cart_icon.png" alt="Cart Icon" style="width:20px; height:20px; filter: invert(1);"></a></li>';
                }
            }
            ?>
            <?php
            // Get categories from the database
            $categories = getCategories($conn);
            foreach ($categories as $category) {
                echo '<li><a href="catalog.php?category=' . htmlspecialchars($category['id']) . '">' . htmlspecialchars($category['name']) . '</a></li>';
            }
            ?>
            <li><a href="logout.php"><img src="log_out_icon.png" alt="Log Out Icon" style="width:20px; height:20px;filter: invert(1);"></a></li>
        </ul>
    </nav>

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
                        echo '<td>' . htmlspecialchars($product['name']) . '</td>';
                        echo '<td>$' . htmlspecialchars($product['sale_price']) . '</td>';
                        echo '<td>' . htmlspecialchars($product['description']) . '</td>';
                        echo '<td>' . htmlspecialchars($product['warranty_length']) . '</td>';
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
