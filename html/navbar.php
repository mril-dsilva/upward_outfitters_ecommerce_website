<?php
// Function to create a navbar
function show_navbar($conn) {
    ?>
    <style>
        /* Styling for the navigation bar */
        nav {
            background-color: #333; /* Dark gray background */
            color: white;
            overflow: hidden;
        }
        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        nav ul li {
            flex: 1; /* Make each list item fill available space */
            text-align: center;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 18px 24px; /* Uniform padding for links */
        }
        nav ul li a:hover {
            background-color: #555; /* Lighter gray on hover */
        }
        nav ul li img {
            vertical-align: middle;
        }
    </style>

    <!-- Navigation Bar -->
    <nav>
        <ul>
            <li><a href="homepage.php">Home</a></li>    
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

                // Get categories from the database
                $categories = getProdCategories($conn);
                foreach ($categories as $category) {
                    echo '<li><a href="catalog.php?category=' . htmlspecialchars($category['id']) . '">' . htmlspecialchars($category['name']) . '</a></li>';
                }
            ?>
            <li><a href="logout.php"><img src="log_out_icon.png" alt="Log Out Icon" style="width:20px; height:20px; filter: invert(1);"></a></li>
        </ul>
    </nav>
    <?php
}

// Function to retrieve categories from the database
function getProdCategories($conn) {
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
?>
