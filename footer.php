
<footer id="footer">
    Copyright <a href="">SYASHIN002</a>. All Rights Reserved.
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
    $(function(){

        //フッターを最下部に固定
        var $ftr = $('#footer');
        if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
            $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) + 'px;' });
        }

        //メッセージ表示
        var $jsShowMsg = $('#js-show-img');
        var msg = $jsShowMsg.text();
        if(msg.replace(/^[\s　]+|[\s　]+$/g, "").length){
            $jsShowMsg.slideToggle('slow');
            setTimeOut(function(){
                $jsShowMsg.slideToggle('slow');
            }, 5000);
        }

        //画像ライブビュー
        var $dropArea = $('.area-drop');
        var $fileInput = $('.input-file');

        $dropArea.on('dragover', function(e){
            e.stopPropagation();
            e.preventDefault();
            $(this).css('border', '3px #ccc dashed');
        });
        $dropArea.on('dragleave', function(e){
            $dropArea.css('border', 'none');
        });

        $fileInput.on('change', function(e){
            $dropArea.css('border', 'none');
            var file = this.files[0];
                $img = $(this).siblings('.prev-img');
                fileReader = new FileReader();

                fileReader.onload = function(event){
                    $img.attr('src', event.target.result).show();
                };

                fileReader.readAsDataURL(file);
        });

        //画像切り替え
        var $switchImgSubs = $('.js-switch-img-sub'),
            $switchImgMain = $('#js-switch-img-main');

        $switchImgSubs.on('click', function(e){
            $switchImgMain.attr('src', $(this).attr('src'));
        });

        //テキストカウント
        var $countUp = $('#js-count'),
            $countView = $('#js-count-view');
        $countUp.on('keyup', function(e){
            $countView.html($(this).val().length);
        });

        //お気に入り登録
        var $like,
            likePhotoId;
        $like = $('.js-click-like') || null;
        likePhotoId = $like.data('photoid') || null;
        if(likePhotoId !== undefined && likePhotoId !== null){
            $like.on('click', function(){
                var $this = $(this);
                $.ajax({
                    type: 'POST',
                    url: 'ajaxLike.php',
                    data: { photo_id : likePhotoId }
                }).done(function(data){
                    console.log('Ajax Success');
                    $this.toggleClass('active');
                }).fail(function(msg){
                    console.log('Ajax Error');
                });
            });
        }
        console.log(likePhotoId);
    });
</script>

</body>
</html>
