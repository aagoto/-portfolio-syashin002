<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('AjaxLikeページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


//================================
// ajaxLike処理
//================================
// POST送信あり、ユーザーIDあり場の合
if(isset($_POST['photo_id']) && isset($_SESSION['user_id'])){
    debug('POST送信あり');
    $p_id = $_POST['photo_id'];
    debug('写真ID：'.$p_id);

    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM `like` WHERE photo_id = :p_id AND user_id = :u_id';
        $data = array(':u_id' => $_SESSION['user_id'], ':p_id' => $p_id);
        $stmt = queryPost($dbh, $sql, $data);
        $resultCount = $stmt->rowCount();

        if(!empty($resultCount)){
            $sql = 'DELETE FROM `like` WHERE photo_id = :p_id AND user_id = :u_id';
            $data = array(':u_id' => $_SESSION['user_id'], ':p_id' => $p_id);
            $stmt = queryPost($dbh, $sql, $data);
        }else{
            $sql = 'INSERT INTO `like` (photo_id, user_id, create_date) VALUES (:p_id, :u_id, :date)';
            $data = array(':u_id' => $_SESSION['user_id'], ':p_id' => $p_id, ':date' => date('Y-m-d H:i:s'));
            $stmt = queryPost($dbh, $sql, $data);
        }
    }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }
}

debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
debug('画面表示処理終了');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
?>
