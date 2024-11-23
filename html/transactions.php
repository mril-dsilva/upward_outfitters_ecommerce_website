<?php
  // Show all errors from the PHP interpreter.
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  // Show all errors from the MySQLi Extension.
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

  // Set config for user
  $config = parse_ini_file('./mysql.ini');
  $dbname = "upward_outfitters";
  $queries_path = "./sql/";

  // -- Handling session -------------------------------------------------------
  session_start();  // need to use session to keep track of
                    // which products the user chose to add to the transaction
  // Set default for session variables if not set
  if (!array_key_exists("new_transaction_products", $_SESSION)) {
    $_SESSION["new_transaction_products"] = [];
  }


  // ---- Select from transactions tables ---------------------------------------
  $retrieve_query = file_get_contents($queries_path . 'transaction_retrieve.sql');
  $conn = create_connection($config, $dbname);
  $transactions_retrieved = $conn->query($retrieve_query);
  $conn->close();
  if (!$transactions_retrieved){
      echo "Retrieve statement failed!\n";
      exit();
  }
  $transaction_rows = $transactions_retrieved->fetch_all();
  $transaction_fields = $transactions_retrieved->fetch_fields();

  // ---- Handling form submissions --------------------------------------------
  // Add new product entry to the new transaction
  if (array_key_exists("add_product_to_new_transaction", $_POST)) {
    $product_id = $_POST["new_transaction_product_id"];
    $conn = create_connection($config, $dbname);
    $prepared_query = $conn->prepare("SELECT product_name, product_sale_price FROM products WHERE product_id = ?");
    $prepared_query->bind_param("i", $product_id);
    if (!$prepared_query->execute()) {
        echo "Query execution failed!\n";
        exit();
    }
    $query_result = $prepared_query->get_result()->fetch_all()[0];

    if (!$_POST["new_transaction_product_quantity"]) {
      $product_quantity = 1;
    } else {
      $product_quantity = $_POST["new_transaction_product_quantity"];
    }
    if (!$_POST["new_transaction_product_price"]) {
      $product_price = $query_result[1];
    } else {
      $product_price = $_POST["new_transaction_product_price"];
    }
    $entry = array(
      "name"=>$query_result[0],
      "id"=>$product_id,
      "quantity"=>$product_quantity,
      "price"=>$product_price
    );
    array_push($_SESSION["new_transaction_products"], $entry);
    redirect_to_get_request();
  }

  // Create transaction
  if (array_key_exists("create_transaction", $_POST)) {
    $create_statement = file_get_contents($queries_path . 'transaction_create_now.sql');
    $conn = create_connection($config, $dbname);

    $prepared_create_statement = $conn->prepare($create_statement);
    $new_transaction_partner_id = $_POST["new_transaction_partner_id"];
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

    $transaction_id = $conn->insert_id;  // id of the transaction we just add
    $create_statement = file_get_contents($queries_path . 'transaction_products_create.sql');
    $prepared_create_statement = $conn->prepare($create_statement);
    $prepared_create_statement->bind_param(
      "iiid",
      $transaction_id,
      $product_id,
      $product_quantity,
      $product_price
    );

    foreach ($_SESSION["new_transaction_products"] as $entry) {
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
    // Clear selected products after transaction create
    $_SESSION["new_transaction_products"] = [];

    redirect_to_get_request();
  }

  // Delete
  if (array_key_exists("delete_transaction", $_POST)) {
    $delete_statement = file_get_contents($queries_path . "transaction_delete.sql");
    $conn = create_connection($config, $dbname);
    $prepared_delete_statement = $conn->prepare($delete_statement);
    $prepared_delete_statement->bind_param('i', $transaction_id);

    for ($i = 0; $i < $transactions_retrieved->num_rows; $i++) {
      $transaction_id = $transaction_rows[$i][0];
      $checkbox_name = "checkbox" . $transaction_id;
      if (array_key_exists($checkbox_name, $_POST)) {
        // delete selected transaction
        $delete_result = $prepared_delete_statement->execute();
        if (!$delete_result){
            echo "Delete statement failed!\n";
            $conn->close();
            exit();
         }
      }
    }
    $conn->close();
    redirect_to_get_request();
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
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Instruments</title>
  <link rel="stylesheet" href="basic.css">
</head>
<body>
  <?php function create_options($result_rows) { ?>
      <?php for ($i = 0; $i < count($result_rows); $i++) { ?>
        <option value=<?= $result_rows[$i][0] ?>>
          <?= $result_rows[$i][1] ?>
        </option>
      <?php } ?>
  <?php } ?>
  <h1>Transactions</h1>
  <!-- Transactions table -->
  <form method="POST">
    <table>
      <!-- Head -->
      <thead>
        <tr>
          <td>Delete?</td>
          <?php for ($i = 0; $i < $transactions_retrieved->field_count; $i++) { ?>
            <td><?= $transaction_fields[$i]->name ?></td>
          <?php } ?>
        </tr>
      </thead>

      <!-- Body -->
      <tbody>
        <?php for ($i = 0; $i < $transactions_retrieved->num_rows; $i++) { ?>
          <?php
            $transaction_id = $transaction_rows[$i][0];
          ?>
          <tr>
            <td>
              <input
                type="checkbox" 
                name=<?= "checkbox" . $transaction_id ?>
                value=<?= $transaction_id ?>
              />
            </td>
            <?php for($j = 0; $j < $transactions_retrieved->field_count; $j++){ ?>
              <td><?= $transaction_rows[$i][$j] ?></td>
            <?php } ?>
          </tr>
        <?php } ?>
      </tbody>
    </table>
    <input type="submit" name="delete_transaction" value="Delete Selected Transactions"/>
  </form>

  <h2> Add transaction </h2>
  <h3> Products in the new transaction </h3>
  <?php if (!$_SESSION["new_transaction_products"]) { ?>
    <p>No products in the new transaction</p>
  <?php } else { ?>
    <ul>
      <?php foreach ($_SESSION['new_transaction_products'] as $entry) { ?>
         <li> <?= $entry["name"] . " x " . $entry["quantity"] . ", price = $" . $entry["price"] ?></li>
       <?php } ?>
    </ul>
  <?php } ?>

  <form method="POST">
    <label for="new_transaction_product_id">Select product</label>
    <select name="new_transaction_product_id">
      <?php 
          $retrieve_query = file_get_contents($queries_path . 'product_retrieve.sql');
          $conn = create_connection($config, $dbname);
          $prepared_query = $conn->prepare($retrieve_query);
          $category = -1;  // do not filter category
          $prepared_query->bind_param('i', $category);
          if (!$prepared_query->execute()) {
            echo "Retrieve products failed to execute!\n";
            exit();
          }
          $products_retrieved = $prepared_query->get_result();
          $conn->close();

          if (!$products_retrieved) {
              echo "Retrieve statement failed!\n";
              exit();
          }
          $result_rows = $products_retrieved->fetch_all();
        ?>
        <?php for ($i = 0; $i < count($result_rows); $i++) { ?>
          <option value=<?= $result_rows[$i][0] ?>>
            <?= $result_rows[$i][1] ?>
          </option>
        <?php } ?>
    </select>
    <label for="new_transaction_product_quantity">Enter quantity for product</label>
    <input type="number" name="new_transaction_product_quantity" value=1 min=0/>
    <label for="new_transaction_product_price">Enter price for product</label>
    <input type="number" name="new_transaction_product_price" min=0 step=0.01 />
    <button type="submit" name="add_product_to_new_transaction">
      Add product to transaction
    </button>
  </form>
  <form method="POST">
    <label for="new_transaction_partner_id">Partner</label>
    <select name="new_transaction_partner_id">
        <?php 
          $conn = create_connection($config, $dbname);
          $retrieve_query = file_get_contents($queries_path . 'partner_retrieve.sql');
          $partners_retrieved = $conn->query($retrieve_query);
          $conn->close();
          if (!$partners_retrieved) {
              echo "Retrieve statement failed!\n";
              exit();
          }
          create_options($partners_retrieved->fetch_all());
        ?>
    </select>
    <input type="submit" name="create_transaction" value="Add transaction" />
  </form>
</body>
</html>
