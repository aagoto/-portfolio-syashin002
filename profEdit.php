<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('プロフィール編集ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

//================================
// プロフィール編集画面処理
//================================
// DBからユーザ情報の取得
$dbFormData = getUser($_SESSION['user_id']);

debug('取得したユーザー情報：'.print_r($dbFormData, true));

// POST送信された場合
if(!empty($_POST)){
    debug('POST送信あり');
    debug('POST情報：'.print_r($_POST, true));
    debug('FILE情報：'.print_r($_FILES, true));

    $username = $_POST['username'];
    $tel = $_POST['tel'];
    $zip = (!empty($_POST['zip'])) ? $_POST['zip'] : 0;
    $addr = $_POST['addr'];
    $age = $_POST['age'];
    $email = $_POST['email'];
    $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'], 'pic') : '';
    $pic = (empty($pic) && !empty($dbFormData['pic'])) ? $dbFormData['pic'] : $pic;

    // バリデーション
    if($dbFormData['username'] !== $username){
        validMaxLen($username, 'username');
    }
    if($dbFormData['tel'] !== $tel){
        validTel($tel, 'tel');
    }
    if($dbFormData['addr'] !== $addr){
        validMaxLen($addr, 'addr');
    }
    if( (int)$dbFormData['zip'] !== $zip){
        validZip($zip, 'zip');
    }
    if($dbFormData['age'] !== $age){
        validMaxLen($age, 'age');
    }
    if($dbFormData['email'] !== $email){
        validMaxLen($email, 'email');
            if(empty($err_msg['email'])){
                validEmailDup($email);
            }
            validEmail($email, 'email');
            validRequired($email, 'email');
    }

    if(empty($err_msg)){
        debug('バリデーションOK');

        try{
            $dbh = dbConnect();
            $sql = 'UPDATE users SET username = :u_name, tel = :tel, zip = :zip, addr = :addr, age = :age, email = :email, pic = :pic WHERE id = :u_id';
            $data = array(':u_name' => $username, ':tel' => $tel, ':zip' => $zip, ':addr' => $addr, ':age' => $age, ':email' => $email, ':pic' => $pic, ':u_id' => $dbFormData['id']);
            $stmt = queryPost($dbh, $sql, $data);

            if($stmt){
                $_SESSION['msg_success'] = SUC02;
                debug('マイページへ遷移');
                header('Location: mypage.php');
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

<?php
$siteTitle = 'プロフィール編集';
require('head.php');
?>

<?php
require('header.php');
?>

<body>
<div id="content" class="site-width">
    <div class="main">
        <h3 class="page-title">プロフィール編集</h3>
            <div class="form-container">
                <form action="" method="post" class="form" enctype="multipart/form-data">
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['common'])) echo $err_msg['common'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['username'])) echo 'err'; ?>">
                    名前
                    <input type="text" name="username" value="<?php echo getFormData('username'); ?>">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['username'])) echo $err_msg['username'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['tel'])) echo 'err'; ?>">
                    TEL<span style="margin-left:5px;" >※ハイフンなしで入力してください</span>
                    <input type="text" name="tel" value="<?php echo getFormData('tel'); ?>">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['tel'])) echo $err_msg['tel'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['zip'])) echo 'err'; ?>">
                    郵便番号<span style="margin-left:5px;" >※ハイフンなしで入力してください</span>
                    <input type="text" name="zip" value="<?php echo getFormData('zip'); ?>">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['zip'])) echo $err_msg['zip'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['addr'])) echo 'err'; ?>">
                    住所
                    <input type="text" name="addr" value="<?php echo getFormData('addr'); ?>">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['addr'])) echo $err_msg['addr'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['age'])) echo 'err'; ?>">
                    年齢
                    <input type="number" name="age" value="<?php echo getFormData('age'); ?>">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['age'])) echo $err_msg['age'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
                    Email
                    <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['email'])) echo $err_msg['email'];
                    ?>
                </div>
                    プロフィール画像
                    <label class="area-drop <?php if(!empty($err_msg['pic'])) echo 'err'; ?>">
                        <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                        <input type="file" name="pic" class="input-file">
                        <img src="<?php echo getFormData('pic'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic'))) echo 'display:none;' ?>">
                            画像を選択
                    </label>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['pic'])) echo $err_msg['pic'];
                        ?>
                    </div>

                <div class="btn-container">
                <button class="btn btn-primary" type="submit">変更する</button>
                </div>

            </form>
            </div>
    </div>
</div>
</body>




</body>

<?php
require('footer.php');
?>

</html>