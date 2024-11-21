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

    $conn->close();
    if (!$create_result){
      echo "Create statement failed!\n";  
      exit();
    }
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
  <form method="POST">
    <label for="new_transaction_partner_id">Partner</label>
    <select name="new_transaction_partner_id">
        <?php 
          $conn = create_connection($config, $dbname);
          $retrieve_query = file_get_contents($queries_path . 'partner_retrieve.sql');
          $partners_retrieved = $conn->query($retrieve_query);
          $conn -> close();
          if (!$partners_retrieved) {
              echo "Retrieve statement failed!\n";
              exit();
          }
          $partner_rows = $partners_retrieved->fetch_all();
        ?>
        <?php for ($i = 0; $i < $partners_retrieved->num_rows; $i++) { ?>
          <option value=<?= $partner_rows[$i][0] ?>>
            <?= $partner_rows[$i][1] ?>
          </option>
        <?php } ?>
    </select>
    <input type="submit" name="create_transaction" value="Add transaction" />
  </form>
</body>
</html>
