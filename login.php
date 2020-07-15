<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('ログインページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

//================================
// ログイン画面処理
//================================
// POST送信されていた場合
if(!empty($_POST)){
    debug('POST送信があります');

    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_save = (!empty($_POST['pass_save'])) ? true : false;

    // バリデーション
    validRequired($email, 'email');
    validRequired($pass, 'pass');

    validEmail($email, 'email');
    validMaxlen($email, 'email');

    validHalf($pass, 'pass');
    validMaxlen($pass, 'pass');
    validMinLen($pass, 'pass');
    

    if(empty($err_msg)){
        debug('バリデーションOK');

        try{
            $dbh = dbConnect();
            $sql = 'SELECT pass,id FROM users WHERE email = :email AND delete_flg = 0';
            $data = array(':email' => $email);
            $stmt = queryPost($dbh, $sql, $data);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            debug('クエリ結果の中身：'.print_r($result, true));

            //パスワードの照合
            if(!empty($result) && password_verify($pass, array_shift($result))){
                debug('パスワードがマッチしました');
                $sesLimit = 60 * 60;
                $_SESSION['login_date'] = time();

                //ログイン保持にチェックありの場合
                if($pass_save){
                    debug('ログイン保持にチェックがあります');
                    $_SESSION['login_limit'] = $sesLimit * 24 * 30;
                }else{
                    debug('ログイン保持にチェックがありません');
                    $_SESSION['login_limit'] = $sesLimit;
                }
                //ユーザーIDを格納
                $_SESSION['user_id'] = $result['id'];

                debug('セッション変数の中身：'.print_r($_SESSION, true));
                debug('マイページへ遷移します');
                header('Location:mypage.php');
            }else{
                debug('パスワードがアンマッチです');
                $err_msg['common'] = MSG18;
            }
        }catch(Exception $e){
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}

debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
debug('画面表示処理終了');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
?>

<!-- head -->
<?php
$siteTitle = 'ログイン';
require('head.php');
?>

<body>
<!-- header -->
<?php
require('header.php');
?>
<p id="js-show-msg" style="display:none;" class="msg-slide">
<?php echo getSessionFlash('msg_success'); ?>
</p>
<!-- main -->
<div id="content" class="site-width">
    <div class="main">
        <div class="form-container">
            <form action="" method="POST" class="form"> 
                <h3 class="page-title">ログイン</h3>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['common'])) echo $err_msg['common'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['email'])) echo 'err' ?>">
                    メールアドレス
                    <input type="text" name="email" id="email" placeholder="メールアドレス">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['email'])) echo $err_msg['email'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['pass'])) echo 'err' ?>">
                    パスワード
                    <input type="password" name="pass" id="pass" placeholder="パスワード">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['pass'])) echo $err_msg['pass'];
                    ?>
                </div>
                <label>
                    <input type="checkbox" name="pass_save">次回ログインを省略する
                </label>
                <div class="btn-container">
                    <button class="btn btn-primary" type="submit" value="ログイン">ログイン</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require('footer.php');
?>
