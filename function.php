<?php
//================================
// ログ
//================================
ini_set('log_errors', 'on');
ini_set('error_log', 'php.log');


//================================
// デバッグ
//================================
$debug_flg = true;

function debug($str){
    global $debug_flg;
    if(!empty($debug_flg)){
        error_log('デバッグ：'.$str);
    }
}


//================================
// セッション
//================================
// セッションファイルの置き場変更
session_save_path("/var/tmp/");
// セッションの有効期間を設定
ini_set('session.gc_maxlifetime', 60*60*24*30);
// クッキーの有効期限を設定
ini_set('session.cookie_lifetime', 60*60*24*30);
// セッションの利用
session_start();
// セッションIDを新しく生成したものと置き換える（なりすまし対策）
session_regenerate_id();


//================================
// セッション
//================================
function debugLogStart(){
    debug('>>>>>>>>>>>>>>>>>>>>>>>>>>画面表示処理開始');
    debug('セッションID：'.session_id());
    debug('セッション変数の中身：'.print_r($_SESSION,true));
    debug('現在日時タイムスタンプ：'.time());
    if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_date'] + $_SESSION['login_limit'])){
        debug('ログイン期限日時タイムスタンプ；'.($_SESSION['login_date'] + $_SESSION['login_limit']));
    }
}

//================================
// 変数
//================================
define('MSG01', '入力必須です');
define('MSG02', 'Emailの形式で入力してください');
define('MSG03', 'パスワード（再入力）が合っていません');
define('MSG04', '半角英数字のみご利用いただけます');
define('MSG05', '6文字以上で入力してください');
define('MSG06', '256文字以内で入力してください');
define('MSG07', 'エラーが発生しました。しばらく経ってからやり直してください');
define('MSG08', 'そのEmailはすでに登録されています');
define('MSG09', '電話番号の形式が違います');
define('MSG11', '郵便番号の形式が違います');
define('MSG12', '古いパスワードが違います');
define('MSG13', '古いパスワードと同じです');
define('MSG14', '文字で入力してください');
define('MSG15', '正しくありません');
define('MSG16', '期限が切れています');
define('MSG17', '半角数字のみご利用いただけます');
define('MSG18', 'メールアドレスかパスワードが違います');
define('SUC01', 'パスワードを変更しました');
define('SUC02', 'プロフィールを変更しました');
define('SUC03', 'メールを送信しました');
define('SUC04', '登録しました');
define('SUC05', 'メッセージを投稿しました');

//================================
// グローバル変数
//================================
// エラーメッセージ格納用の配列
$err_msg = array();


//================================
// バリデーション
//================================
// 未入力チェック
function validRequired($str, $key){
    if($str === ''){
        global $err_msg;
        $err_msg[$key] = MSG01;
    }
}

// Email形式チェック
function validEmail($str, $key){
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG02;
    }
}

// Email重複チェック
function validEmailDup($email){
    global $err_msg;
    try{
        $dbh = dbConnect();
        $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $email);
        $stmt = queryPost($dbh, $sql, $data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty(array_shift($result))){
            $err_msg['email'] = MSG08;
        }
    }catch(Exception $e){
        error_log('エラー発生：' . $e->getMessage());
        $err_msg['common'] = MSG07;
    }
}

// 同値チェック
function validMatch($str1, $str2, $key){
    if($str1 !== $str2){
        global $err_msg;
        $err_msg[$key] = MSG03;
    }
}

// 最小文字数チェック
function validMinLen($str, $key, $min = 6){
    if(mb_strlen($str) < $min){
        global $err_msg;
        $err_msg[$key] = MSG05;
    }
}

// 最大文字数チェック
function validMAxLen($str, $key, $max = 255){
    if(mb_strlen($str) > $max){
        global $err_msg;
        $err_msg[$key] = MSG06;
    }
}

// 半角チェック
function validHalf($str, $key){
    if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG04;
    }
}

