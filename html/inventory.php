<?php
    // Show all errors from the PHP interpreter.
    ini_set('display_errors', 1);    
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Show all errors from the MySQLi Extension.
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);  

    $sql_location = './sql/';

    # Create new connection, specifying the database we care about
    $config = parse_ini_file('../../mysql.ini');
    $dbname = 'upward_outfitters';
    $conn = new mysqli(
            $config['mysqli.default_host'],
            $config['mysqli.default_user'],
            $config['mysqli.default_pw'],
            $dbname);

    // Add a product if the user hit the add submit button
    if(array_key_exists('create_product', $_POST)){
        create_product($conn);
    }

    // Update a product if the user hit the update submit button
    if(array_key_exists('update_product', $_POST)){
        update_product($conn);
    }

    // Add a brand if the user hit the add submit button
    if(array_key_exists('create_brand', $_POST)){
        create_brand($conn);
    }

    // Update a brand if the user hit the update submit button
    if(array_key_exists('update_brand', $_POST)){
        update_brand($conn);
    }

    // Add a brand if the user hit the add submit button
    if(array_key_exists('create_category', $_POST)){
        create_category($conn);
    }

    // Update a brand if the user hit the update submit button
    if(array_key_exists('update_category', $_POST)){
        update_category($conn);
    }

    // Add a size if the user hit the add submit button
    if(array_key_exists('create_size', $_POST)){
        create_size($conn);
    }
?>

