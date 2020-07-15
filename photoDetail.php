<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('写真詳細ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 写真詳細画面処理
//================================
//写真IDのGETパラメータを取得
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
$viewData = getPhotoOne($p_id);
$msgBoard = getMsgsBoard($p_id);

if(empty($viewData)){
    error_log('エラー発生：指定ページに不正な値が入りました');
    header('Location:index.php');
}

debug('取得したDBデータ：'.print_r($viewData,true));


// POST送信された場合
if(!empty($_POST)){
    debug('POST送信あり');
    debug('POST情報：'.print_r($_POST, true));

    $msg = $_POST['msg'];

    if(!empty($msg)){
        validMaxLen($msg, 'msg');
    }

    if(empty($err_msg)){
        debug('バリデーションOK');

        try{
            $dbh = dbConnect();
            $sql = 'INSERT INTO board (photo_id, message, send_date) VALUES (:p_id, :msg, :send_date)';
            $data = array(':p_id' => $p_id, ':msg' => $msg, ':send_date' => date('Y-m-d H:i:s') );
            $stmt = queryPost($dbh, $sql, $data);

            if($stmt){
                $_SESSION['msg_success'] = SUC05;
                $_POST = array();
                header('Location:' .$_SERVER['PHP_SELF']. '?p_id='.$p_id);
            }

        }catch(Exception $e){
            error_log('エラー発生：'.$e->getMessage());
            $err_meg['common'] = MSG07;
        }
    }
}

    // ログイン認証
    require('auth.php');

debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
debug('画面表示処理終了');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
?>

<?php
$siteTitle = '写真詳細';
require('head.php');
?>

<?php
require('header.php');
?>


<body class="page-photoDetail page-1colum">

<div id="content" class="site-width">
    <section id="main">
        <div class="photo-title">
            <span class="badge"><?php echo sanitize($viewData['name']); ?></span>
            <?php echo sanitize($viewData['title']); ?>
            <i class="fa fa-heart icn-like js-click-like <?php if(isLike($_SESSION['user_id'], $viewData['id'])){ echo 'active'; } ?>" aria-hidden="true" data-photoid="<?php echo sanitize($viewData['id']); ?>" ></i>
        </div>
        <div class="photo-img-container">
            <div class="img-main">
                <img src="<?php echo showImg(sanitize($viewData['pic1'])); ?>" alt="メイン画像：<?php echo sanitize($viewData['title']); ?>" id="js-switch-img-main">
            </div>
            <div class="img-sub">
                <img src="<?php echo showImg(sanitize($viewData['pic1'])); ?>" alt="画像1：<?php echo sanitize($viewData['title']); ?>" class="js-switch-img-sub">
                <img src="<?php echo showImg(sanitize($viewData['pic2'])); ?>" alt="画像2：<?php echo sanitize($viewData['title']); ?>" class="js-switch-img-sub">
                <img src="<?php echo showImg(sanitize($viewData['pic3'])); ?>" alt="画像3：<?php echo sanitize($viewData['title']); ?>" class="js-switch-img-sub">
            </div>
        </div>

        <div class="comment-field">
            <h2 class="subtitle">
                詳細
            </h2>
            <div class="photo-comment">
                <p><?php echo sanitize($viewData['comment']); ?></p>
            </div>
        </div>

        <div class="msg-field">
            <h2 class="subtitle">
                掲示板
            </h2>
            <div class="msg-main">
                <?php if(!empty($msgBoard)){
                        foreach($msgBoard as $key => $val){
                ?>
                <div class="msg-content">
                    <p class="msg"><?php echo sanitize($val['message']); ?></p>
                    <p class="send_date"><?php echo sanitize($val['send_date']); ?></p>
                </div>
                <?php
                        }
                } 
                ?>
            </div>
            <div class="send-msg">
                <h2 class="subtitle">
                    コメント投稿
                </h2>
                <form action="" method="POST">
                    <textarea name="msg" cols="30" rows="10"></textarea>
                    <div class="btn-container">
                        <button class="btn btn-primary" type="submit">投稿</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?php
var_dump($viewData['pic2']);
?>

<?php
require('footer.php');
?>