// 電話番号形式チェック
function validTel($str, $key){
    if(!preg_match("/0\d{1,4}\d{1,4}\d{4}/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG10;
    }
}

// 郵便番号形式チェック
function validZip($str, $key){
    if(!preg_match("/^\d{7}$/", $str)){
      global $err_msg;
      $err_msg[$key] = MSG11;
    }

}

// 半角数字チェック
function validNumber($str, $key){
    if(!preg_match("/^[0-9]+$/", $str)){
      global $err_msg;
      $err_msg[$key] = MSG17;
    }
}

// パスワードチェック
function validPass($str, $key){
    validHalf($str, $key);
    validMAxLen($str, $key);
    validMinLen($str, $key);
}

// エラーメッセージ表示
function getErrMsg($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
        return $err_msg[$key];
    }
}


//================================
// データベース
//================================
// DB接続関数
function dbConnect(){  
    $dsn = 'mysql:dbname=syashin002;host=localhost;charaset=utf8';
    $user = 'root';
    $password = 'root';
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );
    $dbh = new PDO($dsn, $user, $password, $options);
    return $dbh;
}

// クエリの実行
function queryPost($dbh, $sql, $data){
    $stmt = $dbh->prepare($sql);
    if(!$stmt->execute($data)){
        debug('クエリに失敗しました');
        debug('失敗したSQL：'.print_r($stmt, true));
        $err_msg['common'] = MSG07;
        return 0;
    }
    debug('クエリ成功');
    return $stmt;
}

// ユーザー情報の取得
function getUser($u_id){
    debug('ユーザー情報を取得します');

    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM users WHERE id = :u_id AND delete_flg = 0';
        $data = array(':u_id' => $u_id);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch(Exception $e){
        error_log('エラー発生：' . $e->getMessage());
    }
}

// 写真情報の取得
function getPhoto($u_id, $p_id){
    debug('写真情報を取得します');
    debug('ユーザーID：'.$u_id);
    debug('商品ID：'.$p_id);

    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM photo WHERE user_id = :u_id AND id = :p_id AND delete_flg = 0';
        $data = array(':u_id' => $u_id, ':p_id' => $p_id);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch(Exception $e){
        error_log('エラー発生：'. $e->getMessage());
    }
}

// 写真情報（一覧）の取得
function getPhotoList($currentMinNum = 1, $category, $sort, $span = 4){
    debug('写真情報を取得します');

    try{
        $dbh = dbConnect();
        $sql = 'SELECT id FROM photo';
        if(!empty($category)) $sql .= ' WHERE category_id = ' .$category;
        if(!empty($sort)){
            switch($sort){
                case 1:
                    $sql .= ' ORDER BY title ASC';
                break;
                case 2:
                    $sql .= ' ORDER BY title DESC';
                break;
            }
        }
        $data = array();
        $stmt = queryPost($dbh, $sql, $data);
        $rst['total'] = $stmt->rowCount();//総レコード数
        $rst['total_page'] = ceil((int)$rst['total']/(int)$span);//総ページ数
        if(!$stmt){
            return false;
        }

        // ページング用のSQL作成
        $sql = 'SELECT * FROM photo';
        if(!empty($category)) $sql .= ' WHERE category_id = '.$category;
        if(!empty($sort)){
            switch($sort){
                case 1:
                    $sql .= ' ORDER BY title ASC';
                break;
                case 2:
                    $sql .= ' ORDER BY title DESC';
                break;
            }
        }
        $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
        $data = array();
        debug('SQL：'.$sql);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            $rst['data'] = $stmt->fetchAll();
            return $rst;
        }else{
            return false;
        }


    }catch(Exception $e){
        error_log('エラー発生：' . $e->getMessage());
    }
}