<html>
    <link rel="stylesheet" href="basic.css">
    <body>
        <h1>Inventory</h1>

        <form action="inventory.php" method="GET">
            <label for="product_category_filter">Category</label>
            <select name="product_category_filter">
                <option value=-1></option>
                <?php create_category_options($conn) ?>
            </select>
            <input type="submit" value="Filter" />
        </form>

        <?php
            // I need to do all of the select queries before I render any of them, because rendering
            // one prevents the GET redirect at the end of any of the delete statements from working. 
            // (Echo locks in the headers, and to do the get request I do a header modification.)

            global $sql_location;

        // Product select
            $prod_tbl = file_get_contents($sql_location . "product_retrieve.sql");
            $prod_tbl = $conn -> prepare($prod_tbl);

            // Make sure add statement was prepared properly
            if (!$prod_tbl) {
                echo "Couldn't prepare the statement";
                exit;
            }

            $prod_tbl -> bind_param('i', $filter);

            if (array_key_exists('product_category_filter', $_GET)){
                    $filter = $_GET['product_category_filter'];
            }else{
                $filter = -1;
            }

            $prod_tbl -> execute();

            $prod_result = $prod_tbl -> get_result();

            $prod_queries = $prod_result -> fetch_all();
            $prod_n_rows = $prod_result -> num_rows;
            $prod_n_cols = $prod_result -> field_count;
            $prod_fields = $prod_result -> fetch_fields();
            $prod_result -> close();
            $conn -> next_result();

            // Delete any products that need to go
            if(array_key_exists('delete_products', $_POST)){
                delete_products($conn, $prod_queries, $prod_n_rows, $prod_n_cols, $prod_fields);
            }

            //Discontinue any products that need to
            discontinue_product($conn, $prod_queries, $prod_n_rows, $prod_n_cols, $prod_fields);

        // Brand select
            $brand_tbl = file_get_contents($sql_location . 'brand_retrieve.sql');
            $brand_result = $conn->query($brand_tbl);

            $brand_queries = $brand_result -> fetch_all();
            $brand_n_rows = $brand_result -> num_rows;
            $brand_n_cols = $brand_result -> field_count;
            $brand_fields = $brand_result -> fetch_fields();

            // Delete any brands that have been selected
            if(array_key_exists('delete_brands', $_POST)){
                delete_brands($conn, $brand_queries, $brand_n_rows, $brand_n_cols, $brand_fields);
            }

        // Category select
            $cat_tbl = file_get_contents($sql_location . 'category_retrieve.sql');
            $cat_result = $conn->query($cat_tbl);

            $cat_queries = $cat_result -> fetch_all();
            $cat_n_rows = $cat_result -> num_rows;
            $cat_n_cols = $cat_result -> field_count;
            $cat_fields = $cat_result -> fetch_fields();

            // Delete any categories that have been selected
            if(array_key_exists('delete_categories', $_POST)){
                delete_categories($conn, $cat_queries, $cat_n_rows, $cat_n_cols, $cat_fields);
            }

        // Size select
            $size_tbl = file_get_contents($sql_location . 'product_size_retrieve.sql');
            $size_result = $conn->query($size_tbl);

            $size_queries = $size_result -> fetch_all();
            $size_n_rows = $size_result -> num_rows;
            $size_n_cols = $size_result -> field_count;
            $size_fields = $size_result -> fetch_fields();

            // Delete any sizes that have been selected
            if(array_key_exists('delete_sizes', $_POST)){
                delete_sizes($conn, $size_queries, $size_n_rows, $size_n_cols, $size_fields);
            }
            

        // Display products on the page
            inventory_result_to_html_table($prod_queries, $prod_n_rows, $prod_n_cols, $prod_fields);

        ?>

        <h2>Add Product</h2>
        <form method="POST">
            <label for="product_name">Name</label>
            <input type="text" name="product_name" required/>

            <label for="product_price">Price</label>
            <input type="number" name="product_price" min="0.01" step=".01" required/>

            <label for="product_desc">Description</label>
            <input type="text" name="product_desc" />

            <label for="product_warranty_length">Warranty Length</label>
            <input type="text" name="product_warranty_length" />

            <label for="product_discontinued">Discontinued</label>
            <input type="checkbox" name="product_discontinued" />

            <label for="product_discount_pct">Discount Percentage</label>
            <input type="number" name="product_discount_pct" />

            <label for="product_length">Length</label>
            <input type="number" name="product_length" />

            <label for="product_size_id">Size</label>
            <select name="product_size_id">
                <option value="none">None</option>
                <?php create_size_options($conn) ?>
            </select>

            <label for="product_shoe_size">Shoe Size</label>
            <input type="text" name="product_shoe_size" />

            <label for="product_capacity">Capacity</label>
            <input type="text" name="product_capacity" />

            <label for="product_brand_id">Brand</label>
            <select name="product_brand_id">
                <option value="none">None</option>
                <?php create_brand_options($conn) ?>
            </select>

            <label for="product_category_id">Category</label>
            <select name="product_category_id">
                <?php create_category_options($conn) ?>
            </select>

            <label for="product_partner_id">Partner</label>
            <select name="product_partner_id">
                <?php create_partner_options($conn) ?>
            </select>

            <input type="submit" name="create_product" value="Submit" />
        </form>
       
        <h2>Update Product</h2>
        <form method="POST">
            <label for="u_product_id">ID</label>
            <input type="number" name="u_product_id" required/>

            <label for="u_product_name">Name</label>
            <input type="text" name="u_product_name" required/>

            <label for="u_product_price">Price</label>
            <input type="number" name="u_product_price" min="0.01" step=".01" required/>

            <label for="u_product_desc">Description</label>
            <input type="text" name="u_product_desc" />

            <label for="u_product_warranty_length">Warranty Length</label>
            <input type="text" name="u_product_warranty_length" />

            <label for="u_product_discontinued">Discontinued</label>
            <input type="checkbox" name="u_product_discontinued" />

            <label for="u_product_discount_pct">Discount Percentage</label>
            <input type="number" name="u_product_discount_pct" />

            <label for="u_product_length">Length</label>
            <input type="number" name="u_product_length" />

            <label for="u_product_size_id">Size</label>
            <select name="u_product_size_id">
                <option value="none">None</option>
                <?php create_size_options($conn) ?>
            </select>

            <label for="u_product_shoe_size">Shoe Size</label>
            <input type="text" name="u_product_shoe_size" />

            <label for="u_product_capacity">Capacity</label>
            <input type="text" name="u_product_capacity" />

            <label for="u_product_brand_id">Brand</label>
            <select name="u_product_brand_id">
                <option value="none">None</option>
                <?php create_brand_options($conn) ?>
            </select>

            <label for="u_product_category_id">Category</label>
            <select name="u_product_category_id">
                <?php create_category_options($conn) ?>
            </select>

            <label for="u_product_partner_id">Partner</label>
            <select name="u_product_partner_id">
                <?php create_partner_options($conn) ?>
            </select>

            <input type="submit" name="update_product" value="Submit" />
        </form>

        <h2>Brands</h2>
        <?php
            // Display records on the page
            brand_result_to_html_table($brand_queries, $brand_n_rows, $brand_n_cols, $brand_fields);
        ?>

        <h3>Add Brand</h3>
        <form method="POST">
            <label for="c_brand_name">Name</label>
            <input type="text" name="c_brand_name" />

            <input type="submit" name="create_brand" value="Submit" />
        </form>

        <h3>Update Brand</h3>
        <form method="POST">
            <label for="u_brand_id">ID</label>
            <input type="number" name="u_brand_id" />

            <label for="u_brand_name">Name</label>
            <input type="text" name="u_brand_name" />

            <input type="submit" name="update_brand" value="Submit" />
        </form>
        

        <h2>Categories</h2>
        <?php
            // Display categories on the page
            category_result_to_html_table($cat_queries, $cat_n_rows, $cat_n_cols, $cat_fields);
        ?>

        <h3>Add Category</h3>
        <form method="POST">
            <label for="c_category_name">Name</label>
            <input type="text" name="c_category_name" />

            <input type="submit" name="create_category" value="Submit" />
        </form>

        <h3>Update Category</h3>
        <form method="POST">
            <label for="u_category_id">ID</label>
            <input type="number" name="u_category_id" />

            <label for="u_brand_name">Name</label>
            <input type="text" name="u_category_name" />

            <input type="submit" name="update_category" value="Submit" />
        </form>

        <h2>Sizes</h2>
        <?php
            // Display sizes on the page
            size_result_to_html_table($size_queries, $size_n_rows, $size_n_cols, $size_fields);
        ?>

        <h3>Add Brand</h3>
        <form method="POST">
            <label for="c_size_name">Name</label>
            <input type="text" name="c_size_name" />

            <input type="submit" name="create_size" value="Submit" />
        </form>

    </body>
