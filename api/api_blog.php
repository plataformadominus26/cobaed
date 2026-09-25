<?php
if(isset($_REQUEST["x"])){
    include_once("conexion.php");
    header("Content-Type: application/json; charset=UTF-8");
    print_r (fetchPosts($_REQUEST));
    exit();
}

 function fetchPosts($p) {
        global $db;
        $params = $p;
        $limit = isset($params['limit']) ? (int)$params['limit'] : 5;
        $offset = isset($params['offset']) ? (int)$params['offset'] : 0;

        $sql = "SELECT * FROM posts_blog ORDER BY id_post DESC LIMIT $limit OFFSET $offset";
       
        $result = $db->query($sql);
        $posts = [];
        if($result) {
            
            while($row=$result->fetch_assoc()) {
                $row['contenido_json'] = json_decode($row['contenido_json'], true);
                
                
                $posts[] = $row;
                
            }
        }
        return  $posts;
 }
?>  