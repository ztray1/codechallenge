<?php

$uploads_directory="uploads";

function last_id(){
    global $connection;
    return mysqli_insert_id($connection);
}


function set_message($msg)
{
    if (!empty($msg)) {
        $_SESSION['message'] = $msg;
    }
}
function display_message()
{
    if (isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }

}

function redirect($location){
    header("Location:$location");
}

function query($sql){
    global $connection;
    return mysqli_query($connection,$sql);
}
function confirm($result){
    global $connection;
    if(!$result){
        die("QUERY FAILED".mysqli_error($connection));
    }
}

function escape_string($string){
    global $connection;
    return mysqli_real_escape_string($connection,$string);
}

function fetch_array($result){
    return (mysqli_fetch_assoc($result));
}

/*********front end function*********/

function get_products(){
    $query=query("SELECT * FROM products");
    confirm($query);
    while($row=fetch_array($query)){
        $category_links=<<<DELIMETER
        
        <a class='list-group-item'>{$row['product_title']}</a>


DELIMETER;
        echo $category_links;
    }
}
function get_categories(){
    $query=query("SELECT * FROM categories");
    confirm($query);
    while($row=fetch_array($query)){
        $category_links=<<<DELIMETER
        
        <a class='list-group-item'>{$row['cat_title']}</a>


DELIMETER;
        echo $category_links;
    }
}

function get_products_in_cat_page(){
    $query=query("SELECT * FROM products WHERE product_category_id=".escape_string($_GET["id"])." AND product_quantity>=1");
    confirm($query);
    while($row=fetch_array($query)){
        $product_image=display_image($row["product_image"]);
        $product=<<<DELIMETER
        
                    <div class="col-md-3 col-sm-6 hero-feature">
                        <div class="thumbnail">
                            <img src="../resources/{$product_image}" alt="">
                            <div class="caption">
                                <h3>{$row["product_title"]}</h3>
                                <p>Lorem ipsum dolor</p>
                                <p>
                                <a href="../resources/cart.php?add={$row['product_id']}"class="btn btn-primary" target="_blank" href="#">Buy now</a> <a href="item.php?id={$row['product_id']}" class="btn btn-default">More Info</a>
                                </p>
                            </div>
                        </div>
                    </div>
               

DELIMETER;
        echo $product;

    }
}





function login_user(){
    if(isset($_POST["submit"])){
        $username=escape_string($_POST["username"]);
        $password=escape_string($_POST["password"]);
        $query=query("SELECT * FROM users WHERE username = '{$username}' AND password ='{$password}'");
        confirm($query);
        if(mysqli_num_rows($query)==0){
            set_message("your password or username is wrong");
            redirect("login.php");
        }
        else{
            $_SESSION['username']=$username;
            redirect("admin");
        }

    }
}



/************Admin Products Page*****************/
function display_image($picture){
    global $uploads_directory;
    return $uploads_directory.DS.$picture;
}
function get_products_in_admin(){
    $query=query("SELECT * FROM products");
    confirm($query);
    while($row=fetch_array($query)){
        $category=show_product_category_title($row['product_category_id']);
        $product_image=display_image($row["product_image"]);
        $product=<<<DELIMETER
        <tr>
            <td>{$row["product_id"]}</td>
            <td><a href="index.php?edit_product&id={$row['product_id']}">{$row["product_title"]}</a> <br>
              <a href="index.php?edit_product&id={$row['product_id']}"><img width="100" src="../../resources/{$product_image}" alt=""></a>
            </td>
            <td>{$category}</td>
            <td>{$row["product_price"]}</td>
            <td>{$row["product_quantity"]}</td>
            <td><a class="btn-danger btn" href="../../resources/templates/back/delete_product.php?id={$row['product_id']}"><span class="glyphicon glyphicon-remove"</a></td>

        </tr>
        

DELIMETER;
        echo $product;

    }

}
function show_product_category_title($product_category_id){

    $category_query=query("SELECT * FROM categories WHERE cat_id ='{$product_category_id}'");
    confirm($category_query);
    while($category_row=fetch_array($category_query)){

        return $category_row['cat_title'];
    }

}
/******************Add products in admin*****************/


function add_product(){
    if(isset($_POST['publish'])){
        $product_title       =escape_string($_POST['product_title']);
        $product_category_id =escape_string($_POST['product_category_id']);
        $product_price       =escape_string($_POST['product_price']);
        $sku                 =escape_string($_POST['sku']);
        $short_desc          =escape_string($_POST['short_desc']);
        $product_quantity    =escape_string($_POST['product_quantity']);
        $product_image       =$_FILES['file']['name'];
        $image_temp_location =$_FILES['file']['tmp_name'];

        move_uploaded_file( $image_temp_location , UPLOAD_DIRECTORY.DS.$product_image );

        $query=query("INSERT INTO products(product_title,product_category_id,product_price,sku,short_desc,product_quantity,product_image)VALUES('{$product_title}','{$product_category_id}','{$product_price}','{$sku}','{$short_desc}','{$product_quantity}','{$product_image}')");
        $last_id=last_id();
        confirm($query);
        set_message("new product with id {$last_id}just added");
        redirect("index.php?products");

    }
}

