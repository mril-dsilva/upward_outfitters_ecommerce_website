<?php

// Function to 
function show_navbar($conn) {
    ?>
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
        nav ul li a:hover {
            background-color: #111;
        }
        
    </style>


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
    <?php
        }
        ?>
        