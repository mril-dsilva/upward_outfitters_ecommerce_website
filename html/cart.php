<?php 
  require 'navbar.php'; 
?>
<?php
    require 'check_authentication.php';
    check_auth();
?>

<?php

  // Set config for user
  $config = parse_ini_file('../../mysql.ini');
  $dbname = "upward_outfitters";
  $queries_path = "./sql/";

  if (array_key_exists("quantity", $_POST) and array_key_exists("product_ids", $_POST)) {
    // Get names of products adn set cart data in session
    $conn = create_connection($config, $dbname);
    $statement = $conn->prepare("SELECT product_name, product_sale_price, product_discount_pct FROM products WHERE product_id = ?");
    $statement->bind_param("i", $escaped_product_id);
    $cart_data = [];
    $total_price = 0;

    $cart_empty = true;
    foreach ($_POST["product_ids"] as $product_id) {
      $escaped_product_id = htmlspecialchars($product_id);
      $quantity = $_POST["quantity"][$product_id];
      if ($quantity > 0) {
        $cart_empty = false;
        $statement->execute();
        $statement->store_result();
        $statement->bind_result($product_name, $product_price, $product_discount_pct);
        while ($statement->fetch()) {
          $cart_data[] = [
            'id' => $product_id,
            'name' => $product_name,
            'quantity' => $quantity,
            'price' => $product_price * (1 - $product_discount_pct)
          ];
          $total_price += $quantity * $product_price * (1 - $product_discount_pct);
        }
      }
    }
    if ($cart_empty) {
      redirect_to_catalog();
    }
    $statement->close();
    $conn->close();
    $_SESSION["cart_data"] = $cart_data;
  } else {
    if (!array_key_exists("cart_data", $_SESSION)) {
      redirect_to_catalog();
    }
    if (!array_key_exists("buy", $_POST)) {
      // don't click buy but cart data still exist so reset it
      unset($_SESSION["cart_data"]);
      redirect_to_catalog();
    }

    // have cart data in session and clicked buy
    // use cart data to create new transaction
    $create_statement = file_get_contents($queries_path . 'transaction_create_now.sql');
    $conn = create_connection($config, $dbname);
    $prepared_create_statement = $conn->prepare($create_statement);
    $new_transaction_partner_id = null;
    // default null for now since not prioritized
    $new_transaction_shipment_id = null;
    $new_transaction_employee_id = null;
    $new_transaction_location_id = null;
    $prepared_create_statement->bind_param(
      "iiii",
      $new_transaction_partner_id,
      $new_transaction_shipment_id,
      $new_transaction_employee_id,
      $new_transaction_location_id
    );
    $create_result = $prepared_create_statement->execute();
    if (!$create_result){
      echo "Create statement failed!\n";
      exit();
    }
    // transaction products
    $transaction_id = $conn->insert_id;  // id of the transaction we just added
    $create_statement = file_get_contents($queries_path . 'transaction_products_create.sql');
    $prepared_create_statement = $conn->prepare($create_statement);
    $prepared_create_statement->bind_param(
      "iiid",
      $transaction_id,
      $product_id,
      $product_quantity,
      $product_price
    );
    foreach ($_SESSION["cart_data"] as $entry) {
      $product_id = htmlspecialchars($entry["id"]);
      $product_quantity = htmlspecialchars($entry["quantity"]);
      $product_price = htmlspecialchars($entry["price"]);
      $prepared_create_statement->execute();
      if (!$create_result){
        echo "Create statement failed!\n";
        exit();
      }
    }

    // preorder
    if ($_POST["is_preorder"]) {
      $statement = $conn->prepare("INSERT INTO preorders(transaction_id) VALUES (?)");
      $statement->bind_param("i", $transaction_id);
      if (!$statement->execute()){
        echo "Create statement failed!\n";
        exit();
      }
    }
    $conn->close();
    // Transaction added
    // Clear cart data and return to catalog
    unset($_SESSION["cart_data"]);
    redirect_to_catalog();
  }

  function create_connection($config, $dbname) {
    $conn = new mysqli(
        $config['mysqli.default_host'],
        $config['mysqli.default_user'],
        $config['mysqli.default_pw'],
        $dbname
    );
    if ($conn->connect_errno) {
        echo "Error: Failed to make a MySQL connection, here is why: ". "<br>";
        echo "Errno: " . $conn->connect_errno . "\n";
        echo "Error: " . $conn->connect_error . "\n";
        exit; // Quit this PHP script if the connection fails.
    }
    return $conn;
  }
  function redirect_to_get_request() {
    // Redirect to GET request
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
  }
  function redirect_to_catalog() {
    header("Location: catalog.php", true, 303);
    exit();
  }
?>

<!DOCTYPE html>
<html>
<head>
  <title>View cart</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      margin: 0;
      padding: 0;
    }

    h1 {
      text-align: center;
      color: #333;
      margin-top: 20px;
    }

    table {
      width: 90%;
      margin: 20px auto;
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

    p {
      text-align: center;
      font-size: 1.2em;
      color: #333;
    }

    form {
      max-width: 500px;
      margin: 20px auto;
      padding: 20px;
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    form label {
      display: block;
      margin-bottom: 10px;
      color: #555;
      font-size: 1em;
    }

    form input[type="checkbox"] {
      margin-right: 10px;
    }

    form input[type="submit"] {
      background-color: #757575;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      font-size: 1em;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    form input[type="submit"]:hover {
      background-color: #FF9800;
    }
  </style>
</head>
<body>
  <?php
    $conn = create_connection($config, $dbname);
    show_navbar($conn);
    $conn->close();
  ?>

  <h1>Cart</h1>
  <table>
    <tr><th>Product</th><th>Price</th></tr>
    <?php foreach ($cart_data as $entry) { ?>
      <tr>
        <td><?= $entry["name"] ?></td>
        <td>$<?= number_format($entry["price"], 2) . " x " . $entry["quantity"] ?></td>
      </tr>
    <?php } ?>
  </table>
  <p>Grand Total: $<?= number_format($total_price, 2) ?></p>

  <form method="POST">
    <label for="is_preorder">Pre-Order:  <input type="checkbox" name="is_preorder" value="preorder" /></label>
    <input type="submit" name="buy" value="Buy items in cart." />
  </form>
</body>
</html>