// 写真情報（一枚分）の取得
function getPhotoOne($p_id){
    debug('写真情報を取得します');
    debug('写真ID：'.$p_id);

    try{
        $dbh = dbConnect();
        $sql = 'SELECT p.id, p.title, p.comment, p.pic1, p.pic2, p.pic3, p.user_id, p.create_date, c.name FROM photo AS p LEFT JOIN category AS c ON category_id = c.id WHERE p.id = :p_id AND p.delete_flg = 0 AND c.delete_flg = 0';
        $data = array(':p_id' => $p_id);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch(Execption $e){
        error_log('エラー発生：'.$e->getMessage());
    }
}

// 自分の投稿写真の取得
function getMyPhoto($u_id){
    debug('自分の投稿写真の情報を取得します');
    debug('ユーザーID：'.$u_id);

    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM photo WHERE user_id = :u_id AND delete_flg = 0';
        $data = array(':u_id' => $u_id);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
    }
    }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }
}

//掲示板メッセージ取得
function getMsgsBoard($p_id){
    debug('メッセージ情報を取得します');
    debug('写真ID：'.$p_id);

    try{
        $dbh = dbConnect();
        $sql = 'SELECT b.id, b.photo_id, b.user_id, b.message, b.send_date, p.id FROM board AS b LEFT JOIN photo AS p ON b.photo_id = p.id WHERE p.id = :p_id';
        $data = array(':p_id' => $p_id);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
        }
    }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }
}

// カテゴリー情報の取得
function getCategory(){
    debug('カテゴリー情報を取得します');
    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM category';
        $data = array();
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
        }
    }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }
}

// お気に入り情報の有無の取得
function isLike($u_id, $p_id){
    debug('お気に入り情報があるか確認します');
    debug('ユーザーID：'.$u_id);
    debug('写真ID：'.$p_id);

    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM `like` WHERE photo_id = :p_id AND user_id = :u_id';
        $data = array(':u_id' => $u_id, ':p_id' => $p_id);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt->rowCount()){
            debug('お気に入りです');
            return true;
        }else{
            debug('特に気に入ってません');
            return false;
        }
    }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }
}

// 自分のお気に入り情報の取得
function getMyLike($u_id){
    debug('自分のお気に入り情報を取得します');
    debug('ユーザーID：'.$u_id);

    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM `like` AS l LEFT JOIN photo AS p ON l.photo_id = p.id WHERE l.user_id = :u_id';
        $data = array(':u_id' => $u_id);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
        }
    }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }
}


//================================
// その他
//================================
// サニタイズ
function sanitize($str){
    return htmlspecialchars($str, ENT_QUOTES);
}

// フォーム入力保持
function getFormData($str, $flg = false){
    if($flg){
        $method = $_GET;
    }else{
        $method = $_POST;
    }
    global $dbFormData;

    // ユーザーデータがある場合
    if(!empty($dbFormData)){
        // フォームのエラーがある場合
        if(!empty($err_msg[$str])){
            // POSTにデータがある場合
            if(isset($method[$str])){
                return sanitize($method[$str]);
            }else{
                return sanitize($dbFormData[$str]);
            }
        }else{
            // POSTにデータがあり、DBの情報と違う場合
            if(isset($method[$str]) && $method[$str] !== $dbFormData[$str]){
                return sanitize($method[$str]);
            }else{
                return sanitize($dbFormData[$str]);
            }
        }
    }else{
        if(isset($method[$str])){
            return sanitize($method[$str]);
        }
    }
}

// セッションを一回だけ取得できる
function getSessionFlash($key){
    if(!empty($_SESSION[$key])){
        $data = $_SESSION[$key];
        $_SESSION[$key] = '';
        return $data;
    }
}

