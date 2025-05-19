<?php
require_once 'config/class.user.php';
$auth_user = new USER();

if(isset($_POST['term']) && !empty($_POST['term'])) {
    $searchTerm = '%' . $_POST['term'] . '%';
    
    try {
        $stmt = $auth_user->runQuery("
            SELECT * FROM products 
            WHERE product_name LIKE :term 
            OR description LIKE :term 
            LIMIT 10
        ");
        $stmt->execute(array(':term' => $searchTerm));
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if(count($results) > 0) {
            foreach($results as $product) {
                echo '<a href="product.php?id='.$product['id'].'" class="dropdown-item d-flex align-items-center p-2">';
                if(!empty($product['image'])) {
                    echo '<img src="'.$product['image'].'" alt="'.$product['product_name'].'" style="width:40px; height:40px; object-fit:cover; margin-right:10px;">';
                }
                echo '<div>';
                echo '<h6 class="mb-0">'.$product['product_name'].'</h6>';
                echo '<small class="text-muted">$'.$product['price'].'</small>';
                echo '</div></a>';
            }
        } else {
            echo '<div class="p-2">No products found</div>';
        }
    } catch(PDOException $e) {
        echo '<div class="p-2 text-danger">Error searching products</div>';
    }
}
?>