</html>

<?php

function delete_products($conn, $queries, $n_rows, $n_cols, $fields) {
    global $sql_location;

    // Prepare delete statement
    $del_stmt = file_get_contents($sql_location . "product_delete.sql");
    $del_stmt = $conn -> prepare($del_stmt);

    // Make sure delete statement was prepared properly
    if (!$del_stmt) {
        echo "Couldn't prepare the statement";
        exit;
    }

    // Bind id parameter to delete statement
    $del_stmt -> bind_param('i', $id);

    // Loop through all of the returned records, and delete them if
    // they are present in the $_POST superglobal array
    for ($i=0; $i<$n_rows; $i++){
        $id = $queries[$i][0];

        // See if array key of checkbox[$id] is present in the superglobal array
        if(array_key_exists('product_delete' . $id, $_POST)){
            $result = $del_stmt -> execute();
        }
    }
    
    // Redirect the client to this page, but using a get request this time.
    // Code 303 means "See other"
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
}

function discontinue_product($conn, $queries, $n_rows, $n_cols, $fields) {
    global $sql_location;

    // Variable to track if something was deleted, and thus if we need
    // to force a get request at the end
    $were_discontinued = false;

    // Prepare delete statement
    $dis_stmt = file_get_contents($sql_location . "product_discontinue.sql");
    $dis_stmt = $conn -> prepare($dis_stmt);

    // Make sure delete statement was prepared properly
    if (!$dis_stmt) {
        echo "Couldn't prepare the statement";
        exit;
    }

    // Bind id parameter to delete statement
    $dis_stmt -> bind_param('i', $id);

    // Loop through all of the returned records, and delete them if
    // they are present in the $_POST superglobal array
    for ($i=0; $i<$n_rows; $i++){
        $id = $queries[$i][0];

        // See if array key of checkbox[$id] is present in the superglobal array
        if(array_key_exists('discontinue' . $id, $_POST)){
            $result = $dis_stmt -> execute();
            $were_discontinued = true;
        }
    }
    
    // If at least 1 record was deleted, redirect the client to this page, but using a get request this time.
    // Code 303 means "See other"
    if($were_discontinued){
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
        exit();
    }
}

