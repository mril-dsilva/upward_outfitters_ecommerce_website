<?php
    require 'check_authentication.php';
    check_auth();
?>

 <!-- Include Navigation Bar function php-->
 <?php 
require 'navbar.php'; 
?>

<?php
ini_set('display_errors', 0);

$sql_location = "./sql/"; 

$config = parse_ini_file('../../mysql.ini');
$dbname = 'upward_outfitters';
$conn = new mysqli(
    $config['mysqli.default_host'],
    $config['mysqli.default_user'],
    $config['mysqli.default_pw'],
    $dbname
);

// form submissions
if (array_key_exists('create_customer_partner', $_POST)) {
    create_customer_partner($conn);
}
if (array_key_exists('create_supplier_partner', $_POST)) {
    create_supplier_partner($conn);
}
if (array_key_exists('update_partner', $_POST)) {
    update_partner($conn);
}
if (array_key_exists('delete_partners', $_POST)) {
    delete_partners($conn);
}

function create_customer_partner($conn) {
    global $sql_location;
    $create_stmt = "CALL create_customer_partner(?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($create_stmt);
    $stmt->bind_param('issss', $id, $name, $address, $phone, $email);

    $id = htmlspecialchars($_POST['partner_id']);
    $name = htmlspecialchars($_POST['partner_name']);
    $address = htmlspecialchars($_POST['address_id']);
    $phone = htmlspecialchars($_POST['partner_phone']);
    $email = htmlspecialchars($_POST['partner_email']);

    $stmt->execute();
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
}

function create_supplier_partner($conn) {
    global $sql_location;
    $create_stmt = "CALL create_supplier_partner(?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($create_stmt);
    $stmt->bind_param('issss', $id, $name, $address, $phone, $email);

    $id = htmlspecialchars($_POST['partner_id']);
    $name = htmlspecialchars($_POST['partner_name']);
    $address = htmlspecialchars($_POST['address_id']);
    $phone = htmlspecialchars($_POST['partner_phone']);
    $email = htmlspecialchars($_POST['partner_email']);

    $stmt->execute();
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
}


function update_partner($conn) {
    global $sql_location;
    $update_stmt = file_get_contents($sql_location . "partner_update.sql");
    $stmt = $conn->prepare($update_stmt);
    $stmt->bind_param('ssssi', $name, $address, $phone, $email, $id);

    $id = htmlspecialchars($_POST['partner_id']);
    $name = htmlspecialchars($_POST['partner_name']);
    $address = htmlspecialchars($_POST['address_id']);
    $phone = htmlspecialchars($_POST['partner_phone']);
    $email = htmlspecialchars($_POST['partner_email']);

    $stmt->execute();
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
}

function delete_partners($conn) {
    global $sql_location;
    if (!empty($_POST['delete'])) {
        foreach ($_POST['delete'] as $id) {
            $delete_stmt = file_get_contents($sql_location . "partner_delete.sql");
            $stmt = $conn->prepare($delete_stmt);
            $stmt->bind_param('i', $escaped_id);
            $escaped_id = htmlspecialchars($id);
            @$stmt->execute();
        }
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
        exit();
    } else {
        echo "<p>No partners selected for deletion.</p>";
    }
}

function fetch_partners($conn) {
    global $sql_location;
    $retrieve_query = file_get_contents($sql_location . 'partner_retrieve.sql');
    $result = $conn->query($retrieve_query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

$partners = fetch_partners($conn);
?>
<html>
<head>
    <title>Manage Partners</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        h1, h2 {
            text-align: center;
            color: #333;
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

        form {
            max-width: 800px;
            margin: 20px auto;
            padding: 10px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        form label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        form input, form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }

        form button {
            background-color: #757575;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #FF9800;
        }
    </style>
</head>
<body>
    <?php  show_navbar($conn);   
    ?>


    <h1>Partners</h1>

    <form method="POST">
        <table>
            <thead>
                <tr>
                    <th>Delete?</th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Address ID</th>
                    <th>Phone</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($partners as $partner): ?>
                    <tr>
                        <td><input type="checkbox" name="delete[]" value="<?= $partner['partner_id'] ?>"></td>
                        <td><?= $partner['partner_id'] ?></td>
                        <td><?= $partner['partner_name'] ?></td>
                        <td><?= $partner['address_id'] ?></td>
                        <td><?= $partner['partner_phone_number'] ?></td>
                        <td><?= $partner['partner_email'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" name="delete_partners">Delete Selected</button>
    </form>

    <h2>Add Customer Partner</h2>
    <form method="POST">
        <input type="text" name="partner_id" placeholder="ID" required>
        <input type="text" name="partner_name" placeholder="Name" required>
        <input type="text" name="address_id" placeholder="Address ID" required>
        <input type="text" name="partner_phone" placeholder="Phone" required>
        <input type="text" name="partner_email" placeholder="Email" required>
        <button type="submit" name="create_customer_partner">Submit</button>
    </form>

    <h2>Add Supplier Partner</h2>
    <form method="POST">
        <input type="text" name="partner_id" placeholder="ID" required>
        <input type="text" name="partner_name" placeholder="Name" required>
        <input type="text" name="address_id" placeholder="Address ID" required>
        <input type="text" name="partner_phone" placeholder="Phone" required>
        <input type="text" name="partner_email" placeholder="Email" required>
        <button type="submit" name="create_supplier_partner">Submit</button>
    </form>

    <h2>Update Partner</h2>
    <form method="POST">
        <input type="text" name="partner_id" placeholder="ID" required>
        <input type="text" name="partner_name" placeholder="Name" required>
        <input type="text" name="address_id" placeholder="Address ID" required>
        <input type="text" name="partner_phone" placeholder="Phone" required>
        <input type="text" name="partner_email" placeholder="Email" required>
        <button type="submit" name="update_partner">Submit</button>
    </form>
</body>
</html>
