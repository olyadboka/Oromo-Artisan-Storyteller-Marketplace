<?php
include "../../dbConnection/dbConnection.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['artisan_region'] = "Jimma";
     $_SESSION['artisan_id']="1";

    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_descrition'];
    $product_material = $_POST['product_material'];
    $product_category = $_POST['product_category'];
    $product_price = $_POST['product_price'];
    $product_quantity = $_POST['product_quantity'];
    $artisan_id = $_SESSION['artisan_id'];
    $pRegion = $_SESSION['artisan_region'];

    // Prepare product image file names
    $pImages = ['', '', '', ''];
    if (!empty($_FILES['pImage']['name'][0])) {
        $uploadDir = "../../uploads/products/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        foreach ($_FILES['pImage']['tmp_name'] as $key => $tmp_name) {
            if ($key >= 4) break; // Only up to 4 images
            $fileName = $_FILES['pImage']['name'][$key];
            $fileTmp = $_FILES['pImage']['tmp_name'][$key];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $newFileName = uniqid('', true) . "." . $fileExt;
            $fileDestination = "{$uploadDir}{$newFileName}";
            if (move_uploaded_file($fileTmp, $fileDestination)) {
                $pImages[$key] = $newFileName;
            }
        }
    }

    // Insert into products table with pImage1-4
    $sql = "INSERT INTO products (artisan_id, name, pRegion, description, materials, category, price, quantity, pImage1, pImage2, pImage3, pImage4)
            VALUES ('$artisan_id', '$product_name', '$pRegion', '$product_description', '$product_material', '$product_category', '$product_price', '$product_quantity',
            '{$pImages[0]}', '{$pImages[1]}', '{$pImages[2]}', '{$pImages[3]}')";
    $result = mysqli_query($con, $sql);

    if ($result) {
        $product_id = mysqli_insert_id($con);

        // Prepare related images
        $rImages = ['', '', ''];
        if (!empty($_FILES['rImage']['name'][0])) {
            $relatedDir = "../../uploads/products/related/";
            if (!is_dir($relatedDir)) {
                mkdir($relatedDir, 0777, true);
            }
            foreach ($_FILES['rImage']['tmp_name'] as $key => $tmp_name) {
                if ($key >= 3) break; // Only up to 3 related images
                $fileName = $_FILES['rImage']['name'][$key];
                $fileTmp = $_FILES['rImage']['tmp_name'][$key];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $newFileName = uniqid('rel_', true) . "." . $fileExt;
                $fileDestination = "{$relatedDir}{$newFileName}";
                if (move_uploaded_file($fileTmp, $fileDestination)) {
                    $rImages[$key] = $newFileName;
                }
            }
        }

        // Prepare related video
        $rVideo = '';
        if (!empty($_FILES['rVideo']['name'])) {
            $mediaDir = "../../uploads/media/";
            if (!is_dir($mediaDir)) {
                mkdir($mediaDir, 0777, true);
            }
            $fileName = $_FILES['rVideo']['name'];
            $fileTmp = $_FILES['rVideo']['tmp_name'];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $newFileName = uniqid('vid_', true) . "." . $fileExt;
            $fileDestination = "{$mediaDir}{$newFileName}";
            if (move_uploaded_file($fileTmp, $fileDestination)) {
                $rVideo = $newFileName;
            }
        }

        // Insert into product_image table with rImage1-3 and rvedio
        $sql2 = "INSERT INTO product_images (product_id, rImage1, rImage2, rImage3, rVideo)
                 VALUES ('$product_id', '{$rImages[0]}', '{$rImages[1]}', '{$rImages[2]}', '$rVideo')";
        mysqli_query($con, $sql2);

        $_SESSION['ProductAdded'] = "Product added successfully";
    } else {
        $_SESSION["ProductNotAdded"] = "Error while adding Product: " . mysqli_error($con);
    }

    header("Location: ../artisanContent/add_product.php");
    exit();
} else {
    header("Location: ../artisanContent/add_product.php");
    exit();
}