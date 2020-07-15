<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('パスワード編集ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

//================================
// パスワード編集画面処理
//================================
//DBからユーザーデータを取得
$userData = getUser($_SESSION['user_id']);
debug('取得したユーザー情報：'.print_r($userData, true));

// POST送信あり
if(!empty($_POST)){
    debug('POST送信あり');
    debug('POST情報：'.print_r($_POST, true));

    $pass_old = $_POST['pass_old'];
    $pass_new = $_POST['pass_new'];
    $pass_new_confirm = $_POST['pass_new_confirm'];

    validRequired($pass_old, 'pass_old');
    validRequired($pass_new, 'pass_new');
    validRequired($pass_new_confirm, 'pass_new_confirm');

    if(empty($err_msg)){
        debug('未入力チェックOK');

    validPass($pass_old, 'pass_old');
    validPass($pass_new, 'pass_new');
    // 古いパスワードとDBパスワードの照合
    if(!password_verify($pass_old, $userData['pass'])){
        $err_msg['pass_old'] = MSG12;
    }
    //新しいパスワードと古いパスワードが同じかチェック
    if($pass_old === $pass_new){
        $err_msg['pass_new'] = MSG13;
    }
    //パスワードと再入力が合っているかチェック
    validMatch($pass_new, $pass_new_confirm, 'pass_new_confirm');

    if(empty($err_msg)){
        debug('バリデーションOK');

        try{
            $dbh = dbConnect();
            $sql = 'UPDATE users SET pass = :pass WHERE id = :id';
            $data = array(':id' => $_SESSION['user_id'], ':pass' => password_hash($pass_new, PASSWORD_DEFAULT));
            $stmt = queryPost($dbh, $sql, $data);

            if($stmt){
                $_SESSION['msg_success'] = SUC01;
                header('Location: mypage.php');
            }
        }catch(Exception $e){
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
    }
}


debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
debug('画面表示処理終了');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
?>

<?php
$siteTitle = 'パスワード編集';
require('head.php');
?>

<?php
require('header.php');
?>

<body>

<div id="content" class="site-width">
    <div class="main">
        <h3 class="page-title">パスワード編集</h3>
            <div class="form-container">
                <form action="" method="post" class="form">
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['common'])) echo $err_msg['common'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['pass_old'])) echo 'err'; ?>">
                    古いパスワード
                    <input type="password" name="pass_old" value="">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['pass_old'])) echo $err_msg['pass_old'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['pass_new'])) echo 'err'; ?>">
                    新しいパスワード
                    <input type="password" name="pass_new" value="">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['pass_new'])) echo $err_msg['pass_new'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['pass_new_confirm'])) echo 'err'; ?>">
                    新しいパスワード
                    <input type="password" name="pass_new_confirm" value="">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['pass_new_confirm'])) echo $err_msg['pass_new_confirm'];
                    ?>
                </div>

                <div class="btn-container">
                <button class="btn btn-primary" type="submit">変更する</button>
                </div>

            </form>
            </div>
    </div>
</div>

<?php
require('footer.php');
?>
