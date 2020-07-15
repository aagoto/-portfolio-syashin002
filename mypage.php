<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('マイページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

//================================
// マイページ画面処理
//================================
// DBからユーザ情報の取得
$u_id = $_SESSION['user_id'];
$photoData = getMyPhoto($u_id);
// $boardData = getMyMsgsAndBoard($u_id);
$likeData = getMyLike($u_id);

debug('取得したユーザー情報：'.print_r($u_id, true));
debug('取得した写真情報：'.print_r($photoData, true));
// debug('取得したユーザー情報：'.print_r($boardData, true));
debug('取得したお気に入り情報：'.print_r($likeData, true));


debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
debug('画面表示処理終了');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
?>

<?php
$siteTitle = 'マイページ';
require('head.php');
?>

<?php
require('header.php');
?>

<p id="js-show-msg" style="display:none;" class="msg-slide">
    <?php echo getSessionFlash('msg_success'); ?>
</p>

<body class="page-mypage page-2colum page-logined">
<div id="content" class="site-width">
    <div class="main">
        <h3 class="page-title">マイページ</h3>
        <section id="main">
            <section class="list panel-list">
            <h2 class="subtitle">
                投稿写真一覧
            </h2>
            <?php
            if(!empty($photoData)):
                foreach($photoData as $key => $val):
            ?>
            <a href="registPhoto.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&p_id='.$val['id'] : '?p_id='.$val['id']; ?>" class="panel">
                <div class="panel-head">
                    <p class="panel-title"><?php echo sanitize($val['title']); ?></p>
                </div>
                <div class="panel-body">
                    <img src="<?php echo showImg(sanitize($val['pic1'])); ?>" alt="<?php echo sanitize($val['name']); ?>">
                </div>
            </a>
            <?php
                endforeach;
                endif;
            ?>
        </section>

        <section class="list panel-list">
            <h2 class="subtitle">
                お気に入り一覧
            </h2>
            <?php 
                if(!empty($likeData)):
                    foreach($likeData as $key => $val):
            ?>
            <a href="photoDetail.php<?php echo(!empty(appendGetParam())) ? appendGetParam().'&p_id='.$val['id'] : '?p_id='.$val['id']; ?>" class="panel">
                <div class="panel-head">
                    <p class="panel-title"><?php echo sanitize($val['title']); ?></p>
                </div>
                <div class="panel-body">
                    <img src="<?php echo showImg(sanitize($val['pic1'])); ?>" alt="<?php echo sanitize($val['title']); ?>">
                </div>
            </a>
            <?php
                endforeach;
                endif;
            ?>
        </section>
        </section>

        <?php
            require('sidebar_mypage.php');
        ?>

    </div>
</div>

<?php
require('footer.php');
?>