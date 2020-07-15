<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('写真登録ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

//================================
// 写真投稿画面処理
//================================

// 画面表示用データ取得
// GETデータを格納
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
// DBから写真データを取得
$dbFormData = (!empty($p_id)) ? getPhoto($_SESSION['user_id'], $p_id): '';
// 新規登録か編集か判断フラグ
$edit_flg = (!empty($dbFormData)) ? true : false;
//DBからカテゴリーデータの取得
$dbCategoryData = getCategory();

debug('写真ID：'.$p_id);
debug('フォーム用DBデータ'.print_r($dbFormData, true));
debug('カテゴリーデータ：'.print_r($dbCategoryData, true));

// パラメータ改竄チェック
if(!empty($p_id) && empty($dbFormData)){
    debug('GETパラメータの商品IDが違うため、マイページへ遷移します');
    header('Location:mypage.php');
}

// POST送信処理
if(!empty($_POST)){
    debug('POST送信があります');
    debug('POST情報：'.print_r($_POST, true));
    debug('FILE情報：'.print_r($_FILES, true));

    $title = $_POST['title'];
    $category = $_POST['category_id'];
    $comment = $_POST['comment'];
    $pic1 = (!empty($_FILES['pic1']['name'])) ? uploadImg($_FILES['pic1'], 'pic1') : '';
    $pic1 = (empty($pic1) && !empty($dbFormData['pic1'])) ? $dbFormData['pic1'] : $pic1;
    $pic2 = (!empty($_FILES['pic2']['name'])) ? uploadImg($_FILES['pic2'], 'pic2') : '';
    $pic2 = (empty($pic2) && !empty($dbFormData['pic2'])) ? $dbFormData['pic2'] : $pic2;
    $pic3 = (!empty($_FILES['pic3']['name'])) ? uploadImg($_FILES['pic3'], 'pic3') : '';
    $pic3 = (empty($pic3) && !empty($dbFormData['pic3'])) ? $dbFormData['pic3'] : $pic3;

    // 更新の場合はDBの情報と入力情報が異なる場合にバリデーションを行う
if(empty($dbFormData)){
    validRequired($title, 'title');
    validMaxLen($title, 'title');
    validMaxLen($comment, 'comment', 500);
    }else{
        if($dbFormData['title'] !== $title){
            validRequired($title, 'title');
            validMaxLen($title, 'title');
        }
        if($dbFormData['comment'] !== $comment){
            validMaxLen($comment, 'comment', 500);
        }
    }
    if(empty($err_msg)){
        debug('バリデーションOK');
    }

    try{
        $dbh = dbConnect();
        if($edit_flg){
             debug('DB更新です');
            $sql = 'UPDATE photo SET title = :title, category_id = :category, comment = :comment, pic1 = :pic1, pic2 = :pic2, pic3 = :pic3 WHERE user_id = :u_id AND id = :p_id';
            $data = array(':title' => $title, ':category' => $category, ':comment' => $comment, ':pic1' => $pic1, ':pic2' => $pic2, ':pic3' => $pic3, ':u_id' => $_SESSION['user_id'], ':p_id' => $p_id);
        }else{
            debug('DB新規登録です');
            $sql = 'INSERT INTO photo (title, category_id, comment, pic1, pic2, pic3, user_id, create_date) VALUES (:title, :category, :comment, :pic1, :pic2, :pic3, :u_id, :date)';
            $data = array(':title' => $title, ':category' => $category, ':comment' => $comment, ':pic1' => $pic1, ':pic2' => $pic2, ':pic3' => $pic3, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));
        }
        debug('SQL：'.$sql);
        debug('流し込みデータ：'.print_r($data, true));
        $stmt = queryPost($dbh, $sql, $data);

        // クエリ成功の場合
        if($stmt){
            $_SESSION['msg_success'] = SUC04;
            debug('マイページへ遷移');
            header('Location: mypage.php');
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
$siteTitle = (!$edit_flg) ? '写真投稿' : '投稿編集';
require('head.php');
?>


<?php
require('header.php');
?>

<body>
<div id="content" class="site-width">
    <div class="main">
        <h3 class="page-title"><?php echo (!$edit_flg) ? '写真を投稿する' : '投稿を編集する'; ?></h3>
            <div class="form-container">
                <form action="" method="post" class="form" enctype="multipart/form-data" style="box-sizing:border-box;">
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['common'])) echo $err_msg['common'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['title'])) echo 'err'; ?>">
                    タイトル<span class="label-require">必須</span>
                    <input type="text" name="title" class="title" value="<?php echo getFormData('title'); ?>">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['title'])) echo $err_msg['title'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['category_id'])) echo 'err'; ?>">
                    カテゴリー
                    <select name="category_id">
                        <option value="0" <?php if(getFormData('category_id') == 0){ echo 'selected'; } ?>>選択してください</option>
                        <?php
                            foreach($dbCategoryData as $key => $val){
                        ?>
                        <option value="<?php echo $val['id'] ?>" <?php if(getFormData('category_id') == $val['id']) { echo 'selected'; }?>>
                            <?php echo $val['name']; ?>
                        </option>
                        <?php
                          }
                        ?>
                    </select>
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['category_id'])) echo $err_msg['category_id'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['comment'])) echo 'err'; ?>">
                    詳細
                    <textarea name="comment" id="js-count" cols="30" rows="10" style="height:150px;"><?php echo getFormData('comment'); ?></textarea>
                </label>
                <p class="counter-text"><span id="js-count-view">0</span>/500文字</p>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['comment'])) echo $err_msg['comment'];
                    ?>
                </div>
                <div style="overflow:hidden;">
                          <div class="imgDrop-container">
                              画像1
                                <label class="area-drop <?php if(!empty($err_msg['pic1'])) echo 'err'; ?>">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                    <input type="file" name="pic1" class="input-file">
                                    <img src="<?php echo getFormData('pic1'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic1'))) echo 'display:none;' ?>">
                                        ドラッグ&ドロップ
                                </label>
                                <div class="area-msg">
                                    <?php
                                    if(!empty($err_msg['pic1'])) echo $err_msg['pic1'];
                                    ?>
                                </div>
                          </div>
                          <div class="imgDrop-container">
                              画像2
                                <label class="area-drop <?php if(!empty($err_msg['pic2'])) echo 'err'; ?>">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                    <input type="file" name="pic2" class="input-file">
                                    <img src="<?php echo getFormData('pic2'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic2'))) echo 'display:none;' ?>">
                                        ドラッグ&ドロップ
                                </label>
                                <div class="area-msg">
                                    <?php
                                    if(!empty($err_msg['pic2'])) echo $err_msg['pic2'];
                                    ?>
                                </div>
                          </div>
                          <div class="imgDrop-container">
                              画像3
                                <label class="area-drop <?php if(!empty($err_msg['pic3'])) echo 'err'; ?>">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                    <input type="file" name="pic3" class="input-file">
                                    <img src="<?php echo getFormData('pic3'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic3'))) echo 'display:none;' ?>">
                                        ドラッグ&ドロップ
                                </label>
                                <div class="area-msg">
                                    <?php
                                    if(!empty($err_msg['pic3'])) echo $err_msg['pic3'];
                                    ?>
                                </div>
                          </div>
                </div>

                <div class="btn-container">
                <button class="btn btn-primary" type="submit"><?php echo (!$edit_flg) ? '投稿する' : '変更する'; ?></button>
                </div>

            </form>
            </div>
    </div>
</div>

<?php
require('footer.php');
?>