<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('退会ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

//================================
// 退会ページ画面処理
//================================
// POST送信あり
if(!empty($_POST)){
    debug('POST送信あり');
    try{
        $dbh = dbConnect();
        $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :id';
        $sql2 = 'UPDATE photo SET delete_flg = 1 WHERE user_id = :u_id';
        $data = array(':id' => $_SESSION['user_id']);
        $stmt1 = queryPost($dbh, $sql1, $data);
        $stmt2 = queryPost($dbh, $sql2, $data);

        if($stmt1){
            session_destroy();
            debug('セッション変数の中身：'.print_r($_SESSION, true));
            header('Location: index.php');
        }else{
            debug('クエリが失敗しました');
            $err_msg['common'] = MSG07;
        }
    }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
    }
}


debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
debug('画面表示処理終了');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
?>

<?php
$siteTitle = '退会';
require('head.php');
?>

<?php
require('header.php');
?>

<body>

<div id="content" class="site-width">
    <div class="main">
        <h3 class="page-title">退会ページ</h3>
            <div class="form-container">
                <form action="" method="post" class="form">
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['common'])) echo $err_msg['common'];
                    ?>
                </div>
                <div class="btn-container btn-withdraw">
                <button class="btn  btn-danger" type="submit" name="submit"　style="text-aline:center;">退会する</button>
                </div>

            </form>
            </div>
        <a href="mypage.php">&lt; マイページに戻る</a>
    </div>
</div>

<?php
require('footer.php');
?>