function show_categories_add_product_page(){
    $query=query("SELECT * FROM categories");
    confirm($query);
    while($row=fetch_array($query)){
        $category_options=<<<DELIMETER
        
        <option value="{$row['cat_id']}">{$row['cat_title']}</option>

DELIMETER;
        echo $category_options;
    }
}



/**************************updating product code****************************/
function update_product(){
    if(isset($_POST['update'])){
        $product_title       =escape_string($_POST['product_title']);
        $product_category_id =escape_string($_POST['product_category_id']);
        $product_price       =escape_string($_POST['product_price']);
        $sku                 =escape_string($_POST['sku']);
        $short_desc          =escape_string($_POST['short_desc']);
        $product_quantity    =escape_string($_POST['product_quantity']);
        $product_image       =$_FILES['file']['name'];
        $image_temp_location =$_FILES['file']['tmp_name'];
        if(empty($product_image)){
            $get_pic=query("SELECT product_image FROM products WHERE product_id= " .escape_string($_GET['id'])." ");
            confirm($get_pic);
            while($row=fetch_array($get_pic)){
                $product_image=$row["product_image"];

            }
        }

        move_uploaded_file( $image_temp_location , UPLOAD_DIRECTORY.DS.$product_image );

       // $query=query("INSERT INTO products(product_title,product_category_id,product_price,product_description,short_desc,product_quantity,product_image)VALUES('{$product_title}','{$product_category_id}','{$product_price}','{$product_description}','{$short_desc}','{$product_quantity}','{$product_image}')");
        $query="UPDATE products SET ";
        $query.="product_title      ='{$product_title}'     ,";
        $query.="product_category_id='{$product_category_id}',";
        $query.="product_price      ='{$product_price}'     ,";
        $query.="sku                ='{$sku}',";
        $query.="short_desc         ='{$short_desc}'        ,";
        $query.="product_quantity   ='{$product_quantity}'   ,";
        $query.="product_image      ='{$product_image}'     ";
        $query.="WHERE product_id=".escape_string($_GET['id']);
        $send_update_query=query($query);
        confirm($send_update_query);
        set_message("Product has been updated");
        redirect("index.php?products");

    }
}

/****************************categories in admin*********************/
function show_categories_in_admin(){
    $query="SELECT * FROM categories";
    $category_query=query($query);
    confirm($query);
    while($row=fetch_array($category_query)) {
        $cat_id = $row['cat_id'];
        $cat_title = $row["cat_title"];

        $category=<<<DELEMETER
        <tr>
            <td>{$cat_id}</td>
            <td>{$cat_title}</td>
            <td><a ></a></td>

        </tr>
DELEMETER;
 echo $category;
    }
}


function add_category(){
    if(isset($_POST['add_category'])){
        $cat_title=escape_string($_POST["cat_title"]);
        if(empty($cat_title)||$cat_title==""){
        echo "<p class='bg-danger'>THIS cannot be empty</p>";
        }else{
            $insert_cat=query("INSERT INTO categories (cat_title) VALUES ('{$cat_title}')");
            confirm($insert_cat);
            set_message("category created");
        }
    }
}




/**************************admin users********************************/

function show_users_in_admin(){
    $query="SELECT * FROM users";
    $category_query=query($query);
    confirm($query);
    while($row=fetch_array($category_query)) {
        $user_id = $row['user_id'];
        $user_name = $row["username"];
        $email= $row["email"];
        $password=$row["password"];


        $users=<<<DELEMETER
        <tr>
            <td>{$user_id}</td>
            <td>{$user_name}</td>
            <td>{$email}</td> 

        </tr>
DELEMETER;
        echo $users;
    }
}
function add_user()
{
    if (isset($_POST['add_user'])) {
        $user_name = escape_string($_POST['username']);
        $email = escape_string($_POST['email']);
        $password = escape_string($_POST['password']);
       // $user_photo = $_FILES['file']['name'];
       // $photo_temp = $_FILES['file']['tmp_name'];
        $query=query("INSERT INTO users(username,email,password)VALUES('{$user_name}','{$email}','{$password}')");
        confirm($query);

        set_message("USER CREATED");
        redirect("index.php?users");
    }
    //move_uploaded_file( $photo_temp , UPLOAD_DIRECTORY.DS.$user_photo );

}


?>