<?php require_once ("../../resources/config.php"); ?>
<?php include(TEMPLATE_BACK ."/header.php"); ?>
<?php
if(!isset($_SESSION["username"])){
    redirect("../../public");
}
?>

        <div id="page-wrapper">

            <div class="container-fluid">


                <?php

               if ($_SERVER["REQUEST_URI"]=="/htdocs/electronic-store/public/admin/"||$_SERVER["REQUEST_URI"]=="/htdocs/electronic-store/public/admin/index.php"){
                    include(TEMPLATE_BACK ."/admin_content.php");
                }


                if(isset($_GET["products"])){
                    include(TEMPLATE_BACK ."/products.php");
                }
                if(isset($_GET["categories"])){
                    include(TEMPLATE_BACK ."/categories.php");
                }
                if(isset($_GET["add_product"])){
                    include(TEMPLATE_BACK ."/add_product.php");
                }
                if(isset($_GET["edit_product"])){
                    include(TEMPLATE_BACK ."/edit_product.php");
                }
                if(isset($_GET["users"])){
                    include(TEMPLATE_BACK ."/users.php");
                }
                if(isset($_GET["add_user"])){
                    include(TEMPLATE_BACK ."/add_user.php");
                }
                if(isset($_GET["edit_user"])){
                    include(TEMPLATE_BACK ."/edit_user.php");
                }
                ?>



            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- /#page-wrapper -->


<?php include(TEMPLATE_BACK ."/footer.php"); ?>