function create_product($conn) {
    global $sql_location;

    // Prepare add statement
    $add_stmt = file_get_contents($sql_location . "product_create.sql");
    $add_stmt = $conn -> prepare($add_stmt);

    // Make sure add statement was prepared properly
    if (!$add_stmt) {
        echo "Couldn't prepare the statement";
        exit;
    }

    $add_stmt -> bind_param('sdsiiiiidssss',
        $name, $price, $desc, $warranty, $brand, $category,
        $discontinued, $partner, $discount, $length, $size,
        $shoe_size, $capacity);

    $name = htmlspecialchars($_POST['product_name']);
    $price = htmlspecialchars($_POST['product_price']);
    $desc = htmlspecialchars($_POST['product_desc']);
    $warranty = htmlspecialchars($_POST['product_warranty_length']);

    $discontinued = 0;

    if (array_key_exists('product_discontinued', $_POST)){
        $discontinued = 1;
    }

    $discount = htmlspecialchars($_POST['product_discount_pct']);
    $category = htmlspecialchars($_POST['product_category_id']);
    $partner = htmlspecialchars($_POST['product_partner_id']);

    $length = htmlspecialchars($_POST['product_length']);
    $shoe_size = htmlspecialchars($_POST['product_shoe_size']);
    $capacity = htmlspecialchars($_POST['product_capacity']);
    
    if ($shoe_size == '') {
        $shoe_size = null;
    }

    if ($length == '') {
        $length = null;
    }

    if ($capacity == '') {
        $capacity = null;
    }

    $size = htmlspecialchars($_POST['product_size_id']);

    if ($size == "none") {
        $size = null;
    }

    $brand = htmlspecialchars($_POST['product_brand_id']);

    if ($brand == "none") {
        $brand = null;
    }

    $result = $add_stmt -> execute();

    // Redirect the client to this page, but using a get request this time.
    // Code 303 means "See other"
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
}

function update_product($conn) {
    global $sql_location;

    // Prepare delete statement
    $upd_stmt = file_get_contents($sql_location . "product_update.sql");
    $upd_stmt = $conn -> prepare($upd_stmt);

    // Make sure delete statement was prepared properly
    if (!$upd_stmt) {
        echo "Couldn't prepare the statement";
        exit;
    }

    $upd_stmt -> bind_param('isdsiiiiidssss', $id,
        $name, $price, $desc, $warranty, $brand, $category,
        $discontinued, $partner, $discount, $length, $size,
        $shoe_size, $capacity);

    $id = htmlspecialchars($_POST['u_product_id']);
    $name = htmlspecialchars($_POST['u_product_name']);
    $price = htmlspecialchars($_POST['u_product_price']);
    $desc = htmlspecialchars($_POST['u_product_desc']);
    $warranty = htmlspecialchars($_POST['u_product_warranty_length']);

    $discontinued = 0;

    if (array_key_exists('u_product_discontinued', $_POST)){
        $discontinued = 1;
    }

    $discount = htmlspecialchars($_POST['u_product_discount_pct']);
    $category = htmlspecialchars($_POST['u_product_category_id']);
    $partner = htmlspecialchars($_POST['u_product_partner_id']);

    $length = htmlspecialchars($_POST['u_product_length']);
    $shoe_size = htmlspecialchars($_POST['u_product_shoe_size']);
    $capacity = htmlspecialchars($_POST['u_product_capacity']);
    
    if ($shoe_size == '') {
        $shoe_size = null;
    }

    if ($length == '') {
        $length = null;
    }

    if ($capacity == '') {
        $capacity = null;
    }

    $size = htmlspecialchars($_POST['u_product_size_id']);

    if ($size == "none") {
        $size = null;
    }

    $brand = htmlspecialchars($_POST['u_product_brand_id']);

    if ($brand == "none") {
        $brand = null;
    }

    $result = $upd_stmt -> execute();

    // Redirect the client to this page, but using a get request this time.
    // Code 303 means "See other"
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
}