//画像アップロード処理
function uploadImg($file, $key){
    debug('画像アップロード処理開始');
    debug('FIlE情報：'.print_r($file, true));
    if(isset($file['error']) && is_int($file['error'])){
        try{
            // バリデーション
            // $file['error'] の値を確認。配列内には「UPLOAD_ERR_OK」などの定数が入っている。
            // 「UPLOAD_ERR_OK」などの定数はphpでファイルアップロード時に自動的に定義される。
            switch($file['error']){
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE: //ファイル未選択
                    throw new RuntimeException('ファイルが選択されていません');
                case UPLOAD_ERR_INI_SIZE: //php.ini定義の最大サイズが超過した場合
                case UPLOAD_ERR_FORM_SIZE:  //フォーム定義の最大サイズ超過した場合
                    throw new RuntimeException('ファイルサイズが大きすぎます');
                defalut: 
                    throw new RuntimeException('その他のエラーが発生しました');
            }

            $type = @exif_imagetype($file['tmp_name']);
            if(!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)){
                throw new RuntimeExcepi¥tion('画像形式が未対応です');
            }
            //ハッシュ化することでファイル名の重複を防ぐ
            $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
            if(!move_uploaded_file($file['tmp_name'], $path)){
                throw new RuntimeException('ファイル保存時にエラーが発生しました');
            }
            // 保存したファイルパスのパーミッションを変更する
            chmod($path, 0644);

            debug('ファイルは正常にアップロードされました');
            debug('ファイルパス：'.$path);
            return $path;
        }catch(RuntimeException $e){
            debug($e->getMessage());
            global $err_msg;
            $err_msg[$key] = $e->getMessage();
        }
    }
}

// 画像表示処理
function showImg($path){
    if(empty($path)){
        return 'img/sample-img.png';
    }else{
        return $path;
    }
}

// GETパラメータ付与
// $del_key : 付与から取り除きたいGETパラメータのキー
function appendGetParam($arr_del_key = array()){
    if(!empty($_GET)){
    $str = '?';
    foreach($_GET as $key => $val){
        if(!in_array($key,$arr_del_key, true)){
            $str .= $key.'='.$val.'&';
        }
    }
        $str = mb_substr($str, 0, -1, 'UTF-8');
        return $str;
    }
}


//ページネーション 
function pagination($currentPageNum, $totalPageNum, $link = '', $pageColNum = 5){
    //現ページが総ページ数と同じ、かつ総ページ数が表示項目数以上なら、左にリンク4個
    if($currentPageNum == $totalPageNum && $totalPageNum > $pageColNum){
        $minPageNum = $currentPageNum - 4;
        $maxPageNum = $currentPageNum;
    //現ページが総ページ数の1ページ前なら、左にリンク3個、右にリンク1個
    }elseif($currentPageNum == ($totalPageNum - 1) && $totalPageNum > $pageColNum){
        $minPageNum = $currentPageNum - 3;
        $maxPageNum = $currentPageNum + 1;
    //現ページが2ページ目なら、左にリンク1個、右にリンク3個
    }elseif($currentPageNum == 2 && $totalPageNum > $pageColNum){
        $minPageNum = $currentPageNum - 1;
        $maxPageNum = $currentPageNum + 3;
    //現ページが1ページ目なら、右にリンク4個
    }elseif($currentPageNum == 1 && $totalPageNum > $pageColNum ){
        $minPageNum = $currentPageNum;
        $maxPageNum = 5;
    //総ページ数が表示項目より少ない場合は、総ページ数をmax,minを1に設定
    }elseif($totalPageNum < $pageColNum){
        $minPageNum = 1;
        $maxPageNum = $totalPageNum;
    //その他の場合は、左右にリンク2個
    }else{
        $minPageNum = $currentPageNum - 2;
        $maxPageNum = $currentPageNum + 2;
    }

    echo '<div class="pagination">';
        echo '<ul class="pagination-list">';
            if($currentPageNum != 1){
                echo '<li class="list-item"><a href="?p=1'.$link.'">&lt;</a></li>';
            }
            for($i = $minPageNum; $i <= $maxPageNum; $i++){
                echo '<li class="list-item';
                if($currentPageNum == $i){ echo ' active'; }
                    echo '"><a href="?p='.$i.$link.'">'.$i.'</a></li>';
            }
            if($currentPageNum != $maxPageNum && $maxPageNum > 1){
                echo '<li class="list-item"><a href="?p='.$maxPageNum.$link.'">&gt;</a></li>';
            }
        echo '</ul>';
    echo '</div>';
}


?>