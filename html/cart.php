<?php
    require 'check_authentication.php';
    check_auth();
?>

<?php
  // Show all errors from the PHP interpreter.
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  // Show all errors from the MySQLi Extension.
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

  // Set config for user
  $config = parse_ini_file('../../mysql.ini');
  $dbname = "upward_outfitters";
  $queries_path = "./sql/";

  if (array_key_exists("quantity", $_POST) and array_key_exists("product_ids", $_POST)) {
    // Get names of products adn set cart data in session
    $conn = create_connection($config, $dbname);
    $statement = $conn->prepare("SELECT product_name, product_sale_price FROM products WHERE product_id = ?");
    $statement->bind_param("i", $product_id);
    $cart_data = [];
    $total_price = 0;

    $cart_empty = true;
    foreach ($_POST["product_ids"] as $product_id) {
      $quantity = $_POST["quantity"][$product_id];
      if ($quantity > 0) {
        $cart_empty = false;
        $statement->execute();
        $statement->store_result();
        $statement->bind_result($product_name, $product_price);
        while ($statement->fetch()) {
          $cart_data[] = [
            'id' => $product_id,
            'name' => $product_name,
            'quantity' => $quantity,
            'price' => $product_price
          ];
          $total_price += $quantity * $product_price;
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
      $product_id = $entry["id"];
      $product_quantity = $entry["quantity"];
      $product_price = $entry["price"];
      $prepared_create_statement->execute();
      if (!$create_result){
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
  <link rel="stylesheet" href="basic.css">
</head>
<body>
  <?php  show_navbar($conn);   
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
    <input type="submit" name="buy" value="Buy items in cart." />
  </form>
</body>
</html>
