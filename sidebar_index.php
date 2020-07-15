<section id="sidebar">
    <form name="" method="GET">
        <div class="selectbox">
        <h3 class="title">カテゴリー</h3>
            <span class="icn_select"></span>
            <select name="c_id">
                <option value="0" <?php if(getFormData('c_id', true) == 0 ){ echo 'selected'; } ?>>選択してください</option>
                <?php
                    foreach($dbCategoryData as $key => $val){
                ?>
                <option value="<?php echo $val['id'] ?>" <?php if(getFormData('c_id', true) == $val['id']){ echo 'selected'; } ?>>
                    <?php echo $val['name']; ?>
                </option>
                <?php
                    }
                ?>
            </select>
        </div>

        <div class="selectbox">
            <h3 class="title">表示順</h3>
            <span class="icn_select"></span>
            <select name="sort">
                <option value="0" <?php if(getFormData('sort', true) == 0){ echo 'selected'; } ?> >選択してください</option>
                <option value="1" <?php if(getFormData('sort', true) == 1){ echo 'selected'; } ?> >昇順（タイトル）</option>
                <option value="2" <?php if(getFormData('sort', true) == 2){ echo 'selected'; } ?> >降順（タイトル）</option>
            </select>
        </div>

        <div class="btn-container">
            <button class="btn btn-primary" type="submit">検索する</button>
        </div>

    </form>
</section>