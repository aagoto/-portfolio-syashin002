<?php
require('function.php');


debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('ユーザー登録ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// ユーザー登録画面処理
//================================
// POST送信されていた場合
if(!empty($_POST)){
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_confirm = $_POST['pass_confirm'];

    // バリデーション
    validRequired($email, 'email');
    validRequired($pass, 'pass');
    validRequired($pass_confirm, 'pass_confirm');

    if(empty($err_msg)){
        validEmail($email, 'email');
        validMaxLen($email, 'email');
        validEmailDup($email);

        validHalf($pass, 'pass');
        validMaxLen($pass, 'pass');
        validMinLen($pass, 'pass');
        
        validMaxLen($pass_confirm, 'pass_confirm');
        validMinLen($pass_confirm, 'pass_confirm');

        if(empty($err_msg)){
            validMatch($pass, $pass_confirm, 'pass_confirm');

            if(empty($err_msg)){
                try{
                    $dbh = dbConnect();
                    $sql = 'INSERT INTO users (email,pass,login_date,create_date) VALUES (:email, :pass, :login_date, :create_date)';
                    $data = array(':email' => $email,
                                  ':pass' => password_hash($pass, PASSWORD_DEFAULT),
                                  ':login_date' => date('Y-m-d H:i:s'),
                                  ':create_date' => date('Y-m-d H:i:s'));
                    $stmt = queryPost($dbh, $sql, $data);

                    // クエリ成功の場合
                    if($stmt){
                        // ログイン有効期限（１時間）
                        $sesLimit = 60 * 60;
                        // 最終日時を現在日時に
                        $_SESSION['login_date'] = time();
                        $_SESSION['login_limit'] = $sesLimit;
                        // ユーザーIDの格納
                        $_SESSION['user_id'] = $dbh->lastInsertId();
                        debug('セッション変数の中身：'.print_r($_SESSION, true));
                        // マイページへ
                        header('Location:mypage.php');
                    }
                }catch(Exception $e){
                    error_log('エラー発生：'.$e->getMessage());
                    $err_msg['common'] = MSG07;
                }
            }
        }
    }
}
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
debug('画面表示処理終了');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
?>

<?php
$siteTitle = 'ユーザー登録';
require('head.php');
?>

<?php
require('header.php');
?>

<body>
<div id="content" class="site-width">
    <div class="main">
        <div class="form-container">
            <form action="" method="POST" class="form"> 
                <h3 class="page-title">ユーザー登録</h3>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['common'])) echo $err_msg['common'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
                    メールアドレス
                    <input type="text" name="email" id="email" placeholder="メールアドレス">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['email'])) echo $err_msg['email'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
                    パスワード<span style="font-size:12px;">※英数字6文字以上</span>
                    <input type="password" name="pass" id="pass" placeholder="パスワード">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['pass'])) echo $err_msg['pass'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['pass_confirm'])) echo 'err'; ?>">
                    パスワード（再入力）
                    <input type="password" name="pass_confirm" id="pass_confirm" placeholder="パスワード（再入力）">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['pass_confirm'])) echo $err_msg['pass_confirm'];
                    ?>
                </div>
                <div class="btn-container">
                <button class="btn btn-primary" type="submit">登録</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require('footer.php');
?>