function inventory_result_to_html_table($queries, $n_rows, $n_cols, $fields) {
    
    ?>
        <!-- Description of table - - - - - - - - - - - - - - - - - - - - -->
        <!-- <p>This table has <?php //echo $n_rows; ?> and <?php //echo $n_cols; ?> columns.</p> -->
        
        <!-- Begin header - - - - - - - - - - - - - - - - - - - - -->
        <!-- Using default action (this page). -->
    <form method="POST">
        <table>
            <thead>
                <tr>
                    <td><b>Delete?</b></td>
                    <td><b>Discontinue?</b></td>
                    <?php for ($i=0; $i<$n_cols; $i++){ ?>
                        <td><b><?php echo $fields[$i]->name; ?></b></td>
                    <?php } ?>
                </tr>
            </thead>
            
            <!-- Begin body - - - - - - - - - - - - - - - - - - - - - -->
            <tbody>
                <?php for ($i=0; $i<$n_rows; $i++){ ?>
                    <?php $id = $queries[$i][0]; ?>
                    <tr>
                        <td>
                            <input type="checkbox" name=<?php echo "product_delete" . $id;?> value=<?php echo $id; ?>>
                        </td>
                        <td>
                            <form method="POST">
                                <input type="submit" name=<?php echo "discontinue" . $id;?> value="Discontinue">
                            </form>
                        </td>
                        <?php for($j=0; $j<$n_cols; $j++){ ?>
                            <td><?php echo $queries[$i][$j]; ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <p><input type="submit" name="delete_products" value="Delete Selected Records" /></p>
    </form>
<?php } 

function create_brand_options($conn){
    global $sql_location;

    $sel_tbl = file_get_contents($sql_location . 'brand_retrieve.sql');
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

function create_partner_options($conn){
    global $sql_location;

    $sel_tbl = file_get_contents($sql_location . 'partner_retrieve.sql');
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

function create_size_options($conn){
    global $sql_location;

    $sel_tbl = file_get_contents($sql_location . 'product_size_retrieve.sql');
    $result = $conn -> query($sel_tbl);

    $queries = $result -> fetch_all();
    $n_rows = $result -> num_rows;
    $n_cols = $result -> field_count;
    $fields = $result -> fetch_fields();
    $result -> close();
    $conn -> next_result();

    for ($i = 0; $i < $n_rows; $i++){
        $id = $queries[$i][0]; ?>
        <option value=<?php echo $id;?>><?php echo $id;?></option>
    <?php }
}

// Brand functions
function brand_result_to_html_table($queries, $n_rows, $n_cols, $fields) {
    
    ?>
        <!-- Description of table - - - - - - - - - - - - - - - - - - - - -->
        <!-- <p>This table has <?php //echo $n_rows; ?> and <?php //echo $n_cols; ?> columns.</p> -->
        
        <!-- Begin header - - - - - - - - - - - - - - - - - - - - -->
        <!-- Using default action (this page). -->
    <form method="POST">
        <table>
            <thead>
                <tr>
                    <td><b>Delete?</b></td>
                    <?php for ($i=0; $i<$n_cols; $i++){ ?>
                        <td><b><?php echo $fields[$i]->name; ?></b></td>
                    <?php } ?>
                </tr>
            </thead>
            
            <!-- Begin body - - - - - - - - - - - - - - - - - - - - - -->
            <tbody>
                <?php for ($i=0; $i<$n_rows; $i++){ ?>
                    <?php $id = $queries[$i][0]; ?>
                    <tr>
                        <td>
                            <input type="checkbox" name=<?php echo "brand_delete" . $id;?> value=<?php echo $id; ?>>
                        </td>
                        <?php for($j=0; $j<$n_cols; $j++){ ?>
                            <td><?php echo $queries[$i][$j]; ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <p><input type="submit" name="delete_brands" value="Delete Selected Records" /></p>
    </form>
<?php } 

function delete_brands($conn, $queries, $n_rows, $n_cols, $fields) {
    global $sql_location;

    // Prepare delete statement
    $del_stmt = file_get_contents($sql_location . "brand_delete.sql");
    $del_stmt = $conn -> prepare($del_stmt);

    // Make sure delete statement was prepared properly
    if (!$del_stmt) {
        echo "Couldn't prepare the statement";
        exit;
    }

    // Bind id parameter to delete statement
    $del_stmt -> bind_param('i', $id);

    // Loop through all of the returned records, and delete them if
    // they are present in the $_POST superglobal array
    for ($i=0; $i<$n_rows; $i++){
        $id = $queries[$i][0];

        // See if array key of checkbox[$id] is present in the superglobal array
        if(array_key_exists('brand_delete' . $id, $_POST)){
            $result = $del_stmt -> execute();
        }
    }
    
    // Redirect the client to this page, but using a get request this time.
    // Code 303 means "See other"
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
}

function create_brand($conn) {
    global $sql_location;

    // Prepare add statement
    $add_stmt = file_get_contents($sql_location . "brand_create.sql");
    $add_stmt = $conn -> prepare($add_stmt);

    // Make sure add statement was prepared properly
    if (!$add_stmt) {
        echo "Couldn't prepare the statement";
        exit;
    }

    $add_stmt -> bind_param('s', $name);

    $name = htmlspecialchars($_POST['c_brand_name']);

    $result = $add_stmt -> execute();

    // Redirect the client to this page, but using a get request this time.
    // Code 303 means "See other"
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
}

function update_brand($conn) {
    global $sql_location;

    // Prepare delete statement
    $upd_stmt = file_get_contents($sql_location . "brand_update.sql");
    $upd_stmt = $conn -> prepare($upd_stmt);

    // Make sure delete statement was prepared properly
    if (!$upd_stmt) {
        echo "Couldn't prepare the statement";
        exit;
    }

    $upd_stmt -> bind_param('is', $id, $name);

    $id = htmlspecialchars($_POST['u_brand_id']);
    $name = htmlspecialchars($_POST['u_brand_name']);
    
    $result = $upd_stmt -> execute();

    // Redirect the client to this page, but using a get request this time.
    // Code 303 means "See other"
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
}

// Category functions
function category_result_to_html_table($queries, $n_rows, $n_cols, $fields) {
    
    ?>
        <!-- Description of table - - - - - - - - - - - - - - - - - - - - -->
        <!-- <p>This table has <?php //echo $n_rows; ?> and <?php //echo $n_cols; ?> columns.</p> -->
        
        <!-- Begin header - - - - - - - - - - - - - - - - - - - - -->
        <!-- Using default action (this page). -->
    <form method="POST">
        <table>
            <thead>
                <tr>
                    <td><b>Delete?</b></td>
                    <?php for ($i=0; $i<$n_cols; $i++){ ?>
                        <td><b><?php echo $fields[$i]->name; ?></b></td>
                    <?php } ?>
                </tr>
            </thead>
            
            <!-- Begin body - - - - - - - - - - - - - - - - - - - - - -->
            <tbody>
                <?php for ($i=0; $i<$n_rows; $i++){ ?>
                    <?php $id = $queries[$i][0]; ?>
                    <tr>
                        <td>
                            <input type="checkbox" name=<?php echo "category_delete" . $id;?> value=<?php echo $id; ?>>
                        </td>
                        <?php for($j=0; $j<$n_cols; $j++){ ?>
                            <td><?php echo $queries[$i][$j]; ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <p><input type="submit" name="delete_categories" value="Delete Selected Records" /></p>
    </form>
<?php } 

function delete_categories($conn, $queries, $n_rows, $n_cols, $fields) {
    global $sql_location;

    // Prepare delete statement
    $del_stmt = file_get_contents($sql_location . "category_delete.sql");
    $del_stmt = $conn -> prepare($del_stmt);

    // Make sure delete statement was prepared properly
    if (!$del_stmt) {
        echo "Couldn't prepare the statement";
        exit;
    }

    // Bind id parameter to delete statement
    $del_stmt -> bind_param('i', $id);

    // Loop through all of the returned records, and delete them if
    // they are present in the $_POST superglobal array
    for ($i=0; $i<$n_rows; $i++){
        $id = $queries[$i][0];

        // See if array key of checkbox[$id] is present in the superglobal array
        if(array_key_exists('category_delete' . $id, $_POST)){
            $result = $del_stmt -> execute();
        }
    }
    
    // Redirect the client to this page, but using a get request this time.
    // Code 303 means "See other"
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
}

function create_category($conn) {
    global $sql_location;

    // Prepare add statement
    $add_stmt = file_get_contents($sql_location . "category_create.sql");
    $add_stmt = $conn -> prepare($add_stmt);

    // Make sure add statement was prepared properly
    if (!$add_stmt) {
        echo "Couldn't prepare the statement";
        exit;
    }

    $add_stmt -> bind_param('s', $name);

    $name = htmlspecialchars($_POST['c_category_name']);

    $result = $add_stmt -> execute();

    // Redirect the client to this page, but using a get request this time.
    // Code 303 means "See other"
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
}

function update_category($conn) {
    global $sql_location;

    // Prepare delete statement
    $upd_stmt = file_get_contents($sql_location . "category_update.sql");
    $upd_stmt = $conn -> prepare($upd_stmt);

    // Make sure delete statement was prepared properly
    if (!$upd_stmt) {
        echo "Couldn't prepare the statement";
        exit;
    }

    $upd_stmt -> bind_param('is', $id, $name);

    $id = htmlspecialchars($_POST['u_category_id']);
    $name = htmlspecialchars($_POST['u_category_name']);
    
    $result = $upd_stmt -> execute();

    // Redirect the client to this page, but using a get request this time.
    // Code 303 means "See other"
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
}

// Category functions
function size_result_to_html_table($queries, $n_rows, $n_cols, $fields) {
    
    ?>
        <!-- Description of table - - - - - - - - - - - - - - - - - - - - -->
        <!-- <p>This table has <?php //echo $n_rows; ?> and <?php //echo $n_cols; ?> columns.</p> -->
        
        <!-- Begin header - - - - - - - - - - - - - - - - - - - - -->
        <!-- Using default action (this page). -->
    <form method="POST">
        <table>
            <thead>
                <tr>
                    <td><b>Delete?</b></td>
                    <?php for ($i=0; $i<$n_cols; $i++){ ?>
                        <td><b><?php echo $fields[$i]->name; ?></b></td>
                    <?php } ?>
                </tr>
            </thead>
            
            <!-- Begin body - - - - - - - - - - - - - - - - - - - - - -->
            <tbody>
                <?php for ($i=0; $i<$n_rows; $i++){ ?>
                    <?php $id = $queries[$i][0]; ?>
                    <tr>
                        <td>
                            <input type="checkbox" name=<?php echo "size_delete" . $id;?> value=<?php echo $id; ?>>
                        </td>
                        <?php for($j=0; $j<$n_cols; $j++){ ?>
                            <td><?php echo $queries[$i][$j]; ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <p><input type="submit" name="delete_sizes" value="Delete Selected Records" /></p>
    </form>
<?php } 

function delete_sizes($conn, $queries, $n_rows, $n_cols, $fields) {
    global $sql_location;

    // Prepare delete statement
    $del_stmt = file_get_contents($sql_location . "product_size_delete.sql");
    $del_stmt = $conn -> prepare($del_stmt);

    // Make sure delete statement was prepared properly
    if (!$del_stmt) {
        echo "Couldn't prepare the statement";
        exit;
    }

    // Bind id parameter to delete statement
    $del_stmt -> bind_param('s', $id);

    // Loop through all of the returned records, and delete them if
    // they are present in the $_POST superglobal array
    for ($i=0; $i<$n_rows; $i++){
        $id = $queries[$i][0];

        // See if array key of checkbox[$id] is present in the superglobal array
        if(array_key_exists('size_delete' . $id, $_POST)){
            $result = $del_stmt -> execute();
        }
    }
    
    // Redirect the client to this page, but using a get request this time.
    // Code 303 means "See other"
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
}

function create_size($conn) {
    global $sql_location;

    // Prepare add statement
    $add_stmt = file_get_contents($sql_location . "product_size_create.sql");
    $add_stmt = $conn -> prepare($add_stmt);

    // Make sure add statement was prepared properly
    if (!$add_stmt) {
        echo "Couldn't prepare the statement";
        exit;
    }

    $add_stmt -> bind_param('s', $name);

    $name = htmlspecialchars($_POST['c_size_name']);

    $result = $add_stmt -> execute();

    // Redirect the client to this page, but using a get request this time.
    // Code 303 means "See other"
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
}

?>