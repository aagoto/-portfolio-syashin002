<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('トップページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

//================================
// トップページ画面処理
//================================
// GETパラメータを取得
//カレントページ
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;
//カテゴリー
$category = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';
//表示順
$sort = (!empty($_GET['sort'])) ? $_GET['sort'] : '';

// 不正な値チェック
if(!is_int((int)$currentPageNum)){
    error_log('エラー発生：指定ページに不正な値が入りました');
    header('Location: index.php');
}

//表示件数
$listSpan = 4;
//現在の表示レコード先頭を算出
$currentMinNum = (($currentPageNum-1) * $listSpan);
//DBから写真データを取得
$dbPhotoData = getPhotoList($currentMinNum, $category, $sort);
//DBからカテゴリーデータを取得
$dbCategoryData = getCategory();


debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
debug('画面表示処理終了');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
?>

<?php
$siteTitle = 'トップページ';
require('head.php');
?>

<?php
require('header.php');
?>

<body class="page-home page-2colum">
<div id="content" class="site-width">

<?php
    require('sidebar_index.php');
?>

    <div class="main">
        <section id="main">
            <div class="search-title">
                <div class="search-left">
                    <span class="total-num"><?php echo sanitize($dbPhotoData['total']); ?></span>件の写真が見つかりました
                </div>
                <div class="search-right">
                    <span class="num"><?php echo (!empty($dbPhotoData['data'])) ? $currentMinNum+1 : 0; ?></span> - <span class="num"><?php echo $currentMinNum + count($dbPhotoData['data']); ?></span>件 / <span class="num"><?php echo sanitize($dbPhotoData['total']) ?></span>件中
                </div>
            </div>
            <div class="panel-list">
                <?php
                    foreach($dbPhotoData['data'] as $key => $val):
                ?>
                <a href="photoDetail.php?p_id=<?php echo $val['id']; ?>" class="panel">
                    <div class="panel-head">
                        <p class="panel-title"><?php echo sanitize($val['title']); ?></p>
                    </div>
                    <div class="panel-body">
                        <img src="<?php echo sanitize($val['pic1']); ?>" alt="<?php echo sanitize($val['title']); ?>">
                    </div>
                 </a>
                 <?php
                    endforeach;
                ?>
            </div>

        <!-- ページネーション  -->
            <?php pagination($currentPageNum, $dbPhotoData['total_page']); ?>

        </section>
    </div>
</div>
<?php
require('footer.php');
?>